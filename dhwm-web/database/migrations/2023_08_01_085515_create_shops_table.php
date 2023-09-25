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
        Schema::create('shops', function (Blueprint $table) {
            $table->id()->from(10000);
            $table->foreignId('apply_id')->nullable()->comment('申请id')->constrained('shop_applies');
            $table->foreignId('area_id');
            $table->string('address_detail')->default('')->comment('地址详情（不包括省/市/区）');
            $table->string('contact_mobile')->default('')->comment('联系电话');
            $table->string('operation_mode')->default('jm')->comment('店铺经营类型：jm)加盟，zj)自营');
            $table->string('operation_entity')->default('')->comment('经营主体');
            $table->string('business_license_img')->default('')->comment('营业执照图片路径');
            $table->string('status')->default('pre')->comment('pre:筹备中，on:营业中，off：已关停');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
