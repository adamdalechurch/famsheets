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
import EditableGrid from '@/components/common/EditableGrid.vue';
import CategoryEdit from '@/components/common/CategoryEdit.vue';

export default {
  name: "TransactionItems",
  components: { EditableGrid, CategoryEdit },
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
          editor: CategoryEdit 
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
