<template>
  <div>
    <button @click="close"><<</button>
    <h2>Budget</h2>
    <form @submit.prevent="submitBudget">
      <div class="input-style-1">
        <label>Name</label>
        <input v-model="budget.name" type="text" required />
      </div>
      <div class="input-style-1">
        <label>Start Date</label>
        <input v-model="budget.start_date" type="date" required />
      </div>
      <div class="input-style-1">
        <label>End Date</label>
        <input v-model="budget.end_date" type="date" required />
      </div>

      <BudgetItems v-model="budget.budget_items" />
      <br />

      <button type="submit">Submit Budget</button>
    </form>
  </div>
</template>

<script>
import BudgetItems from './BudgetItems.vue';
import axios from 'axios';

const BUDGET_BLANK = {
  name: '',
  start_date: new Date().toISOString().split('T')[0],
  end_date: '',
  budget_items: [],
};

export default {
  components: { BudgetItems },
  props: {
    budget_id: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      budget: BUDGET_BLANK,
    };
  },
  watch: {
    budget_id: {
      immediate: true,
      handler(val) {
        if (val) {
          axios.get(`/api/budgets/${val}`).then(res => {
            this.budget = res.data;
          });
        } else {
          this.budget = BUDGET_BLANK;
        }
      },
    },
  },
  methods: {
    submitBudget() {
      if (this.budget_id) {
        axios.put(`/api/budgets/${this.budget_id}`, this.budget).then(res => {
          alert('Budget updated!');
          this.$emit('update:modelValue', res.data);
        });
      } else {
        axios.post('/api/budgets', this.budget).then(res => {
          alert('Budget added!');
          this.$emit('update:modelValue', res.data);
        });
      }
    },
    close() {
      this.$emit('close');
    },
  }
};
</script>
