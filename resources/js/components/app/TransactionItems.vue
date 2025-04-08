<template>
  <div>
    <h4>Transaction Items</h4>
    <EditableGrid
      v-model="items"
      :columns="columns"
    />
  </div>
</template>

<script>
import EditableGrid from './EditableGrid.vue';

export default {
  name: "TransactionItems",
  components: { EditableGrid },
  props: {
    modelValue: Array,
  },
  emits: ["update:modelValue"],
  computed: {
    items: {
      get() {
        return this.modelValue;
      },
      set(val) {
        this.$emit("update:modelValue", val);
      },
    },
    columns() {
      return [
        {
          label: "Category ID",
          key: "category_id",
          type: "number",
          placeholder: "Category ID",
        },
        {
          label: "Description",
          key: "description",
          type: "text",
          placeholder: "Item description",
        },
        {
          label: "Amount",
          key: "amount",
          type: "number",
          placeholder: "0.00",
        },
      ];
    },
  },
};
</script>
