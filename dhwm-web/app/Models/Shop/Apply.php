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
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $sex
 * @property string $mobile
 * @property string $industry
 * @property int $working_seniority
 * @property int $area_id
 * @property int $town_population
 * @property int $available_capital
 * @property bool $has_operating_team
 * @property string $advantage
 * @property string $status
 * @property Area $area
 * @property HasMany $processes
 */
class Apply extends Model
{
    public const STATUSES = [
        'submit' => '待审核',
        'accept' => '审核通过',
        'reject' => '审核驳回',
        'locked' => '已被锁定',
    ];

    protected $table = 'shop_applies';

    protected $fillable = [
        'user_id',
        'name',
        'sex',
        'mobile',
        'industry',
        'working_seniority',
        'area_id',
        'town_population',
        'available_capital',
        'has_operating_team',
        'advantage',
        'status',
    ];

    protected $casts = [
        'working_seniority' => 'integer',
        'has_operating_team' => 'boolean',
        'sex' => 'integer',
        'area_id' => 'integer',
        'user_id' => 'integer',
        'available_capital' => 'integer',
    ];

    protected $attributes = [
    ];

    protected $appends = ['status_text'];

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

    public function processes(): HasMany
    {
        return $this->hasMany(ApplyProcess::class, 'apply_id')
            ->with('administrator')
            ->orderByDesc('id');
    }

    public function statusText(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUSES[$this->status]
        );
    }
}
