<template>
  <!-- <AutoCompleteInput
    v-model="description"
    :suggestions="suggestions"
    placeholder="Transaction Description"
  /> -->
<input v-model="description" placeholder="Transaction Description" />
</template>

<script>
import AutoCompleteInput from './AutoCompleteInput.vue';

export default {
  props: ['modelValue'],
  emits: ['update:modelValue'],
  components: { AutoCompleteInput },
  data() {
    return {
      suggestions: ['Gas', 'Groceries', 'Internet', 'Therapy', 'Hulu', 'Rent'],
      description: this.modelValue
    };
  },
  watch: {
    description(val) {
      this.$emit('update:modelValue', val);
    },
    modelValue(val) {
      this.description = val;
    }
  }
};
</script>
