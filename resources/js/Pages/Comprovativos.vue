<template>

    <Head title="Comprovativos" />

    <div class="container mx-auto py-6 max-w-full">
        <!-- Alertas -->
        <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
            {{ $page.props.flash.success }}
        </div>

        <div v-if="$page.props.flash.error" class="alert alert-danger mb-4">
            {{ $page.props.flash.error }}
        </div>

        <!-- Cabeçalho e Botão de Ação -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-green-950">Gestão dos Comprovativos</h1>
                <p class="text-sm text-gray-600">Pagamentos de Créditos e Poupanças</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <button class="btn btn-primary flex items-center gap-2" @click="abrirModalNovoComprovativo">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Novo Comprovativo
                </button>
            </div>



        </div>
        <hr class="py-4" />
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto"
            v-if="$page.props.user.view_pendentes && $page.props.totalPendente > 0">
            <div class="alert bg-red-50 border-l-4 border-red-500 text-red-700 p-4">
                <div class="flex items-start">
                    <svg class="flex-shrink-0 h-5 w-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>


                    <p class="text-sm">
                        Identificamos que os reembolsos abaixo <b>aplicados no Kixi Utilitário não foram aplicados no
                            LPF</b> ou existe uma possível
                        diferença nas informações como: <i class="text-orange-950">Loan Number, Voucher e Montante</i>.
                        &ThickSpace;Clique sobre o <span
                            class="badge bg-orange-100 text-orange-600 rounded-full px-3 py-1 text-xs font-medium">
                            Loan Number</span> apresentado
                        para mais detalhe do comprovativo.
                        <br />

                        <!-- Agrupamento para manter tudo na mesma linha -->
                        <span class="inline-flex flex-wrap items-center gap-1">
                            [
                            <span v-for="_pendente in pendentesVisiveis" :key="_pendente.Lnr"
                                class="relative inline-block mx-1">
                                <span @click="toggleDetails(_pendente.id)"
                                    class="badge bg-orange-100 text-orange-600 rounded-full px-3 py-1 text-xs font-bold cursor-pointer hover:bg-red-200 transition">
                                    {{ _pendente.Lnr }}
                                </span>

                                <!-- Tooltip Detalhes -->
                                <div v-if="activeDetails === _pendente.id"
                                    class="absolute z-10 left-0 mt-2 w-64 bg-white shadow-lg rounded-md border border-gray-200 p-3">
                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                        <span class="text-gray-500">Voucher:</span>
                                        <span class="font-medium">{{ _pendente.voucher }}</span>

                                        <span class="text-gray-500">Montante:</span>
                                        <span class="font-medium">{{ _pendente.montante }}</span>

                                        <span class="text-gray-500">Data do Comp.:</span>
                                        <span class="font-medium">{{ _pendente.budata }}</span>
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-100 text-right">
                                        <button @click.stop="closeDetails"
                                            class="text-xs text-red-600 hover:text-red-800">
                                            Fechar
                                        </button>
                                    </div>
                                </div>
                            </span>

                            <!-- Botão de ver mais/ver menos -->
                            <button @click="mostrarTodos = !mostrarTodos"
                                class="flex items-center gap-1 text-blue-600 hover:underline">
                                <!-- Transição de ícone -->
                                <transition name="fade-icon" mode="out-in">
                                    <!-- Ícone olho (ver mais) -->
                                    <svg v-if="!mostrarTodos" key="ver" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5
                                                12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0
                                                .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    <!-- Ícone olho cortado (ver menos) -->
                                    <svg v-else key="ocultar" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338
                                            7.244 19.5 12 19.5c.993 0 1.953-.138
                                            2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12
                                            4.5c4.756 0 8.773 3.162 10.065 7.498a10.522
                                            10.522 0 0 1-4.293 5.774M6.228 6.228 3
                                            3m3.228 3.228 3.65 3.65m7.894
                                            7.894L21 21m-3.228-3.228-3.65-3.65m0
                                            0a3 3 0 1 0-4.243-4.243m4.242
                                            4.242L9.88 9.88" />
                                    </svg>
                                </transition>

                                {{ mostrarTodos ? 'Ver menos' : 'Ver mais...' }}
                            </button>]


                        </span>
                    </p>
                    <!-- Botão de exportar para Excel -->
                    <button @click="exportToExcel"
                        class=" btn btn-outline-excelP  flex items-center gap-1 text-white px-3 py-1 rounded text-sm ml-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar Pendentes Para Excel
                    </button>


                    <br /><br />




                </div>

                <p class="mt-1 text-sm font-medium">
                    Por favor, regularize-os para evitar transtornos futuros. Atenção! é necessário que as informações,
                    como Loan Number, Voucher e Montante, estejam iguais no Kixi Utilitário e no LPF.
                </p>




            </div>
        </div>





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
                            <option :value="'TP'">Todas</option>
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
                        <option value="TL">Todos os produtos</option>
                        <option v-for="produto in produtos.filter(p => p.TipoProduto === 'L' || p.TipoProduto === 'G')"
                            :key="produto.Metodologia" :value="produto.Metodologia">
                            {{ produto.PoAgrupado }}
                        </option>
                    </select>

                    <select v-else-if="filtro.filtrarPoupancas && !filtro.filtrarPrestacoes"
                        v-model="filtro.produtoPoupanca"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 text-sm h-[42px] bg-white">
                        <option value="TS">Todos os produtos</option>
                        <option v-for="produto in produtos.filter(p => p.TipoProduto === 'S' || p.TipoProduto === 'G')"
                            :key="produto.Metodologia" :value="produto.Metodologia">
                            {{ produto.PoAgrupado }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- Botões de ação -->
            <div class="mt-4 flex justify-end space-x-2">
                <button @click="resetarFiltros" class="btn btn-outline-secondary flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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

        <!-- Checkboxes Row - Below the main filter row -->


        <!--<div class="mb-6 bg-gray-50 p-3 sm:p-4 rounded-lg">


            <div class="col-span-21 sm:col-span-1">

                <button class="btn btn-outline-consulta flex items-center gap-2 w-full justify-center"
                    @click="showModalLoan = true">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Loan
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-3">



                <div class="col-span-2 sm:col-span-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Período de Início*</label>
                            <div class="relative">
                                <input v-model="filtro.dataInicioInput" type="date" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 transition text-sm"
                                    :max="filtro.dataFimInput" @change="validarDatas" />
                                <span v-if="erros.dataInicio" class="text-red-500 text-xs">{{ erros.dataInicio }}</span>
                            </div>
                        </div>


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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por Tipo de
                        Pagamento</label>
                    <select v-model="filtro.formaPagamento"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 text-sm h-[42px]">
                        <option disabled :value="'s/p'">Selcionar a Forma</option>
                        <option v-for="formapgt in formaspagamentos" :value="formapgt.FormaPago"
                            :key="formapgt.FormaPago">
                            {{ formapgt.FormaPagoN }}
                        </option>
                        <option :value="'TP'">Todas</option>
                    </select>
                </div>


                <div class="col-span-2 sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar
                        Agência</label>
                    <select v-model="filtro.agencia"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 text-sm h-[42px]">
                        <option disabled :value="'s/a'">Escolha agência</option>
                        <option v-for="agencia in $page.props.bases" :value="agencia.OfIdentificador"
                            :key="agencia.OfIdentificador">
                            {{ agencia.OfIdentificador }} - {{ agencia.OfNombre }}
                        </option>
                        <option :value="'T'">Todas que tenho acesso</option>
                    </select>


                </div>


                <div class="col-span-2 sm:col-span-1">
                    <div class="flex flex-col md:flex-row md:items-end gap-2 h-full">

                        <div class="flex-1 ">
                            <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar
                                Estado</label>
                            <select v-model="filtro.estado" class="input input-bordered w-full text-sm py-2 h-[42px]">
                                <option disabled :value="'s/e'">Escolha </option>
                                <option v-for="estado in $page.props.estados" :value="Number(estado.id)"
                                    :key="estado.id">
                                    {{ estado.descricao_estado }}
                                </option>
                                <option :value="28">Todos </option>
                            </select>
                        </div>
                        &VeryThinSpace;


                        <div class="flex flex-col gap-2">


                            <div class="flex items-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="filtro.filtrarPrestacoes" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2
                                        peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full
                                        peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px]
                                        after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full
                                        after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600">
                                    </div>
                                    <span
                                        class="ml-2 text-sm text-gray-600 whitespace-nowrap font-semibold">Prestações(Capital+Juro)
                                    </span>
                                </label>
                            </div>


                            <div class="flex items-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" v-model="filtro.filtrarPoupancas" class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2
                                    peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full
                                    peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px]
                                    after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full
                                    after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600">
                                    </div>
                                    <span
                                        class="ml-2 text-sm text-gray-600 whitespace-nowrap font-semibold">Poupanças</span>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-span-2 sm:col-span-1">

                    <div v-if="filtro.filtrarPrestacoes && !filtro.filtrarPoupancas">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produto de Prestações</label>
                        <select v-model="filtro.produtoPrestacao"
                            class="input input-bordered w-full bg-white border border-gray-300 rounded-md  shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 h-[42px]">
                            <option value="TL">Todos os produtos</option>
                            <option
                                v-for="produto in produtos.filter(p => p.TipoProduto === 'L' || p.TipoProduto === 'G')"
                                :key="produto.Metodologia" :value="produto.Metodologia">
                                {{ produto.PoAgrupado }}
                            </option>
                        </select>


                    </div>


                    <div v-if="filtro.filtrarPoupancas && !filtro.filtrarPrestacoes">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produto de Poupanças</label>
                        <select v-model="filtro.produtoPoupanca"
                            class="input input-bordered w-full bg-white border border-gray-300 rounded-md  shadow-sm focus:border-green-500 focus:ring-green-500 text-sm py-2 h-[42px]">
                            <option value="TS">Todos os produtos</option>
                            <option
                                v-for="produto in produtos.filter(p => p.TipoProduto === 'S' || p.TipoProduto === 'G')"
                                :key="produto.Metodologia" :value="produto.Metodologia">
                                {{ produto.PoAgrupado }}
                            </option>
                        </select>


                    </div>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button @click="resetarFiltros" class="btn btn-outline-secondary mr-2">
                    Limpar Filtros
                </button>
                <button @click="aplicarFiltros" class="btn btn-primary">
                    Aplicar Filtros &MediumSpace;
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
            </div>
        </div>-->



        <div class="rounded-lg p-4 w-full bg-gray-100 border border-white shadow-sm ">
            <!-- Cabeçalho do período -->
            <div class="flex items-center justify-between  pb-4 border-b border-white">
                <div class="bg-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 border border-gray-200">
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                #
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
                                            d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />
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

                                    Voucher
                                </div>

                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                    </svg>

                                    OBS DCF
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
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(comprovativo, index) in comprovativosPaginados" :key="comprovativo.id"
                            class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ calcularNumeroLinha(index) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <a v-if="comprovativo.file" :href="`/storage/comprovativos/${comprovativo.file}`"
                                    target="_blank" class="text-blue-600 hover:underline flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 mr-1">
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
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ comprovativo.voucher || '-' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button @click="abrirModalObservacaoDCF(comprovativo)"
                                    class="btn btn-action btn-validate flex items-center gap-1 mx-auto"
                                    v-if="comprovativo.observacao && comprovativo.observacao.trim() !== ''"
                                    title="Ver observação" aria-label="Ver observação do comprovativo">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </button>
                                <span v-else class="text-gray-400 italic"> Nenhuma!</span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span :class="comprovativo.color" class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ comprovativo.estado }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="comprovativosPaginados.length === 0">
                            <td colspan="9" class="px-4 py-4 text-center text-sm text-gray-500">
                                Nenhum comprovativo encontrado com os filtros aplicados
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

    <!-- Modals (mantidos como estão) -->
    <ModalLoan :isOpen="showModalLoan" @close="showModalLoan = false" @search="buscarPorLoan" v-model="filtroLoan" />
    <ModalDate :isOpen="showModalData" @close="showModalData = false" @search="buscarPorDatas"
        v-model:dataInicio="dataInicio" v-model:dataFim="dataFim" />
    <ModalDelete v-if="showModalEliminar" @close="fecharModalEliminacao" @confirm="confirmarEliminacao"
        v-model:motivo="formEliminacao.txtMotivo" :dados="formEliminacao.txtDadosEliminado"
        :loan="formEliminacao.txtLoan" :id="formEliminacao.txtId" />
    <ModalNovoComprovativo ref="modalNovoComprovativoRef" v-if="showModalNovo" @close="fecharModalNovoComprovativo"
        @save="guardarComprovativo" :bases="$page.props.bases" :tipocomprovativos="$page.props.tipocomprovativos"
        :produtos="$page.props.produtos" :bancos="$page.props.bancos" :contas="$page.props.contas"
        :formaspagamentos="$page.props.formaspagamentos" v-model="novoComprovativo" />

    <ModalObservacaoDFC :show="showModalObservacao" @close="showModalObservacao = false"
        :comprovativoreconci="comprovativoSelecionado" />
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



