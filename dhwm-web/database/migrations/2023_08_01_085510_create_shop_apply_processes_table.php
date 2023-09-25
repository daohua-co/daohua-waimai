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
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_apply_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apply_id')->constrained('shop_applies');
            $table->foreignId('admin_id')->constrained('administrators');
            $table->enum('result', ['accept', 'reject', 'locked'])->comment('审核结果，accept:通过，reject:驳回，locked:锁定');
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_apply_processes');
    }
};
