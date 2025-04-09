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
        <tr v-for="(row, rowIndex) in modelValue" :key="rowIndex">
          <td v-for="column in columns" :key="column.key">
            <div class="input-style-1">
              <component
                v-if="column.editor"
                :is="column.editor"
                v-model="row[column.key]"
              />
              <input
                v-else
                v-model="row[column.key]"
                :type="column.type || 'text'"
                class="form-control"
                :placeholder="column.placeholder || ''"
              />
            </div>
          </td>
          <td>
            <button class="btn btn-danger btn-sm" @click="removeRow(rowIndex)">×</button>
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
    columns: { type: Array, required: true }, // column.editor can be a Vue component
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