const props = defineProps({
    comprovativos: Array,
    filters: Object,
    page: Number,
    hasMorePages: Boolean,
    perPage: {
        type: Number,
        default: 100
    },
    lista_comprovativo: {
        type: Array,
        required: true
    },

    total: Number,
    dataInicioInput: String,
    dataFimInput: String,
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
    lista_pendentes: Object,
    dataInicioPeriodo: String,
    dataFimPeriodo: String,
    produtosPrestacoes: Array,  // Add this prop for loan products
    produtosPoupancas: Array,   // Add this prop for savings products
    formaspagamentos: Array

})

// Estados
const showModalLoan = ref(false)
const showModalData = ref(false)
const showModalNovo = ref(false)
const showModalEliminar = ref(false)
//const totalItens = ref(props.total || 0)
const showModalObservacao = ref(false)
const comprovativoSelecionado = ref(null)


// Configuração da paginação
const perPage = ref(100);
const paginaAtual = ref(1);

// Dados locais para paginação
const dadosLocais = ref([]);
const showDialog = ref(false);
const activeDetails = ref(null);

const mostrarTodos = ref(false)
const limite = 10

const listaCompletaPendentes = props.lista_pendentes

const pendentesVisiveis = computed(() => {
    return mostrarTodos.value ? listaCompletaPendentes : listaCompletaPendentes.slice(0, limite)
})

