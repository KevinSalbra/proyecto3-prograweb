import { reactive } from 'vue';
import api from './api';

export const cartStore = reactive({
    items: [],
    subtotal: 0,
    total: 0,
    count: 0,
    loading: false,

    async load() {
        this.loading = true;

        try {
            const response = await api.get('/cart');
            this.apply(response.data);
        } finally {
            this.loading = false;
        }
    },

    async add(product, quantity = 1) {
        const response = await api.post(`/cart/${product.slug}`, {
            quantity,
        });

        this.apply(response.data.cart);

        return response.data;
    },

    async update(items) {
        const response = await api.put('/cart', {
            items,
        });

        this.apply(response.data.cart);

        return response.data;
    },

    async remove(product) {
        const response = await api.delete(`/cart/${product.slug}`);

        this.apply(response.data.cart);

        return response.data;
    },

    async clear() {
        const response = await api.delete('/cart');

        this.apply(response.data.cart);

        return response.data;
    },

    apply(cart) {
        this.items = cart.items || [];
        this.subtotal = cart.subtotal || 0;
        this.total = cart.total || 0;
        this.count = cart.count || 0;
    },
});