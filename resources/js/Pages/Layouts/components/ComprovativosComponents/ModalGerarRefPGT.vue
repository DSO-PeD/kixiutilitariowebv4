<template>
    <div
        class="fixed inset-0 bg-black/40 backdrop-blur-md z-50 flex justify-center items-center p-4 transition-opacity duration-300">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-y-auto transform transition-all duration-300 scale-95 hover:scale-100 border border-gray-100">
            <!-- Cabeçalho Modernizado -->
            <div
                class="bg-gradient-to-r from-purple-600 to-purple-800 text-white p-6 rounded-t-2xl sticky top-0 z-10 shadow-lg">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-xl">
                            <i class="fas fa-credit-card text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold drop-shadow-sm">Gerar Referência de Pagamento</h3>
                            <p class="text-blue-100 text-sm mt-1">Sistema de gestão de pagamentos</p>
                        </div>
                    </div>
                    <button @click="$emit('close')"
                        class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition-all duration-200 group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-5 group-hover:scale-110 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Corpo do Modal -->
            <div class="p-8 bg-gray-50/50">
                <form @submit.prevent="handleSubmit" class="space-y-8">
                    <!-- Tipo (Loan/Saving) -->
                    <div
                        class="bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-2xl border border-blue-100 shadow-sm">
                        <label class="block text-sm font-semibold text-blue-900 mb-3 flex items-center">
                            <i class="fas fa-wallet mr-2 text-blue-600"></i>
                            Tipo do Pagamento
                        </label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="radio" v-model="modelValue.ls" value="Loan"
                                        class="form-radio h-5 w-5 text-blue-600 transition-all duration-200" />
                                    <div
                                        class="absolute inset-0 rounded-full bg-blue-100 scale-0 group-hover:scale-100 transition-transform">
                                    </div>
                                </div>
                                <span
                                    class="ml-3 text-gray-700 font-medium group-hover:text-blue-700 transition-colors">
                                    Prestações de Crédito
                                </span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer group">
                                <div class="relative">
                                    <input type="radio" v-model="modelValue.ls" value="Saving"
                                        class="form-radio h-5 w-5 text-blue-600 transition-all duration-200" />
                                    <div
                                        class="absolute inset-0 rounded-full bg-blue-100 scale-0 group-hover:scale-100 transition-transform">
                                    </div>
                                </div>
                                <span
                                    class="ml-3 text-gray-700 font-medium group-hover:text-blue-700 transition-colors">
                                    Poupanças
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Mensagem de funcionalidade não disponível -->
                    <div v-if="modelValue.ls === 'Loan'"
                        class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg shadow-sm">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 bg-amber-100 p-2 rounded-lg">
                                <i class="fas fa-exclamation-triangle text-amber-600 text-lg"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-amber-800 font-medium">Funcionalidade em Desenvolvimento</p>
                                <p class="text-amber-700 text-sm mt-1">
                                    As prestações de crédito estarão disponíveis em breve.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Dados Principais -->
                    <div v-if="modelValue.ls === 'Saving'" class="space-y-6">

                        <!-- Grid de Campos Principais -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            <!-- Base -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-building mr-2 text-gray-500 text-xs"></i>
                                    Base
                                </label>
                                <div class="relative group">
                                    <select v-model="modelValue.selectBase" @change="updateReference"
                                        class="form-select w-full pl-10 pr-10 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                        :class="{ 'border-red-400 ring-2 ring-red-100': fieldErrors.selectBase }"
                                        required>
                                        <option value="" disabled selected>Selecione a base</option>
                                        <option v-for="base in bases" :value="base.OfCodigo"
                                            :key="base.OfIdentificador">
                                            {{ base.OfIdentificador }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <!--i class="fas fa-map-marker-alt text-gray-400 group-hover:text-blue-500 transition-colors"></i-->
                                    </div>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <p v-if="fieldErrors.selectBase" class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ fieldErrors.selectBase }}
                                </p>
                            </div>

                            <!-- Grupo/Individual -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-users mr-2 text-gray-500 text-xs"></i>
                                    Grupo/Individual
                                </label>
                                <div class="relative group">
                                    <select v-model="modelValue.selectGrupoIndividual" @change="updateReference"
                                        class="form-select w-full pl-10 pr-10 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                        :class="{ 'border-red-400 ring-2 ring-red-100': fieldErrors.selectGrupoIndividual }"
                                        required>
                                        <option value="" disabled selected>Selecione o tipo</option>
                                        <option v-for="(label, value) in tipocomprovativos" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <!--i class="fas fa-user-friends text-gray-400 group-hover:text-blue-500 transition-colors"></i-->
                                    </div>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <p v-if="fieldErrors.selectGrupoIndividual"
                                    class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ fieldErrors.selectGrupoIndividual
                                    }}
                                </p>
                            </div>

                            <!-- Número Saving -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-hashtag mr-2 text-gray-500 text-xs"></i>
                                    Número Saving
                                </label>
                                <div class="relative group">
                                    <input type="text" v-model="modelValue.txtNumeroLoanSaving"
                                        @input="onLoanSavingInput" @blur="fetchClientData" maxlength="5"
                                        placeholder="00000" minlength="5"
                                        class="form-input w-full pl-10 pr-10 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                        :class="{
                                            'border-red-400 ring-2 ring-red-100': fieldErrors.txtNumeroLoanSaving,
                                            'border-green-400 ring-2 ring-green-100': modelValue.txtNumeroLoanSaving.length === 5 && !fieldErrors.txtNumeroLoanSaving
                                        }" required />
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <!--i class="fas fa-id-card text-gray-400 group-hover:text-blue-500 transition-colors"></i-->
                                    </div>
                                    <!-- Indicador de carregamento -->
                                    <div v-if="isLoadingClientData"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i class="fas fa-spinner fa-spin text-blue-500"></i>
                                    </div>
                                    <div v-else
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-check text-green-500"
                                            v-if="modelValue.txtNumeroLoanSaving.length === 5 && !fieldErrors.txtNumeroLoanSaving"></i>
                                    </div>
                                </div>
                                <p v-if="fieldErrors.txtNumeroLoanSaving"
                                    class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ fieldErrors.txtNumeroLoanSaving }}
                                </p>
                                <p v-if="modelValue.txtNumeroLoanSaving.length === 5 && !fieldErrors.txtNumeroLoanSaving"
                                    class="mt-2 text-sm text-green-600 flex items-center">
                                    <i class="fas fa-check-circle mr-1"></i> Número completo válido
                                </p>

                                <!-- Mostrar o número completo que será buscado-->
                                <div v-if="modelValue.txtNumeroLoanSaving.length === 5 && modelValue.selectBase"
                                    class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                                    <strong class="flex items-center">
                                        <i class="fas fa-search mr-2"></i>
                                        Número completo para busca:
                                    </strong>
                                    <span class="font-mono text-blue-900 mt-1 block">{{ buildCompleteLoanNumber()
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Dados do Cliente -->
                    <div v-if="modelValue.ls === 'Saving'" class="space-y-6">


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Nome do Cliente -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-user mr-2 text-gray-500 text-xs"></i>
                                    Nome do Cliente
                                </label>
                                <div class="relative group">
                                    <input v-model="modelValue.txtInfoAdicional"
                                        class="form-input w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                        :class="{ 'border-red-400 ring-2 ring-red-100': fieldErrors.txtInfoAdicional }"
                                        maxlength="125" placeholder="Ex. Nome completo do cliente" required />
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <!--i class="fas fa-signature text-gray-400 group-hover:text-blue-500 transition-colors"></i-->
                                    </div>
                                </div>
                                <p v-if="fieldErrors.txtInfoAdicional"
                                    class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ fieldErrors.txtInfoAdicional }}
                                </p>
                            </div>

                            <!-- Telefone -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-phone mr-2 text-gray-500 text-xs"></i>
                                    Telefone
                                </label>
                                <div class="relative group">
                                    <input v-model="modelValue.telefone"
                                        class="form-input w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                        :class="{ 'border-red-400 ring-2 ring-red-100': fieldErrors.telefone }"
                                        maxlength="9" minlength="9" placeholder="Ex. 921500000" required />
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <!--i class="fas fa-mobile-alt text-gray-400 group-hover:text-blue-500 transition-colors"></i-->
                                    </div>
                                </div>
                                <p v-if="fieldErrors.telefone" class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ fieldErrors.telefone }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Produto e Valor -->
                    <div v-if="modelValue.ls === 'Saving'" class="space-y-6">


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Produto -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-box-open mr-2 text-gray-500 text-xs"></i>
                                    Produto
                                </label>
                                <div class="relative group">
                                    <select v-model="modelValue.selectProdutoSaving"
                                        class="form-select w-full pl-10 pr-10 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md"
                                        :class="{ 'border-red-400 ring-2 ring-red-100': fieldErrors.selectProdutoSaving }"
                                        required>
                                        <option value="" disabled selected>Selecione o produto</option>
                                        <option
                                            v-for="produto in produtos.filter(p =>
                                                ['S00','S02','S03','S06','S08'].includes(p.Metodologia)
                                            )"
                                            :key="produto.Metodologia" :value="produto.Metodologia">
                                            {{ produto.PoAgrupado }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <!--i class="fas fa-cubes text-gray-400 group-hover:text-blue-500 transition-colors"></i-->
                                    </div>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <p v-if="fieldErrors.selectProdutoSaving"
                                    class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ fieldErrors.selectProdutoSaving }}
                                </p>
                            </div>

                            <!-- Montante -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-money-bill-wave mr-2 text-gray-500 text-xs"></i>
                                    Montante (Kz)
                                </label>
                                <div class="relative group">
                                    <input type="text" v-model="displayValue" @input="onInput" @blur="validateAmount"
                                        placeholder="0,00"
                                        class="form-input w-full pl-10 pr-4 py-3 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm group-hover:shadow-md text-right font-semibold"
                                        :class="{ 'border-red-400 ring-2 ring-red-100': amountError }" required />
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <!--i class="fas fa-coins text-gray-400 group-hover:text-blue-500 transition-colors"></i-->
                                    </div>
                                </div>
                                <p v-if="amountError" class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i> {{ amountError }}
                                </p>
                                <p v-else-if="displayValue && !amountError" class="mt-2 text-sm text-gray-600">
                                    Valor máximo: 7.000.000 Kz
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Seção da Referência de Pagamento (DESTAQUE) -->
                    <div v-if="modelValue.ls === 'Saving'" class="space-y-4">


                        <div
                            class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6 shadow-lg">
                            <label class="block text-sm font-bold text-green-900 mb-3 flex items-center">
                                <i class="fas fa-qrcode mr-2"></i>
                                REFERÊNCIA GERADA
                                <span class="text-red-500 ml-1">*</span>
                            </label>
                            <div class="relative">
                                <input v-model="modelValue.txtRefPagamento"
                                    class="w-full px-6 py-5 text-2xl font-bold text-center bg-white border-2 border-green-300 rounded-xl focus:ring-4 focus:ring-green-200 focus:border-green-500 transition-all duration-300 shadow-lg font-mono tracking-wider"
                                    placeholder="000000000" readonly style="letter-spacing: 0.2em;" />
                                <div class="absolute inset-y-0 right-4 flex items-center">
                                    <i class="fas fa-copy text-green-500 text-xl cursor-pointer hover:text-green-600 transition-colors"
                                        @click="copyReference" title="Copiar referência"></i>
                                </div>
                            </div>
                            <p class="text-green-700 text-sm mt-3 flex items-center justify-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Referência gerada automaticamente com base aos dados
                            </p>
                            <p class="text-green-600 text-xs mt-2 text-center font-mono">
                                preenchidos no formulario.
                            </p>
                        </div>
                    </div>

                    <!-- Mensagem de Erro Geral-->
                    <div v-if="fieldErrors.general"
                        class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-100 p-2 rounded-lg">
                                <i class="fas fa-exclamation-circle text-red-500"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-red-700 font-medium">
                                    {{ fieldErrors.general }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex flex-col sm:flex-row justify-end gap-4 pt-8 border-t border-gray-200">
                        <button type="button" @click="$emit('close')"
                            class="btn btn-secondary order-1 sm:order-none group">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5 group-hover:scale-110 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            &ThickSpace;
                            <span class="group-hover:translate-x-0.5 transition-transform">Cancelar</span>
                        </button>
                        <button @click="showConfirmationModal" :disabled="isSaveDisabled || modelValue.ls === 'Loan'"
                            class="btn btn-primary flex items-center justify-center group relative overflow-hidden"
                            :class="{ 'opacity-50 cursor-not-allowed': isSaveDisabled || modelValue.ls === 'Loan' }">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-purple-600 to-purple-700 group-hover:from-purple-700 group-hover:to-purple-800 transition-all">
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="size-5 relative z-10 mr-2 group-hover:scale-110 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                            </svg>
                            &ThickSpace;
                            <span class="relative z-10 font-semibold">
                                <span v-if="!isSubmitting"
                                    class="group-hover:translate-y-0.5 transition-transform">Gerar Referência</span>
                                <span v-else class="flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Processando...
                                </span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div v-if="showConfirmation"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex justify-center items-center p-4 transition-opacity duration-300">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all duration-300 scale-95 hover:scale-100 border border-gray-100">
            <!-- Cabeçalho do Modal de Confirmação -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-xl">
                            <i class="fas fa-check-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold drop-shadow-sm">Confirmação de Referência</h3>
                            <p class="text-green-100 text-sm mt-1">Revise os dados antes de confirmar</p>
                        </div>
                    </div>
                    <button @click="showConfirmation = false"
                        class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition-all duration-200 group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-5 group-hover:scale-110 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Corpo do Modal de Confirmação -->
            <div class="p-6 space-y-6">
                <!-- Resumo dos Dados -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Número do Cliente -->
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                        <label class="block text-sm font-semibold text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-id-card mr-2"></i>
                            Número do Cliente
                        </label>
                        <p class="text-lg font-bold text-blue-900 font-mono">{{ buildCompleteLoanNumber() }}</p>
                    </div>

                    <!-- Referência Gerada -->
                    <div class="bg-green-50 p-4 rounded-xl border border-green-200">
                        <label class="block text-sm font-semibold text-green-800 mb-2 flex items-center">
                            <i class="fas fa-barcode mr-2"></i>
                            Referência Gerada
                        </label>
                        <p class="text-lg font-bold text-green-900 font-mono tracking-wider">{{
                            modelValue.txtRefPagamento }}</p>
                    </div>

                    <!-- Nome do Cliente -->
                    <div class="bg-purple-50 p-4 rounded-xl border border-purple-200">
                        <label class="block text-sm font-semibold text-purple-800 mb-2 flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            Nome do Cliente
                        </label>
                        <p class="text-lg font-semibold text-purple-900">{{ modelValue.txtInfoAdicional }}</p>
                    </div>

                    <!-- Validade -->
                    <div class="bg-amber-50 p-4 rounded-xl border border-amber-200">
                        <label class="block text-sm font-semibold text-amber-800 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Validade: 3 Dias
                        </label>
                        <div class="space-y-1">
                            <p class="text-sm text-amber-700">
                                <strong>Início:</strong> {{ formatDate(validityDates.start) }}
                            </p>
                            <p class="text-sm text-amber-700">
                                <strong>Fim:</strong> {{ formatDate(validityDates.end) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Montante e Produto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-200">
                        <label class="block text-sm font-semibold text-indigo-800 mb-2 flex items-center">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            Montante
                        </label>
                        <p class="text-xl font-bold text-indigo-900">{{ formatCurrency(modelValue.txtMontante) }} Kz</p>
                    </div>

                    <div class="bg-teal-50 p-4 rounded-xl border border-teal-200">
                        <label class="block text-sm font-semibold text-teal-800 mb-2 flex items-center">
                            <i class="fas fa-box-open mr-2"></i>
                            Produto
                        </label>
                        <p class="text-lg font-semibold text-teal-900">{{ getProductName() }}</p>
                    </div>
                </div>

                <!-- Informações Adicionais -->
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informações Adicionais
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                        <div>
                            <strong class="text-gray-900">Telefone:</strong>
                            <span class="ml-2 font-mono">{{ modelValue.telefone }}</span>
                        </div>
                        <div>
                            <strong class="text-gray-900">Tipo:</strong>
                            <span class="ml-2 capitalize">{{ modelValue.selectGrupoIndividual?.toLowerCase() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Aviso de Validade -->
                <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded-lg">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-orange-500 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-orange-800 font-medium">Atenção</p>
                            <p class="text-orange-700 text-sm mt-1">
                                Esta referência tem validade de 3 dias. Após {{ formatDate(validityDates.end) }} não
                                será mais aceita.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Botões de Confirmação -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                    <button @click="showConfirmation = false" class="btn btn-secondary order-1 sm:order-none group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5 group-hover:scale-110 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        &ThickSpace;
                        <span class="group-hover:translate-x-0.5 transition-transform">Corrigir Dados</span>
                    </button>
                    <button @click="confirmSubmission"
                        class="btn btn-success flex items-center justify-center group relative overflow-hidden">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-green-500 to-green-600 group-hover:from-green-600 group-hover:to-green-700 transition-all">
                        </div>
                        <i class="fas fa-check relative z-10 mr-2 group-hover:scale-110 transition-transform"></i>
                        &ThickSpace;
                        <span class="relative z-10 font-semibold">
                            <span class="group-hover:translate-y-0.5 transition-transform">Confirmar Referência</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, watch, nextTick, computed, onMounted } from 'vue';

// Props
const props = defineProps({
    bases: Array,
    tipocomprovativos: Object,
    produtos: Array,
    bancos: Array,
    contas: Array,
    formaspagamentos: Array,
    modelValue: {
        type: Object,
        required: true
    },
    fieldName: {
        type: String,
        default: 'txtMontante'
    }
});

// Emits
const emit = defineEmits(['update:modelValue', 'close', 'save']);

// Refs
const isSubmitting = ref(false);
const displayValue = ref(formatCurrency(props.modelValue.txtMontante || '0'));
const amountError = ref('');
const isLoadingClientData = ref(false);
const showConfirmation = ref(false);

// Field Errors
const fieldErrors = ref({
    selectBase: '',
    selectGrupoIndividual: '',
    txtNumeroLoanSaving: '',
    txtInfoAdicional: '',
    telefone: '',
    selectProdutoSaving: '',
    general: ''
});

// Datas de validade
const validityDates = ref({
    start: new Date(),
    end: new Date()
});

// Calcular datas de validade (3 dias)
const calculateValidityDates = () => {
    const start = new Date();
    const end = new Date();
    end.setDate(end.getDate() + 3);

    validityDates.value = {
        start: start,
        end: end
    };
};

// Formatar data para exibição
const formatDate = (date) => {
    return date.toLocaleDateString('pt-PT', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Obter nome do produto
const getProductName = () => {
    if (!props.modelValue.selectProdutoSaving) return 'Não selecionado';

    const produto = props.produtos.find(p => p.Metodologia === props.modelValue.selectProdutoSaving);
    return produto ? produto.PoAgrupado : 'Produto não encontrado';
};

// Mostrar modal de confirmação
const showConfirmationModal = () => {
    if (props.modelValue.ls === 'Loan') return;
    if (isSubmitting.value) return;

    // Validar campos antes de mostrar confirmação
    resetAllErrors();
    let isValid = true;

    if (!validateAmount()) isValid = false;
    if (!validateRequiredFields()) isValid = false;
    if (!validatePhoneField()) isValid = false;

    if (!isValid) {
        focusFirstError();
        return;
    }

    // Calcular datas de validade
    calculateValidityDates();

    // Mostrar modal de confirmação
    showConfirmation.value = true;
};

// Confirmar submissão
const confirmSubmission = () => {
    isSubmitting.value = true;
    showConfirmation.value = false;

    try {
        emit('save');
    } catch (error) {
        console.error('Erro durante a confirmação:', error);
        showGeneralError('Ocorreu um erro ao processar a referência');
    } finally {
        isSubmitting.value = false;
    }
};

// Método para copiar referência
const copyReference = async () => {
    if (!props.modelValue.txtRefPagamento) return;

    try {
        await navigator.clipboard.writeText(props.modelValue.txtRefPagamento);
        // Você pode adicionar uma notificação de sucesso aqui
        console.log('Referência copiada:', props.modelValue.txtRefPagamento);
    } catch (err) {
        console.error('Erro ao copiar referência:', err);
    }
};

// ======= BASE E FUNÇÕES AUXILIARES =======

// Carrega números já usados do localStorage (ou cria um Set vazio)
const generatedNumbers = new Set(JSON.parse(localStorage.getItem('generatedNumbers') || '[]'));

// Guarda novamente no localStorage
const saveGeneratedNumbers = () => {
    localStorage.setItem('generatedNumbers', JSON.stringify(Array.from(generatedNumbers)));
};

// Gera número único de 5 dígitos
const generateUniqueNumber = () => {
    let newNumber;
    do {
        newNumber = Math.floor(10000 + Math.random() * 90000); // número entre 10000 e 99999
    } while (generatedNumbers.has(newNumber));

    generatedNumbers.add(newNumber);
    saveGeneratedNumbers();

    return newNumber.toString();
};


// Método atualizado para gerar referência com 9 dígitos
const updateReference = () => {
    const base = props.modelValue.selectBase;
    const tipo = props.modelValue.selectGrupoIndividual;
    // Gerar número aleatório único de 5 dígitos
    const numero = generateUniqueNumber();

    // Só gera referência se for Saving e todos os campos estiverem preenchidos
    if (props.modelValue.ls === 'Saving' && base && tipo && numero && numero.length === 5) {
        // Mapeia o tipo para o código correspondente (G -> 71, I -> 73)
        let tipoCodigo = props.modelValue.selectGrupoIndividual;
        if (tipo === 'G') {
            tipoCodigo = '71'; // G-71
        } else if (tipo === 'I') {
            tipoCodigo = '73'; // I-73
        }

        // A base já é numérica (conforme você mencionou)
        // Calcula quantos zeros precisamos adicionar para completar 9 dígitos
        const baseStr = String(base);
        const baseLength = baseStr.length;

        // Total de dígitos que temos: base + tipoCodigo + numero
        const currentDigits = baseLength + tipoCodigo.length + numero.length;

        // Se já temos 9 dígitos, usa sem zeros
        if (currentDigits === 9) {
            props.modelValue.txtRefPagamento = `${baseStr}${tipoCodigo}${numero}`;
        }
        // Se temos menos de 9 dígitos, adiciona zeros à esquerda da base
        else if (currentDigits < 9) {
            const zerosNeeded = 9 - (tipoCodigo.length + numero.length);
            const baseWithZeros = baseStr.padStart(zerosNeeded, '0');
            props.modelValue.txtRefPagamento = `${baseWithZeros}${tipoCodigo}${numero}`;
        }
        // Se temos mais de 9 dígitos (não deveria acontecer), trunca
        else {
            const availableForBase = 9 - (tipoCodigo.length + numero.length);
            const truncatedBase = baseStr.slice(-availableForBase);
            props.modelValue.txtRefPagamento = `${truncatedBase}${tipoCodigo}${numero}`;
        }
    } else {
        props.modelValue.txtRefPagamento = '';
    }

    // Emitir atualização
    emit('update:modelValue', props.modelValue);
};

// Computed Properties
const isSaveDisabled = computed(() => {
    if (!displayValue.value) return true;
    const numericValue = unformatCurrency(displayValue.value);
    return numericValue > 7000000 || amountError.value !== '' || isSubmitting.value;
});

// Ao montar o componente, definir "Poupança" como selecionado por padrão
onMounted(() => {
    if (props.modelValue.ls !== 'Saving') {
        emit('update:modelValue', {
            ...props.modelValue,
            ls: 'Saving'
        });
    }
});

// Função para construir o número completo
const buildCompleteLoanNumber = () => {
    try {
        // Encontrar a base correspondente pelo código numérico
        const baseEncontrada = props.bases.find(base =>
            base.OfCodigo == props.modelValue.selectBase
        );

        const identificador = baseEncontrada ? baseEncontrada.OfIdentificador : props.modelValue.selectBase;

        if (props.modelValue.ls === 'Loan') {
            return `${identificador}/${props.modelValue.txtNumeroLoanSaving}`;
        } else if (props.modelValue.ls === 'Saving') {
            return `${identificador}/${props.modelValue.selectGrupoIndividual}/${props.modelValue.txtNumeroLoanSaving}`;
        }
        return '';
    } catch (error) {
        console.error('Erro ao construir número completo:', error);
        return 'Erro ao gerar número';
    }
};

// Nova função para input do LoanSaving
const onLoanSavingInput = (event) => {
    props.modelValue.txtNumeroLoanSaving = event.target.value.replace(/[^0-9]/g, '');
    if (props.modelValue.txtNumeroLoanSaving.length === 5) {
        updateReference();
    }
};

// Função para buscar dados do cliente
const fetchClientData = async () => {
    props.modelValue.txtInfoAdicional = '';
    props.modelValue.telefone = '';
    fieldErrors.value.general = '';

    if (props.modelValue.txtNumeroLoanSaving.length !== 5 || !props.modelValue.selectBase) {
        return;
    }

    if (props.modelValue.ls === 'Saving' && !props.modelValue.selectGrupoIndividual) {
        fieldErrors.value.general = 'Para Saving, selecione o Grupo/Individual.';
        return;
    }

    isLoadingClientData.value = true;

    try {
        const completeLoanNumber = buildCompleteLoanNumber();
        if (!completeLoanNumber) {
            fieldErrors.value.general = 'Erro ao construir o número completo.';
            return;
        }

        const response = await axios.get('/loadautofill', {
            params: { completeNumber: completeLoanNumber }
        });

        if (response.data.success) {
            props.modelValue.txtInfoAdicional = response.data.nome || '';
            props.modelValue.telefone = response.data.telefone || '';
        } else {
            fieldErrors.value.general = response.data.error || 'Cliente não encontrado. Não há problemas, podes prosseguir com o preenchimento dos campos.';
        }
    } catch (error) {
        console.error('Erro ao buscar dados do cliente:', error);
        if (error.response) {
            if (error.response.status === 404) {
                fieldErrors.value.general = 'Cliente não encontrado. Não há problemas, podes prosseguir com o preenchimento dos campos.';
            } else {
                fieldErrors.value.general = error.response.data.error || 'Erro ao carregar dados do cliente.';
            }
        } else {
            fieldErrors.value.general = 'Erro ao carregar dados do cliente. Não há problemas, podes prosseguir com o preenchimento dos campos.';
        }
    } finally {
        isLoadingClientData.value = false;
    }
};

// Watchers para buscar dados automaticamente e atualizar referência
watch([
    () => props.modelValue.txtNumeroLoanSaving,
    () => props.modelValue.selectBase,
    () => props.modelValue.selectGrupoIndividual,
    () => props.modelValue.ls
], ([newNumero, newBase, newGrupo, newLs]) => {
    updateReference();

    if (newNumero && newNumero.length === 5 && newBase) {
        if (newLs === 'Saving' && !newGrupo) return;
        setTimeout(() => {
            fetchClientData();
        }, 300);
    }
});

// Funções de validação de montante
function validateAmount() {
    const numericValue = unformatCurrency(displayValue.value);
    if (numericValue <= 0) {
        amountError.value = 'O montante deve ser superior a zero';
        return false;
    }
    if (numericValue > 7000000) {
        amountError.value = 'O montante não pode exceder 7.000.000';
        return false;
    }
    amountError.value = '';
    return true;
}

// Funções auxiliares de validação
function resetAllErrors() {
    const errorElements = document.querySelectorAll('.text-red-600');
    errorElements.forEach(el => el.remove());
    const errorInputs = document.querySelectorAll('.border-red-500');
    errorInputs.forEach(input => input.classList.remove('border-red-500'));
}

function validateRequiredFields() {
    const form = document.querySelector('form');
    const requiredInputs = form.querySelectorAll('[required]');
    let isValid = true;

    requiredInputs.forEach(input => {
        if (!input.value) {
            input.classList.add('border-red-500');
            showError(input, 'Este campo é obrigatório');
            isValid = false;
        }
    });

    return isValid;
}

function validatePhoneField() {
    const phoneInput = document.querySelector('input[v-model="telefone"]');
    if (phoneInput && phoneInput.value && phoneInput.value.length !== 9) {
        phoneInput.classList.add('border-red-500');
        showError(phoneInput, 'O telefone deve ter 9 dígitos');
        return false;
    }
    return true;
}

function showError(inputElement, message) {
    let errorElement = inputElement.nextElementSibling;
    if (!errorElement || !errorElement.classList.contains('text-red-600')) {
        errorElement = document.createElement('p');
        errorElement.className = 'mt-1 text-sm text-red-600';
        inputElement.parentNode.insertBefore(errorElement, inputElement.nextElementSibling);
    }
    errorElement.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1"></i> ${message}`;
}

function focusFirstError() {
    nextTick(() => {
        const firstError = document.querySelector('.border-red-500');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    });
}

function showGeneralError(message) {
    console.error(message);
    alert(message);
}

// Funções de formatação monetária
function formatCurrency(value) {
    if (value === null || value === undefined || value === '') return '';
    if (typeof value === 'string') {
        value = value.replace(/[^\d,]/g, '');
        value = value.replace(/\./g, '').replace(',', '.');
        value = parseFloat(value) || 0;
    }
    return new Intl.NumberFormat('pt-PT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}

function unformatCurrency(value) {
    return parseFloat(value.replace(/\./g, '').replace(',', '.')) || 0;
}

// Watch para sincronização do montante
watch(() => props.modelValue.txtMontante, (newVal) => {
    if (newVal !== unformatCurrency(displayValue.value)) {
        displayValue.value = formatCurrency(newVal);
    }
});

// Manipulação de input do montante
function onInput(event) {
    let value = event.target.value;
    amountError.value = '';
    value = value.replace(/[^\d,]/g, '');
    const hasComma = value.includes(',');
    value = value.replace(/,/g, '');
    if (hasComma) {
        value = value.replace(/(\d{2})$/, ',$1');
    }
    if (value.length > 3) {
        const parts = value.split(',');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        value = parts.join(',');
    }
    const numericValue = unformatCurrency(value);
    if (numericValue > 7000000) {
        amountError.value = 'O montante não pode exceder 7.000.000';
        return;
    }
    displayValue.value = value;
    emit('update:modelValue', {
        ...props.modelValue,
        txtMontante: numericValue
    });
    nextTick(() => {
        const cursorPos = event.target.selectionStart;
        event.target.setSelectionRange(cursorPos, cursorPos);
    });
}

function onBlur() {
    const numericValue = unformatCurrency(displayValue.value);
    if (!validateAmount()) return;
    emit('update:modelValue', {
        ...props.modelValue,
        txtMontante: numericValue
    });
    displayValue.value = formatCurrency(numericValue);
}

// Watch para limite do montante
watch(displayValue, (newValue) => {
    const numericValue = unformatCurrency(newValue);
    if (numericValue > 7000000) {
        alert('O montante não pode exceder 7.000.000');
    }
});
</script>

<style scoped>
/* Estilos melhorados */
.border-red-500 {
    border-color: #ef4444 !important;
    border-width: 1px !important;
}

.form-input,
.form-select,
.form-textarea {
    @apply border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm;
}

.btn {
    @apply px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center text-sm;
}

.btn-primary {
    @apply bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 relative overflow-hidden;
}

.btn-secondary {
    @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:border-gray-400 shadow-sm hover:shadow-md;
}

.btn-success {
    @apply bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 relative overflow-hidden;
}

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

.drag-active {
    @apply border-blue-500 bg-blue-50/50;
}

.form-input.border-red-500 {
    border-color: #ef4444;
    box-shadow: 0 0 0 1px #ef4444;
}

.text-red-600 {
    color: #dc2626;
}

/* Melhorias de responsividade */
@media (max-width: 768px) {
    .grid-cols-3 {
        grid-template-columns: 1fr;
    }

    .p-8 {
        padding: 1.5rem;
    }

    .text-2xl {
        font-size: 1.5rem;
    }

    .py-5 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
}

/* Efeitos de hover suaves */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:translate-x-0\.5 {
    transform: translateX(0.125rem);
}

.group:hover .group-hover\:translate-y-0\.5 {
    transform: translateY(0.125rem);
}

/* Gradientes e sombras melhorados */
.shadow-lg {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
