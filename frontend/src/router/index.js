import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),

    // Automatikus görgetés az oldal tetejére útvonalváltáskor
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    },

    routes: [
        // ==========================================
        // PUBLIKUS / INFORMÁCIÓS OLDALAK
        // ==========================================
        {
            path: '/',
            name: 'home',
            component: () => import('../views/HomeView.vue')
        },
        {
            path: '/products',
            name: 'products',
            component: () => import('../views/ProductsView.vue')
        },
        {
            path: '/cart',
            name: 'cart',
            component: () => import('../views/CartView.vue')
        },
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

        // ==========================================
        // HITELESÍTÉSI OLDALAK (Csak kijelentkezve)
        // ==========================================
        {
            path: '/login',
            name: 'login',
            component: () => import('../views/auth/LoginView.vue'),
            meta: { requiresGuest: true }
        },
        {
            path: '/register',
            name: 'register',
            component: () => import('../views/auth/RegisterView.vue'),
            meta: { requiresGuest: true }
        },

        // ==========================================
        // VÉDETT OLDALAK (Vásárlói profil)
        // ==========================================
        {
            path: '/profile',
            name: 'profile',
            component: () => import('../views/ProfileView.vue'),
            meta: { requiresAuth: true }
        },

        // ==========================================
        // ADMINISZTRÁTORI OLDALAK
        // ==========================================
        {
            path: '/admin/orders',
            name: 'admin-orders',
            component: () => import('../views/admin/AdminOrdersView.vue'),
            meta: { requiresAuth: true, requiresAdmin: true }
        },
        {
            path: '/admin/products',
            name: 'admin-products',
            component: () => import('../views/admin/AdminProductsView.vue'),
            meta: { requiresAuth: true, requiresAdmin: true }
        },
        {
            path: '/admin/dashboard',
            name: 'admin-dashboard',
            component: () => import('../views/admin/AdminDashboardView.vue'),
            meta: { requiresAuth: true, requiresAdmin: true }
        },

        // ==========================================
        // HIBAOLDALAK (403, 404)
        // ==========================================
        {
            path: '/not-authorized',
            name: 'not-authorized',
            component: () => import('../views/errors/NotAuthorizedView.vue')
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'not-found',
            component: () => import('../views/errors/NotFoundView.vue')
        }
    ]
})

// ==========================================
// GLOBÁLIS JOGOSULTSÁGKEZELÉS (Navigation Guard)
// ==========================================
router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem('access_token');
    const userRole = localStorage.getItem('user_role');

    if (to.meta.requiresAuth && !isAuthenticated) {
        // Ha hitelesítés kell, de nincs token
        next({ name: 'login' });
    }
    else if (to.meta.requiresAdmin && userRole !== 'admin') {
        // Ha admin jog kell, de a felhasználó nem admin
        next({ name: 'not-authorized' });
    }
    else if (to.meta.requiresGuest && isAuthenticated) {
        // Ha csak vendégek láthatják (pl. login), de már be van jelentkezve
        next({ name: 'home' });
    }
    else {
        // Ha minden jogosultság rendben van, mehet tovább
        next();
    }
});

export default router