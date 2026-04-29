<template>
  <footer class="bg-dark text-light py-4 mt-auto border-top border-secondary">
    <div class="container">
      <div class="row">

        <div class="col-md-4 mb-3 mb-md-0">
          <h5 class="fw-bold"><i class="bi bi-gem text-info me-2"></i>Kézműves Webshop</h5>
          <p class="text-secondary small mb-0">
            Egyedi, kézzel készített termékek egyenesen a készítőktől. Minőség és hagyomány minden darabban, egyenesen a te otthonodba.
          </p>
        </div>

        <div class="col-md-4 mb-3 mb-md-0 text-md-center">
          <h5 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Információk</h5>
          <ul class="list-unstyled small">
            <li class="mb-2">
              <router-link to="/aszf" class="text-secondary text-decoration-none footer-link">Általános Szerződési Feltételek</router-link>
            </li>
            <li class="mb-2">
              <router-link to="/adatvedelem" class="text-secondary text-decoration-none footer-link">Adatvédelmi tájékoztató</router-link>
            </li>
            <li>
              <router-link to="/szallitas" class="text-secondary text-decoration-none footer-link">Szállítás és fizetés</router-link>
            </li>
          </ul>
        </div>

        <div class="col-md-4 text-md-end">
          <h5 class="fw-bold"><i class="bi bi-headset me-2"></i>Kapcsolat</h5>
          <p class="text-secondary small mb-1">
            <i class="bi bi-envelope me-1"></i> info@kezmuveswebshop.hu
          </p>
          <p class="text-secondary small mb-0">
            <i class="bi bi-telephone me-1"></i> +36 30 123 4567
          </p>
        </div>

      </div>

      <hr class="border-secondary my-3">

      <div class="text-center small text-secondary">
        &copy; {{ currentYear }} Kézműves Webshop. Minden jog fenntartva. | Vizsgaremek projekt
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer',
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
}
</script>

<style scoped>
.footer-link {
  transition: color 0.2s ease-in-out, transform 0.2s ease-in-out;
  display: inline-block;
}
.footer-link:hover {
  color: #0d6efd !important;
  text-decoration: underline !important;
  transform: translateX(3px); /* Egy nagyon apró, elegáns mozgás jobbra hover esetén */
}
</style>