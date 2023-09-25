<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Controllers\Administrator;

use App\Admin\Services\AdministratorService;
use App\Http\Controllers\Controller;
use App\Models\Administrator\Administrator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class RecycleController extends Controller
{
    public function index(): Factory|View|Application|JsonResponse
    {
        $keyword = request('keyword');
        $query = Administrator::onlyTrashed()
            ->orderByDesc('id')
            ->with(['roles', 'department']);

        if ($keyword) {
            AdministratorService::useQueryKeyword($query, $keyword);
        }

        $paginator = $query->paginate(15);
        $data = [
            'listData' => formatListData($paginator),
        ];

        return success($data);
    }

    public function restore(int $id): JsonResponse
    {
        $user = Administrator::onlyTrashed()->find($id);

        if (! $user) {
            return fail('回收站中不存在该账号');
        }

        $user->forceFill([
            'deleted_at' => null,
        ])->save();

        return success();
    }
}
