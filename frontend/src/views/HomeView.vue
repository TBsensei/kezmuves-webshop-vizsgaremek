<template>
  <div class="container mt-4 mb-5">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
      <h2 class="mb-3 mb-md-0">Termékeink</h2>

      <div class="input-group shadow-sm" style="max-width: 350px;">
        <span class="input-group-text bg-white border-end-0">🔍</span>
        <input
            type="text"
            class="form-control border-start-0 ps-0"
            placeholder="Keresés név vagy leírás alapján..."
            v-model="searchQuery"
        >
        <button
            v-if="searchQuery"
            class="btn btn-outline-secondary border-start-0"
            type="button"
            @click="searchQuery = ''"
        >
          ✖
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="text-center mt-5">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2">Termékek betöltése...</p>
    </div>

    <div v-else-if="errorMessage" class="alert alert-danger mt-3">
      {{ errorMessage }}
    </div>

    <div v-else-if="filteredProducts.length === 0" class="alert alert-warning mt-3 text-center shadow-sm">
      <h5 class="mb-1">Hoppá!</h5>
      Nincs olyan termék, ami megfelelne a keresésnek: <strong>"{{ searchQuery }}"</strong>
    </div>

    <div v-else class="row mt-4">
      <div class="col-md-4 mb-4" v-for="product in filteredProducts" :key="product.id">
        <div class="card h-100 shadow-sm product-card">
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
      // Toast változók
      toastMessage: '',
      showToast: false,
      toastTimer: null,
      // Kereső változó
      searchQuery: ''
    }
  },
  computed: {
    // Ez a függvény felelős a szűrésért. Valós időben fut le, amint a searchQuery megváltozik!
    filteredProducts() {
      if (!this.searchQuery) {
        return this.products; // Ha üres a kereső, mindent mutat
      }

      const lowerCaseQuery = this.searchQuery.toLowerCase();

      return this.products.filter(product => {
        // Keresünk a névben és a leírásban is
        const nameMatch = product.name.toLowerCase().includes(lowerCaseQuery);
        const descMatch = product.description ? product.description.toLowerCase().includes(lowerCaseQuery) : false;

        return nameMatch || descMatch;
      });
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
  height: 220px;
  object-fit: cover;
  width: 100%;
}

/* Egy kis hover effekt a kártyáknak, hogy élőbb legyen az oldal */
.product-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

/* Keresőmező fókusz stílus javítása */
.form-control:focus {
  box-shadow: none;
  border-color: #ced4da;
}
</style>