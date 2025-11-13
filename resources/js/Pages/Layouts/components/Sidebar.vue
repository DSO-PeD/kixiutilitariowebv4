<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import SmallLogo from '../../../../../public/imagens/smalllogo.png';
import Modal from './ModalExit.vue';

const isExpanded = ref(false);
const windowWidth = ref(0);
let collapseTimer = null;

// Emitir evento quando a sidebar expandir/recolher
const emit = defineEmits(['expand'])

// Atualizar largura da janela
const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth;
};

// Inicializar e monitorar resize
onMounted(() => {
    windowWidth.value = window.innerWidth;
    window.addEventListener('resize', updateWindowWidth);
});

onUnmounted(() => {
    window.removeEventListener('resize', updateWindowWidth);
});

const expandMenu = () => {
    clearTimeout(collapseTimer);
    isExpanded.value = true;
    emit('expand', true);
};

const startCollapseTimer = () => {
    collapseTimer = setTimeout(() => {
        isExpanded.value = false;
        emit('expand', false);
    }, 300);
};

const keepExpanded = () => {
    clearTimeout(collapseTimer);
};

// Fechar sidebar ao clicar fora (para mobile)
const closeSidebar = () => {
    if (windowWidth.value < 1024) {
        isExpanded.value = false;
        emit('expand', false);
    }
};

const showModal = ref(false);
const dropdownOpen = ref(false);

const logout = () => {
    showModal.value = true;
    dropdownOpen.value = false;
};

const confirmLogout = () => {
    router.post('/logout');
};
</script>

