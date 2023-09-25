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

use App\Models\Area;
use App\Models\Shop\Apply;
use App\Models\Shop\ApplyProcess;
use App\Models\Shop\Seller;
use App\Models\Shop\Shop;
use Illuminate\Support\Facades\DB;

class ShopApplyService
{
    /**
     * @throws \Exception
     */
    public static function verify(Apply $apply, string $result, string $remark): void
    {
        abort_if($apply->status === 'accept', 400, '该申请已通过审核，不能再审核');
        abort_if(! in_array($result, ['accept', 'reject', 'locked']), 400, '参数错误');

        $area = Area::findOrFail($apply->area_id);

        try {
            DB::beginTransaction();
            // 修改申请状态
            $apply->update(['status' => $result]);
            // 保存操作记录
            ApplyProcess::create([
                'apply_id' => $apply->id,
                'result' => $result,
                'remark' => $remark,
                'admin_id' => administrator()->id,
            ]);

            if ($result === 'accept') {
                abort_if($area->joined, 400, '该区域已有加盟，不能再加盟');
                // 创建账号
                $shop = Shop::create([
                    'apply_id' => $apply->id,
                    'area_id' => $apply->area_id,
                    'contact_mobile' => $apply->mobile,
                    'operation_mode' => 'jm',
                ]);
                // 创建店铺店主账号
                Seller::create([
                    'shop_id' => $shop->id,
                    'realname' => $apply->name,
                    'mobile' => $apply->mobile,
                    'sex' => $apply->sex,
                    'is_owner' => true,
                ]);
                // 将地区设为已加盟
                $area->update(['joined' => true]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
