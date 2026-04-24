<template>

    <Head title="Declarações" />

    <div class="container mx-auto py-6 max-w-full">
        <!-- Alertas de notificação -->
        <div v-if="$page.props.flash.success" class="alert alert-success mb-4">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $page.props.flash.success }}
            </div>
        </div>

        <div v-if="$page.props.flash.error" class="alert alert-danger mb-4">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ $page.props.flash.error }}
            </div>
        </div>

        <div v-if="$page.props.errors && Object.keys($page.props.errors).length" class="alert alert-danger mb-4">
            <div v-for="(messages, field) in $page.props.errors" :key="field">
                <div v-for="message in messages" :key="message">
                    {{ message }}
                </div>
            </div>
        </div>

        <!-- Cabeçalho -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-file text-2xl text-green-800"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        Ver Requisição
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Solicitação de Declarações Negativas</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="/declacaongtv"
                    class="border border-purple-600 text-purple-600 hover:bg-purple-700 hover:text-white p-2 rounded-lg flex items-center gap-2"
                    @click="abrirModalGerarRefManual">
                    <i class="fas fa-list text-purple-600 text-xl"></i>
                    Listar Requisições
                </a>
            </div>
        </div>

        <div class="border-t border-gray-200 my-4"></div>

        <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <div class="p-4">
                    <div class="grid grid-cols-12 gap-5 pb-4">
                        <div class="lg:col-span-8 col-span-8">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Detalhes da Requisição</h2>
                        </div>
                        <div class="flex justify-end lg:col-span-4 col-span-4 gap-2">
                            <button @click="abrirModalGerarRefManual"
                                class="bg-orange-400 hover:bg-orange-500 text-white h-10 px-4 rounded-lg gap-2">
                                <i class="fas fa-times text-white"></i>
                                Recusar
                            </button>
                            <button @click="window.print()"
                                class="bg-green-600 hover:bg-green-700 text-white h-10 px-4 rounded-lg gap-2">
                                <i class="fas fa-check text-white"></i>
                                Aprovar
                            </button>
                            <button @click="window.print()"
                                class="bg-green-600 hover:bg-green-700 text-white h-10 px-4 rounded-lg gap-2">
                                <i class="fas fa-print text-white"></i>
                                Imprimir
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-12 gap-5">
                        <div class="lg:col-span-7 col-span-7 bg-gray-100 p-2 rounded-lg">
                            <div class="grid grid-cols-12 gap-5">
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">LNR:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.lnr }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Saving:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.saving }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Estado:</p>
                                    <span :class="declaracao.color"
                                        class="px-2 mt-2 rounded-full text-base font-medium">{{
                                            declaracao.descricao_estado }}</span>
                                </div>
                                <div class="lg:col-span-12 col-span-12">
                                    <p class="text-sm text-gray-600">Nome:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.nome }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Documento:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.documento }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Telefone:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.telefone }}</p>
                                </div>
                                <div class="lg:col-span-3 col-span-3">
                                    <p class="text-sm text-gray-600">Banco:</p>
                                    <p class="text-base font-medium text-gray-800">{{ declaracao.BaNome }}</p>
                                </div>

                            </div>
                        </div>
                        <div class="lg:col-span-5 col-span-5 bg-gray-100 p-2 rounded-lg">
                            <iframe v-if="declaracao" :src="declaracao.ficheiro"
                                class="mt-4 w-full h-64 border rounded-lg">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <ModalSolicitarDeclaracao ref="modalGerarDeclaracao" v-if="showModalDeclaracao" @close="fecharModalDeclaracao"
         />
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import ModalSolicitarDeclaracao from './Layouts/components/ModalSolicitarDeclaracao.vue'

// Props
const props = defineProps({
    declaracao: Object,
})

// Modal Recusar
const showModalDeclaracao = ref(false)
const modalGerarDeclaracao = ref(null)

const abrirModalGerarRefManual = () => {
    showModalDeclaracao.value = true
}

const fecharModalDeclaracao = () => showModalDeclaracao.value = false

const guardarDeclaracao = async (formValue) => {
    try {

        const formData = new FormData()
        Object.entries(formValue).forEach(([key, value]) => {
            if (value) formData.append(key, value)
        })

        await router.post('/guardar-declaracao', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onSuccess: () => {
                fecharModalDeclaracao();
            }
        })
    } catch (error) {
        console.error('Erro ao gerar referência:', error)
    }
}

</script>