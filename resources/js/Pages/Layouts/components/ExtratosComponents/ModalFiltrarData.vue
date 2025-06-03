<template>
    <Modal :show="show" @close="$emit('close')" maxWidth="md">
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- Cabeçalho com gradiente -->
            <div class="bg-gradient-to-r from-green-800 to-green-950 p-5 text-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Filtrar por Data de Registro</span>
                    </h3>
                    <button @click="$emit('close')" class="text-white hover:text-blue-200 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Corpo do modal -->
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Data de Início -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Data de Início</label>
                        <div class="relative">
                            <input type="date" v-model="dataInicio"
                                class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                @input="$emit('update:dataInicio', dataInicio)" :max="dataFim" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Data de Fim -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Data de Fim</label>
                        <div class="relative">
                            <input type="date" v-model="dataFim"
                                class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                @input="$emit('update:dataFim', dataFim)" :min="dataInicio" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validação de datas -->
                <div v-if="dateError" class="text-red-500 text-sm animate-pulse">
                    {{ dateError }}
                </div>
            </div>

            <!-- Rodapé com botões -->
            <div
                class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                <button @click="$emit('close')"
                    class="px-4 py-2 flex items-center justify-center space-x-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>Cancelar</span>
                </button>
                <button @click="handleFilter" :disabled="!isFormValid"
                    :class="{ 'opacity-50 cursor-not-allowed': !isFormValid, 'hover:bg-green-700': isFormValid }"
                    class="px-4 py-2 flex items-center justify-center space-x-2r bg-green-950 border border-transparent ounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    <span>Aplicar Filtro</span>
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
    show: Boolean,
    dataInicio: String,
    dataFim: String
})

const emit = defineEmits(['close', 'filter', 'update:dataInicio', 'update:dataFim'])

const dataInicio = ref(props.dataInicio || '')
const dataFim = ref(props.dataFim || '')
const dateError = ref('')

// Validação do formulário
const isFormValid = computed(() => {
    return dataInicio.value && dataFim.value && !dateError.value
})

// Validação das datas
const validateDates = () => {
    if (dataInicio.value && dataFim.value) {
        if (new Date(dataInicio.value) > new Date(dataFim.value)) {
            dateError.value = 'A data de início não pode ser maior que a data de fim'
            return false
        }
    }
    dateError.value = ''
    return true
}

// Watchers para validação
watch([dataInicio, dataFim], () => {
    validateDates()
})

watch(() => props.dataInicio, (newVal) => {
    dataInicio.value = newVal || ''
})

watch(() => props.dataFim, (newVal) => {
    dataFim.value = newVal || ''
})

const handleFilter = () => {
    if (!validateDates() || !isFormValid.value) return

    emit('update:dataInicio', dataInicio.value)
    emit('update:dataFim', dataFim.value)
    emit('filter')
}
</script>
