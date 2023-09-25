<template>
    <Editor v-model="content" :init="init" :disabled="disabled" :placeholder="placeholder" @onClick="onClick"/>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import tinymce from 'tinymce/tinymce';
import Editor from '@tinymce/tinymce-vue';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/models/dom';
import 'tinymce/plugins/code';
import 'tinymce/plugins/image';
import 'tinymce/plugins/media';
import 'tinymce/plugins/link';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/template';
import 'tinymce/plugins/table';
import 'tinymce/plugins/pagebreak';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/quickbars';
import { http } from '../lib';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  height: {
    type: Number,
    default: 500
  },
  disabled: {
    type: Boolean,
    default: false
  },
  plugins: {
    type: [String, Array],
    default: 'code image media link preview table quickbars template pagebreak lists advlist'
  },
  toolbar: {
    type: [String, Array],
    default: [
      'blocks fontfamily fontsize forecolor backcolor bold italic underline strikethrough link',
      'alignleft aligncenter alignright alignjustify outdent indent numlist bullist',
      'table image media preview code selectall'
    ].join(' ')
  },
  templates: {
    type: Array,
    default: () => []
  },
  options: {
    type: Object,
    default: () => {
    }
  },
  fonts: {
    type: String,
    default: [
      '微软雅黑=Microsoft YaHei,Helvetica Neue,PingFang SC',
      '苹方=PingFang SC,Microsoft YaHei',
      '黑体=SimHei;宋体=simsun,serif;仿宋=FangSong;楷体=KaiTi,楷体;隶书=LiSu,隶书;幼圆=YouYuan,幼圆',
      'Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde',
      'Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier',
      'Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol',
      'Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times',
      'Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings'
    ].join(';')
  }
});
const emit = defineEmits(['update:modelValue']);
const content = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});
const baseUrl = import.meta.env.BASE_URL || '/'; // vite.config: base
// eslint-disable-next-line vue/no-setup-props-destructure
const init = {
  language_url: baseUrl + 'vendor/tinymce/langs/zh-Hans.js',
  language: 'zh-Hans',
  skin_url: baseUrl + 'vendor/tinymce/skins/ui/oxide',
  content_css: baseUrl + 'vendor/tinymce/skins/content/default/content.css',
  menubar: false,
  statusbar: true,
  plugins: props.plugins,
  toolbar: props.toolbar,
  toolbar_mode: 'sliding',
  font_family_formats: props.fonts,
  font_size_formats: '12px 14px 16px 18px 22px 24px 36px 72px',
  height: props.height,
  placeholder: props.placeholder,
  branding: false,
  resize: true,
  elementpath: true,
  content_style: '',
  templates: props.templates,
  quickbars_selection_toolbar: 'forecolor backcolor bold italic underline strikethrough link',
  quickbars_image_toolbar: 'alignleft aligncenter alignright',
  quickbars_insert_toolbar: false,
  image_caption: true,
  image_advtab: true,
  convert_urls: false,
  images_upload_handler: blobInfo => {
    const data = new FormData();
    data.append('img', blobInfo.blob(), blobInfo.filename());
    return http.post('admin/images', data).then(resp => {
      return resp.data.url;
    });
  },
  setup: editor => {
    editor.on('init', function() {
      // this.getBody().style.fontSize = '14px';
    });
  },
  ...props.options
};

onMounted(() => {
  tinymce.init({});
});

const onClick = e => {
  emit('onClick', e, tinymce);
};

</script>
