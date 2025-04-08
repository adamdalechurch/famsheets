<template>
  <AutoCompleteInput
    v-model="category"
    :suggestions="suggestions"
    placeholder="Transaction Category"
  />
</template>

<script>
import AutoCompleteInput from './AutoCompleteInput.vue';

export default {
  props: ['modelValue'],
  emits: ['update:modelValue'],
  components: { AutoCompleteInput },
  data() {
    return {
      category: this.modelValue,
      suggestions: [
        'Groceries', 'Gasoline/Fuel', 'Utilities', 'Entertainment',
        'Healthcare', 'Travel', 'Dining', 'Pets', 'Clothing'
      ]
    };
  },
  watch: {
    category(val) {
      this.$emit('update:modelValue', val);
    },
    modelValue(val) {
      this.category = val;
    }
  }
};
</script>
