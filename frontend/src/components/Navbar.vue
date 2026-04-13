<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <router-link class="navbar-brand" to="/">Kézműves Webshop</router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Kezdőlap</router-link>
          </li>

          <li class="nav-item" v-if="!isAuthenticated">
            <router-link class="nav-link" to="/login">Bejelentkezés</router-link>
          </li>

          <li class="nav-item" v-if="isAuthenticated && userRole === 'admin'">
            <router-link class="nav-link text-warning" to="/admin/products">⚙️ Admin Panel</router-link>
          </li>

          <li class="nav-item" v-if="isAuthenticated">
            <a class="nav-link text-danger" href="#" @click.prevent="logout">Kijelentkezés</a>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/cart">🛒 Kosár</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import axios from '@/api/axios'

export default {
  name: 'Navbar', // A komponens neve
  data() {
    return {
      isAuthenticated: !!localStorage.getItem('access_token'),
      userRole: localStorage.getItem('user_role')
    }
  },
  methods: {
    async logout() {
      try {
        await axios.post('/api/logout');
      } catch (error) {
        console.error("Hiba a kijelentkezéskor:", error);
      } finally {
        localStorage.removeItem('access_token');
        localStorage.removeItem('user_role');
        this.isAuthenticated = false;
        this.userRole = null;
        this.$router.push('/login'); // Kijelentkezés után dobjuk a Login oldalra
      }
    }
  }
}
</script>