<template>
  <div>
    <table class="table">
      <thead>
        <tr>
          <th v-for="column in columns" :key="column.key">{{ column.label }}</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, index) in modelValue" :key="index">
          <td v-for="column in columns" :key="column.key">
            <input
              v-model="row[column.key]"
              :type="column.type || 'text'"
              class="form-control"
              :placeholder="column.placeholder || ''"
            />
          </td>
          <td>
            <button class="btn btn-danger btn-sm" @click="removeRow(index)">×</button>
          </td>
        </tr>
      </tbody>
    </table>
    <button class="btn btn-sm btn-primary" @click="addRow">Add Row</button>
  </div>
</template>

<script>
export default {
  name: "EditableGrid",
  props: {
    modelValue: { type: Array, required: true },
    columns: { type: Array, required: true },
  },
  emits: ["update:modelValue"],
  methods: {
    addRow() {
      const newRow = {};
      this.columns.forEach((col) => {
        newRow[col.key] = col.default || "";
      });
      this.$emit("update:modelValue", [...this.modelValue, newRow]);
    },
    removeRow(index) {
      const updated = [...this.modelValue];
      updated.splice(index, 1);
      this.$emit("update:modelValue", updated);
    },
  },
};
</script>