<template>
    <!-- Overlay para mobile -->
    <div v-if="isExpanded && windowWidth < 1024" class="fixed inset-0 bg-black bg-opacity-50 z-10 lg:hidden"
        @click="closeSidebar"></div>

    <!-- Mini Sidebar -->
    <div class="w-16 fixed h-full bg-white border-r border-gray-200 shadow-sm z-30 transition-all duration-200"
        @mouseenter="expandMenu" @mouseleave="startCollapseTimer">

        <div class="p-4 border-b border-gray-100 flex justify-center">
            <img :src="SmallLogo" alt="Logo"
                class="h-8 w-8 object-contain transition-transform duration-200 hover:scale-110" />
        </div>

        <nav class="mt-6 flex flex-col items-center space-y-4 px-2">
            <!-- Itens do menu (mantenha igual) -->
            <a href="/dashboard" class="nav-link-icon group" :class="{ 'active': $page.url === '/dashboard' }"
                data-preload title="Dashboard">
                <i class="fas fa-home text-lg"></i>
                <span class="nav-tooltip">Dashboard</span>
            </a>
            <!-- ... outros itens ... -->
            <!-- Reembolsos -->
            <a v-if="$page.props.user.rec_comprovativo" href="/comprovativos" class="nav-link-icon group" data-preload
                :class="{ 'active': $page.url.startsWith('/comprovativos') }" title="Reembolsos">
                <i class="fas fa-file-invoice-dollar text-lg"></i>
                <span class="nav-tooltip">Reembolsos</span>
            </a>

            <!-- Reconciliação -->
            <a v-if="$page.props.user.reconci_habilita" href="/reconciliacao" class="nav-link-icon group" data-preload
                :class="{ 'active': $page.url.startsWith('/reconciliacao') }" title="Reconciliação">
                <i class="fas fa-exchange-alt text-lg"></i>
                <span class="nav-tooltip">Reconciliação</span>
            </a>

            <!-- Desembolsos -->
            <a v-if="$page.props.user.rec_extrato" href="/extratos" class="nav-link-icon group" data-preload
                :class="{ 'active': $page.url.startsWith('/extratos') }" title="Desembolsos">
                <i class="fas fa-money-bill-wave text-lg"></i>
                <span class="nav-tooltip">Desembolsos</span>
            </a>

            <!-- Recuperações -->
            <a v-if="$page.props.user.rec_registra" href="/recuperacoes" class="nav-link-icon group" data-preload
                :class="{ 'active': $page.url.startsWith('/recuperacoes') }" title="Recuperações">
                <i class="fas fa-hand-holding-usd text-lg"></i>
                <span class="nav-tooltip">Recuperações</span>
            </a>

            <!-- Referencia de Pagamentos -->
            <a v-if="$page.props.user.mn_referenciapagamento" href="/referenciapgt" class="nav-link-icon group"
                data-preload :class="{ 'active': $page.url.startsWith('/referenciapgt') }" title="Referências de PGT">
                <i class="fas fa-credit-card text-lg"></i>
                <span class="nav-tooltip">Referências de PGT</span>
            </a>

               <!-- Clientes Kixi Corp -->
            <a v-if="$page.props.user.mn_referenciapagamento" href="/ClienteCorp" class="nav-link-icon group"
                data-preload :class="{ 'active': $page.url.startsWith('/ClienteCorp') }" title="Cliente">
                <i class="fas fa-user-tie  text-lg"></i>

                <span class="nav-tooltip">Cliente</span>
            </a>

        </nav>
        <div class="absolute bottom-0 left-0 right-0 py-4 border-t border-gray-100 bg-gray-50">
            <div class="text-center px-2">
                <button @click="logout"
                    class="flex items-center w-full px-4 py-2 text-lg text-red-600 hover:bg-red-200 rounded-lg transition-colors duration-150">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </div>
        </div>

        <!-- Indicador visual de expansão -->
        <div class="absolute top-1/2 -right-1.5 w-3 h-8 bg-orange-500 rounded-l-lg opacity-0 transition-opacity duration-200"
            :class="{ 'opacity-100': isExpanded }"></div>
    </div>

    <!-- Modal de Logout -->
    <Modal :show="showModal" @close="showModal = false">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800 text-center mb-2">Deseja realmente sair?</h2>
            <p class="text-sm text-gray-600 text-center mb-6">Você será desconectado do sistema</p>
            <div class="flex justify-center space-x-3">
                <button @click="showModal = false"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    Cancelar
                </button>
                <button @click="confirmLogout"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                    Sair
                </button>
            </div>
        </div>
    </Modal>

    <!-- Expanded Sidebar -->
    <aside
        class="w-64 fixed h-full bg-white border-r border-gray-200 shadow-lg transition-all duration-300 ease-out z-20 ml-16"
        :class="{
            'translate-x-0 opacity-100': isExpanded,
            '-translate-x-full opacity-0': !isExpanded
        }" @mouseenter="keepExpanded" @mouseleave="startCollapseTimer">

        <div class="p-5 border-b border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 text-center">Operações do Sistema</h2>
            <p class="text-xs text-gray-500 text-center mt-1">Gestão Financeira</p>
        </div>

        <nav class="mt-4 px-3 space-y-1">
            <!-- Itens do menu expandido (mantenha igual) -->
            <a href="/dashboard" class="nav-link group" :class="{ 'active': $page.url === '/dashboard' }" data-preload>
                <div class="nav-link-content">
                    <i class="fas fa-home nav-link-icon"></i>
                    <span class="nav-link-text">Visão Geral</span>
                </div>
                <div class="nav-link-indicator"></div>
            </a>
            <!-- ... outros itens ... -->
            <!-- Reembolsos -->
            <a v-if="$page.props.user.rec_comprovativo" href="/comprovativos" class="nav-link group" data-preload
                :class="{ 'active': $page.url.startsWith('/comprovativos') }">
                <div class="nav-link-content">
                    <i class="fas fa-file-invoice-dollar nav-link-icon"></i>
                    <span class="nav-link-text">Comprovativos</span>
                </div>
                <div class="nav-link-indicator"></div>
            </a>

            <!-- Reconciliação-->
            <a v-if="$page.props.user.reconci_habilita" href="/reconciliacao" class="nav-link group" data-preload
                :class="{ 'active': $page.url.startsWith('/reconciliacao') }">
                <div class="nav-link-content">
                    <i class="fas fa-exchange-alt nav-link-icon"></i>
                    <span class="nav-link-text">Reconciliação</span>
                </div>
                <div class="nav-link-indicator"></div>
            </a>

            <!-- Desembolsos -->
            <a v-if="$page.props.user.rec_extrato" href="/extratos" class="nav-link group" data-preload
                :class="{ 'active': $page.url.startsWith('/extratos') }">
                <div class="nav-link-content">
                    <i class="fas fa-money-bill-wave nav-link-icon"></i>
                    <span class="nav-link-text">Desembolsos</span>
                </div>
                <div class="nav-link-indicator"></div>
            </a>

            <!-- Recuperações -->
            <a v-if="$page.props.user.rec_registra" href="/recuperacoes" class="nav-link group" data-preload
                :class="{ 'active': $page.url.startsWith('/recuperacoes') }">
                <div class="nav-link-content">
                    <i class="fas fa-hand-holding-usd nav-link-icon"></i>
                    <span class="nav-link-text">Recuperações</span>
                </div>
                <div class="nav-link-indicator"></div>
            </a>


            <!-- Referência de Pagamento -->
            <a v-if="$page.props.user.mn_referenciapagamento" href="/referenciapgt" class="nav-link group" data-preload
                :class="{ 'active': $page.url.startsWith('/referenciapgt') }">
                <div class="nav-link-content">
                    <i class="fas fa-credit-card nav-link-icon"></i>
                    <span class="nav-link-text">Referências de PGT.</span>
                </div>
                <div class="nav-link-indicator"></div>
            </a>

            <!-- Cliente Kixi_Corp -->
            <a v-if="$page.props.user.mn_referenciapagamento" href="/ClienteCorp" class="nav-link group" data-preload
                :class="{ 'active': $page.url.startsWith('/ClienteCorp') }">
                <div class="nav-link-content">
                    <i class="fas fa-user-tie nav-link-icon"></i>
                    <span class="nav-link-text">Cliente Corp</span>
                </div>
                <div class="nav-link-indicator"></div>
            </a>

        </nav>

        <!-- Footer da sidebar -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-100 bg-gray-50">
            <div class="text-center">
                <div class="text-center">
                    <button @click="logout"
                        class="flex items-center w-full px-4 py-2 rounded-md text-md text-red-600 hover:bg-red-50 transition-colors duration-150">
                        <i class="fa-solid fa-right-from-bracket mr-2"></i> Sair
                    </button>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
