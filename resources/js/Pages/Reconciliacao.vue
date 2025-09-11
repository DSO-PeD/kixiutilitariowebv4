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
                    <i class="fas fa-exchange-alt text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Reconciliação de Comprovativos
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Validação de pagamentos registrados</p>
                </div>
            </div>
            <!--div class="flex-shrink-0">
                <button class="btn btn-primary flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Nova Operação
                </button>
            </div-->
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Loan Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número do Empréstimo</label>
                        <div class="relative">
                            <button class="btn btn-primary-filter flex items-center justify-center"
                                @click="showModalLoan = true">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                &ThinSpace;Loan Number
                            </button>
                        </div>
                    </div>

                    <!-- Período -->
                    <div>
                        <div class="grid grid-cols-2 gap-2">
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Inicio </label>
                                <input v-model="filtro.dataInicioInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm"
                                    :max="filtro.dataInicioInput" @change="validarDatas" />
                                <span v-if="erros.dataInicio" class="text-red-500 text-xs">{{ erros.dataInicio }}</span>
                            </div>

                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fim </label>
                                <input v-model="filtro.dataFimInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm"
                                    :min="filtro.dataFimInput" @change="validarDatas" />
                                <span v-if="erros.dataFim" class="text-red-500 text-xs">{{ erros.dataFim }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- As três selects agora estão em uma única div com grid de 3 colunas -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 col-span-1 md:col-span-2 lg:col-span-2">
                        <!-- Forma de Pagamento -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento</label>
                            <div class="relative">
                                <select v-model="filtro.formaPagamento"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white">
                                    <option v-for="formapgt in formaspagamentos" :value="formapgt.FormaPago"
                                        :key="formapgt.FormaPago">
                                        {{ formapgt.FormaPagoN }}
                                    </option>
                                    <option :value="'TP'">Todas formas</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Agência -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Agência</label>
                            <div class="relative">
                                <select v-model="filtro.agencia"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white">
                                    <option disabled :value="'s/a'">Escolha agência </option>
                                    <option v-for="agencia in $page.props.bases" :value="agencia.OfIdentificador"
                                        :key="agencia.OfIdentificador">
                                        {{ agencia.OfIdentificador }} - {{ agencia.OfNombre }}
                                    </option>
                                    <option :value="'T'">Todas que tenho acesso</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado que Pretende
                                Consultar</label>
                            <div class="relative">
                                <select v-model="filtro.estado"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white">
                                    <option disabled :value="'s/e'">Escolha</option>
                                    <option v-for="estado in $page.props.estados" :value="Number(estado.id)"
                                        :key="estado.id">
                                        {{ estado.descricao_estado }}
                                    </option>
                                    <option :value="28">Todos</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros inferiores
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" v-model="filtro.filtrarPrestacoes" class="sr-only">
                                    <div class="block bg-gray-300 w-10 h-6 rounded-full transition-colors"
                                        :class="{ 'bg-green-500': filtro.filtrarPrestacoes }"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform"
                                        :class="{ 'transform translate-x-4': filtro.filtrarPrestacoes }"></div>
                                </div>
                                <div class="ml-3 text-sm font-medium text-gray-700">Prestações (Capital+Juro)</div>
                            </label>
                        </div>

                        <div class="flex items-center bg-gray-50 p-3 rounded-lg border border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" v-model="filtro.filtrarPoupancas" class="sr-only">
                                    <div class="block bg-gray-300 w-10 h-6 rounded-full transition-colors"
                                        :class="{ 'bg-green-500': filtro.filtrarPoupancas }"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform"
                                        :class="{ 'transform translate-x-4': filtro.filtrarPoupancas }"></div>
                                </div>
                                <div class="ml-3 text-sm font-medium text-gray-700">Poupanças</div>
                            </label>
                        </div>
                    </div>


                    <div v-if="filtro.filtrarPrestacoes || filtro.filtrarPoupancas" class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            {{ filtro.filtrarPrestacoes && !filtro.filtrarPoupancas ? 'Produto de Prestações' :
                                !filtro.filtrarPrestacoes && filtro.filtrarPoupancas ? 'Produto de Poupanças' : 'Produto' }}
                        </label>
                        <div class="relative">
                            <select v-if="filtro.filtrarPrestacoes && !filtro.filtrarPoupancas"
                                v-model="filtro.produtoPrestacao"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white">
                                <option disabled :value="'s/pl'">Selecione o produto</option>
                                <option
                                    v-for="produto in produtos.filter(p => p.TipoProduto === 'L' || p.TipoProduto === 'G')"
                                    :key="produto.Metodologia" :value="produto.Metodologia">
                                    {{ produto.PoAgrupado }}
                                </option>
                                <option value="TL">Todos os produtos de Prestações</option>
                            </select>
                            <select v-else-if="filtro.filtrarPoupancas && !filtro.filtrarPrestacoes"
                                v-model="filtro.produtoPoupanca"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white">
                                <option disabled :value="'s/ts'">Selecione o produto</option>
                                <option
                                    v-for="produto in produtos.filter(p => p.TipoProduto === 'S' || p.TipoProduto === 'G')"
                                    :key="produto.Metodologia" :value="produto.Metodologia">
                                    {{ produto.PoAgrupado }}
                                </option>
                                <option value="TS">Todos os produtos de Poupanças</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>-->

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

        <!-- Resumo do Período -->
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
                        <h3 class="text-lg font-semibold text-gray-800">Prestações e Poupanças do Periodo</h3>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium text-green-700">{{ dataFimPeriodo }}</span>
                            <span class="mx-2">até</span>
                            <span class="font-medium text-green-700">{{ dataInicioPeriodo }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Cards de métricas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Card 1 - Total Montante Reembolsos -->
                <div
                    class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200 shadow-sm">
                    <div class="flex items-center mb-3">
                        <div class="bg-green-100 p-2 rounded-lg mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 text-green-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-gray-600">Total de Reembolsos</h4>
                    </div>
                    <p class="text-2xl font-bold text-green-700">{{ formatCurrency(montantetotal) }} AKZ</p>
                    <p class="text-xs text-gray-500 mt-1">Principal + Juros</p>
                </div>

                <!-- Card 2 - Total de Poupanças -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5 border border-blue-200 shadow-sm">
                    <div class="flex items-center mb-3">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-gray-600">Total de Poupanças</h4>
                    </div>
                    <p class="text-2xl font-bold text-blue-700">{{ formatCurrency(totalMontantePoupanca) }} AKZ</p>
                </div>

                <!-- Card 3 - Pagamentos por Referência -->
                <div
                    class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5 border border-purple-200 shadow-sm">
                    <div class="flex items-center mb-3">
                        <div class="bg-purple-100 p-2 rounded-lg mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 text-purple-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                            </svg>
                        </div>
                        <h4 class="text-sm font-medium text-gray-600">Pagamentos por Referência</h4>
                    </div>
                    <p class="text-2xl font-bold text-purple-700">{{ formatCurrency(totalMontantePGREF) }} AKZ</p>
                </div>
            </div>
        </div>

        <!-- Tabela de Comprovativos -->
        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
            <!-- Cabeçalho da tabela com paginação e exportação -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                    de {{ totalItens }} registros
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button class="btn btn-outline-excel flex items-center gap-2" @click="exportarParaExcel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar para Excel
                    </button>

                    <div class="flex gap-2">
                        <button :disabled="paginaAtual === 1" @click="mudarPagina(paginaAtual - 1)"
                            class="btn btn-outline px-3"
                            :class="{ 'opacity-50 cursor-not-allowed': paginaAtual === 1 }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <div class="flex items-center bg-gray-100 rounded-lg px-3">
                            <span class="text-sm font-medium">Página {{ paginaAtual }}</span>
                        </div>
                        <button :disabled="!hasMorePages" @click="mudarPagina(paginaAtual + 1)"
                            class="btn btn-outline px-3" :class="{ 'opacity-50 cursor-not-allowed': !hasMorePages }">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Alertas condicionais -->
            <div class="mb-4 space-y-3">
                <div v-if="comprovativosPaginados.some(c => c.montante > 7000000)"
                    class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mt-0.5 mr-3 flex-shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-red-800">Atenção! Existem reembolsos que excedem
                                7.000.000,00 AKZ</p>
                            <button @click="aplicarFiltrosmexc7M"
                                class="text-red-700 hover:text-red-900 text-xs font-medium mt-1 inline-flex items-center">
                                Listar todos
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="comprovativosPaginados.some(c => c.montante >= 500000 && c.montante <= 7000000)"
                    class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                    <div class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 text-yellow-500 mt-0.5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Atenção! Existem reembolsos entre 500.000,00
                                e 7.000.000,00 AKZ</p>
                            <button @click="aplicarFiltrosmai5M"
                                class="text-yellow-700 hover:text-yellow-900 text-xs font-medium mt-1 inline-flex items-center">
                                Listar todos
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#
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
                                Agência</th>
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
                                            d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    Lnr
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                    </svg>

                                    Voucher Transação
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">


                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>


                                    Pagamento
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
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(comprovativo, index) in comprovativosPaginados" :key="comprovativo.id"
                            class="hover:bg-gray-50 transition-colors duration-150" :class="{
                                'bg-red-50': comprovativo.montante > 7000000,
                                'bg-yellow-50': comprovativo.montante >= 500000 && comprovativo.montante <= 7000000,
                                'bg-green-50': comprovativo.estado_id === 8,
                            }">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ calcularNumeroLinha(index)
                            }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <a v-if="comprovativo.file" :href="`/storage/comprovativos/${comprovativo.file}`"
                                    target="_blank"
                                    class="text-blue-600 hover:text-blue-800 transition-colors flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2 15.5v-11a2 2 0 012-2h16a2 2 0 012 2v11a2 2 0 01-2 2H4a2 2 0 01-2-2z" />
                                    </svg>
                                    Visualizar
                                </a>
                                <span v-else class="text-gray-400 text-sm">N/A</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ comprovativo.data }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ comprovativo.agencia || '-'
                            }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ comprovativo.usuario }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ comprovativo.cliente || '-'
                            }}</td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button @click="abrirModalReconciliacaoDetalhe(comprovativo.id)"
                                    class="btn btn-action btn-detail text-xs">
                                    {{ comprovativo.lnr }}
                                </button>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ comprovativo.metodologia }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ formatCurrency(comprovativo.montante) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-center">
                                    <span>{{ comprovativo.voucher || '' }}</span>
                                    <button v-if="isPagamentoReferenciaSemVoucher(comprovativo)"
                                        @click="abrirModalEdicaoVoucher(comprovativo)"
                                        class="ml-2 text-orange-600 hover:text-orange-800 transition-colors"
                                        title="Adicionar voucher">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{
                                comprovativo.vouchertransacao || '-' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ comprovativo.FormaPagoN }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span :class="getEstadoBadgeClass(comprovativo)"
                                    class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ comprovativo.estado }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <button @click="abrirModalReconciliacao(comprovativo)"
                                    class="btn btn-action btn-validate text-xs"
                                    :disabled="deveDesativarBotao(comprovativo.estado)"
                                    :class="{ 'opacity-50 cursor-not-allowed': deveDesativarBotao(comprovativo.estado) }">
                                    Alterar Estado
                                </button>
                            </td>
                        </tr>
                        <tr v-if="comprovativosPaginados.length === 0">
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

            <!-- Paginação inferior -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                    de {{ totalItens }} registros
                </div>
                <div class="flex gap-2">
                    <button :disabled="paginaAtual === 1" @click="mudarPagina(paginaAtual - 1)"
                        class="btn btn-outline px-3" :class="{ 'opacity-50 cursor-not-allowed': paginaAtual === 1 }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <div class="flex items-center bg-gray-100 rounded-lg px-3">
                        <span class="text-sm font-medium">Página {{ paginaAtual }}</span>
                    </div>
                    <button :disabled="!hasMorePages" @click="mudarPagina(paginaAtual + 1)" class="btn btn-outline px-3"
                        :class="{ 'opacity-50 cursor-not-allowed': !hasMorePages }">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <ModalLoan :isOpen="showModalLoan" @close="showModalLoan = false" @search="buscarPorLoan" v-model="filtroLoan" />
    <ModalDate :isOpen="showModalData" :dataInicio="dataInicio" :dataFim="dataFim" :estadoModal="estadoModal"
        :agenciaModal="agenciaModal" @close="showModalData = false" @update:dataInicio="val => dataInicio = val"
        @update:dataFim="val => dataFim = val" @update:estadoModal="val => estadoModal = val"
        @update:agenciaModal="val => agenciaModal = val" @search="buscarPorDatas" />

    <ModalDelete v-if="showModalEliminar" @close="fecharModalEliminacao" @confirm="confirmarEliminacao"
        v-model:motivo="formEliminacao.txtMotivo" :dados="formEliminacao.txtDadosEliminado"
        :loan="formEliminacao.txtLoan" :id="formEliminacao.txtId" />

    <ModalReconcialiacao :show="showModalReconcialiacao" @close="showModalReconcialiacao = false"
        :comprovativoreconci="comprovativoSelecionado" @success="handleReconciliationSuccess" />

    <ModalComprovativoDetalhe :isOpen="showModalDetalhe" @close="showModalDetalhe = false"
        :comprovativo="comprovativoDetalhe" @openReconciliation="abrirModalReconciliacao(comprovativoDetalhe)" />
    <!-- Adicione este modal após os outros modais no template -->
    <ModalEdicaoVoucher :show="showModalVoucherEdicao" @close="fecharModalEdicaoVoucher" @save="salvarEdicaoVoucher"
        :comprovativo="comprovativoSelecionadoVoucher" :novoVoucher="novoVoucher" />
