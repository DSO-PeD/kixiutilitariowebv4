<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import * as XLSX from 'xlsx'
import { Head } from '@inertiajs/vue3'

// Componentes
import ModalLoan from './Layouts/components/ComprovativosComponents/ModalLoan.vue'
import ModalDate from './Layouts/components/ComprovativosComponents/ModalDate.vue'
import ModalDelete from './Layouts/components/ComprovativosComponents/ModalDelete.vue'
import ModalObservacaoDFC from './Layouts/components/ComprovativosComponents/ModalObservacaoDFC.vue'
import ModalNovoComprovativo from './Layouts/components/ComprovativosComponents/ModalNovoComprovativo.vue'
import ConfirmationModal from './Layouts/components/ComprovativosComponents/ConfirmationModal.vue'
import ModalEdicaoMontante from './Layouts/components/ComprovativosComponents/ModalEdicaoMontante.vue'
import ModalEdicaoData from './Layouts/components/ComprovativosComponents/ModalEdicaoData.vue'
import ModalEdicaoVoucher from './Layouts/components/ComprovativosComponents/ModalEdicaoVoucher.vue'

// Props
const props = defineProps({
    comprovativos: Array,
    filters: Object,
    page: Number,
    hasMorePages: Boolean,
    perPage: {
        type: Number,
        default: 100
    },
    lista_comprovativo: Array,
    total: Number,
    dataInicioInput: String,
    dataFimInput: String,
    montantetotal: Number,
    totalMontantePoupanca: Number,
    totalMontantePoupancaRegistado: Number,
    totalMontanteRegistado: Number,
    totalMontanteReflete: Number,
    totalMontantePoupancaReflete: Number,
    totalMontanteInregulares: Number,
    totalMontantePoupancaInregulares: Number,
    totalMontantePGREF: Number,
    totalPendente: Number,
    bases: Array,
    produtos: Array,
    bancos: Array,
    contas: Array,
    tipocomprovativos: Object,
    estados: Array,
    auth: Object,
    errors: Object,
    session: Object,
    flash: Object,
    user: Object,
    lista_pendentes: Object,
    dataInicioPeriodo: String,
    dataFimPeriodo: String,
    produtosPrestacoes: Array,
    produtosPoupancas: Array,
    formaspagamentos: Array
})

// Refs
const showModalLoan = ref(false)
const showModalData = ref(false)
const showModalNovo = ref(false)
const showModalEliminar = ref(false)
const showModalObservacao = ref(false)
const showDeleteModal = ref(false)
const showEditModal = ref(false)
const modalNovoComprovativoRef = ref(null)
const activeDetails = ref(null)
const mostrarTodos = ref(false)
const isDeleting = ref(false)
const novoMontante = ref('')
const paginaAtual = ref(1)
const perPage = ref(100)
const filtroLoan = ref('')
const dataInicio = ref('')
const dataFim = ref('')
const dateError = ref('')
const showModalDataEdicao = ref(false)
const novaDataRegistro = ref('')
const comprovativoSelecionadoData = ref(null)

const showModalVoucherEdicao = ref(false)
const novoVoucher = ref('')
const comprovativoSelecionadoVoucher = ref(null)
// Adicione estas refs
const showModalPagamentosReferencia = ref(false)
const pagamentosReferencia = ref([])

// Método para filtrar pagamentos por referência
const filtrarPagamentosPorReferencia = () => {
  pagamentosReferencia.value = props.lista_comprovativo.filter(comprovativo => {
    const formaPagamento = comprovativo.FormaPagoN || comprovativo.forma_pagamento || '';
    return formaPagamento.includes('Referência');
  });
}

// Método para abrir o modal
const abrirModalPagamentosReferencia = () => {
  filtrarPagamentosPorReferencia();
  showModalPagamentosReferencia.value = true;
}

// Método para verificar se é Pagamento por Referência sem voucher
const isPagamentoReferenciaSemVoucher = (comprovativo) => {
    const formaPagamento = comprovativo.FormaPagoN || comprovativo.forma_pagamento || '';
    const voucher = comprovativo.voucher || '';

    return formaPagamento.includes('Referência') && (!voucher || voucher.trim() === '' || voucher === 'null');
}

// Adicione estes métodos:
const abrirModalEdicaoVoucher = (comprovativo) => {
    comprovativoSelecionadoVoucher.value = { ...comprovativo }
    novoVoucher.value = comprovativo.voucher || ''
    showModalVoucherEdicao.value = true
}

const fecharModalEdicaoVoucher = () => {
    showModalVoucherEdicao.value = false
    comprovativoSelecionadoVoucher.value = null
    novoVoucher.value = ''
}

const salvarEdicaoVoucher = async (dados) => {
    try {
        await router.post('/alterarvoucher', {
            id: dados.id,
            novo_voucher: dados.novoVoucher
        }, {
            preserveScroll: true,
            onSuccess: () => {
                fecharModalEdicaoVoucher()
                router.reload({ only: ['lista_comprovativo'] })
            },
            onError: (errors) => {
                console.error('Erro ao editar voucher:', errors)
            }
        })
    } catch (error) {
        console.error('Erro ao editar voucher:', error)
    }
}
// Adicione estes métodos:
const abrirModalEdicaoData = (comprovativo) => {
    comprovativoSelecionadoData.value = { ...comprovativo } // Criar uma cópia do objeto
    novaDataRegistro.value = comprovativo.data
    showModalDataEdicao.value = true
}



const fecharModalEdicaoData = () => {
    showModalDataEdicao.value = false
    comprovativoSelecionadoData.value = null
    novaDataRegistro.value = ''
}

const salvarEdicaoData = async (dados) => {
    try {
        await router.post('/alterardata', {
            id: dados.id,
            nova_data: dados.novaData
        }, {
            preserveScroll: true,
            onSuccess: () => {
                fecharModalEdicaoData()
                router.reload({ only: ['lista_comprovativo'] })
            }
        })
    } catch (error) {
        console.error('Erro ao editar data:', error)
    }
}



// Dados selecionados
const selectedComprovativo = ref({
    lnr: '',
    cliente: '',
    montante: 0,
    data: '',
    estado: '',
    file: null,
    idestado: 0,
    id: null
})

const comprovativoSelecionado = ref(null)

