<template>
    <section class="form-page">
        <div class="form-container">
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
                <Message
                    v-if="message"
                    severity="success"
                    class="mb-4"
                >
                    {{ message }}
                </Message>

                <div class="grid">
                    <div class="col-12 flex flex-column gap-2">
                        <label for="email">Correo electrónico</label>

                        <InputText
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="correo@ejemplo.com"
                            required
                        />
                    </div>

                    <div class="col-12 flex flex-column gap-2">
                        <label for="password">Contraseña</label>

                        <Password
                            id="password"
                            v-model="form.password"
                            placeholder="Ingrese su contraseña"
                            :feedback="false"
                            toggleMask
                            required
                        />
                    </div>
                </div>

                <div class="form-actions flex gap-3">
                    <Button
                        type="submit"
                        label="Ingresar"
                        class="action-dark w-full"
                    />

                    <Button
                        type="button"
                        label="Volver al inicio"
                        class="action-light w-full"
                        @click="router.push('/')"
                    />
                </div>
            </form>
        </div>
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