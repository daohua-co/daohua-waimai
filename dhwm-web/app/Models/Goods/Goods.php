<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Models\Goods;

use App\Models\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $code
 * @property string $title
 * @property string $intro
 * @property string $usp
 * @property string $detail
 * @property float $price
 * @property int $sales_count
 * @property int $like_count
 * @property int $evaluate_count
 * @property float $evaluate_score
 * @property int $asc_num
 * @property int $desc_num
 * @property bool $is_on_sale
 * @property bool $has_sku
 * @property array $album
 * @property array $tag_ids
 * @property array $cat_ids 商品分类 id，需手动 append
 * @property BelongsToMany $tags
 * @property BelongsToMany $categories
 * @property BelongsToMany $skuNames
 * @property BelongsToMany $skuValues
 * @property HasMany $skus
 * @property array $skus_formatted 商品 sku 格式化数据，需手动 append
 */
class Goods extends Model
{
    //    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'code', 'title', 'usp', 'album', 'intro', 'detail', 'price',
        'sales_count', 'is_on_sale', 'like_count',
        'evaluate_count', 'evaluate_score', 'asc_num', 'desc_num', 'has_sku',
    ];

    protected $casts = [
        'album' => 'array',
        'is_on_sale' => 'boolean',
        'price' => 'float',
        'has_sku' => 'boolean',
    ];

    protected $appends = [
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'goods_tag_pivots', 'goods_id', 'tag_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'goods_category_pivots', 'goods_id', 'category_id')
            ->withTimestamps();
    }

    public function catIds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->categories ? array_column($this->categories->toArray(), 'id') : [],
        );
    }

    /**
     * 提供给前端显示的 SKU 结构化数据。
     */
    public function skusFormatted(): Attribute
    {
        return Attribute::make(get: function () {
            if (! $this->has_sku) {
                return [
                    'options' => [],
                    'skuMap' => (object) [],
                ];
            }

            $options = [];
            $skuMap = [];

            $valueIds = [];
            $valueImages = [];
            foreach ($this->skuValues as $skuValue) {
                $valueIds[$skuValue->name_id][] = $skuValue->id;
                if ($skuValue->pivot->image_path) {
                    isset($valueImages[$skuValue->name_id]) || $valueImages[$skuValue->name_id] = [];
                    $valueImages[$skuValue->name_id][$skuValue->id] = $skuValue->pivot->image_path;
                }
            }
            foreach ($this->skuNames as $nameItem) {
                $options[] = [
                    'name_id' => $nameItem->id,
                    'enable_image' => (bool) $nameItem->pivot->enable_image,
                    'show_image' => (bool) $nameItem->pivot->show_image,
                    'value_ids' => $valueIds[$nameItem->id],
                    'value_images' => (object) ($valueImages[$nameItem->id] ?? []),
                ];
            }
            foreach ($this->skus as $skuItem) {
                /* @var Sku $skuItem */
                $skuMap[$skuItem->sku_key] = [
                    'id' => $skuItem->id,
                    'code' => $skuItem->code,
                    'price' => $skuItem->price,
                    'stocks' => $skuItem->stocks,
                    'sale_count' => $skuItem->sale_count,
                ];
            }

            return [
                'options' => $options,
                'skuMap' => (object) $skuMap,
            ];
        });
    }

    public function tagIds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tags ? array_column($this->tags->toArray(), 'id') : [],
        );
    }

    public function skuNames(): BelongsToMany
    {
        return $this->belongsToMany(SkuName::class, 'goods_sku_name_uses', 'goods_id', 'name_id')
            ->withTimestamps()
            ->withPivot('enable_image', 'show_image')
            ->orderByPivot('sort_index')
            ->orderByPivot('id')
            ->withTimestamps();
    }

    public function skuValues(): BelongsToMany
    {
        return $this->belongsToMany(SkuValue::class, 'goods_sku_value_uses', 'goods_id', 'value_id')
            ->withTimestamps()
            ->withPivot('image_path')
            ->withTimestamps();
    }

    public function skus(): HasMany
    {
        return $this->hasMany(Sku::class, 'goods_id');
    }
}
