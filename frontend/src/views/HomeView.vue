<template>
  <div class="container mt-4">
    <h2>Termékeink</h2>

    <div v-if="isLoading" class="text-center mt-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2">Termékek betöltése...</p>
    </div>

    <div v-else-if="errorMessage" class="alert alert-danger mt-3">
      {{ errorMessage }}
    </div>

    <div v-else class="row mt-4">
      <div class="col-md-4 mb-4" v-for="product in products" :key="product.id">
        <div class="card h-100 shadow-sm">
          <img v-if="product.image_url" :src="product.image_url" class="card-img-top product-image" alt="Termék képe">
          <div v-else class="card-img-top bg-light text-muted d-flex align-items-center justify-content-center product-image">
            Nincs kép
          </div>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ product.name }}</h5>
            <p class="card-text text-truncate">{{ product.description }}</p>
            <div class="mt-auto d-flex justify-content-between align-items-center">
              <span class="fw-bold fs-5">{{ formatPrice(product.price) }} Ft</span>
              <button class="btn btn-outline-primary btn-sm" @click="addToCart(product)">
                🛒 Kosárba
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
      <div class="toast show align-items-center text-bg-success border-0 shadow" role="alert">
        <div class="d-flex">
          <div class="toast-body fw-bold">
            ✅ {{ toastMessage }}
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="showToast = false"></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/api/axios'

export default {
  name: 'HomeView',
  data() {
    return {
      products: [],
      isLoading: false,
      errorMessage: '',
      toastMessage: '',
      showToast: false,
      toastTimer: null
    }
  },
  mounted() {
    this.fetchProducts();
  },
  methods: {
    async fetchProducts() {
      this.isLoading = true;
      try {
        const response = await axios.get('/api/products');
        this.products = response.data;
      } catch (error) {
        console.error("Hiba a termékek betöltésekor:", error);
        this.errorMessage = 'Nem sikerült betölteni a termékeket.';
      } finally {
        this.isLoading = false;
      }
    },

    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    },

    addToCart(product) {
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const existingItem = cart.find(item => item.id === product.id);

      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        cart.push({ id: product.id, name: product.name, price: product.price, quantity: 1 });
      }

      localStorage.setItem('cart', JSON.stringify(cart));

      this.triggerToast(`${product.name} bekerült a kosárba!`);
    },

    triggerToast(message) {
      this.toastMessage = message;
      this.showToast = true;
      if (this.toastTimer) clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => {
        this.showToast = false;
      }, 3000);
    }
  }
}
</script>

<style scoped>
.product-image {
  height: 220px; /* Itt állíthatod be a képek fix magasságát */
  object-fit: cover; /* Megakadályozza a kép torzulását, levágja a kilógó részeket */
  width: 100%;
}
</style>