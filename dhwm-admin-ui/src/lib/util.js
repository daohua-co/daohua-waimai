/**
 * 工具包。
 */
import { isProxy, isRef } from 'vue';
import { ElLoading } from 'element-plus';

export const isMobile = value => {
  return /^(?:(?:\+|00)86)?1[3-9]\d{9}$/.test(value);
};

export const checkMobile = (rule, value, callback) => {
  if (value === '' || isMobile(value)) {
    return callback();
  }

  return callback(new Error());
};

export const guid = () => {
  return 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'.replace(/[x]/g, function(c) {
    return (Math.random() * 16 | 0).toString(16);
  });
};

// 响应式对象工具
export const refKit = {
  // 清空响应式对象元素
  empty(target) {
    if (isProxy(target)) {
      Object.keys(target).forEach((key) => {
        delete target[key];
      });
      return;
    }
    if (isRef(target)) {
      target.value = null;
      return;
    }
    target = {};
  },
  /**
     * 深拷贝响应式对象数据。
     * - 目标对象类型可以是 ref/reactive，也可以是非响应式对象
     * - 目标对象是 reactive 对象，先清空后再深拷贝数据，
     * - 只复制数据，不复制函数。
     * - 浅拷贝可用展开运算符，如对象浅拷贝：{ ...obj }，数组浅拷贝：[...arr]。
     * @param {object} target
     * @param {object} source
     * @return {*}
     */
  cloneDeep(target, source) {
    if (target === null || target === undefined) {
      throw new Error('target 不能为 null 或 undefined');
    }

    if (isRef(target)) {
      target.value = JSON.parse(JSON.stringify(source));
      return target;
    }

    // 响应式对象先清空后再复制
    if (isProxy(target)) {
      this.empty(target);
      Object.assign(target, JSON.parse(JSON.stringify(source ?? {})));
      return target;
    }

    // 原生对象直接深复制
    target = JSON.parse(JSON.stringify(source));
    return target;
  }
};

/**
 * 在构造函数中用 this.xx = this.xx.bind(this) 把 this 绑定到 xx 函数，以支持对象实例解构赋值获得函数名后，可以用函数名执行方法。
 * ```
 * 例如：
 * class Demo {
 *     constructor() {
 *         bindThisDeeper(this, ['fn1']); // 即 this.fn1 = this.fn1.bind(this)
 *     }
 *     fn1() {
 *         this.fn2();
 *     }
 *     fn2() {
 *     }
 * }
 * const demo = new Demo();
 * const { fn1 } = demo;
 * fn1();  // 如果 fn1 方法没有绑定 this，这里将抛出 fn2 函数不存在的错误
 * ```
 * @param {object} that
 * @param {array} methods
 */
export const bindThisDeeper = (that, methods) => {
  methods.forEach(method => {
    that[method] = that[method].bind(that);
  });
};

/**
 * @param {string} path
 */
export const routeTo = path => {
  window.location.hash = '#' + path;
};

/**
 * UTC 时间转换成本地时间
 * @param {string} utcDateTime
 * @return {string}
 */
export const utcDateTimeToLocal = (utcDateTime) => {
  return new Date(utcDateTime).toLocaleString();
};

export const loading = {
  _isLoading: null,
  show: function(options = {}) {
    this._isLoading = ElLoading.service(options);
  },
  hide: function() {
    if (this._isLoading) {
      this._isLoading.close();
    }
  }
};

export const xsrfToken = () => {
  return decodeURIComponent(window.document.cookie.replace(/.*?XSRF-TOKEN=(.*?)(;|$).*/, '$1'));
};
