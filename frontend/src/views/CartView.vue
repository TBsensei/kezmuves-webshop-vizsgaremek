<template>
  <div class="container mt-4">
    <h2>🛒 Kosár tartalma</h2>

    <div v-if="cart.length === 0" class="alert alert-info mt-3">
      A kosarad jelenleg üres.
    </div>

    <div v-else>
      <div class="table-responsive mt-3">
        <table class="table table-hover align-middle">
          <thead class="table-light">
          <tr>
            <th>Termék</th>
            <th>Egységár</th>
            <th style="width: 150px;">Mennyiség</th>
            <th>Összesen</th>
            <th class="text-end">Művelet</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="item in cart" :key="item.id">
            <td class="fw-bold">{{ item.name }}</td>
            <td>{{ formatPrice(item.price) }} Ft</td>

            <td>
              <div class="input-group input-group-sm">
                <button class="btn btn-outline-secondary" type="button" @click="decreaseQuantity(item.id)">-</button>
                <input type="text" class="form-control text-center fw-bold" :value="item.quantity" readonly>
                <button class="btn btn-outline-secondary" type="button" @click="increaseQuantity(item.id)">+</button>
              </div>
            </td>

            <td>{{ formatPrice(item.price * item.quantity) }} Ft</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-danger" @click="removeFromCart(item.id)">🗑️ Törlés</button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 p-3 bg-light rounded shadow-sm">
        <h4 class="mb-3 mb-md-0">Végösszeg: <span class="text-success fw-bold">{{ formatPrice(totalPrice) }} Ft</span></h4>
        <div>
          <button class="btn btn-outline-secondary me-2" @click="clearCart">Kosár ürítése</button>
          <button class="btn btn-success" @click="checkout">Megrendelés leadása</button>
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
  name: 'CartView',
  data() {
    return {
      cart: [],
      // Toast változók
      showToast: false,
      toastMessage: '',
      toastClass: 'bg-success text-white',
      toastTimer: null
    }
  },
  computed: {
    totalPrice() {
      return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    }
  },
  mounted() {
    this.loadCart();
  },
  methods: {
    loadCart() {
      this.cart = JSON.parse(localStorage.getItem('cart')) || [];
    },

    increaseQuantity(id) {
      const item = this.cart.find(i => i.id === id);
      if (item) {
        item.quantity += 1;
        this.saveCart();
      }
    },

    decreaseQuantity(id) {
      const item = this.cart.find(i => i.id === id);
      if (item) {
        if (item.quantity > 1) {
          item.quantity -= 1;
          this.saveCart();
        } else {
          this.removeFromCart(id);
        }
      }
    },

    removeFromCart(id) {
      this.cart = this.cart.filter(item => item.id !== id);
      this.saveCart();
    },

    clearCart() {
      this.cart = [];
      this.saveCart();
    },

    saveCart() {
      localStorage.setItem('cart', JSON.stringify(this.cart));
    },

    // A saját értesítés kezelőnk
    triggerToast(message, type = 'success') {
      this.toastMessage = message;

      if (type === 'warning') {
        this.toastClass = 'bg-warning text-dark';
      } else if (type === 'error') {
        this.toastClass = 'bg-danger text-white';
      } else {
        this.toastClass = 'bg-success text-white';
      }

      this.showToast = true;
      if (this.toastTimer) clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => {
        this.showToast = false;
      }, 3000);
    },

    async checkout() {
      if (this.cart.length === 0) {
        return this.triggerToast('Üres kosár! Tegyél bele valamit a rendeléshez.', 'warning');
      }

      try {
        await axios.post('/api/orders', {
          cart: this.cart,
          total_amount: this.totalPrice
        });

        this.triggerToast('✅ Sikeres rendelés! Köszönjük a vásárlást.', 'success');
        this.clearCart();
      } catch (error) {
        console.error("Hiba a rendelésnél:", error);
        this.triggerToast('❌ Hiba történt a rendelés leadásakor.', 'error');
      }
    },

    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    }
  }
}
</script>