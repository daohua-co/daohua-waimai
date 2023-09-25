<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Seller\Services;

use App\Models\Shop\Seller;

class SellerPermissionService
{
    /**
     * 当前用户是否有权限访问功能.
     */
    public static function can(Seller $seller, string $ability): bool
    {
        // 账号已停用
        abort_if(! $seller->enabled, 403, '该账号已停用');

        if ($seller->is_owner) {
            // 超级管理员有全部权限
            return true;
        }

        return in_array($ability, $seller->permissions, true);
    }
}
