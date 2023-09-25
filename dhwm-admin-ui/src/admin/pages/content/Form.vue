<template>
  <div class="box p-6 mb-14">
    <el-skeleton :rows="20" :loading="is.loading" animated/>
    <el-form
      v-if="!is.loading"
      ref="formRef"
      :model="item"
      :rules="formRules"
      label-width="100px"
      @submit.prevent="save"
      :class="is.showValidateError ? '' : 'hide-validate-error'"
      :scroll-to-error="true"
    >
      <el-row>

        <el-col class="mb-4" :span="12">
          <el-form-item label="作者" prop="author">
            <el-input v-model="item.author" placeholder="" class="w-96" clearable/>
          </el-form-item>
        </el-col>

        <el-col class="mb-4" :span="12">
          <el-form-item label="发布状态" prop="is_published">
            <el-switch
              width="48"
              v-model="item.is_published"
              :active-value="1"
              active-text="发布"
              :inactive-value="0"
              inactive-text="草稿"
              inline-prompt
            />
          </el-form-item>
        </el-col>

        <el-col class="mb-4" :span="12">
          <el-form-item label="编辑" prop="editor">
            <el-input v-model="item.editor" placeholder="" class="w-96" clearable/>
          </el-form-item>
        </el-col>

        <el-col class="mb-4" :span="12">
          <el-form-item label="显示排序" prop="asc_num">
            <el-input-number v-model.number="item.asc_num" :min="0" :max="9999" controls-position="right" class=""/>
            <span class="tips">从小到大显示</span>
          </el-form-item>
        </el-col>

        <el-col class="mb-4" :span="12">
          <el-form-item label="来源" prop="source">
            <el-input v-model="item.source" placeholder="" class="w-96" clearable/>
          </el-form-item>
        </el-col>

        <el-col class="mb-4" :span="12">
          <el-form-item label="分类" prop="cat_ids">
            <el-tree-select
              class="w-96"
              placeholder="请选择"
              v-model="item.cat_ids"
              :data="contentCategoryStore.listData.items"
              :props="{label: 'title', value: 'id'}"
              :render-after-expand="true"
              default-expand-all
              multiple
              clearable
            />
          </el-form-item>
        </el-col>

        <el-col class="mb-4" :span="12">
          <el-form-item label="相册" prop="asc_num">
            <Album v-model="item.album" :size-limit="5" :num-limit="10"/>
          </el-form-item>
        </el-col>

        <el-col class="mb-4" :span="12">
          <el-form-item label="标签" prop="tag_ids">
            <Tags v-model="item.tags" :hot-tags="hotTags"/>
          </el-form-item>
        </el-col>

      </el-row>

      <el-form-item label="标题" prop="title">
        <el-input v-model="item.title" placeholder="" class="w-full" clearable/>
      </el-form-item>


      <el-form-item label="简介" prop="intro">
        <el-input
          type="textarea"
          placeholder=""
          class="w-full"
          v-model="item.intro"
          :autosize="{ minRows: 2 }"
          clearable
        />
      </el-form-item>

      <el-form-item label="详细介绍" prop="detail" class="editor-wp mb-2">
        <Editor v-model="item.detail"/>
      </el-form-item>

      <div class="form-footer-fixed">
        <el-button type="primary" native-type="submit" v-loading="is.submitting">确定</el-button>
        <el-button @click="reset()">重置</el-button>
      </div>
    </el-form>
  </div>
</template>

<script setup>
import { onActivated, ref } from 'vue';
import { FormActions, http, userInfo } from '/src/lib';
import { contentCategoryStore, contentStore } from '/src/admin/stores';
import Editor from '/src/components/Editor.vue';
import Tags from '/src/components/Tags.vue';
import Album from '/src/components/UploadAlbum.vue';

const props = defineProps(['id']);

const formRef = ref();
const formRules = {
  title: {
    required: true,
    message: '请输入标题',
    trigger: ['blur', 'change']
  }
};

const formActions = new FormActions(contentStore, formRef, {
  id: 0,
  cat_ids: [],
  title: '',
  asc_num: 50,
  intro: '',
  is_published: 1,
  detail: '<p><br></p>',
  album: [],
  tags: [],
  // eslint-disable-next-line no-undef
  editor: userInfo.showName,
  author: '',
  source: ''
});
const {
  item,
  is,
  reset,
  save
} = formActions;

// 加载热门标签
const hotTags = ref([]);
onActivated(() => {
  http.get('/admin/contents/tags/hot').then(resp => {
    hotTags.value = resp.items;
  });
});

contentCategoryStore.checkLoaded();
formActions.initFormPage(props);
</script>