/* Sistema de cores */
:root {
    --color-primary: #08583d;
    --color-primary-light: #0c7a5a;
    --color-accent: #f97316;
    --color-accent-light: #ffedd5;
    --color-gray-100: #f8fafc;
    --color-gray-200: #e2e8f0;
    --color-gray-600: #475569;
    --color-gray-800: #1e293b;
}

/* Estilo para os ícones na barra mini */
.nav-link-icon {
    @apply relative flex items-center justify-center p-3 text-gray-500 hover:text-white hover:bg-orange-500 transition-all duration-200 rounded-xl mx-auto w-12 h-12;
}

.nav-link-icon.active {
    @apply bg-orange-500 text-white shadow-lg;
}

.nav-link-icon:hover {
    @apply transform scale-105 shadow-md;
}

/* Tooltip para ícones */
.nav-tooltip {
    @apply absolute left-full ml-3 px-2 py-1 bg-gray-900 text-white text-xs rounded-md opacity-0 invisible transition-all duration-200 group-hover:opacity-100 group-hover:visible group-hover:translate-x-0 translate-x-1;
    z-index: 40;
}

/* Estilo para os itens na barra expandida */
.nav-link {
    @apply relative flex items-center justify-between px-4 py-3 text-gray-600 hover:text-orange-600 hover:bg-orange-50 rounded-xl transition-all duration-200;
}

.nav-link.active {
    @apply bg-orange-100 text-orange-600 font-semibold;
}

.nav-link-content {
    @apply flex items-center;
}

.nav-link-icon {
    @apply w-5 h-5 mr-3 transition-colors duration-200;
}

.nav-link-text {
    @apply transition-all duration-200;
}

.nav-link-indicator {
    @apply w-1.5 h-1.5 bg-orange-500 rounded-full opacity-0 transition-opacity duration-200;
}

.nav-link.active .nav-link-indicator {
    @apply opacity-100;
}

.nav-link:hover {
    @apply transform translate-x-1;
}

/* Animações suaves */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Sombras suaves */
.shadow-sm {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Responsividade */
@media (max-width: 1023px) {
    .nav-link-icon {
        @apply w-10 h-10;
    }

    aside {
        width: 280px;
    }

    main {
        margin-left: 4rem;
    }
}

@media (max-width: 640px) {
    .nav-link-icon {
        @apply w-9 h-9;
    }

    aside {
        width: 260px;
    }
}

/* Melhorias de acessibilidade */
.nav-link:focus,
.nav-link-icon:focus {
    @apply outline-none ring-2 ring-orange-500 ring-offset-2;
}

/* Animações de entrada */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.nav-link {
    animation: slideIn 0.3s ease-out;
}

/* Efeito de profundidade */
.nav-link-icon {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.nav-link-icon:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.nav-link-icon.active {
    box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
}
</style>
