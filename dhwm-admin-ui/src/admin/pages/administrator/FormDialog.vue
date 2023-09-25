<template>
  <el-dialog
    v-model="is.show"
    :title="`${is.edit ? '编辑' : '新建'}人员`"
    @closed="onHide"
    width="640"
    draggable
    align-center
  >
    <el-skeleton :rows="10" :loading="is.loading" animated/>
    <el-form
      v-if="!is.loading"
      ref="formRef"
      :model="item"
      :rules="formRules"
      label-width="80px"
      :validate-on-rule-change="false"
      @submit.prevent="save"
      class="grid grid-cols-2 gap-x-4 gap-y-1"
    >
      <input type="submit" v-show="false">

      <el-form-item label="部门" prop="department_id" class="col-start-1">
        <el-tree-select
          v-model.number="item.department_id"
          class="w-80"
          placeholder="请选择"
          :data="adminDepartmentStore.listData.items"
          :props="{label: 'title', value: 'id'}"
          :render-after-expand="false"
          default-expand-all
          clearable
        />
      </el-form-item>

      <el-form-item label="角色" prop="role_ids">
        <el-select v-model="item.role_ids" placeholder="请选择" class="w-80" multiple>
          <template v-for="role in adminRoleStore.listData.items" :key="role.id">
            <el-option :label="role.title" :value="role.id"/>
          </template>
        </el-select>
      </el-form-item>

      <el-form-item label="用户名" prop="name">
        <el-input v-model="item.name" placeholder="以字母开头，小写字母或数字"/>
      </el-form-item>

      <el-form-item label="姓名" prop="realname">
        <el-input v-model="item.realname"/>
      </el-form-item>

      <el-form-item label="密码" prop="password">
        <el-input
          type="password"
          v-model="item.password"
          :placeholder="`6位以上${is.edit ? '，如果不修改请留空' : ''}`"
          show-password
        ></el-input>
      </el-form-item>

      <el-form-item label="手机号" prop="mobile">
        <el-input v-model="item.mobile" placeholder="请输入大陆手机号"></el-input>
      </el-form-item>

      <el-form-item label="确认密码" prop="password_confirmation">
        <el-input
          type="password"
          v-model="item.password_confirmation"
          :placeholder="`${is.edit ? '如果不修改请留空' : ''}`"
          show-password
        ></el-input>
      </el-form-item>

      <el-form-item label="邮箱" prop="email">
        <el-input v-model="item.email"/>
      </el-form-item>

      <el-form-item label="账号状态" prop="">

        <el-tag size="small" type="danger" effect="plain" round v-if="item.deleted_at">已删除</el-tag>
        <template v-else>
          <el-switch
            width="60"
            v-model="item.enabled"
            active-text="启用"
            inactive-text="停用"
            inline-prompt
          />
          <span class="tips">停用账号后不能再登录</span>
        </template>
      </el-form-item>

    </el-form>
    <template #footer>
      <el-button @click="reset">重置</el-button>
      <el-button type="primary" @click="save()" v-loading="is.submitting">确定</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { FormActions } from '/src/lib';
import { adminUserStore, adminRoleStore, adminDepartmentStore } from '/src/admin/stores';

const formRef = ref();

class UserFormStore extends FormActions {
  create() {
    adaptFormRules(false);
    return super.create();
  }

  edit(id) {
    adaptFormRules(true);
    return super.edit(id);
  }
}

const formActions = new UserFormStore(adminUserStore, formRef, {
  enabled: true,
  department_id: null,
  role_ids: []
});

const {
  is,
  item,
  save,
  reset,
  onHide,
  create,
  edit
} = formActions;

const formRules = reactive({
  name: [
    {
      required: true,
      min: 2,
      message: '用户名不能少于2个字符',
      trigger: ['blur', 'change']
    },
    {
      pattern: /^[a-z][0-9a-z]+$/i,
      message: '仅支持以字母开头，小写字母或数字',
      trigger: ['blur', 'change']
    }
  ],
  realname: [
    {
      required: true,
      min: 2,
      message: '真实姓名名不能少于2个字符',
      trigger: ['blur', 'change']
    }
  ],
  mobile: [
    {
      pattern: /^1[3-9]\d{9}$/,
      message: '手机号码格式不正确',
      trigger: ['blur', 'change']
    }
  ],
  email: [
    {
      pattern: /^[\w\-.]+@[\w-]+(\.[\w-]+)+$/i,
      message: '邮箱格式不正确',
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
      message: '密码不能少于6个字符',
      trigger: ['blur', 'change']
    }
  ],
  password_confirmation: [
    {
      required: true,
      message: '请输入确认密码',
      trigger: ['blur', 'change']
    },
    {
      validator: (rule, value, callback) => {
        if (!value) {
          return callback();
        }
        if (value !== item.password) {
          return callback(new Error('两次输入密码不一致'));
        } else {
          return callback();
        }
      },
      trigger: ['blur', 'change']
    }
  ]
});

const adaptFormRules = (isEdit) => {
  formRules.password[0].required = !isEdit;
  formRules.password_confirmation[0].required = !isEdit;
};

defineExpose({create, edit});
</script>
