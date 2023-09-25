<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

use App\Admin\Controllers\AccountController;
use App\Admin\Controllers\Administrator\AdministratorController;
use App\Admin\Controllers\Administrator\DepartmentController;
use App\Admin\Controllers\Administrator\PermissionController;
use App\Admin\Controllers\Administrator\RecycleController;
use App\Admin\Controllers\Administrator\RoleController;
use App\Admin\Controllers\AreaController;
use App\Admin\Controllers\AuthController;
use App\Admin\Controllers\Content;
use App\Admin\Controllers\Goods;
use App\Admin\Controllers\HomeController;
use App\Admin\Controllers\ImageController;
use App\Admin\Controllers\SettingController;
use App\Admin\Controllers\Shop;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->middleware('web')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('admin.home');

    Route::controller(AuthController::class)->group(function () {
        Route::get('/auth/profile', 'profile')->name('admin.profile');
        Route::post('/auth/login', 'login')->name('admin.login');
        Route::post('/auth/logout', 'logout')->name('admin.logout');
    });

    // 登录后可访问
    Route::middleware('auth:admin')->group(function () {
        //        Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('admin.dashboard');
        Route::controller(Shop\ApplyController::class)->group(function () {
            Route::get('/shops/applies', 'index');
            Route::get('/shops/applies/{apply}', 'show');
            Route::patch('/shops/applies/{apply}', 'verify');
        });
        Route::controller(Shop\RoleController::class)->group(function () {
            Route::get('/shops/roles', 'index');
            Route::get('/shops/roles/{item}', 'show');
            Route::put('/shops/roles/{item}', 'update');
            Route::delete('/shops/roles/{item}', 'destroy');
            Route::post('/shops/roles', 'create');
        });
        Route::get('/shops/permissions', [Shop\PermissionController::class, 'index']);
        Route::controller(Shop\SellerController::class)->group(function () {
            Route::post('/shops/sellers', 'create');
            Route::get('/shops/sellers', 'index');
            Route::get('/shops/sellers/{item}', 'show');
            Route::put('/shops/sellers/{item}', 'update');
            Route::delete('/shops/sellers/{item}', 'destroy');
        });
        Route::controller(Shop\WarehouseController::class)->group(function () {
            Route::get('/shops/warehouses', 'index');
            Route::get('/shops/warehouses/{item}', 'show');
            Route::put('/shops/warehouses/{item}', 'update');
            Route::post('/shops/warehouses', 'create');
        });
        Route::controller(Shop\ShopController::class)->group(function () {
            Route::get('/shops', 'index');
            Route::get('/shops/{shop}', 'show');
            Route::put('/shops/{shop}', 'update');
            Route::post('/shops', 'create');
        });
        // 文章分类
        Route::controller(Content\CategoryController::class)->group(function () {
            Route::get('/contents/categories', 'index')->middleware('can:content.category.viewAll');
            Route::post('/contents/categories', 'create')->middleware('can:content.category.create');
            Route::get('/contents/categories/{category}', 'show')->middleware('can:content.category.view');
            Route::put('/contents/categories/{category}', 'update')->middleware('can:content.category.update');
            Route::delete('/contents/categories/{category}', 'destroy')->middleware('can:content.category.delete');
        });

        // 文章标签
        Route::controller(Content\TagController::class)->group(function () {
            Route::get('/contents/tags/hot', 'hot');
            Route::get('/contents/tags', 'index')->middleware('can:content.tag.viewAll');
            Route::post('/contents/tags', 'create')->middleware('can:content.tag.create');
            Route::get('/contents/tags/{tag}', 'show')->middleware('can:content.tag.view');
            Route::put('/contents/tags/{tag}', 'update')->middleware('can:content.tag.update');
            Route::delete('/contents/tags/bat/delete', 'batDelete')->middleware('can:content.tag.batDelete');
            Route::delete('/contents/tags/{tag}', 'destroy')->middleware('can:content.tag.delete');
        });

        // 文章
        Route::controller(Content\ContentController::class)->group(function () {
            Route::get('/contents', 'index')->middleware('can:content.viewAll');
            Route::get('/contents/{content}', 'show')->middleware('can:content.view');
            Route::post('/contents', 'create')->middleware('can:content.create');
            Route::put('/contents/{content}', 'update')->middleware('can:content.update');
            Route::delete('/contents/{content}', 'destroy')->middleware('can:content.delete');
        });

        //        // 商品分类
        Route::controller(Goods\CategoryController::class)->group(function () {
            Route::get('/goods/categories', 'index')->middleware('can:goods.category.viewAll');
            Route::post('/goods/categories', 'create')->middleware('can:goods.category.create');
            Route::get('/goods/categories/{category}', 'show')->middleware('can:goods.category.view');
            Route::put('/goods/categories/{category}', 'update')->middleware('can:goods.category.update');
            Route::delete('/goods/categories/{category}', 'destroy')->middleware('can:goods.category.delete');
        });

        // 商品标签
        Route::controller(Goods\TagController::class)->group(function () {
            Route::get('/goods/tags/hot', 'hot');
            Route::get('/goods/tags', 'index')->middleware('can:goods.tag.viewAll');
            Route::post('/goods/tags', 'create')->middleware('can:goods.tag.create');
            Route::get('/goods/tags/{tag}', 'show')->middleware('can:goods.tag.view');
            Route::put('/goods/tags/{tag}', 'update')->middleware('can:goods.tag.update');
            Route::delete('/goods/tags/bat/delete', 'batDelete')->middleware('can:goods.tag.batDelete');
            Route::delete('/goods/tags/{tag}', 'destroy')->middleware('can:goods.tag.delete');
        });

        // 规格
        Route::get('/goods/sku-names', [Goods\SkuNameController::class, 'index']);
        Route::get('/goods/sku-values', [Goods\SkuValueController::class, 'index']);

        // 商品
        Route::controller(Goods\GoodsController::class)->group(function () {
            Route::get('/goods', 'index')->middleware('can:goods.viewAll');
            Route::get('/goods/{goods}', 'show')->middleware('can:goods.view');
            Route::post('/goods', 'create')->middleware('can:goods.create');
            Route::put('/goods/{goods}', 'update')->middleware('can:goods.update');
            Route::delete('/goods/{goods}', 'destroy')->middleware('can:goods.delete');
        });

        // 部门
        Route::controller(DepartmentController::class)->group(function () {
            Route::get('/administrators/departments', 'index')->middleware('can:administrator.department.viewAll');
            Route::post('/administrators/departments', 'create')->middleware('can:administrator.department.create');
            Route::get('/administrators/departments/{department}', 'show')->middleware('can:administrator.department.view');
            Route::put('/administrators/departments/{department}', 'update')->middleware('can:administrator.department.update');
            Route::delete('/administrators/departments/{department}', 'destroy')->middleware('can:administrator.department.delete');
        });

        // 权限项目列表
        Route::get('/administrators/permissions', [PermissionController::class, 'index']);

        // 角色
        Route::controller(RoleController::class)->group(function () {
            Route::get('/administrators/roles', 'index')->middleware('can:administrator.role.viewAll');
            Route::post('/administrators/roles', 'create')->middleware('can:administrator.role.create');
            Route::get('/administrators/roles/{role}', 'show')->middleware('can:administrator.role.view');
            Route::put('/administrators/roles/{role}', 'update')->middleware('can:administrator.role.update');
            Route::delete('/administrators/roles/{role}', 'destroy')->middleware('can:administrator.role.delete');
        });

        // 回收站
        Route::controller(RecycleController::class)->group(function () {
            Route::get('/administrators/recycles', 'index')->middleware('can:administrator.recycle.viewAll');
            Route::patch('/administrators/recycles/{id}', 'restore')->middleware('can:administrator.recycle.restore');
        });

        // 员工
        Route::controller(AdministratorController::class)->group(function () {
            Route::get('/administrators', 'index')->middleware('can:administrator.viewAll');
            Route::post('/administrators', 'create')->middleware('can:administrator.create');
            Route::get('/administrators/{user}', 'show')->withTrashed()->middleware('can:administrator.view');
            Route::put('/administrators/{user}', 'update')->middleware('can:administrator.update');
            Route::delete('/administrators/bat/delete', 'batDelete')->middleware('can:administrator.batDelete');
            Route::delete('/administrators/{user}', 'destroy')->middleware('can:administrator.delete');
        });
        // 账号
        Route::controller(AccountController::class)->group(function () {
            Route::patch('/account/ch-password', 'chPassword');
            Route::patch('/account/ch-profile', 'chProfile');
        });
        // 系统设置
        Route::controller(SettingController::class)->group(function () {
            Route::get('/settings', 'index');
            Route::post('/settings', 'save');
        });
        // 图片上传
        Route::post('/images', [ImageController::class, 'save']);
        // 行政区
        Route::controller(AreaController::class)->group(function () {
            Route::get('/areas', 'index')->middleware('can:area.viewAll');
            Route::get('/areas/{item}', 'show')->middleware('can:area.show');
            Route::put('/areas/{item}', 'update')->middleware('can:area.update');
        });
    });
});
