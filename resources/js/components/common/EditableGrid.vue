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
        <tr v-for="(row, rowIndex) in localRows" :key="rowIndex">
          <td class="min-width" v-for="column in columns" :key="column.key">
            <div class="input-style-1">
              <component
                v-if="column.editor"
                :is="column.editor"
                v-bind="column.editorProps || {}"
                v-model="localRows[rowIndex][column.key]"
                @update:modelValue="emitRowsIfNew"
              />
              <template v-else>
                <input
                  v-if="showInput(row, column, rowIndex)"
                  v-model="localRows[rowIndex][column.key]"
                  :type="column.type || 'text'"
                  step="0.01"
                  :class="inputClass(column.key)"
                  :placeholder="column.placeholder || ''"
                  @focus="setEditing(rowIndex, column.key)"
                  @blur="handleBlur"
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
    <button v-if="showAdd" class="btn btn-sm btn-primary" @click="addRow">
      Add Row
    </button>
    <button v-if="showSave" class="btn btn-sm btn-success" @click="save">
      Save
    </button>
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
    showSave: { type: Boolean, default: false },
  },
  emits: ["update:modelValue", "delete", "open"],
  data() {
    return {
      localRows: JSON.parse(JSON.stringify(this.modelValue)), // deep clone
      editingCell: null,
    };
  },
  watch: {
    modelValue: {
      deep: true,
      handler(newVal) {
        // Keep local copy in sync with parent
        this.localRows = JSON.parse(JSON.stringify(newVal));
      },
    },
  },
  methods: {
    showInput(row, column, rowIndex) {
      return (this.isEditing(rowIndex, column.key) || !row[column.key] || column.type === 'checkbox') && !column.readonly;
    },
    inputClass(colKey) {
      const type = this.columns.find((col) => col.key === colKey)?.type || "text";
      return ["text", "number", "date"].includes(type) ? "form-control" : "";
    },
    emitRowsIfNew() {
      if (JSON.stringify(this.localRows) !== JSON.stringify(this.modelValue)) {
        this.emitRows();
      }
    },
    emitRows() {
      this.$emit("update:modelValue", JSON.parse(JSON.stringify(this.localRows)));
    },
    addRow() {
      const newRow = {};
      this.columns.forEach((col) => {
        newRow[col.key] = col.default || "";
      });
      this.localRows.push(newRow);
      this.emitRows();
    },
    removeRow(index) {
      const deleted_val = this.localRows[index];
      this.localRows.splice(index, 1);
      this.emitRows();
      this.$emit("delete", deleted_val);
    },
    isEditing(rowIndex, key) {
      return this.editingCell?.rowIndex === rowIndex && this.editingCell?.key === key;
    },
    setEditing(rowIndex, key) {
      this.editingCell = { rowIndex, key };
    },
    handleBlur() {
      this.clearEditing();
      this.emitRows();
    },
    clearEditing() {
      this.editingCell = null;
    },
    fireOpen(row) {
      this.$emit("open", row);
    },
    save() {
      this.$emit("save");
    },
  },
};
</script>