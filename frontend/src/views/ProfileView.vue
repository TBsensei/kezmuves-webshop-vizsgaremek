<template>
  <div class="container mt-4 mb-5">
    <h2 class="mb-4">👤 A profilom</h2>

    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white fw-bold">Adataim</div>
          <div class="card-body" v-if="user">
            <p><strong>Név:</strong> {{ user.name }}</p>
            <p><strong>E-mail:</strong> {{ user.email }}</p>
            <p><strong>Szerepkör:</strong>
              <span class="badge" :class="user.role === 'admin' ? 'bg-danger' : 'bg-secondary'">
                {{ user.role === 'admin' ? 'Adminisztrátor' : 'Vásárló' }}
              </span>
            </p>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-header bg-dark text-white fw-bold">📦 Korábbi rendeléseim</div>
          <div class="card-body p-0">

            <div v-if="isLoading" class="p-4 text-center">
              <div class="spinner-border text-primary" role="status"></div>
            </div>

            <div v-else-if="orders.length === 0" class="p-4 text-center text-muted">
              Még nem adtál le egyetlen rendelést sem.
            </div>

            <div v-else class="table-responsive">
              <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                <tr>
                  <th>Rendelésszám</th>
                  <th>Dátum</th>
                  <th>Összeg</th>
                  <th>Státusz</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="order in orders" :key="order.id">
                  <td class="fw-bold">#{{ order.id }}</td>
                  <td>{{ formatDate(order.created_at) }}</td>
                  <td>{{ formatPrice(order.total_amount) }} Ft</td>
                  <td>
                      <span class="badge" :class="getStatusColor(order.status)">
                        {{ getStatusText(order.status) }}
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
  name: 'ProfileView',
  data() {
    return {
      user: null,
      orders: [],
      isLoading: true
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
        // Ha nincs belépve, kidobjuk a loginra
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