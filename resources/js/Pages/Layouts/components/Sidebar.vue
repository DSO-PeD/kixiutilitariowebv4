<template>
    <aside class="w-64 fixed h-full bg-white border-r shadow-md transition-all duration-300"
        :class="{ '-translate-x-full': collapsed, 'translate-x-0': !collapsed }">

        <div class="p-6 border-b">
            <img src="/public/imagens/lgkxu.jpg" alt="Logo Utilitario" class="h-16 object-contain mx-auto" />
        </div>

        <nav class="mt-6 px-4">
            <a href="/dashboard" class="nav-link" :class="{ 'active': $page.url === '/dashboard' }">
                <svg class="h-6 w-6 mr-2 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                <span class="ml-3">Dashboard</span>
            </a>

            <a v-if="$page.props.user.rec_comprovativo" href="/comprovativos" class="nav-link"
                :class="{ 'active': $page.url.startsWith('/comprovativos') }">
                <svg class="h-6 w-6 mr-2 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                <span class="ml-3">Reembolsos</span>
            </a>

            <a v-if="$page.props.user.reconci_habilita" href="/reconciliacao" class="nav-link"
                :class="{ 'active': $page.url.startsWith('/reconciliacao') }">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
                </svg>
                <span class="ml-3">Reconciliação</span>
            </a>

            <a v-if="$page.props.user.rec_extrato" href="/extratos" class="nav-link"
                :class="{ 'active': $page.url.startsWith('/extratos') }">
                <svg class="h-6 w-6 mr-2 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <span class="ml-3">Desembolsos</span>
            </a>

            <a v-if="$page.props.user.rec_registra" href="/recuperacoes" class="nav-link"
                :class="{ 'active': $page.url.startsWith('/recuperacoes') }">
                <svg class="h-6 w-6 mr-2 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504 1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                <span class="ml-3">Recuperações</span>
            </a>

            <a href="/configuracoes" class="nav-link" :class="{ 'active': $page.url.startsWith('/configuracoes') }">
                <svg class="h-6 w-6 mr-2 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M4.5 12a7.5 7.5 0 0 0 15 0m-15 0a7.5 7.5 0 1 1 15 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077 1.41-.513m14.095-5.13 1.41-.513M5.106 17.785l1.15-.964m11.49-9.642 1.149-.964M7.501 19.795l.75-1.3m7.5-12.99.75-1.3m-6.063 16.658.26-1.477m2.605-14.772.26-1.477m0 17.726-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205 12 12m6.894 5.785-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                </svg>
                <span class="ml-3">Configurações</span>
            </a>
        </nav>

        <!-- Collapse Button -->
        <button @click="$emit('toggle')"
            class="absolute -right-3 top-6 bg-white p-1 rounded-full shadow-md border border-gray-200 hover:bg-gray-100 focus:outline-none">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                :class="{ 'rotate-180': collapsed }">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
    </aside>
</template>

<script setup>
defineProps({
    collapsed: Boolean
});

defineEmits(['toggle']);
</script>

<style scoped>
.nav-link {
    @apply flex items-center px-4 py-3 text-gray-600 hover:bg-green-50 hover:text-blue-600 transition duration-200 rounded-lg mb-1;
}

.nav-link.active {
    @apply bg-gray-100 text-green-900 font-black;
}

.to-greenkixi-300 {
    --tw-gradient-to: #08583d var(--tw-gradient-to-position);
}




/* Mantenha as cores originais para os ícones e textos */
.nav-link .text-greenkixi-solid,
.nav-link.active .text-greenkixi-solid {
    color: #08583d;
}

/* Se quiser que os ícones também mudem de cor quando ativo */
.nav-link.active svg {
    @apply text-green-900;
}

.nav-link.active .text-greenkixi-solid {
    @apply text-green-900;
}


</style>
