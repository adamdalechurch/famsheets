<template>
  <div class="dashboard-module">
    <!-- Dashboard Cards -->
    <div class="row">
      <!-- current date -->
      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon blue"><i class="lni lni-calendar"></i></div>
          <div class="content">
            <h6 class="mb-10">Current Time</h6>
            <h3 class="text-bold mb-10">{{ currentTime }}</h3>
            <p class="text-sm">
              <span class="text-gray">{{ longDate }}</span>
            </p>
          </div>
        </div>
      </div>
      <!-- Expense Card -->
      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon primary"><i class="lni lni-credit-cards"></i></div>
          <div class="content">
            <h6 class="mb-10">Total Expense</h6>
            <h3 class="text-bold mb-10">${{ totalExpense }}</h3>
            <p class="text-sm text-danger">
              <i class="lni lni-arrow-down"></i> {{ expenseChange }}%
              <span class="text-gray">Expense</span>
            </p>
          </div>
        </div>
      </div>
     <!-- Income Card -->
      <div class="col-xl-3 col-lg-4 col-sm-6">
        <div class="icon-card mb-30">
          <div class="icon green"><i class="lni lni-coin"></i></div>
          <div class="content">
            <h6 class="mb-10">Total Income</h6>
            <h3 class="text-bold mb-10">${{ totalIncome }}</h3>
            <p class="text-sm text-success">
              <i class="lni lni-arrow-up"></i> {{ incomeChange }}%
              <span class="text-gray">Income</span>
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Budget Expense Table -->
  <!-- Budget Breakdown Table -->
    <div class="card-style mb-30">
      <h6 class="text-medium mb-10">Budget Breakdown</h6>
      <div v-for="budget in budgets" :key="budget.id" class="mb-4">
        <h6 class="text-bold">{{ budget.name }} ({{ budget.start_date }} - {{ budget.end_date }})</h6>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Category</th>
                <th>Budgeted</th>
                <th>Spent</th>
                <th>Remaining</th>
                <th>Usage</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in budget.items" :key="item.category_id">
                <td>{{ item.category_name }}</td>
                <td>{{ formatCurrency(item.budgeted) }}</td>
                <td>{{ formatCurrency(item.spent) }}</td>
                <td>{{ formatCurrency(item.budgeted - item.spent) }}</td>
                <td>
                  <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary" role="progressbar"
                      :style="{ width: item.usage + '%' }"
                      :aria-valuenow="item.usage" aria-valuemin="0" aria-valuemax="100">
                    </div>
                  </div>
                  <small>{{ formatPercentage(item.usage) }}</small>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    </div>

    <!-- Chart Area -->
    <div class="row">
      <div class="col-lg-6">
        <div class="card-style mb-30">
          <div class="title d-flex flex-wrap justify-content-between">
            <div class="left">
              <h6 class="text-medium mb-10">Summary</h6>
              <h3 class="text-bold">${{ monthlyTotal }}</h3>
            </div>
            <div class="right">
              <!-- <select v-model="timeRange" class="light-bg select-sm">
                <option>Yearly</option>
                <option>Monthly</option>
                <option>Weekly</option>
              </select> -->
              <select v-model="summaryFrequency" class="light-bg select-sm">
                <option v-for="option in summaryFrequencyOptions" :key="option">{{ option }}</option>
              </select>
            </div>
          </div>

          <!-- Chart component -->
          <div class="chart">
            <Line v-if="chartData" height="400" :data="chartData" :options="chartOptions" />
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card-style">
          <Transactions @submitted="refreshDashboard" per-page="5" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Line } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  CategoryScale,
  LinearScale,
  PointElement,
} from 'chart.js';
import Transactions from '@/components/app/transactions/index.vue';
import axios from 'axios';

ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);

export default {
  components: { Transactions, Line },
  data() {
    return {
      budgets: [],
      totalExpense: 0,
      totalIncome: 0,
      monthlyTotal: 0,
      expenseChange: -2,
      incomeChange: 5,
      summaryFrequencyOptions: ['Yearly', 'Monthly', 'Weekly', 'Daily'],
      summaryFrequency: 'Monthly',
      chartData: null,
      longDate: new Date().toLocaleString('default', { month: 'long', year: 'numeric', day: 'numeric' }),
      currentTime: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    };
  },
  mounted() {
    this.loadDashboardStats();
    this.loadChart();
    this.loadBudgets();
    setInterval(this.updateTime, 1000);
  },
  watch: {
    summaryFrequency() {
      this.loadChart();
    },
  },
  methods: {
    loadDashboardStats() {
      axios.get('/api/dashboard-stats').then(res => {
        const { total_expense, total_income, monthly_total } = res.data;
        this.totalExpense = total_expense;
        this.totalIncome = total_income;
        this.monthlyTotal = monthly_total;
      });
    },
    updateTime() {
      this.currentTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    },
    loadBudgets() {
    axios.get('/api/budget-report').then(res => {
      this.budgets = res.data.map(budget => {
        budget.items = budget.items.map(item => {
          const usage = item.budgeted > 0 ? (item.spent / item.budgeted) * 100 : 0;
          return { ...item, usage };
        });
        return budget;
      });
    });
  },
    loadChart() {
      axios
        .get(`/api/dashboard-chart?frequency=${this.summaryFrequency.toLowerCase()}`)
        .then(res => {
          const { labels, values } = res.data;
          this.chartData = {
            labels,
            datasets: [
              {
                label: 'Transactions',
                data: values,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.3,
                fill: true,
              },
            ],
          };
        });
    },
    refreshDashboard() {
      this.loadDashboardStats();
      this.loadChart();
    },
    formatCurrency(value) {
      return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
    },
    formatPercentage(value) {
      return new Intl.NumberFormat('en-US', { style: 'percent', minimumFractionDigits: 2 }).format(value / 100);
    },
  },
  computed: {
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        height: 400,
        plugins: {
          legend: {
            display: true,
            position: 'top',
          },
        },
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      };
    },
  },
};
</script>

<style scoped>
.dashboard-module {
  padding: 1rem;
}
.select-sm {
  padding: 4px 8px;
  font-size: 0.9rem;
}
</style>
