<template>
  <div class="container mt-4 mb-5">
    <h2 class="mb-4"><i class="bi bi-box-seam text-primary me-2"></i> Beérkezett Rendelések</h2>

    <div v-if="isLoading" class="text-center mt-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2 text-muted">Rendelések betöltése...</p>
    </div>

    <div v-else-if="orders.length === 0" class="alert alert-info mt-3 shadow-sm d-flex align-items-center">
      <i class="bi bi-info-circle-fill me-3 fs-4"></i>
      <div>Még nem érkezett egyetlen rendelés sem.</div>
    </div>

    <div v-else class="mt-4">
      <div class="card mb-4 shadow-sm border-0" v-for="order in orders" :key="order.id">
        <div class="card-header bg-dark text-white d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3">
          <strong class="fs-5"><i class="bi bi-receipt me-2"></i> #{{ order.id }} Rendelés</strong>
          <span class="small mt-1 mt-md-0"><i class="bi bi-calendar3 me-1"></i> {{ formatDate(order.created_at) }}</span>
        </div>
        <div class="card-body">
          <div class="d-flex align-items-center mb-3 bg-light p-3 rounded shadow-sm border">
            <strong class="me-3"><i class="bi bi-arrow-repeat me-1"></i> Státusz:</strong>
            <select
                class="form-select form-select-sm w-auto fw-bold"
                :class="getStatusColor(order.status)"
                v-model="order.status"
                @change="updateOrderStatus(order.id, order.status)"
            >
              <option value="pending" class="bg-white text-dark">Függőben</option>
              <option value="shipped" class="bg-white text-dark">Kiszállítva</option>
              <option value="completed" class="bg-white text-dark">Teljesítve</option>
              <option value="cancelled" class="bg-white text-dark">Törölve</option>
            </select>
          </div>
          <p class="mb-3"><strong><i class="bi bi-truck me-1"></i> Cím:</strong> {{ order.shipping_address }}</p>
          <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered mb-0 align-middle">
              <thead class="table-light">
              <tr><th>Termék</th><th>Ár</th><th>Db</th><th>Részösszeg</th></tr>
              </thead>
              <tbody>
              <tr v-for="item in order.items" :key="item.id">
                <td>{{ item.product ? item.product.name : 'Törölt termék' }}</td>
                <td>{{ formatPrice(item.price) }} Ft</td>
                <td>{{ item.quantity }} db</td>
                <td class="fw-bold">{{ formatPrice(item.price * item.quantity) }} Ft</td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer text-end bg-white py-3">
          <h5 class="mb-0">Végösszeg: <span class="text-success fw-bold">{{ formatPrice(order.total_amount) }} Ft</span></h5>
        </div>
      </div>
    </div>

    <div v-if="showToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
      <div class="toast show align-items-center border-0 shadow-lg" :class="toastClass" role="alert">
        <div class="d-flex text-white" :class="{'text-dark': toastClass.includes('warning')}">
          <div class="toast-body fw-bold">
            <i class="bi" :class="toastClass.includes('danger') ? 'bi-x-circle' : 'bi-check-circle'"></i> {{ toastMessage }}
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
      showToast: false,
      toastMessage: '',
      toastClass: 'bg-success text-white',
      toastTimer: null
    }
  },
  mounted() { this.fetchOrders(); },
  methods: {
    async fetchOrders() {
      this.isLoading = true;
      try {
        const response = await axios.get('/api/admin/orders'); // JAVÍTVA: /admin prefix
        this.orders = response.data;
      } catch (error) {
        this.triggerToast('Hiba a rendelések lekérésekor.', 'error');
      } finally { this.isLoading = false; }
    },
    async updateOrderStatus(orderId, newStatus) {
      try {
        await axios.put(`/api/admin/orders/${orderId}/status`, { status: newStatus }); // JAVÍTVA: /admin prefix
        this.triggerToast('Státusz frissítve!', 'success');
      } catch (error) {
        this.triggerToast('Hiba a módosításkor.', 'error');
        this.fetchOrders();
      }
    },
    getStatusColor(status) {
      if (status === 'pending') return 'bg-warning text-dark';
      if (status === 'shipped') return 'bg-info text-white';
      if (status === 'completed') return 'bg-success text-white';
      if (status === 'cancelled') return 'bg-danger text-white';
      return '';
    },
    triggerToast(message, type = 'success') {
      this.toastMessage = message;
      this.toastClass = type === 'error' ? 'bg-danger text-white' : 'bg-success text-white';
      this.showToast = true;
      if (this.toastTimer) clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => this.showToast = false, 3000);
    },
    formatPrice(price) { return new Intl.NumberFormat('hu-HU').format(price); },
    formatDate(dateString) { return new Date(dateString).toLocaleString('hu-HU'); }
  }
}
</script>