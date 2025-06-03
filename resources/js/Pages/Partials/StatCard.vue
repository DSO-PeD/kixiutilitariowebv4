<template>
    <div :class="['p-4 rounded-lg shadow', color, 'flex items-center gap-4']">
      <div class="text-3xl">{{ icon }}</div>
      <div>
        <div class="text-sm text-gray-600">{{ title }}</div>
        <div class="text-xl font-bold text-gray-800">{{ value }}</div>
      </div>
    </div>
  </template>

  <script setup>
  defineProps({
    title: String,
    value: String,
    icon: String,
    color: {
      type: String,
      default: 'bg-gray-100',
    },
  })
  </script>
