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

use App\Models\Administrator\Administrator;
use App\Models\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $title
 * @property string $intro
 * @property string $detail
 * @property int $asc_num
 * @property int $desc_num
 * @property array $album
 * @property array $tag_ids
 * @property array $cat_ids 商品分类 id，需手动 append
 * @property BelongsToMany $tags
 * @property BelongsToMany $categories
 * @property HasOne $creator
 */
class Content extends Model
{
    use SoftDeletes;

    protected $table = 'contents';

    protected $fillable = [
        'title',
        'album',
        'intro',
        'is_published',
        'asc_num',
        'desc_num',
        'creator_id',
        'author',
        'source',
        'editor',
        'detail',
    ];

    protected $casts = [
        'album' => 'array',
        'asc_num' => 'int',
        'desc_num' => 'int',
        'creator_id' => 'int',
        'is_published' => 'boolean',
    ];

    protected $appends = [
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'content_tag_pivots', 'content_id', 'tag_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'content_category_pivots', 'content_id', 'category_id');
    }

    public function catIds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->categories ? array_column($this->categories->toArray(), 'id') : [],
        );
    }

    public function tagIds(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->tags ? array_column($this->tags->toArray(), 'id') : [],
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Administrator::class, 'creator_id');
    }
}
