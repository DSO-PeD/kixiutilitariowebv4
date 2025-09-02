<template>
  <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center pb-3 border-b">
          <h3 class="text-lg font-medium text-gray-900">
            <span class="text-yellow-600 mr-2">⚠️</span>
            Editar Voucher
          </h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Conteúdo -->
        <div class="mt-4" v-if="comprovativo">
          <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm text-yellow-700">
                  Pagamento por Referência requer voucher obrigatório.
                </p>
              </div>
            </div>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Loan Number</label>
            <p class="text-sm text-gray-900 font-semibold">{{ comprovativo.lnr || 'N/A' }}</p>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <p class="text-sm text-gray-900">{{ comprovativo.cliente || 'N/A' }}</p>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Montante</label>
            <p class="text-sm text-gray-900 font-semibold">{{ formatCurrency(comprovativo.montante) }}</p>
          </div>

          <div class="mb-4">
            <label for="novoVoucher" class="block text-sm font-medium text-gray-700 mb-1">Voucher *</label>
            <input
              type="text"
              id="novoVoucher"
              v-model="voucherLocal"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Digite o número do voucher"
              required
            >
            <p class="text-xs text-gray-500 mt-1">Campo obrigatório para Pagamento por Referência</p>
          </div>
        </div>

        <div v-else class="mt-4 text-center text-red-500">
          Erro: Dados do comprovativo não disponíveis
        </div>

        <!-- Rodapé -->
        <div class="flex justify-end gap-3 pt-4 border-t mt-4">
          <button
            @click="$emit('close')"
            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300"
          >
            Cancelar
          </button>
          <button
            @click="salvar"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
            :disabled="!voucherLocal || !comprovativo"
          >
            Salvar Voucher
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  show: Boolean,
  comprovativo: {
    type: Object,
    default: () => ({})
  },
  novoVoucher: String
})

const emit = defineEmits(['close', 'save'])

const voucherLocal = ref(props.novoVoucher || '')

// Função para formatar moeda (replicada do componente principal)
const formatCurrency = (value) => {
  if (value == null) return ''
  if (typeof value === 'string') {
    value = value.replace(/\D/g, '')
    if (!value) return '0,00'
    value = parseFloat(value) / 100
  }
  return value.toLocaleString('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

watch(() => props.novoVoucher, (newValue) => {
  voucherLocal.value = newValue
})

const salvar = () => {
  if (voucherLocal.value && props.comprovativo) {
    emit('save', {
      id: props.comprovativo.id,
      novoVoucher: voucherLocal.value.trim()
    })
  }
}
</script>
