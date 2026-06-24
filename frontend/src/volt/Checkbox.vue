<script setup>
import PrimeCheckbox from 'primevue/checkbox'

defineOptions({ inheritAttrs: false })

const pt = {
    root:  { class: 'relative inline-flex cursor-pointer select-none align-middle' },
    input: { class: 'absolute opacity-0 w-full h-full inset-0 cursor-pointer z-10 m-0 p-0' },
    box:   ({ context }) => ({
        class: [
            'w-4 h-4 rounded border flex items-center justify-center shrink-0',
            'transition-colors duration-150',
            context.checked
                ? 'bg-primary-gradient border-primary'
                : 'bg-white border-gray-300 hover:border-primary',
            { 'opacity-50 cursor-not-allowed': context.disabled },
        ],
    }),
    icon: { class: 'w-2.5 h-2.5 text-white' },
}
</script>

<template>
    <PrimeCheckbox v-bind="$attrs" :pt="pt" />
</template>
