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
use App\Utils\ChildrenTree;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $title
 * @property string $intro
 * @property string $permissions
 * @property int $asc_num
 * @property int $user_count
 * @property Department $parent
 * @property HasMany $users
 */
class Department extends Model
{
    //    use HasFactory;
    protected $table = 'administrator_departments';

    protected $fillable = [
        'parent_id',
        'title',
        'intro',
        'asc_num',
        'user_count',
    ];

    protected $attributes = [
        'parent_id' => null,
        'intro' => '',
        'asc_num' => 9,
        'user_count' => 0,
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'asc_num' => 'integer',
        'user_count' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(\App\Models\Shop\Seller::class, 'role_id');
    }

    public static function all($columns = ['*']): Collection|array
    {
        return static::query()
            ->with('parent')
            ->orderBy('asc_num')
            ->orderBy('id')
            ->get($columns);
    }

    public static function childrenTree(int $disabledId = 0): array
    {
        $disabled = $disabledId ? ['id' => $disabledId] : [];
        return ChildrenTree::make(static::all()->toArray(), $disabled);
    }
}
