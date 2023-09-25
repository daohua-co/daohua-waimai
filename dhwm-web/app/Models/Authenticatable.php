<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Models;

use App\Models\traits\SerializeDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;

/**
 * @method static null|Collection|static upsert(array $values, $uniqueBy, $update = null)
 * @method static null|Collection|static create(array $attributes = [])
 * @method static null|Collection|static firstOrCreate(array $attributes = [], array $values = [])
 * @method static null|Collection|static firstOrNew(array $attributes = [], array $values = [])
 * @method static null|Collection|static updateOrCreate(array $attributes = [], array $values = [])
 * @method static null|Builder[]|Collection|static find(mixed $id, $columns = ['*'])
 * @method static null|Builder[]|Collection|static findOrFail(mixed $id, $columns = ['*'])
 * @method static Builder where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder whereJsonContains($column, $value, $boolean = 'and', $not = false)
 * @method static Builder whereJsonLength($column, $operator, $value = null, $boolean = 'and')
 * @method static Builder whereJsonDoesntContain($column, $value, $boolean = 'and')
 * @property int $id
 */
class Authenticatable extends User
{
    use SoftDeletes;
    use SerializeDate;
}
