<template>
  <div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card shadow-sm p-4 border-0 rounded-3" style="max-width: 500px; width: 100%;">

      <div class="text-center mb-4">
        <i class="bi bi-person-plus text-success" style="font-size: 3rem;"></i>
        <h3 class="mt-2 fw-bold">Regisztráció</h3>
      </div>

      <div v-if="errorMessage" class="alert alert-danger py-2 shadow-sm d-flex align-items-center">
        <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ errorMessage }}
      </div>

      <form @submit.prevent="register">
        <div class="mb-3">
          <label class="form-label text-muted fw-bold small">Teljes név *</label>
          <div class="input-group shadow-sm">
            <span class="input-group-text bg-white text-muted border-end-0">
              <i class="bi bi-person"></i>
            </span>
            <input
                type="text"
                class="form-control border-start-0 ps-0"
                v-model="form.name"
                required
                placeholder="Pl.: Minta János"
            >
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label text-muted fw-bold small">E-mail cím *</label>
          <div class="input-group shadow-sm">
            <span class="input-group-text bg-white text-muted border-end-0">
              <i class="bi bi-envelope"></i>
            </span>
            <input
                type="email"
                class="form-control border-start-0 ps-0"
                v-model="form.email"
                required
                placeholder="pelda@email.hu"
            >
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label text-muted fw-bold small">Jelszó *</label>
            <div class="input-group shadow-sm">
              <span class="input-group-text bg-white text-muted border-end-0">
                <i class="bi bi-lock"></i>
              </span>
              <input
                  type="password"
                  class="form-control border-start-0 ps-0"
                  v-model="form.password"
                  required
                  minlength="6"
                  placeholder="••••••••"
              >
            </div>
          </div>
          <div class="col-md-6 mb-4">
            <label class="form-label text-muted fw-bold small">Jelszó újra *</label>
            <div class="input-group shadow-sm">
              <span class="input-group-text bg-white text-muted border-end-0">
                <i class="bi bi-check2-all"></i>
              </span>
              <input
                  type="password"
                  class="form-control border-start-0 ps-0"
                  v-model="form.password_confirmation"
                  required
                  minlength="6"
                  placeholder="••••••••"
              >
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-success w-100 fw-bold shadow-sm" :disabled="isLoading">
          <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          <i v-else class="bi bi-person-check-fill me-2"></i>
          {{ isLoading ? 'Regisztráció folyamatban...' : 'Regisztrálok' }}
        </button>
      </form>

      <div class="mt-4 text-center small text-muted">
        Már van fiókod?
        <router-link to="/login" class="fw-bold text-success text-decoration-none">
          Jelentkezz be!
        </router-link>
      </div>

    </div>
  </div>
</template>

<script>
import axios from '@/api/axios'

export default {
  name: 'RegisterView',
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      errorMessage: '',
      isLoading: false
    }
  },
  methods: {
    async register() {
      if (this.form.password !== this.form.password_confirmation) {
        this.errorMessage = 'A két jelszó nem egyezik!';
        return;
      }

      this.isLoading = true;
      this.errorMessage = '';

      try {
        const response = await axios.post('/api/register', this.form);

        localStorage.setItem('access_token', response.data.access_token);
        localStorage.setItem('user_role', response.data.user.role);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        window.location.href = '/';
      } catch (error) {
        this.errorMessage = 'Hiba történt. Lehet, hogy ez az e-mail cím már foglalt.';
        console.error('Regisztrációs hiba:', error);
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
/* Ugyanaz a fókusz-animáció, mint a LoginView-ban a tökéletes összhangért */
.input-group-text {
  transition: border-color 0.15s ease-in-out;
}
.form-control:focus {
  box-shadow: none;
  border-color: #198754; /* Bootstrap success zöld */
}
.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control {
  border-color: #198754;
}
</style>