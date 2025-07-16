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


        <ConfirmationModalExtrato :show="showDeleteModal" :extratoData="selectedExtrato" :isDeleting="isDeleting"
            @confirm="proceedWithDeletion" @cancel="cancelDeletion" />
        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>

                <h1 class="text-2xl font-bold text-green-950"><i
                        class="fas fa-money-bill-wave mr-3 text-3xl"></i>Aplicações de Desembolsos</h1>
                <p class="text-sm text-gray-600">Gestão de desembolsos </p>
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
        <hr />
        <!-- Filtro Avançado -->
        <div class="mb-6 bg-gray-50 p-3 sm:p-4 rounded-lg ">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3  ">
                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por Loan Number
                    </label>
                    <button class=" btn btn-outline-secondary  flex items-center gap-2" @click="showModalLoan = true">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Clicar Aqui
                    </button>


                </div>

                <div class="col-span-2 sm:col-span-1">



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Data de Início -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Periodo de Início*</label>
                            <div class="relative">
                                <input v-model="filtro.dataInicioInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 transition"
                                    :max="filtro.dataFimInput" @change="validarDatas" />
                                <span v-if="erros.dataInicio" class="text-red-500 text-xs">{{ erros.dataInicio }}</span>
                            </div>
                        </div>

                        <!-- Data de Fim -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Periodo de Fim*</label>
                            <div class="relative">
                                <input v-model="filtro.dataFimInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 transition"
                                    :min="filtro.dataInicioInput" @change="validarDatas" />
                                <span v-if="erros.dataFim" class="text-red-500 text-xs">{{ erros.dataFim }}</span>
                            </div>
                        </div>
                    </div>



                </div>




                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por Base </label>
                    <select v-model="filtro.agencia" class="input input-bordered w-full">
                        <option disabled :value="'s/a'">Escolha agência</option>

                        <option v-for="agencia in $page.props.bases" :value="agencia.OfIdentificador"
                            :key="agencia.OfIdentificador">
                            {{ agencia.OfIdentificador }} - {{ agencia.OfNombre }}
                        </option>
                        <option :value="'T'">Todas que tenho acesso</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button @click="resetarFiltros" class="btn btn-outline-secondary mr-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-4 w-4 mr-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                    </svg>
                    Limpar Filtros
                </button>
                <button @click="aplicarFiltros" class="btn btn-primary">

                    Aplicar Filtros &MediumSpace;

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>

            </div>



        </div>





        <div class="rounded-lg p-4 w-full bg-gray-100 border border-white shadow-sm ">
            <!-- Cabeçalho do período -->
            <div class="flex items-center justify-between pb-4 border-b border-white">
                <div class="bg-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-gray-700">DESEMBOLSOS DO PERÍODO:</span>
                    <span class="text-green-800 font-bold text-base">{{ dataFimPeriodo }} </span>
                    <span class="text-gray-600 font-normal">à</span>
                    <span class="text-green-800 font-bold text-base">{{ dataInicioPeriodo }}</span>
                </div>
            </div>

            <!-- Cards de métricas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6 w-full">
                <!-- Card 1 - Total Montante Reembolsos -->
                <div
                    class="flex items-center p-5 bg-gradient-to-r from-green-50 to-green-100 rounded-lg border border-green-200 shadow-sm hover:shadow-md transition-all">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 text-green-700">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-600 font-medium uppercase tracking-wider">TOTAL DE MONTANTE
                            DESEMBOLSADO</p>
                        <p class="text-xl font-bold text-green-900 mt-1">{{ formatCurrency(montantetotal) }} <span
                                class="text-sm font-normal">AKZ</span></p>
                    </div>
                </div>

                <!-- Você pode adicionar mais 3 cards aqui mantendo o mesmo padrão -->
                <!-- Exemplo de card adicional (substitua com seus dados reais) -->
                <div
                    class="flex items-center p-5 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border border-blue-200 shadow-sm hover:shadow-md transition-all">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 text-blue-700">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-600 font-medium uppercase tracking-wider">TOTAL DE PROCESSOS
                            APLICADOS</p>
                        <p class="text-xl font-bold text-blue-900 mt-1">{{ formatCurrency(total) }} <span
                                class="text-sm font-normal">itens</span></p>
                    </div>
                </div>

                <!-- Adicione mais cards conforme necessário -->
            </div>
        </div>

        <br />

        <!-- Card Principal -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <!-- Cabeçalho do Card -->


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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                    #
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Data de Registo
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Loan
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Cliente
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Produto
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Valor de Desembolso
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Ref. de Pagamento
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                <div class="flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Ações
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(item, index) in extratosPaginados" :key="index" class="hover:bg-gray-50">
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
                                    class="px-1.5 py-0.5 text-[0.7rem] btn btn-outline-referencia flex items-center gap-1 mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-3 h-3 text-orange-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                                    {{ item.referenciapagamento }}
                                </button>

                                <button v-else
                                    class="px-1.5 py-0.5 text-[0.7rem] btn btn-outline-referencia-activada flex items-center gap-1 mx-auto cursor-not-allowed"
                                    disabled>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-3 h-3 text-green-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    {{ item.referenciapagamento }}
                                </button>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex space-x-1 justify-center">
                                    <a :href="`/reports/extrato/${item.Num}`"
                                        class="px-1.5 py-0.5 text-[0.7rem] btn btn-warning flex items-center gap-1"
                                        target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        PDF
                                    </a>
                                    <button @click="abrirModalDetalhes(item)"
                                        class="px-1.5 py-0.5 text-[0.7rem] btn btn-info flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                        </svg>
                                        Detalhes
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <button @click="initiateDeletion(item)" :disabled="!podeEliminar(item)"
                                    :class="{
                                        'opacity-50 cursor-not-allowed': !podeEliminar(item),
                                        'text-red-600 hover:text-red-900': podeEliminar(item),
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
                        <tr v-if="extratosPaginados.length === 0">
                            <td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">
                                Nenhum desembolso encontrado com os filtros aplicados
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
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

    <ModalLoan :isOpen="showModalLoan" @close="showModalLoan = false" @search="buscarPorLoan" v-model="filtroLoan" />
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

const selectedExtrato= ref({
  lnr: '',
  cliente: '',
  montante: 0,
  data: '',


})


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
    const isRegistadoHoje = item.CiFecha === hoje.value
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
