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

use App\Models\Area;
use App\Models\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $apply_id
 * @property int $area_id
 * @property string $address_detail
 * @property string $contact_mobile
 * @property string $business_license_img
 * @property string $status
 * @property Area $area
 * @property Apply $apply
 */
class Shop extends Model
{
    protected $table = 'shops';

    protected $fillable = [
        'apply_id',
        'area_id',
        'address_detail',
        'contact_mobile',
        'operation_mode',
        'operation_entity',
        'business_license_img',
        'status',
    ];

    protected $casts = [
        'apply_id' => 'integer',
        'area_id' => 'integer',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id')
            ->with('parentItem');
    }

    public function areaNames(): Attribute
    {
        return Attribute::make(
            get: fn () => Area::getLeafUpNames($this->area_id),
        );
    }

    public function apply(): BelongsTo
    {
        return $this->belongsTo(Apply::class, 'apply_id');
    }
}
