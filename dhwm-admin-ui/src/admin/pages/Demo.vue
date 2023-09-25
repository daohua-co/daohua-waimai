<template>
<span>{{ album }}</span>
</template>

<script setup>
import { ref, watch } from 'vue';
const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
});
const album = ref([]);
watch(() => props.modelValue, function() {
  // console.log('changed...', arguments);
  album.value = props.modelValue.map(url => {
    return {url};
  });
}, {deep: true, immediate: true});
</script>
