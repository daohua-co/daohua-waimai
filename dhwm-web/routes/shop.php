<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

use App\Shop\Controllers\ApplyController;
use App\Shop\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('shop')->middleware('web')->group(function () {
    Route::get('/', [HomeController::class, 'dashboard'])->name('shop.dashboard');
    Route::get('/join-apply/mine', [ApplyController::class, 'mine']);
    Route::get('/join-apply', [ApplyController::class, 'create']);
    // Route::post('/auth/login', [AuthController::class, 'login'])->name('shop.login');
    // Route::post('/auth/logout', [AuthController::class, 'logout'])->name('shop.logout');
});
