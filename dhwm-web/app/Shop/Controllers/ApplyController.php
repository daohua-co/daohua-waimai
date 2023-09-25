<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Shop\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop\Apply;
use Illuminate\Http\JsonResponse;

/**
 * 加盟申请.
 */
class ApplyController extends Controller
{
    /**
     * 提交加盟申请。
     */
    public function create(): JsonResponse
    {
        $inputs = request()->validate([
            'name' => 'required',
            'sex' => 'required',
            'mobile' => 'required|regex:/^1[3-9]\\d{9}$/',
            'industry' => 'required',
            'working_seniority' => 'required',
            'area_id' => 'required',
            'town_population' => 'required',
            'available_capital' => 'required',
            'has_operating_team' => 'required',
            'advantage' => 'nullable',
        ], [
            'name.required' => '请填写姓名',
            'sex.required' => '请选择性别',
            'mobile.required' => '请填写手机号',
            'mobile.regex' => '手机号码格式错误',
            'industry.required' => '请填写目前从事行业',
            'working_seniority.required' => '请填写从业时间',
            'area_id.required' => '请选择意向加盟区域',
            'town_population.required' => '请填写意向加盟区域城镇人口',
            'available_capital.required' => '请填写可投入资金',
            'has_operating_team.required' => '请选择是否有操作团队',
        ]);

        $inputs['user_id'] = request()->user()->id;
        Apply::create($inputs);

        return success();
    }

    /**
     * 用户加盟信息。
     */
    public function mine()
    {
    }
}
