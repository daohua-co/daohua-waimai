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
        Schema::create('administrator_roles', static function (Blueprint $table) {
            $table->id()->from(1000);
            $table->string('title')->comment('角色名称');
            $table->string('intro')->default('')->comment('角色简介');
            $table->unsignedMediumInteger('asc_num')->default(9);
            $table->json('permissions')->nullable();
            $table->unsignedInteger('user_count')->default(0)->comment('成员数量');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        Schema::create('administrator_role_users', static function (Blueprint $table) {
            $table->id(); // 关联表 id 范围尽量大
            $table->foreignId('user_id')->constrained('administrators');
            $table->foreignId('role_id')->constrained('administrator_roles');
        });

        \App\Models\Administrator\Role::upsert([
            ['title' => '系统管理员', 'intro' => '拥有系统全部权限', 'permissions' => '["*"]'],
            ['title' => '编辑', 'intro' => '', 'permissions' => '[]'],
        ], 'id');
    }

    public function down(): void
    {
        Schema::dropIfExists('administrator_roles');
        Schema::dropIfExists('administrator_role_users');
    }
};
