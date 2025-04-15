<template>
  <div>
    <button @click="close"><<</button>
    <h2>Transaction</h2>
    <form @submit.prevent="submitTransaction">
      <div class="input-style-1">
        <label>Description</label>
        <!-- <DescriptionEdit v-model="transaction.description" /> -->
        <input v-model="transaction.description" type="text" required />
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
   
      <TransactionItems v-model="transaction.transaction_items" />

      <br />

      <button class="btn btn-sm btn-primary" type="submit">Submit Transaction</button>
    </form>
  </div>
</template>

<script>
import CategoryEdit from '@/components/common/CategoryEdit.vue';
import TransactionItems from './TransactionItems.vue';
import AutoCompleteInput from '@/components/common/AutoCompleteInput.vue';
import axios from 'axios';

const TRANSACTION_BLANK = {
  description: '',
  total: 0,
  is_income: false,
  recurring: false,
  transaction_schedule_id: null,
  income_source_id: null,
  transaction_date: new Date().toISOString().split('T')[0],
  items: [{ category_id: null, description: '', amount: 0 }],
};

export default {
  components: { CategoryEdit, AutoCompleteInput, TransactionItems },
  data() {
    return {
      transaction: TRANSACTION_BLANK,
    };
  },
  props: {
    transaction_id: {
      type: Number,
      default: null,
    }
  },
  watch: {
    transaction_id: {
      immediate: true,
      handler(val) {
        if (val) {
          axios.get(`/api/transactions/${val}`).then(res => {
            this.transaction = res.data;
          });
        } else {
          this.transaction = TRANSACTION_BLANK;
        }
      },
    },
  },
  methods: {
    addItem() {
      this.transaction.transaction_items.push({ category_id: null, description: '', amount: 0 });
    },
    removeItem(index) {
      this.transaction.transaction_items.splice(index, 1);
    },
    submitTransaction() {
      // axios.post('/api/transactions', this.transaction).then(res => {
      //   alert('Transaction added!');
      //   // Reset form
      //   // .. abridged
      // });
      if(this.transaction_id) {
        axios.put(`/api/transactions/${this.transaction_id}`, this.transaction).then(res => {
          alert('Transaction updated!');
          this.$emit('update:modelValue', res.data);
        });
      } else {
        axios.post('/api/transactions', this.transaction).then(res => {
          alert('Transaction added!');
          this.$emit('update:modelValue', res.data);
        });
      }
    },
    close() {
      this.$emit('close');
    },
  },
};
</script>
