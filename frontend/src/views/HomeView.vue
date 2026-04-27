<template>
  <div>
    <div class="bg-dark text-white text-center py-5 shadow">
      <div class="container py-5">
        <h1 class="display-4 fw-bold mb-4">💎 Üdvözlünk a Kézműves Webshopban!</h1>
        <p class="lead mb-5">
          Fedezd fel egyedi, kézzel készített ékszereinket, kerámiáinkat és ajándéktárgyainkat.
          Minden termékünk szívvel-lélekkel, hazai alkotók műhelyéből származik.
        </p>
        <div class="d-flex justify-content-center gap-3">
          <router-link to="/products" class="btn btn-primary btn-lg fw-bold shadow-sm">
            🛍️ Termékek megtekintése
          </router-link>
        </div>
      </div>
    </div>

    <div class="container mt-5 mb-5">
      <div class="row g-4 text-center">
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm p-3">
            <div class="card-body">
              <div class="display-5 mb-3">🎨</div>
              <h4 class="fw-bold">Egyedi dizájn</h4>
              <p class="text-muted">Nincs két egyforma darab. Nálunk garantáltan megismételhetetlen alkotásokat találsz.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm p-3">
            <div class="card-body">
              <div class="display-5 mb-3">🇭🇺</div>
              <h4 class="fw-bold">Hazai alkotóktól</h4>
              <p class="text-muted">Vásárlásoddal közvetlenül a magyar kézműveseket és kisvállalkozókat támogatod.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm p-3">
            <div class="card-body">
              <div class="display-5 mb-3">🚚</div>
              <h4 class="fw-bold">Gyors szállítás</h4>
              <p class="text-muted">Gondos csomagolás és villámgyors kiszállítás, hogy a csoda minél hamarabb odaérjen hozzád.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomeView'
}
</script>

<style scoped>
/* Egy kis extra háttérkép a Hero szekcióhoz, ha szeretnétek (most egy sötét réteg van rajta) */
.bg-dark {
  background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1452860606245-08befc0ff44b?q=80&w=2070&auto=format&fit=crop') no-repeat center center;
  background-size: cover;
}
</style>