<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Admin\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Content\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return success([
            'items' => Category::childrenTree(),
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $inputs = $this->validatedInputs($request);
        Category::create($inputs);

        return success([
            'options' => Category::childrenTree(),
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        if (request('with-parent')) {
            $category->parent;
        }

        $data = ['item' => $category];
        if (request('with-parent-options')) {
            $data['options'] = Category::childrenTree($category->id);
        }

        return success($data);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $inputs = $this->validatedInputs($request);
        $category->update($inputs);

        // 返回更新后的列表
        return success([
            'options' => Category::childrenTree($category->id),
        ]);
    }

    public function validatedInputs(Request $request): array
    {
        $request->validate(
            ['title' => 'required'],
            ['title.required' => '请输入栏目名']
        );

        $inputs = inputs(['title', 'parent_id', 'intro', 'asc_num', 'is_show']);
        empty($inputs['parent_id']) && $inputs['parent_id'] = null; // 0、'' => null

        return $inputs;
    }

    public function destroy(Category $category): JsonResponse
    {
        // 有文章，不允许删除
        abort_if($category->content_count > 0, 406, '分类下有文章，不允许删除');

        // 有子分类，不允许删除
        $hasChild = Category::query()
            ->where('parent_id', $category->id)
            ->exists();
        abort_if($hasChild, 406, '文章分类有子分类，不允许删除');

        $category->delete();

        return success();
    }
}
