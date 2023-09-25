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
use App\Models\Shop\Shop;
use App\Models\Shop\Warehouse;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    public function index(): JsonResponse
    {
        $shopId = request('shop_id');
        $shop = Shop::findOrFail($shopId);
        $shop->append('area_names');

        $query = Warehouse::query()
            ->where('shop_id', $shopId)
            ->orderByDesc('id');

        return success([
            'items' => $query->get(),
            'shop' => $shop->toArray(),
        ]);
    }

    public function show(Warehouse $item): JsonResponse
    {
        $item->append('area_names');
        return success([
            'item' => $item->toArray(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function create(): JsonResponse
    {
        $inputs = $this->getInputs();

        return success();
    }

    public function update(Warehouse $item): JsonResponse
    {
        $inputs = $this->getInputs();
        unset($inputs['area_id']); // 部允许修改区域
        $item->update($inputs);
        return success();
    }

    private function getInputs(): array
    {
        return request()->validate([
            'shop_id' => 'required',
            'name' => 'required',
        ], []);
    }
}
