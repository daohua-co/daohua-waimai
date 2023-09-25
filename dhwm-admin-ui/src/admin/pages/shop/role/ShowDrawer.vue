<template>
  <el-form ref="formRef" :model="item" label-width="100px" class="show-wp">
    <el-drawer v-model="is.show" title="角色详情" size="560">
      <el-skeleton :rows="10" :loading="is.loading"/>
      <el-descriptions :column="1" label-align="right" label-size="small" border v-if="!is.loading">

        <el-descriptions-item label="门店"> {{ item.shop?.area_names?.join(' > ') }} </el-descriptions-item>
        <el-descriptions-item label="角色名称"> {{ item.title }}</el-descriptions-item>
        <el-descriptions-item label="角色简介"> {{ item.intro ? item.intro : '-' }}</el-descriptions-item>
        <el-descriptions-item label="显示排序"> {{ item.asc_num }}</el-descriptions-item>
        <el-descriptions-item label="显示状态">
          <el-tag size="small" effect="plain" :type="item.enabled ? 'success' : 'danger'" round>
            {{ item.enabled ? '启用' : '停用' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="访问授权">
          <span class="" v-if="item.permissions && item.permissions.length === 0">未设置</span>
          <div class="show-permissions w-full" v-else>
            <div v-if="item.permissions && item.permissions[0] === '*'">全部权限</div>
            <div class="" v-show="!item.permissions || item.permissions[0] != '*'">
              <el-tree
                ref="permissionsTreeRef"
                :data="shopPermissionStore.listData.items"
                node-key="name"
                :props="{label: 'title', class: (data, node) => node.indeterminate ? 'is-indeterminate' : ''}"
                default-expand-all
                :default-checked-keys="item.permissions"
              ></el-tree>
            </div>
          </div>
        </el-descriptions-item>
      </el-descriptions>
      <template #footer>
        <el-button type="primary" @click="$parent.$refs.formRef.edit(item.id)">编辑</el-button>
      </template>
    </el-drawer>
  </el-form>
</template>

<script setup>
import { ref } from 'vue';
import { ShowActions } from '/src/lib';
import { shopPermissionStore, shopRoleStore } from '/src/admin/stores';

const permissionsTreeRef = ref();
const showActions = new ShowActions(shopRoleStore.api);
const {
  item,
  is,
  show
} = showActions;

shopPermissionStore.checkLoaded();

defineExpose({
  show: id => {
    show(id).then(() => {
      permissionsTreeRef.value && permissionsTreeRef.value.setCheckedKeys(item.permissions, false);
    });
  }
});
</script>

<style lang="scss">
.show-permissions .el-tree-node__content {
  display: none;
}

.show-permissions .is-indeterminate > .el-tree-node__content,
.show-permissions .is-checked .el-tree-node__content {
  display: flex;
}
</style>
