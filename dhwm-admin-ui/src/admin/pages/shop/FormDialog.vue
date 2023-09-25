<template>
  <el-dialog
    v-model="is.show"
    :title="`${is.edit ? '编辑' : '新建'}门店`"
    @close="onHide()"
    width="580"
    draggable
    align-center
  >
    <el-skeleton :rows="5" :loading="is.loading" animated/>
    <el-form
      v-if="!is.loading"
      ref="formRef"
      :model="item"
      :rules="formRules"
      label-width="120px"
      @submit.prevent="save()"
      :show-message="is.showValidateError"
    >
      <input type="submit" v-show="false"/>

      <el-form-item label="经营区域" prop="area_id">
        <template v-if="is.edit">{{ item.area_names?.join(' > ') }}</template>
        <el-cascader
          v-model="item.area_id"
          class="w-full"
          placeholder="选择区域"
          :options="areaStore.listData.items"
          :props="{emitPath: false, expandTrigger: 'hover', label: 'name', value: 'id', disabled: 'joined'}"
          filterable
          clearable
          v-else
        >
          <template #default="{ data }">
            <div>{{ data.short_name || data.name }}</div>
          </template>
        </el-cascader>
      </el-form-item>

      <el-form-item label="经营地址详情" prop="address_detail">
        <el-input v-model="item.address_detail" placeholder="括省/市/区之后的部分"/>
      </el-form-item>

      <el-form-item label="联系手机" prop="contact_mobile">
        <el-input v-model="item.contact_mobile" :placeholder="is.edit ? '' : '将同时作为店主登录账号'"/>
      </el-form-item>

      <el-form-item label="经营模式" prop="operation_mode">
        <el-radio-group v-model="item.operation_mode">
          <el-radio label="zy">自营</el-radio>
          <el-radio label="jm">加盟</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="经营实体" prop="operation_entity">
        <el-input v-model="item.operation_entity" placeholder="经营企业名称"/>
      </el-form-item>

      <el-form-item label="营业执照" prop="business_license_img">
        <div class="w-20 h-20">
          <UploadImage v-model="item.business_license_img"/>
        </div>
      </el-form-item>

      <el-form-item label="门店状态" prop="status">
        <el-radio-group v-model="item.status">
          <el-radio label="pre">筹备中</el-radio>
          <el-radio label="on">营业中</el-radio>
          <el-radio label="off">已关停</el-radio>
        </el-radio-group>
      </el-form-item>
    </el-form>
    <template #footer>
      <el-button @click="formActions.reset()">重置</el-button>
      <el-button type="primary" @click="save()" v-loading="is.submitting">确定</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { FormActions } from '/src/lib';
import { areaStore, shopStore } from '/src/admin/stores';
import UploadImage from '/src/components/UploadImage.vue';

const formRef = ref();
const formRules = {
  area_id: {
    required: true,
    message: '请选择区域',
    trigger: ['blur', 'change']
  },
  contact_mobile: [
    {
      required: true,
      message: '请填写联系手机号',
      trigger: ['blur', 'change']
    },
    {
      pattern: /^1[3-9]\d{9}$/,
      message: '手机号码格式不正确',
      trigger: ['blur', 'change']
    }
  ],
  address_detail: {
    required: true,
    message: '请填写地址详情',
    trigger: ['blur', 'change']
  },
  operation_entity: {
    required: true,
    message: '请填写经营实体',
    trigger: ['blur', 'change']
  }
};

const formActions = new FormActions(shopStore, formRef, {
  id: null,
  address_detail: '',
  contact_mobile: '',
  operation_mode: '',
  operation_entity: '',
  business_license_img: '',
  status: 'pre',
  area_names: [],
});
const {
  is,
  item,
  onHide,
  create,
  edit,
} = formActions;

areaStore.checkLoaded();
const save = () => {
  formActions.save().then(() => {
    areaStore.reload();
  });
};
defineExpose({create, edit});
</script>
