<template>
  <AutoCompleteInput
    v-model="categoryId"
    :suggestions="localSuggestions"
    display-field="name"
    placeholder="Transaction Category"
  />
</template>

<script>
import AutoCompleteInput from './AutoCompleteInput.vue';
import axios from '@/util/axios';

export default {
  props: ['modelValue', 'modelValueKey', 'suggestions'],
  emits: ['update:modelValue'],
  components: { AutoCompleteInput },
  data() {
    return {
      categoryId: this.modelValue,
      localSuggestions: this.suggestions || []
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
    if (!this.suggestions) {
      // Load categories if not provided
      axios.get('/api/categories').then((res) => {
        this.localSuggestions = res.data; // expects { id, name }
      });
    }
  }
};
</script>
