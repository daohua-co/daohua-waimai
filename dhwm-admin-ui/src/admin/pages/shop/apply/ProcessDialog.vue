<template>
  <el-dialog
    v-model="is.show"
    title="加盟审核"
    align-center
    draggable
    width="560px"
  >
    <el-skeleton :rows="5" :loading="is.loading" animated/>
    <el-form
      :model="form"
      label-width="100px"
      @submit.prevent="save()"
      ref="formRef"
      v-if="!is.loading"
    >
      <input v-show="false" type="submit"/>
      <el-form-item label="申请人">{{ item.name }}</el-form-item>
      <el-form-item label="申请状态">
        <el-tag :type="statusType(item.status)" size="small" effect="plain" round>{{ item.status_text }}</el-tag>
      </el-form-item>
      <el-form-item label="审核结果" prop="result">
        <el-radio-group v-model="form.result">
          <el-radio label="accept">通过</el-radio>
          <el-radio label="reject">驳回<span class="tips">(重新提交)</span></el-radio>
          <el-radio label="locked">锁定<span class="tips">(禁止再提交)</span></el-radio>
        </el-radio-group>
      </el-form-item>
      <el-form-item label="备注" prop="remark">
        <el-input type="textarea" v-model="form.remark" clearable placeholder=""/>
      </el-form-item>
    </el-form>
    <template #footer>
      <el-button v-loading="is.submitting" type="primary" @click="save()">提交</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { reactive } from 'vue';
import { shopApplyStore, shopStore } from '/src/admin/stores';
import { statusType } from '/src/admin/helpers/shop-apply';
import { ShowActions, success } from '/src/lib';

const form = reactive({});
const actions = new ShowActions(shopApplyStore.api);
const {
  is,
  item,
  show
} = actions;

const save = () => {
  is.submitting = true;
  shopApplyStore.api.patch(item.id, form).then(resp => {
    shopApplyStore.reload();
    shopStore.reload();
    success('审核成功');
    is.show = false;
  }).finally(() => {
    is.submitting = false;
  });
};

defineExpose({
  show(id) {
    actions.show(id).then(resp => {
      const apply = resp.item;
      const respProcess = apply.processes.pop();
      form.result = respProcess?.result || 'accept';
      form.remark = '';
    });
  }
});
</script>
