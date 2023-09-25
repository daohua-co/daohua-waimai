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
        Schema::create('goods_sku_names', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('goods_sku_values', function (Blueprint $table) {
            $table->id()->from(100000);
            $table->foreignId('name_id')->constrained('goods_sku_names');
            $table->string('title');
            $table->timestamps();
        });

        Schema::create('goods_sku_name_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_id')->constrained('goods');
            $table->foreignId('name_id')->constrained('goods_sku_names');
            $table->unsignedTinyInteger('enable_image')->default(0)->comment('规格启用图片');
            $table->unsignedTinyInteger('show_image')->default(0)->comment('商品详情规格列表显示为图片形式');
            $table->unsignedTinyInteger('sort_index')->comment('显示顺序序号（自动）');
            $table->timestamps();
        });

        Schema::create('goods_sku_value_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_id')->constrained('goods');
            $table->foreignId('value_id')->constrained('goods_sku_values');
            $table->string('image_path')->default('')->comment('规格图片');
            $table->timestamps();
        });

        Schema::create('goods_skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_id')->constrained('goods');
            $table->string('sku_key')->comment('规格值列表（逻辑关联 goods_sku_values），从小到大排序，以半角逗号隔开');
            $table->string('code')->default('');
            $table->decimal('price');
            $table->unsignedBigInteger('sale_count')->default(0)->comment('总销量');
            $table->timestamps();
            $table->unique(['goods_id', 'sku_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_sku_names');
        Schema::dropIfExists('goods_sku_values');
        Schema::dropIfExists('goods_sku_name_uses');
        Schema::dropIfExists('goods_sku_value_uses');
        Schema::dropIfExists('goods_skus');
    }
};
