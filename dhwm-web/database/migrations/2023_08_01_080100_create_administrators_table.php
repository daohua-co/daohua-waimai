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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('administrator_departments', function (Blueprint $table) {
            $table->id()->from(10000);
            $table->unsignedBigInteger('parent_id')->nullable()->comment('上级部门 id');
            $table->string('title')->comment('部门名称');
            $table->string('intro')->default('')->comment('部门简介');
            $table->unsignedMediumInteger('asc_num')->default(9);
            $table->unsignedInteger('user_count')->default(0)->comment('成员数量（包括子部门）');
            $table->timestamps();
        });

        Schema::create('administrators', static function (Blueprint $table) {
            $table->id()->from(10000);
            $table->foreignId('department_id')->nullable()->constrained('administrator_departments');
            $table->string('avatar')->default('')->comment('头像URL');
            $table->string('name')->default('')->index();
            $table->string('mobile')->default('')->index();
            $table->string('email')->default('')->index();
            $table->string('password')->default('');
            $table->string('realname')->default('')->comment('真实姓名');
            $table->unsignedTinyInteger('sex')->default(0)->comment('性别，0）未知，1）男，2）女');
            $table->date('birthday')->nullable();
            $table->boolean('enabled')->default(true)->comment('是否启用/禁用');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->index('remember_token');
        });

        (new \App\Models\Administrator\Administrator())->forceFill([
            'id' => 1,
            'name' => 'admin',
            'realname' => 'Administrator',
            'password' => Hash::make('123456'),
            'remember_token' => Str::random(60),
        ])->save();
    }

    public function down(): void
    {
        Schema::dropIfExists('administrator_departments');
        Schema::dropIfExists('administrators');
    }
};
