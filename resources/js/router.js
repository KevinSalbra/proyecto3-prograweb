import { createRouter, createWebHistory } from 'vue-router';

import HomePage from './pages/HomePage.vue';
import CategoryTypePage from './pages/CategoryTypePage.vue';
import CategoryPage from './pages/CategoryPage.vue';
import CatalogPage from './pages/CatalogPage.vue';
import ProductDetailPage from './pages/ProductDetailPage.vue';
import ProductCreatePage from './pages/ProductCreatePage.vue';
import ProductEditPage from './pages/ProductEditPage.vue';
import CartPage from './pages/CartPage.vue';
import CheckoutPage from './pages/CheckoutPage.vue';
import CheckoutSuccessPage from './pages/CheckoutSuccessPage.vue';
import LoginPage from './pages/LoginPage.vue';

const routes = [
    { path: '/', name: 'home', component: HomePage },
    { path: '/vino', name: 'wine', component: CategoryTypePage, props: { type: 'vino' } },
    { path: '/champan', name: 'champagne', component: CategoryTypePage, props: { type: 'champan' } },
    { path: '/categorias/:slug', name: 'category.show', component: CategoryPage },
    { path: '/productos', name: 'products.index', component: CatalogPage },
    { path: '/productos/crear', name: 'products.create', component: ProductCreatePage },
    { path: '/productos/:slug', name: 'products.show', component: ProductDetailPage },
    { path: '/productos/:slug/editar', name: 'products.edit', component: ProductEditPage },
    { path: '/carrito', name: 'cart.index', component: CartPage },
    { path: '/checkout', name: 'checkout.index', component: CheckoutPage },
    { path: '/checkout/exito', name: 'checkout.success', component: CheckoutSuccessPage },
    { path: '/login', name: 'login', component: LoginPage },
];

export default createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 };
    },
});