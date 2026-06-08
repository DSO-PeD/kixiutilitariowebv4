<template>

    <Head title="Gestão de Comprovativos" />

    <div class="container mx-auto py-4 md:py-6 max-w-full">
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

        <!-- Cabeçalho Principal -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-file-invoice-dollar text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Gestão de Comprovativos</h1>
                    <p class="text-sm text-gray-600 mt-1">Prestações de Créditos e Poupanças</p>
                </div>
            </div>


            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <button class="btn btn-primary flex items-center gap-2" @click="abrirModalNovoComprovativo"
                    v-if="$page.props.user.rec_comprovativo">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.5v15m7.5-7.5h-15"></path>
                    </svg>
                    Novo Comprovativo
                </button>

            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>             

        <!-- Seção de Alertas Pendentes -->
        <div v-if="$page.props.user.view_pendentes" class="mb-6">
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex flex-col md:flex-row md:items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-start mb-2">
                            <svg class="flex-shrink-0 h-5 w-5 text-red-500 mt-0.5 mr-2" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-semibold text-red-800">Atenção - Comprovativos Pendentes</h3>
                                <p class="text-sm text-red-700 mt-1">
                                    Identificamos reembolsos aplicados no Kixi Utilitário que não foram aplicados no LPF
                                    ou possuem diferenças nas informações.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 mt-3">
                            <span v-for="_pendente in pendentesVisiveis" :key="_pendente.Lnr" class="relative">
                                <span @click="toggleDetails(_pendente.id)" class="badge badge-warning cursor-pointer">
                                    {{ _pendente.Lnr }}
                                </span>

                                <div v-if="activeDetails === _pendente.id" class="tooltip">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <span class="text-gray-500">Voucher:</span>
                                        <span class="font-medium">{{ _pendente.voucher }}</span>
                                        <span class="text-gray-500">Montante:</span>
                                        <span class="font-medium">{{ _pendente.montante }}</span>
                                        <span class="text-gray-500">Data:</span>
                                        <span class="font-medium">{{ _pendente.budata }}</span>
                                    </div>
                                    <button @click.stop="closeDetails"
                                        class="text-xs text-red-600 hover:text-red-800 mt-2">
                                        Fechar
                                    </button>
                                </div>
                            </span>

                            <button @click="mostrarTodos = !mostrarTodos"
                                class="text-blue-600 hover:text-blue-800 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="!mostrarTodos" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z">
                                    </path>
                                    <path v-if="!mostrarTodos" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88">
                                    </path>
                                </svg>
                                {{ mostrarTodos ? 'Ver menos' : 'Ver mais...' }}
                            </button>
                        </div>
                    </div>

                    <button @click="exportToExcel" class="btn btn-outline-secondary flex items-center gap-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Exportar Pendentes
                    </button>
                </div>
            </div>
        </div>

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
                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm card-hover">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-3 rounded-full mr-3">


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 text-green-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>

                        </div>
                        <h3 class="text-sm font-semibold text-gray-700">Reembolsos (Capital + Juro)</h3>
                    </div>
                    <p class="text-2xl font-bold text-green-700 mb-4">{{ formatCurrency(montantetotal) }} AKZ</p>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Novos:</span>
                            <span class="font-semibold text-blue-600">{{ formatCurrency(totalMontanteRegistado) }}
                                AKZ</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Validados:</span>
                            <span class="font-semibold text-green-600">{{ formatCurrency(totalMontanteReflete) }}
                                AKZ</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Irregulares:</span>
                            <span class="font-semibold text-red-600">{{ formatCurrency(totalMontanteInregulares) }}
                                AKZ</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2 - Total de Poupanças -->
                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm card-hover">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-3 rounded-full mr-3">


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>


                        </div>
                        <h3 class="text-sm font-semibold text-gray-700">Poupanças</h3>
                    </div>
                    <p class="text-2xl font-bold text-blue-700 mb-4">{{ formatCurrency(totalMontantePoupanca) }} AKZ</p>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Novos:</span>
                            <span class="font-semibold text-blue-600">{{ formatCurrency(totalMontantePoupancaRegistado)
                                }}
                                AKZ</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Validados:</span>
                            <span class="font-semibold text-green-600">{{ formatCurrency(totalMontantePoupancaReflete)
                                }}
                                AKZ</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Irregulares:</span>
                            <span class="font-semibold text-red-600">{{ formatCurrency(totalMontantePoupancaInregulares)
                                }}
                                AKZ</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3 - Pagamentos por Referência -->
                <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm card-hover cursor-pointer"
                    @click="abrirModalPagamentosReferencia">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 p-3 rounded-full mr-3">


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6 text-purple-600">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                            </svg>

                        </div>
                        <h3 class="text-sm font-semibold text-gray-700">Pagamentos por Referência</h3>
                    </div>
                    <p class="text-2xl font-bold text-purple-700 mb-4">{{ formatCurrency(totalMontantePGREF) }} AKZ</p>

                    <div class="flex items-center justify-between text-sm text-purple-600 font-medium">
                        <span>Ver detalhes</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>
                </div>


            </div>
        </div>






        <!-- Resumo do Período -->




        <!-- Tabela de Comprovativos -->
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

            <!-- Alertas de Valores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div v-if="comprovativosPaginados.some(c => c.montante > 7000000)" class="alert alert-warning">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                        <span>Atenção! Foram identificados reembolsos com montantes superiores a 7.000.000,00
                            AKZ.</span>
                    </div>
                    <button @click="aplicarFiltrosmexc7M" class="btn btn-sm btn-outline mt-2">
                        Listar todos
                    </button>
                </div>

                <div v-if="comprovativosPaginados.some(c => c.montante >= 500000 && c.montante <= 7000000)"
                    class="alert alert-warning">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Atenção! Foram identificados reembolsos com montantes entre 500.000,00 e 7.000.000,00
                            AKZ.</span>
                    </div>
                    <button @click="aplicarFiltrosmai5M" class="btn btn-sm btn-outline mt-2">
                        Listar todos
                    </button>
                </div>
            </div>

            <!-- Tabela -->
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full">
                    <thead class="bg-gray-50 ">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider flex">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    Arquivo
                                </div>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>

                                    Por
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

                                    Loan Number
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

                                    Cliente
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>


                                    Produto
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
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
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 9.75h4.875a2.625 2.625 0 0 1 0 5.25H12M8.25 9.75 10.5 7.5M8.25 9.75 10.5 12m9-7.243V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
                                    </svg>

                                    Voucher Dia
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z" />
                                    </svg>

                                    Voucher Transação
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">


                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>


                                    Pagamento
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                    </svg>

                                    OBS DCF
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                    </svg>

                                    Estado
                                </div>
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
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
                            class="hover:bg-gray-50 transition-colors duration-150" :class="{
                                'bg-purple-50': comprovativo.montante > 7000000,
                                'bg-yellow-50': comprovativo.montante >= 500000 && comprovativo.montante <= 7000000,
                                'bg-red-50': [13, 19, 9].includes(comprovativo.idestado),
                                'bg-green-50': comprovativo.estado_id === 8,
                                'bg-blue-50': comprovativo.estado_id === 19
                            }">
                            <!-- Conteúdo das células (mantido do original) -->
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ calcularNumeroLinha(index) }} 
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <a v-if="comprovativo.usuario != 'SUPLITEL'"
                                    :href="`/storage/comprovativos/${comprovativo.file}`" target="_blank"
                                    class="btn btn-outline-primary btn-sm flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                        </path>
                                    </svg>
                                    cpvtv.
                                </a>



                                <a v-else-if="comprovativo.usuario == 'SUPLITEL'"
                                    :href="`/reports/comprovativo/${comprovativo.id}`"
                                    class="btn btn-outline-primary btn-sm flex items-center gap-1" target="_blank">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                        </path>
                                    </svg>
                                    cpvtv
                                </a>

                                <span v-else class="text-gray-400 text-sm">N/A</span>
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
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <p class="text-sm">Nenhum comprovativo encontrado</p>
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

    <!-- Modais (mantidos do original) -->
    <ModalLoan :isOpen="showModalLoan" @close="showModalLoan = false" @search="buscarPorLoan" v-model="filtroLoan" />
    <ModalDate :isOpen="showModalData" @close="showModalData = false" @search="buscarPorDatas"
        v-model:dataInicio="dataInicio" v-model:dataFim="dataFim" />
    <ModalDelete :isOpen="showDeleteModal" @close="cancelDeletion" @confirm="proceedWithDeletion"
        v-model:motivo="formEliminacao.txtMotivo" :dados="formEliminacao.txtDadosEliminado"
        :loan="formEliminacao.txtLoan" :id="formEliminacao.txtId" />

    <ModalNovoComprovativo ref="modalNovoComprovativoRef" v-if="showModalNovo" @close="fecharModalNovoComprovativo"
        @save="guardarComprovativo" :bases="$page.props.bases" :tipocomprovativos="$page.props.tipocomprovativos"
        :produtos="$page.props.produtos" :bancos="$page.props.bancos" :contas="$page.props.contas"
        :formaspagamentos="$page.props.formaspagamentos" v-model="novoComprovativo" />

    <ModalGerarRefPGT ref="modalCriarRefManual" v-if="showModalGerarREF" @close="fecharModalCriarRefManual"
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

    <!-- Modal para Pagamentos por Referência -->
    <transition name="fade-scale" appear>
        <div v-if="showModalPagamentosReferencia"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[80vh] overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Pagamentos por Referência - Período: {{ dataFimPeriodo }} à {{ dataInicioPeriodo }}
                    </h3>
                    <button @click="showModalPagamentosReferencia = false" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div class="mb-4 flex justify-between items-center">
                        <p class="text-sm text-gray-600">
                            Total: <b>{{ pagamentosReferencia.length }}</b> pagamentos -
                            <b class="text-green-700"> {{ formatCurrency(totalMontantePGREF) }} AKZ</b>
                        </p>
                        <button @click="exportarPagamentosReferenciaExcel"
                            class="btn btn-outline-excel flex items-center gap-2 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Exportar
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #</th>
                                    <th>
                                        Arquivo
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Data</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Loan Number</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Voucher</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Montante</th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(pagamento, index) in pagamentosReferencia" :key="pagamento.id"
                                    class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ index + 1 }}</td>
                                    <td>
                                        <a :href="`/reports/comprovativo/${pagamento.id}`"
                                            class="btn btn-outline-primary btn-sm flex items-center gap-1"
                                            target="_blank">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                                </path>
                                            </svg>
                                            cpvtv
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ pagamento.data }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{
                                        pagamento.lnr }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ pagamento.cliente
                                    }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ pagamento.voucher
                                        || '-' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                        {{ formatCurrency(pagamento.montante) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span :class="pagamento.color"
                                            class="px-2 py-1 text-xs font-medium rounded-full">
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

</template>

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
import ModalGerarRefPGT from './Layouts/components/ComprovativosComponents/ModalGerarRefPGT.vue'
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
    formaspagamentos: Array,
    refPagamento: String,
    periodo_trans_pgr: String
})

