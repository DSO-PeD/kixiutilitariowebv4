<template>
    <TransitionRoot appear :show="show" as="template">
        <Dialog as="div" @close="close" class="relative z-50">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100"
                leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4">
                    <TransitionChild as="template" enter="ease-out duration-300"
                        enter-from="opacity-0 scale-95 translate-y-4" enter-to="opacity-100 scale-100 translate-y-0"
                        leave="ease-in duration-200" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
                        <DialogPanel
                            class="w-full max-w-4xl transform overflow-hidden rounded-xl bg-white p-6 text-left align-middle shadow-2xl transition-all">
                            <!-- Header -->
                            <div class="flex items-center justify-between">
                                <DialogTitle as="h3"
                                    class="text-xl font-semibold leading-6 text-gray-900 flex items-center">
                                    <div class="p-2 rounded-lg bg-green-50 mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-green-600">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                        </svg>
                                    </div>
                                    <span>Editar Montante do Comprovativo</span>
                                </DialogTitle>
                                <button @click="close" class="text-gray-400 hover:text-gray-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Coluna esquerda - Dados do comprovativo -->
                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Loan
                                            Number</label>
                                        <div class="relative">
                                            <input type="text" :value="comprovativo.lnr"
                                                class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-600 font-medium"
                                                readonly />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Cliente</label>
                                        <input type="text" :value="comprovativo.cliente"
                                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-600 font-medium"
                                            readonly />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Montante
                                            Atual</label>
                                        <input type="text" :value="formatCurrency(comprovativo.montante)"
                                            class="w-full px-3.5 py-2.5 border border-gray-200 rounded-lg bg-gray-50 text-gray-600 font-medium"
                                            readonly />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5 flex items-center">
                                            Novo Montante
                                            <span class="text-red-500 ml-1">*</span>
                                        </label>
                                        <div class="relative">
                                            <span
                                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">AKZ</span>
                                            <input type="text" :value="localNovoMontante"
                                                @input="atualizarMontante($event.target.value)"
                                                class="w-full pl-12 pr-3.5 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-green-50 text-gray-800 font-medium" />
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Digite o novo valor do montante</p>
                                    </div>
                                </div>

                                <!-- Coluna direita - Visualização do comprovativo -->
                                <div class="border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                                    <div
                                        class="bg-white px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                        <h4 class="text-sm font-medium text-gray-700">Pré-visualização do Comprovativo
                                        </h4>
                                        <div class="flex space-x-2">
                                            <button @click="zoomOut" :disabled="zoomLevel <= 0.5"
                                                class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                title="Reduzir zoom">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 12h-15" />
                                                </svg>
                                            </button>
                                            <button @click="resetZoom"
                                                class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100"
                                                title="Resetar zoom">
                                                <span class="text-sm font-medium">{{ Math.round(zoomLevel * 100)
                                                    }}%</span>
                                            </button>
                                            <button @click="zoomIn" :disabled="zoomLevel >= 2"
                                                class="p-1.5 rounded-md text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                title="Aumentar zoom">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-if="comprovativo.file"
                                        class="h-96 overflow-auto flex items-center justify-center bg-gray-50 p-4">
                                        <!-- Se for PDF -->
                                        <iframe v-if="comprovativo.file.toLowerCase().endsWith('.pdf')" :src="pdfUrl"
                                            ref="pdfIframe"
                                            class="w-full h-full rounded-lg border border-gray-200 shadow-sm"
                                            frameborder="0"></iframe>

                                        <!-- Se for imagem -->
                                        <div v-else class="relative w-full h-full overflow-auto">
                                            <img :src="`/storage/comprovativos/${comprovativo.file}`"
                                                :style="{ transform: `scale(${zoomLevel})`, transformOrigin: '0 0', width: '100%', height: 'auto' }"
                                                class="rounded-lg border border-gray-200 shadow-sm" alt="Comprovativo"
                                                ref="imagePreview" />
                                        </div>
                                    </div>

                                    <div v-else
                                        class="h-96 flex flex-col items-center justify-center text-gray-400 p-6">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mb-3">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        <p class="text-sm">Nenhum comprovativo anexado</p>
                                    </div>
                                </div>


                            </div>

                            <!-- Footer -->
                            <div class="mt-8 flex justify-end space-x-3">
                                <button type="button" @click="close"
                                    class="px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Cancelar
                                </button>
                                <button type="button" @click="salvarEdicaoMontante"
                                    :disabled="!localNovoMontante || parseFloat(localNovoMontante.replace(/\./g, '').replace(',', '.')) === comprovativo.montante"
                                    :class="{ 'opacity-50 cursor-not-allowed': !localNovoMontante || parseFloat(localNovoMontante.replace(/\./g, '').replace(',', '.')) === comprovativo.montante }"
                                    class="px-4 py-2.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Guardar Alterações
                                </button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { ref, watch,computed  } from 'vue'
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'

