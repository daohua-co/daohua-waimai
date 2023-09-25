<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Utils;

/**
 * 图片处理类，使用GD2生成缩略图和打水印。
 * @see https://github.com/estart-cc/image
 */
class ImageProcess
{
    protected array $imgInfo = [];

    /**
     * 图片二进制内容.
     */
    protected string $imageContent = '';

    /**
     * 缩略图背景颜色.
     */
    protected array $bgColor = [255, 255, 255];

    /**
     * @throws \RuntimeException
     */
    public function __construct(string $imageContent, array $bgRgb = null)
    {
        if (! function_exists('gd_info')) {
            throw new \RuntimeException('你的php没有使用gd2扩展，不能处理图片');
        }

        @ini_set('memory_limit', '512M');  // 处理大图片的时候要较较大的内存

        if (! $imageContent || ! ($this->imgInfo = @getimagesizefromstring($imageContent))) {
            throw new \RuntimeException('错误的图片文件！');
        }

        $this->imageContent = $imageContent;

        $bgRgb && $this->bgColor = $bgRgb;
    }

    /**
     * @param string $thumbType png/jpeg/gif/webp
     * @throws \Exception
     */
    public function thumb(int $thumbWidth, int $thumbHeight, bool $isCut = true, int $cutPlace = 5, int $quality = 90, string $thumbType = 'jpeg'): string
    {
        if (! in_array($thumbType, ['png', 'jpeg', 'gif', 'webp'])) {
            throw new \Exception('缩略图后缀必须是 png/jpg/gif/webp');
        }

        if ($isCut) {
            $thumbImage = $this->thumbCut($thumbWidth, $thumbHeight, $cutPlace);
        } else {
            $thumbImage = $this->thumbUnCut($thumbWidth, $thumbHeight);
        }

        // 在缓冲区创建缩略图
        // 为兼容云存贮设备，不直接把缩略图写入文件系统，而是返回文件内容
        ob_start();
        $imageFun = 'image' . $thumbType;
        $imageFun($thumbImage, null, $quality);
        $thumb = ob_get_clean();
        imagedestroy($thumbImage);

        if (! $thumb) {
            throw new \RuntimeException('无法生成缩略图');
        }

        return $thumb;
    }

    /**
     * @throws \RuntimeException
     */
    public function watermark(string $watermarkFile, int $watermarkPlace = 9, int $quality = 90): null|string
    {
        if (! is_readable($watermarkFile)) {
            throw new \RuntimeException('不能读取水印图片');
        }

        @[$imgW, $imgH] = $this->imgInfo;

        $watermarkInfo = @getimagesize($watermarkFile);
        $watermarkLogo = ($watermarkInfo['mime'] == 'image/png') ? @imagecreatefrompng($watermarkFile) : @imagecreatefromgif($watermarkFile);

        if (! $watermarkLogo) {
            return null;
        }

        [$logoW, $logoH] = $watermarkInfo;
        $wmWidth = $imgW - $logoW; // 水印横向边距
        $wmHeight = $imgH - $logoH; // 水印纵向边距

        if ($wmWidth < 10 || $wmHeight < 10) {
            // 图片太小，不宜打水印
            return null;
        }

        switch ($watermarkPlace) {
            case 1:
                $x = +5;
                $y = +5;
                break;
            case 2:
                $x = ($imgW - $logoW) / 2;
                $y = +5;
                break;
            case 3:
                $x = $imgW - $logoW - 5;
                $y = +5;
                break;
            case 4:
                $x = +5;
                $y = ($imgH - $logoH) / 2;
                break;
            case 6:
                $x = $imgW - $logoW;
                $y = ($imgH - $logoH) / 2;
                break;
            case 7:
                $x = +5;
                $y = $imgH - $logoH - 5;
                break;
            case 8:
                $x = ($imgW - $logoW) / 2;
                $y = $imgH - $logoH - 5;
                break;
            case 9:
                $x = $imgW - $logoW - 5;
                $y = $imgH - $logoH - 5;
                break;
            default:
                $x = ($imgW - $logoW) / 2;
                $y = ($imgH - $logoH) / 2;
                break;
        }

        $dstImage = imagecreatetruecolor($imgW, $imgH);
        imagefill($dstImage, 0, 0, imagecolorallocate($dstImage, $this->bgColor[0], $this->bgColor[1], $this->bgColor[2]));
        $targetImage = @imagecreatefromstring($this->imageContent);

        imagecopy($dstImage, $targetImage, 0, 0, 0, 0, $imgW, $imgH);
        imagecopy($dstImage, $watermarkLogo, $x, $y, 0, 0, $logoW, $logoH);

        ob_start();
        imagejpeg($dstImage, null, $quality);
        imagedestroy($dstImage);
        return ob_get_clean();
    }

