<template>
    <Modal :show="show" @close="closeModal" maxWidth="5xl">
        <!-- Cabeçalho -->
        <div class="bg-gradient-to-r from-green-900 to-greenkixi-300   to-green-950 text-white p-5 rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span class="drop-shadow-md">&ThinSpace;Validação de Comprovativo</span>
                </h2>
                <button @click="closeModal" class="text-white/80 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                </button>
            </div>
        </div>

        <!-- Corpo do Modal -->
        <div class="p-6 space-y-6">
            <!-- Mensagem Local -->
            <div v-if="localMessage.show" class="animate-fade-in">
                <div :class="{
                    'bg-green-50 border-green-200 text-green-800': localMessage.type === 'success',
                    'bg-red-50 border-red-200 text-red-800': localMessage.type === 'error'
                }" class="border-l-4 rounded-lg p-4 flex items-start shadow-sm">
                    <div class="flex-shrink-0">
                        <i v-if="localMessage.type === 'success'"
                            class="fa-solid fa-circle-check text-green-500 text-xl mr-3"></i>
                        <i v-else class="fa-solid fa-circle-exclamation text-red-500 text-xl mr-3"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="font-medium">
                            {{ localMessage.type === 'success' ? 'Sucesso!' : 'Erro!' }}
                        </p>
                        <p class="text-sm mt-1">
                            {{ localMessage.text }}
                        </p>
                    </div>
                    <button @click="localMessage.show = false" class="ml-4 text-gray-400 hover:text-gray-500">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Seção de Informações do Comprovativo -->
            <div class="p-5 rounded-xl border border-blue-200">


                <div class="mt-1">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6">
                        <!-- Coluna 1 -->
                        <div>
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-medium text-gray-900">Informações da DOP</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between border-b pb-1">
                                            <span class="text-sm text-gray-500">Data de Registro:</span>
                                            <span class="text-sm font-medium">{{ form.data }}</span>
                                        </div>
                                        <div class="flex justify-between border-b pb-1">
                                            <span class="text-sm text-gray-500">Agência:</span>
                                            <span class="text-sm font-medium">{{ form.agencia || '-'
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between border-b pb-1">
                                            <span class="text-sm text-gray-500">Registado Por:</span>
                                            <span class="text-sm font-medium">{{ form.usuario
                                            }}</span>
                                        </div>
                                        <div class="flex justify-between border-b pb-1">
                                            <span class="text-sm text-gray-500">Cliente:</span>
                                            <span class="text-sm font-medium">{{ form.cliente || '-'
                                            }}</span>
                                        </div>

                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-medium text-gray-900">Informações do Pagamento</h4>
                                    <div class="mt-2 space-y-2">
                                        <div class="flex justify-between border-b pb-1">
                                            <span class="text-sm text-gray-500">LNR:</span>
                                            <span class="text-sm font-medium">{{ form.lnr }}</span>
                                        </div>
                                        <div class="flex justify-between border-b pb-1">
                                            <span class="text-sm text-gray-500">Produto:</span>
                                            <span class="text-sm font-medium">{{ form.metodologia ||
                                                '-' }}</span>
                                        </div>
                                        <div class="flex justify-between border-b pb-1">
                                            <span class="text-sm text-gray-500">Montante:</span>
                                            <span class="text-sm font-medium text-green-600">{{
                                                formatCurrency(form.montante) }}</span>
                                        </div>
                                        <div class="flex justify-between border-b pb-1">
                                            <span class="text-sm text-gray-500">Estado Actual do Comprovativo
                                                :</span>
                                            <span :class="form.color"
                                                class="px-2 py-1 text-xs font-medium rounded-full">
                                                {{ form.estado }}
                                            </span>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <!-- Coluna 2 -->
                        <div>
                            <div class="space-y-4">

                                <div>
                                    <!--h4 class="font-medium text-gray-900">Comprovativo</h4-->
                                    <div class="mt-1">
                                        <div v-if="form.file" class="flex flex-col items-center">
                                            <!-- Botão de download (comum para ambos os tipos) -->
                                            <a :href="`/storage/comprovativos/${form.file}`" target="_blank"
                                                class="btn btn-secondary flex items-center gap-2 mb-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                </svg>
                                                Baixar Comprovativo
                                            </a>

                                            <!-- Visualização do arquivo -->
                                            <template v-if="isPdf(form.file)">
                                                <!-- PDF (iframe) -->
                                                <iframe :src="`/storage/comprovativos/${form.file}`"
                                                    class="w-full h-64 border rounded-lg">
                                                </iframe>
                                                <!-- Mensagem alternativa fora do iframe -->
                                                <p class="text-sm text-gray-600 mt-2">
                                                    Seu navegador não suporta PDFs.
                                                    <a :href="`/storage/comprovativos/${form.file}`"
                                                        class="text-blue-600">Baixe o arquivo</a>.
                                                </p>
                                            </template>
                                            <template v-else>
                                                <!-- Imagem -->
                                                <img :src="`/storage/comprovativos/${form.file}`"
                                                    class="max-w-full h-auto max-h-64 border rounded-lg"
                                                    alt="Comprovativo">
                                            </template>
                                        </div>
                                        <div v-else class="text-center text-gray-500 italic">
                                            Nenhum arquivo de comprovativo disponível
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>



            <!-- Seção de Dados para Reconciliação -->

            <div class="bg-gray-50 p-5 rounded-xl border border-green-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 text-greenkixi-solid">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                    </svg> &ThinSpace;
                    Dados para Reconciliação

                </h3>
                <hr />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Banco de Pagamento</label>
                        <div class="relative w-full">
                            <select v-model="form.banco" class="form-select w-full pl-3 pr-10" required>
                                <option value="" disabled selected>Selecione o banco</option>
                                <option v-for="banco in $page.props.bancos" :value="banco.BaCodigo"
                                    :key="banco.BaCodigo">
                                    {{ banco.BaSigla }} - {{ banco.BaNome }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fa-solid fa-building-columns text-gray-400"></i>
                            </div>
                        </div>
                        <p v-if="errors.banco" class="mt-1 text-xs text-red-600">{{ errors.banco }}</p>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Conta Bancária</label>
                        <div class="relative w-full">
                            <select v-model="form.conta" :disabled="!form.banco" class="form-select w-full pl-3 pr-10"
                                required>
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
                        <p v-if="errors.conta" class="mt-1 text-xs text-red-600">{{ errors.conta }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Voucher</label>
                        <div class="relative w-full">
                            <input v-model="form.voucher" type="text" class="form-input w-full pl-3 pr-10" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fa-solid fa-barcode text-gray-400"></i>
                            </div>
                        </div>
                        <p v-if="errors.voucher" class="mt-1 text-xs text-red-600">{{ errors.voucher }}</p>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <div class="relative w-full">
                            <input v-model="form.descricao" type="text" class="form-input w-full pl-3 pr-10" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fa-solid fa-pen-to-square text-gray-400"></i>
                            </div>
                        </div>
                        <p v-if="errors.descricao" class="mt-1 text-xs text-red-600">{{ errors.descricao }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <div class="relative w-full">
                            <select v-model="form.estado" class="form-select w-full pl-3 pr-10" required>
                                <option value="" disabled selected>Selecione o Estado</option>
                                <option v-for="estado in $page.props.estados" :value="estado.id" :key="estado.id">
                                    {{ estado.descricao_estado }}
                                </option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fa-solid fa-circle-check text-gray-400"></i>
                            </div>
                        </div>
                        <p v-if="errors.estado" class="mt-1 text-xs text-red-600">{{ errors.estado }}</p>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observação</label>
                        <div class="relative w-full">
                            <input v-model="form.observacao" type="text" class="form-input w-full pl-3 pr-10"
                                placeholder="Caso tenha uma informação para agência" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fa-solid fa-comment-dots text-gray-400"></i>
                            </div>
                        </div>
                        <p v-if="errors.observacao" class="mt-1 text-xs text-red-600">{{ errors.observacao }}</p>
                    </div>
                </div>
            </div>
            <!-- Botões  -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" @click="closeModal" class="btn btn-secondary order-1 sm:order-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    &ThickSpace;

                    Cancelar
                </button>
                <button :disabled="loading" @click="validarComprovativo"
                    class="btn btn-primary flex items-center justify-center">





                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6" v-if="!loading">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>

                    &ThickSpace;

                    <svg class="animate-spin h-5 w-5 mr-3 text-white" viewBox="0 0 24 24" v-else>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z" />
                    </svg>
                    &ThickSpace;




                    {{ loading ? 'Validando...' : 'Validar Comprovativo' }}


                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watchEffect, computed } from 'vue'
import Modal from '@/Components/Modal.vue'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
    show: Boolean,
    comprovativoreconci: Object
});

const emit = defineEmits(['close', 'success']);

const loading = ref(false)
const localMessage = ref({
    show: false,
    type: '',
    text: ''
})

const errors = ref({})

const form = ref({
    id: '',
    data: '',
    lnr: '',
    agencia: '',
    usuario: '',
    basedelacamento: '',
    metodologia: '',
    montante: '',
    file: '',
    banco: '',
    conta: '',
    estado: '',
    observacao: '',
    voucher: '',
    descricao: ''
})

// Filter accounts based on selected bank
const contasFiltradas = computed(() => {
    if (!form.value.banco) return [];
    return page.props.contas.filter(conta => conta.BaCodigo === form.value.banco);
});

watchEffect(() => {
    if (props.comprovativoreconci) {
        form.value = {
            ...form.value,
            id: props.comprovativoreconci.id || '',
            data: props.comprovativoreconci.data || '',
            lnr: props.comprovativoreconci.lnr || '',
            agencia: props.comprovativoreconci.agencia || '',
            usuario: props.comprovativoreconci.usuario || '',
            basedelacamento: props.comprovativoreconci.basedelacamento || '',
            metodologia: props.comprovativoreconci.metodologia || '',
            montante: props.comprovativoreconci.montante || '',
            file: props.comprovativoreconci.file || '',
            banco: props.comprovativoreconci.banco || '',
            conta: props.comprovativoreconci.conta || '',
            cliente: props.comprovativoreconci.cliente || '',
            estado: props.comprovativoreconci.estado || '',
            color: props.comprovativoreconci.color || ''
        }
    }
})

const resetForm = () => {
    form.value = {
        id: '',
        data: '',
        lnr: '',
        agencia: '',
        usuario: '',
        basedelacamento: '',
        metodologia: '',
        montante: '',
        file: '',
        banco: '',
        conta: '',
        estado: '',
        observacao: '',
        voucher: '',
        descricao: ''
    }
    localMessage.value = { show: false, type: '', text: '' }
    errors.value = {}
}

const closeModal = () => {
    resetForm()
    emit('close')
}

const formatCurrency = (value) => {
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
const isPdf = (filename) => {
    return filename.toLowerCase().endsWith('.pdf')
}

const validarComprovativo = async () => {
    // Reset errors
    errors.value = {}

    // Validate required fields
    if (!form.value.banco) {
        errors.value.banco = 'Por favor selecione o banco';
        return;
    }
    if (!form.value.conta) {
        errors.value.conta = 'Por favor selecione a conta';
        return;
    }
    if (!form.value.estado) {
        errors.value.estado = 'Por favor selecione o estado';
        return;
    }

    loading.value = true
    try {
        await router.post('/validar-comprovativo', form.value, {
            onSuccess: () => {
                localMessage.value = {
                    show: true,
                    type: 'success',
                    text: 'Comprovativo validado com sucesso!'
                }
                setTimeout(() => {
                    emit('success')
                    closeModal()
                }, 1500)
            },
            onError: (err) => {
                errors.value = err
                localMessage.value = {
                    show: true,
                    type: 'error',
                    text: 'Erro ao validar comprovativo: ' +
                        (err.message || Object.values(err).join(', ') || 'Por favor, tente novamente')
                }
            }
        })
    } catch (error) {
        console.error('Erro inesperado:', error)
        localMessage.value = {
            show: true,
            type: 'error',
            text: 'Ocorreu um erro inesperado ao processar a requisição'
        }
    } finally {
        loading.value = false
    }
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
