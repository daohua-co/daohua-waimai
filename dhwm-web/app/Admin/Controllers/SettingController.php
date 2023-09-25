<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        $group = request('group');
        $query = Setting::query();
        if ($group) {
            $query->where('group', $group);
        }

        return success([
            'items' => $query->get()->all(),
        ]);
    }

    public function save(): JsonResponse
    {
        $settings = request('settings');
        foreach ($settings as $key => $value) {
            Setting::where('key', $key)->update([
                'value' => $value,
            ]);
        }
        return success();
    }
}
