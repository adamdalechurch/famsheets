<template>
  <div>
    <h2>All Transactions</h2>
    <EditableGrid
      v-model="localTransactions"
      :columns="columns"
      @open="openTransaction"
      @delete-index="deleteTransactionByIndex"
      show-open
    />
    <br />
    <button class="btn btn-success" @click="saveChanges">Save Changes</button>
  </div>
</template>

<script>
import EditableGrid from '@/components/common/EditableGrid.vue';
import DescriptionEdit from '@/components/common/DescriptionEdit.vue';
import axios from 'axios';

export default {
  name: 'TransactionsList',
  components: { EditableGrid, DescriptionEdit },
  data() {
    return {
      localTransactions: [],
      columns: [
        {
          label: 'Description',
          key: 'description',
          editor: DescriptionEdit,
        },
        {
          label: 'Total',
          key: 'total',
          type: 'number',
          placeholder: '0.00',
        },
        {
          label: 'Date',
          key: 'transaction_date',
          type: 'date',
        },
        {
          label: 'Is Income',
          key: 'is_income',
          type: 'checkbox',
        },
        {
          label: 'Recurring',
          key: 'recurring',
          type: 'checkbox',
        },
      ],
    };
  },
  props: {
    modelValue: Array,
  },
  watch: {
    modelValue: {
      handler(newVal) {
        this.localTransactions = [...newVal];
      },
      immediate: true,
    },
  },
  created() {
    this.loadTransactions();
  },
  methods: {
    loadTransactions() {
      axios.get('/api/transactions').then((res) => {
        // this.transactions = res.data;
        this.$emit('update:modelValue', res.data);
      });
    },
    saveChanges() {
      axios.put('/api/transactions/bulk-update', this.transactions).then(() => {
        alert('Changes saved!');
      }).catch((err) => {
        console.error(err);
        alert('Failed to save changes.');
      });
    },
    openTransaction(transaction) {
      this.$emit('open', transaction.id);
    },
  },
};
</script>
