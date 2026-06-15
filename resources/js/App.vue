<template>
    <Toast />
    <ConfirmDialog />

    <header class="site-header">
        <div class="top-bar">
            <div class="social-links">
                <a href="mailto:contacto@gonzalezsalazar.cr" aria-label="Correo electrónico">✉</a>
                <a href="#" aria-label="Instagram">◎</a>
                <a href="#" aria-label="Facebook">f</a>
                <a href="#" aria-label="X">𝕏</a>
            </div>

            <div class="user-links">
                <Button
                    label="Iniciar sesión"
                    text
                    class="action-outline"
                    @click="goTo('/login')"
                />
                <span>Español</span>
                <Button
                    :label="`Carrito (${cartStore.count})`"
                    text
                    class="action-outline"
                    @click="goTo('/carrito')"
                />
            </div>
        </div>

        <div class="brand">
            <RouterLink to="/" class="brand-link">
                <img
                    :src="logoUrl"
                    alt="Logo González & Salazar"
                    class="brand-logo-image"
                >

                <span class="brand-name">González & Salazar</span>
                <span class="brand-subtitle">Catálogo de vinos y champanes</span>
            </RouterLink>
        </div>

        <nav class="main-nav">
            <Button label="Vino" text class="action-outline" @click="goTo('/vino')" />
            <Button label="Champán" text class="action-outline" @click="goTo('/champan')" />
            <Button label="Catálogo" text class="action-outline" @click="goTo('/productos')" />
        </nav>
    </header>

    <main>
        <RouterView />
    </main>

    <footer class="site-footer">
        <div>
            <h3>González & Salazar</h3>
            <p>La mejor selección de vinos y champanes de Costa Rica.</p>
        </div>

        <div>
            <h4>Enlaces útiles</h4>
            <RouterLink to="/">Últimos productos</RouterLink>
            <RouterLink to="/vino">Vino</RouterLink>
            <RouterLink to="/champan">Champán</RouterLink>
            <RouterLink to="/productos">Administrar catálogo</RouterLink>
        </div>

        <div>
            <h4>Soporte</h4>
            <p>Contacto</p>
            <p>Preguntas frecuentes</p>
            <p>Políticas del sitio</p>
        </div>
    </footer>
</template>

<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { cartStore } from './services/cartStore';

const logoUrl = '/images/icono-gonzalez-salazar.png';
const router = useRouter();

onMounted(() => {
    cartStore.load();
});

function goTo(path) {
    router.push(path);
}
</script>
