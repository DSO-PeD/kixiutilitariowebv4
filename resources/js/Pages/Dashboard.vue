<template>

    <Head :title="`${$page.component}`" />

    <div class="flex h-screen bg-gray-50">
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <main class="flex-1 overflow-y-auto p-4 lg:p-6 transition-all duration-300">
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-green-900 to-greenkixi-300 rounded-xl p-6 mb-8 text-white shadow-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold mb-2 text-orange-400">Olá, {{ $page.props.user.UtNome }}</h1>
                            <p class="opacity-90 max-w-3xl">Seja Bem-vindo ao Kixi Utilitário - Sistema Para Controlo de
                                Comprovativos e
                                Cálculo de Desembolso (Taxa de Imprevisto, Taxa de Processamento e IVA)</p>
                        </div>
                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm ">Versão 9</span>
                    </div>
                </div>

                <!-- Filtro e Período -->
                <div class="flex items-center gap-3 py-4">
                    <button @click="modalDate"
                        class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filtrar por período</span>
                    </button>
                    <div class="bg-gray-100 px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class=" text-green-900">Período: {{ periodoSelecionado }}</span>
                    </div>
                </div>

                <!-- Stats Cards - Agrupados em seções -->
                <div class="space-y-8">
                    <!-- Seção DOP -->
                    <div class="bg-white rounded-xl shadow p-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-blue-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold">Resumo das Operações da DOP</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                            <div
                                class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Nº
                                            Pendentes</h3>
                                        <p class="text-3xl font-bold mt-2 text-gray-800">

                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m21 7.5-2.25-1.313M21 7.5v2.25m0-2.25-2.25 1.313M3 7.5l2.25-1.313M3 7.5l2.25 1.313M3 7.5v2.25m9 3 2.25-1.313M12 12.75l-2.25-1.313M12 12.75V15m0 6.75 2.25-1.313M12 21.75V19.5m0 2.25-2.25-1.313m0-16.875L12 2.25l2.25 1.313M21 14.25v2.25l-2.25 1.313m-13.5 0L3 16.5v-2.25" />
                                            </svg>

                                        </p>
                                    </div>
                                    <div class="bg-red-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">
                                            Recuperações
                                        </h3>
                                        <p class="text-2xl font-bold mt-2 text-green-600 px-2">
                                            {{ $page.props.QtdRegistosRecuperacoes }}
                                            <span class="text-gray-500">/</span>
                                            {{ formatCurrency($page.props.QtdValorRegistosRecuperacoes) }} Kz
                                        </p>
                                    </div>
                                    <div class="bg-green-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">
                                            Reembolsos
                                        </h3>


                                        <p class="text-2xl font-bold mt-2 text-yellow-600 px-2">
                                            {{ $page.props.QtdRegistosComprovativos }}
                                            <span class="text-gray-500">/</span>
                                            {{ formatCurrency($page.props.QtdValorRegistosComprovativos) }} Kz
                                        </p>

                                    </div>
                                    <div class="bg-yellow-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">
                                            Desembolsos
                                        </h3>
                                        <p class="text-2xl font-bold mt-2 text-purple-600 px-2">
                                            {{ $page.props.QtdRegistosDesembosos }}
                                            <span class="text-gray-500">/</span>
                                            {{ formatCurrency($page.props.QtdValorRegistosDesembosos) }} Kz
                                        </p>
                                    </div>
                                    <div class="bg-purple-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção DFC -->
                    <div class="bg-white rounded-xl shadow p-4">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-cyan-100 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold">Resumo das Operações de Reconciliação - DFC</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                            <div
                                class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total de
                                            Comprativos
                                            sem o Paracer</h3>
                                        <p class="text-2xl font-bold mt-2 text-red-600 px-2">
                                            {{ $page.props.TotaldeRegistossemParacer }}
                                            <span class="text-gray-500">/</span>
                                            {{ formatCurrency($page.props.TotalValordeRegistossemParacer) }} Kz
                                        </p>

                                    </div>
                                    <div class="bg-red-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total de
                                            Comprovativos Respondidos
                                        </h3>
                                        <p class="text-2xl font-bold mt-2 text-blue-600 px-2">
                                            {{ $page.props.TotaldeRegistosRespondidos }}
                                            <span class="text-gray-500">/</span>
                                            {{ formatCurrency($page.props.TotalValordeRegistosRespondidos) }} Kz
                                        </p>
                                    </div>
                                    <div class="bg-blue-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total de
                                            Reconciliações Não Finalizadas
                                        </h3>
                                        <p class="text-2xl font-bold mt-2 text-orange-600 px-2">
                                            {{ $page.props.TotaldeReconciliaNaoFinalizado }}
                                            <span class="text-gray-500">/</span>
                                            {{ formatCurrency($page.props.TotalValorReconciliaNaoFinalizado) }} Kz
                                        </p>
                                    </div>
                                    <div class="bg-orange-100 p-2 rounded-full">


                                        <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24"
                                            strokeWidth={1.5} stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round"
                                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                        </svg>



                                    </div>
                                </div>
                            </div>

                            <!--div
                                class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total de
                                            Reembolsos
                                        </h3>
                                        <p class="text-3xl font-bold mt-2 text-gray-800">0 | 0 Kz</p>
                                    </div>
                                    <div class="bg-yellow-100 p-2 rounded-full">
                                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            </div -->

                        </div>
                    </div>
                </div>

                <!-- Gráficos e Tabelas -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-8">
                    <!-- Bases Operacionais -->
                    <div class="bg-white rounded-xl shadow-lg p-6 lg:col-span-2">
                        <div class="bg-white rounded-xl shadow-lg p-6 lg:col-span-2 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-semibold text-gray-800">Bases Operacionais</h2>
                                <button @click="showAllBases = !showAllBases"
                                    class="text-greenkixi-solid hover:text-green-700 text-sm font-medium flex items-center">
                                    {{ showAllBases ? 'Mostrar menos' : 'Ver tudo' }}
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>

                            <ul class="space-y-3">
                                <li v-for="base in displayedBases" :key="base.OfIdentificador"
                                    class="flex items-start p-4 hover:bg-gray-50 rounded-lg transition border border-gray-100">

                                    <div class="bg-blue-100 p-2 rounded-full mr-3 flex-shrink-0">
                                        <span class="text-blue-600">🔵</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-gray-800 truncate">[{{ base.OfCodigo }}] - {{
                                            base.OfIdentificador }}</p>
                                        <p class="text-gray-500 text-sm mt-1 truncate">{{ base.OfNombre }}</p>
                                    </div>
                                    <button class="text-greenkixi-solid hover:text-green-700 ml-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z">
                                            </path>
                                        </svg>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-6">Ações Rápidas</h2>
                        <div class="space-y-4">
                            <button @click="abrirModalNovoComprovativo" v-if="$page.props.user.rec_comprovativo"
                                class="w-full flex items-center justify-between p-4 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition group">
                                <span class="font-medium">Novo Reembolso</span>
                                <div class="flex items-center">
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2">Ctrl+R</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </div>
                            </button>

                            <button @click="showModal = true" v-if="$page.props.user.rec_extrato"
                                class="w-full flex items-center justify-between p-4 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition group">
                                <span class="font-medium">Efetuar Aplicação</span>
                                <div class="flex items-center">
                                    <span
                                        class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded mr-2">Ctrl+A</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </div>
                            </button>

                            <button @click="sms"
                                class="w-full flex items-center justify-between p-4 bg-purple-50 text-purple-600 rounded-lg hover:bg-purple-100 transition group">
                                <span class="font-medium">Fecho Diário</span>
                                <div class="flex items-center">
                                    <span
                                        class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded mr-2">Ctrl+F</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </div>
                            </button>
                        </div>

                    </div>
                </div>
            </main>
        </div>

    </div>

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

            <h2 class="text-lg font-semibold text-gray-800 mb-4">Funcionalidade em Desenvolvimento, estará disponível o
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
                    <br/>
                    <button @click="limparFiltro"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Limpar
                    </button>

                </div>
            </div>
        </div>
    </Modal>


</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import * as XLSX from 'xlsx'
import { Head } from '@inertiajs/vue3'
import ModalNovoComprovativo from './Layouts/components/ComprovativosComponents/ModalNovoComprovativo.vue'
import ModalNovoCalculo from './Layouts/components/ExtratosComponents/ModalNovoCalculo.vue'
import Modal from './Layouts/components/ModalExit.vue';


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

const showAllBases = ref(false);

const props = defineProps({
    session: Object
});

const displayedBases = computed(() => {
    if (!props.session.bases_operacionais) return [];
    return showAllBases.value
        ? props.session.bases_operacionais
        : props.session.bases_operacionais.slice(0, 5); // mostra só os 5 primeiros
});



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
        search:1
    }, {
        preserveState: true,
        replace: true
    });
};



</script>

<style scoped>
/* Melhorias adicionais */
.hover\:shadow-md {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.transition-shadow {
    transition-property: box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
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

/* Adicione no final do seu style scoped */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.section-header {
    border-left: 4px solid #08583d;
    padding-left: 12px;
}

.stat-card {
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
</style>
