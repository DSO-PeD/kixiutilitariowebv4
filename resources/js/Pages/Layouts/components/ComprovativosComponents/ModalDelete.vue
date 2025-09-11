<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <!-- Cabeçalho -->
            <div class="bg-red-500 text-white p-4 rounded-t-xl">
                <h3 class="text-lg font-bold flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Confirmar Eliminação
                </h3>
            </div>

            <!-- Corpo do Modal -->
            <div class="p-6">
                <div class="space-y-4">
                    <!-- Motivo -->
                    <div>
                        <input :value="id" type="hidden" />
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo da eliminação:</label>
                        <textarea v-model="localMotivo"
                            class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            rows="3" placeholder="Digite o motivo da eliminação" required></textarea>
                    </div>

                    <!-- Dados Adicionais -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dados adicionais:</label>
                        <input :value="dados"
                            class="w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed"
                            readonly />
                    </div>

                    <!-- Loan Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loan Number:</label>
                        <input :value="loan"
                            class="w-full p-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed"
                            readonly />
                    </div>
                </div>

                <!-- Botões -->
                <div class="flex justify-end space-x-3 mt-6">
                    <button @click="closeModal" class="btn btn-secondary">
                        Cancelar
                    </button>
                    <button @click="confirmElimination" class="btn btn-danger">
                        Confirmar Eliminação
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    isOpen: Boolean,
    motivo: String,
    dados: String,
    loan: String,
    id: Number
})

const emit = defineEmits(['close', 'confirm', 'update:motivo'])

const localMotivo = ref(props.motivo || '')

watch(() => props.motivo, (newVal) => {
    localMotivo.value = newVal
})

const closeModal = () => {
    emit('close')
}

const confirmElimination = () => {
    if (!localMotivo.value.trim()) {
        alert('Por favor, digite o motivo da eliminação')
        return
    }
    emit('confirm', props.id, localMotivo.value)
}
</script>


<style scoped>
.btn {
    @apply px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center justify-center;
}

.btn-primary {
    @apply bg-green-600 text-white hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2;
}
.btn-danger{
    @apply bg-red-600 text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2;
}
.btn-secondary{
    @apply bg-gray-600 text-white hover:bg-gray-700 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2;
}
</style>

