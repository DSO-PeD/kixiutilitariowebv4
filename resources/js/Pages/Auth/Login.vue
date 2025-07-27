<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

import LoginLayout from '../Layouts/LoginLayout.vue';
import LogoKxCredito from '../../../../public/imagens/LogoKxCredito.png';
import SmallLogo from '../../../../public/imagens/smalllogo.png';

defineOptions({ layout: LoginLayout });

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


</script>

<template>

    <Head title="Login" />

    <div class="min-h-screen bg-gradient-to-br from-green-2808 to-green-2808 flex flex-col">
        <div class="flex-grow flex items-center justify-center p-4 relative">
            <!-- Toasts (mantidos como na versão anterior) -->
            <div class="fixed top-4 right-4 z-50 space-y-2">
                <!-- Toast de Sucesso -->
                <div v-if="toastSuccess"
                    class="animate-fade-in-down bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ toastSuccess }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="closeToastSuccess">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Fechar</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>

                <!-- Toast de Erro -->
                <div v-if="toastError"
                    class="animate-fade-in-down bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ toastError }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3" @click="closeToastError">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <title>Fechar</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>
            </div>


            <div class="w-full max-w-md bg-white rounded-2xl overflow-hidden shadow-lg mx-4">
                <!-- Parte superior verde -->
                <div class="bg-greenkixi-solid py-4 md:py-6 px-4 flex justify-center items-center">

                    <img :src="LogoKxCredito" alt="Logo KixiCrédito" class="h-12 md:h-16 object-contain" />
                </div>

                <!-- Parte inferior com formulário -->
                <div class="p-6 md:p-8 space-y-4 md:space-y-6">
                    <div class="mb-4 md:mb-6 text-center">
                        <div class="flex justify-center">
                          <img :src="SmallLogo" alt="Logo Utilitario" class="h-8 md:h-10 object-contain" />
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

                            <button type="button" @click="togglePassword" class="text-blue-600 hover:underline flex">




                                <svg v-if="showPassword == false" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <svg v-if="showPassword == true" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />

                                </svg>




                                &ThinSpace;
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
