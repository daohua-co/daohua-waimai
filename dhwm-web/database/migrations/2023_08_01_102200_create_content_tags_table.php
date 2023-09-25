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
        Schema::create('content_tags', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->unsignedInteger('content_count')->default(0)->comment('引用数');
            $table->timestamps();
        });

        Schema::create('content_tag_pivots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('contents');
            $table->foreignId('tag_id')->constrained('content_tags');
            $table->timestamps();
            $table->unique(['content_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_tags');
        Schema::dropIfExists('content_tag_pivots');
    }
};
