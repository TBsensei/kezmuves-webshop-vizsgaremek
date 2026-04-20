import { createRouter, createWebHistory } from 'vue-router'

// Alap oldalak beimportálása (Amik rögtön betöltődnek)
// A fájlneveket és mappákat majd igazítsd a sajátjaidhoz, ha máshogy nevezted el őket!
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/auth/LoginView.vue'
import CartView from '../views/CartView.vue'
import AdminOrdersView from '../views/admin/AdminOrdersView.vue'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/aszf',
            name: 'aszf',
            component: () => import('../views/info/AszfView.vue')
        },
        {
            path: '/adatvedelem',
            name: 'adatvedelem',
            component: () => import('../views/info/AdatvedelemView.vue')
        },
        {
            path: '/szallitas',
            name: 'szallitas',
            component: () => import('../views/info/SzallitasView.vue')
        },
        {
            path: '/',
            name: 'home',
            component: HomeView
        },
        {
            path: '/login',
            name: 'login',
            component: LoginView
        },
        {
            path: '/products',
            name: 'products',
            // Lusta betöltés (Lazy loading): Csak akkor tölti be a fájlt, ha a user rákattint
            component: () => import('../views/ProductsView.vue')
        },
        {
            path: '/cart',
            name: 'cart',
            component: CartView
        },

        // ==========================================
        // VÉDETT OLDALAK (A meta mezőkkel jelöljük meg őket)
        // ==========================================
        {
            path: '/admin/orders',
            name: 'admin-orders',
            component: AdminOrdersView,
            meta: { requiresAuth: true }
        },
        {
            path: '/admin/products',
            name: 'admin-products',
            component: () => import('../views/admin/AdminProductsView.vue'),
            meta: { requiresAuth: true, requiresAdmin: true } // Csak ADMIN jogosultsággal!
        },

        // Hibaoldalak
        {
            path: '/not-authorized',
            name: 'not-authorized',
            component: () => import('../views/errors/NotAuthorizedView.vue')
        },
        {
            path: '/:pathMatch(.*)*', // Ha olyan URL-t ír be, ami nem létezik (404)
            name: 'not-found',
            component: () => import('../views/errors/NotFoundView.vue')
        }
    ]
})

// ==========================================
// A "KAPUŐR" (Navigation Guard)
// Minden egyes oldalváltás előtt lefut, és ellenőrzi a jogosultságot!
// ==========================================
router.beforeEach((to, from, next) => {
    // 1. Megnézzük a böngésző memóriáját: van-e tokenünk és mi a szerepkörünk?
    const isAuthenticated = !!localStorage.getItem('access_token');
    const userRole = localStorage.getItem('user_role');

    // 2. Ha az oldal bejelentkezést igényel (requiresAuth), de nincs tokenünk -> Irány a Login!
    if (to.meta.requiresAuth && !isAuthenticated) {
        next({ name: 'login' });
    }
    // 3. Ha az oldal kimondottan Admin jogot kér (requiresAdmin), de a user nem admin -> 403-as hiba
    else if (to.meta.requiresAdmin && userRole !== 'admin') {
        next({ name: 'not-authorized' });
    }
    // 4. Ha az illető be van jelentkezve, de a Login oldalra akar menni -> Dobjuk a Kezdőlapra
    else if (to.name === 'login' && isAuthenticated) {
        next({ name: 'home' });
    }
    // 5. Ha minden rendben van, engedjük tovább a kért oldalra!
    else {
        next();
    }
});

export default router