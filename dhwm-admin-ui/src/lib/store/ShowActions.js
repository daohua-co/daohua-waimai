import { reactive } from 'vue';
import { refKit, bindThisDeeper } from "../util";

/**
 * 预览页通用的数据和方法。
 */
export class ShowActions {
  item = reactive({
    id: 0
  });

  is = reactive({
    show: false,
    loading: true
  });

  /**
   * @param {Api} api
   */
  constructor(api) {
    this.api = api;
    // this.show = this.show.bind(this);
    bindThisDeeper(this, [
      'show', 'toEdit'
    ]);
  }

  /**
   * @param {int|object} idOrItem 为 id 则动态加载数据，为 item（object）则作为显示数据
   * @param {object} params
   * @param {boolean} clearItem
   * @return {Promise<unknown>}
   */
  show(idOrItem, params = {}, clearItem = true) {
    if (clearItem) {
      refKit.empty(this.item);
    }
    this.is.show = true;
    if (typeof idOrItem === 'object') {
      // 传入对象，克隆到 item
      this.is.loading = false;
      refKit.cloneDeep(this.item, idOrItem);
      return Promise.resolve({item: idOrItem});
    }
    // 传入 id，加载远程数据
    this.is.loading = true;
    return this.api.fetchItem(idOrItem, params).then(resp => {
      Object.assign(this.item, resp.item);
      return resp;
    }).finally(() => {
      this.is.loading = false;
    });
  }

  toEdit(formRef, idOrItem = 0) {
    this.is.show = false; // 隐藏详情信息（如果不隐藏则需要编辑保存后，详情页能获取到最新数据）
    formRef.edit(idOrItem || this.item.id);
  }
}
