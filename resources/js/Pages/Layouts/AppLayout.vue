<template>


    <div class="flex h-screen bg-gray-50">

        <!-- Sidebar fixa -->
        <Sidebar />

        <!-- Conteúdo principal -->
        <div class="flex-1 flex flex-col overflow-hidden "> <!-- ml-16 para a mini sidebar -->
            <Header />

            <main class="flex-1 overflow-y-auto transition-all duration-300" :class="{ 'ml-40': isExpanded }">
                <!-- ml-40 quando expandido -->
                <div class="p-20">
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

defineExpose({
    handleSidebarExpand
})
</script>
