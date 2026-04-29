<template>
  <div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card shadow-sm p-4 border-0 rounded-3" style="max-width: 400px; width: 100%;">

      <div class="text-center mb-4">
        <i class="bi bi-person-circle text-primary" style="font-size: 3rem;"></i>
        <h3 class="mt-2 fw-bold">Bejelentkezés</h3>
      </div>

      <div v-if="errorMessage" class="alert alert-danger py-2 shadow-sm d-flex align-items-center">
        <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ errorMessage }}
      </div>

      <form @submit.prevent="login">
        <div class="mb-3">
          <label class="form-label text-muted fw-bold small">E-mail cím</label>
          <div class="input-group shadow-sm">
            <span class="input-group-text bg-white text-muted border-end-0">
              <i class="bi bi-envelope"></i>
            </span>
            <input
                type="email"
                class="form-control border-start-0 ps-0"
                v-model="email"
                required
                placeholder="pelda@email.hu"
            >
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label text-muted fw-bold small">Jelszó</label>
          <div class="input-group shadow-sm">
            <span class="input-group-text bg-white text-muted border-end-0">
              <i class="bi bi-lock"></i>
            </span>
            <input
                type="password"
                class="form-control border-start-0 ps-0"
                v-model="password"
                required
                placeholder="••••••••"
            >
          </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" :disabled="isLoading">
          <span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          <i v-else class="bi bi-box-arrow-in-right me-2"></i>
          {{ isLoading ? 'Bejelentkezés folyamatban...' : 'Belépés' }}
        </button>
      </form>

      <div class="mt-4 text-center small text-muted">
        Nincs még fiókod?
        <router-link to="/register" class="fw-bold text-primary text-decoration-none">
          Regisztrálj itt!
        </router-link>
      </div>

    </div>
  </div>
</template>

<script>
import axios from '@/api/axios'

export default {
  name: 'LoginView',
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
      isLoading: false
    }
  },
  methods: {
    async login() {
      this.isLoading = true;
      this.errorMessage = '';

      try {
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password
        });

        localStorage.setItem('access_token', response.data.access_token);
        localStorage.setItem('user_role', response.data.user.role);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        window.location.href = '/';
      } catch (error) {
        this.errorMessage = 'Hibás e-mail cím vagy jelszó!';
        console.error('Bejelentkezési hiba:', error);
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>

<style scoped>
/* Input mezők és ikonok összekapcsolásának elegáns formázása */
.input-group-text {
  transition: border-color 0.15s ease-in-out;
}
.form-control:focus {
  box-shadow: none;
  border-color: #0d6efd;
}
.input-group:focus-within .input-group-text,
.input-group:focus-within .form-control {
  border-color: #0d6efd;
}
</style>