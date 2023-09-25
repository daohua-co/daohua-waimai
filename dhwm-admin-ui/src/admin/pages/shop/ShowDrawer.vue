<template>
  <el-drawer class="shop-apply-drawer" v-model="is.show" title="分门店信息详情" size="560">
    <el-skeleton :rows="10" :loading="is.loading"/>
    <el-descriptions :column="1" label-align="right" label-size="small" border v-if="!is.loading">
      <el-descriptions-item label="ID"> {{ item.id }}</el-descriptions-item>
      <el-descriptions-item label="经营模式">
        <el-tag size="small" effect="plain" round type="success" v-if="item.operation_mode === 'zy'">自营</el-tag>
        <el-tag size="small" effect="plain" round type="warning" v-if="item.operation_mode === 'jm'">加盟</el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="加盟区域">
        {{ item.area_names?.join('&gt;') }}
      </el-descriptions-item>
      <el-descriptions-item label="详细地址"> {{ item.address_detail }}</el-descriptions-item>
      <el-descriptions-item label="联系电话"> {{ item.contact_mobile }}</el-descriptions-item>
      <el-descriptions-item label="营业执照">
        <el-image
          v-if="item.business_license_img"
          style="width: 100px; height: 100px"
          :src="item.business_license_img"
          :zoom-rate="1.2"
          :preview-src-list="[item.business_license_img]"
          fit="cover"
        />
        <template v-else>
          -
        </template>
      </el-descriptions-item>
      <el-descriptions-item label="状态">
        <el-tag size="small" effect="plain" round type="info" v-if="item.status === 'pre'">筹备中</el-tag>
        <el-tag size="small" effect="plain" round type="success" v-if="item.status === 'on'">营业中</el-tag>
        <el-tag size="small" effect="plain" round type="danger" v-if="item.status === 'off'">已关停</el-tag>
      </el-descriptions-item>
      <el-descriptions-item label="申请时间"> {{ item.apply?.created_at }} </el-descriptions-item>
      <el-descriptions-item label="创建时间"> {{ item.created_at }} </el-descriptions-item>
      <el-descriptions-item label="更新时间"> {{ item.updated_at }} </el-descriptions-item>
    </el-descriptions>
    <template #footer v-if="canUpdate">
      <el-button type="primary" @click="() => {$refs.processRef.show(item); is.show=false;}">编辑</el-button>
    </template>
  </el-drawer>

</template>

<script setup>
import { ShowActions, useCan } from '/src/lib';
import { shopStore } from '/src/admin/stores';

const canUpdate = useCan('shop.update');
const showActions = new ShowActions(shopStore.api);
const {
  is,
  item,
  show,
  toEdit
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
