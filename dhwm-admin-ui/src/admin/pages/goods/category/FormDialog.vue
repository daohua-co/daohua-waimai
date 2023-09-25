<template>
  <el-dialog
    v-model="is.show"
    :title="`${is.edit ? '编辑' : '新建'}商品分类`"
    @close="onHide"
    width="560"
    draggable
    align-center
  >
    <el-skeleton :rows="5" :loading="is.loading" animated/>
    <el-form v-if="!is.loading"
             ref="formRef"
             :model="item"
             :rules="formRules"
             label-width="100px"
             @submit.prevent="save"
             :class="is.showValidateError ? '' : 'hide-validate-error'"
    >
      <input type="hidden" v-show="false"/>

      <el-form-item label="上级分类" prop="parent_id">
        <el-tree-select
          v-model="item.parent_id"
          class="w-full"
          placeholder="请选择"
          :data="parentOptions"
          :props="{label: 'title', value: 'id'}"
          :render-after-expand="true"
          check-strictly
          default-expand-all
          clearable
        />
      </el-form-item>

      <el-form-item label="分类标题" prop="title">
        <el-input v-model="item.title" placeholder=""/>
      </el-form-item>

      <el-form-item label="简介" prop="intro">
        <el-input v-model="item.intro" :row="2" type="textarea" placeholder="" class=""/>
      </el-form-item>

      <el-form-item label="显示排序" prop="asc_num">
        <el-slider v-model="item.asc_num" show-input/>
        <div class="tips">从小到大显示</div>
      </el-form-item>

      <el-form-item label="显示状态" prop="is_show">
        <el-switch
          width="50"
          v-model="item.is_show"
          active-text="显示"
          inactive-text="隐藏"
          inline-prompt
        />
        <span class="tips">在用户端品类菜单中是否显示</span>
      </el-form-item>

    </el-form>
    <template #footer>
      <el-button @click="reset()">重置</el-button>
      <el-button type="primary" @click="save()" v-loading="is.submitting">确定</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { goodsCategoryStore } from '/src/admin/stores';
import { FormActions } from '/src/lib';

const parentOptions = ref([]);
const formRef = ref();
const formRules = {
  title: {
    required: true,
    message: '请输入商品分类名',
    trigger: ['blur', 'change']
  },
  asc_num: {
    type: 'number',
    min: 0,
    max: 100,
    message: '序号不能大于100',
    trigger: ['blur', 'change']
    // transform: value => parseInt(value)
  }
};
const formActions = new FormActions(goodsCategoryStore, formRef, {
  id: 0,
  title: '',
  asc_num: 50,
  intro: '',
  is_show: true
});
const {
  item,
  is,
  onHide,
  reset,
  create,
  edit
} = formActions;

const save = () => {
  formActions.save().then(resp => {
    parentOptions.value = resp.options;
  });
};

defineExpose({
  create() {
    create();
    parentOptions.value = Object.values(goodsCategoryStore.listData.items); // 拷贝数组
  },
  edit(id) {
    edit(id, {'with-parent-options': 1}).then(resp => {
      parentOptions.value = resp.options;
    });
  }
});
</script>
