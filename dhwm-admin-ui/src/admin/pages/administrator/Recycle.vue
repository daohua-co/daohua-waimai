<template>
  <el-container class="is-vertical">
    <div class="op-bar flex mb-4">
      <div class="ops flex-auto">
        <el-button class="px-2" :icon="Refresh" :loading="is.loading" @click="reload">刷新</el-button>
      </div>
      <div class="filters flex-none">
        <el-input clearable @clear="search()" v-model="listData.query.keyword" @keyup.enter="search()" class="keyword mx-1.5" placeholder="账号/姓名/手机号/邮箱">
          <template #prefix>
            <span class="iconfont icon-search"></span>
          </template>
        </el-input>

        <el-button type="primary" @click="search()">搜索</el-button>
      </div>
    </div>

    <div class="box user-list">
      <el-table style="width: 100%;" :data="listData.items" @selection-change="items => selectChange(items)" v-loading="is.loading" stripe>
        <el-table-column type="selection" width="45" align="center" fixed></el-table-column>
        <el-table-column prop="id" label="ID" width="45" class-name="cell-px-0"></el-table-column>
        <el-table-column prop="name" label="账号" width="100" class-name="item-name cell-px-0"></el-table-column>
        <el-table-column prop="realname" label="姓名" width="120"></el-table-column>
        <!--
        <el-table-column prop="mobile" label="手机号" width=""></el-table-column>
        <el-table-column prop="email" label="邮箱" width=""></el-table-column>
        -->
        <el-table-column prop="department" label="部门">
          <template #default="{row}">
            <el-tag v-if="row.department">{{ row.department.title }}</el-tag>
          </template>
        </el-table-column>

        <el-table-column label="角色" width="">
          <template #default="{row}">
            <el-tag class="mr-3" size="small" type="danger" round v-if="row.is_super">超级管理员</el-tag>
            <template v-if="row.roles && row.roles.length > 0">
              <el-tag class="mr-1.5" size="small" round v-for="role in row.roles" :key="role.id">
                {{ role.title }}
              </el-tag>
            </template>
            <span v-else-if="!row.is_super">-</span>
          </template>
        </el-table-column>

        <el-table-column label="状态" prop="enabled" width="70" align="center" class-name="cell-px-0" sortable>
          <template #default="{ row }">
            <el-tag size="small" :type="row.enabled ? 'success' : 'danger'" round>
              {{ row.enabled ? '启用' : '停用' }}
            </el-tag>
          </template>
        </el-table-column>

        <el-table-column prop="created_at" label="创建时间" width="150" align="center" class-name="cell-px-0"></el-table-column>

        <el-table-column label="操作" width="136" class-name="list-item-ops" align="center" fixed="right">
          <template #default="{row}">
            <el-link type="primary" @click="$refs.showRef.show(row.id)" :disabled="!canView">查看</el-link>
            <ConfirmOperate title="您确定要恢复该账号吗？" label="恢复" @confirm="restore(row.id)" :disabled="!canRestore"/>
          </template>
        </el-table-column>
      </el-table>
    </div>

    <Pagination :list-store="adminUserRecycleStore"/>

  </el-container>

  <ShowDrawer ref="showRef"/>
  <FormDialog ref="formRef"/>
</template>

<script setup>
import { onActivated } from "vue";
import { Refresh } from '@element-plus/icons-vue';
import { responseMessage, success, useCan } from '/src/lib';
import { adminUserRecycleStore, adminUserStore } from '/src/admin/stores';
import ShowDrawer from './ShowDrawer.vue';
import FormDialog from './FormDialog.vue';

// 权限
const canView = useCan('administrator.view');
const canRestore = useCan('administrator.recycle.restore');
const {
  listData,
  is,
  selectChange,
  load,
  search,
  reload,
} = adminUserRecycleStore;

const restore = id => {
  adminUserRecycleStore.api.patch(id).then(resp => {
    if (!resp.success) {
      responseMessage(resp);
      return;
    }

    success('恢复成功');
    adminUserRecycleStore.search();
    adminUserStore.reload();
  });
};

onActivated(() => {
  load({
    recycle: 1,
  });
});

</script>

