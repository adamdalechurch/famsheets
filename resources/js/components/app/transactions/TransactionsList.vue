<template>
  <div>
    <h2>All Transactions</h2> 
    <!-- csv upload -->
    <!-- <input type="file" @change="uploadCsv" accept=".csv" /> -->
    <div class="row">
     <!-- <input type="file" @change="uploadCsv" accept=".csv" />
      <button class="btn btn-primary" @click="newTransaction">New Transaction</button> -->
        <div class="col-md-6">
          <input type="file" @change="uploadCsv" accept=".csv" />
          <button class="btn btn-primary" @click="newTransaction">New Transaction</button>
        </div>
    </div>

    <EditableGrid
      v-model="formattedTransactions"
      :columns="columns"
      @open="openTransaction"
      @delete="deleteTransaction"
      show-open
    />
<!-- <div class="pagination mb-3">
  <button class="btn btn-secondary" @click="prevPage" :disabled="pagination.page <= 1">Prev</button>
  <span>Page {{ pagination.page }}</span>
  <button class="btn btn-secondary" @click="nextPage" :disabled="pagination.page >= totalPages">Next</button>
  of <a href="#" @click="pagination.paginate = !pagination.paginate">{{ pagination.paginate ? 'All' : 'Paginated' }}</a>
</div> -->
<div class="pagination mb-3">
  <button class="btn btn-secondary" @click="firstPage" :disabled="pagination.page <= 1"><<</button>
  <button class="btn btn-secondary" @click="prevPage" :disabled="pagination.page <= 1"><</button>
  <span>Page {{ pagination.page }}   of {{ totalPages }}</span>

  <button class="btn btn-secondary" @click="nextPage" :disabled="pagination.page >= totalPages">></button>
  <button class="btn btn-secondary" @click="lastPage" :disabled="pagination.page >= totalPages">>></button>
</div>
    <button class="btn btn-success" @click="saveChanges">Save Changes</button>

  </div>
</template>

<script>
import EditableGrid from '@/components/common/EditableGrid.vue';
import axios from 'axios';

export default {
  name: 'TransactionsList',
  components: { EditableGrid },
  data() {
    return {
      localTransactions: [],
      columns: [
        {
          label: 'Description',
          key: 'description',
          type: 'text',
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
      pagination: {
        page: 1,
        paginate: true, // toggle this to switch between paginated/all
      },
      totalPages: 1,
    };
  },
  props: {
    modelValue: Array,
    perPage: {
      type: Number,
      default: 25,
    },
  },
  watch: {
    modelValue: {
      handler(newVal) {
        this.localTransactions = [...newVal];
        this.$emit('update:modelValue', this.localTransactions);
      },
      immediate: true,
    },
  },
  computed: {
    formattedTransactions:{
      get() {
        return this.localTransactions.map((transaction) => ({
          ...transaction,
          transaction_date: new Date(transaction.transaction_date).toLocaleDateString(),
        }));
      },
      set(value) {
        this.localTransactions = value.map((transaction) => ({
          ...transaction,
          transaction_date: new Date(transaction.transaction_date).toISOString(),
        }));
      },
    },
  },
  created() {
    this.loadTransactions();
  },
  methods: {
    loadTransactions() {
      const { page, paginate } = this.pagination;
      const perPage = this.perPage;
      axios.get('/api/transactions', {
        params: {
          paginate,
          per_page: perPage,
          page,
        }
      }).then((res) => {
        if (paginate) {
          this.localTransactions = res.data.data;
          this.totalPages = res.data.last_page;
        } else {
          this.localTransactions = res.data;
        }
        this.$emit('update:modelValue', this.localTransactions);
      });
    },
    saveChanges() {
      axios.post('/api/transactions/bulk-update', { transactions: this.localTransactions }).then(() => {
        alert('Changes saved!');
      }).catch((err) => {
        console.error(err);
        alert('Failed to save changes.');
      });
    },
    openTransaction(transaction) {
      this.$emit('open', transaction.id);
    },
    deleteTransaction(transaction) {
      axios.delete(`/api/transactions/${transaction.id}`).then(() => {
      }).catch((err) => {
        console.error(err);
        alert('Failed to delete transaction.');
      });

    },
    uploadCsv(event) {
      const file = event.target.files[0];
      const formData = new FormData();
      formData.append('csv', file);

      axios.post('/api/transactions/parse-csv', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }).then((response) => {
        this.localTransactions = response.data;
      }).catch((error) => {
        console.error(error);
      });
    },
    nextPage() {
      if (this.pagination.page < this.totalPages) {
        this.pagination.page++;
        this.loadTransactions();
      }
    },
    prevPage() {
      if (this.pagination.page > 1) {
        this.pagination.page--;
        this.loadTransactions();
      }
    },
    firstPage() {
      this.pagination.page = 1;
      this.loadTransactions();
    },
    lastPage() {
      this.pagination.page = this.totalPages;
      this.loadTransactions();
    },
    newTransaction() {
      this.openTransaction({id: null});
    },
  },
};
</script>
