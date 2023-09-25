import { createApp } from 'vue';
import { createRouter, createWebHashHistory } from 'vue-router';
import Pagination from '../components/Pagination.vue';
import ConfirmOperate from '../components/ConfirmOperate.vue';
import ElementPlus from 'element-plus';
import zhCn from 'element-plus/dist/locale/zh-cn.mjs';
import '../scss/app.scss';

/**
 *
 * @param {Object} App
 * @param {array} routes
 * @return {App<Element>}
 */
export const createAdminApp = (App, routes) => {

  const router = createRouter({
    history: createWebHashHistory(),
    routes
  });
  const app = createApp(App);
  app.use(router);

  app.use(ElementPlus, {
    locale: zhCn
  });

  // 公用组件
  app.component('Pagination', Pagination); // 分页组件
  app.component('ConfirmOperate', ConfirmOperate); // 弹出提示确认删除

  window.app = app;

  return app;
};
