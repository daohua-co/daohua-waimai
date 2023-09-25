import axios from 'axios';
import { isProxy, toRaw } from 'vue';
import { error as showError, responseMessage } from './message';
import { loading } from './util';
import { showLogin } from './auth';

export const http = {
  request(options) {
    if (typeof options.data === 'undefined') {
      options.data = {};
    }

    // 数据为代理对象的处理
    if (isProxy(options.data)) {
      options.data = toRaw(options.data);
    }

    options.url = apiUrl(options.url);
    options.withCredentials = true;

    return ajax(options);
  },
  get(url, params = {}) {
    return this.request({
      method: 'GET',
      url,
      params
    });
  },
  post(url, data) {
    return this.request({
      method: 'POST',
      url,
      data
    });
  },
  option(url, params = {}) {
    return this.request({
      method: 'OPTION',
      url,
      params
    });
  },
  put(url, data) {
    return this.request({
      method: 'PUT',
      url,
      data
    });
  },
  patch(url, data) {
    return this.request({
      method: 'PATCH',
      url,
      data
    });
  },
  delete(url, data = {}) {
    return this.request({
      method: 'DELETE',
      url,
      data
    });
  }
};

/**
 * @param {string} path
 */
export const apiUrl = path => {
  const baseUrl = import.meta.env.VITE_API_PATH || '/';
  if (path.startsWith('/')) {
    return baseUrl + path.substring(1);
  }
  return baseUrl + path;
};

/**
 * @param {object} options
 * @return {Promise}
 */
const ajax = (options = {}) => {
  // console.log(options);
  return axios(options)
    .then((response) => {
      return response.data;
    })
    // 返回 http 状态码为 40x、50x 时，axios 将触发错误
    // 发生任何异常时（包括且不限于参数验证、表单验证等），
    // 代码中手动设置的返回 http 状态码都是 40x
    .catch(error => errorHandler(error, options))
    .finally(() => {
      loading.hide();
    });
};

// 异常处理应该以 「抛出错误」 来退出函数，而不是用用 return，否则 `catch()` 之后的 `then()` 链还会执行。
const errorHandler = (error, options) => {
  const respJar = error.response || {};
  const status = respJar.status;
  const resp = respJar.data || {};

  if (error.code === "ERR_NETWORK") {
    showError('API 请求失败');
    throw error;
  }

  if (status === 401) {
    showLogin();
    throw error;
  }

  if (status === 419) {
    // CSRF token 过期，已随请求返回新 token，重新提交即可
    if (resp.token_cookie_sent) {
      return ajax(options);
    }
    showError('状态已过期，请刷新页面后再执行该操作');
    throw error;
  }

  if (!resp.message) {
    const messages = {
      401: '请您先要登录',
      403: '无权限访问该资源',
      404: '请求资源未找到',
      408: '请求超时',
      413: '请求实体超过大小限制',
      429: '你请求太频繁了，情稍等一会再来', // 访问限流
      500: '服务器打了个盹',
    };
    resp.message = messages[status] || '未知错误';
  }

  resp.success = false;
  responseMessage(resp);

  throw error;
};
