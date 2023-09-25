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

use App\Admin\Requests\AdministratorRequest;
use App\Admin\Services\AdministratorService;
use App\Http\Controllers\Controller;
use App\Models\Administrator\Administrator;
use App\Models\Administrator\Department;
use App\Models\Administrator\Role;
use App\Models\Administrator\RoleUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdministratorController extends Controller
{
    public function index(): Factory|View|Application|JsonResponse
    {
        $keyword = request('keyword');
        $roleId = (int) request('role_id');
        $departmentId = (int) request('department_id');

        $query = Administrator::query()
            ->orderByDesc('id')
            ->with(['roles', 'department']);

        if ($roleId) {
            $query->whereIn(
                'id',
                RoleUser::query()->select('user_id')->where('role_id', $roleId)
            );
        }

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($keyword) {
            AdministratorService::useQueryKeyword($query, $keyword);
        }

        $paginator = $query->paginate(15);
        $data = [
            'listData' => formatListData($paginator),
        ];

        if (request('with-roles')) {
            $data['roles'] = Role::all();
        }
        if (request('with-departments')) {
            $data['departments'] = Department::childrenTree();
        }

        return success($data);
    }

    /**
     * @throws \Exception
     */
    public function create(AdministratorRequest $request): JsonResponse
    {
        AdministratorService::create($this->getInputs(), request('role_ids', []));

        return success();
    }

    public function show(Administrator $user): JsonResponse
    {
        $user->department;
        $user->roles;

        $data = ['item' => $user];

        if (request('with-roles')) {
            $data['roles'] = Role::all();
        }

        return success($data);
    }

    public function update(AdministratorRequest $request, Administrator $user): JsonResponse
    {
        // 只有超级管理员才能管理超级管理员
        if ($user->is_super && ! administrator()->is_super) {
            abort(406, '你无权编辑超级管理员');
        }

        AdministratorService::update($user, $this->getInputs(), request('role_ids', []));

        return success();
    }

    public function destroy(Administrator $user): JsonResponse
    {
        abort_if($user->is_super, 406, '不能删除超级管理员');

        DB::transaction(function () use ($user) {
            $user->roles()->detach();
            $user->delete();
        });

        return success();
    }

    public function batDelete(): JsonResponse
    {
        $ids = request('ids');
        abort_if(! $ids, 406, '请求参数错误');

        Administrator::query()
            ->whereNot('id', config('daohua.super_uid'))
            ->whereIn('id', $ids)
            ->delete();

        return success(compact('ids'));
    }

    private function getInputs(): array
    {
        return inputs(['avatar', 'name', 'password', 'email', 'mobile', 'realname', 'enabled', 'department_id']);
    }
}
