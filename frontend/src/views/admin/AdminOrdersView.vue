<template>
  <div class="container mt-4 mb-5">
    <h2>📦 Beérkezett Rendelések</h2>

    <div v-if="isLoading" class="text-center mt-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2">Rendelések betöltése...</p>
    </div>

    <div v-else-if="orders.length === 0" class="alert alert-info mt-3">
      Még nem érkezett egyetlen rendelés sem.
    </div>

    <div v-else class="mt-4">
      <div class="card mb-4 shadow-sm" v-for="order in orders" :key="order.id">
        <div class="card-header bg-dark text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center">
          <strong>#{{ order.id }} Rendelés</strong>
          <span class="small mt-1 mt-md-0">Dátum: {{ formatDate(order.created_at) }}</span>
        </div>
        <div class="card-body">

          <div class="d-flex align-items-center mb-3 bg-light p-2 rounded">
            <strong class="me-3">Státusz:</strong>
            <select
                class="form-select form-select-sm w-auto fw-bold"
                :class="getStatusColor(order.status)"
                v-model="order.status"
                @change="updateOrderStatus(order.id, order.status)"
            >
              <option value="pending">Függőben</option>
              <option value="shipped">Kiszállítva</option>
              <option value="completed">Teljesítve</option>
              <option value="cancelled">Törölve</option>
            </select>
          </div>

          <p class="mb-3"><strong>Szállítási cím:</strong> {{ order.shipping_address }}</p>

          <div class="table-responsive">
            <table class="table table-sm table-bordered mb-0">
              <thead class="table-light">
              <tr>
                <th>Termék neve</th>
                <th>Egységár</th>
                <th>Mennyiség</th>
                <th>Részösszeg</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="item in order.items" :key="item.id">
                <td>{{ item.product ? item.product.name : 'Törölt termék' }}</td>
                <td>{{ formatPrice(item.price) }} Ft</td>
                <td>{{ item.quantity }} db</td>
                <td>{{ formatPrice(item.price * item.quantity) }} Ft</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer text-end bg-white">
          <h5 class="mb-0">Végösszeg: <span class="text-success fw-bold">{{ formatPrice(order.total_amount) }} Ft</span></h5>
        </div>
      </div>
    </div>

    <div v-if="showToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
      <div class="toast show align-items-center border-0 shadow" :class="toastClass" role="alert">
        <div class="d-flex text-white" :class="{'text-dark': toastClass.includes('warning')}">
          <div class="toast-body fw-bold">
            {{ toastMessage }}
          </div>
          <button type="button" class="btn-close me-2 m-auto" :class="{'btn-close-white': !toastClass.includes('warning')}" @click="showToast = false"></button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from '@/api/axios'

export default {
  name: 'AdminOrdersView',
  data() {
    return {
      orders: [],
      isLoading: false,
      // Toast változók
      showToast: false,
      toastMessage: '',
      toastClass: 'bg-success text-white',
      toastTimer: null
    }
  },
  mounted() {
    this.fetchOrders();
  },
  methods: {
    async fetchOrders() {
      this.isLoading = true;
      try {
        const response = await axios.get('/api/orders');
        this.orders = response.data;
      } catch (error) {
        console.error("Hiba a rendelések lekérdezésekor:", error);
        this.triggerToast('Nem sikerült betölteni a rendeléseket.', 'error');
      } finally {
        this.isLoading = false;
      }
    },

    // ÚJ: Státusz frissítése API hívással
    async updateOrderStatus(orderId, newStatus) {
      try {
        await axios.put(`/api/orders/${orderId}/status`, { status: newStatus });
        this.triggerToast('✅ Rendelés státusza frissítve!', 'success');
      } catch (error) {
        console.error("Hiba a státusz frissítésekor:", error);
        this.triggerToast('❌ Hiba történt a módosítás során.', 'error');
        // Visszatöltjük az eredeti adatokat, ha hiba volt
        this.fetchOrders();
      }
    },

    // Segédfüggvény a legördülő menü színezéséhez
    getStatusColor(status) {
      if (status === 'pending') return 'text-bg-warning';
      if (status === 'shipped') return 'text-bg-info text-white';
      if (status === 'completed') return 'text-bg-success';
      if (status === 'cancelled') return 'text-bg-danger';
      return '';
    },

    triggerToast(message, type = 'success') {
      this.toastMessage = message;
      if (type === 'warning') this.toastClass = 'bg-warning text-dark';
      else if (type === 'error') this.toastClass = 'bg-danger text-white';
      else this.toastClass = 'bg-success text-white';

      this.showToast = true;
      if (this.toastTimer) clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => this.showToast = false, 3000);
    },

    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleString('hu-HU');
    }
  }
}
</script>

<style scoped>
/* Eltüntetjük a select alapértelmezett kék fókusz keretét, hogy szebb legyen a színes háttérrel */
.form-select:focus {
  box-shadow: none;
  border-color: #ced4da;
}
</style>