const toggleDetails = (id) => {
    activeDetails.value = id
}

const closeDetails = () => {
    activeDetails.value = null
}





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
    formaPagamento: props.filters.formaPagamento || 'TP', // Ensure 'TP' is the default
    dataInicioInput: props.filters.data_inicio || '',
    dataFimInput: props.filters.data_fim || '',

    filtrarPrestacoes: props.filters.filtrar_prestacoes !== undefined
        ? Boolean(Number(props.filters.filtrar_prestacoes))
        : true,
    filtrarPoupancas: props.filters.filtrar_poupancas !== undefined
        ? Boolean(Number(props.filters.filtrar_poupancas))
        : true,

    produtoPrestacao: '',     // For loan products combobox
    produtoPoupanca: ''       // For savings products combobox
})

const filtroLoan = ref('')
const dataInicio = ref('')
const dataFim = ref('')
const dataInicioInput = ref(props.dataInicioInput || '')
const dataFimInput = ref(props.dataFimInput || '')
const dateError = ref('')
const erros = ref({
    dataInicio: '',
    dataFim: ''
})


const comprovativosFiltrados = computed(() => {
    return props.comprovativos // Agora usamos diretamente os comprovativos recebidos do backend
})
const montanteTotalFiltrado = computed(() => {
    return props.montanteFiltrado || 0 // Usamos o valor calculado no backend
})



