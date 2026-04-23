import axios from 'axios';

const instance = axios.create({
    // A backendetek címe
    baseURL: 'http://localhost:8000',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

// Ez fűzi hozzá a tokent minden kéréshez
instance.interceptors.request.use(config => {
    // A te routered az access_token nevet használja!
    const token = localStorage.getItem('access_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, error => {
    return Promise.reject(error);
});

export default instance;