<template>
    <header class="sticky top-0 z-10 bg-white shadow-sm px-6 py-4 flex justify-between items-center" :style="{
        'left': sidebarCollapsed ? '0' : '16rem',
        'width': sidebarCollapsed ? '100%' : 'calc(100% - 16rem)'
    }">
        <!--   ... (restante do header) ... -->



        <!-- Left side - Menu button -->
        <button @click="$emit('toggle-sidebar')" class="text-gray-600 hover:text-gray-900 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>

        <!-- Right side - User area -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button class="relative p-1 rounded-full hover:bg-gray-100">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                    class="w-6 h-6 text-gray-500">
                    <path
                        d="M19.006 3.705a.75.75 0 1 0-.512-1.41L6 6.838V3a.75.75 0 0 0-.75-.75h-1.5A.75.75 0 0 0 3 3v4.93l-1.006.365a.75.75 0 0 0 .512 1.41l16.5-6Z" />
                    <path fill-rule="evenodd"
                        d="M3.019 11.114 18 5.667v3.421l4.006 1.457a.75.75 0 1 1-.512 1.41l-.494-.18v8.475h.75a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5H3v-9.129l.019-.007ZM18 20.25v-9.566l1.5.546v9.02H18Zm-9-6a.75.75 0 0 0-.75.75v4.5c0 .414.336.75.75.75h3a.75.75 0 0 0 .75-.75V15a.75.75 0 0 0-.75-.75H9Z"
                        clip-rule="evenodd" />
                </svg>

                <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-green-300 rounded-full"> </span>

            </button>
            {{  $page.props.session.agencia_principal }} |
            <!-- User dropdown -->
            <div class="relative" ref="dropdownRef">
                <button @click="toggleDropdown" class="flex items-center space-x-2 focus:outline-none">

                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-8 h-8 rounded-full">
                        <path fill-rule="evenodd"
                            d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                            clip-rule="evenodd" />
                    </svg>

                    <span class="hidden md:inline text-gray-700 font-medium">Olá, {{ $page.props.user.UtNome }}</span>
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        :class="{ 'rotate-180': dropdownOpen }">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Meu Perfil</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configurações</a>
                    <div class="border-t border-gray-100"></div>
                    <button @click="logout"
                        class="block w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-gray-100">
                        Sair
                    </button>
                </div>
            </div>


        </div>



        <!-- Modal de Logout -->
        <Modal :show="showModal" @close="showModal = false">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Deseja realmente sair?</h2>
                <div class="flex justify-end space-x-3">
                    <button @click="showModal = false"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button @click="confirmLogout" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Sair
                    </button>
                </div>
            </div>
        </Modal>
    </header>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import Modal from './ModalExit.vue';

const dropdownOpen = ref(false);
const showModal = ref(false);
const dropdownRef = ref(null);
defineProps({
    sidebarCollapsed: Boolean
})
onClickOutside(dropdownRef, () => {
    dropdownOpen.value = false;
});

const toggleDropdown = () => {
    dropdownOpen.value = !dropdownOpen.value;
};

const closeDropdown = () => {
    dropdownOpen.value = false;
};

const logout = () => {
    showModal.value = true;
    dropdownOpen.value = false;
};

const confirmLogout = () => {
    router.post('/logout');
};

onClickOutside(dropdownRef, closeDropdown);
</script>
