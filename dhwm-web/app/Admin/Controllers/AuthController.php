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
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function profile(): JsonResponse
    {
        return success([
            'userInfo' => administrator(),
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $this->validation();
        $account = trim($credentials['account']);

        // 支持邮箱、手机号、用户名登录
        if (isEmail($account)) {
            $type = 'email';
        } elseif (isMobile($account)) {
            $type = 'mobile';
        } else {
            $type = 'name';
        }

        // 登录错误限流
        $this->rateLimit($account, $type);

        $attemptData = [
            $type => $account,
            'password' => $credentials['password'],
        ];

        if (adminGuard()->attempt($attemptData, true)) {
            // 登录成功后清空限流
            RateLimiter::clear('admin-auth:' . $account);
            // 账号验证成功
            $request->session()->regenerate();
            return success(['userInfo' => administrator()]);
        }

        return fail('账号或密码错误');
    }

    public function logout(): JsonResponse
    {
        adminGuard()->logout();
        return success();
    }

    private function rateLimit($account, $type): string
    {
        // 一天限登录错误20次
        $executable = RateLimiter::attempt(
            'admin-auth:' . $account,
            20,
            function () {
            },
            24 * 3600
        );

        if ($executable) {
            return '';
        }

        $types = [
            'email' => '邮箱',
            'mobile' => '手机号',
            'name' => '用户名',
        ];
        $currentType = $types[$type];
        unset($types[$type]);

        $message = '今天你的"' . $currentType . '"登录错误次数过多，请更换 '
            . implode('、', $types) . '登录或等到明天再登录。';
        abort(406, $message);
    }

    private function validation(): array
    {
        return request()->validate(
            [
                'account' => 'required',
                'password' => 'required|min:6',
            ],
            [
                'account.required' => '请输入账号',
                'password.required' => '请输入密码',
                'password.min' => '密码长度不能少于 :min 位',
            ]
        );
    }
}
