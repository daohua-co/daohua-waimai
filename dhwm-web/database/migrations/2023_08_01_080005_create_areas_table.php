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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->index();
            $table->string('name');
            $table->string('short_name')->default('');
            $table->decimal('lat', 10, 7)->nullable()->comment('纬度坐标'); // 为兼容多数据库，不用 point 类型
            $table->decimal('lng', 10, 7)->nullable()->comment('经度坐标');
            $table->unsignedTinyInteger('joined')->default(0)->comment('是否已有加盟店（城市joined 为1 表示该市区县已全都加盟）');
        });
        // 录入地区数据
        Artisan::call('app:import-areas');
        echo Artisan::output();
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
