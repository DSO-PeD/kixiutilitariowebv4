<template>

    <Head title="Gestão de Desembolsos" />

    <div class="container mx-auto px-4 py-6 max-w-full">
        <!-- Alertas de Sistema -->
        <div v-if="$page.props.flash.success" class="alert alert-success mb-4 animate-fade-in">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $page.props.flash.success }}
            </div>
        </div>

        <div v-if="$page.props.flash.error" class="alert alert-danger mb-4 animate-fade-in">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                {{ $page.props.flash.error }}
            </div>
        </div>

        <ConfirmationModalExtrato :show="showDeleteModal" :extratoData="selectedExtrato" :isDeleting="isDeleting"
            @confirm="proceedWithDeletion" @cancel="cancelDeletion" />

        <!-- Cabeçalho Principal -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-money-bill-wave text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Gestão de Desembolsos</h1>
                    <p class="text-sm text-gray-600 mt-1">Aplicações e controle de desembolsos</p>
                </div>
            </div>

            <div v-if="sistemaAberto" class="flex flex-col sm:flex-row gap-3">
                <button class="btn btn-primary flex items-center gap-2" @click="showModal = true">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Nova Aplicação
                </button>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <!-- Filtros Avançados -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6 mb-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Filtros de Pesquisa</h2>
                <button @click="toggleFiltros"
                    class="text-green-600 hover:text-green-800 text-sm font-medium flex items-center">
                    {{ filtrosVisiveis ? 'Ocultar Filtros' : 'Mostrar Filtros' }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            :d="filtrosVisiveis ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"></path>
                    </svg>
                </button>
            </div>

            <div v-if="filtrosVisiveis" class="transition-all duration-300 ease-in-out space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Loan Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Número do Empréstimo</label>
                        <button class="btn btn-primary-filter w-full flex items-center gap-2"
                            @click="showModalLoan = true">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z">
                                </path>
                            </svg>
                            Pesquisar Loan
                        </button>
                    </div>

                    <!-- Período -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Período</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <input v-model="filtro.dataInicioInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm"
                                    :max="filtro.dataFimInput" @change="validarDatas" />
                                <span v-if="erros.dataInicio" class="text-red-500 text-xs">{{ erros.dataInicio }}</span>
                            </div>
                            <div>
                                <input v-model="filtro.dataFimInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm"
                                    :min="filtro.dataInicioInput" @change="validarDatas" />
                                <span v-if="erros.dataFim" class="text-red-500 text-xs">{{ erros.dataFim }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agência -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Agência</label>
                        <select v-model="filtro.agencia" class="select-input">
                            <option disabled :value="'s/a'">Escolha agência</option>
                            <option v-for="agencia in $page.props.bases" :value="agencia.OfIdentificador"
                                :key="agencia.OfIdentificador">
                                {{ agencia.OfIdentificador }} - {{ agencia.OfNombre }}
                            </option>
                            <option :value="'T'">Todas as agências</option>
                        </select>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div
                    class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 pt-4 border-t border-gray-200">
                    <button @click="resetarFiltros" class="btn btn-outline-secondary flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Limpar Filtros
                    </button>
                    <button @click="aplicarFiltros" class="btn btn-primary-filter flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Aplicar Filtros
                    </button>
                </div>
            </div>
        </div>




        <div class="bg-gray-100 rounded-xl shadow-sm p-5 mb-6 border border-white">
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-4 mb-4 border-b border-white">
                <div class="flex items-center">
                    <div class="bg-blue-50 p-2 rounded-lg mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Processos Aplicados no periodo:</h3>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium text-green-700">{{ dataFimPeriodo }}</span>
                            <span class="mx-2">até</span>
                            <span class="font-medium text-green-700">{{ dataInicioPeriodo }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Cards de métricas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Card 1 - Total Montante Reembolsos -->
                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200 shadow-sm">




                    <!-- Card Total Desembolsado -->

                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-3 rounded-full mr-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700">Total Desembolsado</h3>
                    </div>
                    <p class="text-2xl font-bold text-green-700">{{ formatCurrency(montantetotal) }} AKZ</p>

                </div>

                <!-- Card 2 - Total de Poupanças -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200 shadow-sm">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-3 rounded-full mr-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-700">Processos Aplicados</h3>
                    </div>
                    <p class="text-2xl font-bold text-blue-700">{{ total }} itens</p>


                </div>


            </div>
        </div>

        <!-- Tabela de Desembolsos -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
            <!-- Cabeçalho da Tabela -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                    de {{ totalItens }} registros
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button class="btn btn-outline-excel flex items-center gap-2" @click="exportarParaExcel">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Exportar Excel
                    </button>

                    <div class="flex gap-2">
                        <button :disabled="paginaAtual === 1" @click="mudarPagina(paginaAtual - 1)"
                            class="btn btn-outline px-3"
                            :class="{ 'opacity-50 cursor-not-allowed': paginaAtual === 1 }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <div class="flex items-center bg-gray-100 rounded-lg px-3">
                            <span class="text-sm font-medium">Página {{ paginaAtual }}</span>
                        </div>
                        <button :disabled="!hasMorePages" @click="mudarPagina(paginaAtual + 1)"
                            class="btn btn-outline px-3" :class="{ 'opacity-50 cursor-not-allowed': !hasMorePages }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabela -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
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

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
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
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">

                                <div class="flex items-center text-center gap-1">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                                    </svg>


                                    Ref. Pagamento
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">

                                <div class="flex items-center text-center gap-1">


                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        strokeWidth={1.5} stroke="currentColor" class="w-4 h-4">
                                        <path strokeLinecap="round" strokeLinejoin="round"
                                            d="M20.25 3.75v4.5m0-4.5h-4.5m4.5 0-6 6m3 12c-8.284 0-15-6.716-15-15V4.5A2.25 2.25 0 0 1 4.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.054.902-.417 1.173l-1.293.97a1.062 1.062 0 0 0-.38 1.21 12.035 12.035 0 0 0 7.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 0 1 1.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 0 1-2.25 2.25h-2.25Z" />
                                    </svg>



                                    Contacto
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(item, index) in extratosPaginados" :key="index"
                            class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ calcularNumeroLinha(index) }}
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
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-semibold text-green-600">
                                {{ formatCurrency(item.ValorTotalCredito) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button v-if="item.RefPgtActivo === 0" @click="abrirModalActivarRerencia(item)"
                                    class="btn btn-outline-warning btn-sm flex items-center gap-1 mx-auto">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z">
                                        </path>
                                    </svg>
                                    {{ item.referenciapagamento }}
                                </button>

                                <button v-else
                                    class="btn btn-outline-success btn-sm flex items-center gap-1 mx-auto cursor-not-allowed"
                                    disabled>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m4.5 12.75 6 6 9-13.5"></path>
                                    </svg>
                                    {{ item.referenciapagamento }}
                                </button>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-semibold text-black-600">

                                <button @click="abrirModalEditarTelefone(item)" title="Alterar Contacto do Cliente"
                                    class="btn-sm flex items-center gap-1">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10">
                                        </path>
                                    </svg>
                                    <span v-if="item.Telefone != 'Nenhum'">{{ formatTelefone(item.Telefone) }}</span>
                                </button>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex space-x-2 justify-center">
                                    <a :href="`/reports/extrato/${item.Num}`"
                                        class="btn btn-outline-primary btn-sm flex items-center gap-1" target="_blank">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                            </path>
                                        </svg>
                                        PDF
                                    </a>
                                    <button @click="abrirModalDetalhes(item)"
                                        class="btn btn-outline-info btn-sm flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z">
                                            </path>
                                        </svg>
                                        Detalhes
                                    </button>
                                    <button @click="initiateDeletion(item)" :disabled="!podeEliminar(item)"
                                        class="btn btn-outline-danger btn-sm flex items-center gap-1"
                                        :class="{ 'opacity-50 cursor-not-allowed': !podeEliminar(item) }">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                            </path>
                                        </svg>
                                        Eliminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="extratosPaginados.length === 0">
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <p class="text-sm">Nenhum desembolso encontrado</p>
                                    <p class="text-xs text-gray-400 mt-1">Tente ajustar os filtros de pesquisa</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação Inferior -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                    de {{ totalItens }} registros
                </div>
                <div class="flex gap-2">
                    <button :disabled="paginaAtual === 1" @click="mudarPagina(paginaAtual - 1)"
                        class="btn btn-outline px-3" :class="{ 'opacity-50 cursor-not-allowed': paginaAtual === 1 }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                    </button>
                    <div class="flex items-center bg-gray-100 rounded-lg px-3">
                        <span class="text-sm font-medium">Página {{ paginaAtual }}</span>
                    </div>
                    <button :disabled="!hasMorePages" @click="mudarPagina(paginaAtual + 1)" class="btn btn-outline px-3"
                        :class="{ 'opacity-50 cursor-not-allowed': !hasMorePages }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
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
        :grupoatividades="$page.props.lista_grupo_actividade_economica" :nesGrupos="$page.props.lista_nes_grupo"
        :nesTipos="$page.props.lista_nes_tipo" v-model="form" />

    <ModalDetalhesExtrato :show="showModalDetalhes" @close="showModalDetalhes = false" :extrato="extratoSelecionado" />

    <ModalActivarReferencia :show="showModalActivarRefencia" @close="showModalActivarRefencia = false"
        :extratoref="extratoSelecionado" />

    <ModalLoan :isOpen="showModalLoan" @close="showModalLoan = false" @search="buscarPorLoan" v-model="filtroLoan" />

    <ModalEditarTelefone :show="showModalEditarTelefone" :extratoSelecionado="extratoSelecionado"
        @close="showModalEditarTelefone = false" @telefoneAtualizado="onTelefoneAtualizado" />
</template>


<script setup>
import { ref, computed, watch } from 'vue'
import * as XLSX from 'xlsx'
import { router, useForm } from '@inertiajs/vue3'
import ModalFiltrarData from './Layouts/components/ExtratosComponents/ModalFiltrarData.vue'
import ModalNovoCalculo from './Layouts/components/ExtratosComponents/ModalNovoCalculo.vue'
import ModalDetalhesExtrato from './Layouts/components/ExtratosComponents/ModalDetalhesExtrato.vue'
import ModalActivarReferencia from './Layouts/components/ExtratosComponents/ModalActivarReferencia.vue'
import ModalLoan from './Layouts/components/ComprovativosComponents/ModalLoan.vue'
import ConfirmationModalExtrato from './Layouts/components/ExtratosComponents/ConfirmationModalExtrato.vue'
import ModalEditarTelefone from './Layouts/components/ExtratosComponents/ModalEditarTelefone.vue'

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

    total: Number,
    dataInicioInput: String,
    dataFimInput: String,
    montantetotal: Number,
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
    dataInicioInput: String,
    dataFimInput: String,
    page: Number,
    hasMorePages: Boolean,
    perPage: {
        type: Number,
        default: 100
    },
    dataInicioPeriodo: String,
    dataFimPeriodo: String

})

// Configuração da paginação
const perPage = ref(100);
const paginaAtual = ref(1);

// Dados locais para paginação
const dadosLocais = ref([]);


// Estados
const showModal = ref(false)
const showModalLoan = ref(false)
const showModalData = ref(false)
const showModalDetalhes = ref(false)
const showModalActivarRefencia = ref(false)
const sistemaAberto = props.sistema_aberto
const dataInicio = ref('')
const dataFim = ref('')
const extratoSelecionado = ref(null)
const filtroLoan = ref('')
const dataInicioInput = ref(props.dataInicioInput || '')
const dataFimInput = ref(props.dataFimInput || '')
const dateError = ref('')
const erros = ref({
    dataInicio: '',
    dataFim: ''
})



const showDeleteModal = ref(false)

const selectedExtrato = ref({
    lnr: '',
    cliente: '',
    montante: 0,
    data: '',


})

// Estados
const showModalEditarTelefone = ref(false);

// Função para abrir o modal de editar telefone
const abrirModalEditarTelefone = (extrato) => {
    extratoSelecionado.value = extrato;
    showModalEditarTelefone.value = true;
};

// Função para quando o telefone for atualizado
const onTelefoneAtualizado = (novoTelefone) => {
    // Atualizar o telefone no extrato selecionado
    if (extratoSelecionado.value) {
        extratoSelecionado.value.Telefone = novoTelefone;
    }
};


// Watch para atualizar dadosLocais quando lista_comprovativo mudar
watch(() => props.lista_extrato, (newVal) => {
    dadosLocais.value = newVal;
    paginaAtual.value = 1; // Resetar para primeira página
}, { immediate: true });

// Computed property para os dados paginados
const extratosPaginados = computed(() => {
    const start = (paginaAtual.value - 1) * perPage.value;
    const end = start + perPage.value;
    return dadosLocais.value.slice(start, end);
});

// Computed properties auxiliares
const totalItens = computed(() => dadosLocais.value.length);
const hasMorePages = computed(() => paginaAtual.value * perPage.value < dadosLocais.value.length);

// Função para mudar de página (client-side)
const mudarPagina = (novaPagina) => {
    paginaAtual.value = novaPagina;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const extratosFiltrados = computed(() => {
    return props.extratos // Agora usamos diretamente os extratos recebidos do backend
})
const extratoTotalFiltrado = computed(() => {
    return props.montanteFiltrado || 0 // Usamos o valor calculado no backend
})

const hoje = computed(() => new Date().toISOString().split('T')[0])

const podeEliminar = (item) => {

    const dataItem = new Date(item.CiFecha).toISOString().split('T')[0] // só pega a data

    const isRegistadoHoje = dataItem === hoje.value
    const temPermissao = props.user.elimina_confirmado_exportado == 1

    return isRegistadoHoje || temPermissao
}
const initiateDeletion = (item) => {

    if (!podeEliminar(item)) return

    // Prepara os dados para exibir no modal
    selectedExtrato.value = {
        lnr: item.Lnr || 'N/A',
        cliente: item.Cliente || 'N/A',
        produto: item.Produto || 0,
        data: item.CiFecha || 'N/A',
        montante: item.ValorCreditoNoContrato || 'N/A',
        id: item.Num,

    }

    showDeleteModal.value = true
}
const isDeleting = ref(false)

const proceedWithDeletion = async () => {
    isDeleting.value = true

    try {

        await router.post("/eliminar-extrato", {
            id: selectedExtrato.value.id,

        }, {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false
                // Opcional: Mostrar notificação de sucesso
            },
            onError: (errors) => {
                // Opcional: Mostrar notificação de erro
                console.error('Erro ao eliminar:', errors)
            }
        })
    } catch (error) {
        console.error('Erro inesperado:', error)
    } finally {
        isDeleting.value = false
    }
}

const cancelDeletion = () => {
    selectedExtrato.value = {
        lnr: '',
        cliente: '',
        montante: 0,
        data: ''

    }
    showDeleteModal.value = false
}

// Formulário
const form = useForm({
    // (Manter o mesmo formulário existente)
    selectBase: '',
    txtNumeroLoan: '',
    // ... outros campos do formulário
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
function formatTelefone(telefone) {
    if (!telefone) return "";
    // Garante que seja string
    telefone = String(telefone);
    // Quebra em blocos de 3
    return telefone.replace(/(\d{3})(?=\d)/g, "$1 ");
}

const calcularNumeroLinha = (index) => {
    return (paginaAtual.value - 1) * props.perPage + index + 1
}


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

    router.get('/extratos', {
        search_input: filtro.value.search,
        lnr_imput: filtro.value.lnr,
        estado_input: filtro.value.estado,
        agencia_imput: filtro.value.agencia,
        data_inicio_imput: filtro.value.dataInicioInput,
        data_fim_imput: filtro.value.dataFimInput,
        tipo: 4
    }, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            paginaAtual.value = 1; // Resetar paginação
        }
    });
};

