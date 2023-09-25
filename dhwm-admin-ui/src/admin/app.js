import { createAdminApp } from '/src/lib/app';
import { initUserInfo } from '/src/lib/auth';
import routes from './routes';
import App from './pages/App.vue';

initUserInfo('admin/auth/profile').then(() => {
  const app = createAdminApp(App, routes);
  app.mount('#app');
});
