<template>
  <div class="dashboard-module">
    <div class="card"> 
      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            <TransactionForm 
              v-if="transaction_id !== null"
              @update:modelValue="upsertTransaction"
              @delete="deleteTransaction"
              @close="backToList"
              :transaction_id="transaction_id"
            />
            <TransactionsList
              v-if="transaction_id === null"
              @open="editTransaction"
              @delete="deleteTransaction"
              v-model="transactions"
              :per-page="perPage"
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
  mounted() {
    this.getTransactionidFromRouter();
  },
  props: {
    perPage: {
      type: Number,
      default: 25,
    },
  },
  watch: {
    $route(to, from) {
      this.getTransactionidFromRouter();
      if (this.transaction_id === null) {
        this.updateTransactions(this.transactions);
      }
    }
  },
  methods: {
    backToList() {
      this.$router.push({ path: '/app/transactions' });
    },
    getTransactionidFromRouter() {
     if (this.$route.params.transaction_id) {
        this.transaction_id = this.$route.params.transaction_id;
      }
     else {
        this.transaction_id = null;
      }
    },

    editTransaction(id) {
      this.$router.push({ path: `/app/transactions/${id}` });
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
    deleteTransaction(index) {
      this.transactions = this.transactions.filter((_, i) => i !== index);
    },
    updateTransactions(transactions) {
      this.transactions = transactions;
    },
  }
}
</script>