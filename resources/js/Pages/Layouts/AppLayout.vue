<script setup>
import { ref } from 'vue'
import Header from './components/Header.vue'
import Sidebar from './components/Sidebar.vue'

const sidebarCollapsed = ref(false)
const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value
}

const props = defineProps({
    session: Object,
    // outros props que você esteja passando
});
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <Sidebar :collapsed="sidebarCollapsed" @toggle="toggleSidebar" />

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Passe a prop sidebarCollapsed para o Header -->
            <Header @toggle-sidebar="toggleSidebar" :sidebar-collapsed="sidebarCollapsed" />

            <main class="flex-1 overflow-y-auto">
                <!--div class="container mx-auto p-4 lg:p-6 transition-all duration-300" style="padding-right: 1rem;"
                    :style="{ 'padding-left': sidebarCollapsed ? '0.5rem' : 'calc(16rem + 1.5rem)' }"-->

                <div class="mx-auto p-4 lg:p-6 transition-all duration-300 pr-4"
                    :style="{ 'padding-left': sidebarCollapsed ? '2rem' : '15rem' }">
               <!--div class="w-full mx-auto px-2 sm:px-4 lg:px-6"-->
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
