<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Models\Content;

use App\Models\Model;
use App\Utils\ChildrenTree;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property int $parent_id
 * @property int $asc_num
 * @property int $content_count
 * @property bool $is_show
 * @property string $intro
 * @property static $parent
 */
class Category extends Model
{
    use HasFactory;

    protected $table = 'content_categories';

    protected $fillable = ['parent_id', 'title', 'intro', 'asc_num', 'is_show'];

    protected $attributes = [
        'parent_id' => null,
        'asc_num' => 99,
        'content_count' => 0,
        'is_show' => 1,
        'intro' => '',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'asc_num' => 'integer',
        'content_count' => 'integer',
        'is_show' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public static function all($columns = ['*']): Collection|array
    {
        return static::query()
            ->orderBy('asc_num')
            ->orderBy('id')
            ->get($columns);
    }

    public static function childrenTree(int $disabledId = 0): array
    {
        $items = static::all(['id', 'parent_id', 'asc_num', 'title', 'intro', 'is_show', 'content_count', 'created_at', 'updated_at'])
            ->toArray();
        $disabled = $disabledId ? ['id' => $disabledId] : [];
        return ChildrenTree::make($items, $disabled);
    }

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(
            Content::class,
            'content_category_pivots',
            'category_id',
            'content_id'
        );
    }
}
