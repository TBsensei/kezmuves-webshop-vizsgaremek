<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
      <router-link class="navbar-brand fw-bold" to="/">Kézműves Webshop</router-link>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Kezdőlap</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/cart">🛒 Kosár</router-link>
          </li>
        </ul>

        <ul class="navbar-nav">
          <template v-if="isLoggedIn">
            <li class="nav-item">
              <router-link class="nav-link" to="/admin/products">⚙️ Termékek Kezelése</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/admin/orders">📦 Rendelések</router-link>
            </li>
            <li class="nav-item ms-lg-3">
              <button class="btn btn-outline-danger btn-sm mt-1" @click="logout">Kijelentkezés</button>
            </li>
          </template>

          <template v-else>
            <li class="nav-item">
              <router-link class="btn btn-outline-light btn-sm mt-1 px-3 custom-login-btn" to="/login">Admin bejelentkezés</router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'Navbar',
  data() {
    return {
      isLoggedIn: false
    }
  },
  // Figyeljük, hogy változik-e az útvonal (így rögtön frissül a menü be/kijelentkezéskor)
  watch: {
    $route() {
      this.checkLoginStatus();
    }
  },
  mounted() {
    this.checkLoginStatus();
  },
  methods: {
    checkLoginStatus() {
      // Ellenőrizzük, hogy van-e elmentett token a böngészőben
      this.isLoggedIn = !!localStorage.getItem('access_token');
    },
    logout() {
      // Töröljük a tokent és a felhasználói adatokat
      localStorage.removeItem('access_token');
      localStorage.removeItem('user_role');

      this.isLoggedIn = false;

      // Visszairányítjuk a kezdőlapra
      this.$router.push('/');
    }
  }
}
</script>

<style scoped>
/* Saját animált bejelentkezés gomb */
.custom-login-btn {
  transition: all 0.3s ease-in-out; /* Lágy átmenet minden változásra */
}

.custom-login-btn:hover {
  background-color: #0d6efd; /* Bootstrap kék színre vált */
  border-color: #0d6efd;     /* A keret is kék lesz */
  color: #ffffff !important; /* A szöveg biztosan fehér marad */
  transform: translateY(-2px); /* Picit "felemelkedik" */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Kap egy elegáns árnyékot */
}
</style>