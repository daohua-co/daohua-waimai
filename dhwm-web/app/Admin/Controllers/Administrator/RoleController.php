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

use App\Admin\Services\AdminPermissionService;
use App\Http\Controllers\Controller;
use App\Models\Administrator\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $data = ['items' => Role::all()];

        if (request('with-permissions')) {
            $data['permissions'] = AdminPermissionService::getPermissionTree();
        }

        return success($data);
    }

    public function create(): JsonResponse
    {
        Role::create($this->validatedInputs());

        return success();
    }

    public function show(Role $role): JsonResponse
    {
        return success([
            'item' => $role,
        ]);
    }

    public function update(Role $role): JsonResponse
    {
        $role->update($this->validatedInputs());
        return success();
    }

    public function destroy(Role $role): JsonResponse
    {
        $hasUser = $role->users()->exists();
        abort_if($hasUser, 406, '该角色下有员工，不能删除');

        $role->delete();
        return success([
            'items' => Role::all(),
        ]);
    }

    private function validatedInputs(): array
    {
        request()->validate(
            ['title' => 'required'],
            ['title.required' => '请输入角色名称']
        );
        return inputs(['title', 'intro', 'permissions', 'asc_num', 'is_show', 'enabled']);
    }
}