// Filtros
const filtro = ref({
    search: props.filters.search || '',
    lnr: props.filters.lnr || '',
    estado: props.filters.estado || 28,
    agencia: props.filters.agencia || 'T',
    formaPagamento: props.filters.formaPagamento || 'TP',
    dataInicioInput: props.filters.data_inicio || '',
    dataFimInput: props.filters.data_fim || '',
    filtrarPrestacoes: props.filters.filtrar_prestacoes !== undefined ? Boolean(Number(props.filters.filtrar_prestacoes)) : true,
    filtrarPoupancas: props.filters.filtrar_poupancas !== undefined ? Boolean(Number(props.filters.filtrar_poupancas)) : true,
    produtoPrestacao: props.filters.produtoPrestacao || 'TL',
    produtoPoupanca: props.filters.produtoPoupanca || 'TS'
})

const erros = ref({
    dataInicio: '',
    dataFim: ''
})

// Novo comprovativo
const novoComprovativo = ref({
    ls: 'Loan',
    selectBase: '',
    selectGrupoIndividual: '',
    txtNumeroLoanSaving: '',
    selectProdutoLoan: '',
    selectProdutoSaving: '',
    txtLoanR: 'Loan Repayment',
    txtSavingD: 'Savings Deposit',
    selectBanco: '',
    selectBancoConta: '',
    txtMontante: '',
    calDataBorderoux: '',
    txtInfoAdicional: '',
    selectFormaPagamento: '',
    telefone: ''
})

// Computed
const hoje = computed(() => new Date().toISOString().split('T')[0])
const listaCompletaPendentes = computed(() => props.lista_pendentes || [])
const pendentesVisiveis = computed(() => mostrarTodos.value ? listaCompletaPendentes.value : listaCompletaPendentes.value.slice(0, 10))
const comprovativosPaginados = computed(() => props.lista_comprovativo.slice((paginaAtual.value - 1) * perPage.value, paginaAtual.value * perPage.value))
const totalItens = computed(() => props.lista_comprovativo.length)
const hasMorePages = computed(() => paginaAtual.value * perPage.value < props.lista_comprovativo.length)

// Métodos

