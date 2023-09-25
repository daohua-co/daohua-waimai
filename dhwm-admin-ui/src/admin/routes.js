import Forbidden from '/src/components/Forbidden.vue';
import NotFound from '/src/components/NotFound.vue';
import Dashboard from './pages/Dashboard.vue';
import Login from './pages/account/Login.vue';

// 非懒加载 vue 文件组件需先 import 出组件变量，再设置到路由表，否则 build 后出现不显示的问题。（vue-router bug？）
// auth: authorization，授权 id
export default [
  {
    path: '/',
    component: Dashboard,
    meta: {
      title: '概况'
    }
  },
  {
    path: '/notfound',
    component: NotFound,
    meta: {
      title: '404 | 页面未找到'
    }
  },
  {
    path: '/forbidden',
    component: Forbidden,
    meta: {
      title: '403 | 无权限'
    }
  },
  {
    path: '/login',
    component: Login,
    meta: {
      title: '登录'
    }
  },

  // content
  {
    path: '/contents',
    component: () => import('./pages/content/Index.vue'),
    meta: {
      parent: '',
      title: '内容管理',
      auth: 'content.viewAll'
    }
  },
  {
    path: '/contents/create',
    component: () => import('./pages/content/Form.vue'),
    meta: {
      parent: '/contents',
      title: '新建内容',
      auth: 'content.create'
    }
  },
  {
    path: '/contents/:id/edit',
    props: true,
    component: () => import('./pages/content/Form.vue'),
    meta: {
      parent: '/contents',
      title: '编辑内容',
      auth: 'content.category.update'
    }
  },
  {
    path: '/contents/categories',
    component: () => import('./pages/content/category/Index.vue'),
    meta: {
      parent: '/contents',
      title: '分类管理',
      auth: 'content.category.viewAll'
    }
  },
  {
    path: '/contents/tags',
    component: () => import('./pages/content/tag/Index.vue'),
    meta: {
      parent: '/contents',
      title: '标签管理',
      auth: 'content.tag.viewAll'
    }
  },

  // goods
  {
    path: '/goods',
    component: () => import('./pages/goods/Index.vue'),
    meta: {
      title: '商品管理',
      auth: 'goods.viewAll'
    }
  },
  {
    path: '/goods/categories',
    component: () => import('./pages/goods/category/Index.vue'),
    meta: {
      parent: '/goods',
      title: '分类管理',
      auth: 'goods.category.viewAll'
    }
  },
  {
    path: '/goods/tags',
    component: () => import('./pages/goods/tag/Index.vue'),
    meta: {
      parent: '/goods',
      title: '标签管理',
      auth: 'goods.tag.viewAll'
    }
  },
  {
    path: '/goods/create',
    component: () => import('./pages/goods/Form.vue'),
    meta: {
      parent: '/goods',
      title: '新建商品',
      auth: 'goods.category.create'
    }
  },
  {
    path: '/goods/:id/edit',
    props: true,
    component: () => import('./pages/goods/Form.vue'),
    meta: {
      parent: '/goods',
      title: '编辑商品',
      auth: 'goods.category.update'
    }
  },

  // shop
  {
    path: '/shops',
    component: () => import('./pages/shop/Index.vue'),
    meta: {
      title: '分店管理',
      auth: 'shop.viewAll'
    }
  },
  {
    path: '/shops/applies',
    component: () => import('./pages/shop/apply/Index.vue'),
    meta: {
      title: '加盟申请管理',
      auth: 'shop.apply.viewAll'
    }
  },
  {
    path: '/shops/warehouses/:shopId',
    props: true,
    component: () => import('./pages/shop/warehouse/Index.vue'),
    meta: {
      parent: '/shops',
      title: '仓库管理',
      auth: 'shop.warehouses.ViewAll'
    }
  },
  {
    path: '/shops/sellers/:shopId',
    props: true,
    component: () => import('./pages/shop/seller/Index.vue'),
    meta: {
      parent: '/shops',
      title: '门店店员管理',
      auth: 'shop.seller.ViewAll'
    }
  },

  // admin user
  {
    path: '/administrators',
    component: () => import('./pages/administrator/Index.vue'),
    meta: {
      title: '人员管理',
      auth: 'administrator.viewAll'
    }
  },
  {
    path: '/administrators/departments',
    component: () => import('./pages/administrator/department/Index.vue'),
    meta: {
      parent: '/administrators',
      title: '部门管理',
      auth: 'administrator.department.viewAll'
    }
  },
  {
    path: '/administrators/roles',
    component: () => import('./pages/administrator/role/Index.vue'),
    meta: {
      parent: '/administrators',
      title: '角色管理',
      auth: 'administrator.role.viewAll'
    }
  },
  {
    path: '/administrators/recycles',
    component: () => import('./pages/administrator/Recycle.vue'),
    meta: {
      parent: '/administrators',
      title: '回收站',
      auth: 'administrator.recycle.viewAll'
    }
  },
  {
    path: '/areas',
    component: () => import('./pages/area/Index.vue'),
    meta: {
      title: '行政区管理',
      auth: 'area.viewAll'
    }
  },
  {
    path: '/settings/:group',
    props: true,
    component: () => import('./pages/setting/IndexSetting.vue'),
    meta: {
      title: '设置',
      auth: 'system.setting.update'
    }
  }
];
