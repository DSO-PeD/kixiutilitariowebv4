<template>
    <div
        class="fixed inset-0 bg-black/40 backdrop-blur-md z-50 flex justify-center items-center p-4 transition-opacity duration-300">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[95vh] overflow-y-auto transform transition-all duration-300 scale-95 hover:scale-100 border border-gray-100">
            <!-- Cabeçalho Modernizado -->
            <div
                class="bg-gradient-to-r from-purple-600 to-purple-800 text-white p-6 rounded-t-2xl sticky top-0 z-10 shadow-lg">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-2 rounded-xl">
                            <i class="fas fa-times-circle text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold drop-shadow-sm">Recusar a Requisição</h3>
                        </div>
                    </div>
                    <button @click="$emit('close')"
                        class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition-all duration-200 group">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-5 group-hover:scale-110 transition-transform">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Corpo do Modal -->
            <div class="p-8 bg-gray-50/50">

                <div v-if="Object.keys($page.props.errors || {}).length"
                    class="p-2  text-red-800 border-t-4 border-red-300 bg-red-50 mb-4">
                    <ul class="list-disc ml-5">
                        <li v-for="(messages, field) in $page.props.errors" :key="field">
                            <span v-for="message in messages" :key="message">
                                {{ message }}
                            </span>
                        </li>
                    </ul>
                </div>

                <form @submit.prevent="confirmSubmission" class="space-y-8">

                    <div class="grid grid-cols-12 gap-5">
                        <!-- Loan Number -->
                        <div class="lg:col-span-12 col-span-12">
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                <i class="fas fa-question-circle mr-2 text-gray-500 text-xs"></i>
                                Comentário de Recusa
                            </label>
                            <div class="relative group">
                                <textarea v-model="formData.comentario" maxlength="250" rows="4" required
                                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                    placeholder="Digite o seu comentário..."></textarea>

                                <div class="text-right text-sm text-gray-500 mt-1">
                                    {{ formData.comentario.length }}/250
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="flex flex-col sm:flex-row justify-end gap-4 pt-8 border-t border-gray-200">
                        <button type="button" @click="$emit('close')"
                            class="btn btn-secondary order-1 sm:order-none group">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-5 group-hover:scale-110 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            &ThickSpace;
                            <span class="group-hover:translate-x-0.5 transition-transform">Cancelar</span>
                        </button>
                        <button type="submit"
                            class="btn btn-primary flex items-center justify-center group relative overflow-hidden">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-purple-600 to-purple-700 group-hover:from-purple-700 group-hover:to-purple-800 transition-all">
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="size-5 relative z-10 mr-2 group-hover:scale-110 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                            </svg>
                            &ThickSpace;
                            <span class="relative z-10 font-semibold">
                                <span v-if="!isSubmitting"
                                    class="group-hover:translate-y-0.5 transition-transform">Guardar</span>
                                <span v-else class="flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i> Processando...
                                </span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, watch, nextTick, computed, onMounted } from 'vue';

// Props
const props = defineProps({
    bancos: Array,
});

const emit = defineEmits(['close', 'save']);

const formData = ref({
    comentario: '',
});

const isSubmitting = ref(false);

// Confirmar submissão
const confirmSubmission = () => {
    isSubmitting.value = true;

    try {
        emit('save', formData.value);
    } catch (error) {
        console.error('Erro durante a confirmação:', error);
        showGeneralError('Ocorreu um erro ao processar a referência');
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<style scoped>
/* Estilos melhorados */
.border-red-500 {
    border-color: #ef4444 !important;
    border-width: 1px !important;
}

.form-input,
.form-select,
.form-textarea {
    @apply border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 shadow-sm;
}

.btn {
    @apply px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center text-sm;
}

.btn-primary {
    @apply bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 relative overflow-hidden;
}

.btn-secondary {
    @apply bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 hover:border-gray-400 shadow-sm hover:shadow-md;
}

.btn-success {
    @apply bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 relative overflow-hidden;
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

.drag-active {
    @apply border-blue-500 bg-blue-50/50;
}

.form-input.border-red-500 {
    border-color: #ef4444;
    box-shadow: 0 0 0 1px #ef4444;
}

.text-red-600 {
    color: #dc2626;
}

/* Melhorias de responsividade */
@media (max-width: 768px) {
    .grid-cols-3 {
        grid-template-columns: 1fr;
    }

    .p-8 {
        padding: 1.5rem;
    }

    .text-2xl {
        font-size: 1.5rem;
    }

    .py-5 {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
}

/* Efeitos de hover suaves */
.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:translate-x-0\.5 {
    transform: translateX(0.125rem);
}

.group:hover .group-hover\:translate-y-0\.5 {
    transform: translateY(0.125rem);
}

/* Gradientes e sombras melhorados */
.shadow-lg {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.shadow-xl {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
