<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Models\traits;

use Illuminate\Support\Carbon;

trait SerializeDate
{
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return Carbon::instance($date)->toDateTimeString();
    }
}
