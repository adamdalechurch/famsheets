<template>
  <div>
    <h2>Categories</h2> 

    <EditableGrid
      v-model="categories"
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
  name: 'Categories',
  components: { EditableGrid },
  data() {
    return {
      categories: [],
      columns: [
        {
          label: 'Name',
          key: 'name',
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
    this.loadCategories();
 },
  methods: {
    
    loadCategories() {
      axios.get('/api/categories').then((res) => {
        this.categories = res.data;
      });
    },
    saveChanges() {
      axios.post('/api/categories', this.localTransactions).then((res) => {
        this.categories = res.data;
      });
    },
  },
};
</script>
