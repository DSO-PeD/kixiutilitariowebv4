<template>
    <header class="sticky top-0 z-10 bg-gradient-to-r from-green-900 to-green-800 shadow-lg px-4 md:px-6 py-3 flex items-center justify-between transition-all duration-300">
        <!-- Left side - Logo and Breadcrumb -->
        <div class="flex items-center space-x-4">
            <img :src="LogoKxCredito" alt="Logo KixiCrédito" class="h-8 w-auto object-contain hidden md:block" />

            <!-- Breadcrumb separator -->
            <div class="hidden md:block h-6 w-px bg-green-400/30"></div>

            <!-- Breadcrumb -->
            <nav class="hidden md:flex items-center space-x-2 text-sm">
                <span class="text-green-200">Sistema</span>
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-white font-medium">{{ getCurrentPageName() }}</span>
            </nav>
        </div>

        <!-- Right side - User area -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
                <button @click="toggleNotifications" class="relative p-2 text-green-200 hover:text-white hover:bg-green-800/50 rounded-lg transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span v-if="hasNotifications" class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 rounded-full border-2 border-green-900"></span>
                </button>

                <div v-if="notificationsOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-800">Notificações</h3>
                    </div>
                    <div class="max-h-60 overflow-y-auto">
                        <div class="px-4 py-3 hover:bg-gray-50">
                            <p class="text-sm text-gray-600">Nenhuma notificação no momento</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User dropdown -->
            <div class="relative" ref="dropdownRef">
                <button @click="toggleDropdown" class="flex items-center space-x-3 focus:outline-none group">
                    <div class="relative">
                        <div class="w-10 h-10 bg-green-700 rounded-full flex items-center justify-center text-white font-semibold text-sm group-hover:bg-green-600 transition-colors duration-200">
                            {{ getUserInitials() }}
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-green-900"></div>
                    </div>

                    <div class="hidden lg:block text-left">
                        <p class="text-sm font-medium text-white">{{ $page.props.user.UtNome }}</p>
                        <p class="text-xs text-green-300">{{ $page.props.user.UtEmail }}</p>
                    </div>

                    <svg class="w-4 h-4 text-green-300 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        :class="{ 'rotate-180': dropdownOpen }">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <!--
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1">
                    
                    <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl py-2 z-50 border border-gray-200">
                        
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-800">{{ $page.props.user.UtNome }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $page.props.user.UtEmail }}</p>
                        </div>
                        
                        <div class="py-2">
                            <a href="#" @click="showSMS" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Meu Perfil
                            </a>
                            <a href="#" @click="showSMS" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Configurações
                            </a>
                        </div>

                        <div class="border-t border-gray-100"></div>

                        <button @click="logout" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Sair
                        </button>
                    </div>
                </transition>-->
            </div>
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
                    <button @click="showModal = false" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200">
                        Cancelar
                    </button>
                    <button @click="confirmLogout" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                        Sair
                    </button>
                </div>
            </div>
        </Modal>

        <!-- Modal de Em Desenvolvimento -->
        <Modal :show="showModalSMS" @close="showModalSMS = false">
            <div class="p-6">
                <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-800 text-center mb-4">Funcionalidade em Desenvolvimento</h2>
                <p class="text-sm text-gray-600 text-center mb-6">Estará disponível em breve. Agradecemos sua compreensão.</p>
                <div class="flex justify-center">
                    <button @click="showModalSMS = false" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Entendi
                    </button>
                </div>
            </div>
        </Modal>
    </header>
</template>
<script setup>
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import Modal from './ModalExit.vue';
import LogoKxCredito from '../../../../../public/imagens/LogoKxCreditoTEla.png';

// Defina as props corretamente
const props = defineProps({
    sidebarCollapsed: {
        type: Boolean,
        default: false
    }
});


// Para Inertia.js, acesse as props através de usePage()
const page = usePage();
const user = computed(() => page.props.user);
const sidebarCollapsed = computed(() => page.props.sidebarCollapsed || false);

const dropdownOpen = ref(false);
const notificationsOpen = ref(false);
const showModal = ref(false);
const showModalSMS = ref(false);
const dropdownRef = ref(null);
const notificationsRef = ref(null);
const hasNotifications = ref(false);



// Fechar dropdown ao clicar fora
onClickOutside(dropdownRef, () => {
    dropdownOpen.value = false;
});

onClickOutside(notificationsRef, () => {
    notificationsOpen.value = false;
});

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
    notificationsOpen.value = false;
};

const toggleNotifications = () => {
    notificationsOpen.value = !notificationsOpen.value;
    dropdownOpen.value = false;
};

const showSMS = () => {
    showModalSMS.value = true;
    dropdownOpen.value = false;
};

const logout = () => {
    showModal.value = true;
    dropdownOpen.value = false;
};

const confirmLogout = () => {
    router.post('/logout');
};

// Helper functions - AGORA usando a computed property user
const getUserInitials = () => {
    const name = user.value?.UtNome || '';
    return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const getCurrentPageName = () => {
    const path = window.location.pathname;
    if (path === '/dashboard') return 'Dashboard';
    if (path.startsWith('/comprovativos')) return 'Comprovativos';
    if (path.startsWith('/reconciliacao')) return 'Reconciliação';
    if (path.startsWith('/extratos')) return 'Desembolsos';
    if (path.startsWith('/recuperacoes')) return 'Recuperações';
    if (path.startsWith('/referenciapgt')) return 'Referências de Pagamentos';
    return 'Sistema';
};




</script>

<style scoped>
header {
    height: 64px;
    backdrop-filter: blur(8px);
}

/* Smooth transitions for all interactive elements */
button, a {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover effects */
button:hover {
    transform: translateY(-1px);
}

/* Focus states for accessibility */
button:focus {
    outline: 2px solid #10b981;
    outline-offset: 2px;
}

/* Custom scrollbar for notifications */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for notification badge */
@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

.notification-badge {
    animation: pulse 2s infinite;
}

/* Gradient border for user avatar */
.avatar-gradient {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

/* Smooth shadow transitions */
.shadow-transition {
    transition: box-shadow 0.3s ease;
}

.shadow-transition:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    header {
        height: 56px;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .user-info {
        display: none;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .dropdown-content {
        background-color: #1f2937;
        border-color: #374151;
    }

    .dropdown-item {
        color: #e5e7eb;
    }

    .dropdown-item:hover {
        background-color: #374151;
    }
}
</style>
