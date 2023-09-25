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
        Schema::create('shop_sellers', function (Blueprint $table) {
            $table->id()->from(10000);
            $table->foreignId('shop_id')->nullable();
            $table->string('avatar')->default('')->comment('商家头像URL');
            $table->string('name')->default('')->index();
            $table->string('mobile')->default('')->index();
            $table->string('email')->default('')->index();
            $table->string('password')->default('');
            $table->string('realname')->default('')->comment('真实姓名');
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别，0）未知，1）男，2）女');
            $table->date('birthday')->nullable();
            $table->boolean('enabled')->default(true)->comment('是否启用/禁用');
            $table->json('permissions')->nullable();
            $table->boolean('is_owner')->default(false)->comment('是否是店主');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index('remember_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_sellers');
    }
};
