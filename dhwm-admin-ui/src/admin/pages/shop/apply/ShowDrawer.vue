<template>
  <el-drawer class="shop-apply-drawer" v-model="is.show" title="加盟申请详情" size="560">
    <el-skeleton :rows="10" :loading="is.loading"/>
    <el-descriptions :column="1" label-align="right" label-size="small" border v-if="!is.loading">
      <el-descriptions-item label="ID"> {{ item.id }}</el-descriptions-item>
      <el-descriptions-item label="姓名"> {{ item.name }}</el-descriptions-item>
      <el-descriptions-item label="性别"> {{ item.sex === 1 ? '男' : '女' }} </el-descriptions-item>
      <el-descriptions-item label="手机号码"> {{ item.mobile }}</el-descriptions-item>
      <el-descriptions-item label="目前从事行业"> {{ item.industry }}</el-descriptions-item>
      <el-descriptions-item label="从业年限"> {{ item.working_seniority }}年</el-descriptions-item>
      <el-descriptions-item label="意向加盟区域">
        {{ item.area_names?.join('&gt;') }}
      </el-descriptions-item>
      <el-descriptions-item label="意向加盟区域城镇人口"> {{ item.town_population }}万</el-descriptions-item>
      <el-descriptions-item label="可投入资金"> {{ item.available_capital }}万元</el-descriptions-item>
      <el-descriptions-item label="是否有操盘团队"> {{ item.has_operating_team ? '有' : '无' }}</el-descriptions-item>
      <el-descriptions-item label="你的优势"> {{ item.advantage || '-' }}</el-descriptions-item>
      <el-descriptions-item label="状态">
        <el-tag size="small" :type="statusType(item.status)" effect="plain" round>{{ item.status_text }}</el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="创建时间"> {{ item.created_at }}</el-descriptions-item>
      <el-descriptions-item label="更新时间"> {{ item.updated_at }}</el-descriptions-item>
    </el-descriptions>

    <el-divider content-position="left">审核日志</el-divider>

    <div class="no-data" v-if="item.processes?.length === 0">暂无</div>

    <template v-else-if="item.processes && !is.loading">
      <el-descriptions class="mb-2" :column="1" label-align="right" label-size="small" border v-for="process in item.processes" :key="process.id">
        <el-descriptions-item label="审核结果">
          <el-tag size="small" :type="process.result === 'locked' ? 'danger' : (process.result === 'reject' ? 'warning' : 'success')" effect="plain" round>
            {{ process.result_text }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="审核说明"> {{ process.remark || '-' }}</el-descriptions-item>
        <el-descriptions-item label="审核人"> {{ process.administrator?.realname }} ({{ process.administrator?.name }})</el-descriptions-item>
        <el-descriptions-item label="审核时间"> {{ process.created_at }}</el-descriptions-item>
      </el-descriptions>
    </template>

    <template #footer v-if="item.status === 'submit' || item.status === 'resubmit'">
      <el-button type="primary" @click="() => {$refs.processRef.show(item); is.show=false;}" :disabled="!canProcess">审核</el-button>
    </template>
  </el-drawer>

  <ProcessDialog ref="processRef" />
</template>

<script setup>
import { ShowActions, useCan } from '/src/lib';
import { shopApplyStore } from '/src/admin/stores';
import { statusType } from '/src/admin/helpers/shop-apply';
import ProcessDialog from './ProcessDialog.vue';

const canProcess = useCan('shop.apply.process');
const showActions = new ShowActions(shopApplyStore.api);
const {
  is,
  item,
  show,
} = showActions;

defineExpose({
  show
});
</script>

<style>
.shop-apply-drawer .el-descriptions__label {
  width: 168px !important;
}
</style>
