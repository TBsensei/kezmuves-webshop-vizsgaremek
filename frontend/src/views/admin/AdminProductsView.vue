<template>
  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>⚙️ Termékek Kezelése</h2>
      <button class="btn btn-success" @click="openCreateForm" v-if="!showForm">
        + Új Termék
      </button>
    </div>

    <div class="card mb-4 shadow-sm border-0" v-if="showForm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ isEditing ? 'Termék szerkesztése' : 'Új termék feltöltése' }}</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="saveProduct">
          <div class="mb-3">
            <label class="form-label fw-bold">Termék neve</label>
            <input type="text" class="form-control" v-model="form.name" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Leírás</label>
            <textarea class="form-control" v-model="form.description" rows="3"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Ár (Ft)</label>
              <input type="number" class="form-control" v-model="form.price" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-bold">Kép URL (Opcionális)</label>
              <input type="text" class="form-control" v-model="form.image_url" placeholder="https://pelda.hu/kep.jpg">
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="button" class="btn btn-secondary" @click="closeForm">Mégsem</button>
            <button type="submit" class="btn btn-primary">
              {{ isEditing ? 'Módosítások mentése' : 'Termék feltöltése' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Kép</th>
            <th>Név</th>
            <th>Ár</th>
            <th class="text-end">Műveletek</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="product in products" :key="product.id">
            <td class="text-muted">#{{ product.id }}</td>
            <td>
              <img
                  :src="product.image_url || 'https://via.placeholder.com/50?text=Kép'"
                  alt="Termék fotó"
                  style="height: 40px; width: 40px; object-fit: cover; border-radius: 5px;"
              >
            </td>
            <td class="fw-bold">{{ product.name }}</td>
            <td>{{ formatPrice(product.price) }} Ft</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-primary me-2" @click="editProduct(product)">✏️ Szerkeszt</button>
              <button class="btn btn-sm btn-outline-danger" @click="deleteProduct(product.id)">🗑️ Töröl</button>
            </td>
          </tr>

          <tr v-if="products.length === 0">
            <td colspan="5" class="text-center py-4 text-muted">Még nincsenek feltöltött termékek.</td>
          </tr>
          </tbody>
        </table>
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
      showForm: false, // Megmutatja vagy elrejti az űrlapot
      isEditing: false, // Tudnunk kell, hogy új terméket hozunk létre, vagy meglévőt szerkesztünk
      form: {
        id: null,
        name: '',
        description: '',
        price: '',
        image_url: ''
      }
    }
  },
  mounted() {
    this.fetchProducts(); // Amint betölt az oldal, lekérjük a listát
  },
  methods: {
    // 1. OLVASÁS (Read)
    async fetchProducts() {
      try {
        const response = await axios.get('/api/products');
        this.products = response.data.data ? response.data.data : response.data;
      } catch (error) {
        console.error("Hiba a betöltéskor:", error);
      }
    },

    // Űrlap megnyitása új termékhez
    openCreateForm() {
      this.resetForm();
      this.showForm = true;
    },

    // Űrlap bezárása
    closeForm() {
      this.showForm = false;
      this.resetForm();
    },

    // Űrlap ürítése
    resetForm() {
      this.isEditing = false;
      this.form = { id: null, name: '', description: '', price: '', image_url: '' };
    },

    // Űrlap megnyitása szerkesztéshez
    editProduct(product) {
      this.isEditing = true;
      // Lemásoljuk a termék adatait az űrlapba (hogy ne írjuk át azonnal a táblázatot)
      this.form = { ...product };
      this.showForm = true;
      window.scrollTo(0, 0); // Felgörgetünk a képernyő tetejére az űrlaphoz
    },

    // 2. és 3. LÉTREHOZÁS / FRISSÍTÉS (Create / Update)
    async saveProduct() {
      try {
        if (this.isEditing) {
          // Ha szerkesztünk, akkor a PUT metódust használjuk a Laravel felé
          await axios.put(`/api/products/${this.form.id}`, this.form);
        } else {
          // Ha új, akkor a POST metódust használjuk
          await axios.post('/api/products', this.form);
        }

        this.fetchProducts(); // Lekérjük az új, friss listát
        this.closeForm(); // Bezárjuk az űrlapot

      } catch (error) {
        console.error("Hiba a mentéskor:", error);
        alert("Hiba történt! Ellenőrizd, hogy minden mezőt kitöltöttél-e.");
      }
    },

    // 4. TÖRLÉS (Delete)
    async deleteProduct(id) {
      // Biztonsági kérdés, nehogy véletlenül kattintsunk félre
      if (confirm("Biztosan véglegesen törölni szeretnéd ezt a terméket?")) {
        try {
          await axios.delete(`/api/products/${id}`);
          this.fetchProducts(); // Frissítjük a táblázatot a törlés után
        } catch (error) {
          console.error("Hiba a törléskor:", error);
          alert("Nem sikerült törölni a terméket.");
        }
      }
    },

    // Ár formázása (ezres tagolás)
    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    }
  }
}
</script>