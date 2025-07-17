<template>

    <Head title="Reconciliação" />

    <div class="container mx-auto py-6 max-w-full"><!-- Ajuste 17rem conforme o sidebar -->
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

                <h1 class="text-2xl font-bold text-green-950">
                    <i class="fas fa-exchange-alt text-5xl  text-green-950"></i>
                    &ThinSpace;&ThinSpace; Reconciliação de Comprovativos
                </h1>
                <p class="text-sm text-gray-600">Validação de pagamentos registrados</p>
            </div>
        </div>



        <hr />
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
                            {{ filtro.filtrarPrestacoes && !filtro.filtrarPoupancas ? 'Produtos de Prestações(Capital + Juros) ' : !filtro.filtrarPrestacoes && filtro.filtrarPoupancas ? 'Produto de Poupanças' :
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
                            {{ filtro.filtrarPrestacoes && !filtro.filtrarPoupancas ? 'Produtos de Prestações(Capital +   Juros) ' : !filtro.filtrarPrestacoes && filtro.filtrarPoupancas ? 'Produto de Poupanças' :
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
                     <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
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





        <div class="rounded-lg p-4 w-full bg-gray-100 border border-white shadow-sm ">
            <!-- Cabeçalho do período -->
            <div class="flex items-center justify-between  pb-4 border-b border-white">
                <div class="bg-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 ">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="font-medium text-gray-700">REEMBOLSOS DO PERÍODO:</span>
                    <span class="text-green-800 font-bold text-base">{{ dataFimPeriodo }} </span>
                    <span class="text-gray-600 font-normal">à</span>
                    <span class="text-green-800 font-bold text-base">{{ dataInicioPeriodo }}</span>
                </div>
            </div>

            <!-- Cards de métricas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-5">
                <!-- Card 1 - Total Montante Reembolsos -->
                <div
                    class="flex items-center p-4 bg-gradient-to-r from-green-50 to-yellow-50 rounded-lg border border-green-100 shadow-sm hover:shadow-md transition-all">
                    <div class="bg-green-100/80 p-2 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">TOTAL DE MONTANTE DE REEMBOLSOS (Principal+Juros)
                        </p>
                        <p class="text-xl font-bold text-green-700">{{ formatCurrency(montantetotal) }} AKZ</p>
                    </div>

                </div>


                <div
                    class="flex items-center p-4 bg-gradient-to-r from-cyan-50 to-green-50 rounded-lg border border-green-100 shadow-sm hover:shadow-md transition-all">
                    <div class="bg-cyan-100/80 p-2 rounded-full mr-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 text-cyan-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium">TOTAL DE MONTANTE DE POUPANÇAS</p>
                        <p class="text-xl font-bold text-cyan-700">{{ formatCurrency(totalMontantePoupanca) }} AKZ
                        </p>
                    </div>

                </div>

                <!-- Adicione mais cards conforme necessário -->
            </div>
        </div>

        <br />


        <!-- Card Principal -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">





            <!-- Paginação -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens)
                    }}
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
            <div class="overflow-x-auto w-full">
                <table class="w-full   divide-y divide-gray-200  table-auto">
                    <!-- lg:min-w-0 para desktop -->
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="w-12 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[10px]">
                                #
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    Arquivo
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider "
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
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="LNR">
                                <div class="flex  items-center gap-1">
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
                            <th class="w-28 px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
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
                            <th class="w-100 px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                data-label="Forma de Pagamento">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 9.75h4.875a2.625 2.625 0 0 1 0 5.25H12M8.25 9.75 10.5 7.5M8.25 9.75 10.5 12m9-7.243V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
                                    </svg>

                                    Forma de Pagamento
                                </div>
                            </th>
                            <th class="w-10 px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
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
                        <tr v-for="(comprovativo, index) in comprovativosPaginados" :key="comprovativo.id"
                            class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-normal text-sm text-gray-500">
                                {{ calcularNumeroLinha(index) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap items-center">
                                <a v-if="comprovativo.file" :href="`/storage/comprovativos/${comprovativo.file}`"
                                    target="_blank" class="text-blue-600 hover:underline flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 mr-1 text-red-500">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    Visualizar
                                </a>
                                <span v-else class="text-gray-400 italic">S/A</span>
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
                            <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">



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
                            <td class="px-4 py-4 whitespace-nowrap text-sm ">
                                {{ comprovativo.FormaPagoN }}
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
                        <tr v-if="comprovativosPaginados.length === 0">
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
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens)
                    }}
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
        default: 100
    },
    total: Number,
    montantetotal: Number,
    totalMontantePoupanca: Number,
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
    dataFimPeriodo: String
})

// Estados
const showModalLoan = ref(false)
const showModalData = ref(false)
const showModalEliminar = ref(false)
const showModalReconcialiacao = ref(false)
const comprovativoSelecionado = ref(null)

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
                    'Voucher': comprovativo.voucher || '-',
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

.input:not(:placeholder-shown) {
    @apply bg-gray-50 border-green-200;
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
