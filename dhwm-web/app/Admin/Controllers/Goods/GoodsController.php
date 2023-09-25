<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Controllers\Goods;

use App\Admin\Requests\GoodsRequest;
use App\Admin\Services\GoodsService;
use App\Http\Controllers\Controller;
use App\Models\Goods\Category;
use App\Models\Goods\CategoryPivot;
use App\Models\Goods\Goods;
use Illuminate\Http\JsonResponse;

class GoodsController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Goods::query()
            ->orderByDesc('desc_num')
            ->orderByDesc('id')
            ->with(['tags', 'categories'/* , 'skus', 'skuNames', 'skuValues' */]);

        if ($cid = request('cat_id')) {
            $query->whereIn(
                'id',
                CategoryPivot::query()->where('category_id', $cid)->get('article_id')
            );
        }

        if ($keyword = request('keyword')) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('intro', 'like', "%{$keyword}%");
            });
        }

        return success([
            'listData' => formatListData($query->paginate(20)),
            'categories' => Category::childrenTree(),
        ]);
    }

    public function show(Goods $goods): JsonResponse
    {
        $goods->append(['cat_ids', 'skus_formatted']);
        $goods->load('tags');
        return success([
            'item' => $goods,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function create(GoodsRequest $request): JsonResponse
    {
        GoodsService::create(
            $this->getInputs(),
            request('tags', []),
            request('cat_ids', []),
            request('skus_formatted', [])
        );

        return success();
    }

    /**
     * @throws \Exception
     */
    public function update(GoodsRequest $request, Goods $goods): JsonResponse
    {
        GoodsService::update(
            $goods,
            $this->getInputs(),
            request('tags', []),
            request('cat_ids', []),
            request('skus_formatted', [])
        );

        return success();
    }

    public function destroy(Goods $goods)
    {
    }

    private function getInputs(): array
    {
        return inputs([
            'code', 'title', 'usp', 'album', 'intro', 'detail',
            'price', 'packing_fee', 'is_on_sale', 'asc_num',
        ]);
    }
}
