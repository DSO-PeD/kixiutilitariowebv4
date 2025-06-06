<template>

    <Head title="Reconciliação" />

    <div class="w-full max-w-[calc(100vw-30rem)] mx-auto"> <!-- Ajuste 17rem conforme o sidebar -->
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
                <h1 class="text-2xl font-bold text-green-950">Reconciliação de Comprovativos</h1>
                <p class="text-sm text-gray-600">Validação de pagamentos registrados</p>
            </div>
        </div>

        <!-- Cabeçalho do Card -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">

            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">

                <button class="btn btn-outline-consulta flex items-center gap-2" @click="showModalLoan = true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Consultar Dados por:= Loan Number
                </button>
                <button class="btn btn-outline-consulta flex items-center gap-2" @click="showModalData = true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                    Consultar Dados por:= Data, Agência e Estado
                </button>

            </div>
        </div>

        <!-- Card Principal -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6 w-full min-w-max">



            <hr />
            <!-- Filtro Avançado -->
            <div class="mb-6 bg-gray-50 p-3 sm:p-4 rounded-lg">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtro Geral nos Dados
                            Listados na
                            Tabela</label>
                        <div class="relative">
                            <input type="text" v-model="filtro.search" placeholder="Digite para filtrar..."
                                class="input input-bordered w-full pl-10" />
                            <span class="absolute right-3 top-3 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por LNR nos Dados
                            Listados
                            na Tabela</label>
                        <input type="text" v-model="filtro.lnr" placeholder="Número do Loan"
                            class="input input-bordered w-full" />
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por Estado nos
                            Dados
                            Listados na Tabela</label>

                        <select v-model="filtro.estado" class="input input-bordered w-full">
                            <option value="">Todos</option>
                            <option v-for="estado in $page.props.estados" :value="estado.descricao_estado"
                                :key="estado.id">{{ estado.descricao_estado }}</option>

                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por Agência nos
                            Dados
                            Listados na Tabela</label>
                        <input type="text" v-model="filtro.agencia" placeholder="Nome da Agência"
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

                <div class="mt-4 flex justify-start">
                    <button class="btn btn-outline-excel flex items-center gap-2 " @click="exportarParaExcel">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Exportar Dados da tabela para Excel
                    </button>
                </div>


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

            <!-- Tabela Responsiva -->
            <div class="overflow-x-auto w-full">
                <table class="w-full min-w-[1024px] divide-y divide-gray-200 lg:min-w-0">
                    <!-- lg:min-w-0 para desktop -->
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[50px]">
                                #
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    Arquivo
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="Registado">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>

                                    Registado
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="Agência">

                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>


                                    Agência
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>

                                    Registado Por
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="Cliente">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                    </svg>


                                    Cliente
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="LNR">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    LNR
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="Produto">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
                                    </svg>

                                    Produto
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="Montante">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>

                                    Montante
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="Estado">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                    </svg>

                                    Estado
                                </div>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="Ações">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 font-thin">
                        <tr v-for="(comprovativo, index) in comprovativosFiltrados" :key="comprovativo.id"
                            class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-normal text-sm text-gray-500">
                                {{ calcularNumeroLinha(index) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <a v-if="comprovativo.file" :href="`/storage/comprovativos/${comprovativo.file}`"
                                    target="_blank" class="text-blue-600 hover:underline flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 mr-1 text-red-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    Visualizar
                                </a>
                                <span v-else class="text-gray-400 italic">Sem arquivo</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.data }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.agencia || '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.usuario }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.cliente || '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">



                                <button @click="abrirModalReconciliacaoDetalhe(comprovativo.id)"
                                    class="btn btn-action btn-detail flex items-center gap-1 mx-auto"
                                    style="font-size:10px;">
                                    {{ comprovativo.lnr }}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m12.75 15 3-3m0 0-3-3m3 3h-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </button>



                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.metodologia }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600 bg-yellow-50">
                                {{ formatCurrency(comprovativo.montante) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">

                                <span :class="comprovativo.color" class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ comprovativo.estado }}
                                </span>

                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">

                                <button @click="abrirModalReconciliacao(comprovativo)"
                                    class="btn btn-action btn-validate flex items-center gap-1 mx-auto"
                                    :disabled="deveDesativarBotao(comprovativo.estado)">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4">...</svg>
                                    <span class="hidden sm:inline">Mudar Estado</span>
                                    <span class="sm:hidden">Estado</span>
                                </button>


                            </td>
                        </tr>
                        <tr v-if="comprovativosFiltrados.length === 0">
                            <td colspan="11" class="px-4 py-4 text-center text-sm text-gray-500">
                                Nenhum comprovativo encontrado.
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
import ModalReconcialiacao from './Layouts/components/ComprovativosComponents/ModalReconcialiacao.vue'
import ModalComprovativoDetalhe from './Layouts/components/ComprovativosComponents/ModalComprovativoDetalhe.vue'

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
        default: 15
    },
    total: Number,
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
    user: Object
})

