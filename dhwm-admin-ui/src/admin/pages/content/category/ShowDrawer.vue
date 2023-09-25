<template>
  <el-drawer
    v-model="is.show"
    title="分类详情"
    size="560"
  >
    <el-skeleton :rows="8" :loading="is.loading" animated/>
    <el-descriptions :column="1" label-align="right" label-size="small" border v-if="!is.loading">
      <el-descriptions-item label="ID"> {{ item?.id }}</el-descriptions-item>
      <el-descriptions-item label="上级分类">
        {{ item.parent ? item.parent.title : '-' }}
      </el-descriptions-item>
      <el-descriptions-item label="分类标题"> {{ item.title }}</el-descriptions-item>
      <el-descriptions-item label="分类简介"> {{ item.intro ?? '-' }}</el-descriptions-item>
      <el-descriptions-item label="显示排序"> {{ item.asc_num }}</el-descriptions-item>
      <el-descriptions-item label="文章数量"> {{ item.content_count }}</el-descriptions-item>

      <el-descriptions-item label="显示状态">
        <el-tag size="small" effect="plain" :type="item.is_show ? 'success' : 'danger'" round>
          {{ item.is_show ? '显示' : '隐藏' }}
        </el-tag>
      </el-descriptions-item>

      <el-descriptions-item label="创建时间"> {{ item.created_at }}</el-descriptions-item>
      <el-descriptions-item label="更新时间"> {{ item.updated_at }}</el-descriptions-item>
    </el-descriptions>
    <template #footer>
      <el-button type="primary" @click="() => toEdit($parent.$refs.formRef)">编辑</el-button>
    </template>
  </el-drawer>
</template>

<script setup>
import { contentCategoryStore } from '/src/admin/stores';
import { ShowActions } from '/src/lib';

const showActions = new ShowActions(contentCategoryStore.api);
const {
  is,
  item,
  show,
  toEdit
} = showActions;

defineExpose({
  show: id => show(id, {'with-parent': 1})
});
</script>