const formatCurrency = (value) => {
    if (value == null) return ''
    if (typeof value === 'string') {
        value = value.replace(/\D/g, '')
        if (!value) return '0,00'
        value = parseFloat(value) / 100
    }
    return value.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
const calcularNumeroLinha = (index) => (paginaAtual.value - 1) * perPage.value + index + 1



const toggleDetails = (id) => activeDetails.value = id
const closeDetails = () => activeDetails.value = null

const podeEditar = (comprovativo) => {
    const isRegistadoHoje = comprovativo.data === hoje.value
    const temPermissao = props.user.comprovativo_btnedita_montante == 1
    return isRegistadoHoje || temPermissao
}

const podeEliminar = (comprovativo) => {
    const isRegistadoHoje = comprovativo.estado_id === 1 && comprovativo.data === hoje.value
    const temPermissao = props.user.elimina_confirmado_exportado == 1
    return isRegistadoHoje || temPermissao
}

const abrirModalEdicao = (comprovativo) => {
    comprovativoSelecionado.value = comprovativo
    novoMontante.value = comprovativo.montante.toString()
    showEditModal.value = true
}

const fecharModalEdicao = () => showEditModal.value = false

const salvarEdicaoMontante = async (dados) => {
    try {
        await router.post('/alterarmontante', {
            id: dados.id,
            novo_montante: dados.novoMontante
        }, {
            preserveScroll: true,
            onSuccess: () => {
                fecharModalEdicao()
                router.reload({ only: ['lista_comprovativo'] })
            }
        })
    } catch (error) {
        console.error('Erro ao editar montante:', error)
    }
}

const initiateDeletion = (comprovativo) => {
    if (!podeEliminar(comprovativo)) return

    selectedComprovativo.value = {
        lnr: comprovativo.lnr || 'N/A',
        cliente: comprovativo.cliente || 'N/A',
        montante: comprovativo.montante || 0,
        data: comprovativo.data || 'N/A',
        estado: comprovativo.estado || 'N/A',
        file: comprovativo.file || null,
        id: comprovativo.id,
        idestado: comprovativo.estado_id
    }

    showDeleteModal.value = true
}

const proceedWithDeletion = async () => {
    isDeleting.value = true
    try {
        await router.post("/eliminar-comprovativo", {
            id: selectedComprovativo.value.id,
            estado_id: selectedComprovativo.value.idestado
        }, {
            preserveScroll: true,
            onSuccess: () => showDeleteModal.value = false
        })
    } catch (error) {
        console.error('Erro ao eliminar:', error)
    } finally {
        isDeleting.value = false
    }
}

const cancelDeletion = () => {
    selectedComprovativo.value = {
        lnr: '',
        cliente: '',
        montante: 0,
        data: '',
        estado: '',
        id: null
    }
    showDeleteModal.value = false
}

const abrirModalObservacaoDCF = (comprovativo) => {
    comprovativoSelecionado.value = comprovativo
    showModalObservacao.value = true
}

const validarDatas = () => {
    erros.value = { dataInicio: '', dataFim: '' }
    let isValid = true

    if (!filtro.value.dataInicioInput) {
        erros.value.dataInicio = 'A data de início é obrigatória'
        isValid = false
    }

    if (!filtro.value.dataFimInput) {
        erros.value.dataFim = 'A data de fim é obrigatória'
        isValid = false
    }

    if (filtro.value.dataInicioInput && filtro.value.dataFimInput) {
        const dataInicio = new Date(filtro.value.dataInicioInput)
        const dataFim = new Date(filtro.value.dataFimInput)

        if (dataInicio > dataFim) {
            erros.value.dataInicio = 'A data de início não pode ser maior que a data de fim'
            erros.value.dataFim = 'A data de fim não pode ser menor que a data de início'
            isValid = false
        }
    }

    return isValid
}

const aplicarFiltros = () => {
    if (!validarDatas()) return

    router.get('/comprovativos', {
        search_input: filtro.value.search,
        lnr_imput: filtro.value.lnr,
        estado_input: filtro.value.estado,
        agencia_imput: filtro.value.agencia,
        data_inicio_imput: filtro.value.dataInicioInput,
        data_fim_imput: filtro.value.dataFimInput,
        filtrar_prestacoes: filtro.value.filtrarPrestacoes ? 1 : 0,
        filtrar_poupancas: filtro.value.filtrarPoupancas ? 1 : 0,
        produto_prestacao: filtro.value.produtoPrestacao,
        produto_poupanca: filtro.value.produtoPoupanca,
        forma_pagamento: filtro.value.formaPagamento,
        tipo: 4
    }, {
        preserveState: true,
        replace: true,
        onSuccess: () => paginaAtual.value = 1
    })
}

const aplicarFiltrosmai5M = () => {
    router.get('/comprovativos', { tipo: 500000 }, {
        preserveState: true,
        replace: true,
        onSuccess: () => paginaAtual.value = 1
    })
}

const aplicarFiltrosmexc7M = () => {
    router.get('/comprovativos', { tipo: 7000000 }, {
        preserveState: true,
        replace: true,
        onSuccess: () => paginaAtual.value = 1
    })
}

const resetarFiltros = () => {
    filtro.value = {
        search: '',
        lnr: '',
        estado: 28,
        agencia: 'T',
        formaPagamento: 'TP',
        produtoPrestacao: 'TL',
        produtoPoupanca: 'TS',
        dataInicioInput: '',
        dataFimInput: '',
        filtrarPrestacoes: true,
        filtrarPoupancas: true
    }

    router.get('/comprovativos', { page: 1 }, {
        preserveState: true,
        replace: true
    })
}

const exportarParaExcel = () => {
    try {
        const dadosFormatados = props.lista_comprovativo.map((comprovativo, index) => ({
            '#': index + 1,
            'Data': comprovativo.CiFecha ? new Date(comprovativo.CiFecha).toLocaleString('pt-PT') : '-',
            'Agência': comprovativo.agencia || '-',
            'Registado Por': comprovativo.usuario || '-',
            'Loan Number': comprovativo.lnr || '-',
            'Cliente': comprovativo.cliente || '-',
            'Produto': comprovativo.metodologia || '-',
            'Voucher Dia': comprovativo.voucher || '-',
            'Voucher Transacao': comprovativo.referenciatransacao || '-',
            'Forma de Pagamento': comprovativo.FormaPagoN || '-',
            'Descrição da DCF': comprovativo.descricao || '-',
            'Banco': comprovativo.banco || '-',
            'Conta Bancaria': comprovativo.conta || '-',
            'Observação da DCF': comprovativo.observacao || '-',
            'Montante': comprovativo.montante || '0,00',
            'Estado': comprovativo.estado || '-',
        }))

        const ws = XLSX.utils.json_to_sheet(dadosFormatados)
        const wb = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(wb, ws, "Comprovativos")
        XLSX.writeFile(wb, `comprovativos_DOP_completa_${new Date().toISOString().split('T')[0]}.xlsx`)
    } catch (error) {
        console.error('Erro ao exportar:', error)
        alert(`Erro ao exportar: ${error.message}`)
    }
}

const exportToExcel = () => {
    try {
        const dadosFormatados = listaCompletaPendentes.value.map((comprovativo, index) => ({
            '#': index + 1,
            'Data de Registo': comprovativo.CiFecha ? new Date(comprovativo.CiFecha).toLocaleString('pt-PT') : '-',
            'Loan Number': comprovativo.Lnr || '-',
            'Voucher dia': comprovativo.voucher === 'null' || comprovativo.voucher == null ? 'não registado' : comprovativo.voucher,
            'Montante': comprovativo.montante || '0,00',
            'Data do Comprovativo': comprovativo.budata ? new Date(comprovativo.budata).toLocaleString('pt-PT') : '-'
        }))

        const ws = XLSX.utils.json_to_sheet(dadosFormatados)
        const wb = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(wb, ws, "Comprovativos")
        XLSX.writeFile(wb, `comprovativos_Pendentes_completa_${new Date().toISOString().split('T')[0]}.xlsx`)
    } catch (error) {
        console.error('Erro ao exportar:', error)
        alert(`Erro ao exportar: ${error.message}`)
    }
}

const exportarPagamentosReferenciaExcel = () => {
  try {
    const dadosFormatados = pagamentosReferencia.value.map((pagamento, index) => ({
      '#': index + 1,
      'Data': pagamento.data || '-',
      'Loan Number': pagamento.lnr || '-',
      'Cliente': pagamento.cliente || '-',
      'Voucher': pagamento.voucher || '-',
      'Montante': pagamento.montante || '0,00',
      'Estado': pagamento.estado || '-',
      'Forma de Pagamento': pagamento.FormaPagoN || '-',
      'Produto': pagamento.metodologia || '-'
    }))

    const ws = XLSX.utils.json_to_sheet(dadosFormatados)
    const wb = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(wb, ws, "Pagamentos por Referência")
    XLSX.writeFile(wb, `pagamentos_referencia_${new Date().toISOString().split('T')[0]}.xlsx`)
  } catch (error) {
    console.error('Erro ao exportar:', error)
    alert(`Erro ao exportar: ${error.message}`)
  }
}

const abrirModalNovoComprovativo = () => {
    showModalNovo.value = true
    novoComprovativo.value = {
        ls: 'Loan',
        selectBase: '',
        selectGrupoIndividual: '',
        txtNumeroLoanSaving: '',
        selectProdutoLoan: '',
        selectProdutoSaving: '',
        txtLoanR: 'Loan Repayment',
        txtSavingD: 'Savings Deposit',
        selectBanco: '',
        selectBancoConta: '',
        txtMontante: '',
        calDataBorderoux: '',
        txtInfoAdicional: '',
        selectFormaPagamento: '',
        telefone: ''
    }
}

const fecharModalNovoComprovativo = () => showModalNovo.value = false

const guardarComprovativo = async () => {
    try {
        const formData = new FormData()
        Object.entries(novoComprovativo.value).forEach(([key, value]) => {
            if (value) formData.append(key, value)
        })

        if (modalNovoComprovativoRef.value?.selectedFile) {
            formData.append('anexo', modalNovoComprovativoRef.value.selectedFile)
        }

        await router.post('/guardar-comprovativo', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onSuccess: () => {
                fecharModalNovoComprovativo()
                modalNovoComprovativoRef.value?.resetFileInput()
            }
        })
    } catch (error) {
        console.error('Erro ao enviar comprovativo:', error)
    }
}

const buscarPorLoan = () => {
    router.get('/comprovativos', { tipo: 3, loan: filtroLoan.value }, { preserveState: true })
    showModalLoan.value = false
}

const buscarPorDatas = () => {
    router.get('/comprovativos', {
        tipo: 1,
        data_inicio: dataInicio.value,
        data_fim: dataFim.value
    }, { preserveState: true })
    showModalData.value = false
}

