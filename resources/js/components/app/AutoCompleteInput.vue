<template>
  <div>
    <input
      type="text"
      v-model="input"
      @input="onInput"
      @focus="showSuggestions = true"
      @blur="() => setTimeout(() => (showSuggestions = false), 100)"
      :placeholder="placeholder"
    />
    <ul v-if="showSuggestions && filteredSuggestions.length">
      <li
        v-for="item in filteredSuggestions"
        :key="item.id"
        @click="select(item)"
      >
        {{ item[displayField] }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: [String, Number],
    suggestions: Array,
    placeholder: String,
    displayField: {
      type: String,
      default: 'name'
    }
  },
  emits: ['update:modelValue'],
  data() {
    return {
      input: '',
      showSuggestions: false
    };
  },
  computed: {
    filteredSuggestions() {
      return this.suggestions.filter(item =>
        item[this.displayField]
          .toLowerCase()
          .includes(this.input.toLowerCase())
      );
    }
  },
  watch: {
    modelValue: {
      immediate: true,
      handler(val) {
        const match = this.suggestions.find(s => s.id === val);
        this.input = match ? match[this.displayField] : '';
      }
    },
    suggestions: {
      immediate: true,
      handler() {
        const match = this.suggestions.find(s => s.id === this.modelValue);
        this.input = match ? match[this.displayField] : '';
      }
    }
  },
  methods: {
    onInput() {
      this.input = this.input; // just trigger filtering, don't update modelValue
    },
    select(item) {
      this.input = item[this.displayField];
      this.$emit('update:modelValue', item.id);
      this.showSuggestions = false;
    }
  }
};
</script>
