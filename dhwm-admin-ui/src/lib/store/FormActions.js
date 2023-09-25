import { onActivated, onDeactivated, reactive, watch } from 'vue';
import { confirmSaveSuccess } from '../message';
import { bindThisDeeper, refKit, routeTo } from '../util';

/**
 * 新建/编辑表单页面通用的数据和方法。
 */
export class FormActions {
  /**
   * ```
   * - 新建/编辑数据不能相互污染
   * - 新建时
   *   - 如果没有上次操作，使用默认值
   *   - 如果上次操作为编辑，则使用暂存数据（把#stashForCreate 复制给 item）
   *   - 如果上次操作为新建，则使用item数据（不需要处理）
   *   - 新建提交成功后，恢复为默认数据
   *   - 重置时，使用默认值
   * - 编辑时
   *   - 如果上次为新建操作，并且未提交，则先暂存 item 到 #stashForCreate
   *   - 编辑时使用远程数据，并记录远程数据到 #stashForEdit
   *   - 编辑保存后，把数据复制到 #stashForEdit
   *   - 重置时，使用 #stashForEdit 数据
   * ```
   */
  item = reactive({}); // 不能在继承中重写 item、is 属性，否则丢失响应性

  is = reactive({
    edit: false, // 是否是编辑，否则是新建
    show: false, // 是否显示表单/对话框
    submitting: false, // 是否正在提交数据
    loading: false, // 是否正在加载数据
    showValidateError: true // 是否显示表单输入框错误提示
  });

  api = null;
  listStore = null;
  formRef = null;

  // item 默认值
  #defaultValue;
  // 上次操作是否是编辑
  #lastIsEdit;
  // 新建暂存数据，从新建切换到编辑时更新
  #stashForCreate;
  // 用于编辑时重置
  #stashForEdit;

  /**
   *
   * @param {ListStore} listStore
   * @param {ref} formRef
   * @param {object} attributes 新建的 item 的默认数据
   */
  constructor(listStore, formRef, attributes = {}) {
    bindThisDeeper(this, [
      'create', 'edit', 'onHide', 'preventValidateCall', 'save', 'reset', 'initFormPage'
    ]);

    this.api = listStore.api;
    this.listStore = listStore;
    this.formRef = formRef;
    this.#defaultValue = attributes;
    // 构造函数中初始化默认数据，避免新建时视图访问默认数据出现未定义的问题
    Object.assign(this.item, attributes);
  }

  // 打开新建表单
  create() {
    if (this.#lastIsEdit) {
      // 上次操作为编辑，使用暂存数据
      refKit.cloneDeep(this.item, this.#stashForCreate || this.#defaultValue);
    }
    this.#show();
  }

  /**
   * 打开编辑表单
   * @return {Promise}
   */
  edit(idOrItem, query = {}) {
    if (!this.#lastIsEdit) {
      // 上次操作为新建，暂存 item
      this.#stashForCreate = refKit.cloneDeep({}, this.item);
    }

    if (typeof idOrItem === 'object') {
      // 传入对象，克隆对象数据到 item
      refKit.cloneDeep(this.item, idOrItem);
      this.#show(true);
      return Promise.resolve({
        item: idOrItem,
      });
    }

    // 传入 id，加载远程数据
    this.#show(true, true);
    return this.api.fetchItem(idOrItem, query).then(resp => {
      refKit.cloneDeep(this.item, resp.item);
      this.#stashForEdit = resp.item;
      return resp;
    }).finally(() => {
      this.is.loading = false;
    });
  }

  // 页面退出时触发该方法
  onHide() {
    this.is.show = false;
  }

  // 表单数据变化触发表单验证，触发显示错误提示信息，
  // 解决：先关闭错误显示开关，数据处理完成后，清空错误提示信息后，再打开错误显示开关
  preventValidateCall(callback) {
    // 停止显示验证错误信息
    this.is.showValidateError = false;
    // 执行回调
    callback.call();
    setTimeout(() => {
      // 等待回调完成后清除错误信息
      this.formRef?.value?.clearValidate();
      this.is.showValidateError = true;
    }, 200);
  }

  /**
   * 表单验证通过后提交保存
   * @return {Promise}
   */
  save(reloadQuery = {}) {
    const savePromise = () => {
      this.is.submitting = true;
      const method = this.is.edit ? 'put' : 'post';
      return this.api[method](this.item).then(resp => {
        // 显示操作成功提示信息
        const message = `${this.is.edit ? '编辑' : '新建'}成功`;
        const cancelBtnText = this.is.edit ? '继续编辑' : '继续新建';
        confirmSaveSuccess(message, cancelBtnText).then(() => {
          // 返回列表页（隐藏对话框）
          this.is.show = false;
        }).catch(() => {
          // console.log('catch...');
        });

        // 处理 item 数据
        if (this.is.edit) {
          // 编辑成功，暂存 item
          this.#stashForEdit = refKit.cloneDeep({}, this.item);
        } else {
          // 创建成功，重置新建数据
          this.reset();
        }

        if (!this.listStore) {
          return resp;
        }

        if (resp.items) {
          this.listStore.listData.items = resp.items;
          return resp;
        }

        this.listStore.reload(0, reloadQuery);

        return resp;
      }).finally(() => {
        this.is.submitting = false;
      });
    };

    if (this.formRef) {
      // 设置了表单引用实例，则验证表单后再执行逻辑
      return this.formRef.value.validate().then(async() => {
        return await savePromise();
      });
    }
    return savePromise();
  }

  // 重置表单
  reset() {
    this.preventValidateCall(() => {
      if (this.is.edit) {
        // 编辑时，重置为服务器端返回的数据
        refKit.cloneDeep(this.item, this.#stashForEdit);
      } else {
        // 创建时，恢复为 item 默认值
        this.#stashForCreate = null;
        refKit.cloneDeep(this.item, this.#defaultValue);
      }
    });
  }

  // 非弹窗表单页（默认是模态窗）
  // 进入表单页面时，判断是新建还是编辑，并且同步弹窗表单的返回列表事件
  initFormPage(props) {
    // 从新建或编辑页提取列表页路由
    const listPath = window.location.hash.replace(/#(\/[\w-/]+?)\/(create|(\d+\/edit)).*/, '$1');

    // 同步 FormDialog 的返回列表事件，监听 is.show，
    // 点击提交成功提示框的"返回列表"按钮时，跳转到列表页
    watch(() => this.is.show, isShow => {
      isShow || routeTo(listPath);
    });

    // 显示页面时，选择编辑或新建
    onActivated(() => {
      props.id ? this.edit(props.id) : this.create();
    });

    // 为同步 FormDialog，退出页面时，执行 onHide()
    onDeactivated(() => this.onHide());
  }

  #show(isEdit = false, loading = false) {
    this.#lastIsEdit = isEdit;
    this.is.edit = isEdit;
    this.is.loading = loading;
    this.is.show = true;

    if (isEdit && loading) {
      refKit.empty(this.item);
    }
  }
}
