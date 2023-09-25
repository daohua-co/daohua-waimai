<template>
  <div class="op-bar flex mb-4">
    <div class="ops flex-auto">
      <el-button type="primary" :disabled="!cans.create" @click="$refs.formRef.create()">+ 新建</el-button>
      <el-button class="px-2" :icon="Refresh" :loading="is.loading" @click="reload">刷新</el-button>
    </div>
    <div class="filters flex-none">
      <el-cascader
        v-model="listData.query.area_id"
        class="w-64 mr-1"
        placeholder="选择区域"
        :options="areaStore.listData.items"
        :props="{checkStrictly: true, emitPath: false, expandTrigger: 'hover', label: 'name', value: 'id'}"
        filterable
        clearable
      >
        <template #default="{ data }">
          <div @click="selectArea(data.id)">{{ data.short_name || data.name }}</div>
        </template>
      </el-cascader>
      <el-select v-model="listData.query.status" class="mr-1 w-28" placeholder="选择状态" clearable>
        <el-option value="" label="全部状态"/>
        <el-option value="pre" label="筹备中"/>
        <el-option value="on" label="营业中"/>
        <el-option value="off" label="已关停"/>
      </el-select>
      <el-input clearable @clear="search()" v-model="listData.query.keyword" @keyup.enter="search()" class="keyword mr-1" placeholder="搜索ID/电话">
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
      v-loading="is.loading"
    >
      <el-table-column prop="id" label="ID" width="68" sortable fixed></el-table-column>
      <el-table-column prop="operation_mode" label="经营模式" width="72" align="center" class-name="cell-px-0">
        <template #default="{row}">
          <el-tag size="small" effect="plain" round type="success" v-if="row.operation_mode === 'zy'">自营</el-tag>
          <el-tag size="small" effect="plain" round type="warning" v-if="row.operation_mode === 'jm'">加盟</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="area_id" label="区域">
        <template #default="{row}">
          {{ row.area_names?.join('&gt;') }}
        </template>
      </el-table-column>
      <el-table-column prop="contact_mobile" label="联系电话"></el-table-column>
      <el-table-column prop="status" label="状态" width="64" align="center" class-name="cell-px-0">
        <template #default="{row}">
          <el-tag size="small" effect="plain" round type="info" v-if="row.status === 'pre'">筹备中</el-tag>
          <el-tag size="small" effect="plain" round type="success" v-if="row.status === 'on'">营业中</el-tag>
          <el-tag size="small" effect="plain" round type="danger" v-if="row.status === 'off'">已关停</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="创建时间" width="160" align="center" class-name="cell-px-0"></el-table-column>
      <el-table-column label="操作" width="300" class-name="list-item-ops" align="center" fixed="right">
        <template #default="{row}">
          <el-link type="primary" @click="$refs.showRef.show(row)" :disabled="!cans.view">详情</el-link>
          <el-link type="primary" @click="$refs.formRef.edit(row.id)" :disabled="!cans.update">修改</el-link>
          <el-link type="primary" @click="routeTo(`shops/sellers/${row.id}`)" :disabled="!useCan('shop.seller.viewAll')">店员</el-link>
          <el-link type="primary" @click="routeTo(`shops/warehouses/${row.id}`)" :disabled="!useCan('shop.warehouse.viewAll')">仓库</el-link>
          <el-link type="primary" @click="$refs.applyRef.show(row.apply.id)" :disabled="!canViewApply || row.operation_mode === 'zy'">申请信息</el-link>
        </template>
      </el-table-column>
    </el-table>
  </div>

  <Pagination :list-store="shopStore"/>

  <ApplyShowDrawer ref="applyRef" />
  <ShowDrawer ref="showRef" />
  <FormDialog ref="formRef" />
</template>

<script setup>
import { watch } from "vue";
import { Refresh } from '@element-plus/icons-vue';
import { useCan, useCans, routeTo } from '/src/lib';
import { areaStore, shopStore } from '/src/admin/stores';
import ApplyShowDrawer from './apply/ShowDrawer.vue';
import ShowDrawer from './ShowDrawer.vue';
import FormDialog from './FormDialog.vue';

// 权限
const cans = useCans('shop', ['create', 'update', 'view']);
const canViewApply = useCan('shop.apply.view');
const {
  listData,
  is,
  search,
  reload
} = shopStore;

// 搜索参数
listData.query.keyword = '';
listData.query.area_id = '';
listData.query.status = '';

watch([
  () => listData.query.area_id,
  () => listData.query.status
], () => {
  search();
});

function selectArea(id) {
  listData.query.area_id = id;
}

shopStore.load();
areaStore.checkLoaded();
</script>
