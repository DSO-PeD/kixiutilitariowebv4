<template>
    <Modal :show="show" max-width="md" @close="fecharModal">
        <div class="bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Registar Desembolso</h3>
                <button @click="fecharModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="desembolsarCredito">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Data Desembolso (Lpf) <span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="fa fa-calendar-check text-gray-400"></i>
                            </div>
                            <input v-model="dataDesembolso" type="date" :max="today"
                                class="block w-full pl-8 pr-3 py-1.5 mt-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="fecharModal" class="btn btn-outline-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <svg v-if="isSubmitting" class="animate-spin h-4 w-4 mr-2 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"></path>
                        </svg>
                        {{ isSubmitting ? 'Desembolsando...' : 'Desembolsar' }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

//Pega a data de hoje
const today = new Date().toISOString().split("T")[0];

const props = defineProps({
    show: Boolean,
    extratoSelecionado: Object
});

const emit = defineEmits(['close', 'afterDesembolso']);

const dataDesembolso = ref('');
const isSubmitting = ref(false);

// Desembolsar
const desembolsarCredito = async () => {
   
    isSubmitting.value = true;

    try {
        await router.post('/desembolsar-credito', {
            id: props.extratoSelecionado.Num,
            dataDesembolso: dataDesembolso.value
        }, {
            preserveScroll: true,
            onSuccess: () => {
                emit('afterDesembolso', dataDesembolso.value);
                fecharModal();
                dataDesembolso.value = '';
            },
            onError: (errors) => {
                console.error('Erro ao atualizar telefone:', errors);
            }
        });
    } catch (error) {
        console.error('Erro inesperado:', error);
    } finally {
        isSubmitting.value = false;
    }
};

// Fechar modal
const fecharModal = () => {
    emit('close');
};

</script>
<style scoped>
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

/* Animações */
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

/* Estilos para o dropdown de busca */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: opacity 0.2s, transform 0.2s;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Garantir que o dropdown fique acima de outros elementos */
.relative {
    position: relative;
}
</style>
