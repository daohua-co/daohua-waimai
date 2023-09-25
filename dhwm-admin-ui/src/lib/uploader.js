import { error, toast } from './message';
import { apiUrl } from './http';

/**
 * 检查图片 mime 和 大小限制
 * @param {string} mime image/xxx
 * @param {int} size 单位（bit）
 * @param {number} sizeLimit 单位（M）
 * @return {boolean}
 */
export const checkImage = (mime, size, sizeLimit) => {
  if (!['image/png', 'image/jpeg', 'image/gif', 'image/webp'].includes(mime)) {
    error('不支持该文件格式');
    return false;
  }
  if (size / 1024 / 1024 > parseFloat(sizeLimit + '')) {
    error(`图片大小不能超过 ${sizeLimit}M`);
    return false;
  }
  return true;
};

export const uploadImageAction = (type = 'shared') => {
  return apiUrl(`/admin/images?type=${type}`);
};

export async function uploadErrorHandler(error) {
  if (!error.message) {
    return toast('上传异常');
  }
  const resp = JSON.parse(error.message);
  if (resp.code === 419 && resp.token_cookie_sent) {
    toast('已为你更新已过期的安全码，请重新上传。');
    return;
  }
  toast(resp.message);
}
