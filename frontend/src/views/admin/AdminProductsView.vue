<template>
  <div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>🛍️ Termékkezelés</h2>
      <button class="btn btn-success" @click="openModal()">
        ➕ Új termék
      </button>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
              <th style="width: 80px;">Kép</th>
              <th>Név</th>
              <th>Kategória</th>
              <th>Ár</th>
              <th class="text-end">Műveletek</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="product in products" :key="product.id">
              <td>
                <img :src="getImageUrl(product.image_url)"
                     class="img-thumbnail"
                     style="width: 60px; height: 60px; object-fit: cover;"
                     alt="Kép">
              </td>
              <td class="fw-bold">{{ product.name }}</td>
              <td><span class="badge bg-secondary">{{ product.category }}</span></td>
              <td>{{ formatPrice(product.price) }} Ft</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-2" @click="openModal(product)">✏️ Szerkesztés</button>
                <button class="btn btn-sm btn-outline-danger" @click="deleteProduct(product.id)">🗑️ Törlés</button>
              </td>
            </tr>
            <tr v-if="products.length === 0">
              <td colspan="5" class="text-center p-4 text-muted">Nincsenek még termékek az adatbázisban.</td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div
        class="modal fade"
        :class="{ 'show d-block': isModalOpen }"
        tabindex="-1"
        :style="isModalOpen ? 'background-color: rgba(0,0,0,0.5);' : ''"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">{{ isEditing ? 'Termék szerkesztése' : 'Új termék hozzáadása' }}</h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveProduct">
              <div class="mb-3">
                <label class="form-label">Termék neve *</label>
                <input type="text" class="form-control" v-model="form.name" required>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Ár (Ft) *</label>
                  <input type="number" class="form-control" v-model="form.price" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Kategória</label>
                  <input type="text" class="form-control" v-model="form.category" placeholder="pl.: Ékszer">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Leírás</label>
                <textarea class="form-control" v-model="form.description" rows="3"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label">Kép feltöltése (.jpg, .png)</label>
                <input type="file" class="form-control" @change="handleFileUpload" accept="image/*">
                <small class="text-muted" v-if="isEditing">Hagyd üresen, ha nem akarod lecserélni a jelenlegi képet.</small>
              </div>

              <div class="text-end mt-4">
                <button type="button" class="btn btn-secondary me-2" @click="closeModal">Mégse</button>
                <button type="submit" class="btn btn-success" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                  Mentés
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from '@/api/axios'
// Nincs több Bootstrap import! Kivágtuk a gyökerét a problémának!

export default {
  name: 'AdminProductsView',
  data() {
    return {
      products: [],
      isModalOpen: false, // Ezzel a Vue változóval irányítjuk az ablakot!
      isEditing: false,
      isLoading: false,
      form: {
        id: null,
        name: '',
        price: '',
        description: '',
        category: ''
      },
      selectedFile: null
    }
  },
  mounted() {
    this.fetchProducts();
  },
  methods: {
    async fetchProducts() {
      try {
        const response = await axios.get('/api/products');
        this.products = response.data;
      } catch (error) {
        console.error('Hiba a lekérdezéskor:', error);
      }
    },

    handleFileUpload(event) {
      this.selectedFile = event.target.files[0];
    },

    openModal(product = null) {
      this.selectedFile = null;

      const fileInput = document.querySelector('input[type="file"]');
      if (fileInput) fileInput.value = '';

      if (product) {
        this.isEditing = true;
        this.form = { ...product };
      } else {
        this.isEditing = false;
        this.form = { id: null, name: '', price: '', description: '', category: '' };
      }

      // Megjelenítjük az ablakot, és rögzítjük a hátteret, hogy ne lehessen görgetni
      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },

    closeModal() {
      // Eltüntetjük az ablakot, és visszaadjuk a görgetést
      this.isModalOpen = false;
      document.body.classList.remove('modal-open');
    },

    async saveProduct() {
      this.isLoading = true;

      const formData = new FormData();
      formData.append('name', this.form.name);
      formData.append('price', this.form.price);
      formData.append('description', this.form.description || '');
      formData.append('category', this.form.category || '');

      if (this.selectedFile) {
        formData.append('image', this.selectedFile);
      }

      try {
        if (this.isEditing) {
          formData.append('_method', 'PUT');
          await axios.post(`/api/products/${this.form.id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        } else {
          await axios.post('/api/products', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          });
        }

        this.fetchProducts();
        this.closeModal(); // Mentés után is bezárjuk!
      } catch (error) {
        console.error('Hiba mentéskor:', error);
        alert('Hiba történt a mentés során!');
      } finally {
        this.isLoading = false;
      }
    },

    async deleteProduct(id) {
      if (confirm('Biztosan törölni szeretnéd ezt a terméket?')) {
        try {
          await axios.delete(`/api/products/${id}`);
          this.fetchProducts();
        } catch (error) {
          console.error('Hiba törléskor:', error);
        }
      }
    },

    getImageUrl(url) {
      if (!url) return 'https://placehold.co/100?text=Nincs+kép';
      if (url.startsWith('http')) return url;
      return 'http://localhost:8000' + url;
    },

    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    }
  }
}
</script>