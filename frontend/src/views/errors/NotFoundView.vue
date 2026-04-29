<template>
  <div class="container mt-5 mb-5">
    <div class="row justify-content-center">
      <div class="col-md-6 text-center">
        <div class="card shadow-sm border-0 p-5 rounded-4">
          <div class="card-body">
            <div class="mb-4">
              <i class="bi bi-search text-primary" style="font-size: 5rem;"></i>
            </div>

            <h1 class="display-1 fw-bold text-dark">404</h1>
            <h3 class="mb-3 fw-bold">Az oldal nem található</h3>

            <p class="lead text-muted mb-4">
              Sajnáljuk, de a keresett oldal nem létezik, vagy elköltözött a helyéről.
              Ellenőrizd az URL-t, vagy térj vissza a biztonságos kezdőlapra.
            </p>

            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
              <router-link to="/" class="btn btn-primary btn-lg px-4 gap-3 shadow-sm">
                <i class="bi bi-house-door me-1"></i> Vissza a főoldalra
              </router-link>
              <router-link to="/products" class="btn btn-outline-secondary btn-lg px-4 shadow-sm">
                <i class="bi bi-bag me-1"></i> Termékek böngészése
              </router-link>
            </div>
          </div>
        </div>

        <div class="mt-4 text-muted small">
          <i class="bi bi-info-circle me-1"></i> Page Not Found
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NotFoundView"
}
</script>

<style scoped>
/* Egy kis "lebegő" animáció a kereső ikonnak */
.bi-search {
  display: inline-block;
  animation: float 3s ease-in-out infinite;
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
  100% { transform: translateY(0px); }
}

.display-1 {
  font-size: 6rem;
  color: #dee2e6;
}
</style>