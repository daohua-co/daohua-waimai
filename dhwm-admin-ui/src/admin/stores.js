/**
 * Store 是一个保存状态和业务逻辑的实体，它承载着全局状态，即包含可以在整个应用中访问的数据。
 */
import { ListStore } from '/src/lib';

export const adminDepartmentStore = new ListStore('admin/administrators/departments');
export const adminRoleStore = new ListStore('admin/administrators/roles');
export const adminUserStore = new ListStore('admin/administrators');
export const adminUserRecycleStore = new ListStore('admin/administrators/recycles');
export const adminPermissionStore = new ListStore('admin/administrators/permissions');
export const contentStore = new ListStore('admin/contents');
export const contentCategoryStore = new ListStore('admin/contents/categories');
export const contentTagStore = new ListStore('admin/contents/tags');
export const goodsStore = new ListStore('admin/goods');
export const goodsCategoryStore = new ListStore('admin/goods/categories');
export const goodsTagStore = new ListStore('admin/goods/tags');
export const goodsSkuNameStore = new ListStore('admin/goods/sku-names');
export const goodsSkuValueStore = new ListStore('admin/goods/sku-values');
export const areaStore = new ListStore('admin/areas');
export const shopStore = new ListStore('admin/shops');
export const shopApplyStore = new ListStore('admin/shops/applies');
export const shopRoleStore = new ListStore('admin/shops/roles');
export const shopSellerStore = new ListStore('admin/shops/sellers');
export const shopPermissionStore = new ListStore('admin/shops/permissions');
