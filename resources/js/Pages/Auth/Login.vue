<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

const form = useForm({
    UtCodigo: '',
    UtSenha: '',
    remember: false,
})

const showPassword = ref(false)
const isLoading = ref(false)
const toastError = ref('')
const toastSuccess = ref('')

function togglePassword() {
    showPassword.value = !showPassword.value
}

function submit() {
    isLoading.value = true
    form.post('/login', {
        onSuccess: () => {
            toastSuccess.value = 'Login realizado com sucesso!'
            setTimeout(() => {
                toastSuccess.value = ''
            }, 3000)
        },
        onFinish: () => {
            form.reset('UtSenha')
            isLoading.value = false
        },
        onError: (errors) => {
            if (errors.UtCodigo || errors.UtSenha) {
                toastError.value = 'Usuário ou senha inválidos!'
                setTimeout(() => {
                    toastError.value = ''
                }, 3000)
            }
        },
    })
}

function closeToastError() {
    toastError.value = ''
}
function closeToastSuccess() {
    toastSuccess.value = ''
}

import LoginLayout from '../Layouts/LoginLayout.vue';
defineOptions({ layout: LoginLayout });
</script>

<template>
    <Head title="Login" />

    <div class="min-h-screen bg-gradient-to-br from-green-2808 to-green-2808 flex flex-col">
        <div class="flex-grow flex items-center justify-center p-4 relative">
            <!-- Toasts (mantidos como na versão anterior) -->

            <div class="w-full max-w-md bg-white rounded-2xl overflow-hidden shadow-lg mx-4">
                <!-- Parte superior verde -->
                <div class="bg-greenkixi-solid py-4 md:py-6 px-4 flex justify-center items-center">
                    <img src="/public/imagens/LogoKxCredito.png" alt="Logo KixiCrédito"
                        class="h-12 md:h-16 object-contain" />
                </div>

                <!-- Parte inferior com formulário -->
                <div class="p-6 md:p-8 space-y-4 md:space-y-6">
                    <div class="mb-4 md:mb-6 text-center">
                        <div class="flex justify-center">
                            <img src="/public/imagens/smalllogo.png" alt="Logo Utilitario"
                                class="h-8 md:h-10 object-contain" />
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-greenkixi-solid">
                            Kixi<span class="text-greenkixi-solid">Utilitário</span>
                        </h3>
                        <p class="text-gray-500 mt-1 text-xs md:text-sm">Acesso restrito ao sistema</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-3 md:space-y-4">
                        <!-- Campo Usuário -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 md:h-5 md:w-5 text-greenkixi-solid" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </span>
                            <input v-model="form.UtCodigo" id="UtCodigo" type="text" placeholder="Nome do Utilizador"
                                class="pl-9 md:pl-10 text-sm md:text-base w-full border rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                                required autofocus />
                        </div>

                        <!-- Campo Senha -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-4 w-4 md:h-5 md:w-5 text-greenkixi-solid" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </span>
                            <input v-model="form.UtSenha" id="UtSenha" name="UtSenha"
                                :type="showPassword ? 'text' : 'password'" placeholder="Senha"
                                class="pl-9 md:pl-10 text-sm md:text-base w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-200"
                                required autocomplete="current-password" />
                        </div>

                        <!-- Lembrar-me -->
                        <div class="flex items-center justify-between text-xs md:text-sm">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" v-model="form.remember"
                                    class="form-checkbox text-blue-600 h-3 w-3 md:h-4 md:w-4" />
                                <span class="text-gray-700">Lembrar-me</span>
                            </label>

                            <button type="button" @click="togglePassword" class="text-blue-600 hover:underline">
                                {{ showPassword ? 'Esconder' : 'Mostrar' }} senha
                            </button>
                        </div>

                        <!-- Botão Entrar -->
                        <button type="submit"
                            class="w-full flex items-center justify-center bg-orange-400 text-white py-2 px-4 rounded hover:bg-green-900 transition duration-200 text-sm md:text-base">
                            <template v-if="isLoading">
                                <svg class="animate-spin h-4 w-4 md:h-5 md:w-5 mr-2 md:mr-3 text-white"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z" />
                                </svg>
                                Carregando...
                            </template>
                            <template v-else>
                                Entrar
                            </template>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rodapé ajustado para mobile -->
        <footer class="py-3 md:py-4 text-center text-white text-xs md:text-sm">
            &copy; {{ new Date().getFullYear() }} KixiCrédito - Todos os direitos reservados
        </footer>
    </div>
</template>

<style scoped>
body,
html {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
}

@keyframes fade-in-down {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }

    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-down {
    animation: fade-in-down 0.5s ease-out;
}

.to-greenkixi-300 {
    --tw-gradient-to: #08583d var(--tw-gradient-to-position);
}

.text-greenkixi-solid {
    color: #08583d;
}

.from-green-2808 {
    --tw-gradient-from: #28755a var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(5 46 22 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}

.to-green-2808 {
    --tw-gradient-to: #085137 var(--tw-gradient-to-position);
}

.bg-greenkixi-solid {
    background-color: #005b3b;
}
</style>
