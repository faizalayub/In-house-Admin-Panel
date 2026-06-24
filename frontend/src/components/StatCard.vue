<template>
    <div class="bg-white border border-gray-200 rounded-xl p-3">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" :class="iconBg">
                <i class="text-base" :class="[icon, iconColor]" />
            </div>
            <slot name="badge" />
        </div>

        <p class="text-2xl font-bold text-gray-900 tabular-nums">
            <slot name="value">{{ value }}</slot>
        </p>
        <p class="text-xs text-gray-500 mt-0.5">{{ label }}</p>

        <slot name="footer" />
    </div>
</template>

<script setup>
defineProps({
    icon: { type: String, required: true },
    iconBg: { type: String, required: true },
    iconColor: { type: String, required: true },
    value: { type: [String, Number], default: null },
    label: { type: String, required: true },
})
</script>