<template>
  <el-drawer v-model="is.show" title="部门详情" size="560">
    <el-skeleton :rows="10" :loading="is.loading"/>
    <el-descriptions :column="1" label-align="right" label-size="small" border v-if="!is.loading">
      <el-descriptions-item label="ID"> {{ item.id }}</el-descriptions-item>
      <el-descriptions-item label="上级部门">
        {{ item.parent ? item.parent.title : '-' }}
      </el-descriptions-item>
      <el-descriptions-item label="部门名称"> {{ item.title }}</el-descriptions-item>
      <el-descriptions-item label="部门简介"> {{ item.intro ?? '-' }}</el-descriptions-item>
      <el-descriptions-item label="显示排序"> {{ item.asc_num }}</el-descriptions-item>
      <el-descriptions-item label="人员数量"> {{ item.user_count }}</el-descriptions-item>
      <el-descriptions-item label="创建时间"> {{ item.created_at }}</el-descriptions-item>
      <el-descriptions-item label="更新时间"> {{ item.updated_at }}</el-descriptions-item>
    </el-descriptions>
    <template #footer>
      <!--            <el-button @click="is.show=false">取消</el-button>-->
      <el-button type="primary" @click="toEdit($parent.$refs.formRef)">编辑
      </el-button>
    </template>
  </el-drawer>

</template>

<script setup>
import { ShowActions } from '/src/lib';
import { adminDepartmentStore } from '/src/admin/stores';

const showActions = new ShowActions(adminDepartmentStore.api);
const {
  is,
  item,
  show,
  toEdit
} = showActions;

defineExpose({
  show: (id) => show(id, {'with-parent': 1})
});
</script>
