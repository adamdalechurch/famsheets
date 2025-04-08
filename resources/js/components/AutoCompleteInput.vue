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
      <li v-for="item in filteredSuggestions" :key="item" @click="select(item)">
        {{ item }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    modelValue: String,
    suggestions: Array,
    placeholder: String
  },
  emits: ['update:modelValue'],
  data() {
    return {
      input: this.modelValue || '',
      showSuggestions: false
    };
  },
  computed: {
    filteredSuggestions() {
      return this.suggestions.filter(item =>
        item.toLowerCase().includes(this.input.toLowerCase())
      );
    }
  },
  watch: {
    modelValue(newVal) {
      this.input = newVal;
    }
  },
  methods: {
    onInput() {
      this.$emit('update:modelValue', this.input);
    },
    select(item) {
      this.input = item;
      this.$emit('update:modelValue', item);
      this.showSuggestions = false;
    }
  }
};
</script>

<style scoped>
ul {
  position: absolute;
  background: white;
  border: 1px solid #ddd;
  max-height: 150px;
  overflow-y: auto;
  padding-left: 0;
  list-style: none;
  z-index: 10;
}
li {
  padding: 5px 10px;
  cursor: pointer;
}
li:hover {
  background-color: #f2f2f2;
}
</style>
