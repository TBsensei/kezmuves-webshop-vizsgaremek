import axios from 'axios'

axios.defaults.baseURL = 'http://localhost:8000'
axios.defaults.withCredentials = true

// ==========================================
// AXIOS INTERCEPTOR (A "Postás")
// Minden egyes hálózati kérés előtt lefut, és ráragasztja a Tokent a fejlésre!
// ==========================================
axios.interceptors.request.use(config => {
    // 1. Kiolvassuk a tokent a memóriából
    const token = localStorage.getItem('access_token');

    // 2. Ha van tokenünk, beletesszük az "Authorization" fejlécbe
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
});

export default axios