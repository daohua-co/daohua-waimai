<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Models\Area;
use App\Utils\ChildrenTree;
use Illuminate\Http\JsonResponse;

class AreaController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Area::all()->toArray();
        return success([
            'items' => ChildrenTree::make($items), // TODO cache
        ]);
    }

    public function show(Area $item): JsonResponse
    {
        $item->parent;
        return success([
            'item' => $item->toArray(),
        ]);
    }

    public function update(Area $item)
    {
        request()->validate(
            ['name' => 'required'],
            ['name.required' => '请输入名称']
        );

        $item->update(inputs([
            'name', 'short_name', 'lat', 'lng',
        ]));

        return success();
    }
}
