<template>
  <div class="table-wrapper table-responsive mb-4">
    <table class="table">
      <thead>
        <tr>
          <th v-for="column in columns" :key="column.key">{{ column.label }}</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, rowIndex) in modelValue" :key="rowIndex">
          <td class="min-width" v-for="column in columns" :key="column.key">
            <div class="input-style-1">
              <component
                v-if="column.editor"
                :is="column.editor"
                v-model="row[column.key]"
              />
              <template v-else>
                <input
                  v-if="isEditing(rowIndex, column.key) || !row[column.key] || column.type === 'checkbox'"
                  v-model="row[column.key]"
                  :type="column.type || 'text'"
                  step="0.01"
                  :class="inputClass(column.key)"
                  :placeholder="column.placeholder || ''"
                  @focus="setEditing(rowIndex, column.key)"
                  @blur="clearEditing"
                />
                <span
                  v-else
                  @click="setEditing(rowIndex, column.key)"
                  style="cursor: pointer;"
                >
                  {{ row[column.key] }}
                </span>
              </template>
            </div>
          </td>
          <td style="padding-top:0">
            <div class="action">
              <a class="text-danger h2" @click="removeRow(rowIndex)">
                <i class="lni lni-trash-can"></i>
              </a>
            </div>
          </td>
          <td v-if="showOpen" style="padding-top:0">
            <div class="action">
              <a class="text-primary h2" @click="fireOpen(row)">
                <i class="lni lni-eye"></i>
              </a>
            </div>
          </td> 
        </tr>
      </tbody>
    </table> 
    <button v-if="showAdd" class="btn btn-sm btn-primary" @click="addRow">Add Row</button>
  </div>
</template>
<script>
export default {
  name: "EditableGrid",
  props: {
    modelValue: { type: Array, required: true },
    columns: { type: Array, required: true },
    showOpen: { type: Boolean, default: false },
    showAdd: { type: Boolean, default: true },
  },
  emits: ["update:modelValue"],
  data() {
    return {
      editingCell: null,
    };
  },
  methods: {
    inputClass(colKey) {
      const type = this.columns.find((col) => col.key === colKey)?.type || "text";

      switch (type) {
        case "text":
          return "form-control";
        case "number":
          return "form-control";
        case "date":
          return "form-control";
        case "checkbox":
          return "";
        default:
          return "form-control";
      }
    },
    addRow() {
      const newRow = {};
      this.columns.forEach((col) => {
        newRow[col.key] = col.default || "";
      });
      this.$emit("update:modelValue", [...this.modelValue, newRow]);
    },
    removeRow(index) {
      let deleted_val = this.modelValue[index];
      const updated = [...this.modelValue];
      updated.splice(index, 1);
      this.$emit("update:modelValue", updated);
      this.$emit("delete", deleted_val);
    },
    isEditing(rowIndex, key) {
      return this.editingCell?.rowIndex === rowIndex && this.editingCell?.key === key;
    },
    setEditing(rowIndex, key) {
      this.editingCell = { rowIndex, key };
    },
    clearEditing() {
      this.editingCell = null;
    },
    fireOpen(row) {
      this.$emit("open", row);
    },
  },
};
</script>

