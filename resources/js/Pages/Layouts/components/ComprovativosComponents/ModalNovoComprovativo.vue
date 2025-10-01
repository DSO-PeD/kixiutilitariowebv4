<template>
    <div
        class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex justify-center items-center p-4 transition-opacity duration-300">
        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 hover:scale-100">
            <!-- Cabeçalho -->
            <div
                class="bg-gradient-to-r from-green-900 to-greenkixi-300 to-green-950 text-white p-5 rounded-t-xl sticky top-0 z-10">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold flex items-center">
                        <i class="fa-solid fa-file-circle-check mr-3 text-blue-100"></i>
                        <span class="drop-shadow-md">Registar Novo Comprovativo</span>
                    </h3>
                    <button @click="$emit('close')" class="text-white/80 hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Corpo do Modal -->
            <div class="p-6">
                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Tipo (Loan/Saving) -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-medium text-green-950 mb-2">Gênero do Pagamento:</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" v-model="modelValue.ls" value="Loan"
                                    class="form-radio h-5 w-5 text-blue-600 transition-colors" />
                                <span class="ml-2 text-gray-700 font-medium">Loan</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" v-model="modelValue.ls" value="Saving"
                                    class="form-radio h-5 w-5 text-blue-600 transition-colors" />
                                <span class="ml-2 text-gray-700 font-medium">Saving</span>
                            </label>
                        </div>
                    </div>

                    <!-- Anexar Comprovativo -->
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Anexar Comprovativo</label>
                        <div
                            class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-gray-300 rounded-lg px-6 pt-5 pb-6 transition-all hover:border-blue-500 hover:bg-blue-50/50">
                            <div class="space-y-1 text-center">
                                <div class="flex justify-center text-blue-500 mb-3">
                                    <i class="fa-solid fa-cloud-arrow-up text-3xl"></i>
                                </div>
                                <div class="flex text-sm text-gray-600">
                                    <label
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Carregar ficheiro</span>
                                        <input type="file" ref="fileInput" @change="handleFileUpload"
                                            accept=".jpg,.jpeg,.png,.pdf" class="sr-only" required />
                                    </label>
                                    <p class="pl-1">ou arraste e solte</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    Formatos aceites: JPG, PNG, PDF (Max. 2MB)
                                </p>
                            </div>
                        </div>
                        <p v-if="fileError" class="mt-2 text-sm text-red-600 animate-pulse">
                            <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fileError }}
                        </p>
                        <div v-if="selectedFile"
                            class="mt-3 flex items-center justify-between bg-blue-50/50 p-3 rounded border border-blue-100">
                            <div class="flex items-center">
                                <i class="fa-solid fa-file-circle-check text-blue-500 mr-2"></i>
                                <span class="text-sm font-medium text-gray-700 truncate max-w-xs">{{ selectedFile.name
                                    }}</span>
                            </div>
                            <button type="button" @click="resetFileInput" class="text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Grid de campos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Base</label>
                            <div class="relative">
                                <select v-model="modelValue.selectBase" class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.selectBase }" required>
                                    <option value="" disabled selected>Selecione a base</option>
                                    <option v-for="base in bases" :value="base.OfIdentificador"
                                        :key="base.OfIdentificador">
                                        {{ base.OfIdentificador }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.selectBase" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.selectBase }}
                            </p>
                        </div>

                        <div v-if="modelValue.ls === 'Saving'" class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grupo/Individual</label>
                            <div class="relative">
                                <select v-model="modelValue.selectGrupoIndividual" class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.selectGrupoIndividual }" required>
                                    <option value="" disabled selected>Selecione o tipo</option>
                                    <option v-for="(label, value) in tipocomprovativos" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.selectGrupoIndividual" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.selectGrupoIndividual
                                }}
                            </p>
                        </div>

                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Número {{ modelValue.ls === 'Loan' ? 'Loan' : 'Saving' }}
                                <span v-if="modelValue.txtNumeroLoanSaving.length === 5 && modelValue.selectBase"
                                    class="text-xs text-green-600 ml-1">
                                    ({{ buildCompleteLoanNumber() }})
                                </span>
                            </label>
                            <div class="relative">
                                <input type="text" v-model="modelValue.txtNumeroLoanSaving"
                                    @input="modelValue.txtNumeroLoanSaving = $event.target.value.replace(/[^0-9]/g, '')"
                                    @blur="fetchClientData" maxlength="5" placeholder="00000" minlength="5"
                                    class="form-input w-full pl-3 pr-10" :class="{
                                        'border-red-500': fieldErrors.txtNumeroLoanSaving,
                                        'border-green-500': modelValue.txtNumeroLoanSaving.length === 5 && !fieldErrors.txtNumeroLoanSaving
                                    }" required />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-hashtag text-gray-400"></i>
                                </div>
                                <!-- Indicador de carregamento -->
                                <div v-if="isLoadingClientData"
                                    class="absolute inset-y-0 right-0 flex items-center pr-8">
                                    <i class="fa-solid fa-spinner fa-spin text-blue-500"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.txtNumeroLoanSaving" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.txtNumeroLoanSaving
                                }}
                            </p>
                            <p v-if="modelValue.txtNumeroLoanSaving.length === 5 && !fieldErrors.txtNumeroLoanSaving"
                                class="mt-1 text-sm text-green-600">
                                <i class="fa-solid fa-check mr-1"></i> Número completo
                            </p>

                            <!-- Mostrar o número completo que será buscado -->
                            <div v-if="modelValue.txtNumeroLoanSaving.length === 5 && modelValue.selectBase"
                                class="mt-2 p-2 bg-blue-50 rounded text-xs text-blue-700">
                                <strong>Número completo para busca:</strong> {{ buildCompleteLoanNumber() }}
                            </div>
                        </div>
                    </div>

                    <!-- Info cliente -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nome do Cliente -->
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Cliente</label>
                            <div class="relative">
                                <input v-model="modelValue.txtInfoAdicional" class="form-input w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.txtInfoAdicional }" maxlength="125"
                                    placeholder="Ex. Nome do cliente" required />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-user text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.txtInfoAdicional" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.txtInfoAdicional }}
                            </p>
                        </div>

                        <!-- Telefone -->
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                            <div class="relative">
                                <input v-model="modelValue.telefone" class="form-input w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.telefone }" maxlength="9"
                                    placeholder="Ex. 921500000" required />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-phone text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.telefone" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.telefone }}
                            </p>
                        </div>
                    </div>

                    <!-- Produto e Forma de Pagamento -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Produto -->
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Produto</label>
                            <div class="relative">
                                <select v-if="modelValue.ls === 'Loan'" v-model="modelValue.selectProdutoLoan"
                                    class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.selectProdutoLoan }" required>
                                    <option value="" disabled selected>Selecione o produto</option>
                                    <option
                                        v-for="produto in produtos.filter(p => p.TipoProduto === 'L' || p.TipoProduto === 'G')"
                                        :key="produto.Metodologia" :value="produto.Metodologia">
                                        {{ produto.PoAgrupado }}
                                    </option>
                                </select>
                                <select v-else v-model="modelValue.selectProdutoSaving"
                                    class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.selectProdutoSaving }" required>
                                    <option value="" disabled selected>Selecione o produto</option>
                                    <option
                                        v-for="produto in produtos.filter(p => p.TipoProduto === 'S' || p.TipoProduto === 'G')"
                                        :key="produto.Metodologia" :value="produto.Metodologia">
                                        {{ produto.PoAgrupado }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-box-open text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.selectProdutoLoan || fieldErrors.selectProdutoSaving"
                                class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                {{ modelValue.ls === 'Loan' ? fieldErrors.selectProdutoLoan :
                                fieldErrors.selectProdutoSaving }}
                            </p>
                        </div>

                        <!-- Forma de Pagamento -->
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Forma de Pagamento</label>
                            <div class="relative">
                                <select v-model="modelValue.selectFormaPagamento" class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.selectFormaPagamento }" required>
                                    <option value="" disabled selected>Selecione a forma</option>
                                    <option v-for="formapgt in formaspagamentosFiltrados" :value="formapgt.FormaPago"
                                        :key="formapgt.FormaPago">
                                        {{ formapgt.FormaPagoN }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-money-bill-wave text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.selectFormaPagamento" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.selectFormaPagamento
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Montante, Data e Voucher -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montante</label>
                            <div class="relative">
                                <input type="text" v-model="displayValue" @input="onInput" @blur="validateAmount"
                                    placeholder="0,00" class="form-input w-full pl-3 pr-10 text-right"
                                    :class="{ 'border-red-500': amountError }" required />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <!--span class="text-gray-500">Kz</span-->
                                </div>
                            </div>
                            <p v-if="amountError" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ amountError }}
                            </p>
                        </div>

                        <!-- Banco de Pagamento -->
                        <div v-if="modelValue.selectBase === 'AC'" class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Banco de Pagamento</label>
                            <div class="relative">
                                <select v-model.number="modelValue.banco" class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.banco }"
                                    :required="modelValue.selectBase === 'AC'">
                                    <option value="" disabled selected>Selecione o banco</option>
                                    <option v-for="banco in $page.props.bancos" :value="Number(banco.BaCodigo)"
                                        :key="banco.BaCodigo">
                                        {{ banco.BaSigla }} - {{ banco.BaNome }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-building-columns text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.banco" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.banco }}
                            </p>
                        </div>

                        <!-- Conta Bancária -->
                        <div v-if="modelValue.selectBase === 'AC'" class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Conta Bancária</label>
                            <div class="relative">
                                <select v-model="modelValue.conta" class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.conta }"
                                    :required="modelValue.selectBase === 'AC'">
                                    <option value="" disabled selected>Selecione a conta</option>
                                    <option v-for="conta in contasFiltradas" :value="conta.codigoConta"
                                        :key="conta.codigoConta">
                                        {{ conta.ContaBacaria }}
                                    </option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-wallet text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.conta" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.conta }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Data do Reembolso -->
                            <div class="flex flex-col">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Data do Reembolso</label>
                                <div class="relative">
                                    <input type="date" v-model="dateValue" @change="validateDate"
                                        class="form-input w-full pl-3 pr-10"
                                        :class="{ 'border-red-500': fieldErrors.calDataBorderoux }" required />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fa-solid fa-calendar-days text-gray-400"></i>
                                    </div>
                                </div>
                                <p v-if="fieldErrors.calDataBorderoux" class="mt-1 text-sm text-red-600">
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.calDataBorderoux
                                    }}
                                </p>
                            </div>

                            <!-- Voucher -->
                            <div class="flex flex-col" v-if="modelValue.selectBase === 'AC'">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Voucher</label>
                                <div class="relative">
                                    <input type="text" v-model="modelValue.txtVoucher" placeholder="Voucher"
                                        class="form-input w-full pl-3 pr-10"
                                        :class="{ 'border-red-500': fieldErrors.txtVoucher }"
                                        :required="modelValue.selectBase === 'AC'" />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fa-solid fa-barcode text-gray-400"></i>
                                    </div>
                                </div>
                                <p v-if="fieldErrors.txtVoucher" class="mt-1 text-sm text-red-600">
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.txtVoucher }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div v-if="fieldErrors.general" class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    {{ fieldErrors.general }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-200">
                        <button type="button" @click="$emit('close')" class="btn btn-secondary order-1 sm:order-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            &ThickSpace; Cancelar
                        </button>
                        <button type="submit" :disabled="isSaveDisabled"
                            class="btn btn-primary flex items-center justify-center"
                            :class="{ 'opacity-50 cursor-not-allowed': isSaveDisabled }">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                            </svg>
                            &ThickSpace;
                            <span v-if="!isSubmitting">Guardar</span>
                            <span v-else>
                                <i class="fa-solid fa-spinner fa-spin mr-1"></i> Processando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, nextTick, computed } from 'vue';