const mudarPagina = (novaPagina) => {
    paginaAtual.value = novaPagina
    window.scrollTo({ top: 0, behavior: 'smooth' })
}



// Watchers
watch(() => props.filters, (newFilters) => {
    filtro.value = {
        search: newFilters.search || '',
        lnr: newFilters.lnr || '',
        estado: newFilters.estado || 28,
        agencia: newFilters.agencia || 'T',
        formaPagamento: newFilters.formaPagamento || 'TP',
        produtoPrestacao: newFilters.produtoPrestacao || 'TL',
        produtoPoupanca: newFilters.produtoPoupanca || 'TS',
        dataInicioInput: newFilters.data_inicio || '',
        dataFimInput: newFilters.data_fim || '',
        filtrarPrestacoes: newFilters.filtrar_prestacoes !== undefined ? Boolean(Number(newFilters.filtrar_prestacoes)) : true,
        filtrarPoupancas: newFilters.filtrar_poupancas !== undefined ? Boolean(Number(newFilters.filtrar_poupancas)) : true
    }
}, { immediate: true, deep: true })

watch(() => props.page, (newPage) => {
    paginaAtual.value = newPage
})

watch(() => [filtro.value.dataInicioInput, filtro.value.dataFimInput], () => {
    validarDatas()
})
</script>

