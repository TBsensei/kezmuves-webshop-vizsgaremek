<template>
  <div class="card h-100 shadow-sm product-card border-0">
    <div class="position-relative">
      <img
          :src="getImageUrl(product.image_url)"
          class="card-img-top"
          :alt="product.name"
          style="height: 250px; object-fit: cover;"
      >
      <span v-if="product.category" class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-3 shadow-sm">
        <i class="bi bi-tag-fill me-1 text-info"></i> {{ product.category }}
      </span>
    </div>

    <div class="card-body d-flex flex-column p-4">
      <h5 class="card-title fw-bold text-dark mb-2">{{ product.name }}</h5>

      <p class="card-text text-muted flex-grow-1 small">
        {{ truncateDescription(product.description, 100) }}
      </p>

      <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="price-tag">
          <span class="fs-5 fw-bold text-success">{{ formatPrice(product.price) }} Ft</span>
        </div>

        <button class="btn btn-primary btn-sm px-3 shadow-sm rounded-pill" @click="$emit('add-to-cart', product)">
          <i class="bi bi-cart-plus me-1"></i> Kosárba
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductCard',
  props: {
    // A termék objektum, amit a szülőtől (ProductsView) kapunk
    product: {
      type: Object,
      required: true
    }
  },
  emits: ['add-to-cart'], // Jelezzük a szülőnek, ha kosárba tették a terméket
  methods: {
    getImageUrl(url) {
      if (!url) return 'https://placehold.co/600x400/eeeeee/999999?text=Nincs+kép';
      if (url.startsWith('http')) return url;
      return 'http://localhost:8000' + url; // Backend elérhetőség
    },
    formatPrice(price) {
      return new Intl.NumberFormat('hu-HU').format(price);
    },
    // Rövidítjük a leírást, hogy ne tolja el a kártyák magasságát
    truncateDescription(text, length) {
      if (!text) return '';
      return text.length > length ? text.substring(0, length) + "..." : text;
    }
  }
}
</script>

<style scoped>
.product-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 12px;
  overflow: hidden;
}

.product-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 1rem 3rem rgba(0,0,0,.12)!important;
}

.card-img-top {
  transition: transform 0.5s ease;
}

.product-card:hover .card-img-top {
  transform: scale(1.05); /* Enyhe zoom effekt a képen hovernél */
}

.badge {
  font-weight: 500;
  letter-spacing: 0.5px;
}
</style>