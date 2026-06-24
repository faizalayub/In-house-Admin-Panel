<script setup>
import PrimeChip from 'primevue/chip'

defineOptions({ inheritAttrs: false })

const pt = {
    root:       { class: 'inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-gray-100 text-gray-700 text-xs font-medium' },
    image:      { class: '' },
    icon:       { class: 'text-xs text-gray-500' },
    label:      { class: '' },
    removeIcon: { class: 'text-xs text-gray-400 hover:text-gray-600 cursor-pointer ml-0.5' },
}
</script>

<template>
    <PrimeChip v-bind="$attrs" :pt="pt" />
</template>
