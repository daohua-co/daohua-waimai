<template>
  <div class="op-bar flex mb-4">
    <div class="ops flex-auto">
      <ConfirmOperate type="button" label="批量删除" @confirm="batOperate()" :disabled="!cans.batDelete || listData.batOperateIds.length === 0"/>
      <el-button type="primary" @click="routeTo('/contents/create')" :disabled="!cans.create">+ 新建</el-button>
      <el-button class="px-2" :icon="Refresh" :loading="is.loading" @click="reload">刷新</el-button>
    </div>
    <div class="filters flex-none">
      <el-tree-select
        class="w-50"
        placeholder="选择分类"
        v-model="listData.query.cat_id"
        :data="contentCategoryStore.listData.items"
        :props="{label: 'title', value: 'id', multiple: true}"
        :render-after-expand="false"
        default-expand-all
        collapse-tags
        collapse-tags-tooltip
        clearable
        filterable
        checkStrictly
      />
      <el-input clearable @clear="search()" v-model="listData.query.keyword" @keyup.enter="search()" class="keyword mx-1.5" placeholder="搜索标题">
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
      @selection-change="(items) => selectChange(items)"
      v-loading="is.loading"
    >
      <el-table-column type="selection" width="45" align="center" fixed></el-table-column>
      <el-table-column prop="id" label="ID" width="50" class-name="cell-px-0" fixed></el-table-column>

      <el-table-column prop="title" label="标题">
        <template #default="{ row }">
          <img :src="row.album[0].thumb" v-if="row.album[0]" width="42" align="left" class="mr-2">
          {{ row.title }}
        </template>
      </el-table-column>

      <el-table-column label="分类" width="">
        <template #default="{row}">
          <template v-if="row.categories.length > 0">
            <el-tag class="mr-1.5" size="small" v-for="cat in row.categories" :key="cat.id">
              {{ cat.title }}
            </el-tag>
          </template>
          <span v-else>-</span>
        </template>
      </el-table-column>

      <el-table-column label="标签" width="">
        <template #default="{row}">
          <template v-if="row.tags.length > 0">
            <el-tag class="mr-1.5" size="small" v-for="tag in row.tags" :key="tag.id">
              {{ tag.title }}
            </el-tag>
          </template>
          <span v-else>-</span>
        </template>
      </el-table-column>

      <el-table-column prop="is_published" label="状态" width="45" class-name="cell-px-0" align="center">
        <template #default="{row}">
          <span class="text-success" v-if="row.is_published">发布</span>
          <span class="text-mutted" v-else>草稿</span>
        </template>
      </el-table-column>

      <el-table-column prop="asc_num" label="显示排序" width="60" class-name="cell-px-0" align="center"></el-table-column>
      <el-table-column prop="created_at" label="创建时间" width="168" align="center" class-name="cell-px-0"></el-table-column>
      <el-table-column label="操作" width="136" class-name="list-item-ops" align="center" fixed="right">
        <template #default="{row}">
          <el-link type="primary" @click="$refs.showRef.show(row.id)" :disabled="!cans.view">详情</el-link>
          <el-link type="primary" @click="routeTo(`/contents/${row.id}/edit`)" :disabled="!cans.update">编辑</el-link>
          <ConfirmOperate title="您确定要删除该文章吗？" @confirm="destroy(row.id)" :disabled="!cans.delete"/>
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
  <Pagination :list-store="contentStore"/>
  <ShowDrawer ref="showRef"/>
</template>

<script setup>
import { watch } from 'vue';
import { Refresh } from '@element-plus/icons-vue';
import { routeTo, useCans } from '/src/lib';
import { contentStore, contentCategoryStore } from '/src/admin/stores';
import ShowDrawer from './ShowDrawer.vue';

// 权限
const cans = useCans('content', ['view', 'create', 'update', 'delete', 'batDelete']);

const {
  listData,
  is,
  batOperate,
  search,
  selectChange,
  destroy,
  load,
  reload,
} = contentStore;

load({'with-category': 1}, {categories: contentCategoryStore.listData.items});

watch(() => listData.query.cat_id, () => search());
</script>
