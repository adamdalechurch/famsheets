<template>
  <div>
    <h2>All Budgets</h2>
    <EditableGrid
      v-model="formattedBudgets"
      :columns="columns"
      @open="openBudget"
      @delete="deleteBudget"
      show-open
    />
    <div class="pagination mb-3">
      <button class="btn btn-secondary" @click="firstPage" :disabled="pagination.page <= 1"><<</button>
      <button class="btn btn-secondary" @click="prevPage" :disabled="pagination.page <= 1"><</button>
      <span>Page {{ pagination.page }} of {{ totalPages }}</span>
      <button class="btn btn-secondary" @click="nextPage" :disabled="pagination.page >= totalPages">></button>
      <button class="btn btn-secondary" @click="lastPage" :disabled="pagination.page >= totalPages">>></button>
    </div>
    <button class="btn btn-success" @click="saveChanges">Save Changes</button>
  </div>
</template>

<script>
import EditableGrid from '@/components/common/EditableGrid.vue';
import axios from 'axios';

export default {
  name: 'BudgetsList',
  components: { EditableGrid },
  data() {
    return {
      localBudgets: [],
      columns: [
        { label: 'Name', key: 'name' },
        { label: 'Start Date', key: 'start_date', type: 'date' },
        { label: 'End Date', key: 'end_date', type: 'date' },
      ],
      pagination: {
        page: 1,
        paginate: true,
      },
      totalPages: 1,
    };
  },
  props: {
    modelValue: Array,
    perPage: {
      type: Number,
      default: 25,
    },
  },
  watch: {
    modelValue: {
      handler(newVal) {
        this.localBudgets = [...newVal];
        this.$emit('update:modelValue', this.localBudgets);
      },
      immediate: true,
    },
  },
  computed: {
    formattedBudgets: {
      get() {
        return this.localBudgets;
      },
      set(val) {
        this.localBudgets = val;
      },
    },
  },
  created() {
    this.loadBudgets();
  },
  methods: {
    loadBudgets() {
      axios.get('/api/budgets', {
        params: {
          paginate: this.pagination.paginate,
          per_page: this.perPage,
          page: this.pagination.page,
        },
      }).then(res => {
        this.localBudgets = this.pagination.paginate ? res.data.data : res.data;
        this.totalPages = res.data.last_page || 1;
        this.$emit('update:modelValue', this.localBudgets);
      });
    },
    saveChanges() {
      axios.post('/api/budgets/bulk-update', { budgets: this.localBudgets }).then(() => {
        alert('Changes saved!');
      }).catch((err) => {
        console.error(err);
        alert('Failed to save changes.');
      });
    },
    openBudget(budget) {
      this.$emit('open', budget.id);
    },
    deleteBudget(budget) {
      axios.delete(`/api/budgets/${budget.id}`).then(() => {
        this.loadBudgets();
      }).catch(err => {
        console.error(err);
        alert('Failed to delete budget.');
      });
    },
    firstPage() {
      this.pagination.page = 1;
      this.loadBudgets();
    },
    prevPage() {
      if (this.pagination.page > 1) {
        this.pagination.page--;
        this.loadBudgets();
      }
    },
    nextPage() {
      if (this.pagination.page < this.totalPages) {
        this.pagination.page++;
        this.loadBudgets();
      }
    },
    lastPage() {
      this.pagination.page = this.totalPages;
      this.loadBudgets();
    },
  },
};
</script>
