<style>
.avatar-wp {
  width: 72px;
  height: 72px;
}
</style>
<template>
  <el-dialog v-model="is.show" title="修改资料" width="560" draggable align-center>
    <el-skeleton :rows="10" :loading="is.loading" animated/>
    <el-form
      v-if="!is.loading"
      ref="formRef"
      :rules="formRules"
      :model="item"
      label-width="100px"
      @submit.prevent="save"
    >
      <input type="submit" v-show="false">

      <el-form-item label="头像" prop="avatar">
        <div class="avatar-wp">
          <UploadImage type="avatar" v-model="item.avatar"/>
        </div>
      </el-form-item>

      <el-form-item label="用户名" prop="name">
        {{ userInfo.name }}
      </el-form-item>

      <el-form-item label="姓名" prop="realname">
        <el-input v-model="item.realname"/>
      </el-form-item>

      <el-form-item label="手机号" prop="mobile">
        <el-input v-model="item.mobile" placeholder="请输入大陆手机号"></el-input>
      </el-form-item>

      <el-form-item label="邮箱" prop="email">
        <el-input v-model="item.email"/>
      </el-form-item>

    </el-form>

    <template #footer>
      <el-button type="primary" @click="save" v-loading="is.submitting">确定</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { http, refKit, setUserInfo, userInfo } from '/src/lib';
import { success } from '/src/lib/message';
import UploadImage from '/src/components/UploadImage.vue';

const is = reactive({
  show: false,
  submitting: false,
  loading: false
});
const item = reactive({});
const formRef = ref();
const formRules = {
  realname: {
    required: true,
    min: 2,
    message: '真实姓名名不能少于2个字符',
    trigger: ['blur', 'change']
  },
  mobile: {
    pattern: /^1[3-9]\d{9}$/,
    message: '手机号码格式不正确',
    trigger: ['blur', 'change']
  },
  email: {
    pattern: /^[\w\-.]+@[\w-]+(\.[\w-]+)+$/i,
    message: '邮箱格式不正确',
    trigger: ['blur', 'change']
  }
};

const reset = () => {
  refKit.empty(item);
};

const save = () => {
  formRef.value.validate().then(() => {
    http.patch('/admin/account/ch-profile', item).then(resp => {
      success('修改成功');
      is.show = false;
      setUserInfo(item);
    });
  });
};

defineExpose({
  show: () => {
    Object.assign(item, {
      avatar: userInfo.avatar,
      realname: userInfo.realname,
      email: userInfo.email,
      mobile: userInfo.mobile
    });
    is.show = true;
  }
});
</script>
