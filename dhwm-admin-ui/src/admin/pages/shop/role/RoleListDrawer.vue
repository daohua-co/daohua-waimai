<template>
  <el-drawer
    v-model="is.show"
    title="管理角色"
    size="800"
  >
    <div class="op-bar flex mb-4" v-if="cans.create">
      <div class="ops" v-cloak>
        <el-button type="primary" @click="$refs.formRef.create()" :disabled="!cans.create">+ 新建</el-button>
        <el-button class="px-2" :icon="Refresh" :loading="is.loading" @click="reload()">刷新</el-button>
      </div>
    </div>
    <div class="box" v-cloak>
      <el-table :data="listData.items" v-loading="is.loading" stripe>
        <el-table-column prop="id" label="ID" width="80"></el-table-column>
        <el-table-column prop="title" label="标题" width="150"></el-table-column>
        <el-table-column prop="intro" label="简介" width=""></el-table-column>
        <el-table-column prop="user_count" label="人员数" class-name="cell-px-0" width="70" align="center" sortable/>
        <el-table-column prop="asc_num" label="显示排序" width="80" align="center"></el-table-column>
        <el-table-column label="状态" prop="enabled" width="70" align="center" class-name="cell-px-0" sortable>
          <template #default="{ row }">
            <el-tag size="small" effect="plain" :type="row.enabled ? 'success' : 'danger'" round>
              {{ row.enabled ? '启用' : '停用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="138" class-name="list-item-ops" align="center">
          <template #default="scope">
            <el-link type="primary" @click="$refs.showRef.show(scope.row.id)" :disabled="!cans.view">查看</el-link>
            <el-link type="primary" @click="$refs.formRef.edit(scope.row.id)" :disabled="!cans.update">编辑</el-link>
            <ConfirmOperate title="您确定要删除该角色吗？" @confirm="destroy(scope.row.id)" :disabled="!cans.delete"/>
          </template>
        </el-table-column>
      </el-table>
    </div>
  </el-drawer>

  <ShowDrawer ref="showRef"/>
  <FormDialog :shop-id="shopId" ref="formRef"/>
</template>

<script setup>
import { Refresh } from '@element-plus/icons-vue';
import { useCans } from '/src/lib';
import { shopPermissionStore, shopRoleStore } from '/src/admin/stores';
import ShowDrawer from './ShowDrawer.vue';
import FormDialog from './FormDialog.vue';

const props = defineProps(['shopId']);

// 权限
const cans = useCans('shop.role', ['view', 'create', 'update', 'delete']);
const {
  listData,
  is,
  load,
  destroy,
  reload,
} = shopRoleStore;

defineExpose({
  show() {
    is.show = true;
    listData.query = {
      shop_id: props.shopId
    };
    load({'with-permissions': 1}, {permissions: shopPermissionStore.listData.items});
  }
});
</script>
