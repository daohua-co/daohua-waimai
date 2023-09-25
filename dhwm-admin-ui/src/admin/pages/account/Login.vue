<template>
  <div class="auth-wrapper">
    <div class="top">
      <a href="./"><img src="/src/assets/login-logo.png"/></a>
      <h1>管理员登录</h1>
    </div>
    <el-form
      size="large"
      label-position="top"
      class="auth-form"
      :rules="formRules"
      ref="formRef"
      :model="inputs"
      @submit.prevent="submitForm($refs.formRef)"
      v-cloak
    >
      <el-form-item prop="account">
        <el-input ref="accountRef" v-model="inputs.account" placeholder="请输入用户名/手机号/邮箱">
          <template #prepend>
            <i class="iconfont icon-user-fill"/>
          </template>
        </el-input>
      </el-form-item>

      <el-form-item prop="password">
        <el-input v-model="inputs.password" type="password" autocomplete="off" placeholder="请输入密码">
          <template #prepend>
            <i class="iconfont icon-password"/>
          </template>
        </el-input>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" class="submit" native-type="submit" v-loading="isSubmitting">登录</el-button>
      </el-form-item>
    </el-form>

    <Copyright/>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { http, responseMessage, setUserInfo, success, userInfo } from '/src/lib';
import Copyright from '/src/components/Copyright.vue';

const inputs = reactive({
  account: '',
  password: ''
});
const formRules = reactive({
  account: [
    {
      required: true,
      message: '请输入账号',
      trigger: ['blur', 'change']
    }
  ],
  password: [
    {
      required: true,
      message: '请输入密码',
      trigger: ['blur', 'change']
    },
    {
      min: 6,
      max: 20,
      message: '密码长度为 6~20 位',
      trigger: ['blur', 'change']
    }
  ]
});

const accountRef = ref();

const isSubmitting = ref(false);
const submitForm = formEl => {
  formEl.validate().then(() => {
    isSubmitting.value = true;
    http.post('/admin/auth/login', inputs).then((resp) => {
      if (!resp.success) {
        responseMessage(resp);
        return;
      }
      success('登录成功');
      window.location.reload(); // 刷新页面，重新加载数据
    }).finally(() => {
      isSubmitting.value = false;
    });
  });
};
</script>
