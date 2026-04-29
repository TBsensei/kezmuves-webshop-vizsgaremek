<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <router-link class="navbar-brand fw-bold d-flex align-items-center" to="/" @click="closeMenu">
        <i class="bi bi-gem text-info me-2"></i> Kézműves Webshop
      </router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/products" @click="closeMenu">
              <i class="bi bi-shop"></i> Termékek
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/cart" @click="closeMenu">
              <i class="bi bi-cart3"></i> Kosár
            </router-link>
          </li>

          <li class="nav-item dropdown" v-if="isAdmin">
            <a class="nav-link dropdown-toggle text-warning fw-bold" href="#" id="adminMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-gear-fill"></i> Admin
            </a>
            <ul class="dropdown-menu shadow" aria-labelledby="adminMenuDropdown">
              <li>
                <router-link class="dropdown-item fw-bold text-primary" to="/admin/dashboard" @click="closeMenu">
                  <i class="bi bi-speedometer2 me-1"></i> Vezérlőpult
                </router-link>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <router-link class="dropdown-item" to="/admin/orders" @click="closeMenu">
                  <i class="bi bi-box-seam me-1"></i> Rendelések
                </router-link>
              </li>
              <li>
                <router-link class="dropdown-item" to="/admin/products" @click="closeMenu">
                  <i class="bi bi-tags me-1"></i> Termékkezelés
                </router-link>
              </li>
            </ul>
          </li>
        </ul>

        <ul class="navbar-nav">
          <template v-if="!isLoggedIn">
            <li class="nav-item">
              <router-link class="nav-link" to="/login" @click="closeMenu">
                <i class="bi bi-box-arrow-in-right"></i> Bejelentkezés
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="btn btn-primary btn-sm mt-1 ms-lg-2" to="/register" @click="closeMenu">
                <i class="bi bi-person-plus"></i> Regisztráció
              </router-link>
            </li>
          </template>

          <template v-else>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person-circle"></i> {{ currentUser.name }}
              </a>
              <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                <li>
                  <router-link class="dropdown-item" to="/profile" @click="closeMenu">
                    <i class="bi bi-person-lines-fill me-1"></i> Profilom
                  </router-link>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item text-danger" href="#" @click.prevent="logout">
                    <i class="bi bi-box-arrow-right me-1"></i> Kijelentkezés
                  </a>
                </li>
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
    // Beépített menü-becsukó logika
    closeMenu() {
      if (document.activeElement) {
        document.activeElement.blur();
      }

      const navbarCollapse = document.getElementById('navbarNav');
      if (navbarCollapse && navbarCollapse.classList.contains('show')) {
        navbarCollapse.classList.remove('show');
      }
    },
    async logout() {
      this.closeMenu();
      try {
        await axios.post('/api/logout');
      } catch (error) {
        console.error("Hiba kijelentkezéskor", error);
      } finally {
        // Helyi adatok törlése
        localStorage.removeItem('access_token');
        localStorage.removeItem('user_role');
        localStorage.removeItem('user');
        localStorage.removeItem('cart');

        // Az oldal újratöltése garantálja, hogy a komponensek elveszítik a hitelesítési állapotot
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