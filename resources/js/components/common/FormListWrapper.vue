<template>
  <div class="dashboard-module">
    <div class="card">
      <div class="card-body">
        <component
          v-if="selectedId !== null"
          :is="formComponent"
          :[idPropName]="selectedId"
          @update:modelValue="upsertItem"
          @delete="deleteItem"
          @close="clearSelection"
        />
        <component
          v-else
          :is="listComponent"
          v-model="items"
          @open="selectItem"
          @delete="deleteItem"
          :per-page="perPage"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FormListWrapper",
  props: {
    idParam: [Number, String],
    formComponent: Object,
    listComponent: Object,
    perPage: { type: Number, default: 25 },
    modelValue: Array,
    baseRoute: String,
    idPropName: {
      type: String,
      default: "id"
    }
  },
  emits: ["update:modelValue"],
  data() {
    return {
      selectedId: null,
    };
  },
  computed: {
    items: {
      get() { return this.modelValue },
      set(val) { this.$emit('update:modelValue', val); }
    },
  },
  watch: {
    idParam: {
      immediate: true,
      handler(val) {
        this.selectedId = val ? Number(val) : null;
      }
    }
  },
  methods: {
    clearSelection() {
      this.$router.push({ path: this.baseRoute });
    },
    selectItem(id) {
      this.$router.push({ path: `${this.baseRoute}/${id}` });
    },
    upsertItem(item) {
      const index = this.items.findIndex(i => i.id === item.id);
      if (index !== -1) this.$set(this.items, index, item);
      else this.items.push(item);
    },
    deleteItem(index) {
      this.items.splice(index, 1);
    }
  }
};
</script>
