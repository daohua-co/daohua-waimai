<template>
    <el-dialog
        v-model="is.show"
        :title="(is.edit ? '编辑' : '新建') + '标签'"
        align-center
        draggable
        width="420px"
        @closed="onHide()"
    >
        <el-skeleton :loading="is.loading" :rows="1" animated/>
        <el-form
            v-if="!is.loading"
            ref="formRef"
            :model="item"
            :rules="formRules"
            label-width="100px"
            @submit.prevent="save()"
            :class="is.showValidateError ? '' : 'hide-validate-error'"
        >
            <input v-show="false" type="submit"/>
            <el-form-item label="标签" prop="title">
                <el-input v-model="item.title" clearable placeholder=""/>
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="reset()">重置</el-button>
            <el-button v-loading="is.submitting" type="primary" @click="save()">确定</el-button>
        </template>
    </el-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { ElMessageBox } from 'element-plus';
import { FormActions, success } from '/src/lib';
import { contentTagStore } from '/src/admin/stores';

const formRef = ref();
const formRules = {
  title: {
    required: true,
    message: '请输入标签名',
    trigger: ['blur', 'change']
  }
};

class TagFormActions extends FormActions {
  save() {
    return super.save().then(resp => {
      if (!this.is.edit) {
        return;
      }
      // 编辑成功提示后退出编辑窗口
      success('编辑成功');
      ElMessageBox.close();
      this.is.show = false;
    });
  }
}

const formActions = new TagFormActions(contentTagStore, formRef);
const {
  is,
  item,
  create,
  edit,
  reset,
  save,
  onHide
} = formActions;

defineExpose({
  create,
  edit
});
</script>
