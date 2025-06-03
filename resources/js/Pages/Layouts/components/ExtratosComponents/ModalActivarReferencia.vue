<template>
    <Modal :show="show" @close="closeModal" max-width="3xl">
        <div class="p-4 md:p-6">
            <!-- Mensagem Local -->
            <div v-if="localMessage.show" class="mb-4">
                <div :class="{
                    'bg-green-100 border-green-400 text-green-700': localMessage.type === 'success',
                    'bg-red-100 border-red-400 text-red-700': localMessage.type === 'error'
                }" class="border px-4 py-3 rounded relative flex items-start">
                    <div class="flex-grow">
                        <strong class="font-bold">{{ localMessage.type === 'success' ? 'Sucesso!' : 'Erro!' }}</strong>
                        <span class="block sm:inline ml-1">{{ localMessage.text }}</span>
                    </div>
                    <button @click="localMessage.show = false" class="ml-2">
                        <svg class="fill-current h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Fechar</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 md:p-6 space-y-4">
                    <div class="border-b pb-4 mb-4">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800">Activar Referência de Pagamento</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Coluna 1 -->
                        <div class="space-y-4">
                            <div v-if="false">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Montante</label>
                                <div class="relative">
                                    <input v-model="form.montante" type="text"
                                        class="input bg-gray-50 cursor-not-allowed" readonly />
                                </div>
                                <p v-if="errors.montante" class="mt-1 text-sm text-red-600">{{ errors.montante }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Emissão</label>
                                <input v-model="form.emissao" type="text" placeholder="dd/mm/yyyy hh:mm"
                                    class="input bg-gray-50 cursor-not-allowed" readonly />
                                <p v-if="errors.emissao" class="mt-1 text-sm text-red-600">{{ errors.emissao }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número da referência</label>
                                <input v-model="form.numero" type="text" class="input bg-gray-50 cursor-not-allowed"
                                    readonly />
                                <p v-if="errors.numero" class="mt-1 text-sm text-red-600">{{ errors.numero }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <input v-model="form.tipo" type="text" class="input bg-gray-50 cursor-not-allowed"
                                    readonly />
                            </div>
                        </div>

                        <!-- Coluna 2 -->
                        <div class="space-y-4">
                            <div v-if="false">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Validade</label>
                                <input v-model="form.validade" type="text" placeholder="dd/mm/yyyy hh:mm"
                                    class="input bg-gray-50 cursor-not-allowed" readonly />
                                <p v-if="errors.validade" class="mt-1 text-sm text-red-600">{{ errors.validade }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <input v-model="form.estado" type="text" class="input bg-gray-50 cursor-not-allowed"
                                    readonly />
                            </div>

                            <div class="bg-gray-50 p-3 rounded-lg">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Entidade</h3>
                                <div class="space-y-2">
                                    <input v-model="form.entidade.nome" type="text"
                                        class="input bg-white cursor-not-allowed" readonly />
                                    <input v-model="form.entidade.numero" type="text"
                                        class="input bg-white cursor-not-allowed" readonly />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Cliente</h3>
                        <div class="space-y-2">
                            <input v-model="form.cliente.nome" type="text" class="input bg-white cursor-not-allowed"
                                readonly />
                            <input v-model="form.cliente.email" type="email" class="input bg-white cursor-not-allowed"
                                readonly />
                            <input v-model="form.cliente.telefone" type="text" class="input bg-white cursor-not-allowed"
                                readonly />
                        </div>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4 border-t">
                        <button class="btn btn-secondary px-4 py-2" @click="closeModal">
                            Cancelar

                        </button>
                        <button :disabled="loading" @click="enviarReferencia"
                            class="btn-primary px-4 py-2 flex items-center justify-center space-x-2r bg-green-950 border border-transparent">
                            <svg v-if="loading" class="animate-spin h-5 w-5 mr-2 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                            </svg>
                            <span>{{ loading ? 'Enviando...' : 'Activar Referência' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { defineProps, defineEmits, ref, watchEffect, watch, onMounted } from 'vue'
import Modal from '@/Components/Modal.vue'
import { router, usePage } from '@inertiajs/vue3'




const props = defineProps({
    show: Boolean,
    extratoref: Object
});

const emit = defineEmits(['close']);
const page = usePage()

const loading = ref(false)
const localMessage = ref({
    show: false,
    type: '',
    text: ''
})

const errors = ref({})

const defaultForm = () => ({
    pago: '0,00',
    montante: '0,00',
    emissao: '',
    validade: '',
    numero: '',
    tipo: 'Fixa',
    estado: 'Válida',
    entidade: {
        nome: 'KIXI CRÉDITO, SA',
        numero: '00589'
    },
    cliente: {
        nome: '',
        email: '',
        telefone: ''
    },
    id: ''
})



const form = ref(defaultForm())

// Função para formatar a data atual no formato dd/mm/yyyy hh:mm
const getCurrentDateTime = () => {
    const now = new Date()
    const day = String(now.getDate()).padStart(2, '0')
    const month = String(now.getMonth() + 1).padStart(2, '0')
    const year = now.getFullYear()
    const hours = String(now.getHours()).padStart(2, '0')
    const minutes = String(now.getMinutes()).padStart(2, '0')

    return `${day}/${month}/${year} ${hours}:${minutes}`
}

// Atualiza a data de emissão quando o modal é aberto
watch(() => props.show, (isShowing) => {
    if (isShowing) {
        form.value.emissao = getCurrentDateTime()
    }
})


watchEffect(() => {
    if (props.extratoref) {
        form.value = {
            ...form.value,
            numero: props.extratoref.referenciapagamento || '',
            montante: props.extratoref.valor || '0,00',
            emissao: props.extratoref.emissao || '',
            validade: props.extratoref.validade || '',
            estado: 'Por Activar',
            cliente: {
                nome: props.extratoref?.Cliente || '',
                email: 'diversos@kxicredito.ao',
                telefone: '92X XXX XXX'
            },
            metadados: {
                item1: "Activacoes de ref.pgt no ambiente prod."
            }
        }
    }
})

watch(() => page.props.flash, (newFlash) => {
    if (newFlash?.success || newFlash?.error) {
        setTimeout(() => {
            if (page.props.flash) {
                page.props.flash.success = null
                page.props.flash.error = null
            }
        }, 5000)
    }
}, { deep: true })

const resetForm = () => {
    form.value = defaultForm()
    localMessage.value = { show: false, type: '', text: '' }
    errors.value = {}
}

const closeModal = () => {
    resetForm()
    emit('close')
}

const enviarReferencia = async () => {
    loading.value = true
    try {
        await router.post('/criar-referencia', form.value, {
            onSuccess: () => {
                localMessage.value = {
                    show: true,
                    type: 'success',
                    text: 'Referência criada com sucesso!'
                }
                setTimeout(() => {
                    closeModal()
                }, 2000)
            },
            onError: (err) => {
                errors.value = err
                localMessage.value = {
                    show: true,
                    type: 'error',
                    text: 'Erro ao criar referência: ' +
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
.input {
    @apply w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500;
}

.btn-primary {
    @apply bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors duration-200;
}

.btn-primary:disabled {
    @apply bg-blue-400 cursor-not-allowed;
}

.btn-secondary {
    @apply bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium rounded-md transition-colors duration-200;
}
</style>
