<template>
  <el-dialog v-model="is.show" title="修改密码" width="480" draggable align-center>
    <el-form
      :class="is.showValidateError ? '' : 'hide-validate-error'"
      ref="formRef"
      :rules="formRules"
      :model="item"
      label-width="100px"
      @submit.prevent="save"
    >
      <input type="submit" v-show="false">

      <el-form-item label="旧密码" prop="old_password">
        <el-input
          type="password"
          v-model="item.old_password"
          placeholder="请输入6位以上的密码"
          show-password
        ></el-input>
      </el-form-item>

      <el-form-item label="新密码" prop="password">
        <el-input
          type="password"
          v-model="item.password"
          placeholder="请输入6位以上的密码"
          show-password
        ></el-input>
      </el-form-item>

      <el-form-item label="确认密码" prop="password_confirmation">
        <el-input
          type="password"
          v-model="item.password_confirmation"
          placeholder="请输入6位以上的密码"
          show-password
        ></el-input>
      </el-form-item>

    </el-form>

    <template #footer>
      <el-button @click="reset">重置</el-button>
      <el-button type="primary" @click="save" v-loading="is.submitting">确定</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { http, refKit, success } from '/src/lib';

const is = reactive({
  show: false,
  submitting: false,
  showValidateError: true,
});
const item = reactive({
  old_password: '',
  password: '',
  password_confirmation: ''
});
const formRef = ref();
const formRules = {
  old_password: {
    required: true,
    min: 6,
    message: '请输入 6 位以上的旧密码',
    trigger: ['blur', 'change']
  },
  password: {
    required: true,
    min: 6,
    message: '请输入 6 位以上的密码',
    trigger: ['blur', 'change']
  },
  password_confirmation: {
    required: true,
    validator: (rule, value, callback) => {
      if (!value || value !== item.password) {
        return callback(new Error('两次输入密码不一致'));
      }
      return callback();
    },
    trigger: ['blur', 'change']
  }
};

const reset = () => {
  is.showValidateError = false;
  refKit.empty(item);
  setTimeout(() => {
    formRef.value.clearValidate();
    is.showValidateError = true;
  }, 200);
};

const save = () => {
  formRef.value.validate().then(() => {
    http.patch('/admin/account/ch-password', item).then(resp => {
      success('修改密码成功');
      is.show = false;
      reset();
    });
  });
};

defineExpose({
  show: () => {
    is.show = true;
  }
});
</script>
