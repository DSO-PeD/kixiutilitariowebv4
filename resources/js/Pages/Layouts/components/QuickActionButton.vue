<template>
    <button @click="$emit('click')"
        class="w-full flex items-center justify-between p-4 rounded-lg transition-all duration-200 group" :class="[
            `bg-${color}-50`,
            `hover:bg-${color}-100`,
            `text-${color}-600`
        ]">
        <div class="flex items-center">
            <div class="p-2 rounded-full mr-3" :class="`bg-${color}-100`">
                <svg class="w-5 h-5" :class="`text-${color}-600`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="icon === 'plus-circle'"     stroke-linecap="round" stroke-linejoin="round" stroke-width="2"   d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    <path v-if="icon === 'calculator'"      stroke-linecap="round" stroke-linejoin="round" stroke-width="2"   d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    <path v-if="icon === 'lock-closed'"     stroke-linecap="round" stroke-linejoin="round" stroke-width="2"   d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />

                </svg>
            </div>
            <span class="font-medium">{{ label }}</span>
        </div>
        <div class="flex items-center">
            <span class="text-xs px-2 py-1 rounded mr-2" :class="[
                `bg-${color}-100`,
                `text-${color}-800`
            ]">
                {{ shortcut }}
            </span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </div>
    </button>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        required: true
    },
    shortcut: {
        type: String,
        default: ''
    },
    color: {
        type: String,
        default: 'blue',
        validator: (value) => ['blue', 'green', 'purple', 'red', 'yellow'].includes(value)
    },
    icon: {
        type: String,
        default: 'plus-circle',
        validator: (value) => ['plus-circle', 'calculator', 'lock-closed'].includes(value)
    }
});

defineEmits(['click']);
</script>

<style scoped>
/* Garantir que as classes dinâmicas sejam geradas */
.bg-blue-50 {
    background-color: #eff6ff;
}

.hover\:bg-blue-100:hover {
    background-color: #dbeafe;
}

.text-blue-600 {
    color: #2563eb;
}

.bg-blue-100 {
    background-color: #dbeafe;
}

.text-blue-800 {
    color: #1e40af;
}

.bg-green-50 {
    background-color: #f0fdf4;
}

.hover\:bg-green-100:hover {
    background-color: #dcfce7;
}

.text-green-600 {
    color: #16a34a;
}

.bg-green-100 {
    background-color: #dcfce7;
}

.text-green-800 {
    color: #166534;
}

.bg-purple-50 {
    background-color: #faf5ff;
}

.hover\:bg-purple-100:hover {
    background-color: #f3e8ff;
}

.text-purple-600 {
    color: #9333ea;
}

.bg-purple-100 {
    background-color: #f3e8ff;
}

.text-purple-800 {
    color: #6b21a8;
}

.bg-red-50 {
    background-color: #fef2f2;
}

.hover\:bg-red-100:hover {
    background-color: #fee2e2;
}

.text-red-600 {
    color: #dc2626;
}

.bg-red-100 {
    background-color: #fee2e2;
}

.text-red-800 {
    color: #991b1b;
}

.bg-yellow-50 {
    background-color: #fefce8;
}

.hover\:bg-yellow-100:hover {
    background-color: #fef9c3;
}

.text-yellow-600 {
    color: #ca8a04;
}

.bg-yellow-100 {
    background-color: #fef9c3;
}

.text-yellow-800 {
    color: #854d0e;
}
</style>
