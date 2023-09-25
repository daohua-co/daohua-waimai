<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Services;

use App\Models\Administrator\Administrator;
use App\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class ImageService
{
    public static function saveUploadImage(UploadedFile $file, string $type): JsonResponse
    {
        $tempPath = $file->path();
        // 文件是否已存在
        $hash = sha1_file($tempPath);
        if ($type === 'shared') {
            $image = Image::query()
                ->where('hash', $hash)
                ->where('type', 'shared')
                ->first('path');
            if ($image) {
                return static::success($image->path);
            }
        }

        $dir = "images/{$type}/" . date('Ym/d');
        $path = $file->storePubliclyAs($dir, $file->hashName(), ['disk' => 'public']);

        // 保存记录
        Image::create([
            'path' => $path,
            'hash' => $hash,
            'size' => filesize($tempPath),
            'type' => $type,
            'module' => request('module', ''),
            'user_id' => administrator()->id,
            'user_type' => Administrator::class,
        ]);

        $imageContent = $file->getContent();

        // TODO 请求时动态生成缩略图
        $thumbSizes = [
            'lg' => [800, 0],
            'md' => [400, 400],
            'sm' => [200, 200],
            'tn' => [100, 100],
        ];

        static::saveThumbs($thumbSizes, $imageContent, $path);
        return static::success($path);
    }

    private static function success($path): JsonResponse
    {
        return success([
            'errno' => 0, // WangEditor 要求返回
            'data' => [
                'org' => storageUrl($path), // 原图
                'url' => storageUrl(static::thumbPath($path, 'lg')), // WangEditor 要求返回
                'thumb' => storageUrl(static::thumbPath($path, 'sm')),
            ],
        ]);
    }

    private static function error(mixed $message, int $errno = 1): JsonResponse
    {
        return response()->json([
            'errno' => $errno,
            'message' => $message,
        ], 400);
    }

    /**
     * @throws \Exception
     */
    private static function saveThumbs(array $sizes, string $imageContent, string $orgPath)
    {
        foreach ($sizes as $thumbType => $size) {
            $thumbPath = static::thumbPath($orgPath, $thumbType);
            thumb($imageContent, $thumbPath, $size[0], $size[1]);
        }
    }

    private static function thumbPath($orgPath, $type): string
    {
        return $orgPath . '-' . $type . '.jpg';
    }

    private function storeStaffAvatar($file, $dir)
    {
        return $file->storePubliclyAs($dir, administrator()->id . '.jpg', ['disk' => 'public']);
    }
}