// Função resetarFiltros
const resetarFiltros = () => {
    filtro.value = {
        search: '',
        lnr: '',
        estado: 28,
        agencia: 'T',
        dataInicioInput: '',
        dataFimInput: ''
    };

    router.get('/extratos', {
        page: 1
    }, {
        preserveState: true,
        replace: true
    });
};


// Filtros
const filtro = ref({
    search: props.filters?.search || '',
    lnr: props.filters?.lnr || '',
    estado: props.filters?.estado || 28,
    agencia: props.filters?.agencia || 'T',
    dataInicioInput: props.filters?.data_inicio || '',
    dataFimInput: props.filters?.data_fim || ''
})
const buscarPorLoan = () => {
    router.get('/extratos', { tipo: 3, loan: filtroLoan.value }, { preserveState: true })
    showModalLoan.value = false
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
    try {
        // Acessando a lista_comprovativo corretamente (dependendo do seu contexto)
        let listaCompleta;

        // Opção 1: Se estiver usando Inertia.js em Composition API
        if (typeof usePage !== 'undefined') {
            const { props } = usePage();
            listaCompleta = props.value.lista_extrato;
        }
        // Opção 2: Se estiver usando Options API
        else if (this && this.$page && this.$page.props) {
            listaCompleta = this.$page.props.lista_extrato;
        }
        // Opção 3: Se a lista estiver disponível como prop no componente
        else if (props && props.lista_extrato) {
            listaCompleta = props.lista_extrato;
        }
        // Opção 4: Se estiver disponível diretamente no escopo
        else if (typeof lista_extrato !== 'undefined') {
            listaCompleta = lista_extrato;
        }
        else {
            throw new Error('Não foi possível encontrar a lista de comprovativos');
        }

        // Verifica se há dados
        if (!listaCompleta || listaCompleta.length === 0) {
            alert('Nenhum dado disponível para exportar');
            return;
        }




        // Formata os dados
        const dadosFormatados = listaCompleta.map((extrato, index) => {
            // alert(extrato.CiFecha)
            try {
                return {
                    '#': index + 1,
                    'Data': extrato.CiFecha ? new Date(extrato.CiFecha).toLocaleString('pt-PT') : '-',
                    'Registado': extrato.UtCodigo || '-',
                    'OficialCredito': extrato.OficialCredito || '-',
                    'Loan Number': extrato.Lnr || '-',
                    'Cliente': extrato.Cliente || '-',
                    'Produto': extrato.Produto || '-',
                    'Valor Credito No Contrato': extrato.ValorCreditoNoContrato || '-',
                    'Colateral': extrato.PercColateral || '-',
                    'Valor Do Colateral': extrato.ValorDoColateral || '-',
                    'Colateral Deduzido': extrato.PercColateralDeduzido || '-',
                    'Valor Do Colateral Deduzido': extrato.ValorDoColateralDeduzido || '-',
                    'Valor Do Credito': extrato.ValorDoCredito || '-',
                    'Valor Total Credito': extrato.ValorTotalCredito || '-',
                    'Tipo da Taxa Processamento': extrato.TaxaProcessamento || '-',
                    'Taxa de Processamento': extrato.TXAProcePercenta || '-',
                    'Valor da Taxa de Processamento': extrato.TXAProcePercentaValor || '-',
                    'Valor IVA Taxa Processamento': extrato.ValorIVATaxaProcessamento || '-',
                    'Taxa Processamento Antecipado': extrato.TaxaProcessamentoAnte || '-',
                    '% Taxa de Processamento Antecipado': extrato.TXAProcePercentaAnte || '-',
                    'Valor  Taxa de Processamento Antecipado': extrato.TXAProcePercentaValorAnte || '-',
                    'Valor IVA Taxa Processamento Antecipado': extrato.ValorIVATaxaProcessamentoAnte || '-',
                    'Taxa Imprevisto': extrato.TaxaImprevisto || '-',
                    'TXAImprePercenta': extrato.TXAImprePercenta || '-',
                    'TXAImprePercentaValor': extrato.TXAImprePercentaValor || '-',
                    'ValorIVATaxaImprevisto': extrato.ValorIVATaxaImprevisto || '-',

                    'Actividade Economica': extrato.DescricaoActividadeEconomica || '-',
                    'Codigo Atividade Economica': extrato.CodigoAtividade || '-',
                    'Sector': extrato.Sector || '-',
                    'Magnitude': extrato.Magnitude || '-',
                    'RendaMensal': extrato.RendaMensal || '-',

                    'PPE': extrato.ppe || '-',
                    'Referencia de Pagamento': extrato.referenciapagamento || '-'


                };
            } catch (error) {
                console.error('Erro ao formatar registro:', extrato, error);
                return null;
            }
        }).filter(record => record !== null);

        if (dadosFormatados.length === 0) {
            alert('Nenhum dado válido para exportar após formatação');
            return;
        }

        // Cria a planilha
        const ws = XLSX.utils.json_to_sheet(dadosFormatados);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "Extrato");

        // Gera o nome do arquivo
        const dataHoje = new Date().toISOString().split('T')[0];
        const nomeArquivo = `extratos_DOP_completa_${dataHoje}.xlsx`;

        // Faz o download
        XLSX.writeFile(wb, nomeArquivo);

    } catch (error) {
        console.error('Erro detalhado ao exportar para Excel:', error);
        alert(`Erro ao exportar: ${error.message || 'Verifique o console para mais detalhes'}`);
    }
};


