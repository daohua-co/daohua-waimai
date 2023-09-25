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

class SellerRequest extends FormRequest
{
    //    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|min:2|regex:/^[a-z][0-9a-z]+$/i',
            'realname' => 'nullable|min:2',
            'mobile' => 'nullable|regex:/^1[3-9]\\d{9}$/',
            'email' => 'nullable|email',
            'avatar' => 'nullable',
            'sex' => 'required',
            'birthday' => 'nullable',
            'enabled' => 'required',
            'permissions' => 'nullable',
        ];

        if ($this->isMethod('POST') || ! empty(request('password'))) {
            // 新建或修改密码时，验证密码规则
            $rules['password'] = 'required|min:6|confirmed';
        }

        if ($this->isMethod('PUT')) {
            $item = $this->route('item');
            $rules['name'] .= '|unique:administrators,name,' . $item->id;
            $rules['mobile'] .= '|unique:administrators,mobile,' . $item->id;
            $rules['email'] .= '|unique:administrators,email,' . $item->id;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => '请输入用户名',
            'name.min' => '用户名长度不能小于 :min 位',
            'name.regex' => '用户名只能以字母开头，由字母和数字组成',
            'name.unique' => '用户名已被使用',
            'password.required' => '请输入密码',
            'password.min' => '密码长度不能小于 :min 位',
            'password.confirmed' => '两次输入密码不一致',
            'mobile.regex' => '手机号码格式不正确',
            'mobile.unique' => '手机号码已被使用',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '邮箱已被使用',
        ];
    }
}
