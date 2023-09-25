<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Services;

use App\Models\Goods\Category;
use App\Models\Goods\Goods;
use App\Models\Goods\SkuName;
use App\Models\Goods\SkuValue;
use App\Models\Goods\Tag;
use App\Utils\TagHelper;
use Illuminate\Support\Facades\DB;

class GoodsService
{
    /**
     * @throws \Exception
     */
    public static function create(array $attributes, array $tags, array $catIds, array $skuData): Goods
    {
        static::preprocess($attributes);

        try {
            DB::beginTransaction();

            /* @var Goods $goods */
            $goods = Goods::create($attributes);

            // 关联 tag
            $tagIds = TagHelper::parseTagIds($tags, Tag::class);
            if ($tagIds) {
                $goods->tags()->attach($tagIds);
                static::updateTagsGoodsCount($tagIds);
            }

            // 关联 分类
            if ($catIds) {
                $goods->categories()->attach($catIds);
                static::updateCatsGoodsCount($catIds);
            }

            // 保存规格
            static::createGoodsSkus($goods, $skuData);

            DB::commit();
            return $goods;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public static function update(Goods $goods, array $attributes, array $tags, array $catIds, array $skuData): void
    {
        static::preprocess($attributes);
        $oldCatIds = $goods->cat_ids;
        $oldTagIds = $goods->tag_ids;

        try {
            DB::beginTransaction();
            $goods->update($attributes);

            // 关联 tag
            $tagIds = TagHelper::parseTagIds($tags, Tag::class);
            $goods->tags()->sync($tagIds);
            $tids = array_unique(array_merge($tagIds, $oldTagIds));
            static::updateTagsGoodsCount($tids);

            // 关联 分类
            $goods->categories()->sync($catIds);
            $cids = array_unique(array_merge($catIds, $oldCatIds));
            static::updateCatsGoodsCount($cids);

            // 更新规格
            static::updateGoodsSkus($goods, $skuData);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function getSkuMap(Goods $goods): array
    {
        $skuMap = [];
        foreach ($goods->skus as $sku) {
            $skuMap[$sku->sku_key] = $sku;
        }
        return $skuMap;
    }

    private static function updateGoodsSkus(Goods $goods, $skuData): void
    {
        $optionResult = static::parseSkuInput($skuData);

        // 同步规格名、规格值关联中间表
        $goods->skuNames()->sync($optionResult['useNameItems'] ?? []);
        $goods->skuValues()->sync($optionResult['useValueItems'] ?? []);

        $hasSku = (bool) $optionResult;
        $goods->update(['has_sku' => (bool) $hasSku]);
        if ($hasSku) {
            static::saveSkus($goods, $skuData['skuMap'], $optionResult['keyReplaces'], true);
        }
    }

    private static function createGoodsSkus($goods, $skuData): void
    {
        if (! $optionResult = static::parseSkuInput($skuData)) {
            return;
        }

        $goods->skuNames()->attach($optionResult['useNameItems']);
        $goods->skuValues()->attach($optionResult['useValueItems']);
        $goods->update(['has_sku' => true]);
        static::saveSkus($goods, $skuData['skuMap'], $optionResult['keyReplaces']);
    }

    private static function getExistsValueIdsFromSkuOptions($options): array
    {
        $allInputValueIds = array_unique(array_merge(...array_column($options, 'value_ids')));
        $existsValues = SkuValue::find($allInputValueIds, 'id')->toArray();
        return array_column($existsValues, 'id');
    }

    private static function parseSkuInputUseName(&$result, &$sku, $sortIndex): void
    {
        $nameId = $sku['name_id'];
        // $nameId 不是纯数字/不存在则创建
        if (preg_match('/\\D/', (string) $nameId) || ! SkuName::find($nameId)) {
            // $nameId 不是纯数字则新建
            $nameItem = SkuName::create([
                'title' => htmlspecialchars(trim($nameId)),
            ]);
            $nameId = $nameItem->id;
            $sku['name_id'] = $nameId;
        }
        $result['useNameItems'][$nameId] = [
            'sort_index' => $sortIndex,
            'enable_image' => (bool) $sku['enable_image'],
            'show_image' => (bool) $sku['show_image'],
        ];
    }

    private static function parseSkuInputUseValues(&$result, $sku, $existsValueIds): void
    {
        foreach ($sku['value_ids'] as $valueId) {
            // $valueId 不存在，则把 $valueId 当做标题创建新记录
            if (! in_array($valueId, $existsValueIds)) {
                $valueTitle = $valueId;
                $valueId = SkuValue::create([
                    'name_id' => $sku['name_id'],
                    'title' => htmlspecialchars(trim($valueTitle)),
                ])->id;
                $result['keyReplaces']['titles'][] = ",{$valueTitle},";
                $result['keyReplaces']['ids'][] = ",{$valueId},";
            }
            $result['useValueItems'][$valueId] = [
                'image_path' => $sku['value_images'][$valueId] ?? '',
            ];
        }
    }

    /**
     * @return array 如果没有规格选项，将返回空数组
     */
    private static function parseSkuInput(array $skuData): array
    {
        if (empty($skuData['options'])) {
            return [];
        }

        $result = [
            'useNameItems' => [],
            'useValueItems' => [],
            'keyReplaces' => [ // TODO 去掉此变量，直接在逻辑中替换
                'ids' => [],
                'titles' => [],
            ],
        ];

        // 从提交上来的所有 value_ids 获取在数据库中已存在的 ids
        $existsValueIds = static::getExistsValueIdsFromSkuOptions($skuData['options']);
        foreach ($skuData['options'] as $sortIndex => $sku) {
            if (empty($sku['value_ids'])) {
                continue;
            }
            static::parseSkuInputUseName($result, $sku, $sortIndex);
            static::parseSkuInputUseValues($result, $sku, $existsValueIds);
        }

        return $result;
    }

    private static function updateTagsGoodsCount($tagIds): void
    {
        if (! $tagIds) {
            return;
        }

        $tagItems = Tag::find($tagIds);
        foreach ($tagItems as $tag) {
            /* @var Tag $tag */
            $tag->forceFill([
                'goods_count' => $tag->goods()->count('goods_id'),
            ])->save();
        }
    }

    private static function updateCatsGoodsCount($catIds): void
    {
        if (! $catIds) {
            return;
        }

        $catItems = Category::find($catIds);
        foreach ($catItems as $cat) {
            $cat->forceFill([
                'goods_count' => $cat->goods()->count('goods_id'),
            ])->save();
        }
    }

    private static function preprocess(&$attributes): void
    {
        $attributes = trimArray($attributes);
        $attributes['desc_num'] = 10000 - $attributes['asc_num'];
        if (empty($attributes['intro'])) {
            $attributes['intro'] = mb_substr(trim(strip_tags($attributes['detail'])), 0, 100);
        }
    }

    private static function saveSkus(Goods $goods, array $skuMap, array $keyReplaces, bool $isUpdate = false): void
    {
        $oldSkuKeyMap = []; // 已创建的规格明细 skuKey
        if ($isUpdate && $goods->skus) {
            foreach ($goods->skus as $item) {
                $oldSkuKeyMap[$item->sku_key] = $item;
            }
        }

        $createRows = [];
        $skuSort = 0;
        foreach ($skuMap as $skuKey => $skuRow) {
            // $skuKey 中的规格值 title 换成 id
            if ($keyReplaces['ids']) {
                $skuKey = ",{$skuKey},";
                $skuKey = str_replace($keyReplaces['titles'], $keyReplaces['ids'], $skuKey);
                $skuKey = trim($skuKey, ',');
            }

            $row = [
                'goods_id' => $goods->id,
                'sku_key' => $skuKey,
                'code' => $skuRow['code'] ?? '', // TODO 检查 code 不能重复
                'price' => $skuRow['price'] ?? 0,
            ];
            ++$skuSort;
            if (! $oldSkuItem = $oldSkuKeyMap[$skuKey] ?? null) {
                // 记录批量新建
                $createRows[] = $row;
                continue;
            }
            // 更新已有
            $oldSkuItem->update($row);
        }

        if ($createRows) {
            $goods->skus()->createMany($createRows);
        }
    }
}
