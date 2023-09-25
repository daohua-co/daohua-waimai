<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Controllers\Shop;

use App\Admin\Controllers\Controller;
use App\Admin\Services\ShopApplyService;
use App\Models\Area;
use App\Models\Shop\Apply;
use App\Utils\ChildrenTree;
use Illuminate\Http\JsonResponse;

class ApplyController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Apply::query()
            ->orderByDesc('id');
        // 过滤地区
        if ($areaId = (int) request('area_id')) {
            $areas = Area::all()->toArray();
            $areaTree = new ChildrenTree($areas);
            $areaIds = $areaTree->getAllChildIds($areaId);
            $query->whereIn('area_id', $areaIds);
        }
        // 过滤状态
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        // 关键词过滤
        if ($keyword = trim(request('keyword', ''))) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%");
            });
        }

        $result = $query->paginate(20);
        foreach ($result->items() as $item) {
            $item->append('area_names');
        }
        return success([
            'listData' => formatListData($result),
        ]);
    }

    public function show(Apply $apply)
    {
        $apply->processes;
        $apply->append('area_names');
        return success([
            'item' => $apply->toArray(),
        ]);
    }

    /**
     * 审批.
     * @throws \Exception
     */
    public function verify(Apply $apply): JsonResponse
    {
        $result = request('result');
        $remark = request('remark');
        ShopApplyService::verify($apply, $result, $remark);
        return success();
    }
}
