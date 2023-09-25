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
      :scroll-to-error="true"
      :class="is.showValidateError ? '' : 'hide-validate-error'"
    >
      <h2 class="form-title">基本信息</h2>

      <el-form-item label="商品标题" prop="title">
        <el-input v-model="item.title" placeholder="" class="w-full" clearable/>
      </el-form-item>

      <el-form-item label="商品卖点" prop="usp">
        <el-input v-model="item.usp" placeholder="" class="w-full" clearable/>
      </el-form-item>

      <el-form-item label="商品编码" prop="code">
        <el-input v-model="item.code" placeholder="" class="w-full" clearable/>
      </el-form-item>

      <el-form-item label="价格" prop="price">
        <el-input v-model.number="item.price" class="w-40" :min="0" :step="0.01" :precision="2" placeholder="" :disabled="item.skus_formatted.options?.length > 0"/>
        <span class="tips">元</span>
      </el-form-item>

      <el-form-item label="显示排序" prop="asc_num">
        <el-input-number v-model.number="item.asc_num" :min="0" :max="9999" class=""/>
        <span class="tips">从小到大显示</span>
      </el-form-item>

      <el-form-item label="销售状态" prop="is_on_sale">
        <el-switch
          width="50"
          v-model="item.is_on_sale"
          active-text="在售"
          inactive-text="停售"
          inline-prompt
        />
      </el-form-item>

      <el-form-item label="分类" prop="cat_ids">
        <el-tree-select
          class="w-full"
          placeholder="请选择"
          v-model="item.cat_ids"
          :data="goodsCategoryStore.listData.items"
          :props="{label: 'title', value: 'id'}"
          :render-after-expand="true"
          default-expand-all
          multiple
          clearable
        />
      </el-form-item>

      <el-form-item label="标签" prop="tag_ids">
        <Tags v-model="item.tags" :hot-tags="hotTags"/>
      </el-form-item>

      <el-form-item label="商品相册" prop="asc_num">
        <Album v-model="item.album" :size-limit="5" :num-limit="10"/>
      </el-form-item>

      <h2 class="form-title">商品规格</h2>

      <FormSku v-model="item.skus_formatted" v-model:price="item.price" :form-ref="formRef"/>

      <h2 class="form-title">商品介绍</h2>

      <el-form-item label="商品简介" prop="intro">
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
import { goodsCategoryStore, goodsStore } from '/src/admin/stores';
import { FormActions, http } from '/src/lib';
import Editor from '/src/components/Editor.vue';
import Tags from '/src/components/Tags.vue';
import Album from '/src/components/UploadAlbum.vue';
import FormSku from './FormSku.vue';

const props = defineProps(['id']);

const formRef = ref();
const formRules = {
  title: {
    required: true,
    message: '请输入商品标题',
    trigger: ['blur', 'change']
  },
  price: {
    required: true,
    type: 'number',
    min: 0,
    message: '请输入金额',
    trigger: ['blur', 'change']
    // transform: value => parseInt(value)
  }
};

const formActions = new FormActions(goodsStore, formRef, {
  id: 0,
  cat_ids: [],
  title: '',
  asc_num: 50,
  intro: '',
  usp: '',
  is_on_sale: true,
  packing_fee: 0.00,
  detail: '<p><br></p>',
  album: [],
  tags: [],
  skus_formatted: {
    options: [],
    skuMap: {}
  }
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
  http.get('/admin/goods/tags/hot').then(resp => {
    hotTags.value = resp.items;
  });
});

// 确保分类数据已加载
goodsCategoryStore.checkLoaded();
formActions.initFormPage(props);

window.item = item;
</script>
