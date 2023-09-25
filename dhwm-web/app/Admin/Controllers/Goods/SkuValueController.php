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

use App\Http\Controllers\Controller;
use App\Models\Goods\SkuValue;
use Illuminate\Http\JsonResponse;

class SkuValueController extends Controller
{
    public function index(): JsonResponse
    {
        if (! $nameId = request('name_id')) {
            return fail('name_id 参数是必须的');
        }

        $items = SkuValue::query()
            ->where('name_id', $nameId)
            ->get(['id', 'title'])
            ->all();

        return success(compact('items'));
    }
}
