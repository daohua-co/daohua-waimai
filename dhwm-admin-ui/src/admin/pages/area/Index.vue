<template>
  <div class="box p-4 pr-0">
    <el-tree-v2 :data="listData.items" :height="height" v-loading="is.loading">
      <template #default="{ data }">
        <span class="">{{ data.name }}</span>
        <span class="ml-auto mr-8 text-neutral-400">
          <span class="ml-4" v-if="data.short_name">{{ data.short_name }}</span>
          <span class="data-id ml-6">{{ data.id }}</span>
          <el-text class="ml-6" type="primary" @click.stop="$refs.formRef.edit(data.id)" :disabled="!cans.update">编辑</el-text>
        </span>
      </template>
    </el-tree-v2>
  </div>

  <FormDialog ref="formRef"/>
</template>

<script setup>
import { ref } from 'vue';
import { useCans } from '/src/lib';
import { areaStore } from '/src/admin/stores';
import FormDialog from './FormDialog.vue';

const cans = useCans('area', ['create', 'update']);

const {is, load, listData} = areaStore;
load();

const height = ref(window.innerHeight - 160);
window.onresize = e => {
  height.value = e.currentTarget.innerHeight - 160;
};
</script>

<style lang="scss" scoped>
.data-id {
  font-family: Menlo, Consolas, Courier New, serif; // 用等宽字体让 id 排版对齐
}
</style>
