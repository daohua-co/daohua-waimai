import { reactive, ref } from 'vue';
import { http } from '../lib';

export const isShowLogin = ref(false);

export const userInfo = reactive({
  id: 0,
  name: '',
  realname: '',
  showName: '',
  avatar: '',
  email: '',
  mobile: '',
  enabled: true,
  permissions: []
});

// 在 VueRouter 初始化之前初始化用户信息
export const initUserInfo = async profilePath => {
  return await http.get(profilePath).then(resp => {
    setUserInfo(resp.userInfo ?? {}); // 在 blade 模板中动态设置变量 userInfo
    if (!isLogined()) {
      showLogin(); // 如果未登录，在 VueRouter 初始化之前修改 hash
    }
  });
};

export const setUserInfo = info => {
  Object.assign(userInfo, info);
  userInfo.showName = (info.realname ? info.realname : info.name) || '';
};

export const useCan = name => {
  if (!userInfo.enabled) {
    return false;
  }
  const permissions = userInfo.permissions || []; // 用户权限列表
  if (permissions[0] === '*') {
    return true;
  }
  return permissions.includes(name);
};

/**
 * @param {string} prefix
 * @param {array} actions
 * @return {object}
 */
export const useCans = (prefix, actions) => {
  const cans = {};
  actions.forEach(action => {
    cans[action] = useCan(prefix + '.' + action);
  });
  return cans;
};

export const showLogin = () => {
  isShowLogin.value = true;
};

export const isLogined = () => {
  return userInfo.id > 0;
};
