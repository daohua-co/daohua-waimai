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
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->string('code')->default('')->comment('商品编号')->index();
            $table->string('title');
            $table->string('usp')->default('')->comment('独特卖点（Unique Selling Point）');
            $table->json('album')->nullable()->comment('商品相册');
            $table->string('intro')->default('')->comment('商品简介');
            $table->mediumText('detail')->nullable()->comment('商品详细介绍');
            $table->decimal('price')->comment('售价');
            $table->unsignedInteger('sales_count')->default(0)->comment('总销量');
            $table->unsignedInteger('like_count')->default(0)->comment('点赞量');
            $table->unsignedInteger('evaluate_count')->default(0)->comment('评价数量');
            $table->unsignedFloat('evaluate_score')->default(0)->comment('平均评价分');
            $table->unsignedSmallInteger('asc_num')->default(50)->comment('正序排序数');
            $table->unsignedSmallInteger('desc_num')->comment('asc_num 反向数，用于反向排序，9999 - asc_num');
            $table->boolean('has_sku')->default(false)->comment('是否有规格');
            $table->boolean('is_on_sale')->default(false)->comment('是否售卖中');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods');
    }
};
