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
use App\Admin\Requests\SellerRequest;
use App\Admin\Services\SellerService;
use App\Models\Shop\Seller;
use App\Models\Shop\Shop;
use Illuminate\Http\JsonResponse;

class SellerController extends Controller
{
    public function index(): JsonResponse
    {
        $shopId = request('shop_id');
        $shop = Shop::findOrFail($shopId);
        $shop->append('area_names');

        $query = Seller::query()
            ->where('shop_id', $shopId)
            ->orderByDesc('id');

        $enabled = request('input', '');
        if ($enabled !== '') {
            $query->where('enabled', $enabled);
        }
        // keyword
        if ($keyword = request('keyword')) {
            $query->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%")
                    ->orWhere('realname', 'like', "%{$keyword}%");
            });
        }
        $result = $query->paginate(20);
        return success([
            'listData' => formatListData($result),
            'shop' => $shop->toArray(),
        ]);
    }

    public function show(Seller $item): JsonResponse
    {
        return success([
            'item' => $item->toArray(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function create(SellerRequest $request): JsonResponse
    {
        SellerService::create($request->validated());
        return success();
    }

    /**
     * @throws \Exception
     */
    public function update(SellerRequest $request, Seller $item): JsonResponse
    {
        SellerService::update($item, $request->validated());
        return success();
    }

    public function destroy(Seller $item): JsonResponse
    {
        SellerService::destroy($item);
        return success();
    }
}
