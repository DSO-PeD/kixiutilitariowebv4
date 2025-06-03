<template>
    <Modal :show="show" max-width="4xl" @close="$emit('close')">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-semibold text-gray-800">
                    <i class="fa-solid fa-file-invoice-dollar text-blue-600 mr-2"></i>
                    Detalhes do Extrato
                </h3>
                <button @click="$emit('close')" class="text-gray-500 hover:text-gray-700">

                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>

                </button>
            </div>

            <div v-if="extrato" class="space-y-6">
                <!-- Cabeçalho -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm text-gray-500">Loan Number</p>
                        <p class="font-medium">{{ extrato.Lnr }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm text-gray-500">Cliente</p>
                        <p class="font-medium">{{ extrato.Cliente }}</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm text-gray-500">Data</p>
                        <p class="font-medium">{{ extrato.CiFecha }}</p>
                    </div>
                </div>

                <!-- Valores Principais -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                        <p class="text-sm text-blue-600">Valor Total</p>
                        <p class="font-bold text-blue-800">{{ formatNumber(extrato.ValorTotalCredito) }}</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                        <p class="text-sm text-green-600">Colateral</p>
                        <p class="font-bold text-green-800">{{ extrato.PercColateral }}% ({{
                            formatNumber(extrato.ValorDoColateral) }})</p>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                        <p class="text-sm text-yellow-600">Taxa Processamento</p>
                        <p class="font-bold text-yellow-800">{{ formatNumber(extrato.TXAProcePercentaValor) }}</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                        <p class="text-sm text-purple-600">Taxa Imprevisto</p>
                        <p class="font-bold text-purple-800">{{ formatNumber(extrato.TXAImprePercentaValor) }}</p>
                    </div>
                </div>

                <!-- Detalhes -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-500">Produto</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ extrato.Produto }}</td>
                            </tr>
                            <!-- Outras linhas de detalhes -->
                            <!-- ... -->
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mt-4">
                    <a :href="`/reports/extrato/${extrato.Num}`" class="btn  btn-warning" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>


                        Gerar PDF
                    </a>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    show: Boolean,
    extrato: Object

})

const emit = defineEmits(['close'])

const formatNumber = (value) => {
    return Number(value).toLocaleString('pt-PT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}
</script>
<style scoped>
.btn {
    @apply px-5 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center text-sm;
}

.btn-warning {
    @apply bg-yellow-500 text-white hover:bg-yellow-600;
}

.btn-secondary {
    @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 shadow-sm hover:shadow-md;
}
</style>
