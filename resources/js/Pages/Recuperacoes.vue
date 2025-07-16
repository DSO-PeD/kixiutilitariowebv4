<template>

    <Head title="Recuperações" />
    <div class="container mx-auto py-6 max-w-full">

 <!-- Modal com binding dos dados -->
  <ConfirmationModal
    :show="showDeleteModal"
    :comprovativoData="selectedRecuperacao"
    :isDeleting="isDeleting"
    @confirm="proceedWithDeletion"
    @cancel="cancelDeletion"
  />
        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>

                <h1 class="text-2xl font-bold text-green-950">
                    <i class="fas fa-hand-holding-usd text-5xl  text-green-950"></i>
                    &ThinSpace;&ThinSpace; Recuperações de Créditos
                </h1>
                <p class="text-sm text-gray-600">Registros de Pagamentos </p>
            </div>
        </div>
        <hr class="py-2" />

        <!-- Formulário de Recuperação -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6" v-if="$page.props.user.nova_recuperacao">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Nova Recuperação
                </h2>
                <button @click="openClientModal" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg> &ThinSpace;
                    Selecionar Cliente
                </button>
            </div>

            <form @submit.prevent="guardarRecuperacao" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fa-solid fa-receipt mr-1 text-gray-500"></i>
                            Voucher
                        </label>
                        <input v-model="formRecuperacao.txtVoucher" type="text" class="form-input bg-gray-100" readonly
                            required />
                        <span v-if="errors.txtVoucher" class="text-red-500">{{ errors.txtVoucher }}</span>

                        <input v-model="formRecuperacao.textBaseOperacao" type="hidden" />
                        <input v-model="formRecuperacao.textIdComprovativo" type="hidden" />
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-file-invoice-dollar"></i>
                            Loan
                        </label>
                        <input v-model="formRecuperacao.Loan" type="text" class="form-input bg-gray-100" readonly
                            required />
                        <span v-if="errors.Loan" class="text-red-500">{{ errors.Loan }}</span>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fa-solid fa-dollar-sign mr-1 text-gray-500"></i>
                            Valor Recuperado
                        </label>
                        <input v-model="formRecuperacao.txtMontante" type="text" class="form-input bg-yellow-50"
                            readonly required />
                        <span v-if="errors.txtMontante" class="text-red-500">{{ errors.txtVoucher }}</span>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fa-solid fa-calendar-check mr-1 text-gray-500"></i>
                            Data Borderoux
                        </label>
                        <input v-model="formRecuperacao.calDataBorderoux" type="text" class="form-input bg-gray-100"
                            readonly required />
                        <span v-if="errors.txtMontante" class="text-red-500">{{ errors.calDataBorderoux }}</span>
                        <input v-model="formRecuperacao.txtBaCodigo" type="hidden" />
                    </div>
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">

                            <i class="fas fa-balance-scale-left"></i>
                            Maturidade do Crédito
                        </label>
                        <select v-model="formRecuperacao.selectMaturidadeCredito" class="form-select" required>
                            <option value="" disabled selected>Selecione a Maturidade do Crédito</option>
                            <option v-for="maturidade in $page.props.listacomissoes_taxas" :key="maturidade.id"
                                :value="maturidade.id">
                                {{ maturidade.prazo_maturidade }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="border-l-4 border-orange-400 bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fa-solid fa-user-check mr-1 text-orange-500"></i>
                                Recuperador
                            </label>
                            <select v-model="formRecuperacao.selectRecuperador" class="form-select" required>
                                <option value="" disabled selected>Selecione o recuperador</option>
                                <option v-for="recuperador in $page.props.listar_recuperador" :key="recuperador.id"
                                    :value="recuperador.id">
                                    {{ recuperador.nome_recuperador }}
                                </option>
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fa-solid fa-calendar-check mr-1 text-orange-500"></i>
                                Data LPF
                            </label>
                            <input v-model="formRecuperacao.txtDataLPF" type="date" class="form-input" required />
                        </div>

                        <div class="flex items-end space-x-2">


                            <button type="submit" class="btn btn-primary2 flex-1 items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                                </svg>
                                &ThickSpace; Guardar
                            </button>

                            <button type="button" @click="limparCampos" class="btn btn-secondary flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                                </svg> &ThickSpace;

                                Limpar Campos
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Alertas -->
        <div v-if="$page.props.flash.success" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-200"
            role="alert">
            <i class="fa fa-info-circle"></i> {{ $page.props.flash.success }}
        </div>

        <div v-if="$page.props.flash.error" class="p-4 mb-4 text-sm text-green-800 font-meddium rounded-lg bg-green-200"
            role="alert">
            <i class="fa fa-info-circle"></i> {{ $page.props.flash.error }}
        </div>

        <!-- Tabela de Recuperações -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <!-- Cabeçalho do Card -->



            <!-- Filtro Avançado -->
            <div class="mb-6 bg-gray-50 p-3 sm:p-4 rounded-lg ">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3  ">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por Loan Number
                        </label>
                        <button class="btn btn-outline-consulta flex items-center gap-2" @click="showModalLoan = true">
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
                                    <span v-if="erros.dataInicio" class="text-red-500 text-xs">{{ erros.dataInicio
                                        }}</span>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por Estado
                        </label>



                        <select v-model="filtro.estado" class="input input-bordered w-full">
                            <option disabled :value="'s/e'">Escolha estado</option>
                            <option v-for="estado in $page.props.listar_estados" :value="Number(estado.id)"
                                :key="estado.id">
                                {{ estado.descricao_estado }}
                            </option>
                            <option :value="28">Todos estados</option>
                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1 truncate">Filtrar por Agência/Base
                        </label>
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



            </div>



            <!-- Paginação -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                <div class="text-sm text-gray-600">
                    Mostrando {{ (paginaAtual - 1) * perPage + 1 }} a {{ Math.min(paginaAtual * perPage, totalItens) }}
                    de {{ totalItens }} registros
                </div>
                <div class="text-sm text-green-500" v-if="$page.props.user.rec_exporta">
                    <a href="/exportar-recuperacao" preserve-state preserve-scroll
                        class="btn btn-outline-secondary px-4 text-blue-500 hover:text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                        </svg>

                        Exportar
                        Script</a>

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

                <div class="flex gap-4">
                    <div class="text-wrap">

                        <span class="bg-blue-50  text-blue-600 x-2 py-2 px-2 text-sm font-bold flex rounded-md">

                            <i class="fas fa-hand-holding-usd"></i> &ThinSpace;&ThinSpace;<span>Quantidade de
                                Recuperação: </span> &ThickSpace; {{
                                    formatCurrency(total) }}
                        </span>
                    </div>

                </div>

                <div class="flex gap-4">
                    <div class="text-wrap">

                        <span class="bg-yellow-50  text-green-600 x-2 py-2 px-2 text-sm font-bold flex rounded-md">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg> &ThinSpace;<span>Total de Montante: </span> &ThickSpace; {{
                                formatCurrency(montantetotal) }}
                        </span>
                    </div>

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

            <!--Tabela listagem recuperações-->
            <div class="overflow-x-auto -mx-4 sm:mx-0">

                <div class="flex justify-between items-center mb-4">
                    <button @click="confirmarSelecionados" :disabled="selectedRecuperacoes.length === 0" v-if="$page.props.user.rec_confirma"
                        class="btn btn-primary"
                        :class="{ 'opacity-50 cursor-not-allowed': selectedRecuperacoes.length === 0 }">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Confirmar Selecionados
                    </button>
                    <button @click="openPdfDialog" class="btn btn-outline-pdf flex items-center gap-2">
                        <i class="far fa-file-pdf"></i>
                        Gerar Relatório
                    </button>
                </div>


                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[50px]">
                                <input type="checkbox" v-model="selectAll" @change="toggleSelectAll"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" /> #
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
                            <!--  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>

                                    Por
                                </div>
                            </th>-->
                            <!-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">


                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z" />
                                    </svg>


                                    Agencia
                                </div>
                            </th>-->
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-yellow-100">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    LNR
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                    </svg>


                                    Cliente
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>

                                    Valor Recuperado
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    Voucher
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    Maturidade do Crédito

                                </div>
                            </th>
                            <!--th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 9.75h4.875a2.625 2.625 0 0 1 0 5.25H12M8.25 9.75 10.5 7.5M8.25 9.75 10.5 12m9-7.243V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
                                    </svg>

                                    Bordereau
                                </div>
                            </th-->
                            <!-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                    Data PGT
                                </div>
                            </th>-->
                            <!-- <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg> Data LPF
                                </div>
                            </th>-->
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-slate-100">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    Recuperador
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>

                                    Valor a Receber
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>

                                    Mês de Pagamento
                                </div>
                            </th>

                            <th class="px-4 py-3 text-center  text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
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
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>

                                </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="(rec, index) in recuperacoesPaginados" :key="rec.id" class="hover:bg-gray-50">

                            <td class="px-4 py-4 whitespace-normal text-sm text-gray-500">
                                <input type="checkbox" v-model="selectedRecuperacoes" :value="rec.id"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" /> {{
                                        calcularNumeroLinha(index) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(rec.CiFecha) }}
                            </td>
                            <!-- <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ rec.UtCodigo }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ rec.OfNombre }}</td>-->
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-yellow-100">{{
                                rec.ReBuDadoOrigem }}</td>
                            <td class="px-4 py-4 text-sm text-gray-500 bg-yellow-100">
                                {{ rec.infoadicional }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600 bg-yellow-50">{{
                                formatCurrency(rec.ReBuMontante) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 bg-yellow-50">{{ rec.voucher }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 bg-yellow-50">{{
                                rec.prazo_maturidade }}</td>

                            <!--<td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ rec.ReBuData }} </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ rec.ReBuDataLPF
                            }}</td>-->
                          <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 bg-slate-100">
                            <div class="flex items-center">
                                <!-- Imagem com fallback -->
                                <div class="flex-shrink-0 h-10 w-10 mr-3 ">
                                    <img
                                        :src="rec.Imagen ? `/imagens/imgsrecuperadores/${rec.Imagen}` : '/imagens/imgsrecuperadores/sem-foto.jpg'"
                                        :alt="rec.nome_recuperador || 'Sem foto'"
                                        class="foto-perfil h-10 w-10 rounded-full object-cover bg-slate-300"

                                    />
                                </div>
                                <span>{{ rec.nome_recuperador }}</span>
                            </div>
                        </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600 bg-slate-50">{{
                                formatCurrency(rec.valor_a_receber) }}</td>
                            <td v-if="rec.mes_ano_pagamento == 'Não definido'"
                                class="px-4 py-4 text-sm text-red-500 bg-slate-50">
                                {{ rec.mes_ano_pagamento }}
                            </td>
                            <td v-else class="px-4 py-4 text-sm text-gray-900 bg-slate-50">
                                {{ rec.mes_ano_pagamento }}
                            </td>


                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                <button @click="openDetails(rec)" class="btn btn-outline-secondary btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    Detalhes
                                </button>
                            </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                <span :class="rec.color" class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ rec.estado }}
                                </span>
                            </td>

                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                             <button
                            @click="initiateDeletion(rec)"
                            :disabled="!podeEliminar(rec)"
                            :class="{
                                'opacity-50 cursor-not-allowed': !podeEliminar(rec),
                                'text-red-600 hover:text-red-900': podeEliminar(rec),
                                'flex items-center gap-1': true
                            }"
                            title="Eliminar Recuperação"
                            >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>

                            </button>
                            </td>

                        </tr>
                        <tr v-if="recuperacoesPaginados.length === 0">
                            <td colspan="12" class="px-4 py-4 text-center text-sm text-gray-500">
                                Nenhum registro encontrado
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

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

        <!-- Modal para gerar PDF -->
        <Modal :show="showPdfDialog" @close="showPdfDialog = false" maxWidth="lg">
            <div class="bg-white p-6 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Gerar Relatório de Pagamento</h3>
                    <button @click="showPdfDialog = false" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recuperador -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Recuperador <span
                                class="text-red-500">*</span></label>
                        <select v-model="pdfFilters.recuperador" class="form-select" required>
                            <option value="">Selecione um recuperador</option>
                            <option value="TR">Todos</option>
                            <option v-for="rec in listar_recuperador" :key="rec.id" :value="rec.id">
                                {{ rec.nome_recuperador }}
                            </option>
                        </select>
                        <span v-if="errors.recuperador" class="text-red-500 text-xs">{{ errors.recuperador }}</span>
                    </div>

                    <!-- Agência -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Agência/Base <span
                                class="text-red-500">*</span></label>
                        <select v-model="pdfFilters.agencia" class="form-select" required>
                            <option value="">Selecione uma agência</option>
                            <option value="T">Todas</option>
                            <option v-for="ag in bases" :key="ag.OfIdentificador" :value="ag.OfIdentificador">
                                {{ ag.OfIdentificador }} - {{ ag.OfNombre }}
                            </option>
                        </select>
                        <span v-if="errors.agencia" class="text-red-500 text-xs">{{ errors.agencia }}</span>
                    </div>

                    <!-- Estado -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Estado <span
                                class="text-red-500">*</span></label>
                        <select v-model="pdfFilters.estado" class="form-select" required>
                            <option value="">Selecione um estado</option>
                            <option value="28">Todos</option>
                            <option v-for="est in listar_estados" :key="est.id" :value="est.id">
                                {{ est.descricao_estado }}
                            </option>
                        </select>
                        <span v-if="errors.estado" class="text-red-500 text-xs">{{ errors.estado }}</span>
                    </div>

                    <!-- Período -->
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Período <span
                                class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <input v-model="pdfFilters.dataInicio" type="date" class="form-input"
                                    placeholder="Data Início" required>
                                <span v-if="errors.dataInicio" class="text-red-500 text-xs">{{ errors.dataInicio
                                }}</span>
                            </div>
                            <div>
                                <input v-model="pdfFilters.dataFim" type="date" class="form-input"
                                    placeholder="Data Fim" required>
                                <span v-if="errors.dataFim" class="text-red-500 text-xs">{{ errors.dataFim }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="showPdfDialog = false" type="button" class="btn btn-secondary">
                        Cancelar
                    </button>
                    <button @click="gerarPdf" type="button" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5 mr-2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Gerar Relatório
                    </button>
                </div>
            </div>
        </Modal>


        <!-- Modal de Detalhes da Recuperação -->
        <Modal :show="showDetailsModal" @close="showDetailsModal = false" maxWidth="md">
            <div class="bg-white p-6 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Detalhes da Recuperação</h3>
                    <button @click="showDetailsModal = false" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-4" v-if="selectedRecuperacaoDetails">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Registado por</p>
                            <p class="text-sm text-gray-900">{{ selectedRecuperacaoDetails.UtCodigo }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Agência</p>
                            <p class="text-sm text-gray-900">{{ selectedRecuperacaoDetails.OfNombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nome do Recuperador</p>
                            <p class="text-sm text-gray-900">{{ selectedRecuperacaoDetails.nome_recuperador }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">LNR</p>
                            <p class="text-sm text-gray-900">{{ selectedRecuperacaoDetails.ReBuDadoOrigem }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Voucher</p>
                            <p class="text-sm text-gray-900">{{ selectedRecuperacaoDetails.voucher }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Montante</p>
                            <p class="text-sm text-gray-900">{{ formatCurrency(selectedRecuperacaoDetails.ReBuMontante)
                                }}</p>
                        </div>
                         <div>
                            <p class="text-sm font-medium text-gray-500">Data do Borderoux</p>
                            <p class="text-sm text-gray-900"> {{ formatApenasDate(selectedRecuperacaoDetails.ReBuData) }}</p>
                        </div>
                         <div>
                            <p class="text-sm font-medium text-gray-500">Data do LPF</p>
                            <p class="text-sm text-gray-900"> {{ formatApenasDate(selectedRecuperacaoDetails.ReBuDataLPF) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Comissão bruta</p>
                            <p class="text-sm text-gray-900">{{
                                formatCurrency(selectedRecuperacaoDetails.comissao_bruta) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Desconto IRT(6.5%)</p>
                            <p class="text-sm text-gray-900">{{ formatCurrency(selectedRecuperacaoDetails.desconto_IRT)
                                }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Valor a Receber</p>
                            <p class="text-sm text-gray-900">{{
                                formatCurrency(selectedRecuperacaoDetails.valor_a_receber) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Estado</p>
                            <p class="text-sm text-gray-900">{{ selectedRecuperacaoDetails.estado }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Observação</p>
                        <p class="text-sm text-gray-900">{{ selectedRecuperacaoDetails.obs }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="showDetailsModal = false" class="btn btn-secondary">
                        Fechar
                    </button>
                </div>
            </div>
        </Modal>


        <!-- Modal para seleção do mês de pagamento -->
        <Modal :show="showMonthDialog" @close="showMonthDialog = false" maxWidth="md">
            <div class="bg-white p-6 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar Recuperação</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estado <span
                            class="text-red-500">*</span></label>
                    <select v-model="selectedEstado" @change="handleEstadoChange" class="form-select" required>
                        <option value="" disabled selected>Selecione o estado</option>
                        <option v-for="est in estadosFiltrados" :key="est.id" :value="est.id">
                            {{ est.descricao_estado }}
                        </option>
                    </select>
                </div>

                <!-- Input para mês de pagamento (mostrado apenas quando estado é "Confirmado") -->
                <div class="mb-4" v-if="selectedEstado === 3">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mês/Ano de Pagamento <span
                            class="text-red-500">*</span></label>
                    <input type="month" v-model="selectedMonth"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        :min="minMonth" required />
                </div>

                <!-- Input para motivo (mostrado apenas quando estado é "Não confirmado") -->
                <div class="mb-4" v-if="selectedEstado === 17">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo <span
                            class="text-red-500">*</span></label>
                    <textarea v-model="motivoNaoConfirmado"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Digite o motivo da não confirmação" required></textarea>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button @click="showMonthDialog = false" type="button"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </button>
                    <button @click="enviarConfirmacao" type="button"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Finalizar
                    </button>
                </div>
            </div>
        </Modal>


        <ModalLoan :isOpen="showModalLoan" @close="showModalLoan = false" @search="buscarPorLoan"
            v-model="filtroLoan" />
        <!-- ... (modais mantidos iguais) ... -->
        <ModalDate :isOpen="showModalData" @close="showModalData = false" @search="buscarPorDatas"
            v-model:dataInicio="dataInicio" v-model:dataFim="dataFim" v-model:estadoModal="estadoModal"
            v-model:agenciaModal="agenciaModal" />
        <!-- Modal de Seleção de Cliente -->
        <Modal :show="showClientModal" @close="closeClientModal" maxWidth="3xl">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold">
                        <i class="fa-solid fa-users-viewfinder mr-2"></i>
                        Selecionar Cliente
                    </h3>
                    <button @click="closeClientModal" class="text-white hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>

                    </button>
                </div>
            </div>

            <div class="bg-gray-800 p-4">
                <div class="overflow-x-auto">
                    <div class="py-4">
                        <input v-model="nome_cliente" type="text" placeholder="pesquisar cliente"
                            class="flex justify-end bg-white border border-gray-300 text-gray-900 text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 px-3 py-1">
                    </div>
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-orange-400 uppercase tracking-wider">
                                    Loan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-orange-400 uppercase tracking-wider">
                                    Voucher</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-orange-400 uppercase tracking-wider">
                                    Cliente</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-orange-400 uppercase tracking-wider">
                                    Ação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                            <tr v-for="voucher in clientesFiltrados" :key="voucher.idComprovativo"
                                class="hover:bg-gray-700">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">{{ voucher.BuDadoOrigem }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">{{ voucher.BuReferencia }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-300">{{ voucher.infoadicional }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                    <button @click="selectClient(voucher)" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-file-import mr-1"></i>
                                        Selecionar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-gray-100 px-4 py-3 flex justify-end">
                <button @click="closeClientModal" class="btn btn-secondary">
                    <i class="fa-solid fa-times mr-2"></i>
                    Fechar
                </button>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, computed, watch } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { router, usePage } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue';
import * as XLSX from 'xlsx'
import ModalDate from './Layouts/components/ComprovativosComponents/ModalDate.vue'
import ModalLoan from './Layouts/components/ComprovativosComponents/ModalLoan.vue'
import Pagination from '@/Components/Pagination.vue'; // Componente de paginação
import ConfirmationModal from './Layouts/components/ComprovativosComponents/ConfirmationModal.vue'

// Props recebidas do backend
const props = defineProps({
    lista_recuperacoes: {
        type: Array,
        required: true
    },
    listar_recuperador: Array,
    listar_estados: {
        type: Array,
        required: false,
        default: () => []
    },
    listacomissoes_taxas: Array,
    BasesOperacao: Array,
    lista_agencias_consultas: Array,
    listar_voucher_para_recuperacao: Array,
    estatistica: Object,
    auth: Object,
    dataInicioInput: String,
    dataFimInput: String,
    montantetotal: Number,
    total: Number,
    errors: Object,
    session: Object,
    flash: Object,
    user: Object,
    bases: Array,
    filters: Object,
    page: Number,
    perPage: {
        type: Number,
        default: 100
    },
});

const showModalData = ref(false);
const showModalLoan = ref(false);
const showDetailsModal = ref(false);
const selectedRecuperacaoDetails = ref(null);
// Estados de controle
const errors = ref({})
const loading = ref(false)
const successMessage = ref('')
// Configuração da paginação
const perPage = ref(100);
const paginaAtual = ref(1);


const filtroLoan = ref('')

const estados = ref([]);
// Dados locais para paginação
const dadosLocais = ref([]);

const selectedRecuperacoes = ref([]);
const selectAll = ref(false);
const showMonthDialog = ref(false);
const selectedMonth = ref('');

const openDetails = (recuperacao) => {
    selectedRecuperacaoDetails.value = recuperacao;
    showDetailsModal.value = true;
};



const minMonth = computed(() => {
    const today = new Date();
    return `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}`;
});

// Função para alternar seleção de todos
const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedRecuperacoes.value = recuperacoesPaginados.value.map(rec => rec.id);
    } else {
        selectedRecuperacoes.value = [];
    }
};

// Função para abrir o diálogo de confirmação
const confirmarSelecionados = () => {
    if (selectedRecuperacoes.value.length === 0) return;

    // Resetar o mês selecionado
    const today = new Date();
    selectedMonth.value = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}`;

    showMonthDialog.value = true;
};

// Watch para atualizar dadosLocais quando lista_recuperacoes mudar
watch(() => props.lista_recuperacoes, (newVal) => {
    dadosLocais.value = newVal;
    paginaAtual.value = 1; // Resetar para primeira página
}, { immediate: true });

// Computed property para os dados paginados
const recuperacoesPaginados = computed(() => {
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

    search: props.filters?.search || '',
    lnr: props.filters?.lnr || '',
    estado: props.filters?.estado || 28,
    agencia: props.filters?.agencia || 'T',
    dataInicioInput: props.filters?.data_inicio || '',
    dataFimInput: props.filters?.data_fim || ''


})

const recuperacoesFiltrados = computed(() => {
    return props.recuperacoes // Agora usamos diretamente os recuperacoes recebidos do backend
})
const montanteTotalFiltrado = computed(() => {
    return props.montanteFiltrado || 0 // Usamos o valor calculado no backend
})

const calcularNumeroLinha = (index) => {
    return (paginaAtual.value - 1) * props.perPage + index + 1
}

// Função aplicarFiltros modificada
const aplicarFiltros = () => {
    if (!validarDatas()) return;

    router.get('/recuperacoes', {
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

    router.get('/recuperacoes', {
        page: 1
    }, {
        preserveState: true,
        replace: true
    });
};

const buscarPorLoan = () => {
    router.get('/recuperacoes', { tipo: 3, loan: filtroLoan.value }, { preserveState: true })
    showModalLoan.value = false
}


watch(() => [filtro.value.dataInicioInput, filtro.value.dataFimInput], ([newInicio, newFim]) => {
    if (newInicio && newFim && newInicio > newFim) {
        alert('A data de início não pode ser maior que a data de fim');
        filtro.value.dataInicioInput = '';
        filtro.value.dataFimInput = '';
    }
});



const listarEstados = async () => {
    const res = await fetch('/listarEstados');
    const json = await res.json();
    estados.value = json;
}

const agencias = ref([]);
const listarAgencias = async () => {
    const res = await fetch('/listarAgencias');
    const json = await res.json();
    agencias.value = json;
}



const buscarPorDatas = (params) => {
    router.get('/recuperacoes', {
        tipo: 1,
        data_inicio: params.data_inicio,
        data_fim: params.data_fim,
        estadoconsulta: params.estadoconsulta,
        agenciaconsulta: params.agenciaconsulta
    }, { preserveState: true })

    showModalData.value = false
}

const confirmarRecuperacao = (idRecuperacao) => {
    router.get('/confirmarRecuperacao',
        {
            id: idRecuperacao
        }, {
        preserveState: true,
        preserveScroll: true,
    });
}

// Estado do formulário
const formRecuperacao = reactive({
    txtVoucher: '',
    textBaseOperacao: '',
    textIdComprovativo: '',
    Loan: '',
    txtMontante: '',
    calDataBorderoux: '',
    txtBaCodigo: '',
    selectRecuperador: '',
    txtDataLPF: '',
});

//Filtrar cliente numa tabela de clientes para recuperação
const nome_cliente = ref('');
const clientesFiltrados = computed(() => {
    return props.listar_voucher_para_recuperacao.filter(voucher =>
        voucher.infoadicional.toLowerCase().includes(nome_cliente.value.toLowerCase())
    );
});

const estadosFiltrados = computed(() => {
    return props.listar_estados.filter(estado => estado.id === 3 || estado.id === 17);
});

// Função para submeter
function guardarRecuperacao() {
    loading.value = true
    successMessage.value = ''
    errors.value = {}

    Inertia.post('/guardar-recuperacao', formRecuperacao, {
        onSuccess: () => {
            successMessage.value = 'Dados guardados com sucesso!'
            loadRecuperacoes(); // Recarregar a lista após inserção
        },
        onError: (err) => {
            errors.value = err
        },
        onFinish: () => {
            loading.value = false
        }
    })
}


const selectedEstado = ref('');
const motivoNaoConfirmado = ref('');

const handleEstadoChange = () => {
    // Reset fields when estado changes
    selectedMonth.value = '';
    motivoNaoConfirmado.value = '';
    console.log('Estado selecionado:', selectedEstado.value);
};

// Função para enviar a confirmação
const enviarConfirmacao = () => {


    // Validate required fields based on estado
    if (!selectedEstado.value) {
        alert('Por favor, selecione o estado');
        return;
    }

    if (selectedEstado.value === 3 && !selectedMonth.value) {
        alert('Por favor, selecione o mês de pagamento');
        return;
    }

    if (selectedEstado.value === 17 && !motivoNaoConfirmado.value) {
        alert('Por favor, digite o motivo da não confirmação');
        return;
    }

    // Extrair ano e mês do valor selecionado
    //const [year, month] = selectedMonth.value.split('-');

    // Enviar para o controlador
    Inertia.post('/confirmar-recuperacoes', {
        ids: selectedRecuperacoes.value,
        id_estado: selectedEstado.value,
        mes_para_pagamento: selectedMonth.value,
        obs: selectedEstado.value === 17 ? motivoNaoConfirmado.value : null
    }, {
        onSuccess: () => {
            showMonthDialog.value = false;
            selectedRecuperacoes.value = []; // Limpar seleção
            selectedEstado.value = '';
            motivoNaoConfirmado.value = '';

            // Recarregar dados ou mostrar mensagem de sucesso
            loadRecuperacoes(); // Recarregar a lista após inserção
        },
        onError: (errors) => {
            alert('Ocorreu um erro ao confirmar as recuperações');
        }
    });
};

//Estados do componente
const showDeleteModal = ref(false);
const selectedRecuperacao = ref(null);
const showClientModal = ref(false);

// Funções auxiliares
const formatDate = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('pt-PT') + ' ' + d.toLocaleTimeString('pt-PT').slice(0, 5);
};
const formatApenasDate = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('pt-PT')
};

const formatMoney = (amount) => {
    return parseFloat(amount).toLocaleString('pt-PT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
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

// Funções de manipulação de modais
const openClientModal = () => showClientModal.value = true;
const closeClientModal = () => showClientModal.value = false;
const dataInicio = ref('')
const dataFim = ref('')
const estadoModal = ref(0)
const agenciaModal = ref('')
const error = ref(null);
const dataInicioInput = ref(props.dataInicioInput || '')
const dataFimInput = ref(props.dataFimInput || '')
const dateError = ref('')
const erros = ref({
    dataInicio: '',
    dataFim: ''
})

// Estados para o PDF
const showPdfDialog = ref(false);
const pdfFilters = reactive({
    recuperador: '',
    agencia: '',
    estado: '',
    dataInicio: '',
    dataFim: ''
});

// Método para abrir o diálogo do PDF
const openPdfDialog = () => {
    // Resetar filtros
    pdfFilters.recuperador = '';
    pdfFilters.agencia = '';
    pdfFilters.estado = '';
    pdfFilters.dataInicio = '';
    pdfFilters.dataFim = '';

    showPdfDialog.value = true;
};

// Método para gerar o PDF
const gerarPdf = () => {
    // Resetar erros
    errors.value = {};
    let hasErrors = false;

    // Validar datas
    if (pdfFilters.dataInicio && pdfFilters.dataFim && pdfFilters.dataInicio > pdfFilters.dataFim) {
        errors.value.dataInicio = "A data de início não pode ser maior que a data de fim";
        hasErrors = true;
    }

    // Validação dos campos obrigatórios
    if (!pdfFilters.recuperador) {
        errors.value.recuperador = "Por favor, selecione um recuperador";
        hasErrors = true;
    }
    if (!pdfFilters.agencia) {
        errors.value.agencia = "Por favor, selecione uma agência";
        hasErrors = true;
    }
    if (!pdfFilters.estado) {
        errors.value.estado = "Por favor, selecione um estado";
        hasErrors = true;
    }
    if (!pdfFilters.dataInicio) {
        errors.value.dataInicio = "Por favor, selecione a data de início";
        hasErrors = true;
    }
    if (!pdfFilters.dataFim) {
        errors.value.dataFim = "Por favor, selecione a data de fim";
        hasErrors = true;
    }

    // Se houver erros, não prossegue
    if (hasErrors) {
        return;
    }

    // Construir URL com os filtros
    const params = new URLSearchParams();
    params.append('recuperador', pdfFilters.recuperador);
    params.append('agencia', pdfFilters.agencia);
    params.append('estado', pdfFilters.estado);
    params.append('data_inicio', pdfFilters.dataInicio);
    params.append('data_fim', pdfFilters.dataFim);

    // Abrir em nova aba
    window.open(`/gerar-relatorio-pdf?${params.toString()}`, '_blank');

    // Fechar o modal
    showPdfDialog.value = false;
};


const podeEliminar = (rec) => {
  const isRegistadoHoje = rec.id_estado === 1 && rec.CiFecha === hoje.value
  const temPermissao = props.user.elimina_confirmado_exportado == 1

  return isRegistadoHoje || temPermissao
}

const initiateDeletion = (rec) => {

  if (!podeEliminar(rec)) return

  // Prepara os dados para exibir no modal
  selectedRecuperacao.value = {
    lnr: rec.ReBuDadoOrigem || 'N/A',
    cliente: rec.infoadicional || 'N/A',
    montante: rec.ReBuMontante || 0,
    data: rec.CiFecha || 'N/A',
    estado: rec.estado || 'N/A',
    id: rec.id,

  }

  showDeleteModal.value = true
}

const isDeleting = ref(false)

const proceedWithDeletion = async () => {
  isDeleting.value = true

  try {

    await router.post("/eliminar-recuperacao", {
      id: selectedRecuperacao.value.id,

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
  selectedRecuperacao.value = {
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


const confirmDelete = () => {
    if (!selectedRecuperacao.value) return;

    Inertia.delete(`/recuperacoes/${selectedRecuperacao.value.id}`, {
        onSuccess: () => {
            loadRecuperacoes(); // Recarregar a lista após exclusão
            showDeleteModal.value = false;
        },
        onError: () => {
            alert('Erro ao excluir recuperação');
        }
    });
};

// Função para selecionar um cliente
const selectClient = (client) => {
    try {
        console.log('Selecionando cliente:', client);
        formRecuperacao.txtVoucher = client.BuReferencia;
        formRecuperacao.textBaseOperacao = client.BaseOperacao || '';
        formRecuperacao.textIdComprovativo = client.idComprovativo;
        formRecuperacao.Loan = client.BuDadoOrigem;
        formRecuperacao.txtMontante = client.BuMontante || '';
        formRecuperacao.calDataBorderoux = client.BuData || '';
        formRecuperacao.txtBaCodigo = client.BaCodigo || '';
        closeClientModal();
    } catch (error) {
        console.error('Erro ao selecionar cliente:', error);
    }
};

const limparCampos = () => {
    formRecuperacao.txtVoucher = '';
    formRecuperacao.textBaseOperacao = '';
    formRecuperacao.textIdComprovativo = '';
    formRecuperacao.Loan = '';
    formRecuperacao.txtMontante = '';
    formRecuperacao.calDataBorderoux = '';
    formRecuperacao.txtBaCodigo = '';
    formRecuperacao.selectRecuperador = '';
    formRecuperacao.txtDataLPF = '';
    formRecuperacao.selectMaturidadeCredito = ''
};

onMounted(async () => {
    listarEstados();
    listarAgencias();
});

const handleImageError = (event) => {
    event.target.src = '/imagens/sem-foto.jpg'; // Imagem de fallback
    event.target.alt = 'Imagem não disponível';
};
// Exportar para Excel (mantido como está)
const exportarParaExcel = () => {
    if (!props.lista_recuperacoes?.length) {
        alert('Nenhum dado disponível para exportar');
        return;
    }

    const dadosFormatados = props.lista_recuperacoes.map((rec, index) => ({
        '#': index + 1,
        'Data': rec.CiFecha ? new Date(rec.CiFecha).toLocaleString('pt-PT') : '-',
        'Agência': rec.OfNombre || '-',
        'Loan Number': rec.ReBuDadoOrigem || '-',
        'Cliente': rec.infoadicional || '-',
        'Montante': rec.ReBuMontante || '0,00',
        'Estado': rec.estado || '-',
        'Recuperador': rec.nome_recuperador || '-'
    }));

    const ws = XLSX.utils.json_to_sheet(dadosFormatados);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Recuperacoes");
    XLSX.writeFile(wb, `recuperacoes_${new Date().toISOString().split('T')[0]}.xlsx`);
};
</script>

<style scoped>
/* ... (estilos mantidos iguais) ... */

/* Estilos adicionais para ordenação */
.sortable-header {
    @apply cursor-pointer hover:bg-gray-100 transition-colors;
}

.sort-icon {
    @apply ml-1;
}

.form-input,
.form-select {
    @apply border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors;
}

.btn {
    @apply px-4 py-2 rounded-md font-medium transition-colors flex items-center justify-center;
}

.btn-primary {
    @apply bg-gradient-to-r from-green-900 to-greenkixi-300 text-white hover:from-green-800 shadow-md hover:shadow-lg;
}

.btn-primary2 {
    @apply bg-gradient-to-r from-orange-900 to-greenkixi-300 text-white hover:from-green-800 shadow-md hover:shadow-lg;
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

.input {
    @apply border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 w-full;
}


.btn-secondary {
    @apply bg-gray-200 text-gray-800 hover:bg-gray-300;
}

.btn-danger {
    @apply bg-red-600 text-white hover:bg-red-700;
}

.btn-outline-consulta {
    @apply border border-gray-300 bg-white text-cyan-800 hover:bg-gray-50;
}

.btn-warning {
    @apply bg-yellow-500 text-white hover:bg-yellow-600;
}

.btn-sm {
    @apply px-3 py-1 text-sm;
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

/* Animações para o modal */
.modal-enter-active {
    animation: modal-fade 0.3s ease-out;
}

@keyframes modal-fade {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.btn-sm {
    @apply px-2 py-1 text-xs;
}

.btn-outline-secondary {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
}

.btn-outline-pdf {

    @apply border border-blue-300 bg-white text-blue-700 hover:bg-blue-50;
}
</style>
