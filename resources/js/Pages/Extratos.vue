<template>
    <Head title="Extratos" />

    <div class="container mx-auto px-4 py-6 max-w-full">
        <!-- Alertas -->
        <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
            {{ $page.props.flash.success }}
        </div>

        <div v-if="$page.props.flash.error" class="alert alert-danger mb-4">
            {{ $page.props.flash.error }}
        </div>

        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-green-950">Aplicações de Desembolsos</h1>
                <p class="text-sm text-gray-600">Visualização e gestão de desembolsos existentes</p>
            </div>

            <div v-if="sistemaAberto" class="flex flex-col sm:flex-row gap-2">
                <button class="btn btn-primary flex items-center gap-2" @click="showModal = true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Efectuar Nova Aplicação
                </button>
            </div>
        </div>

        <!-- Card Principal -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <!-- Cabeçalho do Card -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                <h2 class="text-lg font-semibold text-gray-700">Lista de Desembolsos</h2>

                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <button class="btn btn-outline-secondary flex items-center gap-2" @click="showModalData = true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        Consultar por Data
                    </button>
                    <button class="btn btn-outline-excel flex items-center gap-2" @click="exportarParaExcel">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Exportar para Excel
                    </button>
                </div>
            </div>

            <!-- Filtro Avançado -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisa Geral</label>
                        <div class="relative">
                            <input type="text" v-model="filtro.search" placeholder="Digite para filtrar..."
                                class="input input-bordered w-full pl-10" />
                            <span class="absolute right-3 top-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Loan</label>
                        <input type="text" v-model="filtro.lnr" placeholder="Número do Loan"
                            class="input input-bordered w-full" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Cliente</label>
                        <input type="text" v-model="filtro.cliente" placeholder="Nome do Cliente"
                            class="input input-bordered w-full" />
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button @click="resetarFiltros" class="btn btn-outline-secondary mr-2">
                        Limpar Filtros
                    </button>
                    <button @click="aplicarFiltros" class="btn btn-primary">
                        Aplicar Filtros
                    </button>
                </div>
            </div>

            <!-- Tabela Responsiva -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data de Registo
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Loan
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produto
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ref. de Pagamento
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(item, index) in extratosFiltrados" :key="index" class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ (lista_extrato.current_page - 1) * lista_extrato.per_page + index + 1 }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatarData(item.CiFecha) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ item.Lnr }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ item.Cliente }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ item.Produto }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ formatCurrency(item.ValorTotalCredito) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button v-if="item.RefPgtActivo === 0"
                                    @click="abrirModalActivarRerencia(item)"
                                    class="btn btn-outline-referencia flex items-center gap-2 mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="size-5 text-orange-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                    {{ item.referenciapagamento }}
                                </button>

                                <button v-else
                                    class="btn btn-outline-referencia-activada flex items-center gap-2 mx-auto cursor-not-allowed"
                                    disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="size-5 text-green-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    {{ item.referenciapagamento }}
                                </button>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex space-x-2 justify-center">
                                    <a :href="`/reports/extrato/${item.Num}`"
                                       class="btn btn-warning flex items-center gap-1"
                                       target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        PDF
                                    </a>
                                    <button @click="abrirModalDetalhes(item)"
                                        class="btn btn-info flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="size-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                        </svg>
                                        Detalhes
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="extratosFiltrados.length === 0">
                            <td colspan="8" class="px-4 py-4 text-center text-sm text-gray-500">
                                Nenhum desembolso encontrado com os filtros aplicados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ lista_extrato.from }} a {{ lista_extrato.to }} de {{ lista_extrato.total }} registros
                </div>
                <div class="flex gap-2">
                    <button @click="changePage(lista_extrato.current_page - 1)"
                        :disabled="lista_extrato.current_page === 1"
                        class="btn btn-outline flex items-center gap-1"
                        :class="{ 'opacity-50 cursor-not-allowed': lista_extrato.current_page === 1 }">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                        Anterior
                    </button>
                    <div class="flex items-center">
                        <span class="mx-2">Página {{ lista_extrato.current_page }}</span>
                    </div>
                    <button @click="changePage(lista_extrato.current_page + 1)"
                        :disabled="lista_extrato.current_page === lista_extrato.last_page"
                        class="btn btn-outline flex items-center gap-1"
                        :class="{ 'opacity-50 cursor-not-allowed': lista_extrato.current_page === lista_extrato.last_page }">
                        Próxima
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <ModalFiltrarData :show="showModalData" @close="showModalData = false" @filter="buscarPorDatas"
        v-model:dataInicio="dataInicio" v-model:dataFim="dataFim" />

    <ModalNovoCalculo :show="showModal" :form="form" @update:form="(newValue) => form = newValue"
        @close="showModal = false" @submit="submitForm" :bases="$page.props.bases"
        :produtosext="$page.props.produtosextratos" :bancos="$page.props.lista_banco"
        :contas="$page.props.lista_bancos_contas" :atividades="$page.props.lista_actividade_economica"
        :nesGrupos="$page.props.lista_nes_grupo" :nesTipos="$page.props.lista_nes_tipo" v-model="form" />

    <ModalDetalhesExtrato :show="showModalDetalhes" @close="showModalDetalhes = false" :extrato="extratoSelecionado" />

    <ModalActivarReferencia :show="showModalActivarRefencia" @close="showModalActivarRefencia = false"
        :extratoref="extratoSelecionado" />
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import ModalFiltrarData from './Layouts/components/ExtratosComponents/ModalFiltrarData.vue'
import ModalNovoCalculo from './Layouts/components/ExtratosComponents/ModalNovoCalculo.vue'
import ModalDetalhesExtrato from './Layouts/components/ExtratosComponents/ModalDetalhesExtrato.vue'
import ModalActivarReferencia from './Layouts/components/ExtratosComponents/ModalActivarReferencia.vue'

