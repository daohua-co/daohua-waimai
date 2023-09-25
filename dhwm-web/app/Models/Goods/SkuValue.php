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
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $name_id
 * @property string $title
 * @property SkuName $name
 */
class SkuValue extends Model
{
    protected $table = 'goods_sku_values';

    protected $fillable = ['name_id', 'title'];

    protected $casts = [
        'name_id' => 'integer',
    ];

    public function name(): BelongsTo
    {
        return $this->belongsTo(SkuName::class, 'name_id');
    }
}
