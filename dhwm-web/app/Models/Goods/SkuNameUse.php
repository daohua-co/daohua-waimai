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

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * 商品使用规格名的中间表。
 */
class SkuNameUse extends Pivot
{
    protected $table = 'goods_sku_name_uses';

    protected $fillable = [
        'goods_id',
        'name_id',
        'enable_image',
        'show_image',
        'sort_index',
    ];

    protected $casts = [
        'goods_id' => 'integer',
        'name_id' => 'integer',
        'enable_image' => 'boolean',
        'show_image' => 'boolean',
    ];
}
