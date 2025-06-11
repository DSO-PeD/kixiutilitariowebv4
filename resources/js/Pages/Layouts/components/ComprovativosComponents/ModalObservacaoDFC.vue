<template>
    <Modal :show="show" @close="closeModal" maxWidth="3xl">
        <!-- Cabeçalho -->
        <div class="bg-gradient-to-r from-green-900 to-greenkixi-300 to-green-950 text-white p-5 rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center">

                    <span class="drop-shadow-md">&ThinSpace;Observação do DCF</span>
                </h2>
                <button @click="closeModal" class="text-white/80 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Corpo do Modal -->
        <div class="p-6 space-y-6">
            <!-- Seção de Informações do Comprovativo -->
            <div class="bg-green-50 p-5 rounded-xl border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2 mt-0.5 text-greenkixi-solid" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                    Informações do Comprovativo
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">


                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loan Number</label>
                        <div class="relative">
                            <input v-model="form.lnr" type="text" class="form-input bg-gray-100 pl-3 pr-10" readonly />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fa-solid fa-hashtag text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Voucher</label>
                        <div class="relative">
                            <input v-model="form.voucher" type="text" class="form-input bg-gray-100 pl-3 pr-10"
                                readonly />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fa-solid fa-building text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montante</label>
                        <div class="relative">
                            <input v-model="form.montante" type="text"
                                class="form-input bg-gray-100 pl-3 pr-10 text-right" readonly />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"></div>
                        </div>
                    </div>
                </div>

                <div v-if="form.file" class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comprovativo</label>
                    <a :href="`/storage/comprovativos/${form.file}`" target="_blank"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-file-pdf text-red-500 mr-2"></i>
                        Visualizar Comprovativo
                    </a>
                </div>
            </div>

            <div class="bg-orange-50 p-5 rounded-xl border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">



                    <div class="flex flex-col">
                      <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">{{ form.observacao }}</h2>
                    </div>
                </div>

                <div v-if="form.file" class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comprovativo</label>
                    <a :href="`/storage/comprovativos/${form.file}`" target="_blank"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-gray-50 transition-colors">
                        <i class="fa-solid fa-file-pdf text-red-500 mr-2"></i>
                        Visualizar Comprovativo
                    </a>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" @click="closeModal" class="btn btn-secondary order-1 sm:order-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    &ThickSpace;
                    Cancelar
                </button>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watchEffect } from 'vue'
import Modal from '@/Components/Modal.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
    show: Boolean,
    comprovativoreconci: Object
})

const emit = defineEmits(['close', 'success'])

const form = ref({
    id: '',
    data: '',
    lnr: '',
    agencia: '',
    usuario: '',
    basedelacamento: '',
    metodologia: '',
    montante: '',
    file: '',
    banco: '',
    conta: ''
})

watchEffect(() => {
    if (props.comprovativoreconci) {
        form.value = {
            ...form.value,
            id: props.comprovativoreconci.id || '',
            data: props.comprovativoreconci.data || '',
            lnr: props.comprovativoreconci.lnr || '',
            agencia: props.comprovativoreconci.agencia || '',
            usuario: props.comprovativoreconci.usuario || '',
            basedelacamento: props.comprovativoreconci.basedelacamento || '',
            metodologia: props.comprovativoreconci.metodologia || '',
            montante: props.comprovativoreconci.montante || '',
            file: props.comprovativoreconci.file || '',
            banco: props.comprovativoreconci.banco || '',
            conta: props.comprovativoreconci.conta || '',
            referencia: props.comprovativoreconci.referencia || '',
            observacao: props.comprovativoreconci.observacao || '',

        }
    }
})

const resetForm = () => {
    form.value = {
        id: '',
        data: '',
        lnr: '',
        agencia: '',
        usuario: '',
        basedelacamento: '',
        metodologia: '',
        montante: '',
        file: '',
        banco: '',
        conta: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}
</script>

<style scoped>
.form-select,
.form-input {
    @apply border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm;
}

.btn {
    @apply px-5 py-2.5 rounded-lg font-medium transition-all flex items-center justify-center text-sm;
}

.btn-primary {
    @apply bg-gradient-to-r from-green-900 to-greenkixi-300 text-white hover:from-orange-400 hover:to-green-400 shadow-md hover:shadow-lg transform hover:-translate-y-0.5;
}

.btn-secondary {
    @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 shadow-sm hover:shadow-md;
}

.bg-greenkixi-300 {
    background-color: #08583d;
}

.text-greenkixi-solid {
    color: #08583d;
}

.to-greenkixi-300 {
    --tw-gradient-to: #08583d;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}
</style>
