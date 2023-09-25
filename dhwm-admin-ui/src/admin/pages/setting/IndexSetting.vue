<template>
  <div class="box p-6 mb-14">
    <el-skeleton :rows="8" :loading="data.loading" animated/>
    <el-form
      v-if="!data.loading"
      ref="formRef"
      :model="data.items"
      label-width="100px"
      @submit.prevent="save"
    >
      <h2 class="form-title">{{ data.title }}</h2>
      <template v-for="item in data.items" :key="item.id">
        <el-form-item :label="item.title" :prop="item.key">
          <el-input
            v-model="item.value"
            :placeholder="item.tips"
            type="textarea"
            :rows="3"
            v-if="item.input_type === 'textarea'"
          />
          <el-switch
            v-model="item.value"
            active-value="1"
            inactive-value="0"
            v-else-if="item.input_type === 'switch'"
          />
          <el-input v-model="item.value" placeholder="" v-else/>
        </el-form-item>
      </template>
      <el-form-item>
        <el-button type="primary" native-type="submit" v-loading="data.submitting">保存</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script setup>
import { onActivated, onDeactivated, reactive, watch } from 'vue';
import { http, message, routeTo } from '/src/lib';

const data = reactive({
  title: '',
  items: [],
  loading: false,
  submitting: false
});
const props = defineProps(['group']);
const groups = {
  system: '系统设置',
  sms: '短信设置',
  contact: '联系信息设置'
};

const save = () => {
  data.submitting = true;
  const settings = {};
  for (const item of data.items) {
    settings[item.key] = item.value;
  }
  http.post('admin/settings', {settings}).then(resp => {
    message.success('操作成功');
  }).finally(() => {
    data.submitting = false;
  });
};

const load = () => {
  if (!groups[props.group]) {
    return routeTo('/notfound');
  }
  data.title = groups[props.group];
  // 加载数据
  data.items = [];
  data.loading = true;
  http.get(`admin/settings?group=${props.group}`).then(resp => {
    data.items = resp.items;
  }).finally(() => {
    data.loading = false;
  });
};

watch(() => props.group, () => {
  load();
}, {immediate: true});

onActivated(() => {
  load();
});
onDeactivated(() => {
  data.items = [];
});

</script>
