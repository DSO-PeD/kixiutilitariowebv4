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
                    <i class="fas fa-user text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Utilizadores
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Ver Perfil de Utilizador</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
            <div class="flex flex-col items-center justify-center">
                <i class="fa fa-user-circle text-8xl text-gray-400"></i>
                <h1 class="text-3xl font-medium">
                    {{ utilizador.UtNome }}
                    <hr>
                </h1>
                <hr>
                <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 mt-4 items-center justify-start">
                    <span class="text-gray-400 text-sm text-right">🪪 Código:</span>
                    <span class="font-medium">{{ utilizador.UtCodigo }}</span>

                    <span class="text-gray-400 text-sm text-right">💼 Função:</span>
                    <span class="font-medium">{{ utilizador.UtFuncao }}</span>

                    <span class="text-gray-400 text-sm text-right">🏦 Agência: </span>
                    <span class="font-medium">{{ utilizador.OfNombre }}</span>

                    <span class="text-gray-400 text-sm text-right">Estado</span>
                    <span
                        :class="utilizador.activo == 1 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700'"
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium w-fit">
                        {{ utilizador.activo ? '● Activo' : '● Desactivado' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4 mt-2">
            <div class="lg:col-span-6 col-span-12 sm:col-span-12 bg-orange-50 rounded-xl shadow-2xl px-4 py-2 mt-2">

                <div class="relative w-48 mt-4">
                    <i class="fa fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="ex.: Criar..." v-model="pesquisa"
                        class="w-full pl-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5 pr-10">
                </div>

                <div class="mt-2 rounded-lg p-4 h-48 overflow-y-auto space-y-1">
                    <div v-for="perm in filteredPermissions" :key="perm.id" class="flex items-center gap-2">
                        <input type="checkbox" :value="perm.id" v-model="selecionadoPermissoes" />
                        <span class="text-gray-500">{{ perm.label }}</span>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-1 col-span-12 sm:col-span-12 px-4 py-2">
                <div class="flex flex-col items-center justify-center mt-24 gap-2">
                    <button @click="atribuirPermissao" title="Adicionar Permissão"
                        class="bg-green-300 hover:bg-green-400 rounded-lg py-2 px-3">>></button>
                    <button @click="removerPermissao" title="Remover Permissão"
                        class="bg-orange-300 text-orange-900 font-medium hover:bg-orange-400 rounded-lg py-2 px-3">
                        << </button>
                </div>
            </div>
            <div class="lg:col-span-5 col-span-12 sm:col-span-12 bg-green-50 rounded-xl shadow-2xl px-4 py-2 mt-2">

                <div class="relative w-48 mt-4">
                    <i class="fa fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="ex.: Criar..." v-model="pesquisaAtr"
                        class="w-full pl-2 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 py-1.5 pr-10">
                </div>

                <div class="mt-2 rounded-lg p-4 h-48 overflow-y-auto space-y-1">
                    <div v-for="(permissao, index) in filterUserPermissions" class="flex items-center gap-2">
                        <input type="checkbox" :value="permissao.id" v-model="salvoPermissoes" />
                        <span class="text-gray-500">{{ permissao.label }}</span>
                    </div>
                </div>
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
    utilizador: Object,
    permissions: Array
})

/** Filtrar permissões de um utilizador */
const pesquisaAtr = ref('');

const filterUserPermissions = computed(() => {
    if (!pesquisaAtr.value) return props.utilizador.permissoes;

    return props.utilizador.permissoes.filter(perfil =>
        perfil.label?.toLowerCase().includes(pesquisaAtr.value.toLowerCase())
    );
});

/** Filtrar geral permissões */
const pesquisa = ref('');
const filteredPermissions = computed(() => {
    const termo = pesquisa.value?.toLowerCase().trim();

    return props.permissions
        .filter(fp => !props.utilizador.permissoes.some(up => up.id === fp.id))
        .filter(fp => {
            if (!termo) return true;
            return fp?.label?.toLowerCase().includes(pesquisa.value);
        });
});

// Atribui permissões a um user
const selecionadoPermissoes = ref([]);
const atribuirPermissao = async () => {
    if (selecionadoPermissoes.value.length < 1) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Ups!!! Nenhuma permissão selecionada!"
        });
        return;
    }

    try {
        const payload = {
            permission_ids: selecionadoPermissoes.value
        };

        router.post('/atribuir-permission/' + props.utilizador.UtCodigo, payload, {
            onSuccess: () => {
                selecionadoPermissoes.value = [];
            }
        });

    } catch (error) {

    } finally {

    }
}

//Remove permissões de um user
const salvoPermissoes = ref([]);
const removerPermissao = async () => {
    if (salvoPermissoes.value.length < 1) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Ups!!! Escolha a permissão que deseja remover."
        });
        return;
    }

    try {
        const payload = {
            permission_ids: salvoPermissoes.value
        };

        router.post('/remover-permission/' + props.utilizador.UtCodigo, payload, {
            onSuccess: () => {
                salvoPermissoes.value = [];
            }
        });

    } catch (error) {

    } finally {

    }
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
