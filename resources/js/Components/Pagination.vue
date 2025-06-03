<!-- Components/Pagination.vue -->
<template>
    <div class="flex items-center space-x-1">
        <template v-for="(link, key) in links" :key="key">
            <button v-if="link.url === null" class="px-3 py-1 rounded text-gray-400 cursor-not-allowed"
                v-html="link.label" disabled />
            <button v-else @click="$emit('update:page', link.url.split('page=')[1])"
                class="px-3 py-1 rounded hover:bg-gray-100" :class="{ 'bg-blue-500 text-white': link.active }"
                v-html="link.label" />
        </template>
    </div>
</template>

<script setup>
defineProps({
    links: Array
});

defineEmits(['update:page']);
</script>
