<script setup>
import PrimeDialog from 'primevue/dialog'

defineOptions({ inheritAttrs: false })

const pt = {
    root:            { class: 'bg-white rounded-xl shadow-2xl w-full' },
    mask:            { class: 'fixed inset-0 flex items-center justify-center z-50 p-3 animate-[dialog-backdrop-in_0.25s_ease-out_both]' },
    header:          { class: 'flex items-center justify-between px-6 py-3 border-b border-gray-100' },
    title:           { class: 'text-base font-semibold text-gray-900' },
    headerActions:   { class: 'flex items-center' },
    pcMaximizeButton:{ class: '' },
    pcCloseButton:   {
        root: {
            class: [
                'ml-3 w-8 h-8 flex items-center justify-center rounded-lg',
                'text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer',
            ],
        },
        icon: { class: 'w-4 h-4' },
    },
    content:         { class: 'px-6 py-6' },
    footer:          { class: 'px-6 py-3 border-t border-gray-100 flex items-center justify-end gap-2' },
    transition:      {
        enterFromClass:   'opacity-0 scale-95 translate-y-2',
        enterActiveClass: 'transition-all duration-300 ease-out',
        enterToClass:     'opacity-100 scale-100 translate-y-0',
        leaveFromClass:   'opacity-100 scale-100 translate-y-0',
        leaveActiveClass: 'transition-all duration-200 ease-in',
        leaveToClass:     'opacity-0 scale-95 translate-y-2',
    },
}
</script>

<template>
    <PrimeDialog v-bind="$attrs" :pt="pt">
        <template v-for="(_, name) in $slots" #[name]="slotProps">
            <slot :name="name" v-bind="slotProps ?? {}" />
        </template>
    </PrimeDialog>
</template>
