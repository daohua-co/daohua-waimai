<template>
  <div class="op-bar flex mb-4">
    <div class="ops flex-auto">
      <el-button type="primary" @click="$refs.formRef.create()" :disabled="!cans.create">+ 新建</el-button>
      <el-button class="px-2" :icon="Refresh" :loading="is.loading" @click="reload">刷新</el-button>
    </div>
  </div>

  <div class="box">
    <el-table :data="listData.items" row-key="id" default-expand-all stripe v-loading="is.loading">
      <el-table-column prop="title" label="部门名称" class-name="title"/>
      <el-table-column prop="intro" label="部门简介"/>
      <el-table-column prop="user_count" label="人员数" class-name="cell-px-0" width="70" align="center" sortable/>
      <el-table-column prop="asc_num" label="显示排序" class-name="cell-px-0" width="88" align="center" sortable/>
      <el-table-column prop="id" label="ID" width="80" align="center" class-name="cell-px-0" sortable/>
      <el-table-column label="操作" width="136" class-name="list-item-ops" align="center" fixed="right">
        <template #default="{ row }">
          <el-link type="primary" @click="$refs.showRef.show(row)" :disabled="!cans.view">查看</el-link>
          <el-link type="primary" @click="$refs.formRef.edit(row.id)" :disabled="!cans.update">编辑</el-link>
          <ConfirmOperate title="您确定要删除该部门吗？" @confirm="destroy(row.id)" :disabled="!cans.delete"/>
        </template>
      </el-table-column>
    </el-table>
  </div>

  <FormDialog ref="formRef"/>
  <ShowDrawer ref="showRef"/>
</template>

<script setup>
import { Refresh } from '@element-plus/icons-vue';
import { useCans } from '/src/lib';
import { adminDepartmentStore } from '/src/admin/stores';
import FormDialog from './FormDialog.vue';
import ShowDrawer from './ShowDrawer.vue';

const cans = useCans('administrator.department', ['view', 'create', 'update', 'delete']);

const {
  listData,
  is,
  load,
  destroy,
  reload,
} = adminDepartmentStore;

load();
</script>
