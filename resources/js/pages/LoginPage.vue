<template>
    <section class="form-page">
        <div class="form-header">
            <p class="breadcrumb">
                <RouterLink to="/">Inicio</RouterLink>
                <span>›</span>
                <span>Iniciar sesión</span>
            </p>

            <h1>Iniciar sesión</h1>
            <p>
                Acceda a su cuenta para consultar sus pedidos, guardar productos favoritos
                y gestionar su información de compra.
            </p>
        </div>

        <form class="form-card" @submit.prevent="showMessage">
            <Message v-if="message" severity="success" class="mb-3">
                {{ message }}
            </Message>

            <div class="grid">
                <div class="col-12 flex flex-column gap-2">
                    <label>Correo electrónico</label>
                    <InputText
                        v-model="form.email"
                        type="email"
                        placeholder="correo@ejemplo.com"
                        required
                    />
                </div>

                <div class="col-12 flex flex-column gap-2">
                    <label>Contraseña</label>
                    <Password
                        v-model="form.password"
                        placeholder="Ingrese su contraseña"
                        :feedback="false"
                        toggleMask
                        required
                    />
                </div>
            </div>

            <div class="form-actions flex flex-wrap gap-3">
                <Button type="submit" label="Ingresar" class="action-dark" />

                <Button
                    type="button"
                    label="Volver"
                    class="action-light"
                    @click="router.push('/')"
                />
            </div>
        </form>
    </section>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

const message = ref('');
const router = useRouter();

const form = reactive({
    email: '',
    password: '',
});

function showMessage() {
    message.value = 'El acceso de clientes estará disponible próximamente.';
}
</script>
