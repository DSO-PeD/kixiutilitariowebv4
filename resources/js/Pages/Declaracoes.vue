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
                    <i class="fas fa-file text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Declarações Negativas
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Solicitação de Declarações Negativas</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button class="btn btn-outline-primary-pgr flex items-center gap-2" @click="abrirModalGerarRefManual">
                    <i class="fas fa-file text-purple-600 text-xl"></i>
                    Nova Requisição
                </button>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <!-- Filtros Avançados -->
        <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700">Filtros de Pesquisa</h2>
                <button @click="toggleFiltros"
                    class="text-green-600 hover:text-green-800 text-sm font-medium flex items-center">
                    {{ filtrosVisiveis ? 'Ocultar Filtros' : 'Mostrar Filtros' }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            :d="filtrosVisiveis ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'" />
                    </svg>
                </button>
            </div>

            <div v-if="filtrosVisiveis" class="transition-all duration-300 ease-in-out">
                <!-- Filtros superiores -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    <!-- Loan Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número do Empréstimo(Loan
                            Nr)</label>
                        <div class="relative group">
                            <input v-model="formFiltro.lnr"
                                class="form-input w-full pl-3 pr-2 h-10 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                placeholder="Ex.: AC/12345" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <div class="relative group">
                            <input v-model="formFiltro.nome"
                                class="form-input w-full pl-3 pr-2 h-10 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                placeholder="Ex.: Juliana António" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Documento Nº</label>
                        <div class="relative group">
                            <input v-model="formFiltro.documento"
                                class="form-input w-full pl-3 pr-2 h-10 text-sm bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                placeholder="Ex.: 0000000LA0123" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Banco</label>
                        <div class="relative group">
                            <select v-model="formFiltro.banco" required
                                class="form-select w-full pl-4 pr-10 h-10 bg-white text-gray-500 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md">
                                <option :value="null" disabled>Selecione o banco</option>
                                <option v-for="banco in bancos.filter(b => !['KIX'].includes(b.BaSigla))"
                                    :key="banco.BaCodigo" :value="banco.BaCodigo">
                                    {{ banco.BaNome }}
                                </option>
                            </select>                            
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <div class="relative group">
                            <select v-model="formFiltro.estado" required
                                class="form-select w-full pl-4 pr-10 h-10 bg-white text-gray-500 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md">
                                <option :value="null" disabled>Selecione o estado</option>
                                <option v-for="estado in estados" :key="estado.id" :value="estado.id">
                                    {{ estado.descricao_estado }}
                                </option>
                            </select>                            
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div
                    class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4 border-t border-gray-200">
                    <button @click="resetarFiltros" class="btn btn-outline-secondary flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Limpar Filtros
                    </button>
                    <button @click="aplicarFiltros" class="btn btn-primary-filter flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full">
                    <thead class="bg-gray-50 ">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>

                                    Registado
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-barcode mr-2 text-gray-500 text-md"></i>
                                    Loan Number
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    Saving
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                    Nome Cliente
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        strokeWidth={1.5} stroke="currentColor" class="w-4 h-4">
                                        <path strokeLinecap="round" strokeLinejoin="round"
                                            d="M14.25 9.75v-4.5m0 4.5h4.5m-4.5 0 6-6m-3 18c-8.284 0-15-6.716-15-15V4.5A2.25 2.25 0 0 1 4.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.054.902-.417 1.173l-1.293.97a1.062 1.062 0 0 0-.38 1.21 12.035 12.035 0 0 0 7.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 0 1 1.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 0 1-2.25 2.25h-2.25Z" />
                                    </svg>
                                    Telefone do Cliente
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-id-card mr-2 text-gray-500 text-xs"></i>
                                    Documento Nº
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-question-circle mr-2 text-gray-500 text-xs"></i>
                                    Estado
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <i class="fas fa-building mr-2 text-gray-500 text-xs"></i>
                                    Banco Destino
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">

                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(declaracao,index) in declaracoes?.data || []" :key="declaracao.id">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ index + 1 + ((declaracoes.current_page - 1) * declaracoes.per_page) }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ declaracao.created_at }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ declaracao.lnr }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ declaracao.saving }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-700">
                                {{ declaracao.nome }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-500">
                                {{ declaracao.telefone }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ declaracao.documento }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                <span :class="declaracao.color" class="px-2 py-1 rounded-full text-xs">
                                    {{ declaracao.descricao_estado }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ declaracao.BaNome }}
                            </td>
                            <td class="flex justify-end px-4 py-2 whitespace-nowrap text-sm font-semibold text-green-600">
                                <a :href="`/verDeclaracao/${declaracao.id}`" target="_blank"
                                    class="hover:underline hover:bg-green-200 bg-green-100 text-green-800 py-1 px-3 rounded-md text-sm">
                                    <i class="fas fa-eye text-green-600"></i>
                                    Abrir Requisição
                                </a>
                            </td>
                        </tr>
                        <tr v-if="declaracoes.data?.length === 0">
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <p class="text-sm">Nenhuma declaração solicitada</p>
                                    <p class="text-xs text-gray-400 mt-1">Tente ajustar os filtros de pesquisa</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Paginação -->
            <div class="pt-2 flex items-center justify-end gap-4">
                <!-- Previous -->
                <button @click="goTo(declaracoes.prev_page_url)" :disabled="!declaracoes.prev_page_url"
                    class="px-3 py-1.5 border rounded" :class="!declaracoes.prev_page_url
                        ? 'opacity-50 cursor-not-allowed'
                        : 'hover:bg-gray-100'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>

                <!-- Page Info -->
                <span class="text-sm text-gray-700 py-1.5 px-3 bg-gray-50 border rounded">
                    Página {{ declaracoes.current_page }} de {{ declaracoes.last_page }}
                </span>

                <!-- Next -->
                <button @click="goTo(declaracoes.next_page_url)" :disabled="!declaracoes.next_page_url"
                    class="px-3 py-1.5 border rounded" :class="!declaracoes.next_page_url
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

    <ModalSolicitarDeclaracao ref="modalGerarDeclaracao" v-if="showModalDeclaracao" @close="fecharModalDeclaracao"
        @save="guardarDeclaracao" :bancos="$page.props.bancos" v-model="novaDeclaracao" />
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

// Componentes
import ModalSolicitarDeclaracao from './Layouts/components/ModalSolicitarDeclaracao.vue'

// Props
const props = defineProps({
    bancos: Array,
    estados: Array,
    declaracoes: Array,
})

// Refs
const showModalDeclaracao = ref(false)
const modalGerarDeclaracao = ref(null)

const abrirModalGerarRefManual = () => {
    showModalDeclaracao.value = true
}

const fecharModalDeclaracao = () => showModalDeclaracao.value = false

const guardarDeclaracao = async (formValue) => {
    try {

        const formData = new FormData()
        Object.entries(formValue).forEach(([key, value]) => {
            if (value) formData.append(key, value)
        })

        await router.post('/guardar-declaracao', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onSuccess: () => {
                fecharModalDeclaracao();
            }
        })
    } catch (error) {
        console.error('Erro ao gerar referência:', error)
    }
}

// Função para alternar a visibilidade dos filtros
const filtrosVisiveis = ref(true)
const toggleFiltros = () => {
    filtrosVisiveis.value = !filtrosVisiveis.value
}

const formFiltro = ref({
    lnr: props.formFiltro?.lnr || '',
    nome: props.formFiltro?.nome || '',
    documento: props.formFiltro?.documento || '',
    estado: props.formFiltro?.estado || null,
    banco: props.formFiltro?.banco || null
});

// Chama next page na paginação sem recarregar a página 
const goTo = (url) => {
    router.visit(url, {
        preserveState: true,
    });
}

const aplicarFiltros = () => {
    router.get('/declacaongtv', formFiltro.value, {
        preserveState: true,
        replace: true,
    })
}

const resetarFiltros = () => {
    formFiltro.value = {
        lnr: '',
        nome: '',
        documento: '',
        estado: null,
        banco: null
    }

    router.get('/declacaongtv', formFiltro.value, {
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
