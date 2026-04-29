<template>
  <div class="container mt-4 mb-5">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
      <h2 class="mb-3 mb-md-0"><i class="bi bi-tags text-primary me-2"></i> Termékkezelés</h2>
      <button class="btn btn-success fw-bold shadow-sm" @click="openModal()">
        <i class="bi bi-plus-circle me-1"></i> Új termék
      </button>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
              <th style="width: 80px;" class="text-center"><i class="bi bi-image text-muted"></i></th>
              <th>Név</th>
              <th>Kategória</th>
              <th>Ár</th>
              <th class="text-end">Műveletek</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="product in products" :key="product.id">
              <td class="text-center">
                <img :src="getImageUrl(product.image_url)"
                     class="img-thumbnail shadow-sm border-0"
                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"
                     alt="Kép">
              </td>
              <td class="fw-bold">{{ product.name }}</td>
              <td>
                <span class="badge bg-secondary"><i class="bi bi-tag-fill me-1"></i> {{ product.category }}</span>
              </td>
              <td class="fw-bold text-success">{{ formatPrice(product.price) }} Ft</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary me-2 mb-1 mb-md-0" @click="openModal(product)">
                  <i class="bi bi-pencil-square"></i> Szerkesztés
                </button>
                <button class="btn btn-sm btn-outline-danger mb-1 mb-md-0" @click="deleteProduct(product.id)">
                  <i class="bi bi-trash"></i> Törlés
                </button>
              </td>
            </tr>
            <tr v-if="products.length === 0">
              <td colspan="5" class="text-center p-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                Nincsenek még termékek az adatbázisban.
              </td>
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
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">
              <i class="bi" :class="isEditing ? 'bi-pencil-square' : 'bi-plus-circle'"></i>
              {{ isEditing ? 'Termék szerkesztése' : 'Új termék hozzáadása' }}
            </h5>
            <button type="button" class="btn-close btn-close-white" @click="closeModal"></button>
          </div>
          <div class="modal-body bg-light">
            <form @submit.prevent="saveProduct">
              <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Termék neve *</label>
                <input type="text" class="form-control shadow-sm" v-model="form.name" required>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold text-muted small">Ár (Ft) *</label>
                  <input type="number" class="form-control shadow-sm" v-model="form.price" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label fw-bold text-muted small">Kategória</label>
                  <input type="text" class="form-control shadow-sm" v-model="form.category" placeholder="pl.: Ékszer">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Leírás</label>
                <textarea class="form-control shadow-sm" v-model="form.description" rows="3"></textarea>
              </div>

              <div class="mb-3">
                <label class="form-label fw-bold text-muted small">Kép feltöltése (.jpg, .png)</label>
                <input type="file" class="form-control shadow-sm" @change="handleFileUpload" accept="image/*">
                <small class="text-muted mt-1 d-block" v-if="isEditing">
                  <i class="bi bi-info-circle me-1"></i> Hagyd üresen, ha nem akarod lecserélni a jelenlegi képet.
                </small>
              </div>

              <div class="text-end mt-4 pt-3 border-top">
                <button type="button" class="btn btn-secondary me-2 shadow-sm" @click="closeModal">
                  <i class="bi bi-x-circle"></i> Mégse
                </button>
                <button type="submit" class="btn btn-success shadow-sm" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
                  <i v-else class="bi bi-check2-circle me-1"></i> Mentés
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showToast" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1060">
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
  name: 'AdminProductsView',
  data() {
    return {
      products: [],
      isModalOpen: false,
      isEditing: false,
      isLoading: false,
      form: {
        id: null,
        name: '',
        price: '',
        description: '',
        category: ''
      },
      selectedFile: null,

      // Toast változók
      showToast: false,
      toastMessage: '',
      toastClass: 'bg-success text-white',
      toastTimer: null
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
        this.triggerToast('Hiba a termékek betöltésekor!', 'error');
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

      this.isModalOpen = true;
      document.body.classList.add('modal-open');
    },

    closeModal() {
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
      if (this.selectedFile) formData.append('image', this.selectedFile);

      try {
        if (this.isEditing) {
          formData.append('_method', 'PUT');
          await axios.post(`/api/admin/products/${this.form.id}`, formData); // JAVÍTVA
        } else {
          await axios.post('/api/admin/products', formData); // JAVÍTVA
        }
        this.fetchProducts();
        this.closeModal();
      } catch (error) {
        this.triggerToast('Hiba mentéskor!', 'error');
      } finally { this.isLoading = false; }
    },
    async deleteProduct(id) {
      if (confirm('Törlöd?')) {
        try {
          await axios.delete(`/api/admin/products/${id}`); // JAVÍTVA
          this.fetchProducts();
        } catch (error) { this.triggerToast('Hiba törléskor!', 'error'); }
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

<style scoped>
/* Egy pici finomítás, hogy a modális ablak háttérsötétítése ne ugorjon, hanem szép sima legyen */
.modal.fade {
  transition: opacity .15s linear;
}
</style>