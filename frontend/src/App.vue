<template>
  <div>
    <Navbar />

    <main class="py-4">
      <router-view></router-view>
    </main>
  </div>
</template>

<script>
// 1. Beimportáljuk a komponenst
import Navbar from './components/Navbar.vue'

export default {
  name: 'App',
  components: {
    // 2. Regisztráljuk, hogy használni tudjuk a <template>-ben
    Navbar
  }
}
</script>

<style>
body {
  background-color: #f8f9fa;
}
</style>