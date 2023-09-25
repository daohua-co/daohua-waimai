export default [
  // overview
  {
    title: '首页',
    path: '/',
    icon: 'dashboard',
    desc: '后台首页',
    children: [
      { title: '控制台', path: '/' }
    ]
  },

  // goods
  {
    title: '商品',
    path: '/goods',
    icon: 'goods',
    desc: '商品管理',
    module: 'goods',
    children: [
      { title: '商品', path: '/goods' },
      { title: '分类', path: '/goods/categories' },
      { title: '标签', path: '/goods/tags' }
    ]
  },
  // 加盟商
  {
    title: '门店',
    path: '/shops',
    icon: 'shop',
    desc: '门店管理',
    module: 'shops',
    children: [
      { title: '门店管理', path: '/shops' },
      { title: '加盟申请管理', path: '/shops/applies' },
      { title: 'Todo2', path: '/todo' }
    ]
  },

  // contents
  {
    title: '内容',
    path: '/contents',
    icon: 'news',
    desc: '内容管理',
    module: 'contents',
    children: [
      { title: '文章', path: '/contents' },
      { title: '分类', path: '/contents/categories' },
      { title: '标签', path: '/contents/tags' }
    ]
  },

  // todo
  {
    title: 'TODO',
    path: '/todo',
    icon: 'stats',
    desc: 'TODO',
    module: 'todo',
    children: [
      { title: 'Todo1', path: '/todo' },
      { title: 'Todo2', path: '/todo' }
    ]
  },

  // administrators
  {
    title: '人员',
    path: '/administrators',
    icon: 'user',
    desc: '人员管理',
    module: 'administrators',
    children: [
      {
        title: '人员管理',
        path: '/administrators'
      },
      {
        parent: '/administrators',
        title: '角色管理',
        path: '/administrators/roles'
      },
      {
        parent: '/administrators',
        title: '部门管理',
        path: '/administrators/departments'
      },
      {
        parent: '/administrators',
        title: '回收站',
        path: '/administrators/recycles'
      }
      // { title: '授权条目', path: '/administrators/permissions' }
    ]
  },

  // setting
  {
    title: '设置',
    path: '/settings/system',
    icon: 'setting',
    desc: '系统设置',
    module: 'settings',
    children: [
      {
        title: '系统设置',
        path: '/settings/system'
      },
      {
        title: '联系信息',
        path: '/settings/contact'
      },
      {
        title: '短信设置',
        path: '/settings/sms'
      },
      {
        title: '行政区管理',
        path: '/areas'
      }
    ]
  }
];