// Refs
const showModalLoan = ref(false)
const showModalData = ref(false)
const showModalNovo = ref(false)
const showModalGerarREF = ref(false)
const showModalEliminar = ref(false)
const showModalObservacao = ref(false)
const showDeleteModal = ref(false)
const showEditModal = ref(false)
const modalNovoComprovativoRef = ref(null)
const modalCriarRefManual = ref(null)
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

const formEliminacao = ref({
    txtMotivo: '',
    txtDadosEliminado: '',
    txtLoan: '',
    txtId: null
})

// Adicione esta variável para controlar a visibilidade dos filtros
const filtrosVisiveis = ref(true)

// Função para alternar a visibilidade dos filtros
const toggleFiltros = () => {
    filtrosVisiveis.value = !filtrosVisiveis.value
}

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

const novoReferenciaManual = ref({
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

    const dataItem = new Date(comprovativo.CiFecha).toISOString().split('T')[0] // só pega a data
    const isRegistadoHoje = dataItem === hoje.value
    const temPermissao = props.user.comprovativo_btnedita_montante == 1
    return isRegistadoHoje || temPermissao
}

const podeEliminar = (comprovativo) => {
    const dataItem = new Date(comprovativo.CiFecha).toISOString().split('T')[0] // só pega a data
    const isRegistadoHoje = comprovativo.estado_id === 1 && dataItem === hoje.value
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

    // Preencha os dados para o modal
    formEliminacao.value = {
        txtMotivo: '',
        txtDadosEliminado: `${comprovativo.cliente} - ${formatCurrency(comprovativo.montante)} AKZ`,
        txtLoan: comprovativo.lnr || 'N/A',
        txtId: comprovativo.id
    }

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

const proceedWithDeletion = async (id, motivo) => {
    isDeleting.value = true
    try {
        await router.post("/eliminar-comprovativo", {
            id: id,
            estado_id: selectedComprovativo.value.idestado,
            motivo: motivo
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showDeleteModal.value = false
                // Limpar formulário
                formEliminacao.value = {
                    txtMotivo: '',
                    txtDadosEliminado: '',
                    txtLoan: '',
                    txtId: null
                }
            }
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
            'Referência de Pagamento': comprovativo.refPagamento || '-',
            'Periodo': comprovativo.periodo_trans_pgr || '-',
            'Descrição da DCF': comprovativo.descricao || '-',
            'Banco': comprovativo.banco || '-',
            'Conta Bancaria': comprovativo.conta || '-',
            'Observação da DCF': comprovativo.observacao || '-',
            'Montante': comprovativo.montante || '0,00',
            'Estado': comprovativo.estado || '-',
            'Operador DCF': comprovativo.operadordcf || '-',
            'Data de Operação DCF': comprovativo.datareconciliacao || '-'

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
const abrirModalGerarRefManual = () => {
    showModalGerarREF.value = true
    novoReferenciaManual.value = {
        ls: 'Saving', // Já está como 'Saving', isso está correto
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
const fecharModalCriarRefManual = () => showModalGerarREF.value = false

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
