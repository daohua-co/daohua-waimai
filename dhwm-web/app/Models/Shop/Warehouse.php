<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Models\Shop;

use App\Models\Model;

/**
 * @property int $shop_id
 * @property string $name
 * @property string $intro
 * @property string $address
 * @property bool $enabled
 * @property array $delivery_map_points
 */
class Warehouse extends Model
{
    protected $table = 'shop_warehouses';

    protected $fillable = [
        'shop_id', 'name', 'intro', 'address', 'delivery_map_points', 'enabled',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'enabled' => 'boolean',
        'delivery_map_points' => 'array',
    ];
}
