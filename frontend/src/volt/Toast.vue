<script setup>
import PrimeToast from 'primevue/toast'

defineOptions({ inheritAttrs: false })

const pt = {
    root: { class: 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none w-80' },
    message: ({ props }) => ({
        class: [
            'flex items-start gap-3 p-3 rounded-xl shadow-lg border',
            {
                'bg-green-50 border-green-200':   props.message.severity === 'success',
                'bg-red-50 border-red-200':       props.message.severity === 'error',
                'bg-blue-50 border-blue-200':     props.message.severity === 'info',
                'bg-yellow-50 border-yellow-200': props.message.severity === 'warn',
            },
        ],
    }),
    messageContent: { class: 'flex items-start gap-3 flex-1 min-w-0' },
    messageIcon: ({ props }) => ({
        class: [
            'text-lg mt-0.5 shrink-0',
            {
                'pi pi-check-circle text-green-600':        props.message.severity === 'success',
                'pi pi-times-circle text-red-600':          props.message.severity === 'error',
                'pi pi-info-circle text-blue-600':          props.message.severity === 'info',
                'pi pi-exclamation-circle text-yellow-600': props.message.severity === 'warn',
            },
        ],
    }),
    messageText: { class: 'flex-1 min-w-0' },
    summary: ({ props }) => ({
        class: [
            'block font-semibold text-sm',
            {
                'text-green-800':  props.message.severity === 'success',
                'text-red-800':    props.message.severity === 'error',
                'text-blue-800':   props.message.severity === 'info',
                'text-yellow-800': props.message.severity === 'warn',
            },
        ],
    }),
    detail: ({ props }) => ({
        class: [
            'block text-sm mt-0.5',
            {
                'text-green-700':  props.message.severity === 'success',
                'text-red-700':    props.message.severity === 'error',
                'text-blue-700':   props.message.severity === 'info',
                'text-yellow-700': props.message.severity === 'warn',
            },
        ],
    }),
    buttonContainer: { class: '' },
    closeButton:     { class: 'ml-auto shrink-0 p-0.5 rounded text-gray-400 hover:text-gray-600 transition-colors cursor-pointer' },
    closeIcon:       { class: 'w-3.5 h-3.5' },
    transition:      {},
}
</script>

<template>
    <PrimeToast v-bind="$attrs" :pt="pt" />
</template>
