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

use App\Models\Content\Category;
use App\Models\Content\Content;
use App\Models\Content\Tag;
use App\Utils\TagHelper;
use Illuminate\Support\Facades\DB;

class ContentService
{
    /**
     * @throws \Exception
     */
    public static function create(array $attributes, array $tags, array $catIds): Content
    {
        $attributes = static::preprocess($attributes);
        $attributes['creator_id'] = administrator()->id;

        try {
            DB::beginTransaction();
            /* @var Content $content */
            $content = Content::create($attributes);

            // 关联 tag
            $tagIds = TagHelper::parseTagIds($tags, Tag::class);
            if ($tagIds) {
                $content->tags()->attach($tagIds);
                static::updateTagsContentCount($tagIds);
            }

            // 关联 分类
            if ($catIds) {
                $content->categories()->attach($catIds);
                static::updateCatsContentCount($tagIds);
            }

            DB::commit();
            return $content;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public static function update(Content $content, array $attributes, array $tags, array $catIds): void
    {
        $attributes = static::preprocess($attributes);
        $oldCatIds = $content->cat_ids;
        $oldTagIds = $content->tag_ids;
        try {
            DB::beginTransaction();
            $content->update($attributes);

            // 关联 tag
            $tagIds = TagHelper::parseTagIds($tags, Tag::class);
            $content->tags()->sync($tagIds);
            $tids = array_unique(array_merge($tagIds, $oldTagIds));
            static::updateTagsContentCount($tids);

            // 关联 分类
            $content->categories()->sync($catIds);
            $cids = array_unique(array_merge($catIds, $oldCatIds));
            static::updateCatsContentCount($cids);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private static function preprocess(array $attributes): array
    {
        $attributes = trimArray($attributes);
        $attributes['desc_num'] = 10000 - $attributes['asc_num'];
        if (empty($attributes['intro'])) {
            $attributes['intro'] = mb_substr(strip_tags($attributes['detail']), 0, 100);
        }
        return $attributes;
    }

    private static function updateTagsContentCount($tagIds): void
    {
        if (! $tagIds) {
            return;
        }

        $tagItems = Tag::find($tagIds);
        foreach ($tagItems as $tag) {
            /* @var Tag $tag */
            $tag->forceFill([
                'content_count' => $tag->contents()->count('content_id'),
            ])->save();
        }
    }

    private static function updateCatsContentCount($catIds): void
    {
        if (! $catIds) {
            return;
        }

        $catItems = Category::find($catIds);
        foreach ($catItems as $cat) {
            /* @var Category $cat */
            $cat->forceFill([
                'content_count' => $cat->contents()->count('content_id'),
            ])->update();
        }
    }
}
