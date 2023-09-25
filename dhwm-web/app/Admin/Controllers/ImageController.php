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

use App\Admin\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function index()
    {
    }

    /**
     * @throws \Exception
     */
    public function save(Request $request)
    {
        if (! $request->hasFile('img')) {
            return $this->error('请选择上传图片');
        }

        $file = $request->file('img');
        if (! $file->isValid()) {
            return $this->error('非法的图片');
        }

        $type = request('type', 'shared');
        // shared 类型永久保存
        // 其它类型由程序控制保存（新建/覆盖/删除）
        if (! in_array($type, ['shared', 'album', 'avatar'])) {
            return $this->error('type 参数不正确');
        }

        return ImageService::saveUploadImage($file, $type);
    }
}
