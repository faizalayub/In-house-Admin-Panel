<script setup>
import PrimeTag from 'primevue/tag'

defineOptions({ inheritAttrs: false })

const pt = {
    root: ({ props }) => ({
        class: [
            'inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-medium',
            {
                'bg-red-50 text-primary':     !props.severity || props.severity === 'primary',
                'bg-gray-100 text-gray-600':    props.severity === 'secondary',
                'bg-green-50 text-green-700':   props.severity === 'success',
                'bg-yellow-50 text-yellow-700': props.severity === 'warn' || props.severity === 'warning',
                'bg-red-50 text-red-700':       props.severity === 'danger',
                'bg-sky-50 text-sky-700':       props.severity === 'info',
            },
        ],
    }),
    label: {},
    icon:  { class: 'text-xs' },
}
</script>

<template>
    <PrimeTag v-bind="$attrs" :pt="pt" />
</template>
