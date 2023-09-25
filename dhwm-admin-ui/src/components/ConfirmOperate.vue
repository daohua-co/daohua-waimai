<!--
 气泡确认框操作。
 可选显示方式：
   - 按钮
   - 图标
   - 链接
 -->
<template>
    <template v-if="disabled">
        <el-button type="primary" disabled v-if="type==='button'">{{ label }}</el-button>
        <el-icon disabled v-else-if="type==='icon'"><CircleCloseFilled /></el-icon>
        <el-link type="primary" disabled v-else>{{ label }}</el-link>
    </template>
    <el-popconfirm
        v-else
        confirm-button-text="确定"
        cancel-button-text="取消"
        @confirm="$emit('confirm')"
        :title="title"
    >
        <template #reference>
            <el-button type="primary" v-if="type==='button'">{{ label }}</el-button>
            <el-icon v-else-if="type==='icon'"><CircleCloseFilled /></el-icon>
            <el-link type="primary" v-else>{{ label }}</el-link>
        </template>
    </el-popconfirm>
</template>

<script setup>
import { CircleCloseFilled } from '@element-plus/icons-vue';

defineProps({
  title: {
    type: String, // link(default)/button/icon
    default: '确定要删除吗'
  },
  label: {
    type: String,
    default: '删除'
  },
  type: {
    type: String,
    default: 'link'
  },
  disabled: Boolean
});
defineEmits(['confirm']);
</script>
