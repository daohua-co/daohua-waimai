<template>
  <div class="op-bar flex mb-4">
    <div class="ops flex-auto">
      <el-button type="primary" @click="$refs.formRef.create()" :disabled="!cans.create">+ 新建</el-button>
      <el-button class="px-2" :icon="Refresh" :loading="is.loading" @click="reload">刷新</el-button>
      <el-button @click="$refs.roleRef.show()">管理角色</el-button>
    </div>
    <div class="filters flex-none">
      <el-select v-model="listData.query.enabled" class="mr-1 w-28" placeholder="选择状态" clearable>
        <el-option value="" label="全部状态"/>
        <el-option :value="1" label="启用"/>
        <el-option :value="0" label="停用"/>
      </el-select>
      <el-input clearable @clear="search()" v-model="listData.query.keyword" @keyup.enter="search()" class="keyword mr-1" placeholder="搜用户名/姓名/电话/邮箱">
        <template #prefix>
          <span class="iconfont icon-search"></span>
        </template>
      </el-input>
      <el-button type="primary" @click="search()">搜索</el-button>
    </div>
  </div>

  <div class="box">
    <el-table :data="listData.items" row-key="id" default-expand-all stripe v-loading="is.loading">
      <el-table-column prop="id" label="ID" width="72" class-name="" sortable/>
      <el-table-column prop="shop_id" label="区域" class-name="cell-px-0">
        <template #default>
          {{ shop.area_names?.join(' &gt; ') }}
        </template>
      </el-table-column>
      <el-table-column prop="name" label="登录名"/>
      <el-table-column prop="avatar" label="姓名">
        <template #default="{ row }">
          <el-avatar size="small" class="float-left mr-1" :src="row.avatar" fit="cover">
            <el-icon size="20">
              <Avatar/>
            </el-icon>
          </el-avatar>
          {{ row.realname }}
        </template>
      </el-table-column>
      <el-table-column prop="mobile" label="手机号" class-name="cell-px-0" width=""/>
      <el-table-column prop="email" label="邮箱" class-name="cell-px-0" width="">
        <template #default="{ row }">
          {{ row.email || '-' }}
        </template>
      </el-table-column>
      <el-table-column prop="is_owner" label="账户类型" class-name="cell-px-0" width="72" align="center">
        <template #default="{ row }">
          <el-tag size="small" effect="plain" type="warning" v-if="row.is_owner">店主</el-tag>
          <el-tag size="small" effect="plain" type="info" v-else>店员</el-tag>
        </template>
      </el-table-column>
      <el-table-column prop="enabled" label="状态" class-name="cell-px-0" width="66" align="center">
        <template #default="{ row }">
          <el-tag size="small" effect="plain" type="success" v-if="row.enabled">正常</el-tag>
          <el-tag size="small" effect="plain" type="info" v-else>锁定</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="136" class-name="list-item-ops" align="center" fixed="right">
        <template #default="{ row }">
          <el-link type="primary" @click="$refs.showRef.show(row)" :disabled="!cans.view">查看</el-link>
          <el-link type="primary" @click="$refs.formRef.edit(row.id)" :disabled="!cans.update">编辑</el-link>
          <ConfirmOperate title="您确定要删除该卖家账号吗？" @confirm="destroy(row.id)" :disabled="!cans.delete || row.is_owner"/>
        </template>
      </el-table-column>
    </el-table>
  </div>

  <ShowDrawer ref="showRef"/>
  <FormDialog :shop-id="shopId" ref="formRef"/>
  <RoleListDrawer :shop-id="shopId" ref="roleRef"/>
</template>

<script setup>
import { onActivated, ref, watch } from 'vue';
import { Avatar, Refresh } from '@element-plus/icons-vue';
import { useCans } from '/src/lib';
import { shopPermissionStore, shopRoleStore, shopSellerStore } from '/src/admin/stores';
import FormDialog from './FormDialog.vue';
import ShowDrawer from './ShowDrawer.vue';
import RoleListDrawer from '../role/RoleListDrawer.vue';

const props = defineProps(['shopId']);
const cans = useCans('shop.seller', ['view', 'create', 'update', 'delete']);
const shop = ref({});
const {
  listData,
  is,
  load,
  destroy,
  reload,
  search,
} = shopSellerStore;

onActivated(() => {
  listData.items = [];
  listData.query = {
    shop_id: props.shopId
  };
  load({}, {shop});
  shopRoleStore.listData.query = {
    shop_id: props.shopId
  };
  shopRoleStore.reload();
});

watch(() => listData.query.enabled, () => {
  search();
});

shopPermissionStore.checkLoaded();
</script>
