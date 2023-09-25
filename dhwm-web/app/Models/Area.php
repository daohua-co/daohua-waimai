<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Models;

use App\Utils\ChildrenTree;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $id
 * @property int $parent_id
 * @property string $name
 * @property string $short_name
 * @property float $lat
 * @property float $lng
 * @property bool $joined
 * @property Area $parentItem
 */
class Area extends Model
{
    public $timestamps = false;

    protected $fillable = ['parent_id', 'name', 'short_name', 'lat', 'lng', 'joined'];

    protected $casts = [
        'parent_id' => 'integer',
        'lat' => 'float',
        'lng' => 'float',
        'joined' => 'boolean',
    ];

    public function parentItem(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    /** 获取子地区id数组，包括当前id */
    public static function getChildIds(int $areaId): array
    {
        $map = static::childIdsMap();
        return $map[$areaId] ?? [];
    }

    public static function getLeafUpIds(int $areaId)
    {
        $map = static::leafUpIdsMap();
        return $map[$areaId] ?? [];
    }

    public static function childIdsMap(): array
    {
        $cacheKey = 'area-child-ids-map';
        if ($map = Cache::get($cacheKey)) {
            return $map;
        }

        $map = [];
        $areas = static::all(['id', 'parent_id'])->toArray();
        $areaTree = new ChildrenTree($areas);
        foreach ($areas as $area) {
            $map[$area['id']] = $areaTree->getAllChildIds($area['id']);
        }

        Cache::put($cacheKey, $map);

        return $map;
    }

    public static function leafUpIdsMap()
    {
        $cacheKey = 'area-leaf-up-ids-map';
        if ($map = Cache::get($cacheKey)) {
            // return $map;
        }

        $areas = static::all(['id', 'parent_id'])->toArray();
        $parentIds = array_column($areas, 'parent_id');
        $parentIds = array_unique($parentIds);
        $middleMap = [];
        $map = [];
        foreach ($areas as $area) {
            ['id' => $id, 'parent_id' => $parentId] = $area;
            if ($parentId) {
                if (! in_array($id, $parentIds)) {
                    // 不在 parent_id 列表中则为叶子结点
                    $map[$id] = [$parentId, $id];
                } else {
                    // 中间结点
                    $middleMap[$id] = $parentId;
                }
            }
        }

        foreach ($map as $id => $item) {
            $parentId = $item[0];
            if (empty($middleMap[$parentId])) {
                continue;
            }
            array_unshift($map[$id], $middleMap[$parentId]);
        }

        Cache::put($cacheKey, $map);
        return $map;
    }

    public static function getLeafUpNames(int $areaId): array
    {
        $areaUpIds = static::getLeafUpIds($areaId);
        $areaItems = static::find($areaUpIds);
        $areaNames = [];
        foreach ($areaItems as $areaItem) {
            $areaNames[] = $areaItem->short_name ?: $areaItem->name;
        }
        return $areaNames;
    }
}
