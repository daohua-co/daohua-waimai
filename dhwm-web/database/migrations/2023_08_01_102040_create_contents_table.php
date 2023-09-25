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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('creator_id')->constrained('administrators');
            $table->string('author')->default('')->comment('作者');
            $table->string('source')->default('')->comment('来源');
            $table->string('editor')->default('')->comment('编辑');
            $table->string('intro')->default('')->comment('简介');
            $table->mediumText('detail')->nullable()->comment('详细介绍');
            $table->json('album')->nullable()->comment('商品相册');
            $table->unsignedSmallInteger('asc_num')->default(50)->comment('正序排序数');
            $table->unsignedSmallInteger('desc_num')->comment('asc_num 反向数，用于反向排序，9999 - asc_num');
            $table->boolean('is_published')->default(false)->comment('发布状态：0）草稿，1）发布');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['asc_num', 'id']);
            $table->index(['desc_num', 'id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contents');
    }
};