    /**
     * 不裁剪方式生成缩略图.
     * @return resource
     */
    private function thumbUnCut(int $thumbWidth, int $thumbHeight)
    {
        [$srcW, $srcH] = $this->imgInfo;

        // 宽或高按比例缩放
        if ($thumbWidth == 0 || $thumbHeight == 0) {
            if ($thumbWidth == 0) {
                $thumbHeight = $srcH * ($thumbWidth / $srcW);
            } else {
                $thumbWidth = $srcW * ($thumbHeight / $srcH);
            }
            $imgW = $thumbWidth; // 图片显示宽
            $imgH = $thumbHeight; // 图片显示高
            $posX = 0;
            $posY = 0;
        } else {
            if ($thumbWidth / $thumbHeight < $srcW / $srcH) {
                // 宽比例超过，补上高
                $imgW = $thumbWidth; // 图片显示宽
                $imgH = $thumbWidth * $srcH / $srcW; // 图片显示高
                $posX = 0;
                $posY = ($thumbHeight - $imgH) / 2;
            } else {
                // 高比例超过，补上宽
                $imgH = $thumbHeight; // 图片显示宽
                $imgW = $thumbHeight * $srcW / $srcH; // 图片显示高
                $posX = ($thumbWidth - $imgW) / 2;
                $posY = 0;
            }
        }

        $thumbImage = imagecreate((int) $thumbWidth, (int) $thumbHeight);
        $attachImage = imagecreatefromstring($this->imageContent);

        // 填充背景色
        $fillColor = imagecolorallocate($thumbImage, $this->bgColor[0], $this->bgColor[1], $this->bgColor[2]);
        imagefill($thumbImage, 0, 0, $fillColor);
        imagecopyresized($thumbImage, $attachImage, (int) $posX, (int) $posY, 0, 0, (int) $imgW, (int) $imgH, (int) $srcW, (int) $srcH);
        imagedestroy($attachImage);

        return $thumbImage;
    }

    /**
     * 裁剪方式生成缩略图.
     * @return resource
     */
    private function thumbCut(int $thumbWidth, int $thumbHeight, int $cutPlace = 5)
    {
        [$srcW, $srcH] = $this->imgInfo;

        $imgH = $srcH;  // 取样图片高
        $imgW = $srcW;  // 取样图片宽
        $srcX = 0; // 取样图片x坐标开始值
        $srcY = 0; // 取样图片y坐标开始值

        if ($thumbWidth == 0) {
            // 宽等比例缩放
            $thumbWidth = $srcW * ($thumbHeight / $srcH);
        } elseif ($thumbHeight == 0) {
            // 高等比例缩放
            $thumbHeight = $srcH * ($thumbWidth / $srcW);
        } elseif ((($thumbWidth / $imgW) * $imgH) > $thumbHeight) {
            // 高需要截掉
            $imgH = ($imgW / $thumbWidth) * $thumbHeight;
            // 高开始截取位置
            if (in_array($cutPlace, [4, 5, 6])) {
                $srcY = ($srcH - $imgH) / 2;
            } elseif (in_array($cutPlace, [7, 8, 9])) {
                $srcY = $srcH - $imgH;
            }
        } else {
            // 宽需要截掉
            $imgW = ($imgH / $thumbHeight) * $thumbWidth;

            if (in_array($cutPlace, [2, 5, 8])) {
                $srcX = ($srcW - $imgW) / 2;
            } elseif (in_array($cutPlace, [3, 6, 9])) {
                $srcX = $srcW - $imgW;
            }
        }

        $thumbWidth = (int) $thumbWidth;
        $thumbHeight = (int) $thumbHeight;

        $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        $attachImage = imagecreatefromstring($this->imageContent);

        if ($this->imgInfo['mime'] == 'image/gif') {
            imagecolortransparent($attachImage, imagecolorallocate($attachImage, $this->bgColor[0], $this->bgColor[1], $this->bgColor[2]));
        } elseif ($this->imgInfo['mime'] == 'image/png') {
            imagealphablending($thumbImage, false); // 关闭混合模式，以便透明颜色能覆盖原画布
            imagesavealpha($thumbImage, true);
        }

        // 重采样拷贝部分图像并调整大小到$thumbImage
        imagecopyresampled($thumbImage, $attachImage, 0, 0, (int) $srcX, (int) $srcY, $thumbWidth, $thumbHeight, (int) $imgW, (int) $imgH);

        imagedestroy($attachImage);

        return $thumbImage;
    }
}
