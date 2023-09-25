<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('shop_roles', static function (Blueprint $table) {
            $table->id()->from(10000);
            $table->foreignId('shop_id');
            $table->string('title')->comment('角色名称');
            $table->string('intro')->default('')->comment('角色简介');
            $table->unsignedMediumInteger('asc_num')->default(9);
            $table->json('permissions')->nullable();
            $table->unsignedInteger('user_count')->default(0)->comment('成员数量');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        Schema::create('shop_role_sellers', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('shop_sellers');
            $table->foreignId('role_id')->constrained('shop_roles');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_roles');
        Schema::dropIfExists('shop_role_sellers');
    }
};
