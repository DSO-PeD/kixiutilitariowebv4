<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="$emit('close')">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
                <slot></slot>
            </div>
        </div>
    </transition>
</template>

<script setup>
defineProps({
    show: Boolean
});

defineEmits(['close']);
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