</template>

<script setup>
// O script permanece praticamente o mesmo, apenas adicionando a função getEstadoBadgeClass
// e a variável filtrosVisiveis para controlar a exibição dos filtros

import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import * as XLSX from 'xlsx'
import { Head } from '@inertiajs/vue3'

// Componentes
import ModalLoan from './Layouts/components/ComprovativosComponents/ModalLoan.vue'
import ModalDate from './Layouts/components/ComprovativosComponents/ModalDate.vue'
import ModalDelete from './Layouts/components/ComprovativosComponents/ModalDelete.vue'
import ModalReconcialiacao from './Layouts/components/ComprovativosComponents/ModalReconcialiacao.vue'
import ModalComprovativoDetalhe from './Layouts/components/ComprovativosComponents/ModalComprovativoDetalhe.vue'
import ModalEdicaoVoucher from './Layouts/components/ComprovativosComponents/ModalEdicaoVoucher.vue'





const props = defineProps({
    comprovativos: Array,
    filters: Object,
    page: Number,
    hasMorePages: Boolean,
    lista_comprovativo: {
        type: Array,
        required: true
    },
    perPage: {
        type: Number,
        default: 100
    },
    total: Number,
    montantetotal: Number,
    totalMontantePoupanca: Number,
    totalMontantePGREF: Number,
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
    montanteFiltrado: Number,
    dataInicioPeriodo: String,
    dataFimPeriodo: String,
    totalMontantePoupancaRegistado: Number,
    totalMontanteRegistado: Number,
    totalMontanteReflete: Number,
    totalMontantePoupancaReflete: Number,
    totalMontanteInregulares: Number,
    totalMontantePoupancaInregulares: Number,
    formaspagamentos: Array
})

