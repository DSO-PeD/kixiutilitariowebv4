<template>

    <div
        class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex justify-center items-center p-4 transition-opacity duration-300">
        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto transform transition-all duration-300 scale-95 hover:scale-100">
            <!-- Cabeçalho -->
            <div
                class="bg-gradient-to-r from-green-900 to-greenkixi-300   to-green-950 text-white p-5 rounded-t-xl sticky top-0 z-10">
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
                            </label>
                            <div class="relative">
                                <input type="text" v-model="modelValue.txtNumeroLoanSaving" maxlength="5"
                                    placeholder="00000" minlength="5" class="form-input w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.txtNumeroLoanSaving }" required />
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fa-solid fa-hashtag text-gray-400"></i>
                                </div>
                            </div>
                            <p v-if="fieldErrors.txtNumeroLoanSaving" class="mt-1 text-sm text-red-600">
                                <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ fieldErrors.txtNumeroLoanSaving
                                }}
                            </p>
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
                                    <option v-for="formapgt in formaspagamentos" :value="formapgt.FormaPago"
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
                        <!-- Banco de Pagamento (visível apenas para Depósito Bancário) -->
                        <!-- Banco de Pagamento -->
                        <div v-if="modelValue.selectBase === 'AC' || modelValue.selectFormaPagamento === 14"
                            class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Banco de Pagamento</label>
                            <div class="relative">
                                <select v-model.number="modelValue.banco" class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.banco }"
                                    :required="modelValue.selectBase === 'AC' || modelValue.selectFormaPagamento === 14">
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
                        <div v-if="modelValue.selectBase === 'AC' || modelValue.selectFormaPagamento === 14"
                            class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Conta Bancária</label>
                            <div class="relative" >
                                <select v-model="modelValue.conta"
                                    class="form-select w-full pl-3 pr-10"
                                    :class="{ 'border-red-500': fieldErrors.conta }"
                                    :required="modelValue.selectBase === 'AC' || modelValue.selectFormaPagamento === 14">
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
                            <div class="flex flex-col"
                                v-if="modelValue.selectBase === 'AC' || modelValue.selectFormaPagamento === 14">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Voucher</label>
                                <div class="relative">
                                    <input type="text" v-model="modelValue.txtVoucher" placeholder="Voucher"
                                        class="form-input w-full pl-3 pr-10"
                                        :class="{ 'border-red-500': fieldErrors.txtVoucher }"
                                        :required="modelValue.selectBase === 'AC' || modelValue.selectFormaPagamento === 14" />
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
                        <button type="submit" :disabled="isSubmitting"
                            class="btn btn-primary flex items-center justify-center">
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
//const props = defineProps(['modelValue']);
//const emit = defineEmits(['update:modelValue']);

// FORMANTANDO VALORES
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
        default: 'txtMontante' // Default name if not provided
    }
});

const isSubmitting = ref(false);

function handleSubmit(event) {
    if (isSubmitting.value) return;
    isSubmitting.value = true;
    try {
        // 1. Garantir que o event existe
        if (!event) {
            console.error('Evento não definido');
            return;
        }

        // 2. Resetar todos os erros visuais
        resetAllErrors();

        // 3. Validar campos passo a passo
        let isValid = true;

        // Validação do montante
        if (!validateAmountField()) {
            isValid = false;
        }

        // Validação dos campos required
        if (!validateRequiredFields()) {
            isValid = false;
        }

        // Validação específica do telefone
        if (!validatePhoneField()) {
            isValid = false;
        }

        // 4. Se inválido, mostrar primeiro erro
        if (!isValid) {
            focusFirstError();
            return;
        }

        // 5. Se tudo válido, emitir evento
        emit('save');

    } catch (error) {
        console.error('Erro durante a submissão:', error);
        // Mostrar mensagem de erro genérico para o usuário
        showGeneralError('Ocorreu um erro ao processar o formulário');
    } finally {
        isSubmitting.value = false; // Reativa o botão
    }
}

// Funções auxiliares:

function resetAllErrors() {
    const errorElements = document.querySelectorAll('.text-red-600');
    errorElements.forEach(el => el.remove());

    const errorInputs = document.querySelectorAll('.border-red-500');
    errorInputs.forEach(input => input.classList.remove('border-red-500'));
}

function validateAmountField() {
    const amountInput = document.querySelector('input[name="txtMontante"]');
    if (!validateAmount()) {
        amountInput.classList.add('border-red-500');
        const errorMsg = amountError.value || 'O montante deve ser maior que zero';
        showError(amountInput, errorMsg);
        return false;
    }
    return true;
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
    // Implemente conforme necessário (pode ser um toast, alerta, etc.)
    console.error(message);
    // Exemplo com um alerta:
    alert(message);
}

const emit = defineEmits(['update:modelValue', 'close', 'save']);

const dateError = ref('');
const dateValue = ref('');
const amountError = ref('');

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
    general: '' // Erro geral
});


// Método para validar o montante
function validateAmount() {
    const numericValue = unformatCurrency(displayValue.value);

    if (numericValue <= 0) {
        amountError.value = 'O montante deve ser superior a zero';
        return false;
    }

    amountError.value = '';
    return true;
}

