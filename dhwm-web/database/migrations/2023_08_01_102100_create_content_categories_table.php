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
    public function up()
    {
        Schema::create('content_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title');
            $table->string('intro')->default('')->comment('简介');
            $table->unsignedMediumInteger('content_count')->default(0);
            $table->unsignedSmallInteger('asc_num')->default(99)->comment('正序排序数');
            $table->boolean('is_show')->default(true)->comment('在前台菜单中是否显示');
            $table->timestamps();
        });
        Schema::create('content_category_pivots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents');
            $table->foreignId('category_id')->constrained('content_categories');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('content_categories');
        Schema::dropIfExists('content_category_pivots');
    }
};
