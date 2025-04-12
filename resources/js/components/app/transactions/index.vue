<template>
  <div class="dashboard-module">
    <div class="card"> 
      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            <TransactionForm 
              @update:modelValue="upsertTransaction"
              @delete="deleteTransaction"
              :transaction_id="transaction_id"

            />
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <TransactionsList 
              @open="editTransaction"
              v-model="transactions"
            />
          </div>
        </div>    
      </div>
    </div>
  </div>
</template>
<script>

import TransactionsList from '@/components/app/transactions/TransactionsList.vue';
import TransactionForm  from '@/components/app/transactions/TransactionForm.vue';

export default {
  name: 'Transactions',
  components: {TransactionsList, TransactionForm},
  data: function() {
    return {
      transactions: [],
      transaction_id: null,
    }
  },
  methods: {
    editTransaction(id) {
      alert('editTransaction', id);
      this.transaction_id = id;
    },
    addTransaction() {
      this.transaction_id = null;
    },
    upsertTransaction(transaction) {
      const index = this.transactions.findIndex(t => t.id === transaction.id);
      if (index !== -1) {
        this.$set(this.transactions, index, transaction);
      } else {
        // transaction receives it's ID after being created on the transaction component
        this.transactions.push(transaction);
      }
    },
    deleteTransaction(id) {
      this.transactions = this.transactions.filter(t => t.id !== id);
    },
    updateTransactions(transactions) {
      alert('updateTransactions', transactions);
      this.transactions = transactions;
    },
  }
}
</script>