<template>
    <!-- Seu template existente permanece exatamente o mesmo -->


    <Head title="Comprovativos" />

    <div class="container mx-auto py-6 max-w-full">

        <!-- Modal com binding dos dados -->
        <ConfirmationModal :show="showDeleteModal" :comprovativoData="selectedComprovativo" :isDeleting="isDeleting"
            @confirm="proceedWithDeletion" @cancel="cancelDeletion" />


        <!-- Alertas -->
        <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
            {{ $page.props.flash.success }}
        </div>

        <div v-if="$page.props.flash.error" class="alert alert-danger mb-4">
            {{ $page.props.flash.error }}
        </div>

        <!-- Cabeçalho e Botão de Ação -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-green-950"><i
                        class="fas fa-file-invoice-dollar mr-3 text-3xl"></i>Gestão dos Comprovativos</h1>
                <p class="text-sm text-gray-600">Pagamentos de Créditos e Poupanças</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto" v-if="$page.props.user.rec_comprovativo">
                <button class="btn btn-primary flex items-center gap-2" @click="abrirModalNovoComprovativo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Novo Comprovativo
                </button>
            </div>

        </div>

        <hr class="py-4" />
        <div v-if="$page.props.user.view_pendentes" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <div class="alert bg-red-50 border-l-4 border-red-500 text-red-700 p-4">
                <div class="flex items-start">
                    <svg class="flex-shrink-0 h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>


                    <p class="text-sm">
                        | Identificamos que os reembolsos abaixo <b>aplicados no Kixi Utilitário não foram aplicados no
                            LPF</b> ou existe uma possível
                        diferença nas informações como: <i class="text-orange-950">Loan Number, Voucher e Montante</i>.
                        &ThickSpace;Clique sobre o <span
                            class="badge bg-orange-100 text-orange-600 rounded-full px-3 py-1 text-xs font-medium">
                            Loan Number</span> apresentado
                        para mais detalhe do comprovativo.
                        <br />

                        <!-- Agrupamento para manter tudo na mesma linha -->
                        <span class="inline-flex flex-wrap items-center gap-1">
                            [
                            <span v-for="_pendente in pendentesVisiveis" :key="_pendente.Lnr"
                                class="relative inline-block mx-1">
                                <span @click="toggleDetails(_pendente.id)"
                                    class="badge bg-orange-100 text-orange-600 rounded-full px-3 py-1 text-xs font-bold cursor-pointer hover:bg-red-200 transition">
                                    {{ _pendente.Lnr }}
                                </span>

                                <!-- Tooltip Detalhes -->
                                <div v-if="activeDetails === _pendente.id"
                                    class="absolute z-10 left-0 mt-2 w-64 bg-white shadow-lg rounded-md border border-gray-200 p-3">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <span class="text-gray-500">Voucher:</span>
                                        <span class="font-medium">{{ _pendente.voucher }}</span>

                                        <span class="text-gray-500">Montante:</span>
                                        <span class="font-medium">{{ _pendente.montante }}</span>

                                        <span class="text-gray-500">Data do Comp.:</span>
                                        <span class="font-medium">{{ _pendente.budata }}</span>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-100 text-right">
                                        <button @click.stop="closeDetails"
                                            class="text-xs text-red-600 hover:text-red-800">
                                            Fechar
                                        </button>
                                    </div>
                                </div>
                            </span>

                            <!-- Botão de ver mais/ver menos -->
                            <button @click="mostrarTodos = !mostrarTodos"
                                class="flex items-center gap-1 text-blue-600 hover:underline">
                                <!-- Transição de ícone -->
                                <transition name="fade-icon" mode="out-in">
                                    <!-- Ícone olho (ver mais) -->
                                    <svg v-if="!mostrarTodos" key="ver" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5
                                                12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0
                                                .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    <!-- Ícone olho cortado (ver menos) -->
                                    <svg v-else key="ocultar" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338
                                            7.244 19.5 12 19.5c.993 0 1.953-.138
                                            2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12
                                            4.5c4.756 0 8.773 3.162 10.065 7.498a10.522
                                            10.522 0 0 1-4.293 5.774M6.228 6.228 3
                                            3m3.228 3.228 3.65 3.65m7.894
                                            7.894L21 21m-3.228-3.228-3.65-3.65m0
                                            0a3 3 0 1 0-4.243-4.243m4.242
                                            4.242L9.88 9.88" />
                                    </svg>
                                </transition>

                                {{ mostrarTodos ? 'Ver menos' : 'Ver mais...' }}
                            </button>]


                        </span>
                    </p>
                    <!-- Botão de exportar para Excel -->
                    <button @click="exportToExcel"
                        class=" btn btn-outline-excelP  flex items-center gap-1 text-white px-3 py-1 rounded text-sm ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar Pendentes Para Excel
                    </button>


                    <br /><br />




                </div>

                <p class="mt-1 text-sm font-medium">
                    Por favor, regularize-os para evitar transtornos futuros. Atenção! é necessário que as informações,
                    como Loan Number, Voucher e Montante, estejam iguais no Kixi Utilitário e no LPF.
                </p>




            </div>
        </div>





        <!-- Filtro Avançado -->

        <div class="mb-6 bg-gray-50 p-3 sm:p-4 rounded-lg">
            <!-- Filtros superiores -->

            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <!-- Coluna 1: Botão Loan -->


                <button class="btn btn-outline-consulta flex items-center gap-2 w-full justify-center"
                    @click="showModalLoan = true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Loan Number
                </button>


                <!-- Coluna 2: Datas -->
                <div class="col-span-2 sm:col-span-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Data de Início -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Período de Início*</label>
                            <div class="relative">
                                <input v-model="filtro.dataInicioInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 transition text-sm"
                                    :max="filtro.dataFimInput" @change="validarDatas" />
                                <span v-if="erros.dataInicio" class="text-red-500 text-xs">{{ erros.dataInicio }}</span>
                            </div>
                        </div>

                        <!-- Data de Fim -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Período de Fim*</label>
                            <div class="relative">
                                <input v-model="filtro.dataFimInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 transition text-sm"
                                    :min="filtro.dataInicioInput" @change="validarDatas" />
                                <span v-if="erros.dataFim" class="text-red-500 text-xs">{{ erros.dataFim }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna 3: Forma de Pagamento -->
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento</label>
                    <div class="relative">
                        <select v-model="filtro.formaPagamento"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 text-sm h-[42px] bg-white">

                            <option v-for="formapgt in formaspagamentos" :value="formapgt.FormaPago"
                                :key="formapgt.FormaPago">
                                {{ formapgt.FormaPagoN }}
                            </option>
                            <option :value="'TP'">Todas formas</option>

                        </select>
                    </div>
                </div>

                <!-- Coluna 4: Agência -->
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agência</label>
                    <div class="relative">
                        <select v-model="filtro.agencia"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 text-sm h-[42px] bg-white">
                            <option disabled :value="'s/a'">Escolha agência</option>
                            <option v-for="agencia in $page.props.bases" :value="agencia.OfIdentificador"
                                :key="agencia.OfIdentificador">
                                {{ agencia.OfIdentificador }} - {{ agencia.OfNombre }}
                            </option>
                            <option :value="'T'">Todas que tenho acesso</option>
                        </select>
                    </div>
                </div>

                <!-- Coluna 5: Estado -->
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <div class="relative">
                        <select v-model="filtro.estado"
                            class="input input-bordered w-full text-sm py-2 h-[42px] bg-white">
                            <option disabled :value="'s/e'">Escolha</option>
                            <option v-for="estado in $page.props.estados" :value="Number(estado.id)" :key="estado.id">
                                {{ estado.descricao_estado }}
                            </option>
                            <option :value="28">Todos</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Filtros inferiores (checkboxes e produtos) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 mt-3">
                <!-- Checkbox Prestações -->
                <div class="flex items-center bg-white p-2 rounded border border-gray-200">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="filtro.filtrarPrestacoes" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2
                    peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full
                    peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px]
                    after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full
                    after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600">
                        </div>
                        <span class="ml-2 text-sm text-gray-600 whitespace-nowrap font-semibold">
                            Prestações (Capital+Juro)
                        </span>
                    </label>
                </div>

                <!-- Checkbox Poupanças -->
                <div class="flex items-center bg-white p-2 rounded border border-gray-200">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="filtro.filtrarPoupancas" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2
                    peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full
                    peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px]
                    after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full
                    after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600">
                        </div>
                        <span class="ml-2 text-sm text-gray-600 whitespace-nowrap font-semibold">
                            Poupanças
                        </span>
                    </label>
                </div>

                <!-- Combobox Produtos (Conditional) -->
                <div v-if="filtro.filtrarPrestacoes || filtro.filtrarPoupancas" class="space-y-1">
                    <!--label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ filtro.filtrarPrestacoes && !filtro.filtrarPoupancas ? 'Produto de Prestações' :
                            !filtro.filtrarPrestacoes && filtro.filtrarPoupancas ? 'Produto de Poupanças' : 'Produto' }}
                    </label-->
                    <select v-if="filtro.filtrarPrestacoes && !filtro.filtrarPoupancas"
                        v-model="filtro.produtoPrestacao"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 text-sm h-[42px] bg-white">
                        <option disabled :value="'s/pl'">
                            {{ filtro.filtrarPrestacoes && !filtro.filtrarPoupancas ? 'Produtos de Prestações(Capital +  Juros) ' : !filtro.filtrarPrestacoes && filtro.filtrarPoupancas ? 'Produto de Poupanças' :
                            'Produto' }}
                        </option>
                        <option v-for="produto in produtos.filter(p => p.TipoProduto === 'L' || p.TipoProduto === 'G')"
                            :key="produto.Metodologia" :value="produto.Metodologia">
                            {{ produto.PoAgrupado }}
                        </option>
                        <option value="TL">Todos os produtos de Prestações</option>

                    </select>

                    <select v-else-if="filtro.filtrarPoupancas && !filtro.filtrarPrestacoes"
                        v-model="filtro.produtoPoupanca"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 text-sm h-[42px] bg-white">


                        <option disabled :value="'s/ts'">
                            {{ filtro.filtrarPrestacoes && !filtro.filtrarPoupancas ? 'Produtos de Prestações(Capital + Juros) ' : !filtro.filtrarPrestacoes && filtro.filtrarPoupancas ? 'Produto de Poupanças' :
                            'Produto' }}
                        </option>
                        <option v-for="produto in produtos.filter(p => p.TipoProduto === 'S' || p.TipoProduto === 'G')"
                            :key="produto.Metodologia" :value="produto.Metodologia">
                            {{ produto.PoAgrupado }}
                        </option>
                        <option value="TS">Todos os produtos de Poupanças</option>
                    </select>
                </div>
            </div>

            <!-- Botões de ação -->
            <div class="mt-4 flex justify-end space-x-2">
                <button @click="resetarFiltros" class="btn btn-outline-secondary flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-4 w-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                    </svg>
                    Limpar Filtros
                </button>
                <button @click="aplicarFiltros" class="btn btn-primary flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Aplicar Filtros
                </button>
            </div>
        </div>




            <div class="rounded-lg p-4 w-full bg-gray-100 border border-white shadow-sm">
                <!-- Cabeçalho compacto do período -->
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200">
                    <div
                        class="bg-white px-3 py-1.5 rounded-lg text-sm flex items-center gap-1.5 border border-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium text-gray-700">REEMBOLSOS:</span>
                        <span class="text-green-800 font-bold text-sm">{{ dataFimPeriodo }} </span>
                        <span class="text-gray-600 font-normal">à</span>
                        <span class="text-green-800 font-bold text-sm">{{ dataInicioPeriodo }}</span>
                    </div>
                </div>

                <!-- Cards consolidados de métricas -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 opacity-0 animate-fade-in">
                    <!-- Card Consolidado - Reembolsos -->
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm card-hover">
                        <div class="flex items-center mb-3">
                            <div class="bg-green-100 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-green-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-700">REEMBOLSOS DE PRESTAÇÕES (Capital + Juros) | Total</h3>
                        </div>
                        <p class="text-lg font-bold text-green-700 mb-3">{{ formatCurrency(montantetotal) }} AKZ</p>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                                <div class="flex items-center">
                                    <div class="p-1 bg-blue-100 rounded-full mr-2">
                                        <i class="fas fa-file-export text-blue-500 text-xs"></i>
                                    </div>
                                    <span class="text-gray-600">Registados:</span>
                                </div>
                                <span class="font-semibold">{{ formatCurrency(totalMontanteRegistado) }} AKZ</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                <div class="flex items-center">
                                    <div class="p-1 bg-green-100 rounded-full mr-2">
                                        <i class="fas fa-clipboard-check text-green-600 text-xs"></i>
                                    </div>
                                    <span class="text-gray-600">Refletidos:</span>
                                </div>
                                <span class="font-semibold text-green-600">{{ formatCurrency(totalMontanteReflete) }} AKZ</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-red-50 rounded">
                                <div class="flex items-center">
                                    <div class="p-1 bg-red-100 rounded-full mr-2">
                                        <i class="fas fa-thumbs-down text-red-600 text-xs"></i>
                                    </div>
                                    <span class="text-gray-600">Irregulares:</span>
                                </div>
                                <span class="font-semibold text-red-600">{{ formatCurrency(totalMontanteInregulares)  }} AKZ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card Consolidado - Poupanças -->
                    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm card-hover">
                        <div class="flex items-center mb-3">
                            <div class="bg-cyan-100 p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5 text-cyan-600">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-700">POUPANÇAS |Total </h3>
                        </div>
                        <p class="text-lg font-bold text-cyan-700 mb-3">{{ formatCurrency(totalMontantePoupanca) }} AKZ</p>

                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                                <div class="flex items-center">
                                    <div class="p-1 bg-blue-100 rounded-full mr-2">
                                        <i class="fas fa-file-export text-blue-500 text-xs"></i>
                                    </div>
                                    <span class="text-gray-600">Registados:</span>
                                </div>
                                <span class="font-semibold">{{   formatCurrency(totalMontantePoupancaRegistado) }} AKZ</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                <div class="flex items-center">
                                    <div class="p-1 bg-green-100 rounded-full mr-2">
                                        <i class="fas fa-clipboard-check text-green-600 text-xs"></i>
                                    </div>
                                    <span class="text-gray-600">Refletidos:</span>
                                </div>
                                <span class="font-semibold text-cyan-600">{{ formatCurrency(totalMontantePoupancaReflete) }}  AKZ</span>
                            </div>
                            <div class="flex justify-between items-center p-2 bg-red-50 rounded">
                                <div class="flex items-center">
                                    <div class="p-1 bg-red-100 rounded-full mr-2">
                                        <i class="fas fa-thumbs-down text-red-600 text-xs"></i>
                                    </div>
                                    <span class="text-gray-600">Irregulares:</span>
                                </div>
                                <span class="font-semibold text-red-600">{{   formatCurrency(totalMontantePoupancaInregulares) }}AKZ</span>
                            </div>
                        </div>
                    </div>

                     <!-- Card de Pagamentos por Referência - Adicione @click -->
  <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm card-hover cursor-pointer"
       @click="abrirModalPagamentosReferencia">
    <div class="flex items-center mb-3">
      <div class="bg-purple-100 p-2 rounded-full mr-3">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="size-5 text-purple-600">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
        </svg>
      </div>
      <h3 class="text-sm font-semibold text-gray-700">MONTANTE DE OPERAÇÕES REALIZADAS VIA PAGAMENTO POR REFERÊNCIAS | Total</h3>
    </div>
    <p class="text-lg font-bold text-purple-700">{{ formatCurrency(totalMontantePGREF) }} AKZ</p>
    <p class="text-xs text-gray-500 mt-2">Total de operações realizadas via pagamento por referências</p>

    <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-cyan-600 font-medium flex items-center">
      <span>Ver detalhes</span>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24"
           stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5l7 7-7 7" />
      </svg>
    </div>
  </div>

  <!-- Modal para Pagamentos por Referência -->
