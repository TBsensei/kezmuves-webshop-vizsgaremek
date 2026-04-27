<template>
  <div class="container mt-4 mb-5">

    <div class="row mb-4 align-items-center">
      <div class="col-md-6">
        <h2 class="mb-0">🛍️ Kínálatunk</h2>
      </div>
      <div class="col-md-6 mt-3 mt-md-0">
        <input
            type="text"
            class="form-control form-control-lg border-primary shadow-sm"
            placeholder="🔍 Keresés a termékek között..."
            v-model="searchQuery"
        >
      </div>
    </div>

    <div class="row mb-4" v-if="uniqueCategories.length > 0">
      <div class="col-12 d-flex flex-wrap gap-2">
        <button
            class="btn rounded-pill shadow-sm"
            :class="selectedCategory === '' ? 'btn-primary fw-bold' : 'btn-outline-primary'"
            @click="selectedCategory = ''"
        >
          Összes termék
        </button>

        <button
            v-for="category in uniqueCategories"
            :key="category"
            class="btn rounded-pill shadow-sm"
            :class="selectedCategory === category ? 'btn-primary fw-bold' : 'btn-outline-primary'"
            @click="selectedCategory = category"
        >
          {{ category }}
        </button>
      </div>
    </div>

    <div v-if="isLoading" class="text-center my-5">
      <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
      <p class="mt-2 text-muted">Termékek betöltése...</p>
    </div>

    <div v-else-if="filteredProducts.length === 0" class="alert alert-warning text-center shadow-sm py-4">
      <div class="fs-1 mb-2">🕵️‍♂️</div>
      Sajnos nem találtunk a keresésnek megfelelő terméket ebben a kategóriában.
    </div>

    <div v-else class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <div class="col" v-for="product in filteredProducts" :key="product.id">
        <div class="card h-100 shadow-sm product-card border-0">

          <img
              :src="getImageUrl(product.image_url)"
              class="card-img-top"
              :alt="product.name"
              style="height: 250px; object-fit: cover;"
          >

          <div class="card-body d-flex flex-column">
            <h5 class="card-title fw-bold text-dark">{{ product.name }}</h5>
            <span v-if="product.category" class="badge bg-secondary mb-2 align-self-start">{{ product.category }}</span>

            <p class="card-text text-muted flex-grow-1">{{ product.description }}</p>

            <div class="d-flex justify-content-between align-items-center mt-3">
              <span class="fs-5 fw-bold text-success">{{ formatPrice(product.price) }} Ft</span>
              <button class="btn btn-primary shadow-sm px-4" @click="addToCart(product)">
                🛒 Kosárba
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
      <div class="toast show align-items-center text-white bg-success border-0 shadow-lg" role="alert">
        <div class="d-flex">
          <div class="toast-body fw-bold fs-6">
            ✅ Termék sikeresen a kosárba helyezve!
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
  name: 'ProductsView',
  data() {
    return {
      products: [],
      searchQuery: '',
      selectedCategory: '', // Ez tárolja, hogy épp melyik gombra kattintottunk
      isLoading: true,
      showToast: false,
      toastTimer: null
    }
  },
  computed: {
    // 1. Dinamikusan kinyerjük az egyedi kategóriákat a termékekből
    uniqueCategories() {
      // Csak azokat a kategóriákat vesszük, amik ki vannak töltve
      const categories = this.products
          .map(p => p.category)
          .filter(category => category && category.trim() !== '');

      // A Set segítségével kiszűrjük a duplikációkat (pl. ha 5 "Ékszer" van, csak egyszer jelenik meg)
      return [...new Set(categories)].sort();
    },

    // 2. Kombinált szűrő: Kategória + Szöveges kereső
    filteredProducts() {
      let result = this.products;

      // Ha van kiválasztott kategória, először aszerint szűrünk
      if (this.selectedCategory !== '') {
        result = result.filter(product => product.category === this.selectedCategory);
      }

      // Utána szűrünk a beírt szövegre (ha van)
      if (this.searchQuery !== '') {
        const lowerCaseQuery = this.searchQuery.toLowerCase();
        result = result.filter(product =>
            product.name.toLowerCase().includes(lowerCaseQuery) ||
            (product.description && product.description.toLowerCase().includes(lowerCaseQuery))
        );
      }

      return result;
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
        console.error('Hiba a termékek lekérésekor:', error);
      } finally {
        this.isLoading = false;
      }
    },
    addToCart(product) {
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const existingItem = cart.find(item => item.id === product.id);

      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        cart.push({
          id: product.id,
          name: product.name,
          price: product.price,
          quantity: 1
        });
      }

      localStorage.setItem('cart', JSON.stringify(cart));

      this.showToast = true;
      if (this.toastTimer) clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => this.showToast = false, 3000);
    },
    getImageUrl(url) {
      if (!url) return 'https://placehold.co/600x400/eeeeee/999999?text=Nincs+kép';
      if (url.startsWith('http')) return url;
      return 'http://localhost:8000' + url;
    },
    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    }
  }
}
</script>

<style scoped>
.product-card {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
}
</style>