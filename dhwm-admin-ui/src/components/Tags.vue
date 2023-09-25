<template>
    <el-tag
        v-for="(tag, tagKey) in tags"
        :key="tag.title"
        class="mr-1"
        @close="onRemoveTag(tagKey)"
        closable
        effect="plain"
    >
        {{ tag.title }}
    </el-tag>
    <el-input
        v-if="tagInputVisible"
        ref="tagInputRef"
        v-model="tagInputValue"
        class="mr-1 w-24"
        size="small"
        @keyup.enter.stop="onTagInputConfirm"
        @keydown.enter="$event.preventDefault()"
        @blur="tagInputVisible=false"
        plain
    />
    <el-button v-else class="w-24 mr-1" size="small" @click="showTagInput" plain>
        + 添加标签
    </el-button>

    <div class="hot-tags w-full">
        热门标签：
        <template v-for="(hotTag, hotTagKey) in hotTags" :key="hotTag.id">
            <el-link class="mr-4" @click="addTagFromHot(hotTagKey)" type="primary" title="点击选择">
                {{ hotTag.title }}
            </el-link>
        </template>
    </div>
</template>

<script setup>
import { computed, nextTick, ref } from 'vue';
import { toast } from '../lib';

const props = defineProps(['modelValue', 'hotTags']);
const emit = defineEmits(['update:modelValue']);
const tags = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});

const tagInputValue = ref('');
const tagInputRef = ref();
const tagInputVisible = ref(false);
const showTagInput = () => {
  tagInputVisible.value = true;
  nextTick(() => {
    tagInputRef.value.input.focus();
  });
};
const onTagInputConfirm = () => {
  if (tagInputValue.value) {
    tags.value.push({
      title: tagInputValue.value.trim()
    });
  }
  tagInputVisible.value = false;
  tagInputValue.value = '';
};
const onRemoveTag = tagKey => {
  tags.value.splice(tagKey, 1);
};
const addTagFromHot = hotTagKey => {
  const hotTag = props.hotTags[hotTagKey];
  for (const tag of tags.value) {
    if (tag.title === hotTag.title) {
      toast('该标签已添加，不能重复添加');
      return;
    }
  }
  tags.value.push({
    id: hotTag.id,
    title: hotTag.title
  });
};
</script>

<style scoped>

</style>
