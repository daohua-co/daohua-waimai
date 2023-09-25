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
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 */
class SkuName extends Model
{
    protected $table = 'goods_sku_names';

    protected $fillable = ['title'];

    public function values(): HasMany
    {
        return $this->hasMany(SkuValue::class, 'name_id');
    }
}
