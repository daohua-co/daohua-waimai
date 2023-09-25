<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Models\Goods;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 商品使用规格值的中间表。
 * @property Carbon $created_at
 */
class SkuValueUse extends Pivot
{
    protected $table = 'goods_sku_value_uses';

    protected $fillable = [
        'goods_id',
        'value_id',
        'image_path',
    ];

    protected $casts = [
        'goods_id' => 'integer',
        'value_id' => 'integer',
    ];
}
