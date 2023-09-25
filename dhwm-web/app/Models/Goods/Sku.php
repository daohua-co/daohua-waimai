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

use App\Models\Model;

/**
 * @property int $id
 * @property int $goods_id
 * @property string $sku_key
 * @property string $code
 * @property float $price
 * @property int $stocks 库存
 * @property int $sale_count sku 销量
 */
class Sku extends Model
{
    protected $table = 'goods_skus';

    protected $fillable = [
        'goods_id',
        'sku_key',
        'code',
        'price',
    ];

    protected $casts = [
        'goods_id' => 'integer',
        'price' => 'float',
        'sale_count' => 'integer',
    ];
}
