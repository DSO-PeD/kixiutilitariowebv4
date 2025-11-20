<template>

    <Head title="Gestão de Referências de Pagamentos" />

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
                    <i class="fas fa-user-tie  text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Gestão de Clientes</h1>
                    <p class="text-sm text-gray-600 mt-1">Registro e Listagem de Clientes</p>
                </div>
            </div>


            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">

                <button class="btn btn-outline-primary-pgr flex items-center gap-2" @click="abrirModalGerarRefManual">

                    <i class="fas fa-credit-card text-purple-600 text-xl"></i>
                    Gerar Referência de PGT.
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
                                &ThinSpace;Código do Cliente
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

        <!--div class="bg-gray-100 rounded-xl shadow-sm p-5 mb-6 border border-white">
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


            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


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
        </div-->






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
                                    Card
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

                                    Código do Cliente
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

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                    </svg>


                                    Produto de Pagamento
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

                                    Referência
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
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>

                                    Montante Pago
                                </div>
                            </th>
                            <!--th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                    </svg>


                                </div>
                            </th-->
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(comprovativo, index) in comprovativosPaginados" :key="comprovativo.id"
                            class="hover:bg-gray-50 transition-colors duration-150" :class="{
                                'bg-yellow-50': comprovativo.idestado === 23,
                                'bg-red-50': [23].includes(comprovativo.idestado),
                                'bg-purple-50': comprovativo.idestado === 21,
                                'bg-green-50': comprovativo.idestado === 22
                            }">
                            <!-- Conteúdo das células (mantido do original) -->
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ calcularNumeroLinha(index) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">




                                <a :href="`/reports/cardpgtr/${comprovativo.id}`"
                                    class="btn btn-outline-primary-pgr btn-sm flex items-center gap-1" target="_blank">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z">
                                        </path>
                                    </svg>
                                    Ver-Card
                                </a>


                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.data }}

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
                                {{ comprovativo.telefone }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.metodologia }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ formatCurrency(comprovativo.montante) }}

                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">

                                <span>{{ comprovativo.referencia || '-' }}</span>



                            </td>



                            <td class="px-4 py-4 whitespace-nowrap">
                                <span :class="comprovativo.color" class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ comprovativo.estado }}
                                </span>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-purple-600">
                                {{ formatCurrency(comprovativo.montantepago) }}

                            </td>
                            <!--td class="px-4 py-4 whitespace-nowrap">
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
                            </td-->
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



    <ModalGerarRefPGT ref="modalCriarRefManual" v-if="showModalGerarREF" @close="fecharModalCriarRefManual"
        @save="guardarComprovativo" :bases="$page.props.bases" :tipocomprovativos="$page.props.tipocomprovativos"
        :produtos="$page.props.produtos" :bancos="$page.props.bancos" :contas="$page.props.contas"
        :formaspagamentos="$page.props.formaspagamentos" v-model="novoComprovativo" />







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
import ModalGerarRefPGT from './Layouts/components/ComprovativosComponents/ModalGerarRefPGT.vue'



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

})

// Refs
const showModalLoan = ref(false)
const showModalData = ref(false)
const showModalNovo = ref(false)
const showModalGerarREF = ref(false)
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
    dataInicioInput: props.filters.data_inicio || '',
    dataFimInput: props.filters.data_fim || '',

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





const podeEliminar = (comprovativo) => {
    const dataItem = new Date(comprovativo.CiFecha).toISOString().split('T')[0] // só pega a data
    const isRegistadoHoje = comprovativo.estado_id === 1 && dataItem === hoje.value
    const temPermissao = props.user.elimina_confirmado_exportado == 1
    return isRegistadoHoje || temPermissao
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

    router.get('/referenciapgt', {
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

    router.get('/referenciapgt', { page: 1 }, {
        preserveState: true,
        replace: true
    })
}

const exportarParaExcel = () => {
    try {
        const dadosFormatados = props.lista_comprovativo.map((comprovativo, index) => ({
            '#': index + 1,
            'Data': comprovativo.data ? new Date(comprovativo.data).toLocaleString('pt-PT') : '-',
            'Agência': comprovativo.agencia || '-',
            'Registado Por': comprovativo.usuario || '-',
            'Código do Cliente': comprovativo.lnr || '-',
            'Cliente': comprovativo.cliente || '-',
            'Produto': comprovativo.metodologia || '-',
            'Montante': comprovativo.montante || '0,00',
            'Referência de Pagamento': comprovativo.referencia || '-',
            'Estado': comprovativo.estado || '-',

        }))

        const ws = XLSX.utils.json_to_sheet(dadosFormatados)
        const wb = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(wb, ws, "Comprovativos")
        XLSX.writeFile(wb, `lista_refpagamentos_completa_${new Date().toISOString().split('T')[0]}.xlsx`)
    } catch (error) {
        console.error('Erro ao exportar:', error)
        alert(`Erro ao exportar: ${error.message}`)
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


        txtMontante: '',

        txtInfoAdicional: '',

        telefone: ''
    }
}
// Função para resetar o formulário
const resetarFormularioReferencia = () => {
    novoReferenciaManual.value = {
        ls: 'Saving',
        selectBase: '',
        selectGrupoIndividual: '',
        txtNumeroLoanSaving: '',
        selectProdutoSaving: '',
        txtSavingD: 'Savings Deposit',
        txtMontante: '',
        txtInfoAdicional: '',
        telefone: '',
        txtRefPagamento: ''
    };
};
const fecharModalCriarRefManual = () => showModalGerarREF.value = false


const guardarComprovativo = async () => {
    try {
        const formData = new FormData()
        Object.entries(novoComprovativo.value).forEach(([key, value]) => {
            if (value) formData.append(key, value)
        })



        await router.post('/guardar-referencia-pagamento', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onSuccess: () => {
                fecharModalCriarRefManual();

                // Resetar formulário
                resetarFormularioReferencia();

            }
        })
    } catch (error) {
        console.error('Erro ao gerar referência:', error)
    }
}

const buscarPorLoan = () => {
    router.get('/referenciapgt', { tipo: 3, loan: filtroLoan.value }, { preserveState: true })
    showModalLoan.value = false
}

const buscarPorDatas = () => {
    router.get('/referenciapgt', {
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