// Estados
const showModalLoan = ref(false)
const showModalData = ref(false)
const showModalEliminar = ref(false)
const showModalReconcialiacao = ref(false)
const comprovativoSelecionado = ref(null)

const showModalVoucherEdicao = ref(false)
const novoVoucher = ref('')
const comprovativoSelecionadoVoucher = ref(null)

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
        await router.post('/alterarvoucherrec', {
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

// Configuração da paginação
const perPage = ref(100);
const paginaAtual = ref(1);

// Dados locais para paginação
const dadosLocais = ref([]);

// Watch para atualizar dadosLocais quando lista_comprovativo mudar
watch(() => props.lista_comprovativo, (newVal) => {
    dadosLocais.value = newVal;
    paginaAtual.value = 1; // Resetar para primeira página
}, { immediate: true });


// Computed property para os dados paginados
const comprovativosPaginados = computed(() => {
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

// Filtros
const filtro = ref({

    search: props.filters.search || '',
    lnr: props.filters.lnr || '',
    estado: props.filters.estado || 28,
    agencia: props.filters.agencia || 'T',
    dataInicioInput: props.filters.data_inicio || '',
    dataFimInput: props.filters.data_fim || '',


    filtrarPrestacoes: props.filters.filtrar_prestacoes !== undefined
        ? Boolean(Number(props.filters.filtrar_prestacoes))
        : true,
    filtrarPoupancas: props.filters.filtrar_poupancas !== undefined
        ? Boolean(Number(props.filters.filtrar_poupancas))
        : true,

    produtoPrestacao: props.filters.produtoPrestacao || 'TL',     // For loan products combobox
    produtoPoupanca: props.filters.produtoPoupanca || 'TS',       // For savings products combobox
    formaPagamento: props.filters.formaPagamento || 'TP'


})

const filtroLoan = ref('')
const dataInicio = ref('')
const dataFim = ref('')
const estadoModal = ref(0)
const agenciaModal = ref('')



const comprovativosFiltrados = computed(() => {
    return props.comprovativos // Agora usamos diretamente os comprovativos recebidos do backend
})
const montanteTotalFiltrado = computed(() => {
    return props.montanteFiltrado || 0 // Usamos o valor calculado no backend
})


// Métodos


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

const calcularNumeroLinha = (index) => {
    return (paginaAtual.value - 1) * props.perPage + index + 1
}


// Função aplicarFiltros modificada
const aplicarFiltros = () => {
    if (!validarDatas()) return;

    router.get('/reconciliacao', {
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
        onSuccess: () => {
            paginaAtual.value = 1; // Resetar paginação
        }
    });
};

const aplicarFiltrosmai5M = () => {

    router.get('/reconciliacao', {
        tipo: 500000
    }, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            paginaAtual.value = 1; // Resetar paginação
        }
    });
};
const aplicarFiltrosmexc7M = () => {

    router.get('/reconciliacao', {
        tipo: 7000000
    }, {
        preserveState: true,
        replace: true,
        onSuccess: () => {
            paginaAtual.value = 1; // Resetar paginação
        }
    });
}

// Função resetarFiltros
const resetarFiltros = () => {
    filtro.value = {
        search: '',
        lnr: '',
        estado: 28,
        agencia: 'T',
        dataInicioInput: '',
        dataFimInput: '',
        formaPagamento: 'TP',
        produtoPrestacao: 'TL',
        produtoPoupanca: 'TS',
    };

    router.get('/reconciliacao', {
        page: 1
    }, {
        preserveState: true,
        replace: true
    });
};

const buscarPorLoan = () => {
    router.get('/reconciliacao', { tipo: 3, loan: filtroLoan.value }, { preserveState: true })
    showModalLoan.value = false
}


watch(() => [filtro.value.dataInicioInput, filtro.value.dataFimInput], ([newInicio, newFim]) => {
    if (newInicio && newFim && newInicio > newFim) {
        alert('A data de início não pode ser maior que a data de fim');
        filtro.value.dataInicioInput = '';
        filtro.value.dataFimInput = '';
    }
});

const buscarPorDatas = (params) => {
    router.get('/reconciliacao', {
        tipo: 1,
        data_inicio: params.data_inicio,
        data_fim: params.data_fim,
        estadoconsulta: params.estadoconsulta,
        agenciaconsulta: params.agenciaconsulta
    }, { preserveState: true })

    showModalData.value = false
}
const exportarParaExcel = () => {
    try {
        // Acessando a lista_comprovativo corretamente (dependendo do seu contexto)
        let listaCompleta;

        // Opção 1: Se estiver usando Inertia.js em Composition API
        if (typeof usePage !== 'undefined') {
            const { props } = usePage();
            listaCompleta = props.value.lista_comprovativo;
        }
        // Opção 2: Se estiver usando Options API
        else if (this && this.$page && this.$page.props) {
            listaCompleta = this.$page.props.lista_comprovativo;
        }
        // Opção 3: Se a lista estiver disponível como prop no componente
        else if (props && props.lista_comprovativo) {
            listaCompleta = props.lista_comprovativo;
        }
        // Opção 4: Se estiver disponível diretamente no escopo
        else if (typeof lista_comprovativo !== 'undefined') {
            listaCompleta = lista_comprovativo;
        }
        else {
            throw new Error('Não foi possível encontrar a lista de comprovativos');
        }

        // Verifica se há dados
        if (!listaCompleta || listaCompleta.length === 0) {
            alert('Nenhum dado disponível para exportar');
            return;
        }

        console.log('Total de registros a exportar:', listaCompleta.length);

        // Formata os dados
        const dadosFormatados = listaCompleta.map((comprovativo, index) => {
            try {
                return {
                    '#': index + 1,
                    'Data': comprovativo.CiFecha ? new Date(comprovativo.CiFecha).toLocaleString('pt-PT') : '-',
                    'Agência': comprovativo.agencia || '-',
                    'Registado Por': comprovativo.usuario || '-',
                    //'Base': comprovativo.basedelacamento || '-',
                    'LNR': comprovativo.lnr || '-',
                    'Cliente': comprovativo.cliente || '-',
                    'Produto': comprovativo.metodologia || '-',
                    'Voucher Dia': comprovativo.voucher || '-',
                    'Voucher Transacao': comprovativo.vouchertransacao || '-',
                    'Forma de Pagamento': comprovativo.FormaPagoN || '-',
                    'Descrição': comprovativo.descricao || '-',
                    'Banco': comprovativo.banco || '-',
                    'Conta Bancaria': comprovativo.conta || '-',
                    'Observação': comprovativo.observacao || '-',
                    'Montante': comprovativo.montante || '0,00',
                    'Estado': comprovativo.estado || '-',
                    'Operador DCF': comprovativo.operadordcf || '-',
                    'Data de Operação DCF': comprovativo.datareconciliacao || '-',
                    // 'Arquivo': comprovativo.file ? 'Sim' : 'Não'
                };
            } catch (error) {
                console.error('Erro ao formatar registro:', comprovativo, error);
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
        XLSX.utils.book_append_sheet(wb, ws, "Comprovativos");

        // Gera o nome do arquivo
        const dataHoje = new Date().toISOString().split('T')[0];
        const nomeArquivo = `comprovativos_reconciliacao_completa_${dataHoje}.xlsx`;

        // Faz o download
        XLSX.writeFile(wb, nomeArquivo);

    } catch (error) {
        console.error('Erro detalhado ao exportar para Excel:', error);
        alert(`Erro ao exportar: ${error.message || 'Verifique o console para mais detalhes'}`);
    }
};


const showModalDetalhe = ref(false)
const comprovativoDetalhe = ref(null)
const operacoesReconciliacao = ref([]);
const loading = ref(false);
const error = ref(null);
const dataInicioInput = ref(props.dataInicioInput || '')
const dataFimInput = ref(props.dataFimInput || '')
const dateError = ref('')
const erros = ref({
    dataInicio: '',
    dataFim: ''
})

const carregarOperacoes = async (idComprovativo) => {
    loading.value = true;
    error.value = null;

    try {
        const response = await axios.get('/listarCpvtDetalheDCF', {
            params: { idComprovativo },
            timeout: 10000
        });

        if (!response.data) {
            throw new Error('Resposta vazia da API');
        }

        return Array.isArray(response.data) ? response.data : [];

    } catch (err) {
        error.value = err.response?.data?.message ||
            err.message ||
            'Erro ao carregar operações';

        console.error('Erro detalhado:', {
            error: err,
            request: err.config,
            response: err.response
        });

        return [];
    } finally {
        loading.value = false;
    }
};


const abrirModalReconciliacao = (comprovativo) => {
    comprovativoSelecionado.value = comprovativo
    showModalReconcialiacao.value = true
}

const abrirModalReconciliacaoDetalhe = async (idComprovativo) => {
    try {
        // 1. Encontra o comprovativo
        comprovativoDetalhe.value = props.lista_comprovativo.find(c => c.id === idComprovativo);

        if (!comprovativoDetalhe.value) {
            alert('Comprovativo não encontrado');
            return;
        }

        // 2. Carrega as operações
        const operacoes = await carregarOperacoes(idComprovativo);

        // 3. Adiciona as operações ao comprovativo
        comprovativoDetalhe.value = {
            ...comprovativoDetalhe.value,
            operacoesReconciliacao: operacoes
        };

        // 4. Abre o modal
        showModalDetalhe.value = true;
    } catch (err) {
        console.error('Erro ao abrir modal de detalhes:', err);
        // Exibe mensagem de erro para o usuário
        alert('Ocorreu um erro ao carregar os detalhes. Por favor, tente novamente.');
    } finally {
        loading.value = false;
    }
};

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

const handleReconciliationSuccess = () => {
    showModalReconcialiacao.value = false
    router.reload({ only: ['comprovativos'] })
}

const deveDesativarBotao = (estado) => {
    return ['Validado', 'Reflete'].includes(estado);
}
// Watcher para sincronizar quando as props forem atualizadas
watch(() => props.filters, (newFilters) => {
    filtro.value = {
        search: newFilters.search || '',
        lnr: newFilters.lnr || '',
        estado: newFilters.estado || 28,
        agencia: newFilters.agencia || 'T',
        dataInicioInput: newFilters.data_inicio || '',
        dataFimInput: newFilters.data_fim || '',
        formaPagamento: newFilters.formaPagamento || 'TP',
        produtoPrestacao: newFilters.produtoPrestacao || 'TL',
        produtoPoupanca: newFilters.produtoPoupanca || 'TS',
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

// Função para obter a classe do badge de estado
const getEstadoBadgeClass = (comprovativo) => {
    const estado = comprovativo.estado.toLowerCase()
    if (estado.includes('validado') || estado.includes('aprovado')) {
        return 'bg-green-100 text-green-800'
    } else if (estado.includes('pendente') || estado.includes('aguardando')) {
        return 'bg-yellow-100 text-yellow-800'
    } else if (estado.includes('rejeitado') || estado.includes('erro')) {
        return 'bg-red-100 text-red-800'
    } else if (estado.includes('processando')) {
        return 'bg-blue-100 text-blue-800'
    }
    return 'bg-gray-100 text-gray-800'
}

// O restante do script permanece igual...
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
