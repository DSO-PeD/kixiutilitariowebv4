<template>
    <Head title="Monitoramento de Captação de Clientes" />

    <div class="container mx-auto py-4 md:py-6 max-w-full">
        <!-- Alertas de Sistema -->
        <div v-if="$page.props.flash?.success" class="alert alert-success mb-4 animate-fade-in">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $page.props.flash.success }}
            </div>
        </div>

        <div v-if="$page.props.flash?.error" class="alert alert-danger mb-4 animate-fade-in">
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
                    <svg class="w-7 h-7 text-green-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Monitoramento de Captação de Clientes</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Acompanhamento de prospecção, atribuições hierárquicas e pareceres de crédito
                    </p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <!-- Cards de Resumo / KPIs -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Total Captados</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ totalItens }}</p>
                    </div>
                    <div class="bg-green-100 p-2.5 rounded-lg text-green-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Registos no filtro atual</p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Montante Solicitado</p>
                        <p class="text-xl font-bold text-green-700 mt-1">{{ formatCurrency(resumoKPI.valorTotal) }} AKZ</p>
                    </div>
                    <div class="bg-emerald-100 p-2.5 rounded-lg text-emerald-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Volume financeiro prospectado</p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Atribuídos a Oficial</p>
                        <p class="text-2xl font-bold text-blue-700 mt-1">{{ resumoKPI.comOficial }}</p>
                    </div>
                    <div class="bg-blue-100 p-2.5 rounded-lg text-blue-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Sem atribuição: <span class="font-semibold text-amber-600">{{ resumoKPI.semAtribuicao }}</span>
                </p>
            </div>

            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase">Com Parecer / Detalhe</p>
                        <p class="text-2xl font-bold text-purple-700 mt-1">{{ resumoKPI.comDetalhe }}</p>
                    </div>
                    <div class="bg-purple-100 p-2.5 rounded-lg text-purple-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Rejeitados: <span class="font-semibold text-red-600">{{ resumoKPI.rejeitados }}</span>
                </p>
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
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    <!-- Data Início -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Captação (Início)</label>
                        <input v-model="filtro.dataInicioInput" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm"
                            @change="validarDatas" />
                        <span v-if="erros.dataInicio" class="text-red-500 text-xs">{{ erros.dataInicio }}</span>
                    </div>

                    <!-- Data Fim -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Captação (Fim)</label>
                        <input v-model="filtro.dataFimInput" type="date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm"
                            @change="validarDatas" />
                        <span v-if="erros.dataFim" class="text-red-500 text-xs">{{ erros.dataFim }}</span>
                    </div>

                    <!-- Agência -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Agência</label>
                        <div class="relative">
                            <select v-model="filtro.agencia"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white">
                                <option :value="'T'">Todas que tenho acesso</option>
                                <option v-for="agencia in $page.props.bases" :value="agencia.OfCodigo"
                                    :key="agencia.OfCodigo">
                                    {{ agencia.OfCodigo }} - {{ agencia.OfNombre }}
                                </option>
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

                    <!-- Estado da Resposta -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <div class="relative">
                            <select v-model="filtro.estado"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white">
                                <option :value="0">Todos os Estados</option>
                                <option v-for="est in estados" :value="est.id" :key="est.id">
                                    {{ est.descricao }}
                                </option>
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

                    <!-- Fase de Atribuição -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fase de Atribuição</label>
                        <div class="relative">
                            <select v-model="filtro.faseAtribuicao"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm appearance-none bg-white">
                                <option value="TODOS">Todas as Fases</option>
                                <option value="SEM_ATRIBUICAO">Aguardando Atribuição</option>
                                <option value="GERENTE">Com Gerente</option>
                                <option value="SUPERVISOR">Com Supervisor</option>
                                <option value="OFICIAL">Com Oficial de Crédito</option>
                                <option value="COM_PARECER">Com Parecer Registado</option>
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

                <!-- Linha 2: Pesquisa rápida -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Pesquisar Cliente, Telefone, Local ou Utilizador
                        </label>
                        <input v-model="filtro.search" type="text"
                            placeholder="Ex: Luana André, 923..., Praça das Mulheres, dion.andr..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition text-sm" />
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

        <!-- Tabela de Captações -->
        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
            <!-- Cabeçalho da Tabela -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                <div class="text-sm text-gray-600">
                    <template v-if="totalItens > 0">
                        Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                        de {{ totalItens }} registos
                    </template>
                    <template v-else>
                        0 registos encontrados
                    </template>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                #
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Data Captação
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Agência
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Cliente / Empresa
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Telefone
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tipo & Local Negócio
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Valor Solicitado
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Captador
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Fluxo de Atribuição
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Parecer / Detalhe
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(item, index) in captacoesPaginadas" :key="item.id"
                            class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ calcularNumeroLinha(index) }}
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <button @click="abrirModalDetalhes(item)"
                                    class="btn btn-outline-primary-pgr btn-sm flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detalhes
                                </button>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ formatarData(item.data_captacao) }}
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                {{ getNomeAgencia(item.OfCodigoAgencia) }}
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ item.nome_cliente_empresa }}
                                    </span>
                                    <span class="px-1.5 py-0.5 text-xs font-bold rounded bg-gray-100 text-gray-600">
                                        {{ item.genero }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ item.telefone || '-' }}
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-800">
                                    {{ item.tipo_negocio?.descricao || `Tipo #${item.id_tipo_negocio}` }}
                                </div>
                                <div class="text-xs text-gray-500 max-w-xs truncate" :title="item.local_negocio">
                                    {{ item.local_negocio }}
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-700">
                                {{ formatCurrency(item.valor_solicitado) }} AKZ
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div class="font-medium text-gray-800">
                                    {{ item.captador?.UtNome || item.UtCodigoCaptador }}
                                </div>
                                <div v-if="item.captador?.UtNome" class="text-xs text-gray-400">
                                    {{ item.UtCodigoCaptador }}
                                </div>
                            </td>

                            <!-- Fluxo Hierárquico de Atribuição -->
                            <td class="px-4 py-4 text-xs">
                                <div class="space-y-1">
                                    <div v-if="item.UtCodigoGerente" class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">
                                            Gerente:
                                        </span>
                                        <span class="text-gray-700">
                                            {{ item.gerente?.UtNome || item.UtCodigoGerente }}
                                        </span>
                                    </div>

                                    <div v-if="item.UtCodigoSupervisor" class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 font-semibold">
                                            Supervisor:
                                        </span>
                                        <span class="text-gray-700">
                                            {{ item.supervisor?.UtNome || item.UtCodigoSupervisor }}
                                        </span>
                                    </div>

                                    <div v-if="item.UtCodigoOficialCredito" class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 font-semibold">
                                            Oficial:
                                        </span>
                                        <span class="text-gray-700 font-medium">
                                            {{ item.oficial_credito?.UtNome || item.UtCodigoOficialCredito }}
                                        </span>
                                    </div>

                                    <span v-if="!item.UtCodigoGerente && !item.UtCodigoSupervisor && !item.UtCodigoOficialCredito"
                                        class="text-gray-400 italic">
                                        Não atribuído
                                    </span>
                                </div>
                            </td>

                            <!-- Estado -->
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full"
                                    :style="getEstiloEstado(item)">
                                    {{ item.estado_resposta?.descricao || 'Pendente' }}
                                </span>
                            </td>

                            <!-- Resumo do Detalhe / Parecer (tblcaptacaoclientedetalhe) -->
                            <td class="px-4 py-4 text-xs">
                                <template v-if="getDetalhePrincipal(item)">
                                    <div class="font-medium text-gray-800">
                                        BI: {{ getDetalhePrincipal(item).numero_bi || '-' }}
                                        <span v-if="getDetalhePrincipal(item).idade">
                                            ({{ getDetalhePrincipal(item).idade }} anos)
                                        </span>
                                    </div>
                                    <div v-if="getDetalhePrincipal(item).razao_rejeicao"
                                        class="text-red-600 font-medium max-w-xs truncate"
                                        :title="getDetalhePrincipal(item).razao_rejeicao">
                                        Rejeição: {{ getDetalhePrincipal(item).razao_rejeicao }}
                                    </div>
                                    <div v-else-if="getDetalhePrincipal(item).observacoes"
                                        class="text-gray-500 max-w-xs truncate"
                                        :title="getDetalhePrincipal(item).observacoes">
                                        {{ getDetalhePrincipal(item).observacoes }}
                                    </div>
                                </template>
                                <span v-else class="text-gray-400 italic">Sem parecer</span>
                            </td>
                        </tr>

                        <tr v-if="captacoesPaginadas.length === 0">
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    <p class="text-sm">Nenhuma captação encontrada</p>
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
                    <template v-if="totalItens > 0">
                        Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                        de {{ totalItens }} registos
                    </template>
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

    <!-- Modal de Detalhes, Histórico de Atribuições e Parecer (tblcaptacaoclientedetalhe) -->
    <div v-if="showModalDetalhes && captacaoSelecionada"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full overflow-hidden animate-fade-in">
            <!-- Cabeçalho Modal -->
            <div class="flex items-center justify-between px-6 py-4 bg-green-900 text-white">
                <div>
                    <h3 class="text-lg font-bold">
                        Ficha de Monitoramento #{{ captacaoSelecionada.id }}
                    </h3>
                    <p class="text-xs text-green-100">
                        {{ captacaoSelecionada.nome_cliente_empresa }} • {{ getNomeAgencia(captacaoSelecionada.OfCodigoAgencia) }}
                    </p>
                </div>
                <button @click="fecharModalDetalhes" class="text-green-100 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Corpo Modal -->
            <div class="p-6 max-h-[80vh] overflow-y-auto space-y-6">
                <!-- Secção 1: Dados da Captação (tblCaptacoesClientes) -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase border-b pb-2 mb-3">
                        1. Dados da Prospecção
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 block text-xs">Cliente / Empresa</span>
                            <span class="font-semibold text-gray-900">{{ captacaoSelecionada.nome_cliente_empresa }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-xs">Género / Telefone</span>
                            <span class="font-medium text-gray-800">
                                {{ captacaoSelecionada.genero }} • {{ captacaoSelecionada.telefone || '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-xs">Valor Solicitado</span>
                            <span class="font-bold text-green-700">
                                {{ formatCurrency(captacaoSelecionada.valor_solicitado) }} AKZ
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 block text-xs">Tipo de Negócio</span>
                            <span class="font-medium text-gray-800">
                                {{ captacaoSelecionada.tipo_negocio?.descricao || captacaoSelecionada.id_tipo_negocio }}
                            </span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-gray-500 block text-xs">Local do Negócio</span>
                            <span class="font-medium text-gray-800">{{ captacaoSelecionada.local_negocio }}</span>
                        </div>
                        <div v-if="captacaoSelecionada.ObservacaoEstado" class="sm:col-span-3">
                            <span class="text-gray-500 block text-xs">Observação do Estado</span>
                            <span class="font-medium text-gray-800">{{ captacaoSelecionada.ObservacaoEstado }}</span>
                        </div>
                    </div>
                </div>

                <!-- Secção 2: Cronograma de Atribuições -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase border-b pb-2 mb-3">
                        2. Cadeia de Atribuição
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-xs font-semibold text-gray-500 block">Captado por</span>
                            <span class="font-bold text-gray-800">
                                {{ captacaoSelecionada.captador?.UtNome || captacaoSelecionada.UtCodigoCaptador }}
                            </span>
                            <span class="text-xs text-gray-500 block mt-0.5">
                                Data: {{ formatarData(captacaoSelecionada.data_captacao) }}
                            </span>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-xs font-semibold text-gray-500 block">Atribuído ao Gerente</span>
                            <span class="font-bold text-gray-800">
                                {{ captacaoSelecionada.gerente?.UtNome || captacaoSelecionada.UtCodigoGerente || 'Pendente' }}
                            </span>
                            <span class="text-xs text-gray-500 block mt-0.5">
                                {{ formatarDataHora(captacaoSelecionada.data_atribuicao_gerente) }}
                            </span>
                        </div>

                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-xs font-semibold text-gray-500 block">Atribuído ao Supervisor</span>
                            <span class="font-bold text-gray-800">
                                {{ captacaoSelecionada.supervisor?.UtNome || captacaoSelecionada.UtCodigoSupervisor || 'Pendente' }}
                            </span>
                            <span class="text-xs text-gray-500 block mt-0.5">
                                {{ formatarDataHora(captacaoSelecionada.data_atribuicao_supervisor) }}
                            </span>
                        </div>

                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <span class="text-xs font-semibold text-blue-700 block">Atribuído ao Oficial de Crédito</span>
                            <span class="font-bold text-blue-900">
                                {{ captacaoSelecionada.oficial_credito?.UtNome || captacaoSelecionada.UtCodigoOficialCredito || 'Pendente' }}
                            </span>
                            <span class="text-xs text-blue-600 block mt-0.5">
                                {{ formatarDataHora(captacaoSelecionada.data_atribuicao_oficial) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Secção 3: Detalhes / Parecer do Oficial (tblcaptacaoclientedetalhe) -->
                <div>
                    <h4 class="text-sm font-bold text-gray-700 uppercase border-b pb-2 mb-3">
                        3. Parecer e Detalhes do Oficial de Crédito
                    </h4>

                    <div v-if="detalhesSelecionados.length > 0" class="space-y-3">
                        <div v-for="det in detalhesSelecionados" :key="det.id"
                            class="p-4 rounded-lg border border-gray-200 bg-white shadow-sm space-y-2 text-sm">
                            <div class="flex flex-wrap justify-between items-center gap-2 border-b pb-2">
                                <span class="font-semibold text-gray-800">
                                    Oficial: {{ det.oficial?.UtNome || det.UtCodigoOficial || '-' }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    Registado em: {{ formatarDataHora(det.created_at) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <span class="text-xs text-gray-500 block">Número do BI</span>
                                    <span class="font-medium text-gray-900">{{ det.numero_bi || '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block">Idade</span>
                                    <span class="font-medium text-gray-900">{{ det.idade ? `${det.idade} anos` : '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-xs text-gray-500 block">Estado Registado</span>
                                    <span class="font-medium text-gray-900">
                                        {{ det.estado_resposta?.descricao || det.id_estado_resposta_agencia || '-' }}
                                    </span>
                                </div>
                            </div>

                            <div v-if="det.data_rejeicao || det.razao_rejeicao"
                                class="p-3 bg-red-50 border border-red-200 rounded-lg text-xs text-red-800 mt-2">
                                <p class="font-bold">
                                    Rejeitado em {{ formatarData(det.data_rejeicao) }}
                                    <span v-if="det.id_quem_rejeitou">(por {{ det.id_quem_rejeitou }})</span>
                                </p>
                                <p class="mt-1">Motivo: {{ det.razao_rejeicao || 'Não especificado' }}</p>
                            </div>

                            <div v-if="det.observacoes" class="pt-1">
                                <span class="text-xs text-gray-500 block">Observações do Oficial</span>
                                <p class="text-gray-800 bg-gray-50 p-2.5 rounded border border-gray-100 mt-1">
                                    {{ det.observacoes }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <p class="text-sm text-gray-500">Ainda não existe parecer registado para este cliente.</p>
                    </div>
                </div>
            </div>

            <!-- Rodapé Modal -->
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex justify-end">
                <button @click="fecharModalDetalhes" class="btn btn-outline-secondary">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, Head } from '@inertiajs/vue3'
import * as XLSX from 'xlsx'

// Props vindas do Controller Inertia
const props = defineProps({
    lista_captacoes: {
        type: Array,
        default: () => []
    },
    estados: {
        type: Array,
        default: () => []
    },
    tipos_negocio: {
        type: Array,
        default: () => []
    },
    bases: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    page: {
        type: Number,
        default: 1
    },
    perPage: {
        type: Number,
        default: 50
    },
    auth: Object,
    user: Object,
    flash: Object,
    errors: Object
})

// Estado de Paginação e UI
const paginaAtual = ref(props.page || 1)
const perPage = ref(props.perPage || 50)
const filtrosVisiveis = ref(true)
const showModalDetalhes = ref(false)
const captacaoSelecionada = ref(null)

// Filtros
const filtro = ref({
    search: props.filters?.search || '',
    agencia: props.filters?.agencia || 'T',
    estado: Number(props.filters?.estado) || 0,
    faseAtribuicao: props.filters?.fase_atribuicao || 'TODOS',
    dataInicioInput: props.filters?.data_inicio || '',
    dataFimInput: props.filters?.data_fim || '',
})

const erros = ref({
    dataInicio: '',
    dataFim: ''
})

// Helper para extrair o detalhe mais recente (suporta relação hasOne `detalhe` ou hasMany `detalhes`)
const getDetalhePrincipal = (item) => {
    if (item.detalhe) return item.detalhe
    if (Array.isArray(item.detalhes) && item.detalhes.length > 0) {
        return item.detalhes[item.detalhes.length - 1]
    }
    return null
}

const detalhesSelecionados = computed(() => {
    if (!captacaoSelecionada.value) return []
    if (Array.isArray(captacaoSelecionada.value.detalhes)) {
        return captacaoSelecionada.value.detalhes
    }
    if (captacaoSelecionada.value.detalhe) {
        return [captacaoSelecionada.value.detalhe]
    }
    return []
})

// Filtragem reativa no cliente (além do filtro no backend via Inertia)
const captacoesFiltradas = computed(() => {
    return (props.lista_captacoes || []).filter((item) => {
        // Filtro por Agência
        if (filtro.value.agencia !== 'T' && Number(item.OfCodigoAgencia) !== Number(filtro.value.agencia)) {
            return false
        }

        // Filtro por Estado
        if (Number(filtro.value.estado) > 0 && Number(item.id_estado_resposta_agencia) !== Number(filtro.value.estado)) {
            return false
        }

        // Filtro por Fase de Atribuição
        const det = getDetalhePrincipal(item)
        if (filtro.value.faseAtribuicao === 'SEM_ATRIBUICAO') {
            if (item.UtCodigoGerente || item.UtCodigoSupervisor || item.UtCodigoOficialCredito) return false
        } else if (filtro.value.faseAtribuicao === 'GERENTE') {
            if (!item.UtCodigoGerente) return false
        } else if (filtro.value.faseAtribuicao === 'SUPERVISOR') {
            if (!item.UtCodigoSupervisor) return false
        } else if (filtro.value.faseAtribuicao === 'OFICIAL') {
            if (!item.UtCodigoOficialCredito) return false
        } else if (filtro.value.faseAtribuicao === 'COM_PARECER') {
            if (!det) return false
        }

        // Filtro por Datas
        if (filtro.value.dataInicioInput && item.data_captacao < filtro.value.dataInicioInput) {
            return false
        }
        if (filtro.value.dataFimInput && item.data_captacao > filtro.value.dataFimInput) {
            return false
        }

        // Pesquisa textual
        if (filtro.value.search.trim()) {
            const termo = filtro.value.search.toLowerCase()
            const campos = [
                item.nome_cliente_empresa,
                item.telefone,
                item.local_negocio,
                item.UtCodigoCaptador,
                item.UtCodigoGerente,
                item.UtCodigoSupervisor,
                item.UtCodigoOficialCredito,
                item.captador?.UtNome,
                item.oficial_credito?.UtNome,
                det?.numero_bi
            ]
            const match = campos.some((c) => c && String(c).toLowerCase().includes(termo))
            if (!match) return false
        }

        return true
    })
})

// Paginação
const totalItens = computed(() => captacoesFiltradas.value.length)
const captacoesPaginadas = computed(() =>
    captacoesFiltradas.value.slice((paginaAtual.value - 1) * perPage.value, paginaAtual.value * perPage.value)
)
const hasMorePages = computed(() => paginaAtual.value * perPage.value < totalItens.value)

// KPIs calculados sobre os dados filtrados
const resumoKPI = computed(() => {
    let valorTotal = 0
    let comOficial = 0
    let semAtribuicao = 0
    let comDetalhe = 0
    let rejeitados = 0

    captacoesFiltradas.value.forEach((item) => {
        valorTotal += parseFloat(item.valor_solicitado) || 0
        if (item.UtCodigoOficialCredito) comOficial++
        if (!item.UtCodigoGerente && !item.UtCodigoSupervisor && !item.UtCodigoOficialCredito) {
            semAtribuicao++
        }
        const det = getDetalhePrincipal(item)
        if (det) {
            comDetalhe++
            if (det.data_rejeicao || det.razao_rejeicao) rejeitados++
        }
    })

    return { valorTotal, comOficial, semAtribuicao, comDetalhe, rejeitados }
})

// Helpers de Formatação
const formatCurrency = (value) => {
    if (value == null || value === '') return '0,00'
    const num = typeof value === 'string' ? parseFloat(value) : value
    if (isNaN(num)) return '0,00'
    return num.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const formatarData = (data) => {
    if (!data) return '-'
    const d = new Date(data)
    return isNaN(d.getTime()) ? data : d.toLocaleDateString('pt-PT')
}

const formatarDataHora = (dataHora) => {
    if (!dataHora) return 'Sem registo'
    const d = new Date(dataHora)
    return isNaN(d.getTime())
        ? dataHora
        : d.toLocaleString('pt-PT', { dateStyle: 'short', timeStyle: 'short' })
}

const getNomeAgencia = (ofCodigo) => {
    const ag = (props.bases || []).find((b) => Number(b.OfCodigo) === Number(ofCodigo))
    return ag ? `${ag.OfCodigo} - ${ag.OfNombre}` : `Agência #${ofCodigo}`
}

const getEstiloEstado = (item) => {
    const cor = item.estado_resposta?.cor_hex || '#f59e0b'
    return {
        backgroundColor: `${cor}20`,
        color: cor
    }
}

const calcularNumeroLinha = (index) => (paginaAtual.value - 1) * perPage.value + index + 1

const toggleFiltros = () => {
    filtrosVisiveis.value = !filtrosVisiveis.value
}

// Modais e Navegação
const abrirModalDetalhes = (item) => {
    captacaoSelecionada.value = item
    showModalDetalhes.value = true
}

const fecharModalDetalhes = () => {
    showModalDetalhes.value = false
    captacaoSelecionada.value = null
}

const validarDatas = () => {
    erros.value = { dataInicio: '', dataFim: '' }
    if (filtro.value.dataInicioInput && filtro.value.dataFimInput) {
        if (new Date(filtro.value.dataInicioInput) > new Date(filtro.value.dataFimInput)) {
            erros.value.dataInicio = 'A data de início não pode ser maior que a data de fim'
            erros.value.dataFim = 'A data de fim não pode ser menor que a data de início'
            return false
        }
    }
    return true
}

const aplicarFiltros = () => {
    if (!validarDatas()) return
    paginaAtual.value = 1

    // Atualiza também via Inertia caso o backend filtre por período/agência
    router.get(window.location.pathname, {
        search: filtro.value.search,
        agencia: filtro.value.agencia,
        estado: filtro.value.estado,
        fase_atribuicao: filtro.value.faseAtribuicao,
        data_inicio: filtro.value.dataInicioInput,
        data_fim: filtro.value.dataFimInput,
    }, {
        preserveState: true,
        replace: true
    })
}

const resetarFiltros = () => {
    filtro.value = {
        search: '',
        agencia: 'T',
        estado: 0,
        faseAtribuicao: 'TODOS',
        dataInicioInput: '',
        dataFimInput: '',
    }
    paginaAtual.value = 1

    router.get(window.location.pathname, {}, {
        preserveState: true,
        replace: true
    })
}

const mudarPagina = (novaPagina) => {
    paginaAtual.value = novaPagina
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

// Exportação Excel completa
const exportarParaExcel = () => {
    try {
        const dadosFormatados = captacoesFiltradas.value.map((item, index) => {
            const det = getDetalhePrincipal(item)
            return {
                '#': index + 1,
                'ID Captação': item.id,
                'Data Captação': formatarData(item.data_captacao),
                'Agência': getNomeAgencia(item.OfCodigoAgencia),
                'Cliente / Empresa': item.nome_cliente_empresa,
                'Género': item.genero,
                'Telefone': item.telefone || '-',
                'Tipo de Negócio': item.tipo_negocio?.descricao || item.id_tipo_negocio,
                'Local do Negócio': item.local_negocio,
                'Valor Solicitado (AKZ)': parseFloat(item.valor_solicitado) || 0,
                'Captador': item.captador?.UtNome || item.UtCodigoCaptador,
                'Gerente Atribuído': item.gerente?.UtNome || item.UtCodigoGerente || '-',
                'Data Atrib. Gerente': item.data_atribuicao_gerente || '-',
                'Supervisor Atribuído': item.supervisor?.UtNome || item.UtCodigoSupervisor || '-',
                'Data Atrib. Supervisor': item.data_atribuicao_supervisor || '-',
                'Oficial Crédito': item.oficial_credito?.UtNome || item.UtCodigoOficialCredito || '-',
                'Data Atrib. Oficial': item.data_atribuicao_oficial || '-',
                'Atribuído Por': item.atribuido_por || '-',
                'Estado': item.estado_resposta?.descricao || 'Pendente',
                'Nº BI (Detalhe)': det?.numero_bi || '-',
                'Idade (Detalhe)': det?.idade || '-',
                'Data Rejeição': det?.data_rejeicao || '-',
                'Razão Rejeição': det?.razao_rejeicao || '-',
                'Observações Oficial': det?.observacoes || item.ObservacaoEstado || '-'
            }
        })

        const ws = XLSX.utils.json_to_sheet(dadosFormatados)
        const wb = XLSX.utils.book_new()
        XLSX.utils.book_append_sheet(wb, ws, 'Monitoramento_Captacoes')
        XLSX.writeFile(wb, `monitoramento_captacoes_${new Date().toISOString().split('T')[0]}.xlsx`)
    } catch (error) {
        console.error('Erro ao exportar:', error)
        alert(`Erro ao exportar: ${error.message}`)
    }
}

watch(() => props.filters, (newFilters) => {
    if (!newFilters) return
    filtro.value = {
        search: newFilters.search || '',
        agencia: newFilters.agencia || 'T',
        estado: Number(newFilters.estado) || 0,
        faseAtribuicao: newFilters.fase_atribuicao || 'TODOS',
        dataInicioInput: newFilters.data_inicio || '',
        dataFimInput: newFilters.data_fim || '',
    }
}, { deep: true })
</script>

<style scoped>
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
.btn-outline-primary-pgr {
    @apply border border-purple-500 text-purple-600 hover:bg-purple-600 hover:text-white;
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
.btn-sm {
    @apply px-3 py-1 text-xs;
}
.card-hover {
    @apply transition-all duration-200 hover:shadow-md hover:-translate-y-0.5;
}
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>