<script setup>
import PrimeInputText from 'primevue/inputtext'

defineOptions({ inheritAttrs: false })

const pt = {
    root: ({ props, parent }) => ({
        class: [
            'border rounded-lg text-sm text-gray-900 bg-white placeholder-gray-400',
            'focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-transparent',
            'transition-colors duration-150',
            props.invalid ? 'border-red-400 focus:ring-red-400' : 'border-gray-300',
            props.fluid ? 'w-full' : '',
            parent?.instance?.$name === 'IconField' ? 'pl-8' : 'px-3',
            'py-1.5',
            { 'opacity-50 cursor-not-allowed': props.disabled },
        ],
    }),
}
</script>

<template>
    <PrimeInputText v-bind="$attrs" :pt="pt" />
</template>
