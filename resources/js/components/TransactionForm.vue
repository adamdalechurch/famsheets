<template>
  <div>
    <h2>Transactions</h2>
    <form @submit.prevent="submitTransaction">
      <input v-model="transaction.description" placeholder="Description" required />
      <input v-model="transaction.total" type="number" placeholder="Amount" required />
      <input v-model="transaction.transaction_date" type="date" required />
      <button type="submit">Add Transaction</button>
    </form>

    <ul>
      <li v-for="tx in transactions" :key="tx.id">
        {{ tx.description }} - ${{ tx.total }} on {{ tx.transaction_date }}
      </li>
    </ul>
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
