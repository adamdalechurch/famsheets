<template>
  <div class="dashboard-module">
    <div class="card"> 
      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            <BudgetForm 
              v-if="budget_id !== null"
              @update:modelValue="upsertBudget"
              @delete="deleteBudget"
              @close="backToList"
              :budget_id="budget_id"
            />
            <BudgetsList
              v-if="budget_id === null"
              @open="editBudget"
              @delete="deleteBudget"
              v-model="budgets"
              :per-page="perPage"
            />
          </div>
        </div>   
      </div>
    </div>
  </div>
</template>

<script>
import BudgetsList from '@/components/app/budgets/BudgetsList.vue';
import BudgetForm from '@/components/app/budgets/BudgetForm.vue';

export default {
  name: 'Budgets',
  components: { BudgetsList, BudgetForm },
  data() {
    return {
      budgets: [],
      budget_id: null,
    };
  },
  mounted() {
    this.getBudgetIdFromRouter();
  },
  props: {
    perPage: {
      type: Number,
      default: 25,
    },
  },
  watch: {
    $route(to, from) {
      this.getBudgetIdFromRouter();
      if (this.budget_id === null) {
        this.updateBudgets(this.budgets);
      }
    }
  },
  methods: {
    backToList() {
      this.$router.push({ path: '/app/budgets' });
    },
    getBudgetIdFromRouter() {
      this.budget_id = this.$route.params.budget_id || null;
    },
    editBudget(id) {
      this.$router.push({ path: `/app/budgets/${id}` });
    },
    upsertBudget(budget) {
      const index = this.budgets.findIndex(b => b.id === budget.id);
      if (index !== -1) {
        this.$set(this.budgets, index, budget);
      } else {
        this.budgets.push(budget);
      }
    },
    deleteBudget(index) {
      this.budgets = this.budgets.filter((_, i) => i !== index);
    },
    updateBudgets(budgets) {
      this.budgets = budgets;
    },
  }
};
</script>
