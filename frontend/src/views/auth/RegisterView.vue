<template>
  <div class="container mt-5 mb-5 d-flex justify-content-center">
    <div class="card shadow-sm p-4" style="max-width: 500px; width: 100%;">
      <h3 class="text-center mb-4">Regisztráció</h3>

      <div v-if="errorMessage" class="alert alert-danger py-2">{{ errorMessage }}</div>

      <form @submit.prevent="register">
        <div class="mb-3">
          <label class="form-label">Teljes név *</label>
          <input type="text" class="form-control" v-model="form.name" required>
        </div>
        <div class="mb-3">
          <label class="form-label">E-mail cím *</label>
          <input type="email" class="form-control" v-model="form.email" required>
        </div>
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Jelszó *</label>
            <input type="password" class="form-control" v-model="form.password" required minlength="6">
          </div>
          <div class="col-md-6 mb-4">
            <label class="form-label">Jelszó újra *</label>
            <input type="password" class="form-control" v-model="form.password_confirmation" required minlength="6">
          </div>
        </div>

        <button type="submit" class="btn btn-success w-100" :disabled="isLoading">
          <span v-if="isLoading" class="spinner-border spinner-border-sm me-2"></span>
          Regisztrálok
        </button>
      </form>

      <div class="mt-3 text-center small">
        Már van fiókod? <router-link to="/login">Jelentkezz be!</router-link>
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
      form: { name: '', email: '', password: '', password_confirmation: '' },
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

        // --- ITT AZ ÖSSZEHANGOLÁS A TE ROUTEREDDEL ---
        localStorage.setItem('access_token', response.data.access_token);
        localStorage.setItem('user_role', response.data.user.role);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        window.location.href = '/';
      } catch (error) {
        this.errorMessage = 'Hiba történt. Lehet, hogy ez az e-mail cím már foglalt.';
        console.error(error);
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>