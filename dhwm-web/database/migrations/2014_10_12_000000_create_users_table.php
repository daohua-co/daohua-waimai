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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mobile')->default('');
            $table->string('email')->default('');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('wx_open_id')->default('');
            $table->string('wx_union_id')->default('');
            $table->string('password')->nullable();
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别，0）未知，1）男，2）女');
            $table->date('birthday')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
