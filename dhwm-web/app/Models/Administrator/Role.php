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

use App\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $title
 * @property string $intro
 * @property string $permissions
 * @property int $asc_num
 * @property bool $enabled
 * @property int $user_count
 * @property string $type
 * @property BelongsToMany $users
 */
class Role extends Model
{
    //    use HasFactory;

    protected $table = 'administrator_roles';

    protected $fillable = [
        'title',
        'intro',
        'permissions',
        'asc_num',
        'enabled',
        'type',
    ];

    protected $attributes = [
        'intro' => '',
        'permissions' => '[]',
        'asc_num' => 9,
        'enabled' => 1,
    ];

    protected $casts = [
        'permissions' => 'array',
        'asc_num' => 'integer',
        'enabled' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Administrator::class, 'administrator_role_users', 'role_id', 'user_id');
    }

    public static function all($columns = ['*']): Collection|array
    {
        return static::query()
            ->orderBy('asc_num')
            ->orderBy('id')
            ->get($columns);
    }
    /*
        protected function permissions(): Attribute
        {
            return Attribute::make(
                get: fn ($value) => is_null($value) ? [] : json_decode($value, false, 512, JSON_THROW_ON_ERROR),
            );
        }*/
}
