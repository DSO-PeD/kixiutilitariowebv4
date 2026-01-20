<template>

    <Head title="Reconciliação" />

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

        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-box text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Fecho Pagamento
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Validação de pagamentos por referência</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <!-- Filtro Avançado -->
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
                <div class="grid grid-cols-12 p-4 gap-4 mb-4">
                    <!-- Período -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Inicio </label>
                        <input v-model="filtro.dataInicioInput" type="date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm"
                             @change="validarDatas" />
                        <span v-if="erros.dataInicio" class="text-red-500 text-xs">{{ erros.dataInicio }}</span>
                    </div>

                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fim </label>
                        <input v-model="filtro.dataFimInput" type="date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm"
                             @change="validarDatas" />
                        <span v-if="erros.dataFim" class="text-red-500 text-xs">{{ erros.dataFim }}</span>
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
        

        <!-- Tabela de Comprovativos -->
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <!-- Cabeçalho da tabela com paginação e exportação -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <!--<div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                    de {{ totalItens }} registros
                </div>-->

                <div class="flex flex-col sm:flex-row gap-3">
                    <button class="btn btn-outline-excel flex items-center gap-2" @click="exportarParaExcel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar para Excel
                    </button>
                </div>
            </div>

            <!-- Tabela -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periodo
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>

                                    Data Transação
                                </div>
                            </th>                         
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>

                                    Montante
                                </div>
                            </th>                            
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(fecho, index) in fechos" :key="fecho.periodo"
                            class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ index + 1 }}
                            </td>                            
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ fecho.periodo }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ fecho.ciFecha }}</td>
                            <td class="px-4 py-4 whitespace-nowrap font-medium text-sm">
                                <span class="bg-green-400 p-1 rounded-lg text-green-800 font-bold">{{ formatarMoeda(fecho.total_montante) }}</span>
                            </td>                            
                        </tr>
                        <tr v-if="fechos.length === 0">
                            <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300 mb-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm">Nenhum comprovativo encontrado</p>
                                    <p class="text-xs text-gray-400 mt-1">Tente ajustar os filtros de pesquisa</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-6 gap-1">
                <template v-for="link in links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        v-html="formatLabel(link.label)"
                        class="px-3 py-1 border rounded"
                        :class="{ 'bg-yellow-700 text-white': link.active }"
                        />
                        <span
                        v-else
                        v-html="formatLabel(link.label)"
                        class="px-3 py-1 border rounded text-gray-400 cursor-not-allowed"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import * as XLSX from 'xlsx'
import { Head } from '@inertiajs/vue3'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage();

const fechos = computed(() => page.props.lista_fechos.data);
const links  = computed(() => page.props.lista_fechos.links);

function formatLabel(label) {
  if (label.includes('Previous')) return '« Anterior'
  if (label.includes('Next')) return 'Próximo »'
  return label
}

const props = defineProps({
    lista_fechos: {
        type: Array,
        required: true
    }
})

function formatarMoeda(value) {
  if (value == null) return '0 AKZ';
  return new Intl.NumberFormat('pt-AO', { 
    style: 'currency', 
    currency: 'AOA', 
    minimumFractionDigits: 2 
  }).format(value);
}

const exportarParaExcel = () => {
    const listaCompleta = fechos.value;

    if (!listaCompleta || listaCompleta.length === 0) {
        alert('Nenhum dado disponível para exportar');
        return;
    }

    const dadosFormatados = listaCompleta.map((fecho, index) => ({
        '#': index + 1,
        'Período': fecho.periodo ?? '-',
        'Data': fecho.ciFecha ?? '-',
        'Montante': fecho.total_montante ?? 0,
    }));

    const ws = XLSX.utils.json_to_sheet(dadosFormatados);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Fechos');

    const hoje = new Date().toISOString().split('T')[0];
    XLSX.writeFile(wb, `fechos_reconciliacao_${hoje}.xlsx`);
};

// Adicione esta variável para controlar a visibilidade dos filtros
const filtrosVisiveis = ref(true)