// Estados
const showModalLoan = ref(false)
const showModalData = ref(false)
const showModalEliminar = ref(false)
const showModalReconcialiacao = ref(false)
const paginaAtual = ref(props.page || 1)
const totalItens = ref(props.total || 0)
const comprovativoSelecionado = ref(null)

// Filtros
const filtro = ref({
    search: props.filters.search || '',
    lnr: props.filters.lnr || '',
    estadoconsulta: props.filters.estado || '',
    agenciaconsulta: props.filters.agencia || '',
    data_inicio: props.filters.data_inicio || '',
    data_fim: props.filters.data_fim || ''
})

const filtroLoan = ref('')
const dataInicio = ref('')
const dataFim = ref('')
const estadoModal = ref(0)
const agenciaModal = ref('')

// Computed
const comprovativosFiltrados = computed(() => {
    return props.comprovativos.filter(comp => {
        const searchTerm = filtro.value.search.toLowerCase()
        const matchesSearch =
            (comp.usuario && comp.usuario.toLowerCase().includes(searchTerm)) ||
            (comp.lnr && comp.lnr.toString().includes(searchTerm)) ||
            (comp.agencia && comp.agencia.toLowerCase().includes(searchTerm)) ||
            (comp.metodologia && comp.metodologia.toLowerCase().includes(searchTerm)) ||
            (comp.basedelacamento && comp.basedelacamento.toLowerCase().includes(searchTerm))

        const matchesLnr = !filtro.value.lnr || (comp.lnr && comp.lnr.toString().includes(filtro.value.lnr))
        const matchesEstado = !filtro.value.estado || comp.estado === filtro.value.estado
        const matchesAgencia = !filtro.value.agencia || (comp.agencia && comp.agencia.toLowerCase().includes(filtro.value.agencia.toLowerCase()))

        return matchesSearch && matchesLnr && matchesEstado && matchesAgencia
    })
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

const aplicarFiltros = () => {
    router.get('/reconciliacao', {
        ...filtro.value,
        page: 1
    }, {
        preserveState: true,
        replace: true
    })
}

const buscarPorLoan = () => {
    router.get('/reconciliacao', { tipo: 3, loan: filtroLoan.value }, { preserveState: true })
    showModalLoan.value = false
}

const resetarFiltros = () => {
    filtro.value = {
        search: '',
        lnr: '',
        estadoconsulta: '',
        agenciaconsulta: '',
        data_inicio: '',
        data_fim: ''
    }
    aplicarFiltros()
}

const mudarPagina = (novaPagina) => {
    router.get('/reconciliacao', {
        ...filtro.value,
        page: novaPagina
    }, {
        preserveState: true,
        replace: true
    })
}
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
                    'Data': comprovativo.CiFecha ? new Date(comprovativo.CiFecha).toLocaleDateString('pt-PT') : '-',
                    'Agência': comprovativo.OfNombre || '-',
                    'Registado Por': comprovativo.UtNome || '-',
                    //'Base': comprovativo.basedelacamento || '-',
                    'LNR': comprovativo.BuDadoOrigem || '-',
                    'Cliente': comprovativo.infoadicional || '-',
                    'Produto': comprovativo.PoAgrupado || '-',
                    'Voucher': comprovativo.BuReferencia || '-',
                    'Montante': formatCurrency(comprovativo.BuMontante) || '0,00',
                    'Estado': comprovativo.estado || '-',
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
    // 1. Encontra o comprovativo
    comprovativoDetalhe.value = props.comprovativos.find(c => c.id === idComprovativo);

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
};




const handleReconciliationSuccess = () => {
    showModalReconcialiacao.value = false
    router.reload({ only: ['comprovativos'] })
}

const deveDesativarBotao = (estado) => {
    return ['Validado', 'Reflete'].includes(estado);
}
// Watchers
watch(() => props.filters, (newFilters) => {
    filtro.value.search = newFilters.search || ''
    filtro.value.lnr = newFilters.lnr || ''
    filtro.value.estado = newFilters.estado || ''
    filtro.value.agencia = newFilters.agencia || ''
    filtro.value.data_inicio = newFilters.data_inicio || ''
    filtro.value.data_fim = newFilters.data_fim || ''
})

watch(() => props.page, (newPage) => {
    paginaAtual.value = newPage
})

watch(() => props.total, (newTotal) => {
    totalItens.value = newTotal
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
    @apply bg-gradient-to-r from-green-900 to-greenkixi-300 text-white hover:from-green-800 hover:bg-green-800 shadow-md hover:shadow-lg;
}

.btn-outline {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
}

.btn-outline-secondary {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
}

.btn-outline-consulta {
    @apply border border-gray-300 bg-white text-cyan-800 hover:bg-gray-50;
}

.btn-outline-excel {
    @apply border border-green-900 bg-white text-green-900 hover:bg-green-50;
}

.btn-action {
    @apply px-3 py-1.5 rounded-md text-sm font-medium transition-colors;
}

.btn-validate {
    @apply bg-stone-50 text-stone-600 hover:bg-stone-500 border border-stone-200;
}

.btn-detail {
    @apply bg-cyan-50 text-cyan-600 hover:bg-cyan-500 border border-cyan-200;
}


.btn-validate:disabled {
    @apply bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed;
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

.btn-validate:disabled {
    @apply bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed;
    opacity: 0.7;
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

/* Estados */
.bg-green-100 {
    background-color: #dcfce7;
}

.text-green-800 {
    color: #166534;
}

.bg-yellow-100 {
    background-color: #fef9c3;
}

.text-yellow-800 {
    color: #92400e;
}

.bg-red-100 {
    background-color: #fee2e2;
}

.text-red-800 {
    color: #991b1b;
}

/* Efeitos de hover */
tr:hover {
    @apply bg-gray-50;
}

/* Garante que a tabela seja scrollável horizontalmente */
.table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

/* Versão mobile - transforma tabela em cards */
@media (max-width: 1023px) {
    table {
        display: block;
        width: 100%;
    }

    thead {
        display: none;
    }

    tbody {
        display: block;
        width: 100%;
    }

    tr {
        display: block;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1rem;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f3f4f6;
        text-align: right;
    }

    td:last-child {
        border-bottom: none;
    }

    td::before {
        content: attr(data-label);
        float: left;
        font-weight: 500;
        color: #6b7280;
        margin-right: 1rem;
    }

    /* Esconde algumas colunas menos importantes em mobile */
    td:nth-child(4),
    /* Agência */
    td:nth-child(6)

    /* Cliente */
        {
        display: none;
    }
}

/* Melhora a legibilidade em mobile */
@media (max-width: 640px) {
    .btn {
        @apply px-3 py-1.5 text-xs;
    }

    .input {
        @apply py-1.5 text-sm;
    }

    /* Ajusta o tamanho dos textos */
    td {
        @apply text-sm;
    }

    /* Garante que os textos longos quebrem */
    .whitespace-normal {
        white-space: normal !important;
    }
}

/* Barra de rolagem personalizada */
::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

::-webkit-scrollbar-thumb {
    @apply bg-gray-300 rounded-full;
}

::-webkit-scrollbar-track {
    @apply bg-gray-100;
}

@media (min-width: 768px) and (max-width: 1023px) {
    /* Mostra mais colunas que em mobile */
    td:nth-child(4),
    /* Agência */
    td:nth-child(6)

    /* Cliente */
        {
        display: flex !important;
    }

    /* Ajusta o tamanho dos textos */
    td {
        @apply text-sm;
    }

    /* Reduz o padding */
    td {
        @apply px-2 py-3;
    }
}
</style>
