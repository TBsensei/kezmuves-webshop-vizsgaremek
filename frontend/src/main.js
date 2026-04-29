import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

/**
 * Bootstrap 5 és Bootstrap Icons importálása
 * (npm install bootstrap bootstrap-icons parancsok után használható)
 */
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import 'bootstrap-icons/font/bootstrap-icons.css'

const app = createApp(App)

// Router példány hozzáadása az alkalmazáshoz
app.use(router)

// Az alkalmazás csatolása a DOM-hoz
app.mount('#app')