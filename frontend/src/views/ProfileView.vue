<template>
  <div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="bi bi-person-badge text-primary me-2"></i> A profilom</h2>

    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-primary text-white fw-bold py-3">
            <i class="bi bi-card-text me-1"></i> Adataim
          </div>
          <div class="card-body bg-light" v-if="user">
            <p class="mb-2"><strong><i class="bi bi-person me-1"></i> Név:</strong> {{ user.name }}</p>
            <p class="mb-2"><strong><i class="bi bi-envelope me-1"></i> E-mail:</strong> {{ user.email }}</p>
            <p class="mb-0"><strong><i class="bi bi-shield-lock me-1"></i> Szerepkör:</strong>
              <span class="badge ms-1" :class="user.role === 'admin' ? 'bg-danger' : 'bg-secondary'">
                {{ user.role === 'admin' ? 'Adminisztrátor' : 'Vásárló' }}
              </span>
            </p>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card shadow-sm border-0">
          <div class="card-header bg-dark text-white fw-bold py-3">
            <i class="bi bi-box-seam me-1"></i> Korábbi rendeléseim
          </div>
          <div class="card-body p-0">

            <div v-if="isLoading" class="p-5 text-center">
              <div class="spinner-border text-primary" role="status"></div>
              <p class="mt-2 text-muted">Rendelések betöltése...</p>
            </div>

            <div v-else-if="orders.length === 0" class="p-5 text-center text-muted">
              <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
              Még nem adtál le egyetlen rendelést sem.
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Dátum</th>
                  <th>Összeg</th>
                  <th>Státusz</th>
                  <th class="text-end">Művelet</th>
                </tr>
                </thead>
                <tbody v-for="order in orders" :key="order.id">
                <tr>
                  <td class="fw-bold">#{{ order.id }}</td>
                  <td>{{ formatDate(order.created_at) }}</td>
                  <td class="fw-bold">{{ formatPrice(order.total_amount) }} Ft</td>
                  <td>
                      <span class="badge" :class="getStatusColor(order.status)">
                        {{ getStatusText(order.status) }}
                      </span>
                  </td>
                  <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary shadow-sm" @click="toggleDetails(order.id)">
                      {{ expandedOrderId === order.id ? 'Kevesebb' : 'Részletek' }}
                      <i class="bi ms-1" :class="expandedOrderId === order.id ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                    </button>
                  </td>
                </tr>

                <tr v-if="expandedOrderId === order.id" class="table-light">
                  <td colspan="5" class="p-4 border-bottom">
                    <h6 class="fw-bold mb-3"><i class="bi bi-cart-check me-1 text-success"></i> Rendelés tartalma:</h6>
                    <ul class="list-group list-group-flush shadow-sm rounded border">
                      <li v-for="item in order.items" :key="item.id" class="list-group-item d-flex justify-content-between align-items-center bg-white">
                        <div>
                          <span class="fw-bold">{{ item.product ? item.product.name : 'Törölt termék' }}</span>
                          <span class="text-muted ms-2">({{ formatPrice(item.price) }} Ft / db)</span>
                        </div>
                        <span class="badge bg-secondary rounded-pill shadow-sm">{{ item.quantity }} db</span>
                      </li>
                    </ul>

                    <div class="mt-4 p-3 bg-white border rounded shadow-sm small text-muted">
                      <strong class="text-dark"><i class="bi bi-truck me-1"></i> Szállítási adatok:</strong><br>
                      {{ order.shipping_address }}
                    </div>
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
  name: 'ProfileView',
  data() {
    return {
      user: null,
      orders: [],
      isLoading: true,
      expandedOrderId: null
    }
  },
  mounted() {
    this.loadUser();
    this.fetchMyOrders();
  },
  methods: {
    loadUser() {
      const userStr = localStorage.getItem('user');
      if (userStr) {
        this.user = JSON.parse(userStr);
      } else {
        this.$router.push('/login');
      }
    },
    async fetchMyOrders() {
      this.isLoading = true;
      try {
        const response = await axios.get('/api/my-orders');
        this.orders = response.data;
      } catch (error) {
        console.error("Hiba a rendelések lekérésekor:", error);
      } finally {
        this.isLoading = false;
      }
    },
    toggleDetails(orderId) {
      if (this.expandedOrderId === orderId) {
        this.expandedOrderId = null;
      } else {
        this.expandedOrderId = orderId;
      }
    },
    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('hu-HU');
    },
    getStatusText(status) {
      const statuses = {
        'pending': 'Függőben',
        'shipped': 'Kiszállítva',
        'completed': 'Teljesítve',
        'cancelled': 'Törölve'
      };
      return statuses[status] || status;
    },
    getStatusColor(status) {
      const colors = {
        'pending': 'bg-warning text-dark',
        'shipped': 'bg-info text-dark',
        'completed': 'bg-success',
        'cancelled': 'bg-danger'
      };
      return colors[status] || 'bg-secondary';
    }
  }
}
</script>