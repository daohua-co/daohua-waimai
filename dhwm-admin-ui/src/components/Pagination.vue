<template>
    <el-pagination
        background
        class="mt-4"
        layout="prev, pager, next, ->, total"
        @current-change="(page) => listStore.reload(page)"
        :current-page="listStore.listData.current_page"
        :total="listStore.listData.total"
        :pager-count="9"
        :page-size="listStore.listData.per_page"
        v-if="listStore.listData.total > 0 && listStore.listData.per_page < 200"
    ></el-pagination>
</template>

<script setup>
defineProps({
  listStore: Object
});
</script>
