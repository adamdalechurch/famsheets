<template>
  <div class="dashboard-module">
    <div class="card"> 
      <div class="card-body">
        <div class="row">
          <div class="col-lg-12">
            <TransactionForm />
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <TransactionsList />
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
return {}
}
}
</script>