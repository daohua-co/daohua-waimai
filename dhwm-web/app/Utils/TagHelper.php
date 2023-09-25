<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Utils;

class TagHelper
{
    public static function parseTagIds($tags, $tagModel): array
    {
        $tagIds = [];
        foreach ($tags as $tag) {
            if (! empty($tag['id'])) {
                $tagIds[] = $tag['id'];
                continue;
            }
            if (empty($tag['title'])) {
                continue;
            }
            $tagItem = $tagModel::query()
                ->where('title', $tag['title'])
                ->first(['id']);
            if (! $tagItem) {
                $tagItem = $tagModel::create(['title' => $tag['title']]);
            }
            $tagIds[] = $tagItem['id'];
        }

        return $tagIds;
    }
}