const fileInput = ref(null);
const selectedFile = ref(null);
const fileError = ref('');

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
const dateError = ref('');
const dateValue = ref('');
const isLoadingClientData = ref(false);

// Field Errors
const fieldErrors = ref({
    selectBase: '',
    selectGrupoIndividual: '',
    txtNumeroLoanSaving: '',
    txtInfoAdicional: '',
    telefone: '',
    selectProdutoLoan: '',
    selectProdutoSaving: '',
    selectFormaPagamento: '',
    calDataBorderoux: '',
    banco: null,
    conta: null,
    txtVoucher: '',
    general: ''
});

// Computed Properties
const formaspagamentosFiltrados = computed(() => {
    return props.formaspagamentos.filter(formapgt => {
        return formapgt.FormaPago !== 8;
    });
});

const isSaveDisabled = computed(() => {
    if (!displayValue.value) return true;
    const numericValue = unformatCurrency(displayValue.value);
    return numericValue > 7000000 || amountError.value !== '' || isSubmitting.value;
});

const contasFiltradas = computed(() => {
    if (props.modelValue.banco === null || props.modelValue.banco === undefined) return [];
    return props.contas.filter(conta =>
        Number(conta.BaCodigo) === Number(props.modelValue.banco)
    );
});

const showBankFields = computed(() => {
    const depositoBancario = props.formaspagamentos.find(
        fp => fp.FormaPago == 14 || fp.FormaPago == 14
    );
    return depositoBancario && props.modelValue.selectFormaPagamento === depositoBancario.FormaPago;
});

