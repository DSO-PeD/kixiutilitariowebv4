<template>

    <Head title="Declarações" />

    <div class="container mx-auto py-6 max-w-full">
        <!-- Alertas de notificação -->
        <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $page.props.flash.success }}
            </div>
        </div>

        <div v-if="$page.props.flash.error" class="alert alert-danger mb-4">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ $page.props.flash.error }}
            </div>
        </div>

        <div v-if="$page.props.errors && Object.keys($page.props.errors).length" class="alert alert-danger mb-4">
            <div v-for="(messages, field) in $page.props.errors" :key="field">
                <div v-for="message in messages" :key="message">
                    {{ message }}
                </div>
            </div>
        </div>

        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-users text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Utilizadores
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Lista de Utilizadores Registados</p>
                </div>
            </div>

            <!--<div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button v-if="can('create_declaracao')" class="btn btn-outline-primary-pgr flex items-center gap-2"
                    @click="abrirModalGerarRefManual">
                    <i class="fas fa-file text-purple-600 text-xl"></i>
                    Nova Requisição
                </button>
            </div>-->

        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <!-- Tabela -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">

            <div>
                <div class="relative w-1/4">
                    <input v-model="formFiltro.nome"
                        class="form-input w-full pl-3 pr-10 h-10 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                        placeholder="Ex.: Dionísio André" required />
                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8 mb-4">
                <div v-for="(utilizador, index) in utilizadores?.data || []" :key="utilizador.id" class="max-w-sm">
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">

                        <!-- Avatar + Info -->
                        <div class="flex items-center gap-3 mb-4">
                            <div
                                class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center text-blue-700 dark:text-blue-300 font-medium">
                                <p class="ml-2 text-2xl">{{ getSigla(utilizador.UtNome) }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">{{ utilizador.UtNome }}</p>
                                <p class="text-xs text-gray-500 font-mono mt-0.5">{{ utilizador.UtCodigo }}</p>
                            </div>
                        </div>

                        <!-- Botão -->
                        <div class="border-t border-gray-100 pt-3 flex items-center justify-between">

                            <div class="flex items-center gap-1.5 rounded-xl px-1" :class="utilizador.user_id ? 'bg-green-400':'bg-red-400'">
                                <span class="w-2.5 h-2.5 rounded-full inline-block"
                                    :class="utilizador.user_id ? 'bg-green-500' : 'bg-red-500'">
                                </span>
                                <span class="text-xs text-white font-medium">
                                    {{ utilizador.user_id ? 'Logado' : 'Não logado' }}
                                </span>
                            </div>

                            <a :href="`/verUtilizador/${utilizador.UtCodigo}`"
                                class="text-xs px-4 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
                                <i class="fas fa-eye text-blue-400"></i> Ver utilizador
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <!-- Paginação -->
            <div class="pt-2 flex items-center justify-end gap-4">
                <!-- Previous -->
                <button @click="goTo(utilizadores.prev_page_url)" :disabled="!utilizadores.prev_page_url"
                    class="px-3 py-1.5 border rounded" :class="!utilizadores.prev_page_url
                        ? 'opacity-50 cursor-not-allowed'
                        : 'hover:bg-gray-100'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <!-- Page Info -->
                <span class="text-sm text-gray-700 py-1.5 px-3 bg-gray-50 border rounded">
                    Página {{ utilizadores.current_page }} de {{ utilizadores.last_page }}
                </span>

                <!-- Next -->
                <button @click="goTo(utilizadores.next_page_url)" :disabled="!utilizadores.next_page_url"
                    class="px-3 py-1.5 border rounded" :class="!utilizadores.next_page_url
                        ? 'opacity-50 cursor-not-allowed'
                        : 'hover:bg-gray-100'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                        </path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { GlobalPermissions } from '../Components/GlobalPermissions'
const { can } = GlobalPermissions()

// Props
const props = defineProps({
    utilizadores: Array,
})

const getSigla = (nome) => {
    const partes = nome.trim().split(' ').filter(n => n)
    if (partes.length === 1) return partes[0][0].toUpperCase()
    return (partes[0][0] + partes[partes.length - 1][0]).toUpperCase()
}

const formFiltro = ref({
    nome: props.formFiltro?.nome || '',
});

watch(() => formFiltro.value.nome, () => {
    aplicarFiltros();
})

// Chama next page na paginação sem recarregar a página 
const goTo = (url) => {
    router.visit(url, {
        preserveState: true,
    });
}

const aplicarFiltros = () => {
    router.get('/users', formFiltro.value, {
        preserveState: true,
        replace: true,
    })
}
</script>

<style scoped>
/* Sistema de Cores */
:root {
    --color-primary: #08583d;
    --color-primary-light: #0c7a5a;
    --color-secondary: #6b7280;
    --color-success: #10b981;
    --color-warning: #f59e0b;
    --color-danger: #ef4444;
    --color-info: #3b82f6;
}

/* Componentes Estilizados */
.alert {
    @apply p-4 rounded-lg border-l-4;
}

.alert-success {
    @apply bg-green-50 text-green-800 border-green-500;
}

.alert-danger {
    @apply bg-red-50 text-red-800 border-red-500;
}

.alert-warning {
    @apply bg-yellow-50 text-yellow-800 border-yellow-500;
}

.alert-info {
    @apply bg-blue-50 text-blue-800 border-blue-500;
}

.btn {
    @apply px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center justify-center;
}

.btn-primary {
    @apply bg-green-600 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2;
}

.btn-outline-primary {
    @apply border border-blue-500 text-blue-500 hover:bg-blue-50;
}

.btn-outline-primary-pgr {
    @apply border border-purple-500 text-purple-500 hover:bg-purple-500 hover:text-purple-50;
}

.btn-primary-filter {
    @apply bg-green-900 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-900 focus:ring-offset-2;
}

.btn-outline {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
}

.btn-outline-secondary {
    @apply border border-gray-300 text-gray-700 hover:bg-gray-50;
}

.btn-outline-excel {
    @apply border border-green-600 text-green-600 hover:bg-green-50;
}

.btn-outline-success {
    @apply border border-green-500 text-green-500 hover:bg-green-50;
}

.btn-sm {
    @apply px-3 py-1 text-sm;
}

/* Componentes de Formulário */
.select-input {
    @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white;
}

.checkbox-label {
    @apply flex items-center cursor-pointer p-3 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100;
}

.checkbox-input {
    @apply sr-only;
}

.checkbox-custom {
    @apply w-4 h-4 border border-gray-300 rounded mr-3 relative;
}

.checkbox-input:checked+.checkbox-custom {
    @apply bg-green-600 border-green-600;
}

.checkbox-input:checked+.checkbox-custom::after {
    content: '';
    @apply absolute inset-0.5 bg-white rounded-sm;
}

/* Cards e Efeitos */
.card-hover {
    @apply transition-all duration-200 hover:shadow-md hover:-translate-y-0.5;
}

.badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.badge-warning {
    @apply bg-yellow-100 text-yellow-800;
}

/* Tooltips */
.tooltip {
    @apply absolute z-10 left-0 mt-2 w-64 bg-white shadow-lg rounded-lg border border-gray-200 p-3;
}

/* Animações */
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .container {
        @apply px-4;
    }

    .btn {
        @apply px-3 py-1.5 text-sm;
    }

    /* Otimizações para mobile */
    .hidden-mobile {
        display: none;
    }
}

/* Melhorias de Performance */
* {
    box-sizing: border-box;
}

/* Scroll personalizado */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    @apply bg-gray-100;
}

::-webkit-scrollbar-thumb {
    @apply bg-gray-300 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-400;
}

/* Estados de foco melhorados */
button:focus,
input:focus,
select:focus {
    @apply outline-none ring-2 ring-green-500 ring-offset-2;
}

/* Transições suaves */
.transition-all {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
