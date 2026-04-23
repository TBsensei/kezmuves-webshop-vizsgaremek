<template>
  <div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
      <h3 class="text-center mb-4">Bejelentkezés</h3>

      <div v-if="errorMessage" class="alert alert-danger py-2">{{ errorMessage }}</div>

      <form @submit.prevent="login">
        <div class="mb-3">
          <label class="form-label">E-mail cím</label>
          <input type="email" class="form-control" v-model="email" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Jelszó</label>
          <input type="password" class="form-control" v-model="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100" :disabled="isLoading">
          <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
          Belépés
        </button>
      </form>

      <div class="mt-3 text-center small">
        Nincs még fiókod? <router-link to="/register">Regisztrálj itt!</router-link>
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

        // --- ITT AZ ÖSSZEHANGOLÁS A TE ROUTEREDDEL ---
        localStorage.setItem('access_token', response.data.access_token);
        localStorage.setItem('user_role', response.data.user.role);
        localStorage.setItem('user', JSON.stringify(response.data.user)); // Ez kell a név kiírásához

        window.location.href = '/';
      } catch (error) {
        this.errorMessage = 'Hibás e-mail cím vagy jelszó!';
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>