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
    'goods.category' => [
        'title' => '商品分类管理',
        'items' => $crud,
    ],
    'goods.tag' => [
        'title' => '商品标签管理',
        'items' => $crudAndBatDelete,
    ],
    'content' => [
        'title' => '内容管理',
        'items' => $crudAndBatDelete,
    ],
    'content.tag' => [
        'title' => '标签管理',
        'items' => $crud,
    ],
    'content.category' => [
        'title' => '分类管理',
        'items' => $crud,
    ],
    'member' => [
        'title' => '会员管理',
        'items' => $crud,
    ],
    'administrator' => [
        'title' => '员工管理',
        'items' => $crudAndBatDelete,
    ],
    'administrator.recycle' => [
        'title' => '回收站',
        'items' => [
            'viewAll' => '查看列表',
            'restore' => '恢复人员账号',
        ],
    ],
    'administrator.role' => [
        'title' => '角色管理',
        'items' => $crud,
    ],
    'administrator.department' => [
        'title' => '部门管理',
        'items' => $crud,
    ],
    'setting' => [
        'title' => '系统设置',
        'items' => [
            'basic' => '基本设置',
            'trade' => '营业设置',
        ],
    ],
];
