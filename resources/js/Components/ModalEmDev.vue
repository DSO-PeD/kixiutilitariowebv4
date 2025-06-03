<template>

        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="$emit('close')">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
                <h2 class="text-lg font-bold mb-4">Modal Simples</h2>
                <p class="text-gray-600 mb-4">Conteúdo da modal aqui.</p>
                <button @click="$emit('close')" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Fechar
                </button>
            </div>
        </div>

</template>
