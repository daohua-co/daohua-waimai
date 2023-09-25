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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
 * @property BelongsToMany $sellers
 * @property Shop $shop
 */
class Role extends Model
{
    protected $table = 'shop_roles';

    protected $fillable = [
        'shop_id',
        'title',
        'intro',
        'asc_num',
        'permissions',
        'enabled',
    ];

    protected $casts = [
        'shop_id' => 'integer',
        'asc_num' => 'integer',
        'permissions' => 'array',
        'enabled' => 'boolean',
    ];

    public function findRolesByShopId(int $id, $columns = ['*']): Collection|array
    {
        return static::where('shop_id', $id)->get($columns);
    }

    public function sellers(): BelongsToMany
    {
        return $this->belongsToMany(Seller::class, 'shop_role_sellers', 'role_id', 'user_id');
    }

    public function shop(): BelongsTo|Shop
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}
