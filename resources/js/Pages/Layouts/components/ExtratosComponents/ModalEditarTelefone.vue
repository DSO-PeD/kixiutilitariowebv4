<template>
    <Modal :show="show" max-width="md" @close="fecharModal">
        <div class="bg-white p-6 rounded-lg">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Editar Telefone</h3>
                <button @click="fecharModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="atualizarTelefone">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Loan Number</label>
                        <input type="text" :value="extratoSelecionado.Lnr" class="form-input mt-1 block w-full bg-gray-100" readonly />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cliente</label>
                        <input type="text" :value="extratoSelecionado.Cliente" class="form-input mt-1 block w-full bg-gray-100" readonly />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Telefone <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <img src="/imagens/angola.svg" class="h-5 w-6 object-contain" alt="AO">
                            </div>
                            <input v-model="telefoneEditado" @input="formatarTelefoneEditado" type="tel"
                                   class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="921502056" maxlength="9" required
                                   :class="{'border-red-500': erroTelefone}" />
                        </div>
                        <p v-if="erroTelefone" class="text-red-500 text-xs mt-1">O telefone deve ter exatamente 9 dígitos</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" @click="fecharModal" class="btn btn-outline-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
                        <svg v-if="isSubmitting" class="animate-spin h-4 w-4 mr-2 text-white" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"></path>
                        </svg>
                        {{ isSubmitting ? 'Atualizando...' : 'Atualizar' }}
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

const props = defineProps({
    show: Boolean,
    extratoSelecionado: Object
});

const emit = defineEmits(['close', 'telefoneAtualizado']);

const telefoneEditado = ref('');
const isSubmitting = ref(false);
const erroTelefone = ref(false);

// Formatador de telefone
const formatarTelefoneEditado = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    value = value.substring(0, 9);
    telefoneEditado.value = value;
    erroTelefone.value = value.length > 0 && value.length !== 9;
};

// Validar telefone
const validarTelefone = () => {
    if (telefoneEditado.value.length !== 9) {
        erroTelefone.value = true;
        return false;
    }
    erroTelefone.value = false;
    return true;
};

// Atualizar telefone
const atualizarTelefone = async () => {
    if (!validarTelefone()) return;

    isSubmitting.value = true;

    try {
        await router.post('/atualizar-telefone', {
            id: props.extratoSelecionado.Num,
            telefone: telefoneEditado.value
        }, {
            preserveScroll: true,
            onSuccess: () => {
                emit('telefoneAtualizado', telefoneEditado.value);
                fecharModal();
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

// Watch para quando o extrato selecionado mudar, atualizar o telefone editado
watch(() => props.extratoSelecionado, (newVal) => {
    if (newVal) {
        telefoneEditado.value = newVal.Telefone || '';
        erroTelefone.value = false;
    }
}, { immediate: true });
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
.dropdown-enter-active, .dropdown-leave-active {
    transition: opacity 0.2s, transform 0.2s;
}
.dropdown-enter-from, .dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Garantir que o dropdown fique acima de outros elementos */
.relative {
    position: relative;
}
</style>