// Converter entre formato ISO (YYYY-MM-DD) e DD/MM/YYYY
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

// Validação da data
function validateDate() {
    dateError.value = '';
    if (!dateValue.value) {
        dateError.value = 'A data é obrigatória';
        return;
    }

    // Atualiza o modelValue no formato DD/MM/YYYY
    emit('update:modelValue', {
        ...props.modelValue,
        calDataBorderoux: toDDMMYYYY(dateValue.value)
    });
}

// Watcher para sincronizar quando o modelValue mudar externamente
watch(() => props.modelValue.calDataBorderoux, (newVal) => {
    if (newVal) {
        dateValue.value = toISODate(newVal);
    } else {
        dateValue.value = '';
    }
}, { immediate: true });

// Create a display value ref
// Initialize display value
const displayValue = ref(formatCurrency(props.modelValue.txtMontante || '0'));

// Função de formatação
function formatCurrency(value) {
    if (value === null || value === undefined || value === '') return '';

    // Se for string, limpa e converte para número
    if (typeof value === 'string') {
        value = value.replace(/[^\d,]/g, '');
        // Converte para número (considerando vírgula como decimal)
        value = value.replace(/\./g, '').replace(',', '.');
        value = parseFloat(value) || 0;
    }

    // Formata para o padrão PT (1.234,56)
    return new Intl.NumberFormat('pt-PT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}

// Função para limpar a formatação
function unformatCurrency(value) {
    return parseFloat(
        value.replace(/\./g, '').replace(',', '.')
    ) || 0;
}

// Watch para sincronização externa
watch(() => props.modelValue.txtMontante, (newVal) => {
    if (newVal !== unformatCurrency(displayValue.value)) {
        displayValue.value = formatCurrency(newVal);
    }
});

// Atualização durante a digitação
function onInput(event) {
    let value = event.target.value;
    amountError.value = '';

    // Mantém apenas números e vírgula
    value = value.replace(/[^\d,]/g, '');

    // Garante apenas uma vírgula
    const hasComma = value.includes(',');
    value = value.replace(/,/g, '');
    if (hasComma) {
        value = value.replace(/(\d{2})$/, ',$1');
    }

    // Adiciona pontos como separadores de milhar
    if (value.length > 3) {
        const parts = value.split(',');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        value = parts.join(',');
    }

    // Atualiza o valor exibido
    displayValue.value = value;

    // Emite o valor numérico
    const numericValue = unformatCurrency(value);
    emit('update:modelValue', {
        ...props.modelValue,
        txtMontante: numericValue
    });

    // Mantém a posição do cursor
    nextTick(() => {
        const cursorPos = event.target.selectionStart;
        event.target.setSelectionRange(cursorPos, cursorPos);
    });
}


function onBlur() {

    // Converte o valor formatado para numérico
    const numericValue = unformatCurrency(displayValue.value);

    // Valida o montante
    if (!validateAmount()) {
        return;
    }




    // Atualiza o v-model
    emit('update:modelValue', {
        ...props.modelValue,
        txtMontante: numericValue
    });

    // Formata o valor para exibição
    displayValue.value = formatCurrency(numericValue);
}

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    fileError.value = '';

    if (!file) return;

    // Verificar tipo de arquivo
    const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    if (!validTypes.includes(file.type)) {
        fileError.value = 'Formato de arquivo inválido. Use JPG, PNG ou PDF.';
        resetFileInput();
        return;
    }

    // Verificar tamanho (2MB)
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

defineExpose({
    selectedFile,
    resetFileInput
});

// Adicione esta computed property para determinar se os campos de banco devem ser mostrados
const showBankFields = computed(() => {
    // Encontre o item "Depósito Bancário" no array formaspagamentos
    const depositoBancario = props.formaspagamentos.find(
        fp => fp.FormaPago == 14 ||
            fp.FormaPago == 14
    );

    // Verifique se a forma de pagamento selecionada é "Depósito Bancário"
    return depositoBancario && props.modelValue.selectFormaPagamento === depositoBancario.FormaPago;
});

// Atualize a computed property contasFiltradas para usar props.contas
const contasFiltradas = computed(() => {
    // Verifica se o banco está definido (incluindo 0)
    if (props.modelValue.banco === null || props.modelValue.banco === undefined) return [];

    // Converte para número e compara
    return props.contas.filter(conta =>
        Number(conta.BaCodigo) === Number(props.modelValue.banco)
    );
});

watch(fieldErrors, (newErrors) => {
    console.log('Erros atuais:', newErrors);
}, { deep: true });
</script>

<style scoped>
/* Adicione isto ao seu <style scoped> */
.border-red-500 {
    border-color: #ef4444 !important;
    border-width: 1px !important;
}

/* Garanta que as classes do input não estão sobrescrevendo */
.form-input {
    border-width: 1px !important;
}

/* Garante que o input date tenha a mesma aparência que outros inputs */
input[type="date"] {
    appearance: none;
    -webkit-appearance: none;
    min-height: 42px;
    /* Ajuste conforme necessário */
}

/* Esconde o ícone de calendário nativo em alguns navegadores */
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

/* Drag and drop styles */
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
