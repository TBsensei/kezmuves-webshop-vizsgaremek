<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <router-link class="navbar-brand fw-bold" to="/">💎 Kézműves Webshop</router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/products">Termékek</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/cart">🛒 Kosár</router-link>
          </li>

          <li class="nav-item dropdown" v-if="isAdmin">
            <a class="nav-link dropdown-toggle text-warning fw-bold" href="#" id="adminMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              ⚙️ Admin
            </a>
            <ul class="dropdown-menu shadow" aria-labelledby="adminMenuDropdown">
              <li><router-link class="dropdown-item" to="/admin/orders">📦 Rendelések</router-link></li>
              <li><router-link class="dropdown-item" to="/admin/products">🛍️ Termékkezelés</router-link></li>
            </ul>
          </li>
        </ul>

        <ul class="navbar-nav">
          <template v-if="!isLoggedIn">
            <li class="nav-item">
              <router-link class="nav-link" to="/login">Bejelentkezés</router-link>
            </li>
            <li class="nav-item">
              <router-link class="btn btn-primary btn-sm mt-1 ms-lg-2" to="/register">Regisztráció</router-link>
            </li>
          </template>

          <template v-else>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                👤 {{ currentUser.name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                <li><router-link class="dropdown-item" to="/profile">Profilom</router-link></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="#" @click.prevent="logout">Kijelentkezés</a></li>
              </ul>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import axios from '@/api/axios'

export default {
  name: 'Navbar',
  data() {
    return {
      isLoggedIn: false,
      currentUser: null
    }
  },
  computed: {
    isAdmin() {
      return this.isLoggedIn && this.currentUser && this.currentUser.role === 'admin';
    }
  },
  mounted() {
    this.checkAuth();
  },
  methods: {
    checkAuth() {
      // A te routered szerint nézzük meg a tokent
      const token = localStorage.getItem('access_token');
      const userStr = localStorage.getItem('user');

      if (token && userStr) {
        this.isLoggedIn = true;
        this.currentUser = JSON.parse(userStr);
      }
    },
    async logout() {
      try {
        await axios.post('/api/logout');
      } catch (error) {
        console.error("Hiba kijelentkezéskor", error);
      } finally {
        // Mindent letörlünk, amit beállítottunk
        localStorage.removeItem('access_token');
        localStorage.removeItem('user_role');
        localStorage.removeItem('user');

        window.location.href = '/';
      }
    }
  }
}
</script>

<style scoped>
.dropdown-menu {
  border-radius: 0.5rem;
  border: none;
}
.navbar-brand {
  letter-spacing: 0.5px;
}
</style>