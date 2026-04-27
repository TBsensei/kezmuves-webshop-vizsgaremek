<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <router-link class="navbar-brand fw-bold" to="/" @click="closeMenu">💎 Kézműves Webshop</router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/products" @click="closeMenu">Termékek</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/cart" @click="closeMenu">🛒 Kosár</router-link>
          </li>

          <li class="nav-item dropdown" v-if="isAdmin">
            <a class="nav-link dropdown-toggle text-warning fw-bold" href="#" id="adminMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              ⚙️ Admin
            </a>
            <ul class="dropdown-menu shadow" aria-labelledby="adminMenuDropdown">
              <li><router-link class="dropdown-item" to="/admin/orders" @click="closeMenu">📦 Rendelések</router-link></li>
              <li><router-link class="dropdown-item" to="/admin/products" @click="closeMenu">🛍️ Termékkezelés</router-link></li>
            </ul>
          </li>
        </ul>

        <ul class="navbar-nav">
          <template v-if="!isLoggedIn">
            <li class="nav-item">
              <router-link class="nav-link" to="/login" @click="closeMenu">Bejelentkezés</router-link>
            </li>
            <li class="nav-item">
              <router-link class="btn btn-primary btn-sm mt-1 ms-lg-2" to="/register" @click="closeMenu">Regisztráció</router-link>
            </li>
          </template>

          <template v-else>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                👤 {{ currentUser.name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                <li><router-link class="dropdown-item" to="/profile" @click="closeMenu">Profilom</router-link></li>
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
      const token = localStorage.getItem('access_token');
      const userStr = localStorage.getItem('user');

      if (token && userStr) {
        this.isLoggedIn = true;
        this.currentUser = JSON.parse(userStr);
      }
    },
    // EZ AZ ÚJ FÜGGVÉNY: Beépített menü-becsukó varázslat
    closeMenu() {
      // 1. Ha egy lenyíló menüben vagyunk, levesszük róla a fókuszt, így azonnal becsukódik
      if (document.activeElement) {
        document.activeElement.blur();
      }

      // 2. Ha mobilon nyitva van a nagy menü, azt is összecsukja
      const navbarCollapse = document.getElementById('navbarNav');
      if (navbarCollapse && navbarCollapse.classList.contains('show')) {
        navbarCollapse.classList.remove('show');
      }
    },
    async logout() {
      this.closeMenu(); // Kijelentkezésnél is csukjuk be
      try {
        await axios.post('/api/logout');
      } catch (error) {
        console.error("Hiba kijelentkezéskor", error);
      } finally {
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