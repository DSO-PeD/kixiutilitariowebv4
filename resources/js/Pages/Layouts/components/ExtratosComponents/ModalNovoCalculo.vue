<template>
    <Modal :show="show" max-width="5xl" @close="$emit('close')">
        <!-- Cabeçalho -->
        <div class="bg-gradient-to-r from-green-900 to-greenkixi-300   to-green-950 text-white p-5 rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <span class="drop-shadow-md">&ThinSpace; Novo Cálculo de Desembolso</span>
                </h2>
                <button @click="$emit('close')" class="text-white/80 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                </button>
            </div>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(100vh-150px)]">


            <form @submit.prevent="submitForm" class="space-y-6">

                <!-- Seção 1: Dados Básicos - Card melhorado -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-5">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>

                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Dados do Empréstimo
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Campo Base/Loan Number melhorado -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Base / Loan Number <span
                                    class="text-red-500">*</span></label>
                            <div class="flex items-center space-x-2">
                                <select v-model="internalForm.selectBase" class="form-select flex-auto" required>
                                    <option disabled value="">-Base-</option>
                                    <option v-for="base in bases" :key="base.OfIdentificador" :value="base.OfCodigo">
                                        {{ base.OfIdentificador }}
                                    </option>
                                </select>
                                <span class="text-gray-400">/</span>
                                <input v-model="internalForm.txtNumeroLoan" type="text"
                                    @input="internalForm.txtNumeroLoan = $event.target.value.replace(/[^0-9]/g, '')"
                                    class="form-input w-24 text-center" maxlength="5" placeholder="00000" minlength="5"
                                    required />
                            </div>
                        </div>

                        <!-- Demais campos com melhor espaçamento -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Nome do Cliente <span
                                    class="text-red-500">*</span></label>
                            <input v-model="internalForm.txtNomeCliente" class="form-input"
                                placeholder="Nome completo do cliente" required />
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Produto <span
                                    class="text-red-500">*</span></label>
                            <select v-model="internalForm.selectProduto" class="form-select" required>
                                <option disabled value="">Selecione o produto</option>
                                <option v-for="produto in produtosext" :key="produto.PoAgrupado"
                                    :value="produto.PoAgrupado">
                                    {{ produto.PoAgrupado }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Oficial de Crédito <span
                                    class="text-red-500">*</span></label>
                            <input v-model="internalForm.txtOficialCredito" type="text" class="form-input"
                                placeholder="Nome do oficial" required />
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Referência de Pagamento</label>
                            <input v-model="internalForm.txtRefPagamento" class="form-input bg-gray-50"
                                placeholder="000000000" readonly />
                        </div>
                    </div>
                </div>


                <!-- Seção 2: Informações do Cliente - Card melhorado -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-5">
                        <div class="bg-purple-100 p-2 rounded-lg mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>

                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Informações do Cliente
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Campos com melhor organização -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Pessoa Politicamente Exposta (PPE)
                                <span class="text-red-500">*</span></label>
                            <select v-model="internalForm.cbPPE" class="form-select" required>
                                <option disabled value="">Escolha...</option>
                                <option>Sim</option>
                                <option>Não</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Renda Mensal <span
                                    class="text-red-500">*</span></label>
                            <select v-model="internalForm.cbRendaMensal" class="form-select" required>
                                <option disabled value="">Escolha...</option>
                                <option v-for="item in rendaMensalOptions" :key="item" :value="item">
                                    {{ item }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Setor Econômico <span
                                    class="text-red-500">*</span></label>
                            <select v-model="internalForm.cbSector" class="form-select" required>
                                <option disabled value="">Escolha...</option>
                                <option v-for="item in sectorOptions" :key="item" :value="item">
                                    {{ item }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Magnitude <span
                                    class="text-red-500">*</span></label>
                            <select v-model="internalForm.cbMagnitude" class="form-select" required>
                                <option disabled value="">Escolha...</option>
                                <option v-for="item in magnitudeOptions" :key="item" :value="item">
                                    {{ item }}
                                </option>
                            </select>
                        </div>

                        <!-- Campos de largura dupla -->
                        <div class="md:col-span-2 space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Atividade Econômica (CAE) <span
                                    class="text-red-500">*</span></label>
                            <select v-model="internalForm.cbAE" class="form-select" required>
                                <option disabled value="">Escolha...</option>
                                <option v-for="ae in atividades" :key="ae.Codigo" :value="ae.Codigo">
                                    {{ ae.caeDesignacao }} ({{ ae.Codigo }})
                                </option>
                            </select>
                        </div>

                        <div class="md:col-span-2 space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Descrição da Atividade Econômica
                                <span class="text-red-500">*</span></label>
                            <textarea v-model="internalForm.txtDecricaoAE" rows="3" class="form-input"
                                placeholder="Descreva a atividade econômica..." required></textarea>
                        </div>
                    </div>
                </div>


                <!-- Seção 3: Necessidades Especiais - Card melhorado -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-5">
                        <div class="bg-orange-100 p-2 rounded-lg mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                            </svg>

                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Necessidades Especiais
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Cliente com Necessidades Especiais?
                                <span class="text-red-500">*</span></label>
                            <div class="flex space-x-4 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" value="Sim" v-model="internalForm.CNE"
                                        class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Sim</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" value="Nao" v-model="internalForm.CNE"
                                        class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-gray-700">Não</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="internalForm.CNE === 'Sim'" class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Tipo de Necessidade</label>
                            <select v-model="internalForm.nes" class="form-select">
                                <optgroup v-for="grupo in nesGrupos" :label="grupo.descricaones"
                                    :key="grupo.codigrupones">
                                    <option v-for="tipo in nesTipos.filter(t => t.grupones === grupo.codigrupones)"
                                        :key="tipo.codigotipones" :value="tipo.codigotipones">
                                        {{ tipo.descricaotipones }}
                                    </option>
                                </optgroup>
                            </select>
                        </div>

                        <div v-if="internalForm.CNE === 'Sim'" class="md:col-span-2 space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Descrição da Necessidade</label>
                            <input v-model="internalForm.txtDesc" class="form-input"
                                placeholder="Descreva a necessidade especial..." />
                        </div>
                    </div>
                </div>

                <!-- Seção 4: Valores Financeiros - Card melhorado -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-5">
                        <div class="bg-green-600 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Valores Financeiros
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Campo de valor com ícone de moeda -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Valor no Contrato <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-gray-500">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtValorCreditoNoContrato" type="text"
                                    class="form-input pl-14" @input="onInput" name="txtValorCreditoNoContrato"
                                    required />
                            </div>
                        </div>

                        <!-- Grid aninhado para colaterais -->
                        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Colateral Depositado/Activo(S08)
                                    (%) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input v-model="internalForm.PecentCPD" type="text" class="form-input pr-10"
                                        @input="calcularColateralDepositado" required />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Valor Colateral
                                    Depositado</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <!--span class="text-gray-500">AKZ</span-->
                                    </div>
                                    <input v-model="internalForm.txtValorColateralDepositado" type="text"
                                        class="form-input pl-14 bg-gray-50" readonly />
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Colateral Deduzido/Pasivo(S06)
                                    (%) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input v-model="internalForm.txtPecentCDD" type="text" class="form-input pr-10"
                                        @input="calcularColateralDeduzido" required />
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Valor Colateral Deduzido</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <!--span class="text-gray-500">AKZ</span-->
                                    </div>
                                    <input v-model="internalForm.txtValorColateralDeduzido" type="text" @input="onInput"
                                        name="txtValorColateralDeduzido" class="form-input pl-14 bg-gray-50" readonly />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Seção 5: Taxa de Processamento - Card melhorado -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-5">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-percentage text-blue-600 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Taxa de Processamento - Pós-Antecipado
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Campos de taxa com melhor organização -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Taxa de Processamento (%) <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <input v-model="internalForm.txtPecentTP" type="text" class="form-input pr-10"
                                    @input="calcularTaxaDeProcessamento" required />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Valor TP</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-gray-500">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtValorTP" type="text" class="form-input pl-14 bg-gray-50"
                                    readonly />
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">IVA TP</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-gray-500">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtValorIVATP" type="text"
                                    class="form-input pl-14 bg-gray-50" readonly />
                            </div>
                        </div>
                    </div>

                    <hr class="my-6 border-gray-200" />

                    <div class="flex items-center mb-5">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-percentage text-blue-600 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Taxa de Processamento - Antecipado
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Taxa de Processamento (%) <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <input v-model="internalForm.txtPecentTPAnte" type="text" class="form-input pr-10"
                                    @input="calcularTaxaDeProcessamentoAnte" required />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Valor TP</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-gray-500">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtValorTPAnte" type="text"
                                    class="form-input pl-14 bg-gray-50" readonly />
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">IVA TP</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-gray-500">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtValorIVATPAnte" type="text"
                                    class="form-input pl-14 bg-gray-50" readonly />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Banco Borderoux (TP) <span
                                    class="text-red-500">*</span></label>
                            <select v-model="internalForm.selectBancoBorderoux_TP" class="form-select" required>
                                <option disabled value="">Escolha...</option>
                                <option v-for="banco in bancos" :key="banco.BaCodigo" :value="banco.BaCodigo">
                                    {{ banco.BaSigla }} - {{ banco.BaNome }}
                                </option>
                            </select>



                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Conta Bancária (TP) <span
                                    class="text-red-500">*</span></label>
                            <select v-model="internalForm.selectContaBancariaBorderoux_TP" class="form-select" required>
                                <option disabled value="">Escolha...</option>
                                <option v-for="conta in contasFiltradasTP" :key="conta.codigoConta"
                                    :value="conta.codigoConta">
                                    {{ conta.ContaBacaria }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Montante Borderoux</label>
                            <input v-model="internalForm.txtMontanteBorderoux_TP" type="text" @input="onInput"
                                name="txtMontanteBorderoux_TP" class="form-input" />
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Data Borderoux</label>
                            <input v-model="internalForm.dataBorderoux_TP" type="date" class="form-input" />
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Código do Borderoux</label>
                            <input v-model="internalForm.txtVoucherBorderou_TP" type="text" class="form-input" />
                        </div>
                    </div>
                </div>

                <!-- Seção 6: Taxa de Imprevisto - Card melhorado -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center mb-5">
                        <div class="bg-yellow-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Taxa de Imprevisto
                        </h3>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Imprevisto <span
                                class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" value="PosAntecipado" v-model="internalForm.TIPO_TI" name="TIPO_TI"
                                    class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">Pós-Antecipado</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" value="Antecipado" v-model="internalForm.TIPO_TI"
                                    class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-gray-700">Antecipado</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Taxa de Imprevisto (%) <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <input v-model="internalForm.txtPecentTI" type="text" class="form-input pr-10"
                                    @input="calcularTaxaDeImprevisto" required />
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">%</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Valor TI</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-gray-500">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtValorTI" type="text" class="form-input pl-14 bg-gray-50"
                                    readonly />
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">IVA TI</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-gray-500">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtValorIVATI" type="text"
                                    class="form-input pl-14 bg-gray-50" readonly />
                            </div>
                        </div>
                    </div>

                    <div v-if="internalForm.TIPO_TI === 'Antecipado'">
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Banco Borderoux (TI)</label>
                                <select v-model="internalForm.selectBancoBorderoux_TI" class="form-select">
                                    <option disabled value="">Escolha...</option>
                                    <option v-for="banco in bancos" :key="banco.BaCodigo" :value="banco.BaCodigo">
                                        {{ banco.BaSigla }} - {{ banco.BaNome }}
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Conta Bancária (TI)</label>
                                <select v-model="internalForm.selectContaBancariaBorderoux_TI" class="form-select">
                                    <option disabled value="">Escolha...</option>
                                    <option v-for="conta in contasFiltradasTI" :key="conta.codigoConta"
                                        :value="conta.codigoConta">
                                        {{ conta.ContaBacaria }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Montante Borderoux TI</label>
                                <input v-model="internalForm.txtMontanteBorderoux_TI" type="text" @input="onInput"
                                    name="txtMontanteBorderoux_TI" class="form-input" />
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Data Borderoux TI</label>
                                <input v-model="internalForm.dataBorderoux_TI" type="date" class="form-input" />
                            </div>

                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-gray-700">Código do Borderoux</label>
                                <input v-model="internalForm.txtVoucherBorderou_TI" type="text" class="form-input" />
                            </div>
                        </div>
                    </div>
                </div>





                <!-- Seção 7: Totais - Card destacado -->
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-xl shadow-sm border border-blue-200">
                    <div class="flex items-center mb-5">
                        <div class="bg-blue-200 p-2 rounded-lg mr-3">
                            <i class="fas fa-calculator text-blue-700 text-lg"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-blue-800">
                            Totais
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-blue-700">Valor no Contrato</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-green-600">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtFinalValorCreditoNoContrato" @input="onInput"
                                    class="form-input pl-14 text-blue-800 font-bold bg-blue-50 border-blue-200"
                                    name="txtFinalValorCreditoNoContrato" readonly />
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-green-700">Total a Receber</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <!--span class="text-green-600">AKZ</span-->
                                </div>
                                <input v-model="internalForm.txtFinalValorAReceber"
                                    class="form-input pl-14 text-green-800 font-bold bg-green-50 border-green-200"
                                    readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rodapé do Modal -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" class="btn btn-secondary" @click="$emit('close')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        &ThickSpace;Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">



                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6" v-if="!isSubmitting">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                        </svg>



                        &ThickSpace;

                        <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24" v-else>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z" />
                        </svg>



                        &ThickSpace;


                        {{ isSubmitting ? 'Processando...' : 'Guardar Cálculo' }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>

<script setup>
import { watch, ref, onMounted, computed } from 'vue';
import Modal from '@/Components/Modal.vue'

// Adicione esta função para verificação
const validateFormData = (data) => {
    const requiredFields = [
        'selectBase', 'txtNumeroLoan', 'txtNomeCliente',
        'selectProduto', 'txtOficialCredito'
    ];

    const missingFields = requiredFields.filter(field => !data[field]);

    if (missingFields.length > 0) {
        throw new Error(`Campos obrigatórios faltando: ${missingFields.join(', ')}`);
    }

    // Verifique valores numéricos
    if (isNaN(parseCurrency(data.txtValorCreditoNoContrato))) {
        throw new Error('Valor do crédito inválido');
    }
};

const isSubmitting = ref(false);




// No método submitForm do modal:
const submitForm = async () => {
    isSubmitting.value = true;
    try {
        // Criar cópia dos dados do formulário
        const formData = { ...internalForm.value };

        // Converter valores formatados para números
        const monetaryFields = [
            'txtValorCreditoNoContrato',
            'txtValorColateralDepositado',
            'txtValorColateralDeduzido',
            'txtValorTP',
            'txtValorIVATP',
            'txtValorTI',
            'txtValorIVATI',
            'txtFinalValorCreditoNoContrato',
            'txtFinalValorAReceber'
        ];

        monetaryFields.forEach(field => {
            if (formData[field]) {
                formData[field] = parseCurrency(formData[field]);
            }
        });


        await emit('submit', formData);// Emite para o componente pai
    } catch (error) {
        console.error('Erro ao preparar dados:', error);
    } finally {
        isSubmitting.value = false;
    }
}

const props = defineProps({
    show: Boolean,
    form: {
        type: Object,
        required: true,
        default: () => ({})
    },
    bases: {
        type: Array,
        default: () => []
    },
    produtosext: {
        type: Array,
        default: () => []
    },
    bancos: {
        type: Array,
        default: () => []
    },
    contas: {
        type: Array,
        default: () => []
    },
    atividades: {
        type: Array,
        default: () => []
    },
    nesGrupos: {
        type: Array,
        default: () => []
    },
    nesTipos: {
        type: Array,
        default: () => []
    }
});

const emit = defineEmits(['close', 'submit', 'update:form']);

// 1. Use apenas internalForm (remova o internalForm comentado)
const internalForm = ref({
    ...props.form,
    txtRefPagamento: props.form.selectBase && props.form.txtNumeroLoan
        ? `${props.form.selectBase}/${props.form.txtNumeroLoan}`
        : '',
    TIPO_TI: 'PosAntecipado',
    txtValorCreditoNoContrato: '',
    txtValorTI: '',
    txtValorIVATI: '',
    txtValorTP: '',
    txtValorIVATP: '',
    txtValorColateralDeduzido: '',
    txtValorColateralDepositado: '',
    txtFinalValorCreditoNoContrato: '',
    txtFinalValorAReceber: '',
    // Informações adicionais
    cbPPE: '',
    cbRendaMensal: '',
    cbSector: '',
    cbMagnitude: '',
    cbAE: '',
    txtDecricaoAE: '',
    CNE: 'Sim',
    nes: '',
    txtDesc: ''
});

onMounted(() => {
    internalForm.value.TIPO_TI = 'PosAntecipado';
});

// 2. Função para atualizar referência


const updateReference = () => {
    const base = internalForm.value.selectBase;
    const loan = internalForm.value.txtNumeroLoan;
    const quantidadeDigitos = String(base).length;
    if (quantidadeDigitos < 2) {
        internalForm.value.txtRefPagamento = base && loan ? `000${base}${loan}` : '';
    } else {
        internalForm.value.txtRefPagamento = base && loan ? `00${base}${loan}` : '';
    }


    emit('update:form', internalForm.value);
};

const contasFiltradasTP = computed(() => {

    if (internalForm.value.selectBancoBorderoux_TP === null || internalForm.value.selectBancoBorderoux_TP === undefined) return [];

    return props.contas.filter(conta => conta.BaCodigo === internalForm.value.selectBancoBorderoux_TP
    );
});

const contasFiltradasTI = computed(() => {

    if (internalForm.value.selectBancoBorderoux_TI === null || internalForm.value.selectBancoBorderoux_TI === undefined) return [];

    return props.contas.filter(conta =>
        conta.BaCodigo === internalForm.value.selectBancoBorderoux_TI
    );
});

//  watchers para limpar a conta quando o banco mudar
watch(() => internalForm.value.selectBancoBorderoux_TP, (newVal, oldVal) => {
    if (newVal !== oldVal) {
        internalForm.value.selectContaBancariaBorderoux_TP = '';
    }
});

watch(() => internalForm.value.selectBancoBorderoux_TI, (newVal, oldVal) => {
    if (newVal !== oldVal) {
        internalForm.value.selectContaBancariaBorderoux_TI = '';
    }
});

// 3. Watch para monitorar mudanças
watch(

    () => [internalForm.value.selectBase, internalForm.value.txtNumeroLoan],
    () => {
        updateReference();
    },
    { immediate: true, deep: true }
);
watch(() => internalForm.value.TIPO_TI, () => {
    calcularTaxaDeImprevisto();
});




// Opções para os selects
const rendaMensalOptions = [
    '01.( > 50K - 124K )akz',
    '02.( > 125K - 199K )akz',
    '03.( > 199K - 298K )akz',
    '04.( > 299K - 398K )akz',
    '05.( > 399K - 498K )akz',
    '06.( > 499K - 597K )akz',
    '07.( > 598K - 697K )akz',
    '08.( > 698K - 796K )akz',
    '09.( > 797K - 896K )akz',
    '10.( > 897K - 995K )akz',
    '11.( > 996K - 1.194K )akz',
    '12.( > 1.195K - 1.393K )akz',
    '13.( > 1.394K - 1.592K )akz',
    '14.( > 1.593K - 1.790K )akz',
    '15.( > 1.791K - 1.989K )akz',
    '16.( > 1.990K - 2.188K )akz',
    '17.( > 2.189K - 2.386K )akz',
    '18.( > 2.387K - 2.585K )akz',
    '19.( > 2.586K - 2.784K )akz',
    '20.( > 2.785K - 2.982K )akz'
]

const sectorOptions = [
    '1.Privado - Indústria',
    '2.Privado - Comércio',
    '3.Privado - Serviços',
    '4.Público - Central',
    '5.Público - Local',
    '6.ONG Nacional',
    '7.ONG Internacional'
]

const magnitudeOptions = [
    '0. Não Comercio',
    '1. Retalhista',
    '2. Retalhista/Grossista',
    '3. Grossista'
]


// FORMANTANDO VALORES
function formatCurrency(value) {
    if (value == null) return '';

    if (typeof value === 'string') {
        // Remove tudo que não for número
        value = value.replace(/\D/g, '');
        if (!value) return '0,00';
        value = parseFloat(value) / 100;
    }

    return value.toLocaleString('pt-PT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
function onInput(event) {
    const campo = event.target.name; // ou outro identificador que você usa
    let valor = event.target.value;

    valor = formatCurrency(valor);

    internalForm.value = {
        ...internalForm.value,
        [campo]: valor
    };
}
function parseCurrency(value) {
    if (!value) return 0;

    // Remove tudo que não for número
    const numericString = value.replace(/\D/g, '');

    if (!numericString) return 0;

    // Converte e ajusta para centavos
    return parseFloat(numericString) / 100;
}

const calcularColateralDepositado = () => {
    const valor = parseCurrency(internalForm.value.txtValorCreditoNoContrato);
    const percent = internalForm.value.PecentCPD;

    const valorCalculado = (valor * (percent / 100));
    const valorCa = formatCurrency(valorCalculado);
    internalForm.value = {
        ...internalForm.value,
        txtValorColateralDepositado: valorCa
    }

    atualizarTotais()


};

const calcularColateralDeduzido = () => {
    const valor = parseCurrency(internalForm.value.txtValorCreditoNoContrato) || 0;
    const percent = internalForm.value.txtPecentCDD || 0;
    const valorCalculado = (valor * (percent / 100));
    const valorCa = formatCurrency(valorCalculado);



    internalForm.value = {
        ...internalForm.value,
        txtValorColateralDeduzido: valorCa
    }

    atualizarTotais()
};

const calcularTaxaDeProcessamento = () => {

    const valor = parseCurrency(internalForm.value.txtValorCreditoNoContrato) || 0
    const percent = internalForm.value.txtPecentTP || 0

    const tp = valor * (percent / 100)


    const iva = tp * 0.14

    const frmIva = formatCurrency(iva);
    const frmTp = formatCurrency(tp);

    internalForm.value = {
        ...internalForm.value,
        txtValorTP: frmTp,
        txtValorIVATP: frmIva
    }

    atualizarTotais()
};

function calcularTaxaDeProcessamentoAnte() {
    var defaultValor = 350000;


    const valor = parseCurrency(internalForm.value.txtValorCreditoNoContrato) || 0
    const percent = internalForm.value.txtPecentTPAnte || 0

    const tp = valor * (percent / 100)


    const iva = tp * 0.14

    const frmIva = formatCurrency(iva);
    const frmTp = formatCurrency(tp);

    internalForm.value = {
        ...internalForm.value,
        txtValorTPAnte: frmTp,
        txtValorIVATPAnte: frmIva
    }

    atualizarTotais()




}

const calcularTaxaDeImprevisto = () => {


    const PercentTI = parseFloat(internalForm.value.txtPecentTI || 0) / 100;
    const PercentIVATI = 14 / 100;

    let valorCredito = parseCurrency(internalForm.value.txtValorCreditoNoContrato);
    let colateralDeduzido = parseCurrency(internalForm.value.txtValorColateralDeduzido) || 0;
    let produto = internalForm.value.selectProduto;

    let valorDescontadoTI = 0;

    if (produto === 'Kixi Empresa') {
        valorDescontadoTI = PercentTI * (valorCredito - colateralDeduzido);
    } else {
        valorDescontadoTI = PercentTI * valorCredito;
    }

    let valorIVATI = PercentIVATI * valorDescontadoTI;

    // Ajuste para Kixi Vale
    if (produto === 'Kixi Vale' && colateralDeduzido > 0) {
        valorDescontadoTI = PercentTI * valorCredito;
        valorIVATI = PercentIVATI * valorDescontadoTI;
    }

    // Ajuste para Kixi Empresa com colateral depositado
    if (produto === 'Kixi Empresa') {
        let colateralDepositado = parseCurrency(internalForm.value.txtValorColateralDepositado) || 0;
        valorDescontadoTI = PercentTI * (valorCredito - colateralDepositado);
        valorIVATI = PercentIVATI * valorDescontadoTI;
    }

    // Ajuste para Kixi Agropesca
    if (produto === 'Kixi Agropesca') {
        valorDescontadoTI = PercentTI * valorCredito;
        valorIVATI = PercentIVATI * valorDescontadoTI;
    }

    internalForm.value.txtValorTI = manterCasaDecimais(valorDescontadoTI, 2);
    internalForm.value.txtValorIVATI = manterCasaDecimais(valorIVATI, 2);


    atualizarTotais()
};

const atualizarTotais = () => {

    const ValorCreditoDoContrato = parseCurrency(internalForm.value.txtValorCreditoNoContrato) || 0;

    const ValorTI = parseCurrency(internalForm.value.txtValorTI) || 0;
    let ValorIVATI = parseCurrency(internalForm.value.txtValorIVATI) || 0;

    ValorIVATI += ValorTI;

    const ValorTP = parseCurrency(internalForm.value.txtValorTP) || 0;
    let ValorIVATP = parseCurrency(internalForm.value.txtValorIVATP) || 0;

    ValorIVATP += ValorTP;

    const selectedRadioTI = internalForm.value.TIPO_TI;

    const valorColateralDeduzido = parseCurrency(internalForm.value.txtValorColateralDeduzido) || 0;

    if (selectedRadioTI === 'Antecipado') {
        ValorIVATI = 0;
    }

    const TotalIVAS_e_COLATDD = ValorIVATI + ValorIVATP + valorColateralDeduzido;

    const ValorAReceber = ValorCreditoDoContrato - TotalIVAS_e_COLATDD;

    internalForm.value.txtFinalValorCreditoNoContrato = formatCurrency(ValorCreditoDoContrato);
    internalForm.value.txtFinalValorAReceber = formatCurrency(ValorAReceber);




};
function manterCasaDecimais(value, casas) {
    return value.toFixed(casas).replace('.', ',');
}

</script>

<style scoped>
.form-select,
.form-input {
    @apply border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm;
}

.form-select,
.form-input {
    @apply w-full pl-3 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm;
}

.btn {
    @apply px-5 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center text-sm;
}

.btn-primary {
    @apply bg-gradient-to-r from-green-900 to-greenkixi-300 text-white hover:from-orange-400 hover:to-green-400 shadow-md hover:shadow-lg transform hover:-translate-y-0.5;
}

.btn-secondary {
    @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 shadow-sm hover:shadow-md;
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

/* Animações */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>