const abrirModalObservacaoDCF = (comprovativo) => {
    comprovativoSelecionado.value = comprovativo
    showModalObservacao.value = true
}

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

    router.get('/comprovativos', {
        page: 1
    }, {
        preserveState: true,
        replace: true
    });
};

// Exportar para Excel (mantido como está)
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
                    'Loan Number': comprovativo.lnr || '-',
                    'Cliente': comprovativo.cliente || '-',
                    'Produto': comprovativo.metodologia || '-',
                    'Voucher': comprovativo.voucher || '-',
                    'Descrição da DCF': comprovativo.descricao || '-',
                    'Banco': comprovativo.banco || '-',
                    'Conta Bancaria': comprovativo.conta || '-',
                    'Observação da DCF': comprovativo.observacao || '-',
                    'Montante': comprovativo.montante || '0,00',
                    'Estado': comprovativo.estado || '-',

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
        const nomeArquivo = `comprovativos_DOP_completa_${dataHoje}.xlsx`;

        // Faz o download
        XLSX.writeFile(wb, nomeArquivo);

    } catch (error) {
        console.error('Erro detalhado ao exportar para Excel:', error);
        alert(`Erro ao exportar: ${error.message || 'Verifique o console para mais detalhes'}`);
    }
};

const exportToExcel = () => {


    try {

        // Verifica se há dados
        if (!listaCompletaPendentes || listaCompletaPendentes.length === 0) {
            alert('Nenhum dado disponível para exportar');
            return;
        }

        console.log('Total de registros a exportar:', listaCompletaPendentes.length);

        // Formata os dados
        const dadosFormatados = listaCompletaPendentes.map((comprovativo, index) => {
            try {
                return {
                    '#': index + 1,
                    'Data de Registo': comprovativo.CiFecha ? new Date(comprovativo.CiFecha).toLocaleString('pt-PT') : '-',
                    //'Agência': comprovativo.agencia || '-',
                    //'Registado Por': comprovativo.usuario || '-',
                    'Loan Number': comprovativo.Lnr || '-',
                    //'Cliente': comprovativo.cliente || '-',
                    //'Produto': comprovativo.metodologia || '-',
                    'Voucher': comprovativo.voucher === 'null' || comprovativo.voucher == null ? 'não registado' : comprovativo.voucher,
                    //'Descrição da DCF': comprovativo.descricao || '-',
                    //'Banco': comprovativo.banco || '-',
                    //'Conta Bancaria': comprovativo.conta || '-',
                    //'Observação da DCF': comprovativo.observacao || '-',
                    'Montante': comprovativo.montante || '0,00',
                    'Data do Comprovativo': comprovativo.budata ? new Date(comprovativo.budata).toLocaleString('pt-PT') : '-'
                    //'Estado': comprovativo.estado || '-',

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
        const nomeArquivo = `comprovativos_Pendentes_completa_${dataHoje}.xlsx`;

        // Faz o download
        XLSX.writeFile(wb, nomeArquivo);

    } catch (error) {
        console.error('Erro detalhado ao exportar para Excel:', error);
        alert(`Erro ao exportar: ${error.message || 'Verifique o console para mais detalhes'}`);
    }
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
    //txtVoucher: '',
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
        //txtVoucher: '',
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
const modalNovoComprovativoRef = ref(null);

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
        search: newFilters.search || '',
        lnr: newFilters.lnr || '',
        estado: newFilters.estado || 28,
        agencia: newFilters.agencia || 'T',
        dataInicioInput: newFilters.data_inicio || '',
        dataFimInput: newFilters.data_fim || ''
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



// Restante do código (modais, formulários) mantido como está



</script>

<style scoped>
.fade-icon-enter-active,
.fade-icon-leave-active {
    transition: all 0.2s ease;
}

.fade-icon-enter-from,
.fade-icon-leave-to {
    opacity: 0;
    transform: scale(0.8);
}

.fade-icon-enter-to,
.fade-icon-leave-from {
    opacity: 1;
    transform: scale(1);
}

/* Estilos melhorados */
.alert {
    @apply p-4 rounded-lg mb-4 border-l-4;
}

.alert-success {
    @apply bg-green-50 text-green-800 border-green-500;
}

.alert-danger {
    @apply bg-red-50 border-l-4 border-red-500 text-red-700 p-4;
}

.badge {
    display: inline-flex;
    align-items: center;
    transition: background-color 0.2s;
}

.btn {
    @apply px-4 py-2 rounded-md font-medium transition-colors flex items-center justify-center;
}

.btn-primary {
    @apply bg-gradient-to-r from-green-900 to-greenkixi-300 text-white hover:from-green-800 shadow-md hover:shadow-lg;
}

.btn-primarykxu {
    @apply bg-gradient-to-r from-orange-500 to-greenkixi-300 text-white hover:from-orange-800 shadow-md hover:shadow-lg;
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

.btn-outline-excelP {
    @apply border border-orange-950 bg-orange-100 text-orange-600 hover:bg-orange-50;
}

.btn-action {
    @apply px-3 py-1.5 rounded-md text-sm font-medium transition-colors;
}

.btn-validate {
    @apply bg-orange-50 text-orange-600 hover:bg-orange-300 border border-orange-200;
}

.input {
    @apply border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full;
}

/* Responsividade da tabela */
@media (max-width: 640px) {
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
        @apply block mb-4 border border-gray-200 rounded-lg;
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
.bg-yellow-100 {
    background-color: #fef9c3;
}

.text-yellow-800 {
    color: #92400e;
}

.bg-green-100 {
    background-color: #dcfce7;
}

.text-green-800 {
    color: #166534;
}

.bg-red-100 {
    background-color: #fee2e2;
}

.text-red-800 {
    color: #991b1b;
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

.btn-outline-consulta {
    @apply border border-gray-300 bg-white text-cyan-800 hover:bg-gray-50;
}
</style>