const props = defineProps({
    show: Boolean,
    comprovativo: Object,
    novoMontante: String
})

const emit = defineEmits(['close', 'save', 'update:novoMontante'])

const zoomLevel = ref(1)
const pdfIframe = ref(null)
const imagePreview = ref(null)

const pdfUrl = computed(() => {
    return `/storage/comprovativos/${props.comprovativo.file}#toolbar=0&navpanes=0&zoom=${zoomLevel.value * 100}`
})

const zoomIn = () => {
    if (zoomLevel.value < 2) {
        zoomLevel.value = parseFloat((zoomLevel.value + 0.1).toFixed(1))
        applyZoom()
    }
}

const zoomOut = () => {
    if (zoomLevel.value > 0.5) {
        zoomLevel.value = parseFloat((zoomLevel.value - 0.1).toFixed(1))
        applyZoom()
    }
}

const resetZoom = () => {
    zoomLevel.value = 1
    applyZoom()
}

const applyZoom = () => {
    // Para imagens, o zoom é aplicado via CSS (já está sendo feito no template)
    // Para PDFs, precisamos recarregar o iframe com o novo parâmetro de zoom
    if (props.comprovativo.file && props.comprovativo.file.toLowerCase().endsWith('.pdf') && pdfIframe.value) {
        // Forçar recarregamento do iframe com o novo zoom
        pdfIframe.value.src = pdfUrl.value
    }
}

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

const localNovoMontante = ref(formatCurrency(props.novoMontante))

const atualizarMontante = (value) => {
    // Remove todos os caracteres não numéricos, exceto vírgula
    let cleanedValue = value.replace(/[^\d,]/g, '')

    // Se houver mais de uma vírgula, mantém apenas a primeira
    const commaCount = cleanedValue.split(',').length - 1
    if (commaCount > 1) {
        const firstCommaIndex = cleanedValue.indexOf(',')
        cleanedValue = cleanedValue.substring(0, firstCommaIndex + 1) +
            cleanedValue.substring(firstCommaIndex + 1).replace(/,/g, '')
    }

    // Separa parte inteira e decimal
    let [integerPart, decimalPart = ''] = cleanedValue.split(',')

    // Remove zeros à esquerda da parte inteira
    integerPart = integerPart.replace(/^0+/, '') || '0'

    // Limita a parte decimal a 2 dígitos
    decimalPart = decimalPart.substring(0, 2)

    // Formata a parte inteira com pontos separadores de milhar
    const formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.')

    // Junta tudo
    const formattedValue = decimalPart ?
        `${formattedInteger},${decimalPart}` :
        formattedInteger

    localNovoMontante.value = formattedValue
    emit('update:novoMontante', formattedValue)
}

const salvarEdicaoMontante = () => {
    const valorNumerico = parseFloat(
        localNovoMontante.value.replace(/\./g, '').replace(',', '.')
    )
    emit('save', {
        id: props.comprovativo.id,
        novoMontante: valorNumerico
    })
}

const close = () => {
    emit('close')
}

watch(() => props.novoMontante, (newVal) => {
    localNovoMontante.value = newVal
})
</script>
