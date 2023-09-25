import { isReactive, isRef, reactive, watch } from 'vue';
import { deleteMessage, responseMessage } from '../message';
import { bindThisDeeper, loading } from '../util';
import { Api } from "../Api";

/**
 * 列表页通用的数据和方法。
 */
export class ListStore {
  /**
   * @type {Api}
   */
  api = null;
  listData = reactive({
    items: [],
    path: '',
    total: 0,
    per_page: 15,
    current_page: 0,
    query: {
      page: 1
    },
    batOperateIds: [],
  });

  is = reactive({
    loading: false,
    loaded: false
  });

  constructor(listApiPath) {
    /**
     * @type {Api}
     */
    this.api = new Api(listApiPath);

    // 给方法绑定 this，实例解构赋值获得函数名也能正常执行
    bindThisDeeper(this, [
      'load', 'reload', 'search', 'selectChange', 'batOperate', 'destroy'
    ]);
    // 列表数据改变时，标记数据已加载
    watch(() => this.listData.items, () => {
      this.is.loaded = true;
    });
  }

  /**
   * 加载列表数据。
   * @param { object } appendQuery 额外增加的查询参数（不会同步到 listData.query 中）
   * @param { object } dataMap 数据映射表 { 返回变量名: 变量名数据要赋值的变量} 默认已绑定返回的 resp.items 和 resp.listData，建议不用重复绑定这两个。
   * @returns {*} Promise
   */
  load(appendQuery = {}, dataMap = {}) {
    const query = Object.assign({}, this.listData.query, appendQuery);

    this.is.loading = true;
    return this.api.fetchItems(query).then((resp) => {
      // 默认返回绑定
      if (resp.listData?.items) {
        // 1、如果返回 resp.listData，则绑定到 listData
        if (resp.listData.items.length === 0 && this.listData.query?.page && parseInt(this.listData.query.page) > 1) {
          // 返回数据为空，如果不是第页则显示前一页
          --this.listData.query.page;
          return this.load(dataMap, appendQuery);
        }
        Object.assign(this.listData, resp.listData);
      } else if (resp.items) {
        // 2、如果返回 resp.items，则绑定到 listData.items
        this.listData.items = resp.items;
      }

      // 绑定返回数据，赋值给待接收的变量
      for (const key in dataMap) {
        if (typeof resp[key] === 'undefined') {
          continue;
        }
        const valObj = dataMap[key];
        if (isRef(valObj)) {
          valObj.value = resp[key];
          continue;
        }
        if (isReactive(valObj)) {
          Object.assign(valObj, resp[key]);
          continue;
        }
        dataMap[key] = resp[key];
      }

      return resp;
    }).finally(() => {
      this.is.loading = false;
      this.is.loaded = true;
    });
  }

  /**
   * 重新加载列表页数据。
   */
  reload(page = 0, appendQuery = {}) {
    page ||= this.listData.current_page; // 默认使用当前页
    if (page > 1) {
      this.listData.query.page = page; // 页码同步到请求参数
    } else {
      delete this.listData.query.page; // 默认请求第一页
    }

    return this.load(appendQuery);
  }

  checkLoaded(appendQuery = {}) {
    if (this.is.loaded) {
      return;
    }
    this.load(appendQuery);
  }

  search() {
    this.reload(1);
  }

  selectChange(items) {
    const ids = [];
    for (const item of items) {
      ids.push(item.id);
    }
    this.listData.batOperateIds = ids;
  }

  // 批量操作
  batOperate(operate = 'delete') {
    loading.show();
    return this.api.batOperate(operate, {
      ids: this.listData.batOperateIds
    }).then((resp) => {
      responseMessage(resp);
      this.listData.batOperateIds = [];
      this.reload();
    });
  }

  // 列表页删除事件回调
  destroy(id, reloadQuery = {}) {
    loading.show({title: '正在删除...'});
    return this.api.delete(id).then(resp => {
      deleteMessage(resp);

      if (!resp.success) {
        return resp;
      }

      // 列表页显示全部记录时，返回列表刷新记录
      if (resp.items) {
        this.listData.items = resp.items;
        return resp;
      }

      // 分页列表页数据刷新
      this.reload(0, reloadQuery);

      return resp;
    });
  }
}
