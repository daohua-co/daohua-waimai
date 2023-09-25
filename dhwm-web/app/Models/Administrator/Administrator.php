<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Models\Administrator;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $avatar
 * @property string $email
 * @property string $password
 * @property string $mobile
 * @property string $realname
 * @property bool $is_super 是否是超级管理员
 * @property bool $enabled 是否启用
 * @property string $remember_token
 * @property BelongsToMany $roles
 * @property array $role_ids
 * @property Department $department
 * @property array $permissions
 */
class Administrator extends \App\Models\Authenticatable
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'administrators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'email',
        'password',
        'mobile',
        'realname',
        'remember_token',
        'enabled',
        'department_id',
    ];

    protected $attributes = [
        'avatar' => '',
        'email' => '',
        'mobile' => '',
        'realname' => '',
        'enabled' => 1,
        'department_id' => null,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
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
    ];

    protected $appends = [
        'role_ids',
        'permissions',
        'is_super',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'administrator_role_users', 'user_id', 'role_id');
    }

    public function isSuper(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->id === (int) config('daohua.super_uid')
        );
    }

    public function roleIds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->roles ? array_column($this->roles->toArray(), 'id') : [],
        );
    }

    public function permissions(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->is_super) {
                // 超级管理员拥有全部权限
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
