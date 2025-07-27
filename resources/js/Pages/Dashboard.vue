<template>

    <Head :title="`${$page.component}`" />



    <div class="flex flex-col min-h-screen font-sans">



        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto  ">
            <!-- Welcome Banner -->

            <div
                class="bg-gradient-to-r from-green-800 to-green-700 rounded-xl p-6 mb-8 text-white shadow-lg relative overflow-hidden">
                <!-- Decorative elements -->
                <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-white bg-opacity-10"></div>
                <div class="absolute -right-5 -bottom-5 w-20 h-20 rounded-full bg-orange-300 bg-opacity-20"></div>

                <div class="flex items-start relative z-10 gap-6">
                    <!-- Icon container with better framing -->
                    <div class="flex-shrink-0 p-3 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="h-20 w-20 text-orange-300">
                            <path fill-rule="evenodd"
                                d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>

                    <!-- Main content area -->
                    <div class="flex-1 flex flex-col">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <!-- Text content -->
                            <div class="space-y-2">
                                <h1 class="text-3xl font-bold text-orange-300">Olá, {{ $page.props.user.UtNome }}</h1>
                                <h4 class="text-xl font-thin text-orange-100 flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="size-6">
                                        <path fill-rule="evenodd"
                                            d="M7.5 5.25a3 3 0 0 1 3-3h3a3 3 0 0 1 3 3v.205c.933.085 1.857.197 2.774.334 1.454.218 2.476 1.483 2.476 2.917v3.033c0 1.211-.734 2.352-1.936 2.752A24.726 24.726 0 0 1 12 15.75c-2.73 0-5.357-.442-7.814-1.259-1.202-.4-1.936-1.541-1.936-2.752V8.706c0-1.434 1.022-2.7 2.476-2.917A48.814 48.814 0 0 1 7.5 5.455V5.25Zm7.5 0v.09a49.488 49.488 0 0 0-6 0v-.09a1.5 1.5 0 0 1 1.5-1.5h3a1.5 1.5 0 0 1 1.5 1.5Zm-3 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z"
                                            clip-rule="evenodd" />
                                        <path
                                            d="M3 18.4v-2.796a4.3 4.3 0 0 0 .713.31A26.226 26.226 0 0 0 12 17.25c2.892 0 5.68-.468 8.287-1.335.252-.084.49-.189.713-.311V18.4c0 1.452-1.047 2.728-2.523 2.923-2.12.282-4.282.427-6.477.427a49.19 49.19 0 0 1-6.477-.427C4.047 21.128 3 19.852 3 18.4Z" />
                                    </svg>
                                    &ThinSpace;
                                    {{ $page.props.user.UtFuncao }}
                                </h4>

                                <div class="flex items-center gap-2 text-gray-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-5 h-5 text-orange-300">
                                        <path
                                            d="M19.006 3.705a.75.75 0 1 0-.512-1.41L6 6.838V3a.75.75 0 0 0-.75-.75h-1.5A.75.75 0 0 0 3 3v4.93l-1.006.365a.75.75 0 0 0 .512 1.41l16.5-6Z" />
                                        <path fill-rule="evenodd"
                                            d="M3.019 11.114 18 5.667v3.421l4.006 1.457a.75.75 0 1 1-.512 1.41l-.494-.18v8.475h.75a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5H3v-9.129l.019-.007ZM18 20.25v-9.566l1.5.546v9.02H18Zm-9-6a.75.75 0 0 0-.75.75v4.5c0 .414.336.75.75.75h3a.75.75 0 0 0 .75-.75V15a.75.75 0 0 0-.75-.75H9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>AGÊNCIA:&space  {{ $page.props.agencia_principal }}</span>
                                </div>
                            </div>

                            <!-- Version info -->
                            <div class="flex flex-col items-end">
                                <span class="bg-white bg-opacity-20 px-4 py-2 rounded-full text-sm font-medium">Kixi
                                    Utilitário | Versão 9.1</span>
                                <p class="text-xs text-white text-opacity-80 mt-2 text-right max-w-xs">
                                    Seja Bem vindo,Sistema interno para o controlo de reembolsos,<br />
                                    cálculos de desembolsos e recuperação de crédito.<br />
                                    <span class="font-bold text-teal-300">Mais cliente, menos risco.</span>
                                </p>
                            </div>
                        </div>

                        <!-- Additional space for future content if needed -->
                        <div class="mt-4 border-t border-white border-opacity-10 pt-3">
                            <!-- Could add status indicators or quick actions here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtro e Período -->
            <div class="flex items-center gap-3 py-4 mb-6">
                <button @click="modalDate"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span>Filtrar por período</span>
                </button>
                <div class="bg-gray-100 px-4 py-2 rounded-lg text-sm flex items-center gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-green-800">Período: {{ periodoSelecionado }}</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Seção DOP -->
                <div class="bg-white rounded-xl shadow p-6" v-if="$page.props.auth.user?.UtAgencia !== 99">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-blue-100 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Resumo das Operações da DOP</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <StatCard title="Reembolsos Pendentes" :value="TotaldeRegistosdeReembolsosPendentes"
                            :secondaryValue="formatCurrency(TotalValordeReembolsosPendentes) + ' Kz'" iconColor="red"
                            icon="clock" />

                        <StatCard title="Recuperações" :value="QtdRegistosRecuperacoes"
                            :secondaryValue="formatCurrency(QtdValorRegistosRecuperacoes) + ' Kz'" iconColor="green"
                            icon="check-circle" />

                        <StatCard title="Reembolsos" :value="QtdRegistosComprovativos"
                            :secondaryValue="formatCurrency(QtdValorRegistosComprovativos) + ' Kz'" iconColor="yellow"
                            icon="currency-dollar" />

                        <StatCard title="Desembolsos" :value="QtdRegistosDesembosos"
                            :secondaryValue="formatCurrency(QtdValorRegistosDesembosos) + ' Kz'" iconColor="purple"
                            icon="arrow-down" />
                    </div>
                </div>

                <!-- Seção DFC -->
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-cyan-100 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Resumo das Operações de Reconciliação - DFC</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <StatCard title="Comprovativos sem Parecer" :value="TotaldeRegistossemParacer"
                            :secondaryValue="formatCurrency(TotalValordeRegistossemParacer) + ' Kz'" iconColor="red"
                            icon="exclamation" />

                        <StatCard title="Comprovativos Respondidos" :value="TotaldeRegistosRespondidos"
                            :secondaryValue="formatCurrency(TotalValordeRegistosRespondidos) + ' Kz'" iconColor="blue"
                            icon="c-respo" />

                        <StatCard title="Reconciliações Não Finalizadas" :value="TotaldeReconciliaNaoFinalizado"
                            :secondaryValue="formatCurrency(TotalValorReconciliaNaoFinalizado) + ' Kz'"
                            iconColor="orange" icon="exclamation-triangle" />
                    </div>
                </div>
            </div>

            <!-- Lower Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Bases Operacionais -->
                <div class="bg-white rounded-xl shadow-lg p-6 lg:col-span-2">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-800">Bases Operacionais</h2>
                        <button @click="showAllBases = !showAllBases"
                            class="text-greenkixi-solid hover:text-green-700 text-sm font-medium flex items-center">
                            {{ showAllBases ? 'Mostrar menos' : 'Ver tudo' }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>

                    <ul class="space-y-3 max-h-96 overflow-y-auto pr-2">
                        <li v-for="base in displayedBases" :key="base.OfIdentificador"
                            class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition border border-gray-100">
                            <div class="bg-blue-100 p-2 rounded-full mr-3">
                                <span class="text-blue-600">🔵</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">[{{ base.OfCodigo }}] - {{
                                    base.OfIdentificador }}</p>
                                <p class="text-gray-500 text-sm mt-1 truncate">{{ base.OfNombre }}</p>
                            </div>
                            <button class="text-greenkixi-solid hover:text-green-700 ml-2 p-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                                </svg>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Ações Rápidas</h2>
                    <div class="space-y-3">
                        <QuickActionButton v-if="$page.props.auth.user?.rec_comprovativo"
                            @click="abrirModalNovoComprovativo" color="blue" label="Novo Reembolso" shortcut="Ctrl+R"
                            icon="plus-circle" />

                        <QuickActionButton v-if="$page.props.auth.user?.rec_extrato" @click="showModal = true"
                            color="green" label="Efetuar Aplicação" shortcut="Ctrl+A" icon="calculator" />

                        <QuickActionButton @click="sms" color="purple" label="Fecho Diário" shortcut="Ctrl+F"
                            icon="lock-closed" />
                    </div>
                </div>
            </div>
        </main>



        <!-- Modals (mantidos do código anterior) -->

        <ModalNovoComprovativo ref="modalNovoComprovativoRef" v-if="showModalNovo" @close="fecharModalNovoComprovativo"
            @save="guardarComprovativo" :bases="$page.props.bases" :tipocomprovativos="$page.props.tipocomprovativos"
            :produtos="$page.props.produtos" :bancos="$page.props.bancos" :contas="$page.props.contas"
            :formaspagamentos="$page.props.formaspagamentos" v-model="novoComprovativo" />

        <ModalNovoCalculo :show="showModal" :form="form" @update:form="(newValue) => form = newValue"
            @close="showModal = false" @submit="submitForm" :bases="$page.props.bases"
            :produtosext="$page.props.produtosextratos" :bancos="$page.props.lista_banco"
            :contas="$page.props.lista_bancos_contas" :atividades="$page.props.lista_actividade_economica"
            :nesGrupos="$page.props.lista_nes_grupo" :nesTipos="$page.props.lista_nes_tipo" v-model="form" />

        <!-- Modal de Em Desenvolvimento -->
        <Modal :show="showModalSMS" @close="showModalSMS = false">
            <div class="p-6">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                </svg>

                <h2 class="text-lg font-semibold text-gray-800 mb-4">Funcionalidade em Desenvolvimento, estará
                    disponível o
                    mais breve.</h2>
                <div class="flex justify-end space-x-3">
                    <button @click="showModalSMS = false"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>

                </div>
            </div>
        </Modal>


        <!-- Modal de Date -->
        <Modal :show="showModalDate" @close="showModalDate = false">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-cyan-600 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-800">Filtrar por Período</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Data de Início -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Data de Início</label>
                        <div class="relative">
                            <input v-model="startDate" type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-500 transition"
                                :max="endDate || ''" />
                        </div>
                    </div>

                    <!-- Data de Fim -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Data de Fim</label>
                        <div class="relative">
                            <input v-model="endDate" type="date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-cyan-600 focus:border-cyan-500 transition"
                                :min="startDate || ''" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-between">
                    <button @click="aplicarFiltroHoje"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        Hoje
                    </button>
                    <div class="space-x-3">
                        <button @click="aplicarFiltro"
                            class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Aplicar
                        </button>
                        <br />
                        <button @click="limparFiltro"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                            Limpar
                        </button>

                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { Head, usePage } from '@inertiajs/vue3'
