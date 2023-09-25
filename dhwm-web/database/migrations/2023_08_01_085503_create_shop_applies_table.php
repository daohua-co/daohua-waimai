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
        Schema::create('shop_applies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->comment('申请人id');
            $table->string('name')->comment('申请人姓名');
            $table->unsignedTinyInteger('sex')->comment('性别，1男，2女');
            $table->string('mobile')->default('')->comment('手机号码');
            $table->string('industry')->default('')->comment('目前从事行业');
            $table->unsignedTinyInteger('working_seniority')->comment('从业时间（年）');
            $table->foreignId('area_id')->comment('意向加盟区域（选择省/市/区）');
            $table->unsignedSmallInteger('town_population')->comment('意向加盟区域城镇人口（万人）');
            $table->unsignedInteger('available_capital')->comment('（万元）可投入资金（40万元起）');
            $table->unsignedTinyInteger('has_operating_team')->comment('是否有操盘团队（3~5人）');
            $table->string('advantage')->default('')->comment('你的优势（非必填，100字以内）');
            $table->enum('status', ['submit', 'accept', 'reject', 'locked'])->default('submit')->comment('状态为 locked 是锁定不允许再提交');
            $table->boolean('is_resubmit')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_applies');
    }
};