// Função para construir o número completo
const buildCompleteLoanNumber = () => {
    if (props.modelValue.ls === 'Loan') {
        return `${props.modelValue.selectBase}/${props.modelValue.txtNumeroLoanSaving}`;
    } else if (props.modelValue.ls === 'Saving') {
        return `${props.modelValue.selectBase}/${props.modelValue.selectGrupoIndividual}/${props.modelValue.txtNumeroLoanSaving}`;
    }
    return '';
};

// Função para buscar dados do cliente
const fetchClientData = async () => {
    // Resetar campos anteriores
    props.modelValue.txtInfoAdicional = '';
    props.modelValue.telefone = '';
    fieldErrors.value.general = '';

    // Verificar se todos os campos necessários estão preenchidos
    if (props.modelValue.txtNumeroLoanSaving.length !== 5 || !props.modelValue.selectBase) {
        return;
    }

    // Para Saving, verificar se o grupo/individual está selecionado
    if (props.modelValue.ls === 'Saving' && !props.modelValue.selectGrupoIndividual) {
        fieldErrors.value.general = 'Para Saving, selecione o Grupo/Individual.';
        return;
    }

    isLoadingClientData.value = true;

    try {
        // Construir o número completo
        const completeLoanNumber = buildCompleteLoanNumber();

        if (!completeLoanNumber) {
            fieldErrors.value.general = 'Erro ao construir o número completo.';
            return;
        }

        // Fazer a requisição para a API - CORREÇÃO AQUI
        //const response = await axios.get(`/loadautofill/${encodeURIComponent(completeLoanNumber)}`);
        const response = await axios.get('/loadautofill', {
            params: {
                completeNumber: completeLoanNumber
            }
        });
        // Verificar se a resposta foi bem-sucedida
        if (response.data.success) {
            // Preencher os campos automaticamente - CORREÇÃO AQUI
            props.modelValue.txtInfoAdicional = response.data.nome || '';
            props.modelValue.telefone = response.data.telefone || '';

            console.log('Dados do cliente carregados com sucesso para:', completeLoanNumber);
        } else {
            fieldErrors.value.general = response.data.error || 'Cliente não encontrado. Não há problemas, podes prosseguir com o preenchimento dos campos.';
        }
    } catch (error) {
        console.error('Erro ao buscar dados do cliente:', error);

        // Melhor tratamento de erro
        if (error.response) {
            // O servidor respondeu com um status de erro
            if (error.response.status === 404) {
                fieldErrors.value.general = 'Cliente não encontrado. Não há  problemas, podes prosseguir com o preenchimento dos campos.';
            } else if (error.response.status === 500) {
                fieldErrors.value.general = 'Erro interno do servidor. Tente novamente.';
            } else {
                fieldErrors.value.general = error.response.data.error || 'Erro ao carregar dados do cliente.';
            }
        } else if (error.request) {
            // A requisição foi feita mas não houve resposta
            fieldErrors.value.general = 'Sem resposta do servidor. Não há problemas, podes prosseguir com o preenchimento dos campos.';
        } else {
            // Outro tipo de erro
            fieldErrors.value.general = 'Erro ao carregar dados do cliente. Não há problemas, podes prosseguir com o preenchimento dos campos.';
        }
    } finally {
        isLoadingClientData.value = false;
    }
};