import ModalNovoComprovativo from './Layouts/components/ComprovativosComponents/ModalNovoComprovativo.vue'
import ModalNovoCalculo from './Layouts/components/ExtratosComponents/ModalNovoCalculo.vue'
import Modal from './Layouts/components/ModalExit.vue';
import StatCard from '../Pages/Layouts/components/StatCard.vue'
import QuickActionButton from '../Pages/Layouts/components/QuickActionButton.vue'


// Dados da página
const page = usePage()

 //Acessando os dados do novo AuthController
const {
  QtdRegistosComprovativos,
  QtdValorRegistosComprovativos,
  QtdRegistosRecuperacoes,
  QtdValorRegistosRecuperacoes,
  QtdRegistosDesembosos,
  QtdValorRegistosDesembosos,
  TotaldeRegistossemParacer,
  TotalValordeRegistossemParacer,
  TotaldeRegistosRespondidos,
  TotalValordeRegistosRespondidos,
  TotaldeReconciliaNaoFinalizado,
  TotalValorReconciliaNaoFinalizado,
  TotaldeRegistosdeReembolsosPendentes,
  TotalValordeReembolsosPendentes,
  bases,
  produtos,
  bancos,
  contas,
  formaspagamentos,
  produtosextratos,
  lista_banco,
  lista_bancos_contas,
  lista_actividade_economica,
  lista_nes_grupo,
  lista_nes_tipo,
  tipocomprovativos
} = page.props



