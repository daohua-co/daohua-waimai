<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
    ];

    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // token 错误则重新下发 token，退出当前资源
            return $this->responseAndResendToken($request, $e->getMessage());
        }
    }

    protected function getTokenFromRequest($request): ?string
    {
        if ($xsrfToken = $request->input('_xsrf_token')) {
            // 为解决 element-plus upload 组件不能通过 header 提交响应式变量值，
            // 通过 body 提交 _xsrf_token 参数来验证 token。
            try {
                return CookieValuePrefix::remove($this->encrypter->decrypt($xsrfToken, static::serialized()));
            } catch (DecryptException) {
            }
        }

        return parent::getTokenFromRequest($request);
    }

    protected function responseAndResendToken($request, $message): JsonResponse
    {
        $cookie = $this->newCookie($request, config('session'));
        return response()
            ->json(['code' => 419, 'message' => $message, 'token_cookie_sent' => 1], 419)
            ->withCookie($cookie);
    }
}
