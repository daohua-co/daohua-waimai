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

use App\Admin\Services\SellerService;
use App\Http\Controllers\Controller;
use App\Models\Shop\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $data = ['items' => Role::all()];

        if (request('with-permissions')) {
            $data['permissions'] = SellerService::getPermissionTree();
        }

        return success($data);
    }

    public function create(): JsonResponse
    {
        Role::create($this->getInputs());

        return success();
    }

    public function show(Role $item): JsonResponse
    {
        $item->shop->append('area_names');
        return success([
            'item' => $item,
        ]);
    }

    public function update(Role $item): JsonResponse
    {
        $item->update($this->getInputs());
        return success();
    }

    public function destroy(Role $item): JsonResponse
    {
        $hasUser = $item->sellers()->exists();
        abort_if($hasUser, 406, '该角色下有店员，不能删除');

        $item->delete();
        return success();
    }

    private function getInputs(): array
    {
        request()->validate(
            [
                'title' => 'required',
                'shop_id' => 'required',
            ],
            ['title.required' => '请输入角色名称']
        );
        return inputs([
            'title',
            'intro',
            'permissions',
            'asc_num',
            'is_show',
            'enabled',
            'shop_id',
        ]);
    }
}
