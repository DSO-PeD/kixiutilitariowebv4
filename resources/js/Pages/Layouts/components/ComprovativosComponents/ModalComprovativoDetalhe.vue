<!-- ModalComprovativoDetalhe.vue -->
<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-50">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100"
                leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-black/25" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95">
                        <DialogPanel
                            class="w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                            <DialogTitle as="h3" class="text-lg font-medium leading-6 text-green-950  ">
                                <b>Detalhes do Comprovativo - LNR {{ comprovativo.lnr }}</b>

                            </DialogTitle>

                            <div class="mt-4">
                                <hr />
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-6">
                                    <!-- Coluna 1 -->
                                    <div>
                                        <div class="space-y-4">
                                            <div>
                                                <h4 class="font-medium text-gray-900">Informações Básicas - DOP</h4>
                                                <div class="mt-2 space-y-2">
                                                    <div class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Data de Registro:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.data }}</span>
                                                    </div>
                                                    <div class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Agência:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.agencia || '-'
                                                            }}</span>
                                                    </div>
                                                    <div class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Registado Por:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.usuario
                                                            }}</span>
                                                    </div>
                                                    <div class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Cliente:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.cliente || '-'
                                                            }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <h4 class="font-medium text-gray-900">Informações do Pagamento</h4>
                                                <div class="mt-2 space-y-2">
                                                    <div class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">LNR:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.lnr }}</span>
                                                    </div>
                                                    <div class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Produto:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.metodologia ||
                                                            '-' }}</span>
                                                    </div>
                                                    <div class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Montante:</span>
                                                        <span class="text-sm font-medium text-green-600">{{
                                                            formatCurrency(comprovativo.montante) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="font-medium text-gray-900">Operação de Reconciliação</h6>

                                                <table style="font-size:10;">
                                                    <th>Data</th>
                                                    <th>Registado</th>
                                                    <th>Estado</th>
                                                    <th>Obs</th>
                                                     <th>Descrição</th>
                                                     <th>Obs</th>
                                                     <th>Dado Bancarios</th>
                                                </table>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Coluna 2 -->
                                    <div>
                                        <div class="space-y-4">
                                            <!--div>
                                                <h4 class="font-medium text-gray-900">Estado Actual</h4>
                                                <div class="mt-2 space-y-2">
                                                    <div class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Estado:</span>
                                                        <span :class="comprovativo.color"
                                                            class="px-2 py-1 text-xs font-medium rounded-full">
                                                            {{ comprovativo.estado }}
                                                        </span>
                                                    </div>
                                                    <div v-if="comprovativo.validado_por"
                                                        class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Validado Por:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.validado_por
                                                        }}</span>
                                                    </div>
                                                    <div v-if="comprovativo.data_validacao"
                                                        class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Data Validação:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.data_validacao
                                                        }}</span>
                                                    </div>
                                                    <div v-if="comprovativo.observacoes"
                                                        class="flex justify-between border-b pb-1">
                                                        <span class="text-sm text-gray-500">Observações:</span>
                                                        <span class="text-sm font-medium">{{ comprovativo.observacoes
                                                        }}</span>
                                                    </div>
                                                </div>
                                            </div-->

                                            <div>
                                                <!--h4 class="font-medium text-gray-900">Comprovativo</h4-->
                                                <div class="mt-2">
                                                    <div v-if="comprovativo.file" class="flex flex-col items-center">
                                                        <a :href="`/storage/comprovativos/${comprovativo.file}`"
                                                            target="_blank"
                                                            class="btn btn-primary flex items-center gap-2 mb-2">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="size-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                            </svg>
                                                            Baixar Comprovativo
                                                        </a>
                                                        <iframe :src="`/storage/comprovativos/${comprovativo.file}`"
                                                            class="w-full h-64 border rounded-lg"
                                                            v-if="isPdf(comprovativo.file)">
                                                            Seu navegador não suporta PDFs. <a
                                                                :href="`/storage/comprovativos/${comprovativo.file}`">Baixe
                                                                o arquivo</a>.
                                                        </iframe>
                                                        <img :src="`/storage/comprovativos/${comprovativo.file}`"
                                                            class="max-w-full h-auto border rounded-lg" v-else>
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

                            <div class="mt-6 flex justify-end space-x-3">
                                <button type="button" @click="closeModal" class="btn btn-outline-secondary">
                                    Fechar
                                </button>
                                <!--button @click="abrirReconciliacao" class="btn btn-primary"
                                    :disabled="comprovativo.estado === 'Validado'">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-5 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    Validar Comprovativo
                                </button-->
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue'

const props = defineProps({
    isOpen: Boolean,
    comprovativo: Object
})

const emit = defineEmits(['close', 'openReconciliation'])

const closeModal = () => {
    emit('close')
}

const abrirReconciliacao = () => {
    emit('openReconciliation')
    closeModal()
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
</script>
