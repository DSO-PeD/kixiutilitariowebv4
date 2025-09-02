<template>
  <div v-if="show" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center pb-3 border-b">
          <h3 class="text-lg font-medium text-gray-900">Habilitar Comprovativo para Recuperação</h3>
          <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Conteúdo -->
        <div class="mt-4" v-if="comprovativo">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Loan Number</label>
            <p class="text-sm text-gray-900 font-semibold">{{ comprovativo.lnr || 'N/A' }}</p>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
            <p class="text-sm text-gray-900">{{ comprovativo.cliente || 'N/A' }}</p>
          </div>

          <div class="mb-4">
            <label for="novaData" class="block text-sm font-medium text-gray-700 mb-1">Nova Data de Recuperação</label>
            <input
              type="date"
              id="novaData"
              v-model="novaDataLocal"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              :max="maxDate"
            >
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
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
            :disabled="!novaDataLocal || !comprovativo"
          >
            Salvar
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
    default: () => ({}) // Objeto vazio como padrão
  },
  novaData: String
})

const emit = defineEmits(['close', 'save'])

const novaDataLocal = ref(props.novaData || '')

// Data máxima permitida (hoje)
const maxDate = computed(() => {
  return new Date().toISOString().split('T')[0]
})

watch(() => props.novaData, (newValue) => {
  novaDataLocal.value = newValue
})

const salvar = () => {
  if (novaDataLocal.value && props.comprovativo) {
    emit('save', {
      id: props.comprovativo.id,
      novaData: novaDataLocal.value
    })
  }
}
</script>
