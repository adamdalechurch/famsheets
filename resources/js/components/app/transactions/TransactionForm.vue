<template>
  <div>
    <h2>New Transaction</h2>
    <form @submit.prevent="submitTransaction">
      <div class="input-style-1">
        <label>Description</label>
        <DescriptionEdit v-model="transaction.description" />
      </div>
      <div class="input-style-1">
        <label>Total</label>
        <!-- <input v-model="transaction.total" type="number" required /> -->
        <!-- 2 decimal places: -->
        <input v-model.number="transaction.total" type="number" step="0.01" required />
      </div>
      <div class="input-style-1">
        <label>Transaction Date</label>
        <input v-model="transaction.transaction_date" type="date" required />
      </div>
      <div class="input-style-1">
        <label>Is Income</label>
        <input type="checkbox" v-model="transaction.is_income" />
      </div>
      <div class="input-style-1">
        <label>Recurring</label>
        <input type="checkbox" v-model="transaction.recurring" />
      </div>
   
      <TransactionItems v-model="transaction.items" />

      <br />

      <button type="submit">Submit Transaction</button>
    </form>
  </div>
</template>

<script>
import DescriptionEdit from '@/components/common/DescriptionEdit.vue';
import CategoryEdit from '@/components/common/CategoryEdit.vue';
import TransactionItems from './TransactionItems.vue';
import AutoCompleteInput from '@/components/common/AutoCompleteInput.vue';
import axios from 'axios';

export default {
  components: { DescriptionEdit, CategoryEdit, AutoCompleteInput, TransactionItems },
  data() {
    return {
      transaction: {
        user_id: 1, // Replace with auth info
        user_group_id: null,
        description: '',
        total: 0,
        is_income: false,
        recurring: false,
        transaction_schedule_id: null,
        income_source_id: null,
        transaction_date: new Date().toISOString().split('T')[0],
        items: [{ category_id: null, description: '', amount: 0 }],
      },
    };
  },
  methods: {
    addItem() {
      this.transaction.items.push({ category_id: null, description: '', amount: 0 });
    },
    removeItem(index) {
      this.transaction.items.splice(index, 1);
    },
    submitTransaction() {
      axios.post('/api/transactions', this.transaction).then(res => {
        alert('Transaction added!');
        // Reset form
        // .. abridged
      });
    },
  },
};
</script>
