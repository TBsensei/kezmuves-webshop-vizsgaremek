<template>
  <div class="container mt-5 mb-5">
    <div class="row justify-content-center">
      <div class="col-md-6 text-center">
        <div class="card shadow-sm border-0 p-5 rounded-4">
          <div class="card-body">
            <div class="mb-4">
              <i class="bi bi-shield-lock text-danger" style="font-size: 5rem;"></i>
            </div>

            <h1 class="display-5 fw-bold text-dark mb-3">Hozzáférés megtagadva</h1>

            <p class="lead text-muted mb-4">
              Sajnáljuk, de nincs jogosultságod az oldal megtekintéséhez.
              Ez a tartalom csak adminisztrátorok számára érhető el.
            </p>

            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
              <router-link to="/" class="btn btn-primary btn-lg px-4 gap-3 shadow-sm">
                <i class="bi bi-house-door me-1"></i> Vissza a főoldalra
              </router-link>
              <router-link to="/login" class="btn btn-outline-secondary btn-lg px-4 shadow-sm">
                <i class="bi bi-box-arrow-in-right me-1"></i> Bejelentkezés másként
              </router-link>
            </div>
          </div>
        </div>

        <div class="mt-4 text-muted small">
          <i class="bi bi-info-circle me-1"></i> Hibakód: 403 Forbidden
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "NotAuthorizedView"
}
</script>

<style scoped>
/* Egy kis finom animáció az ikonnak, hogy vonzza a tekintetet */
.bi-shield-lock {
  display: inline-block;
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.05); }
  100% { transform: scale(1); }
}

.card {
  background-color: #ffffff;
}
</style>