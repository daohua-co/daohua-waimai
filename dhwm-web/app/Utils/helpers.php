<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

use App\Models\Administrator\Administrator;
use App\Utils\ChildrenTree;
use App\Utils\ImageProcess;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

if (! function_exists('success')) {
    function success(array $data = [], mixed $message = ''): JsonResponse
    {
        $data['success'] = true;
        if ($message) {
            $data['message'] = $message;
        }
        return response()->json($data);
    }
}

if (! function_exists('fail')) {
    function fail(mixed $message, array $errors = [], int $statusCode = 400): JsonResponse
    {
        $data = [
            'success' => false,
            'code' => $statusCode, // 客户端一些 js 库的 API 不能获取到 http 状态码，因此在返回数据中返回
            'message' => $message,
        ];

        if ($errors) {
            $data['errors'] = $errors;
        }

        return response()->json($data, $statusCode);
    }
}

if (! function_exists('isMobile')) {
    function isMobile(string $input): bool
    {
        return (bool) preg_match('/^(?:(?:\\+|00)86)?1[3-9]\\d{9}$/', $input);
    }
}

if (! function_exists('isEmail')) {
    function isEmail(string $input): bool
    {
        return (bool) preg_match('/^[\\w\\-]+@[a-z0-9_\\-]+(\\.[a-z0-9_\\-]+)+$/i', $input);
    }
}

if (! function_exists('adminGuard')) {
    /**
     * 以 admin/ 开头的 URL 的看守。
     */
    function adminGuard(): Guard|StatefulGuard
    {
        return auth('admin');
    }
}

if (! function_exists('sellerGuard')) {
    /**
     * 以 seller/ 开头的 URL 的看守。
     */
    function sellerGuard(): Guard|StatefulGuard
    {
        return auth('seller');
    }
}

if (! function_exists('seller')) {
    function seller(): App\Models\Shop\Seller|Authenticatable|null
    {
        return sellerGuard()->user();
    }
}

if (! function_exists('administrator')) {
    function administrator(): Administrator|Authenticatable|null
    {
        return adminGuard()->user();
    }
}

if (! function_exists('inputs')) {
    function inputs(array $keys = null, mixed $defaults = null): array
    {
        $inputs = $keys ? request()->only($keys) : request()->all();

        if (is_null($defaults)) {
            return $inputs;
        }

        $keys || $keys = array_keys($inputs);
        foreach ($keys as $key => $val) {
            if (! is_null($val)) {
                continue;
            }
            if (is_array($defaults)) {
                $inputs[$key] = $defaults[$key] ?? null;
                continue;
            }
            $inputs[$key] = $defaults;
        }

        return $inputs;
    }
}

if (! function_exists('formatListData')) {
    function formatListData(LengthAwarePaginator $paginator): array
    {
        return [
            'path' => $paginator->path(),
            'items' => $paginator->items(),
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
        ];
    }
}

if (! function_exists('thumb')) {
    /**
     * 生成缩略图。
     * 指定宽高则缩放后裁剪，只指定宽或高则等比缩小。
     * @param string $imageContent 原图内容
     * @param string $targetPath 小图保存路径
     * @throws Exception
     */
    function thumb(string $imageContent, string $targetPath, int $width, int $height): void
    {
        if (! $imageContent) {
            return;
        }
        $image = new ImageProcess($imageContent);
        Storage::disk('public')->put($targetPath, $image->thumb($width, $height));
    }
}

if (! function_exists('storageUrl')) {
    function storageUrl($path): string
    {
        return asset('storage/' . $path);
    }
}

if (! function_exists('isInAdmin')) {
    /**
     * 访问的 URI 是否是管理后台。
     */
    function isInAdmin(): bool
    {
        return request()->is('admin*');
    }
}

if (! function_exists('isInConsole')) {
    function isInConsole(): bool
    {
        return app()->runningInConsole();
    }
}

if (! function_exists('trimArray')) {
    function trimArray(array $data): array
    {
        return array_map(fn ($value) => is_string($value) ? trim($value) : $value, $data);
    }
}

if (! function_exists('assets')) {
    function assets(array $assets): string
    {
        $html = '';
        foreach ($assets as $path) {
            if (str_ends_with($path, '.css')) {
                $html .= '<link href="' . asset($path) . '" rel="stylesheet" />';
                continue;
            }
            $html .= '<script src="' . asset($path) . '" defer></script>';
        }

        return $html;
    }
}

if (! function_exists('makePermissions')) {
    function makePermissions($options): array
    {
        $items = [];
        foreach ($options as $key => $option) {
            $lastDotPos = strrpos($key, '.');
            $items[] = [
                'name' => $key,
                'parent_name' => $lastDotPos ? substr($key, 0, $lastDotPos) : '',
                'title' => $option['title'],
            ];
            if (empty($option['items'])) {
                continue;
            }
            foreach ($option['items'] as $action => $title) {
                $items[] = [
                    'name' => "{$key}.{$action}",
                    'parent_name' => $key,
                    'title' => $title,
                ];
            }
        }

        return ChildrenTree::make($items, props: [
            'id' => 'name',
            'parent_id' => 'parent_name',
        ]);
    }
}
