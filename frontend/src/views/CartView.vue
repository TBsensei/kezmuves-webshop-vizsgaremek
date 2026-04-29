<template>
  <div class="container mt-4 mb-5">
    <h2><i class="bi bi-cart3 text-primary"></i> Kosár tartalma</h2>

    <div v-if="cart.length === 0" class="alert alert-info mt-3 shadow-sm">
      <i class="bi bi-info-circle me-2"></i> A kosarad jelenleg üres. Nézz szét a termékeink között!
    </div>

    <div v-else>
      <div class="table-responsive mt-3">
        <table class="table table-hover align-middle border shadow-sm">
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
                <button class="btn btn-outline-secondary" type="button" @click="decreaseQuantity(item.id)">
                  <i class="bi bi-dash"></i>
                </button>
                <input type="text" class="form-control text-center fw-bold" :value="item.quantity" readonly>
                <button class="btn btn-outline-secondary" type="button" @click="increaseQuantity(item.id)">
                  <i class="bi bi-plus"></i>
                </button>
              </div>
            </td>
            <td>{{ formatPrice(item.price * item.quantity) }} Ft</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-danger" @click="removeFromCart(item.id)">
                <i class="bi bi-trash"></i> Törlés
              </button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-4 p-3 bg-light rounded shadow-sm border">
        <h4 class="mb-3 mb-md-0">Végösszeg: <span class="text-success fw-bold">{{ formatPrice(totalPrice) }} Ft</span></h4>
        <div>
          <button class="btn btn-outline-secondary me-2" @click="clearCart">
            <i class="bi bi-cart-x"></i> Kosár ürítése
          </button>

          <button v-if="isLoggedIn && !showForm" class="btn btn-primary" @click="showForm = true">
            Tovább a rendeléshez <i class="bi bi-arrow-right"></i>
          </button>
        </div>
      </div>

      <div v-if="!isLoggedIn" class="alert alert-warning mt-4 shadow-sm border-warning">
        <div class="d-flex align-items-center justify-content-between">
          <div>
            <h5 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Figyelem!</h5>
            <p class="mb-0">A rendelés leadásához be kell jelentkezned vagy regisztrálnod kell egy fiókot.</p>
          </div>
          <router-link to="/login" class="btn btn-warning">
            <i class="bi bi-box-arrow-in-right"></i> Bejelentkezés
          </router-link>
        </div>
      </div>

      <div v-if="showForm && isLoggedIn" class="card mt-4 shadow border-primary">
        <div class="card-header bg-primary text-white fw-bold">
          <i class="bi bi-truck"></i> Szállítási adatok megadása
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
              <button type="button" class="btn btn-outline-secondary me-2" @click="showForm = false">
                <i class="bi bi-x-circle"></i> Mégse
              </button>
              <button type="submit" class="btn btn-success" :disabled="isProcessing">
                <span v-if="isProcessing" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                <i v-else class="bi bi-check2-circle"></i> Megrendelés véglegesítése
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div v-if="showToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
      <div class="toast show align-items-center border-0 shadow" :class="toastClass" role="alert">
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
  name: 'CartView',
  data() {
    return {
      cart: [],
      showForm: false,
      isLoggedIn: false,
      isProcessing: false,
      customer: {
        name: '',
        phone: '',
        address: ''
      },
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
    this.checkAuthAndLoadUserData();
  },
  methods: {
    checkAuthAndLoadUserData() {
      const token = localStorage.getItem('access_token');
      const userStr = localStorage.getItem('user');

      if (token && userStr) {
        this.isLoggedIn = true;
        const user = JSON.parse(userStr);
        this.customer.name = user.name || '';
        this.customer.phone = user.phone || '';
        this.customer.address = user.address || '';
      } else {
        this.isLoggedIn = false;
      }
    },
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
      this.showForm = false;
      this.saveCart();
    },
    saveCart() {
      localStorage.setItem('cart', JSON.stringify(this.cart));
    },
    filterPhone() {
      this.customer.phone = this.customer.phone.replace(/[^\d+]/g, '');
    },
    async submitOrder() {
      if (!this.isLoggedIn) {
        this.triggerToast('A rendelés leadásához be kell jelentkezned!', 'error');
        return;
      }

      this.isProcessing = true;
      const fullShippingAddress = `${this.customer.name} | ${this.customer.address} | Tel: ${this.customer.phone}`;

      try {
        await axios.post('/api/orders', {
          cart: this.cart,
          total_amount: this.totalPrice,
          shipping_address: fullShippingAddress
        });

        this.triggerToast('Sikeres rendelés! Köszönjük a vásárlást.', 'success');
        this.clearCart();
        this.customer = { name: '', phone: '', address: '' };

        setTimeout(() => {
          this.$router.push('/profile');
        }, 1500);

      } catch (error) {
        console.error("Hiba a rendelésnél:", error);
        this.triggerToast('Hiba történt a rendelés leadásakor.', 'error');
      } finally {
        this.isProcessing = false;
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