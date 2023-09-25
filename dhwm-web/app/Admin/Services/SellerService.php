<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Services;

use App\Models\Shop\Role;
use App\Models\Shop\Seller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SellerService
{
    public static function getPermissionTree(): array
    {
        $options = config('seller-permissions');
        return makePermissions($options);
    }

    /**
     * @throws \Exception
     */
    public static function create(array $attributes): Collection|Seller|null
    {
        $attributes = static::preprocess($attributes);
        $roleIds = (array) request('role_ids');
        try {
            DB::beginTransaction();
            /**
             * @var Seller $user
             */
            $user = Seller::create($attributes);
            // 员工关联角色
            if ($roleIds) {
                $user->roles()->attach($roleIds);
                static::updateRolesUserCount($roleIds);
            }
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public static function update(Seller $seller, array $attributes): void
    {
        $attributes = static::preprocess($attributes);
        unset($attributes['shop_id']); // 不允许修改所属店铺

        $roleIds = (array) request('role_ids');
        $oldRoleIds = $seller->role_ids;
        $intersect = array_intersect($roleIds, $oldRoleIds); // 交集
        $bothMerge = array_unique([...$roleIds, ...$oldRoleIds]); // 合并
        $updateCountRoleIds = array_diff($bothMerge, $intersect); // 把交集去掉不更新

        try {
            DB::beginTransaction();
            $seller->update($attributes);
            // `员工-角色`关联同步
            $seller->roles()->sync($roleIds);
            // 更新角色用户数统计
            static::updateRolesUserCount($updateCountRoleIds);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function destroy(Seller $item): ?bool
    {
        // todo
        // 不允许删除店主
        abort_if($item->is_owner, 400, '不能删除店主');
        return $item->delete();
    }

    public static function updateRolesUserCount(array $roleIds): void
    {
        foreach ($roleIds as $id) {
            if (! $role = Role::find($id)) {
                continue;
            }

            $role->forceFill([
                'user_count' => $role->sellers->count(),
            ])->save();
        }
    }

    protected static function preprocess($attributes)
    {
        if (empty($attributes['permissions'])) {
            $attributes['permissions'] = [];
        }
        if (! empty($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }
        return $attributes;
    }
}
