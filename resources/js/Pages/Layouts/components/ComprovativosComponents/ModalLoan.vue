<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-40 z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md">
            <div class="bg-green-950 text-white p-4 rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m15.75 15.75-2.489-2.489m0 0a3.375 3.375 0 1 0-4.773-4.773 3.375 3.375 0 0 0 4.774 4.774ZM21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg> &ThinSpace; Consultar por Loan Number
                </h3>
            </div>

            <div class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Inserir o Loan Number</label>
                    <input v-model="inputValue" type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Digite aqui.." />
                </div>

                <div class="flex justify-end space-x-3">
                    <button @click="handleClose"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors flex">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Cancelar
                    </button>
                    <button @click="handleSearch"
                        class="px-4 py-2 bg-green-950 text-white rounded-md hover:bg-orange-400 transition-colors flex"
                        :disabled="!inputValue">
                      &MediumSpace;
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                        Filtrar
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
    modelValue: String
})

const emit = defineEmits(['update:modelValue', 'close', 'search'])

const inputValue = ref(props.modelValue || '');

watch(() => props.modelValue, (newVal) => {
    inputValue.value = newVal || ''
})

const handleClose = () => {
    emit('close')
}

const handleSearch = () => {
    if (!inputValue.value) return
    emit('update:modelValue', inputValue.value)
    emit('search', inputValue.value)
}
</script>

<style scoped>
/* Estilos adicionais se necessário */
</style>
