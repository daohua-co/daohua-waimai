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

class Setting extends Model
{
    public $timestamps = false;

    protected $table = 'settings';

    protected $fillable = ['key', 'group', 'title', 'value', 'input_type', 'options', 'tips'];

    protected $casts = [
        'options' => 'array',
    ];
}