const props = defineProps({
    lista_extrato: Object,
    BasesOperacao: Array,
    agencia: String,
    produtosextratos: Array,
    lista_banco: Array,
    lista_bancos_contas: Array,
    lista_actividade_economica: Array,
    sistema_aberto: Boolean,
    lista_nes_grupo: Array,
    lista_nes_tipo: Array,
})

// Estados
const showModal = ref(false)
const showModalData = ref(false)
const showModalDetalhes = ref(false)
const showModalActivarRefencia = ref(false)
const sistemaAberto = props.sistema_aberto
const dataInicio = ref('')
const dataFim = ref('')
const extratoSelecionado = ref(null)

// Filtros
const filtro = ref({
    search: '',
    lnr: '',
    cliente: '',
    data_inicio: '',
    data_fim: ''
})

// Formulário
const form = useForm({
    // (Manter o mesmo formulário existente)
    selectBase: '',
    txtNumeroLoan: '',
    // ... outros campos do formulário
})

// Computed
const extratosFiltrados = computed(() => {
    return props.lista_extrato.data.filter(item => {
        const searchTerm = filtro.value.search.toLowerCase()
        const matchesSearch =
            (item.Cliente && item.Cliente.toLowerCase().includes(searchTerm)) ||
            (item.Lnr && item.Lnr.toString().includes(searchTerm)) ||
            (item.Produto && item.Produto.toLowerCase().includes(searchTerm)) ||
            (item.referenciapagamento && item.referenciapagamento.toLowerCase().includes(searchTerm))

        const matchesLnr = !filtro.value.lnr || (item.Lnr && item.Lnr.toString().includes(filtro.value.lnr))
        const matchesCliente = !filtro.value.cliente || (item.Cliente && item.Cliente.toLowerCase().includes(filtro.value.cliente.toLowerCase()))

        return matchesSearch && matchesLnr && matchesCliente
    })
})

// Métodos
const formatarData = (data) => {
    if (!data) return '-'
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' }
    return new Date(data).toLocaleDateString('pt-PT', options)
}

