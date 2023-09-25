<!-- 上传单图 -->
<style lang="scss">
.image-uploader {
    position: relative;
    border: 1px dashed var(--el-border-color-darker);
    background-color: var(--el-fill-color-lighter);

    &, .el-upload, .image-wp, .image, .image-uploader-icon {
        width: 100%;
        height: 100%;
        box-sizing: border-box;
    }

    .image-uploader-icon {
        font-size: 2rem;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        color: #aaa;
    }

    .loading {
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
    }

    .image-action {
        position: absolute;
        right: 2px;
        top: 2px;
        background: rgba(0, 0, 0, .5);
        padding: 6px 5px;
        color: #ddd;
        display: none;
        width: 1.5rem;
        height: 1.6rem;

        &:hover {
            color: #fff;
            background: rgba(0, 0, 0, .75);
        }
        &.image-preview-button {
            right: 40px;
        }
    }

    .image-wp:hover {
        .image-action {
            display: block;
        }
    }
}

.image-uploader-disabled {
    .image-uploader-icon {
        cursor: default;
    }
}
</style>

<template>
    <el-upload
        :class="`image-uploader rounded ${disabled ? ' image-uploader-disabled' : ''}`"
        accept="image/png, image/jpeg, image/gif, image/webp"
        :action="uploadImageAction(type)"
        :with-credentials="true"
        :data="appendData"
        :multiple="false"
        :show-file-list="false"
        :on-success="onUploadSuccess"
        :on-error="onUploadError"
        :before-upload="beforeUpload"
        name="img"
        :disabled="disabled"
    >
        <div class="loading" v-loading="isUploading"></div>
        <div class="image-wp" v-if="imageUrl">
            <el-image :src="imageUrl" fit="cover" class="image"/>
            <el-icon title="预览" class="image-preview-button image-action rounded" @click.prevent.stop="isShowViewer=true" v-if="enablePreview">
                <ZoomIn/>
            </el-icon>
            <el-icon title="删除" class="image-remove-button image-action rounded" @click.prevent.stop="onRemoveImage">
                <Delete/>
            </el-icon>
        </div>
        <el-icon class="image-uploader-icon" v-else>
            <Plus/>
        </el-icon>
    </el-upload>
    <el-image-viewer
        :url-list="[imageUrl]"
        v-if="enablePreview && imageUrl && isShowViewer"
        :z-index="9999"
        :hide-on-click-modal="true"
        :close-on-press-escape="true"
        :teleported="true"
        @close="hideViewer"
    ></el-image-viewer>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Delete, Plus, ZoomIn } from '@element-plus/icons-vue';
import { showLogin, xsrfToken } from '../lib';
import { checkImage, uploadErrorHandler, uploadImageAction } from '../lib/uploader';
// el-upload 组件通过 data 传递 X-XSRF-TOKEN（不通过 headers 传递，因为 headers 初始化后再修改不会同步到组件）
const appendData = {
  _xsrf_token: xsrfToken()
};
const props = defineProps({
  modelValue: {
    type: String
  },
  type: {
    type: String,
    default: 'shared'
  },
  // 文件大小限制（M)
  sizeLimit: {
    type: Number,
    default: 5
  },
  disabled: {
    type: Boolean,
    default: false
  },
  enablePreview: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['update:modelValue']);
const imageUrl = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const isShowViewer = ref(false);
const isUploading = ref(false);

const onUploadSuccess = (resp, file) => {
  imageUrl.value = resp.data.url;
  isUploading.value = false;
};

const onRemoveImage = () => {
  imageUrl.value = '';
};

const beforeUpload = rawFile => {
  if (checkImage(rawFile.type, rawFile.size, props.sizeLimit)) {
    isUploading.value = true;
    return true;
  }
  return false;
};

const onUploadError = error => {
  // 未登录
  if (error.message.includes('Unauthenticated')) {
    showLogin();
    appendData._xsrf_token = xsrfToken();
    return;
  }
  uploadErrorHandler(error).then(() => {
    isUploading.value = false;
    appendData._xsrf_token = xsrfToken();
  });
};

const hideViewer = () => {
  isShowViewer.value = false;
};
</script>
