<template>
    <div class="fixed inset-0 bg-black bg-opacity-40 z-50 flex justify-center items-center p-4">
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
                        <input :value="id" hidden />
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo da eliminação:</label>
                        <textarea v-model="localMotivo" @input="updateMotivo($event.target.value)"
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
                    <button @click="$emit('close')" class="btn btn-secondary">
                        Cancelar
                    </button>
                    <button @click="$emit('confirm',id)" class="btn btn-danger">
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
    motivo: String,
    dados: String,
    loan: String,
    id:Number
})

const emit = defineEmits(['close', 'confirm', 'update:motivo'])

const localMotivo = ref('');

// Sempre que a prop mudar (por exemplo, abrir o modal com outro motivo), atualiza a variável local
watch(() => props.motivo, (newVal) => {
    localMotivo.value = newVal;
});

// Quando o usuário digitar, emite a alteração
function updateMotivo(value) {
    localMotivo.value = value;
    emit('update:motivo', value);
}
</script>

<style scoped>
.btn {
    @apply px-4 py-2 rounded-md font-medium transition-colors;
}

.btn-secondary {
    @apply bg-gray-200 text-gray-800 hover:bg-gray-300;
}

.btn-danger {
    @apply bg-red-600 text-white hover:bg-red-700;
}
</style>
