<template>
  <div id="app" class="d-flex flex-column min-vh-100">
    <Navbar />

    <main class="flex-grow-1">
      <router-view />
    </main>

    <Footer />
  </div>
</template>

<script>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue' // Footer beimportálása

export default {
  name: 'App',
  components: {
    Navbar,
    Footer // Komponens regisztrálása
  }
}
</script>

<style>
/* Ide jöhetnek a globális stílusok, ha vannak */
body {
  background-color: #f8f9fa; /* Kellemes, világosszürke háttér az egész oldalnak */
}
</style>