const dataFormatada = ref('');
const startDate = ref('');
const endDate = ref('');
const periodoSelecionado = ref('');

const formatarData = () => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    dataFormatada.value = new Date().toLocaleDateString('pt-BR', options);
};

const inicializarDatas = () => {
    const today = new Date().toISOString().split('T')[0];
    startDate.value = today;
    endDate.value = today;
    periodoSelecionado.value = new Date(today).toLocaleDateString('pt-BR');
};

onMounted(() => {
    formatarData();
    inicializarDatas();
    // Você pode chamar aplicarFiltro() aqui se quiser carregar os dados automaticamente
});


const props = defineProps({
    session: Object
});

/*const displayedBases = computed(() => {
    if (!props.session.bases_operacionais) return [];
    return showAllBases.value
        ? props.session.bases_operacionais
        : props.session.bases_operacionais.slice(0, 5); // mostra só os 5 primeiros
});*/

// Bases operacionais
const showAllBases = ref(false)
const displayedBases = computed(() => {
  return showAllBases.value ? bases : bases.slice(0, 5)
})



const showModalNovo = ref(false);
const modalNovoComprovativoRef = ref(null);
const showModal = ref(false);
const showModalSMS = ref(false)
const showModalDate = ref(false)



const sms = () => {
    showModalSMS.value = true;

};

