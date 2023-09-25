<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Area;
use Illuminate\Console\Command;

class ImportAreas extends Command
{
    protected $signature = 'app:import-areas';

    protected $description = '导入地区数据';

    public function handle(): void
    {
        // 数据来源： https://apis.map.qq.com/ws/district/v1/list?key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77
        // 该网址限制访问服务器 IP，保存到本地再处理
        $data = file_get_contents(__DIR__ . '/areas.json');
        $data = json_decode($data, true);
        $result = $data['result'];
        $rows = [];
        $this->line('-----------------');
        $this->line('开始导入地区');
        foreach ($result[0] as $item) {
            $this->handleRow($rows, $item, 0, 0, $result);
        }
        $this->line('写入地区数据');
        Area::upsert($rows, 'id');
        $this->line('导入地区完成');
        $this->line('-----------------');
    }

    private function handleRow(&$rows, $item, $parentId, $level, $result): void
    {
        $rows[] = [
            'id' => $item['id'],
            'parent_id' => $parentId,
            'short_name' => $item['name'] ?? '',
            'name' => $item['fullname'],
            'lat' => $item['location']['lat'],
            'lng' => $item['location']['lng'],
        ];

        // 子地区处理
        if (empty($item['cidx'])) {
            return;
        }
        ++$level;
        $cidx = $item['cidx'];
        $parentId = $item['id'];
        $items = $result[$level];
        for ($i = $cidx[0]; $i <= $cidx[1]; ++$i) {
            $item = $items[$i];
            $this->handleRow($rows, $item, $parentId, $level, $result);
        }
    }
}
