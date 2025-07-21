<template>
    <div class="flex h-screen">
        <!-- Mini Sidebar (ícones sempre visíveis) -->
      <div class="w-16 fixed h-full bg-white border-r shadow-md z-30 transition-all duration-200"
         @mouseenter="expandMenu"
         @mouseleave="startCollapseTimer">

            <div class="p-4 border-b flex justify-center">
                <img src="/public/imagens/smalllogo.png" alt="Logo" class="h-10 w-10 object-contain rounded-full">
            </div>

            <nav class="mt-6 flex flex-col items-center space-y-4">
                <!-- Dashboard -->
                <a href="/dashboard" class="nav-link-icon" :class="{ 'active': $page.url === '/dashboard' }"
                    title="Dashboard">
                    <i class="fas fa-home text-lg"></i>
                </a>

                <!-- Reembolsos -->
                <a v-if="$page.props.user.rec_comprovativo" href="/comprovativos" class="nav-link-icon"
                    :class="{ 'active': $page.url.startsWith('/comprovativos') }" title="Reembolsos">
                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                </a>

                <!-- Reconciliação -->
                <a v-if="$page.props.user.reconci_habilita" href="/reconciliacao" class="nav-link-icon"
                    :class="{ 'active': $page.url.startsWith('/reconciliacao') }" title="Reconciliação">
                    <i class="fas fa-exchange-alt text-lg"></i>
                </a>

                <!-- Desembolsos -->
                <a v-if="$page.props.user.rec_extrato" href="/extratos" class="nav-link-icon"
                    :class="{ 'active': $page.url.startsWith('/extratos') }" title="Desembolsos">
                    <i class="fas fa-money-bill-wave text-lg"></i>
                </a>

                <!-- Recuperações -->
                <a v-if="$page.props.user.rec_registra" href="/recuperacoes" class="nav-link-icon"
                    :class="{ 'active': $page.url.startsWith('/recuperacoes') }" title="Recuperações">
                    <i class="fas fa-hand-holding-usd text-lg"></i>
                </a>

                  <!-- Tesouraria
                <a v-if="$page.props.user.mn_tesouraria" href="/tesouraria" class="nav-link-icon"
                    :class="{ 'active': $page.url.startsWith('/tesouraria') }" title="Tesouria">
                       <i class="fas fa-cash-register text-lg"></i>

                </a>-->

                <!-- Configurações
                <a href="/configuracoes" class="nav-link-icon"
                    :class="{ 'active': $page.url.startsWith('/configuracoes') }" title="Configurações">
                    <i class="fas fa-cog text-lg"></i>
                </a>-->
            </nav>
        </div>

        <!-- Expanded Sidebar -->
       <aside class="w-56 fixed h-full bg-white border-r shadow-md transition-all duration-200 ease-in-out z-20 ml-16"
           :class="{ 'translate-x-0': isExpanded, '-translate-x-full': !isExpanded }"
           @mouseenter="keepExpanded"
           @mouseleave="startCollapseTimer">



            <nav class="mt-4 px-2 space-y-1">
                <div class="p-4 border-b">
                    <h2 class="text-xl font-semibold text-gray-800 text-center">Operações</h2>
                </div>
                <!-- Dashboard -->
                <a href="/dashboard" class="nav-link" :class="{ 'active': $page.url === '/dashboard' }">
                    <i class="fas fa-home mr-3"></i>
                    <span>Visão Geral</span>
                </a>

                <!-- Reembolsos -->
                <a v-if="$page.props.user.rec_comprovativo" href="/comprovativos" class="nav-link"
                    :class="{ 'active': $page.url.startsWith('/comprovativos') }">
                    <i class="fas fa-file-invoice-dollar mr-3"></i>
                    <span>Comprovativos</span>
                </a>

                <!-- Reconciliação-->
                <a v-if="$page.props.user.reconci_habilita" href="/reconciliacao" class="nav-link"
                    :class="{ 'active': $page.url.startsWith('/reconciliacao') }">
                    <i class="fas fa-exchange-alt mr-3"></i>
                    <span>Reconciliação</span>
                </a>

                <!-- Desembolsos -->
                <a v-if="$page.props.user.rec_extrato" href="/extratos" class="nav-link"
                    :class="{ 'active': $page.url.startsWith('/extratos') }">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    <span>Desembolsos</span>
                </a>

                <!-- Recuperações -->
                <a v-if="$page.props.user.rec_registra" href="/recuperacoes" class="nav-link"
                    :class="{ 'active': $page.url.startsWith('/recuperacoes') }">
                    <i class="fas fa-hand-holding-usd mr-3"></i>
                    <span>Recuperações</span>
                </a>



                  <!-- Tesouraria
                <a v-if="$page.props.user.mn_tesouraria"  href="/tesouraria" class="nav-link"
                    :class="{ 'active': $page.url.startsWith('/tesouraria') }">

                    <i class="fas fa-cash-register mr-3"></i>
                    <span>Tesouraria</span>
                </a> -->

                <!-- Configurações
                <a href="#" class="nav-link" :class="{ 'active': $page.url.startsWith('/configuracoes') }">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Configurações</span>
                </a>-->
            </nav>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 transition-all duration-200 min-h-screen"
            :class="{ 'ml-1': !isExpanded, 'ml-1': isExpanded }">
            <div class="p-6">
                <slot />
            </div>
        </main>


    </div>
</template>

<script setup>
import { ref } from 'vue';

const isExpanded = ref(false);
let collapseTimer = null;

// Emitir evento quando a sidebar expandir/recolher
const emit = defineEmits(['expand'])

const expandMenu = () => {
    clearTimeout(collapseTimer);
    isExpanded.value = true;
    emit('expand', true)
};

const startCollapseTimer = () => {
    collapseTimer = setTimeout(() => {
        isExpanded.value = false
        emit('expand', false)
    }, 200)
};

const keepExpanded = () => {
    clearTimeout(collapseTimer)
};
</script>

<style scoped>
/* Estilo para os ícones na barra mini */
.nav-link-icon {
    @apply flex items-center justify-center p-3 text-gray-500 hover:text-orange-600 hover:bg-orange-50 transition-all duration-200 rounded-lg mx-auto w-10 h-10;
}

.nav-link-icon.active {
    @apply bg-orange-100 text-orange-600;
}

.nav-link-icon:hover {
    @apply transform scale-110;
}

/* Estilo para os itens na barra expandida */
.nav-link {
    @apply flex items-center px-3 py-2.5 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all duration-200;
}

.nav-link.active {
    @apply bg-orange-100 text-orange-600 font-medium;
}

/* Adicione isso no seu arquivo principal CSS ou no cabeçalho */
</style>
