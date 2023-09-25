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
        Schema::create('shop_warehouses', function (Blueprint $table) {
            $table->id()->from(10000);
            $table->foreignId('shop_id')->nullable();
            $table->string('name')->comment('送货点/仓库');
            $table->string('intro')->nullable()->comment('配送点简介');
            $table->string('address')->nullable()->comment('仓库详细地址，省/市/区之后的部分');
            $table->multiPoint('delivery_map_points')->comment('配送范围坐标多点');
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_warehouses');
    }
};