watch(() => [filtro.value.dataInicioInput, filtro.value.dataFimInput], ([newInicio, newFim]) => {
    if (newInicio && newFim && newInicio > newFim) {
        alert('A data de início não pode ser maior que a data de fim');
        filtro.value.dataInicioInput = '';
        filtro.value.dataFimInput = '';
    }
});

// Watcher para sincronizar quando as props forem atualizadas
watch(() => props.filters, (newFilters) => {
    filtro.value = {
        search: newFilters?.search || '',
        lnr: newFilters?.lnr || '',
        estado: newFilters?.estado || 28,
        agencia: newFilters?.agencia || 'T',
        dataInicioInput: newFilters?.data_inicio || '',
        dataFimInput: newFilters?.data_fim || ''
    }
}, { immediate: true, deep: true })

watch(() => props.page, (newPage) => {
    paginaAtual.value = newPage
})


// Validação das datas
const validateDates = () => {
    if (dataInicioInput.value && dataFimInput.value) {
        if (new Date(dataInicioInput.value) > new Date(dataFimInput.value)) {
            dateError.value = 'A data de início não pode ser maior que a data de fim'
            return false
        }
    }
    dateError.value = ''
    return true
}

// Watchers para validação
watch([dataInicioInput, dataFimInput], () => {
    validateDates()
})

