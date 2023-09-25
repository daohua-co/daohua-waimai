<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoodsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // TODO 验证 code、goods_skus.code 不重复
        return [
            'title' => 'required',
            'price' => 'required|min:0.01',
            'asc_num' => 'between:0,9999',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '请输入标题',
            'price.required' => '请输入价格',
            'price.min' => '价格必须大于 0',
            'asc_num.between' => '显示排序必须在 :min - :max 之间',
        ];
    }
}
