<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Controllers\Administrator;

use App\Http\Controllers\Controller;
use App\Models\Administrator\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        return success([
            'items' => Department::childrenTree(),
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $inputs = $this->validatedInputs($request);
        Department::create($inputs);

        return success([
            'options' => Department::childrenTree(),
        ]);
    }

    public function show(Department $department): JsonResponse
    {
        if (request('with-parent')) {
            $department->parent;
        }

        $data = ['item' => $department];
        if (request('with-parent-options')) {
            $data['options'] = Department::childrenTree($department->id);
        }

        return success($data);
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        $inputs = $this->validatedInputs($request);
        $department->update($inputs);

        // 返回更新后的列表
        return success([
            'options' => Department::childrenTree($department->id),
        ]);
    }

    public function validatedInputs(Request $request): array
    {
        $request->validate(
            ['title' => 'required'],
            ['title.required' => '请输入部门名称']
        );

        $inputs = inputs(['title', 'parent_id', 'intro', 'asc_num']);
        empty($inputs['parent_id']) && $inputs['parent_id'] = null; // 0、'' => null

        return $inputs;
    }

    public function destroy(Department $department): JsonResponse
    {
        // 有员工，不允许删除
        abort_if($department->user_count > 0, 406, '部门下有员工，不允许删除');

        // 有子分类，不允许删除
        $hasChild = Department::query()
            ->where('parent_id', $department->id)
            ->exists();
        abort_if($hasChild, 406, '该部门有子部门，不允许删除');

        $department->delete();

        return success();
    }
}
