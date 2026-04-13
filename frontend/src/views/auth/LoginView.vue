<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h2 class="text-center mb-4">Bejelentkezés</h2>

            <div v-if="errorMessage" class="alert alert-danger" role="alert">
              {{ errorMessage }}
            </div>

            <form @submit.prevent="handleLogin">
              <div class="mb-3">
                <label for="email" class="form-label">E-mail cím</label>
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    v-model="form.email"
                    required
                    placeholder="admin@kezmuves.hu"
                >
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Jelszó</label>
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    v-model="form.password"
                    required
                    placeholder="********"
                >
              </div>

              <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary btn-lg" :disabled="isLoading">
                  <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  {{ isLoading ? 'Bejelentkezés...' : 'Belépés' }}
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
// Beimportáljuk a korábban beállított Axios példányunkat
import axios from '@/api/axios'

export default {
  name: 'LoginView',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      errorMessage: '',
      isLoading: false
    }
  },
  methods: {
    async handleLogin() {
      this.isLoading = true
      this.errorMessage = ''

      try {
        // 1. Lépés: CSRF süti lekérése a Laraveltől (Biztonsági lépés a Sanctumhoz)
        await axios.get('/sanctum/csrf-cookie')

        // 2. Lépés: A bejelentkezési adatok elküldése a backendnek
        const response = await axios.post('/api/login', this.form)

        // 3. Lépés: Ha sikeres, elmentjük a kapott tokent a böngésző LocalStorage-ába
        localStorage.setItem('access_token', response.data.access_token)
        localStorage.setItem('user_role', response.data.user.role)

        // 4. Lépés: Átirányítjuk a felhasználót a főoldalra (vagy az admin panelre)
        this.$router.push('/')

        // Frissítjük az oldalt, hogy a navigációs menü is észrevegye a belépést
        setTimeout(() => {
          window.location.reload()
        }, 100)

      } catch (error) {
        // Ha hibát kapunk (pl. 401 Unauthorized), kiírjuk a felhasználónak
        if (error.response && error.response.status === 401) {
          this.errorMessage = 'Hibás e-mail cím vagy jelszó!'
        } else {
          this.errorMessage = 'Hiba történt a szerverrel való kommunikáció során.'
        }
        console.error("Login hiba:", error)
      } finally {
        this.isLoading = false
      }
    }
  }
}
</script>

<style scoped>
.card {
  border-radius: 15px;
  border: none;
}
</style>