/**
 * 申请状态 tag 类型
 * @param {string} status
 * @return {string}
 */
export function statusType(status) {
  return {
    locked: 'danger',
    reject: 'warning',
    accept: 'success',
    submit: 'info',
  }[status];
}
