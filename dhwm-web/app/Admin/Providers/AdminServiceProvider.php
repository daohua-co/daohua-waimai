<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Providers;

use App\Admin\Services\AdminPermissionService;
use App\Models\Administrator\Administrator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // $this->mergeConfigFrom(config_path('admin-permissions.php'), 'admin-permissions');
        $this->loadRoutesFrom(base_path('routes/admin.php'));

        // 管理后台 rbac
        Gate::before(function (Administrator $user, string $ability) {
            return AdminPermissionService::can($user, $ability);
        });
    }
}
