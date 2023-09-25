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

use App\Models\Administrator\Administrator;
use App\Models\Administrator\Role;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdministratorService
{
    public static function useQueryKeyword(Builder $query, $keyword): void
    {
        $query->where(function ($query) use ($keyword) {
            $query->where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%")
                ->orWhere('mobile', 'like', "%{$keyword}%")
                ->orWhere('realname', 'like', "%{$keyword}%");
        });
    }

    /**
     * @throws \Exception
     */
    public static function create(array $attributes, array $roleIds): Administrator
    {
        $attributes = static::castInputs($attributes);
        $attributes['remember_token'] = Str::random(60);

        try {
            DB::beginTransaction();
            /**
             * @var Administrator $user
             */
            $user = Administrator::create($attributes);
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
    public static function update(Administrator $user, array $attributes, array $roleIds): void
    {
        $attributes = static::castInputs($attributes);
        if (! empty($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }

        $oldRoleIds = $user->role_ids;
        $intersect = array_intersect($roleIds, $oldRoleIds); // 交集
        $bothMerge = array_unique([...$roleIds, ...$oldRoleIds]); // 合并
        $updateCountRoleIds = array_diff($bothMerge, $intersect); // 把交集去掉不更新

        try {
            DB::beginTransaction();
            $user->update($attributes);
            // `员工-角色`关联同步
            $user->roles()->sync($roleIds);
            static::updateRolesUserCount($updateCountRoleIds);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function updateRolesUserCount(array $roleIds): void
    {
        foreach ($roleIds as $id) {
            if (! $role = Role::find($id)) {
                continue;
            }

            $role->forceFill([
                'user_count' => $role->users->count(),
            ])->save();
        }
    }

    private static function castInputs($attributes)
    {
        $attributes = array_map('trim', $attributes);
        $attributes['name'] = strtolower($attributes['name']);
        $attributes['enabled'] = (int) $attributes['enabled'];
        $attributes['department_id'] = $attributes['department_id'] ?: null; // 外键默认值是 null

        return $attributes;
    }
}
