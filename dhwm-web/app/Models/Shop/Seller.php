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

use App\Models\Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $avatar
 * @property string $email
 * @property string $password
 * @property string $mobile
 * @property string $realname
 * @property bool $enabled 是否启用
 * @property string $remember_token
 * @property bool $is_owner
 * @property BelongsToMany $roles
 * @property array $role_ids
 * @property array $permissions
 * @property Shop $shop
 */
class Seller extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'shop_sellers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'shop_id',
        'name',
        'avatar',
        'email',
        'password',
        'mobile',
        'realname',
        'remember_token',
        'enabled',
        'sex',
        'birthday',
        'permissions',
        'is_owner',
    ];

    protected $attributes = [
        'avatar' => '',
        'email' => '',
        'mobile' => '',
        'realname' => '',
        'enabled' => 1,
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enabled' => 'boolean',
        'is_owner' => 'boolean',
        'sex' => 'integer',
        'permissions' => 'array',
    ];

    protected $appends = [
        'role_ids',
        'permissions',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'shop_role_sellers', 'user_id', 'role_id');
    }

    public function roleIds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->roles ? array_column($this->roles->toArray(), 'id') : [],
        );
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function permissions(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->is_owner) {
                // 店主拥有全部权限
                return ['*'];
            }

            // 从角色获取授权列表
            if (! $roles = $this->roles->toArray()) {
                return [];
            }

            $permissions = array_merge(...array_column($roles, 'permissions'));
            $permissions = array_unique($permissions);
            if (in_array('*', $permissions)) {
                return ['*'];
            }
            return $permissions;
        });
    }
}