// Watchers para buscar dados automaticamente
watch([
    () => props.modelValue.txtNumeroLoanSaving,
    () => props.modelValue.selectBase,
    () => props.modelValue.selectGrupoIndividual,
    () => props.modelValue.ls
], ([newNumero, newBase, newGrupo, newLs], [oldNumero, oldBase, oldGrupo, oldLs]) => {
    // Executar apenas quando houver mudanças relevantes e campos estiverem prontos
    if (newNumero && newNumero.length === 5 && newBase) {
        // Para Saving, esperar também pelo grupo/individual
        if (newLs === 'Saving' && !newGrupo) {
            return;
        }

        // Pequeno delay para evitar múltiplas chamadas rápidas
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

function validateAmountField() {
    const amountInput = document.querySelector('input[name="txtMontante"]');
    if (!validateAmount()) {
        amountInput.classList.add('border-red-500');
        const errorMsg = amountError.value || 'O montante deve ser maior que 0 e abaixo ou igual a 7.000.000';
        showError(amountInput, errorMsg);
        return false;
    }
    return true;
}

// Função de submissão do formulário
function handleSubmit(event) {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

    try {
        if (!event) {
            console.error('Evento não definido');
            return;
        }

        // Resetar todos os erros visuais
        resetAllErrors();

        // Validar campos passo a passo
        let isValid = true;

        if (!validateAmountField()) {
            isValid = false;
        }

        if (!validateRequiredFields()) {
            isValid = false;
        }

        if (!validatePhoneField()) {
            isValid = false;
        }

        if (!isValid) {
            focusFirstError();
            return;
        }

        emit('save');

    } catch (error) {
        console.error('Erro durante a submissão:', error);
        showGeneralError('Ocorreu um erro ao processar o formulário');
    } finally {
        isSubmitting.value = false;
    }
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

// Funções de formatação de data
function toDDMMYYYY(isoDate) {
    if (!isoDate) return '';
    const [year, month, day] = isoDate.split('-');
    return `${day}/${month}/${year}`;
}

function toISODate(ddmmyyyy) {
    if (!ddmmyyyy) return '';
    const [day, month, year] = ddmmyyyy.split('/');
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
}

function validateDate() {
    dateError.value = '';
    if (!dateValue.value) {
        dateError.value = 'A data é obrigatória';
        return;
    }

    emit('update:modelValue', {
        ...props.modelValue,
        calDataBorderoux: toDDMMYYYY(dateValue.value)
    });
}

// Watcher para data
watch(() => props.modelValue.calDataBorderoux, (newVal) => {
    if (newVal) {
        dateValue.value = toISODate(newVal);
    } else {
        dateValue.value = '';
    }
}, { immediate: true });

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
    return parseFloat(
        value.replace(/\./g, '').replace(',', '.')
    ) || 0;
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

    if (!validateAmount()) {
        return;
    }

    emit('update:modelValue', {
        ...props.modelValue,
        txtMontante: numericValue
    });

    displayValue.value = formatCurrency(numericValue);
}

// Funções de manipulação de arquivos
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    fileError.value = '';

    if (!file) return;

    const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!validTypes.includes(file.type)) {
        fileError.value = 'Formato de arquivo inválido. Use JPG, PNG ou PDF.';
        resetFileInput();
        return;
    }

    const maxSize = 2 * 1024 * 1024;
    if (file.size > maxSize) {
        fileError.value = 'Arquivo muito grande. Tamanho máximo: 2MB.';
        resetFileInput();
        return;
    }

    selectedFile.value = file;
};

const resetFileInput = () => {
    selectedFile.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

// Expose
defineExpose({
    selectedFile,
    resetFileInput
});

// Watch para debug
watch(fieldErrors, (newErrors) => {
    console.log('Erros atuais:', newErrors);
}, { deep: true });

// Watch para limite do montante
watch(displayValue, (newValue) => {
    const numericValue = unformatCurrency(newValue);
    if (numericValue > 7000000) {
        alert('O montante não pode exceder 7.000.000');
    }
});
</script>

<style scoped>
.border-red-500 {
    border-color: #ef4444 !important;
    border-width: 1px !important;
}

.form-input {
    border-width: 1px !important;
}

input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    min-height: 42px;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
    position: absolute;
    right: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.form-select,
.form-input,
.form-textarea {
    @apply border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm;
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
</style>
