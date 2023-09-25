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

use App\Models\Administrator\Administrator;
use App\Models\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $apply_id
 * @property string $result
 * @property string $remark
 * @property Administrator $administrator
 */
class ApplyProcess extends Model
{
    public const RESULTS = [
        'accept' => '通过',
        'reject' => '驳回',
        'locked' => '锁定',
    ];

    protected $table = 'shop_apply_processes';

    protected $fillable = [
        'apply_id',
        'result',
        'remark',
        'admin_id',
    ];

    protected $casts = [
        'apply_id' => 'integer',
    ];

    protected $attributes = [
    ];

    protected $appends = ['result_text'];

    public function resultText(): Attribute
    {
        return Attribute::make(
            get: fn () => self::RESULTS[$this->result]
        );
    }

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'admin_id');
    }
}
