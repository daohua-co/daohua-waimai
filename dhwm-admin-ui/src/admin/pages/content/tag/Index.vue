<template>
  <div class="op-bar flex mb-4">
    <div class="ops flex-auto">
      <ConfirmOperate type="button" label="批量删除" @confirm="batOperate()" :disabled="!cans.batDelete || listData.batOperateIds.length === 0"/>
      <el-button type="primary" @click="$refs.formRef.create()" :disabled="!cans.create">+ 新建</el-button>
      <el-button class="px-2" :icon="Refresh" :loading="is.loading" @click="reload">刷新</el-button>
    </div>
    <div class="filters flex-none">
      <el-input clearable @clear="search()" v-model="listData.query.keyword" @keyup.enter="search()" class="keyword mx-1.5" placeholder="搜索标签名">
        <template #prefix>
          <span class="iconfont icon-search"></span>
        </template>
      </el-input>
      <el-button type="primary" @click="search()">搜索</el-button>
    </div>
  </div>

  <div class="box">
    <el-table
      :data="listData.items"
      @selection-change="items => selectChange(items)"
      v-loading="is.loading"
    >
      <el-table-column type="selection" width="45" align="center" fixed></el-table-column>
      <el-table-column prop="id" label="ID" width="60" fixed></el-table-column>
      <el-table-column prop="title" label="标签"></el-table-column>
      <el-table-column prop="content_count" label="文章数" width="72" align="center"></el-table-column>
      <el-table-column prop="created_at" label="创建时间" width="168" align="center" class-name="cell-px-0"></el-table-column>
      <el-table-column label="操作" width="120" class-name="list-item-ops" align="center" fixed="right">
        <template #default="scope">
          <el-link type="primary" @click="$refs.formRef.edit(scope.row.id)" :disabled="!cans.update">编辑</el-link>
          <ConfirmOperate title="您确定要删除该标签吗？" @confirm="destroy(scope.row.id)" :disabled="!cans.delete"/>
        </template>
      </el-table-column>
    </el-table>
  </div>

  <div class="btm-ops mt-3">
    <ConfirmOperate
      type="button"
      label="批量删除"
      @confirm="batOperate()"
      :disabled="!cans.batDelete || listData.batOperateIds.length === 0"
    />
  </div>

  <Pagination :list-store="contentTagStore"/>
  <FormDialog ref="formRef"/>
</template>

<script setup>
import { Refresh } from '@element-plus/icons-vue';
import { useCans } from '/src/lib';
import { contentTagStore } from '/src/admin/stores';
import FormDialog from './FormDialog.vue';

// 权限
const cans = useCans('content.tag', ['create', 'update', 'delete', 'batDelete']);
const {
  listData,
  is,
  search,
  load,
  destroy,
  batOperate,
  selectChange,
  reload,
} = contentTagStore;
load();
</script>
