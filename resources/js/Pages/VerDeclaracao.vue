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
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mt-8 mb-6 gap-4">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-file text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Ver Requisição
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Solicitação de Declarações Negativas</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="/declacaongtv"
                    class="border border-purple-600 text-purple-600 hover:bg-purple-700 hover:text-white p-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-list text-purple-600 text-xl"></i>
                    Listar Requisições
                </a>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <div class="p-4">
                    <div class="grid grid-cols-12 gap-5 pb-4">
                        <div class="lg:col-span-8 col-span-8">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Detalhes da Requisição</h2>
                        </div>
                        <div class="flex justify-end lg:col-span-4 col-span-4 gap-2">
                            <button
                                v-if="declaracao.descricao_estado != 'Aprovado' && declaracao.descricao_estado != 'Recusado' && can('reject_declaracao')"
                                @click="abrirModal"
                                class="bg-orange-400 hover:bg-orange-500 text-white h-10 px-4 rounded-lg gap-2">
                                <i class="fas fa-times text-white"></i>
                                Recusar
                            </button>
                            <button v-if="declaracao.descricao_estado == 'Registado' && can('aprove_declaracao')"
                                @click="aprovarRequisicao"
                                class="bg-green-600 hover:bg-green-700 text-white h-10 px-4 rounded-lg gap-2">
                                <i class="fas fa-check text-white"></i>
                                Aprovar
                            </button>
                            <a target="_blank" v-if="declaracao.descricao_estado == 'Aprovado' && declaracao.isPago"
                                :href="`/imprimir-declaracao/${declaracao.id}`"
                                class="bg-green-600 hover:bg-green-700 text-white h-10 p-2 rounded-lg gap-2">
                                <i class="fas fa-print text-white"></i>
                                Imprimir
                            </a>
                            <span v-if="declaracao.descricao_estado == 'Aprovado' && !declaracao.isPago"
                                class="border border-orange-400 text-orange-400 h-10 p-2 rounded-lg gap-2">
                                <i class="fas fa-money-bill text-orange-400"></i>
                                Aguardando o Pagamento
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-5">
                        <div class="lg:col-span-7 col-span-7 bg-gray-100 p-2 rounded-lg">
                            <div class="grid grid-cols-12 gap-5">
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">LNR:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.lnr }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Saving:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.saving }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Estado:</p>
                                    <span :class="declaracao.color"
                                        class="px-2 mt-2 rounded-full border border-1 border-gray-300 text-base font-medium">{{
                                            declaracao.descricao_estado }}</span>
                                </div>
                                <div class="lg:col-span-12 col-span-12">
                                    <p class="text-sm text-gray-600">Nome:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.nome }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Documento:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.documento }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Telefone:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.telefone }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Banco:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.BaNome }}</p>
                                </div>
                                <div v-if="declaracao.comentario" class="lg:col-span-12 col-span-12">
                                    <p class="text-sm text-gray-600">Comentário:</p>
                                    <p class="text-base font-bold text-green-600">
                                        <i class="fa fa-info-circle"></i> {{ declaracao.comentario }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="lg:col-span-5 col-span-5 bg-gray-100 p-2 rounded-lg">
                            <iframe v-if="declaracao" :src="declaracao.ficheiro"
                                class="mt-4 w-full h-64 border rounded-lg">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ModalRecusarDeclaracao ref="modalRecusarDeclaracao" v-if="showModalRecusarDeclaracao" @close="fecharModal"
        @save="recusarRequisicao" />
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import ModalSolicitarDeclaracao from './Layouts/components/ModalSolicitarDeclaracao.vue'
import ModalRecusarDeclaracao from './Layouts/components/ModalRecusarDeclaracao.vue'
import { GlobalPermissions } from '../Components/GlobalPermissions'
const { can } = GlobalPermissions()

// Props
const props = defineProps({
    declaracao: Object,
})

// Modal Recusar
const showModalRecusarDeclaracao = ref(false)
const modalRecusarDeclaracao = ref(null)

const abrirModal = () => {
    showModalRecusarDeclaracao.value = true
}

const fecharModal = () => showModalRecusarDeclaracao.value = false

const recusarRequisicao = async (formValue) => {
    try {

        const formData = new FormData()
        formData.append('id', props.declaracao.id) // Adiciona o ID da declaração para identificação no backend
        Object.entries(formValue).forEach(([key, value]) => {
            if (value) formData.append(key, value)
        })

        await router.post('/recusar-declaracao', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onSuccess: () => {
                fecharModal();
            }
        })
    } catch (error) {
        console.error('Erro ao gerar referência:', error)
    }
}

const aprovarRequisicao = async () => {
    try {
        Swal.fire({
            title: "Tem a certeza?",
            text: "A requisição será aprovada!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Aprovar"
        }).then((result) => {
            if (result.isConfirmed) {

                router.post('/aprovar-declaracao/' + props.declaracao.id, {
                    onSuccess: () => {
                        // Lógica após aprovação, se necessário
                    }
                });

            };
        });


    } catch (error) {
        console.error('Erro ao aprovar declaração:', error)
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