const modalDate = () => {
    showModalDate.value = true;

};

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
    txtVoucher: '',
    txtInfoAdicional: '',
    selectFormaPagamento: '',
    telefone: ''
})

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
        txtVoucher: '',
        txtInfoAdicional: '',
        selectFormaPagamento: ''
    }
}
const fecharModalNovoComprovativo = () => {
    showModalNovo.value = false
}
const guardarComprovativo = async () => {
    try {
        const formData = new FormData();

        Object.entries(novoComprovativo.value).forEach(([key, value]) => {
            if (value) formData.append(key, value);
        });

        if (modalNovoComprovativoRef.value?.selectedFile) {
            formData.append('anexo', modalNovoComprovativoRef.value.selectedFile);
        }

        await router.post('/guardar-comprovativo', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            onSuccess: () => {
                fecharModalNovoComprovativo();
                modalNovoComprovativoRef.value?.resetFileInput();
            }
        });
    } catch (error) {
        console.error('Erro ao enviar comprovativo:', error);
    }
};
// Formulário
const form = useForm({
    // (Manter o mesmo formulário existente)
    selectBase: '',
    txtNumeroLoan: '',
    // ... outros campos do formulário
});

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
};


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



const aplicarFiltroHoje = () => {
    const today = new Date().toISOString().split('T')[0];
    startDate.value = today;
    endDate.value = today;
    aplicarFiltro();
};

const limparFiltro = () => {
    startDate.value = '';
    endDate.value = '';
    aplicarFiltro();
};

const aplicarFiltro = () => {
    showModalDate.value = false;

    // Formatar o período selecionado para exibição
    if (startDate.value && endDate.value) {
        const start = new Date(startDate.value).toLocaleDateString('pt-BR');
        const end = new Date(endDate.value).toLocaleDateString('pt-BR');
        periodoSelecionado.value = start === end ? start : `${start} a ${end}`;
    } else {
        periodoSelecionado.value = 'Todos os períodos';
    }

    // Fazer a requisição para o backend
    router.get('/dashboard', {
        start_date: startDate.value,
        end_date: endDate.value,
        search: 1
    }, {
        preserveState: true,
        replace: true
    });
};

</script>

<style scoped>
/* Estilos globais melhorados */
main {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

main::-webkit-scrollbar {
    width: 8px;
}

main::-webkit-scrollbar-track {
    background: #f7fafc;
}

main::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 4px;
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

.from-green-2808 {
    --tw-gradient-from: #28755a var(--tw-gradient-from-position);
    --tw-gradient-to: rgb(5 46 22 / 0) var(--tw-gradient-to-position);
    --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to);
}

.to-green-2808 {
    --tw-gradient-to: #085137 var(--tw-gradient-to-position);
}

/* Transições suaves */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
