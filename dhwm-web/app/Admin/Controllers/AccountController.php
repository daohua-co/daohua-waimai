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

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function chPassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => 'required|min:6',
            'password' => 'required|min:6|confirmed',
        ]);

        $account = administrator();

        $password = request('password');
        if (! Hash::check(request('old_password'), $account->password)) {
            return fail('旧密码不正确');
        }

        $account->forceFill([
            'password' => Hash::make($password),
        ])->setRememberToken(Str::random(60));

        $account->save();

        return success();
    }

    public function chProfile(Request $request): JsonResponse
    {
        $account = administrator();
        $messages = [
            'mobile.unique' => '该手机号已被使用',
            'email.unique' => '该邮箱已被使用',
        ];
        $inputs = $request->validate([
            'avatar' => 'nullable',
            'realname' => 'nullable|min:2',
            'mobile' => 'nullable|regex:/^1[3-9]\\d{9}$/|unique:administrators,mobile,' . $account->id,
            'email' => 'nullable|email|unique:administrators,email,' . $account->id,
        ]);

        $account->fill($inputs)
            ->save();

        return success();
    }
}
