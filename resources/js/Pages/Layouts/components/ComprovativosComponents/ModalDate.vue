<template>
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Overlay com transição suave -->
        <div class="absolute inset-0 bg-black bg-opacity-40 transition-opacity" @click="handleClose"></div>

        <!-- Modal principal -->
        <div class="relative w-full max-w-md transform transition-all">
            <div class="bg-white rounded-xl shadow-xl overflow-hidden">
                <!-- Cabeçalho -->
                <div class="bg-gradient-to-r from-green-800 to-green-950 p-5 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Consultar Dados</span>
                        </h3>
                        <button @click="handleClose" class="text-white hover:text-gray-200 focus:outline-none">
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
                                <input v-model="startDate" type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 transition"
                                    :max="endDate" />
                            </div>
                        </div>

                        <!-- Data de Fim -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Data de Fim</label>
                            <div class="relative">
                                <input v-model="endDate" type="date"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-500 transition"
                                    :min="startDate" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                         <label class="block text-sm font-medium text-gray-700">Estado</label>
                          <label class="block text-sm font-medium text-gray-700">Agência</label>
                        <select v-model="estadoModal" class="form-select pr-8"
                            :class="{ 'border-red-500': dateError && !estadoModal }">
                            <option disabled :value="0">Escolha estado</option>
                            <option v-for="estado in $page.props.estados" :value="Number(estado.id)" :key="estado.id">
                                {{ estado.descricao_estado }}
                            </option>
                            <option  :value="28">Todos estados</option>
                        </select>

                        <select v-model="agenciaModal" class="form-select pr-8"
                            :class="{ 'border-red-500': dateError && !agenciaModal }">
                            <option disabled selected :value="''">Escolha agência</option>

                            <option v-for="age in $page.props.bases" :value="age.OfIdentificador"
                                :key="age.OfIdentificador">
                                {{ age.OfIdentificador }} - {{ age.OfNombre }}
                            </option>
                            <option  :value="'T'">Todas que tenho acesso</option>
                        </select>
                    </div>

                    <!-- Validação de datas -->
                    <div v-if="dateError" class="text-red-500 text-sm animate-pulse">
                        {{ dateError }}
                    </div>
                </div>

                <!-- Rodapé com botões -->
                <div
                    class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                    <button @click="handleClose"
                        class="px-4 py-2 flex items-center justify-center space-x-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>Cancelar</span>
                    </button>
                    <button @click="handleSearch" :disabled="!isFormValid"
                        :class="{ 'opacity-50 cursor-not-allowed': !isFormValid, 'hover:bg-green-700': isFormValid }"
                        class="px-4 py-2 flex items-center justify-center space-x-2 bg-green-950 border border-transparent rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Buscar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const props = defineProps({
    isOpen: Boolean,
    dataInicio: String,
    dataFim: String,
    agenciaModal: String,
    estadoModal: {
        type: Number,
        default: 0 // Valor padrão numérico
    }

})
const emit = defineEmits([
    'update:dataInicio',
    'update:dataFim',
    'update:estadoModal',
    'update:agenciaModal',
    'close',
    'search'
])

const startDate = ref(props.dataInicio || '')
const endDate = ref(props.dataFim || '')
const estadoModal = ref(props.estadoModal || 0)
const agenciaModal = ref(props.agenciaModal || '')
const dateError = ref('')






// Validação do formulário
const isFormValid = computed(() => {
    const datesValid = startDate.value && endDate.value && !dateError.value
    const selectsValid = estadoModal.value && agenciaModal.value
    return datesValid && selectsValid
})

// Validação das datas
const validateDates = () => {
    if (startDate.value && endDate.value) {
        if (new Date(startDate.value) > new Date(endDate.value)) {
            dateError.value = 'A data de início não pode ser maior que a data de fim'
            return false
        }
    }
    dateError.value = ''
    return true
}

// Watchers para validação
watch([startDate, endDate], () => {
    validateDates()
})

watch(() => props.dataInicio, (newVal) => {
    startDate.value = newVal || ''
})

watch(() => props.dataFim, (newVal) => {
    endDate.value = newVal || ''
})

watch(() => props.estadoModal, (newVal) => {
    estadoModal.value = newVal || ''
})

watch(() => props.agenciaModal, (newVal) => {
    agenciaModal.value = newVal || ''
})

const handleClose = () => {
    emit('close')
}

const handleSearch = () => {
    // Resetar erro anterior
    dateError.value = ''

    // Validação básica
    if (!startDate.value || !endDate.value) {
        dateError.value = 'Por favor, selecione ambas as datas'
        return
    }

    // Validação de datas
    if (new Date(startDate.value) > new Date(endDate.value)) {
        dateError.value = 'A data de início não pode ser maior que a data de fim'
        return
    }

    // Validação de estado/agência (se obrigatórios)
    if (!estadoModal.value || !agenciaModal.value) {
        dateError.value = 'Por favor, selecione o estado e a agência'
        return
    }

    // Emitir os dados
    emit('update:dataInicio', startDate.value)
    emit('update:dataFim', endDate.value)
    emit('update:estadoModal', estadoModal.value)
    emit('update:agenciaModal', agenciaModal.value)
    emit('search', {
        data_inicio: startDate.value,
        data_fim: endDate.value,
        estadoconsulta: estadoModal.value,
        agenciaconsulta: agenciaModal.value
    })
}


</script>
<style scoped>
.form-input,
.form-select {
    @apply border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors;
}
</style>
