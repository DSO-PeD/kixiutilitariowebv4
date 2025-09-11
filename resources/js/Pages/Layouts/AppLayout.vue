<!-- Layout.vue -->
<template>
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar fixa -->
        <Sidebar @expand="handleSidebarExpand" />

        <!-- Conteúdo principal -->
        <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300 ml-16"
            :class="{ 'lg:ml-64': isExpanded }">

            <Header :sidebar-collapsed="!isExpanded" />

            <main class="flex-1 overflow-y-auto">
                <div class="p-4 sm:p-6">
                    <slot />
                </div>
            </main>

            <Footer />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import Header from './components/Header.vue'
import Sidebar from './components/Sidebar.vue'
import Footer from './components/Footer.vue'

const isExpanded = ref(false)

// Função para receber o estado da sidebar
const handleSidebarExpand = (expanded) => {
    isExpanded.value = expanded
}
</script>
