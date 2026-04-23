<template>
  <div class="container mt-4 mb-5">
    <h2>🛒 Kosár tartalma</h2>

    <div v-if="cart.length === 0" class="alert alert-info mt-3">
      A kosarad jelenleg üres. Nézz szét a termékeink között!
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
          <button v-if="!showForm" class="btn btn-primary" @click="showForm = true">Tovább a rendeléshez ➔</button>
        </div>
      </div>

      <div v-if="showForm" class="card mt-4 shadow border-primary">
        <div class="card-header bg-primary text-white fw-bold">
          Szállítási adatok megadása
        </div>
        <div class="card-body">
          <form @submit.prevent="submitOrder">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Teljes név *</label>
                <input type="text" class="form-control" v-model="customer.name" required placeholder="Pl.: Minta János">
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Telefonszám *</label>
                <input
                    type="tel"
                    class="form-control"
                    v-model="customer.phone"
                    @input="filterPhone"
                    required
                    placeholder="Pl.: +36301234567"
                >
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Szállítási cím (Irányítószám, Település, Utca, Házszám) *</label>
              <input type="text" class="form-control" v-model="customer.address" required placeholder="Pl.: 1234 Budapest, Fő utca 1.">
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-outline-secondary me-2" @click="showForm = false">Mégse</button>
              <button type="submit" class="btn btn-success">✅ Megrendelés véglegesítése</button>
            </div>
          </form>
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
      showForm: false,
      customer: {
        name: '',
        phone: '',
        address: ''
      },
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
    this.loadUserData();
  },
  methods: {
    loadUserData() {
      const userStr = localStorage.getItem('user');
      if (userStr) {
        const user = JSON.parse(userStr);
        // Automatikusan beírjuk a nevét, ha be van lépve
        this.customer.name = user.name || '';
        this.customer.phone = user.phone || '';
        this.customer.address = user.address || '';
      }
    },
    loadCart() {
      this.cart = JSON.parse(localStorage.getItem('cart')) || [];
    },
    increaseQuantity(id) {
      const item = this.cart.find(i => i.id === id);
      if (item) { item.quantity += 1; this.saveCart(); }
    },
    decreaseQuantity(id) {
      const item = this.cart.find(i => i.id === id);
      if (item) {
        if (item.quantity > 1) { item.quantity -= 1; this.saveCart(); }
        else { this.removeFromCart(id); }
      }
    },
    removeFromCart(id) {
      this.cart = this.cart.filter(item => item.id !== id);
      this.saveCart();
    },
    clearCart() {
      this.cart = [];
      this.showForm = false;
      this.saveCart();
    },
    saveCart() {
      localStorage.setItem('cart', JSON.stringify(this.cart));
    },

    // Telefonszám szűrése gépelés közben
    filterPhone() {
      this.customer.phone = this.customer.phone.replace(/[^\d+]/g, '');
    },

    // Az új rendelés leadó függvény
    async submitOrder() {
      // Összefűzzük a 3 mezőt egyetlen sztringgé
      const fullShippingAddress = `${this.customer.name} | ${this.customer.address} | Tel: ${this.customer.phone}`;

      try {
        await axios.post('/api/orders', {
          cart: this.cart,
          total_amount: this.totalPrice,
          shipping_address: fullShippingAddress
        });

        this.triggerToast('✅ Sikeres rendelés! Köszönjük a vásárlást.', 'success');
        this.clearCart();

        // Űrlap alaphelyzetbe állítása
        this.customer = { name: '', phone: '', address: '' };

      } catch (error) {
        console.error("Hiba a rendelésnél:", error);
        this.triggerToast('❌ Hiba történt a rendelés leadásakor.', 'error');
      }
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
    }
  }
}
</script>