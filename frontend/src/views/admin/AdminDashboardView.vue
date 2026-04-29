<template>
  <div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="bi bi-speedometer2 text-primary me-2"></i> Admin Vezérlőpult</h2>

    <div v-if="isLoading" class="text-center mt-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2 text-muted">Statisztikák betöltése...</p>
    </div>

    <div v-else class="row g-4">
      <div class="col-md-4">
        <div class="card bg-success text-white shadow-sm border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 bg-white bg-opacity-25 rounded p-3 me-3">
              <i class="bi bi-currency-euro fs-1"></i>
            </div>
            <div>
              <h6 class="card-title mb-0 opacity-75">Összes bevétel</h6>
              <h3 class="fw-bold mb-0">{{ formatPrice(totalRevenue) }} Ft</h3>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 bg-white bg-opacity-25 rounded p-3 me-3">
              <i class="bi bi-cart-check fs-1"></i>
            </div>
            <div>
              <h6 class="card-title mb-0 opacity-75">Rendelések száma</h6>
              <h3 class="fw-bold mb-0">{{ orders.length }} db</h3>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card bg-info text-white shadow-sm border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0 bg-white bg-opacity-25 rounded p-3 me-3">
              <i class="bi bi-box-seam fs-1"></i>
            </div>
            <div>
              <h6 class="card-title mb-0 opacity-75">Aktív termékek</h6>
              <h3 class="fw-bold mb-0">{{ productCount }} db</h3>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 mt-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i> Legutóbbi rendelések</h5>
            <router-link to="/admin/orders" class="btn btn-sm btn-outline-primary">Összes megtekintése</router-link>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                  <th class="ps-3">ID</th>
                  <th>Vásárló</th>
                  <th>Dátum</th>
                  <th>Összeg</th>
                  <th>Státusz</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="order in orders.slice(0, 5)" :key="order.id">
                  <td class="ps-3 fw-bold">#{{ order.id }}</td>
                  <td>{{ order.user ? order.user.name : 'Vendég' }}</td>
                  <td>{{ formatDate(order.created_at) }}</td>
                  <td class="fw-bold text-success">{{ formatPrice(order.total_amount) }} Ft</td>
                  <td>
                      <span :class="getStatusBadgeClass(order.status)">
                        {{ translateStatus(order.status) }}
                      </span>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/api/axios'

export default {
  name: 'AdminDashboardView',
  data() {
    return {
      orders: [],
      productCount: 0,
      isLoading: false
    }
  },
  computed: {
    totalRevenue() {
      // Csak a teljesített rendeléseket számoljuk bele a bevételbe
      return this.orders
          .filter(o => o.status === 'completed' || o.status === 'shipped')
          .reduce((sum, order) => sum + Number(order.total_amount), 0);
    }
  },
  mounted() {
    this.fetchDashboardStats();
  },
  methods: {
    async fetchDashboardStats() {
      this.isLoading = true;
      try {
        // JAVÍTVA: Mindkét hívás megkapta az /admin prefixet
        const [ordersRes, productsRes] = await Promise.all([
          axios.get('/api/admin/orders'),
          axios.get('/api/products') // A terméklista publikus, maradhat így is, vagy /api/admin/products ha védett
        ]);

        this.orders = ordersRes.data;
        this.productCount = productsRes.data.length;
      } catch (error) {
        console.error("Hiba a statisztikák betöltésekor:", error);
      } finally {
        this.isLoading = false;
      }
    },
    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('hu-HU');
    },
    translateStatus(status) {
      const statuses = {
        'pending': 'Függőben',
        'shipped': 'Kiszállítva',
        'completed': 'Teljesítve',
        'cancelled': 'Törölve'
      };
      return statuses[status] || status;
    },
    getStatusBadgeClass(status) {
      const classes = {
        'pending': 'badge bg-warning text-dark',
        'shipped': 'badge bg-info text-white',
        'completed': 'badge bg-success text-white',
        'cancelled': 'badge bg-danger text-white'
      };
      return classes[status] || 'badge bg-secondary';
    }
  }
}
</script>