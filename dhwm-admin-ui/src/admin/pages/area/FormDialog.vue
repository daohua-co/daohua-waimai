<template>
  <el-dialog
    v-model="is.show"
    :title="`${is.edit ? '编辑' : '新建'}行政区`"
    @close="onHide()"
    width="560"
    draggable
    align-center
  >
    <el-skeleton :rows="5" :loading="is.loading" animated/>
    <el-form
      v-if="!is.loading"
      ref="formRef"
      :model="item"
      :rules="formRules"
      label-width="100px"
      @submit.prevent="save()"
      :show-message="is.showValidateError"
    >
      <input type="submit" v-show="false"/>

      <el-form-item label="上级地区" prop="parent_id">
        {{ item.parent ? item.parent.name : '无' }}
      </el-form-item>

      <el-form-item label="地区名称" prop="name">
        <el-input v-model="item.name" placeholder=""/>
      </el-form-item>

      <el-form-item label="地区名称简写" prop="short_name">
        <el-input v-model="item.short_name" placeholder=""/>
      </el-form-item>

      <el-form-item label="经度坐标" prop="lng">
        <el-input v-model="item.lng" placeholder=""/>
      </el-form-item>

      <el-form-item label="纬度坐标" prop="lat">
        <el-input v-model="item.lat" placeholder=""/>
      </el-form-item>

    </el-form>
    <template #footer>
      <el-button @click="formActions.reset()">重置</el-button>
      <el-button type="primary" @click="save()" v-loading="is.submitting">确定</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { FormActions } from '/src/lib';
import { areaStore } from '/src/admin/stores';

const parentOptions = ref([]);
const formRef = ref();
const formRules = {
  name: {
    required: true,
    message: '请输入名称',
    trigger: ['blur', 'change']
  }
};

const formActions = new FormActions(areaStore, formRef, {
  id: 0,
  name: '',
  short_name: ''
});
const {
  is,
  item,
  onHide,
  edit,
  save
} = formActions;

defineExpose({ edit });
</script>
