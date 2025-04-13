<template>
  <div>
    <h2>Debug</h2>
    <!-- csv upload -->
    <!-- <input type="file" @change="uploadCsv" accept=".csv" /> -->

    <EditableGrid
      v-model="debugText"
      :columns="columns"
      show-open="false"
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

  </div>
</template>

<script>
import EditableGrid from '@/components/common/EditableGrid.vue';
import axios from 'axios';

export default {
  name: 'DebugText',
  components: { EditableGrid },
  data() {
    return {
      debugText: [],
      columns: [
        {
          label: 'Text',
          key: 'text',
          type: 'text',
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
    perPage: {
      type: Number,
      default: 25,
    },
  },
  created() {
    this.loadDebugText();
 },
  methods: {
    loadDebugText() {
      axios.get('/api/debug-text').then((res) => {
        this.localTransactions = res.data;
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