<transition name="fade-scale" appear>
  <div v-if="showModalPagamentosReferencia" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[80vh] overflow-hidden">
      <div class="flex items-center justify-between p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Pagamentos por Referência - Período: {{ dataFimPeriodo }} à {{ dataInicioPeriodo }}
        </h3>
        <button @click="showModalPagamentosReferencia = false" class="text-gray-400 hover:text-gray-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="p-6 overflow-y-auto max-h-[60vh]">
        <div class="mb-4 flex justify-between items-center">
          <p class="text-sm text-gray-600">
            Total: <b>{{ pagamentosReferencia.length }}</b> pagamentos -
           <b class="text-green-700"> {{ formatCurrency(totalMontantePGREF) }} AKZ</b>
          </p>
          <button @click="exportarPagamentosReferenciaExcel" class="btn btn-outline-excel flex items-center gap-2 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            Exportar
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loan Number</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Voucher</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montante</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(pagamento, index) in pagamentosReferencia" :key="pagamento.id" class="hover:bg-gray-50">
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ index + 1 }}</td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ pagamento.data }}</td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ pagamento.lnr }}</td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ pagamento.cliente }}</td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ pagamento.voucher || '-' }}</td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                  {{ formatCurrency(pagamento.montante) }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <span :class="pagamento.color" class="px-2 py-1 text-xs font-medium rounded-full">
                    {{ pagamento.estado }}
                  </span>
                </td>
              </tr>
              <tr v-if="pagamentosReferencia.length === 0">
                <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">
                  Nenhum pagamento por referência encontrado no período
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="flex justify-end p-6 border-t border-gray-200">
        <button @click="showModalPagamentosReferencia = false" class="btn btn-outline">
          Fechar
        </button>
      </div>
    </div>
   </div>
</transition>
                </div>

                <!-- Informação adicional compacta -->
                <div class="mt-6 pt-4 border-t border-gray-200 text-xs text-gray-500">
                    <p><i class="fas fa-info-circle mr-1 text-blue-400"></i> Valores refletidos representam valores
                        existentes no banco validados pelo DCF</p>
                </div>
            </div>

        <br />

        <!-- Card Principal -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">


            <!-- Paginação -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                    de {{ totalItens }} registros
                </div>
                <div class="text-sm text-green-500 ">
                    <button class="btn btn-outline-excel flex items-center gap-2 " @click="exportarParaExcel">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Exportar Dados da tabela para Excel
                    </button>
                </div>







                <div class="flex gap-2">

                    <button :disabled="paginaAtual === 1" @click="mudarPagina(paginaAtual - 1)" class="btn btn-outline"
                        :class="{ 'opacity-50 cursor-not-allowed': paginaAtual === 1 }">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                        Anterior
                    </button>
                    <div class="flex items-center">
                        <span class="mx-2">Página {{ paginaAtual }}</span>
                    </div>
                    <button :disabled="!hasMorePages" @click="mudarPagina(paginaAtual + 1)" class="btn btn-outline"
                        :class="{ 'opacity-50 cursor-not-allowed': !hasMorePages }">
                        Próxima
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tabela Responsiva -->
            <div class="overflow-x-auto">
                <!-- Alertas condicionais acima da tabela - agora em linha quando ambos existirem -->
                <div class="flex flex-wrap gap-4 mb-4">
                    <div v-if="comprovativosPaginados.some(c => c.montante > 7000000)"
                        class="flex-1 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p>Atenção! detectamos que existem Reembolsos que excedem <b>7.000.000,00</b> </p>
                            <button @click="aplicarFiltrosmexc7M"
                                class="ml-4 px-3 py-1 text-xs font-medium bg-white border border-red-300 rounded-md shadow-sm hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500"
                                title="Filtrar por valores > 7.000.000">
                                Listar todos
                            </button>
                        </div>
                    </div>

                    <div v-if="comprovativosPaginados.some(c => c.montante >= 500000 && c.montante <= 7000000)"
                        class="flex-1 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p>Atenção! detectamos que existem Reembolsos iguais ou superiores a <b>500.000,00</b> </p>
                            <button @click="aplicarFiltrosmai5M"
                                class="ml-4 px-3 py-1 text-xs font-medium bg-white border border-yellow-300 rounded-md shadow-sm hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                title="Filtrar por valores entre 500.000 e 7.000.000">
                                Listar todos
                            </button>
                        </div>
                    </div>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    Arquivo
                                </div>
                            </th>

                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>

                                    Registado
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>

                                    Por
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    Loan Number
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>

                                    Cliente
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>


                                    Produto
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 9.75h4.875a2.625 2.625 0 0 1 0 5.25H12M8.25 9.75 10.5 7.5M8.25 9.75 10.5 12m9-7.243V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
                                    </svg>

                                    Voucher Dia
                                </div>

                            </th>
                              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                             <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                </svg>

                                    Voucher Transação
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">


                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>


                                   Pagamento
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                    </svg>

                                    OBS DCF
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                    </svg>

                                    Estado
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                    </svg>


                                </div>

                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(comprovativo, index) in comprovativosPaginados" :key="comprovativo.id"
                            class="hover:bg-gray-50" :class="{
                                'bg-purple-50': comprovativo.montante > 7000000,
                                'bg-red-100': ['Valor não Condiz', 'Falta Extrato', 'Não Reflete'].includes(comprovativo.estado),
                                'bg-aliceblue': comprovativo.estado === 'Registado',
                                'bg-green-50': comprovativo.estado === 'Reflete',
                                'bg-coral': comprovativo.estado === 'Pendente'
                            }">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ calcularNumeroLinha(index) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <a v-if="comprovativo.file" :href="`/storage/comprovativos/${comprovativo.file}`"
                                    target="_blank" class="text-blue-600 hover:underline flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    Visualizar
                                </a>
                                <span v-else class="text-gray-400 italic">Sem arquivo</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.data }}
                                <button v-if="$page.props.user && $page.props.user.rec_habilita_comprovativo"
                                    @click="abrirModalEdicaoData(comprovativo)"
                                    class="ml-2 text-gray-400 hover:text-blue-600 transition-colors"
                                    title="Editar data de registro">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.usuario }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ comprovativo.lnr }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ comprovativo.cliente }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.metodologia }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ formatCurrency(comprovativo.montante) }}

                                <button v-if="podeEditar(comprovativo)" @click="abrirModalEdicao(comprovativo)"
                                    class="ml-2 text-gray-400 hover:text-green-600 transition-colors"
                                    title="Editar montante">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">

                                <span>{{ comprovativo.voucher || '-' }}</span>

                                <!-- Botão normal de edição para outros casos (opcional) -->
                                <button v-if="$page.props.user.comprovativo_editar_voucher"
                                    @click="abrirModalEdicaoVoucher(comprovativo)"
                                    class="ml-2 text-gray-400 hover:text-blue-600 transition-colors"
                                    title="Editar voucher">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>

                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">

                                <span>{{ comprovativo.referenciatransacao || '-' }}</span>



                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.FormaPagoN || '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button @click="abrirModalObservacaoDCF(comprovativo)"
                                    class="btn btn-action btn-validate ml-2 flex items-center gap-1 mx-auto"
                                    v-if="comprovativo.observacao && comprovativo.observacao.trim() !== ''"
                                    title="Ver observação" aria-label="Ver observação do comprovativo">
                                    <i class="far fa-comment-dots"></i>
                                </button>
                                <span v-else class="text-gray-400 italic"> Nenhuma!</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span :class="comprovativo.color" class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ comprovativo.estado }}
                                </span>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <button @click="initiateDeletion(comprovativo)" :disabled="!podeEliminar(comprovativo)"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': !podeEliminar(comprovativo),
                                        'text-red-600 hover:text-red-900': podeEliminar(comprovativo),
                                        'flex items-center gap-1': true
                                    }" title="Eliminar comprovativo">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                    <span>Eliminar</span>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="comprovativosPaginados.length === 0">
                            <td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">
                                Nenhum comprovativo encontrado com os filtros aplicados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                    de {{ totalItens }} registros
                </div>
                <div class="flex gap-2">
                    <button :disabled="paginaAtual === 1" @click="mudarPagina(paginaAtual - 1)" class="btn btn-outline"
                        :class="{ 'opacity-50 cursor-not-allowed': paginaAtual === 1 }">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                        Anterior
                    </button>
                    <div class="flex items-center">
                        <span class="mx-2">Página {{ paginaAtual }}</span>
                    </div>
                    <button :disabled="!hasMorePages" @click="mudarPagina(paginaAtual + 1)" class="btn btn-outline"
                        :class="{ 'opacity-50 cursor-not-allowed': !hasMorePages }">
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

    <!-- Modals (mantidos como estão) -->
    <ModalLoan :isOpen="showModalLoan" @close="showModalLoan = false" @search="buscarPorLoan" v-model="filtroLoan" />
    <ModalDate :isOpen="showModalData" @close="showModalData = false" @search="buscarPorDatas"
        v-model:dataInicio="dataInicio" v-model:dataFim="dataFim" />
    <ModalDelete v-if="showModalEliminar" @close="fecharModalEliminacao" @confirm="confirmarEliminacao"
        v-model:motivo="formEliminacao.txtMotivo" :dados="formEliminacao.txtDadosEliminado"
        :loan="formEliminacao.txtLoan" :id="formEliminacao.txtId" />
    <ModalNovoComprovativo ref="modalNovoComprovativoRef" v-if="showModalNovo" @close="fecharModalNovoComprovativo"
        @save="guardarComprovativo" :bases="$page.props.bases" :tipocomprovativos="$page.props.tipocomprovativos"
        :produtos="$page.props.produtos" :bancos="$page.props.bancos" :contas="$page.props.contas"
        :formaspagamentos="$page.props.formaspagamentos" v-model="novoComprovativo" />

    <ModalObservacaoDFC :show="showModalObservacao" @close="showModalObservacao = false"
        :comprovativoreconci="comprovativoSelecionado" />

    <!-- Modal para edição do montante -->
    <ModalEdicaoMontante :show="showEditModal" @close="fecharModalEdicao" @save="salvarEdicaoMontante"
        :comprovativo="comprovativoSelecionado" :novoMontante="novoMontante" />

    <!-- Adicione este modal após os outros modais no template -->
    <ModalEdicaoData :show="showModalDataEdicao && comprovativoSelecionadoData !== null" @close="fecharModalEdicaoData"
        @save="salvarEdicaoData" :comprovativo="comprovativoSelecionadoData || {}" :novaData="novaDataRegistro" />
    <!-- Adicione este modal após os outros modais no template -->
    <ModalEdicaoVoucher :show="showModalVoucherEdicao" @close="fecharModalEdicaoVoucher" @save="salvarEdicaoVoucher"
        :comprovativo="comprovativoSelecionadoVoucher" :novoVoucher="novoVoucher" />


