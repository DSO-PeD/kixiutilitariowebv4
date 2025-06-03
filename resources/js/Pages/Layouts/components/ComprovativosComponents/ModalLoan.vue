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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Loan Number</label>
                    <input v-model="loanNumber" type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Digite o Loan Number" />
                </div>

                <div class="flex justify-end space-x-3">
                    <button @click="handleClose"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>

                        Cancelar
                    </button>
                    <button @click="handleSearch"
                        class="px-4 py-2 bg-green-950 text-white rounded-md hover:bg-orange-400 transition-colors"
                        :disabled="!loanNumber">


                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>

                        Buscar
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

const loanNumber = ref(props.modelValue || '')

watch(() => props.modelValue, (newVal) => {
    loanNumber.value = newVal || ''
})

const handleClose = () => {
    emit('close')
}

const handleSearch = () => {
    if (!loanNumber.value) return
    emit('update:modelValue', loanNumber.value)
    emit('search', loanNumber.value)
}
</script>

<style scoped>
/* Estilos adicionais se necessário */
</style>
