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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property mixed $id
 */
class Tag extends Model
{
    public $timestamps = false;

    protected $table = 'goods_tags';

    protected $fillable = ['title'];

    protected $casts = [
        'goods_count' => 'integer',
    ];

    public function goods(): BelongsToMany
    {
        return $this->belongsToMany(Goods::class, 'goods_tag_pivots', 'tag_id', 'goods_id');
    }
}