</template>

<style scoped>
/* Seus estilos existentes permanecem os mesmos */
.bg-aliceblue {
    background-color: aliceblue;
}

.bg-coral {
    background-color: coral;
}

/* Outros estilos personalizados */

.fade-icon-enter-active,
.fade-icon-leave-active {
    transition: all 0.2s ease;
}

.fade-icon-enter-from,
.fade-icon-leave-to {
    opacity: 0;
    transform: scale(0.8);
}

.fade-icon-enter-to,
.fade-icon-leave-from {
    opacity: 1;
    transform: scale(1);
}

/* Estilos melhorados */
.alert {
    @apply p-4 rounded-lg mb-4 border-l-4;
}

.alert-success {
    @apply bg-green-50 text-green-800 border-green-500;
}

.alert-danger {
    @apply bg-red-50 border-l-4 border-red-500 text-red-700 p-4;
}

.badge {
    display: inline-flex;
    align-items: center;
    transition: background-color 0.2s;
}

.btn {
    @apply px-4 py-2 rounded-md font-medium transition-colors flex items-center justify-center;
}

.btn-primary {
    @apply bg-gradient-to-r from-green-900 to-greenkixi-300 text-white hover:from-green-800 shadow-md hover:shadow-lg;
}

.btn-primarykxu {
    @apply bg-gradient-to-r from-orange-500 to-greenkixi-300 text-white hover:from-orange-800 shadow-md hover:shadow-lg;
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

.btn-outline-excelP {
    @apply border border-orange-950 bg-orange-100 text-orange-600 hover:bg-orange-50;
}

.btn-action {
    @apply px-3 py-1.5 rounded-md text-sm font-medium transition-colors;
}

.btn-validate {
    @apply bg-blue-50 text-blue-600 hover:bg-blue-300 border border-blue-200;
}

.btn-alertvoucher {
    @apply bg-orange-50 text-orange-600 hover:bg-orange-300 border border-orange-200;
}

.input {
    @apply border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full;
}

/* Responsividade da tabela */
@media (max-width: 640px) {
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
        @apply block mb-4 border border-gray-200 rounded-lg;
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

/* Estados */
.bg-yellow-100 {
    background-color: #fef9c3;
}

.text-yellow-800 {
    color: #92400e;
}

.bg-green-100 {
    background-color: #dcfce7;
}

.text-green-800 {
    color: #166534;
}

.bg-red-100 {
    background-color: #fee2e2;
}

.text-red-800 {
    color: #991b1b;
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

.btn-outline-consulta {
    @apply border border-gray-300 bg-white text-cyan-800 hover:bg-gray-50;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
}

/* Adicione estes estilos se necessário */
.cursor-pointer {
  cursor: pointer;
}

.card-hover:hover {
  transform: translateY(-2px);
  transition: transform 0.2s ease;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}


/* Versão minimalista - mais rápida e leve */
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: all 0.2s ease-out;
}

.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* Ou se preferir apenas fade */
.fade-only-enter-active,
.fade-only-leave-active {
  transition: opacity 0.15s ease;
}

.fade-only-enter-from,
.fade-only-leave-to {
  opacity: 0;
}

/* Para um efeito de desfoque no backdrop (mais moderno) */
.modal-backdrop {
  backdrop-filter: blur(2px);
  -webkit-backdrop-filter: blur(2px);
}

/* Transições suaves para todos os elementos interativos */
button, .btn, .card-hover {
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Melhoria de performance */
* {
  box-sizing: border-box;
}

.modal-content {
  transform: translateZ(0);
  will-change: transform, opacity;
}
</style>
