<template>
  <div>
    <h2>Transactions</h2>
    <form @submit.prevent="submitTransaction">
      <div class="input-style-1">
        <label>Description</label>
        <input v-model="transaction.description" placeholder="Description" required />
      </div>
      <div class="input-style-1">
        <label>Amount</label>
        <input v-model="transaction.total" type="number" placeholder="Amount" required />
      </div>
      <div class="input-style-1">
        <label>Transaction Date</label>
        <input v-model="transaction.transaction_date" type="date" required />
      </div>
      <button type="submit" class="btn btn-primary mt-3">Add Transaction</button>
    </form>

    <div class="card-style mb-30">
      <h6 class="mb-25">Transactions List</h6>
      <ul>
        <li v-for="tx in transactions" :key="tx.id">
          {{ tx.description }} - ${{ tx.total }} on {{ tx.transaction_date }}
        </li>
      </ul>
    </div>
  </div>
</template>
<script>
import axios from 'axios';

export default {
  data() {
    return {
      transactions: [],
      transaction: { description: '', total: 0, transaction_date: '' },
    };
  },
  methods: {
    fetchTransactions() {
      axios.get('/api/transactions').then(response => {
        this.transactions = response.data;
      });
    },
    submitTransaction() {
      axios.post('/api/transactions', this.transaction).then(() => {
        this.fetchTransactions();
        this.transaction = { description: '', total: 0, transaction_date: '' };
      });
    },
  },
  mounted() {
    this.fetchTransactions();
  },
};
</script>