// Função para alternar a visibilidade dos filtros
const toggleFiltros = () => {
    filtrosVisiveis.value = !filtrosVisiveis.value
}


/** Pesquisa de fechos */
const filtro = ref({
    dataInicioInput: '',
    dataFimInput: '',
});

const erros = ref({
    dataInicio: '',
    dataFim: ''
})

const validarDatas = () => {
    // Resetar erros
    erros.value = {
        dataInicio: '',
        dataFim: ''
    };

    let isValid = true;

    // Validar se as datas foram preenchidas
    if (!filtro.value.dataInicioInput) {
        erros.value.dataInicio = 'A data de início é obrigatória';
        isValid = false;
    }

    if (!filtro.value.dataFimInput) {
        erros.value.dataFim = 'A data de fim é obrigatória';
        isValid = false;
    }

    // Validar se a data de início é maior que a data de fim
    if (filtro.value.dataInicioInput && filtro.value.dataFimInput) {
        const dataInicio = new Date(filtro.value.dataInicioInput);
        const dataFim = new Date(filtro.value.dataFimInput);

        if (dataInicio > dataFim) {
            erros.value.dataInicio = 'A data de início não pode ser maior que a data de fim';
            erros.value.dataFim = 'A data de fim não pode ser menor que a data de início';
            isValid = false;
        }
    }

    return isValid;
};

// Função aplicarFiltros modificada
const aplicarFiltros = () => {
    if (!validarDatas()) return;

    router.get('/fechoPagamento', {
        data_inicio: filtro.value.dataInicioInput,
        data_fim: filtro.value.dataFimInput,
    }, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            //paginaAtual.value = 1; // Resetar paginação
        }
    });
};

// Função resetarFiltros
const resetarFiltros = () => {
    filtro.value = {        
        dataInicioInput: '',
        dataFimInput: ''        
    };

    router.get('/fechoPagamento', {
        page: 1
    }, {
        preserveState: true,
        replace: true
    });
};
</script>

<style scoped>
/* Estilos melhorados e mais consistentes */
.alert {
    @apply p-4 rounded-lg border-l-4;
}

.alert-success {
    @apply bg-green-50 text-green-800 border-green-500;
}

.alert-danger {
    @apply bg-red-50 text-red-800 border-red-500;
}

.btn {
    @apply px-4 py-2 rounded-lg font-medium transition-colors flex items-center justify-center text-sm;
}

.btn-primary {
    @apply bg-green-600 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2;
}

.btn-outline-primary {
    @apply border border-blue-500 text-blue-500 hover:bg-blue-50;
}


.btn-primary-filter {
    @apply bg-green-900 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-900 focus:ring-offset-2;
}

.btn-outline {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
}

.btn-outline-secondary {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
}

.btn-outline-excel {
    @apply border border-green-600 bg-white text-green-600 hover:bg-green-50;
}

.btn-action {
    @apply px-3 py-1.5 rounded-md text-sm font-medium transition-colors;
}

.btn-validate {
    @apply bg-blue-100 text-blue-700 hover:bg-blue-200 border border-blue-200;
}

.btn-detail {
    @apply bg-cyan-100 text-cyan-700 hover:bg-cyan-200 border border-cyan-200;
}

/* Melhorias de responsividade */
@media (max-width: 768px) {
    .container {
        @apply px-4;
    }

    .btn {
        @apply px-3 py-1.5 text-xs;
    }

    /* Esconder colunas menos importantes em mobile */
    .hidden-mobile {
        display: none;
    }
}

/* Animações suaves */
.transition-all {
    transition: all 0.3s ease;
}

/* Estados de hover melhorados */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Scrollbar personalizada para tabela */
.table-container::-webkit-scrollbar {
    height: 6px;
}

.table-container::-webkit-scrollbar-track {
    @apply bg-gray-100;
}

.table-container::-webkit-scrollbar-thumb {
    @apply bg-gray-300 rounded-full;
}

.table-container::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-400;
}
</style>
