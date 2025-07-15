<template>
    <footer class="bg-white border-t border-gray-200 py-4 px-6">
        <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
            <div class="text-gray-500 text-sm mb-4 md:mb-0">
                &copy; Kixi Crédito - {{ new Date().getFullYear() }}     Kixi Utilitário. Todos os direitos reservados.
            </div>
            <div class="flex items-center space-x-6">
                <a href="#" class="text-gray-500 hover:text-greenkixi-solid text-sm">Termos de Serviço</a>
                <a href="#" class="text-gray-500 hover:text-greenkixi-solid text-sm">Política de Privacidade</a>
                <a href="https://kixiagenda.kixicredito.com/" class="text-gray-500 hover:text-greenkixi-solid text-sm">Suporte</a>
            </div>
        </div>
    </footer>
</template>

<script setup>
// Lógica do footer pode ser adicionada aqui se necessário
</script>

<style scoped>
footer {
    box-shadow: 0 -1px 3px rgba(0, 0, 0, 0.05);
}
</style>