function formatCurrency(value) {
    if (value == null) return '';

    if (typeof value === 'string') {
        value = value.replace(/\D/g, '');
        if (!value) return '0,00';
        value = parseFloat(value) / 100;
    }

    return value.toLocaleString('pt-PT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

const aplicarFiltros = () => {
    router.get('/extratos', {
        ...filtro.value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    })
}

const resetarFiltros = () => {
    filtro.value = {
        search: '',
        lnr: '',
        cliente: '',
        data_inicio: '',
        data_fim: ''
    }
    aplicarFiltros()
}

const changePage = (page) => {
    if (page === '...') return
    router.get('/extratos', {
        ...filtro.value,
        page
    }, {
        preserveState: true,
        replace: true
    })
}

const buscarPorDatas = () => {
    router.get('/extratos', {
        tipo: 1,
        data_inicio: dataInicio.value,
        data_fim: dataFim.value
    }, { preserveState: true })
    showModalData.value = false
}

const abrirModalDetalhes = (extrato) => {
    extratoSelecionado.value = extrato
    showModalDetalhes.value = true
}

const abrirModalActivarRerencia = (extratoref) => {
    extratoSelecionado.value = extratoref
    showModalActivarRefencia.value = true
}

const submitForm = (form) => {
    router.post('/guardar-extrato', form, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
        },
        onError: (errors) => {
            console.error('Erros:', errors);
        }
    });
}

const exportarParaExcel = () => {
    const dadosFormatados = extratosFiltrados.value.map((item, index) => ({
        '#': (lista_extrato.current_page - 1) * lista_extrato.per_page + index + 1,
        'Data': formatarData(item.CiFecha),
        'Loan': item.Lnr,
        'Cliente': item.Cliente,
        'Produto': item.Produto,
        'Valor': formatCurrency(item.ValorTotalCredito),
        'Referência': item.referenciapagamento,
        'Status': item.RefPgtActivo === 1 ? 'Ativo' : 'Inativo'
    }))

    const ws = XLSX.utils.json_to_sheet(dadosFormatados)
    const wb = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(wb, ws, "Desembolsos")

    const nomeArquivo = `desembolsos_${new Date().toISOString().split('T')[0]}.xlsx`
    XLSX.writeFile(wb, nomeArquivo)
}

// Watchers
watch(() => props.lista_extrato, (newValue) => {
    // Atualizar dados se necessário
})
</script>

<style scoped>
/* Estilos melhorados */
.alert {
    @apply p-4 rounded-lg mb-4 border-l-4;
}

.alert-success {
    @apply bg-green-50 text-green-800 border-green-500;
}

.alert-danger {
    @apply bg-red-50 text-red-800 border-red-500;
}

.btn {
    @apply px-4 py-2 rounded-md font-medium transition-colors flex items-center justify-center text-sm;
}

.btn-primary {
    @apply bg-gradient-to-r from-green-900 to-greenkixi-300 text-white hover:from-green-800 hover:to-green-400 shadow-md hover:shadow-lg;
}

.btn-outline {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
}

.btn-outline-secondary {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
}

.btn-outline-excel {
    @apply border border-green-900 bg-white text-green-900 hover:bg-green-50;
}

.btn-outline-referencia {
    @apply border border-orange-500 bg-white text-orange-500 hover:bg-orange-50;
}

.btn-outline-referencia-activada {
    @apply border border-green-500 bg-white text-green-500;
}

.btn-warning {
    @apply bg-yellow-500 text-white hover:bg-yellow-600;
}

.btn-info {
    @apply bg-blue-500 text-white hover:bg-blue-600;
}

.input {
    @apply border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full;
}
.bg-greenkixi-300 {
    background-color: #08583d;
}

.text-greenkixi-solid {
    color: #08583d;
}

.to-greenkixi-300 {
    --tw-gradient-to: #08583d;
}

/* Responsividade da tabela */
@media (max-width: 1024px) {
    table {
        @apply block;
    }
    thead {
        @apply hidden;
    }
    tbody {
        @apply block;
    }
    tr {
        @apply block mb-4 border border-gray-200 rounded-lg p-2;
    }
    td {
        @apply block px-4 py-2 text-right border-b border-gray-200;
    }
    td::before {
        content: attr(data-label);
        @apply float-left font-medium text-gray-500;
    }
    td:last-child {
        @apply border-b-0;
    }
}

/* Efeitos de hover */
tr:hover {
    @apply bg-gray-50;
}
</style>
