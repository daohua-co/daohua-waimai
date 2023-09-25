<template>
  <div class="op-bar flex mb-4">
    <div class="ops flex-auto">
      <el-button class="px-2" :icon="Refresh" :loading="is.loading" @click="reload">刷新</el-button>
    </div>
    <div class="filters flex-none">
      <el-cascader
        v-model="listData.query.area_id"
        class="w-64 mr-2"
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
      <el-select v-model="listData.query.status" class="mr-2 w-28" placeholder="选择状态" clearable>
        <el-option value="" label="全部状态"/>
        <el-option value="submit" label="待审"/>
        <el-option value="accept" label="通过"/>
        <el-option value="reject" label="驳回"/>
        <el-option value="locked" label="锁定"/>
      </el-select>
      <el-input clearable @clear="search()" v-model="listData.query.keyword" @keyup.enter="search()" class="keyword mx-1.5 w-36" placeholder="搜索姓名/电话">
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
      <el-table-column prop="id" label="ID" width="68" fixed></el-table-column>
      <el-table-column prop="area_id" label="意向加盟区域">
        <template #default="{row}">
          {{ row.area_names?.join('&gt;') }}
        </template>
      </el-table-column>
      <el-table-column prop="name" label="姓名" width="80"></el-table-column>
      <el-table-column prop="sex" label="性别" width="60">
        <template #default="{ row }">
          {{ row.sex === 1 ? '男' : '女' }}
        </template>
      </el-table-column>
      <el-table-column prop="mobile" label="手机号"></el-table-column>
      <el-table-column prop="industry" label="从事行业"></el-table-column>
      <el-table-column prop="working_seniority" label="从业年限" align="center">
        <template #default="{row}">
          {{ row.working_seniority }}年
        </template>
      </el-table-column>
      <el-table-column prop="status" label="状态" width="64" align="center" class-name="cell-px-0">
        <template #default="{row}">
          <el-tag :type="statusType(row.status)" size="small" effect="plain" round>{{ row.status_text }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="created_at" label="创建时间" width="168" align="center" class-name="cell-px-0"></el-table-column>
      <el-table-column label="操作" width="120" class-name="list-item-ops" align="center" fixed="right">
        <template #default="{row}">
          <el-link type="primary" @click="$refs.showRef.show(row.id)" :disabled="!cans.view">查看</el-link>
          <el-link type="primary" @click="$refs.processRef.show(row.id)" :disabled="!cans.process || row.status === 'accept'">审核</el-link>
        </template>
      </el-table-column>
    </el-table>
  </div>
  <Pagination :list-store="shopApplyStore"/>

  <ShowDrawer ref="showRef"/>
  <ProcessDialog ref="processRef"/>
</template>

<script setup>
import { watch } from "vue";
import { Refresh } from '@element-plus/icons-vue';
import { useCans } from '/src/lib';
import { areaStore, shopApplyStore } from '/src/admin/stores';
import { statusType } from '/src/admin/helpers/shop-apply';
import ShowDrawer from './ShowDrawer.vue';
import ProcessDialog from './ProcessDialog.vue';

// 权限
const cans = useCans('shop.apply', ['process', 'view']);
const {
  listData,
  is,
  search,
  reload
} = shopApplyStore;

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

shopApplyStore.load();
areaStore.checkLoaded();

</script>
