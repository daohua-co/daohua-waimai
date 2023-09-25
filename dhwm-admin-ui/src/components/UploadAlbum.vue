<template>
  <div class="album">
    <ul class="el-upload-list el-upload-list--picture-card flex">
      <Draggable
        v-model="album"
        group="people"
        @start="drag=true"
        @end="drag=false"
        item-key="id"
      >
        <template #item="{ element, index}">
          <li class="el-upload-list__item" tabindex="0" style="">
            <el-progress type="circle" :percentage="element.percentage" :width="90" v-show="element.status !== 'success'"/>
            <el-image
              fit="cover"
              :src="element.url"
              :preview-src-list="album.map(img => img.url)"
              :initial-index="index"
              :hide-on-click-modal="true"
            />
            <div class="el-upload-list__item-actions">
              <el-popconfirm title="您确定要移除该图片吗？" @confirm="onAlbumImageRemove(element, index)">
                <template #reference>
                  <span class="el-upload-list__item-delete">
                    <el-icon><Delete/></el-icon>
                  </span>
                </template>
              </el-popconfirm>
            </div>
          </li>
        </template>
      </Draggable>

      <el-upload
        v-model:file-list="album"
        :headers="{Accept: 'application/json'}"
        :action="uploadImageAction('album')"
        list-type="picture-card"
        :on-success="onUploadSuccess"
        :on-error="onUploadError"
        :on-exceed="onExceed"
        :with-credentials="true"
        :data="appendData"
        :multiple="true"
        :show-file-list="false"
        :before-upload="beforeUpload"
        :limit="numLimit"
        accept="image/png, image/jpeg, image/gif, image/webp"
        name="img"
        class="mr-3"
      >
        <el-icon>
          <Plus/>
        </el-icon>
      </el-upload>
    </ul>
    <div class="tips mt-1">可上传{{ numLimit }}张以内，格式为 png、jpg、jpeg、gif、webp 的图片。可拖动图片调整顺序。</div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import Draggable from 'vuedraggable';
import { Delete, Plus } from '@element-plus/icons-vue';
import { toast, xsrfToken, showLogin } from '../lib';
import { checkImage, uploadErrorHandler, uploadImageAction } from '../lib/uploader';
// el-upload 组件通过 data 传递 X-XSRF-TOKEN（不通过 headers 传递，因为 headers 初始化后再修改不会同步到组件）
const appendData = {
  _xsrf_token: xsrfToken()
};
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  sizeLimit: {
    type: Number,
    default: 2
  },
  numLimit: {
    type: Number,
    default: 10
  }
});
const emit = defineEmits(['update:modelValue']);
let isInnerUpdate = false;
// 相册图片列表初始化，album 与 props.modelValue 数据格式不同，不能用 computed
const album = ref([]);

// 相册更新后，同步到 modelValue
// album => props.modelValue （[{url}, ...] => [url, ...]）
watch(album, () => {
  const urls = [];
  album.value.forEach(img => {
    // 上传成功才同步
    if (img.status !== 'success') {
      return;
    }
    urls.push(img.org ?? img.url);
  });
  isInnerUpdate = true;
  emit('update:modelValue', urls);
}, {deep: true});

// 外部改变 modelValue 时，同步到 album
// props.modelValue => album （[url, ...] => [{url}, ...]）
watch(() => props.modelValue, () => {
  if (isInnerUpdate) {
    isInnerUpdate = false;
    return;
  }
  album.value = props.modelValue.map(url => {
    return {url};
  });
}, {deep: true, immediate: true});

const drag = ref(false);

const onAlbumImageRemove = (file, index) => {
  album.value.splice(index, 1);
};

const onUploadSuccess = (resp, file, files) => {
  file.org = resp.data.org;
};

const beforeUpload = file => {
  return checkImage(file.type, file.size, props.sizeLimit);
};

const onUploadError = error => {
  // 未登录
  if (error.message.includes('Unauthenticated')) {
    showLogin();
    appendData._xsrf_token = xsrfToken();
    return;
  }
  uploadErrorHandler(error).then(() => {
    appendData._xsrf_token = xsrfToken();
  });
};

const onExceed = () => {
  toast(`图片数量不能超过 ${props.numLimit} 个`);
};
</script>

<style lang="scss">
.album .el-progress {
  z-index: 9;
  .el-progress-circle {
    margin: 4px auto 0;
  }
}
</style>
