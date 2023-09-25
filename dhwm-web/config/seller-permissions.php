<?php
/**
 * 稻花外卖，一个支持连锁加盟、同城配送的外卖商城系统。
 *
 * @author  Yiba <yibafun@gmail.com>
 * @link    https://github.com/daohua-co/daohua-waimai
 * @license MIT (http://opensource.org/licenses/MIT)
 */
declare(strict_types=1);

$crud = [
    'viewAll' => '查看列表',
    'view' => '查看详情',
    'create' => '新建',
    'update' => '编辑',
    'delete' => '删除',
];
$crudAndBatDelete = [
    ...$crud,
    'batDelete' => '批量删除',
];

return [
    'order' => [
        'title' => '订单管理',
        'items' => [
            ...$crud,
            'append' => '追加商品',
        ],
    ],
    'goods' => [
        'title' => '商品管理',
        'items' => $crud,
    ],
    'goods.stock' => [
        'title' => '库存管理',
        'items' => [
            'update' => '修改库存',
            'taking' => '盘点',
        ],
    ],
    'seller。role' => [
        'title' => '角色管理',
        'items' => $crud,
    ],
    'seller' => [
        'title' => '店员管理',
        'items' => $crud,
    ],
];
