<script setup>
import PrimeButton from 'primevue/button'

defineOptions({ inheritAttrs: false })

const pt = {
    root: ({ props, context }) => ({
        class: [
            'inline-flex items-center justify-center gap-2 rounded-lg cursor-pointer',
            'transition-all duration-150 select-none focus:outline-none',

            { 'px-3 py-1.5 text-sm': !props.size || props.size === 'normal' },
            { 'px-3 py-1.5 text-xs': props.size === 'small' },
            { 'px-6 py-3 text-base': props.size === 'large' },

            // Primary
            { 'bg-primary-gradient text-white font-bold hover:brightness-90 active:brightness-75':
                !props.text && !props.outlined && (!props.severity || props.severity === 'primary') },

            // Secondary
            { 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 active:bg-gray-100':
                !props.text && !props.outlined && props.severity === 'secondary' },

            // Danger
            { 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800':
                !props.text && !props.outlined && props.severity === 'danger' },

            // Success
            { 'bg-green-600 text-white hover:bg-green-700':
                !props.text && !props.outlined && props.severity === 'success' },

            // Text variants
            { 'text-primary hover:bg-red-50':
                props.text && (!props.severity || props.severity === 'primary') },
            { 'text-gray-600 hover:bg-gray-100':
                props.text && props.severity === 'secondary' },
            { 'text-red-600 hover:bg-red-50':
                props.text && props.severity === 'danger' },

            { 'w-full': props.fluid },
            { 'opacity-50 cursor-not-allowed pointer-events-none': context.disabled },
        ],
    }),
    loadingIcon: { class: '' },
    icon:        { class: '' },
    label:       { class: '' },
    pcBadge:     { class: '' },
}
</script>

<template>
    <PrimeButton v-bind="$attrs" :pt="pt" />
</template>
