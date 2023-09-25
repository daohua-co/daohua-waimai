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
use App\Models\Area;
use App\Models\Shop\Seller;
use App\Models\Shop\Shop;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Shop::query()
            ->with(['apply'])
            ->orderByDesc('id');
        // 过滤地区
        if ($areaId = (int) request('area_id')) {
            $areaIds = Area::getChildIds($areaId);
            $query->whereIn('area_id', $areaIds);
        }
        // 过滤状态
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        // 过滤电话
        if ($keyword = trim(request('keyword', ''))) {
            $query->where(function ($query) use ($keyword) {
                $query->where('id', 'like', "{$keyword}%")
                    ->orWhere('contact_mobile', 'like', "%{$keyword}%");
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

    public function show(Shop $shop): JsonResponse
    {
        $shop->append('area_names');
        $shop->apply;
        return success([
            'item' => $shop->toArray(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function create(): JsonResponse
    {
        $inputs = $this->getInputs();
        // 检查区域
        $area = Area::findOrFail($inputs['area_id']);
        if ($area->joined) {
            return fail('该区域已有门店，不能再创建门店');
        }
        try {
            DB::beginTransaction();
            $shop = Shop::create($inputs);
            // 创建店主账号
            Seller::create([
                'shop_id' => $shop->id,
                'realname' => $area->name . '店',
                'mobile' => $shop->contact_mobile,
                'is_owner' => true,
            ]);
            // 将地区设为已加盟
            $area->update(['joined' => true]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        return success();
    }

    public function update(Shop $shop): JsonResponse
    {
        $inputs = $this->getInputs();
        unset($inputs['area_id']); // 部允许修改区域
        $shop->update($inputs);
        return success();
    }

    private function getInputs(): array
    {
        return request()->validate([
            'area_id' => 'required',
            'address_detail' => 'required',
            'contact_mobile' => 'required',
            'operation_mode' => 'required',
            'operation_entity' => 'required',
            'status' => 'required',
            'business_license_img' => 'nullable',
        ], []);
    }
}
