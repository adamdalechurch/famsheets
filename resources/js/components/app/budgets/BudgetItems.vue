<template>
  <div>
    <h4>Budget Items</h4>
    <EditableGrid
      v-if="categories?.length > 0"
      v-model="items"
      :columns="columns"
    />
  </div>
</template>

<script>
import EditableGrid from '@/components/common/EditableGrid.vue';
import CategoryEdit from '@/components/common/CategoryEdit.vue';
import axios from 'axios';

export default {
  name: "BudgetItems",
  components: { EditableGrid, CategoryEdit },
  props: {
    modelValue: Array,
  },
  emits: ["update:modelValue"],
  data() {
    return {
      columns: [
        {
          label: "Category ID",
          key: "category_id",
          type: "number",
          placeholder: "Category ID",
          editor: CategoryEdit,
          editorProps: {
            modelValueKey: 'category_id',
            suggestions: []
          },
        },
        {
          label: "Amount",
          key: "amount",
          type: "number",
          placeholder: "0.00",
        },
      ],
    };
  },
  computed: {
    items: {
      get() {
        return this.modelValue;
      },
      set(val) {
        this.$emit("update:modelValue", val);
      },
    },
    categories: {
      get() {
        return this.columns.find(col => col.key === 'category_id').editorProps.suggestions;
      },
      set(val) {
        this.columns.find(col => col.key === 'category_id').editorProps.suggestions = val;
      },
    },
  },
  mounted() {
    this.loadCategories();
  },
  methods: {
    loadCategories() {
      axios.get('/api/categories').then((res) => {
        this.categories = res.data;
      });
    },
  },
};
</script>
