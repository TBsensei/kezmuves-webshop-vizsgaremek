<template>
  <div class="container mt-4 mb-5">

    <div class="row mb-4 align-items-center">
      <div class="col-md-6">
        <h2 class="mb-0"><i class="bi bi-box-seam text-primary me-2"></i> Kínálatunk</h2>
      </div>
      <div class="col-md-6 mt-3 mt-md-0">
        <div class="input-group input-group-lg shadow-sm">
          <span class="input-group-text bg-white border-primary border-end-0">
            <i class="bi bi-search text-muted"></i>
          </span>
          <input
              type="text"
              class="form-control border-primary border-start-0 ps-0"
              placeholder="Keresés a termékek között..."
              v-model="searchQuery"
          >
        </div>
      </div>
    </div>

    <div class="row mb-4" v-if="uniqueCategories.length > 0">
      <div class="col-12 d-flex flex-wrap gap-2">
        <button
            class="btn rounded-pill shadow-sm transition-all"
            :class="selectedCategory === '' ? 'btn-primary fw-bold' : 'btn-outline-primary'"
            @click="selectedCategory = ''"
        >
          <i class="bi bi-collection me-1"></i> Összes termék
        </button>

        <button
            v-for="category in uniqueCategories"
            :key="category"
            class="btn rounded-pill shadow-sm transition-all"
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

    <div v-else-if="filteredProducts.length === 0" class="alert alert-warning text-center shadow-sm py-5 border-warning">
      <div class="fs-1 mb-2"><i class="bi bi-binoculars text-warning"></i></div>
      <h4 class="fw-bold">Nincs találat</h4>
      <p class="mb-0 text-muted">Sajnos nem találtunk a keresésnek megfelelő terméket.</p>
    </div>

    <div v-else class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
      <div class="col" v-for="product in filteredProducts" :key="product.id">
        <ProductCard
            :product="product"
            @add-to-cart="handleAddToCart"
        />
      </div>
    </div>

    <div v-if="showToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
      <div class="toast show align-items-center text-white bg-success border-0 shadow-lg" role="alert">
        <div class="d-flex">
          <div class="toast-body fw-bold fs-6">
            <i class="bi bi-check-circle-fill me-2"></i> Termék sikeresen a kosárba helyezve!
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" @click="showToast = false"></button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from '@/api/axios'
// Beimportáljuk a kártya komponenst
import ProductCard from '@/components/ProductCard.vue'

export default {
  name: 'ProductsView',
  // Regisztráljuk a komponenst, hogy használhassuk a template-ben
  components: {
    ProductCard
  },
  data() {
    return {
      products: [],
      searchQuery: '',
      selectedCategory: '',
      isLoading: true,
      showToast: false,
      toastTimer: null
    }
  },
  computed: {
    // Egyedi kategóriák kigyűjtése az aktív termékekből
    uniqueCategories() {
      const categories = this.products
          .map(p => p.category)
          .filter(category => category && category.trim() !== '');

      return [...new Set(categories)].sort();
    },
    // Szűrt lista (Kereső + Kategória gombok)
    filteredProducts() {
      let result = this.products;

      if (this.selectedCategory !== '') {
        result = result.filter(product => product.category === this.selectedCategory);
      }

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
        console.error('API hiba:', error);
      } finally {
        this.isLoading = false;
      }
    },
    // A kártya komponenstől érkező esemény lekezelése
    handleAddToCart(product) {
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
      this.triggerToast();
    },
    triggerToast() {
      this.showToast = true;
      if (this.toastTimer) clearTimeout(this.toastTimer);
      this.toastTimer = setTimeout(() => this.showToast = false, 3000);
    }
  }
}
</script>

<style scoped>
/* Egy kis extra interakció a gombokhoz */
.transition-all {
  transition: all 0.2s ease-in-out;
}

.btn:hover {
  transform: translateY(-2px);
}

/* Keresőmező fókusz stílus */
.input-group .form-control:focus {
  box-shadow: none;
  border-color: #0d6efd;
}
.input-group:focus-within .input-group-text {
  border-color: #0d6efd;
}
</style>