watch(() => props.dataInicioInput, (newVal) => {
    dataInicioInput.value = newVal || ''
})

watch(() => props.dataFimInput, (newVal) => {
    dataFimInput.value = newVal || ''
})


// Adicione esta variável para controlar a visibilidade dos filtros
const filtrosVisiveis = ref(true)

// Função para alternar a visibilidade dos filtros
const toggleFiltros = () => {
    filtrosVisiveis.value = !filtrosVisiveis.value
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

.btn {
    @apply px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center justify-center;
}

.btn-primary {
    @apply bg-green-600 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2;
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

.btn-outline-warning {
    @apply border border-yellow-500 text-yellow-500 hover:bg-yellow-50;
}

.btn-outline-success {
    @apply border border-green-500 text-green-500 hover:bg-green-50;
}

.btn-outline-primary {
    @apply border border-blue-500 text-blue-500 hover:bg-blue-50;
}

.btn-outline-info {
    @apply border border-cyan-500 text-cyan-500 hover:bg-cyan-50;
}

.btn-outline-danger {
    @apply border border-red-500 text-red-500 hover:bg-red-50;
}

.btn-sm {
    @apply px-3 py-1 text-sm;
}

/* Componentes de Formulário */
.select-input {
    @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white;
}

/* Cards e Efeitos */
.card-hover {
    @apply transition-all duration-200 hover:shadow-md hover:-translate-y-0.5;
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

/* Tabela responsiva */
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
</style>
