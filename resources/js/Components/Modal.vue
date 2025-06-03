<template>
    <transition name="modal">
        <div v-if="show" class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container" :class="maxWidthClass">
                    <slot></slot>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    show: Boolean,
    maxWidth: {
        type: String,
        default: '2xl'
    }
});



const maxWidthClass = computed(() => {
    return {
        'sm': 'max-w-sm',
        'md': 'max-w-md',
        'lg': 'max-w-lg',
        'xl': 'max-w-xl',
        '2xl': 'max-w-2xl',
        '3xl': 'max-w-3xl',
        '4xl': 'max-w-4xl',
        '5xl': 'max-w-5xl',
        '6xl': 'max-w-6xl',
        '7xl': 'max-w-7xl',
    }[props.maxWidth];
});
</script>

<style scoped>
.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    transition: opacity 0.3s ease;
}

.modal-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    min-height: 100%;
}

.modal-container {
    width: 100%;
    margin: 0 auto;
    background-color: #fff;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.33);
    transition: all 0.3s ease;
    overflow-y: auto;
    max-height: 90vh;
}

.modal-enter-from {
    opacity: 0;
}

.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
    transform: scale(1.1);
}
</style>
