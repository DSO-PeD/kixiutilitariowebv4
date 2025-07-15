<template>
    <div class="bg-white rounded-xl p-5 border-l-4" :class="`border-${iconColor}-500`">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider">{{ title }}</h3>
                <p class="text-2xl font-bold mt-2" :class="`text-${iconColor}-600`">
                    {{ value }}
                    <span v-if="secondaryValue" class="text-gray-500 text-lg">/ {{ secondaryValue }}</span>
                </p>
            </div>
            <div class="p-2 rounded-full" :class="`bg-${iconColor}-100`">
                <svg v-if="icon === 'clock'" class="w-5 h-5" :class="`text-${iconColor}-500`" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

                </svg>

                <svg v-if="icon === 'currency-dollar'" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>


                <svg v-if="icon === 'arrow-down'" class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>

                <svg v-if="icon === 'check-circle'" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg v-if="icon === 'exclamation'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>



                <svg v-if="icon === 'exclamation-triangle'" class="w-5 h-5 text-orange-500" fill="none"
                    viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>

                  <svg v-if="icon === 'c-respo'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>

                <!-- Adicione outros ícones conforme necessário -->
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: String,
    value: [String, Number],
    secondaryValue: String,
    iconColor: {
        type: String,
        default: 'blue'
    },
    icon: {
        type: String,
        default: 'chart-bar'
    }
});
</script>
