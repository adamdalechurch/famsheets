<template>
  <div>
    <h4>Budget Items</h4>
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
  name: "BudgetItems",
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
          label: "Planned Amount",
          key: "planned",
          type: "number",
          placeholder: "0.00",
        },
        {
          label: "Actual Amount",
          key: "actual",
          type: "number",
          placeholder: "0.00",
        },
      ];
    },
  },
};
</script>
