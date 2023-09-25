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

use App\Admin\Requests\ContentRequest;
use App\Admin\Services\ContentService;
use App\Http\Controllers\Controller;
use App\Models\Content\Category;
use App\Models\Content\CategoryPivot;
use App\Models\Content\Content;
use Illuminate\Http\JsonResponse;

class ContentController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Content::query()
            ->orderByDesc('desc_num')
            ->orderByDesc('id')
            ->with(['tags', 'categories']);

        if ($cid = request('cat_id')) {
            $query->whereIn(
                'id',
                CategoryPivot::query()->where('category_id', $cid)->get('content_id')
            );
        }

        if ($keyword = request('keyword')) {
            $query->where(function ($query) use ($keyword) {
                $query->where('title', 'like', "%{$keyword}%")
                    ->orWhere('intro', 'like', "%{$keyword}%");
            });
        }

        return success([
            'listData' => formatListData($query->paginate(20)),
            'categories' => Category::childrenTree(),
        ]);
    }

    public function show(Content $content): JsonResponse
    {
        $content->append(['cat_ids'])
            ->load(['tags', 'creator']);
        return success([
            'item' => $content->toArray(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function create(ContentRequest $request): JsonResponse
    {
        ContentService::create(
            $this->getInputs(),
            request('tags', []),
            request('cat_ids', []),
        );

        return success();
    }

    /**
     * @throws \Exception
     */
    public function update(ContentRequest $request, Content $content): JsonResponse
    {
        ContentService::update(
            $content,
            $this->getInputs(),
            request('tags', []),
            request('cat_ids', []),
        );

        return success();
    }

    public function destroy(Content $content): JsonResponse
    {
        return success('TODO');
    }

    private function getInputs(): array
    {
        return inputs([
            'title',
            'album',
            'intro',
            'asc_num',
            'is_published',
            'author',
            'source',
            'editor',
            'detail',
        ]);
    }
}
