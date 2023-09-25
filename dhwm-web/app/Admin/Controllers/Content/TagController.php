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
use App\Models\Content\Tag;
use App\Models\Content\TagPivot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Tag::query();

        if ($keyword = request('keyword')) {
            $query->where('title', 'like', "%{$keyword}%");
        }

        if (request('sort') === 'content_count') {
            $query->orderByDesc('content_count');
        } else {
            $query->orderByDesc('id');
        }

        $paginator = $query->paginate(15);

        return success([
            'listData' => formatListData($paginator),
        ]);
    }

    public function hot(): JsonResponse
    {
        $limit = request('limit', 10);
        $items = Tag::query()
            ->orderByDesc('content_count')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->all();
        return success(compact('items'));
    }

    public function create(Request $request): JsonResponse
    {
        $inputs = $this->validatedInputs($request);
        Tag::create($inputs);

        return success();
    }

    public function show(Tag $tag): JsonResponse
    {
        return success([
            'item' => $tag,
        ]);
    }

    public function update(Request $request, Tag $tag): JsonResponse
    {
        $inputs = $this->validatedInputs($request, $tag);
        $tag->update($inputs);

        return success();
    }

    public function validatedInputs(Request $request, Tag $tag = null): array
    {
        $rules = [
            'title' => 'required|unique:content_tags',
        ];

        if ($request->isMethod('PUT')) {
            $rules['title'] .= ',title,' . $tag->id;
        }

        return $request->validate(
            $rules,
            [
                'title.required' => '请输入标签名',
                'title.unique' => '标签名已存在',
            ]
        );
    }

    public function destroy(Tag $tag): JsonResponse
    {
        // 删除标签同时删除与文章的关联
        DB::transaction(function () use ($tag) {
            $tag->contents()->detach();
            $tag->delete();
        });

        return success();
    }

    public function batDelete(): JsonResponse
    {
        $ids = request('ids');

        abort_if(! $ids, 406, '请求参数错误');

        DB::transaction(function () use ($ids) {
            TagPivot::query()
                ->whereIn('tag_id', $ids)
                ->delete();
            \App\Models\Content\Tag::query()
                ->whereIn('id', $ids)
                ->delete();
        });

        return success(compact('ids'));
    }
}
