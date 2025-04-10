<template>
  <AutoCompleteInput
    v-model="categoryId"
    :suggestions="suggestions"
    display-field="name"
    placeholder="Transaction Category"
  />
</template>

<script>
import AutoCompleteInput from './AutoCompleteInput.vue';
import axios from '@/util/axios';

export default {
  props: ['modelValue'],
  emits: ['update:modelValue'],
  components: { AutoCompleteInput },
  data() {
    return {
      categoryId: this.modelValue,
      suggestions: []
    };
  },
  watch: {
    categoryId(val) {
      this.$emit('update:modelValue', val);
    },
    modelValue(val) {
      this.categoryId = val;
    }
  },
  mounted() {
    axios.get('/api/categories').then(res => {
      this.suggestions = res.data; // expects { id, name }
    });
  }
};
</script>
