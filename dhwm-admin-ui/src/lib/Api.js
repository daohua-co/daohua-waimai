import { http } from './';

export class Api {
  path = '';

  constructor(path) {
    this.path = path;
  }

  fetchItems(params = {}) {
    return http.get(this.path, params);
  }

  fetchItem(id, params = {}) {
    return http.get(`${this.path}/${id}`, params);
  }

  delete(id, data = {}) {
    return http.delete(`${this.path}/${id}`, data);
  }

  patch(id, data) {
    return http.patch(`${this.path}/${id}`, data);
  }

  post(data) {
    return http.post(this.path, data);
  }

  put(data) {
    return http.put(`${this.path}/${data.id}`, data);
  }

  // 批量操作（如批量删除、批量更新等）
  batOperate(operate, data) {
    const method = operate === 'delete' ? 'delete' : 'patch';
    return http[method](`${this.path}/bat/${operate}`, data);
  }
}
