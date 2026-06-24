<script setup>
import PrimeDrawer from 'primevue/drawer'

defineOptions({ inheritAttrs: false })

const pt = {
    root:    { class: 'fixed inset-y-0 left-0 z-50 w-64 bg-white flex flex-col shadow-xl' },
    mask:    { class: 'fixed inset-0 z-40 bg-black/40' },
    header:  { class: 'flex items-center justify-between px-6 py-3 border-b border-gray-100 shrink-0' },
    title:   { class: 'font-semibold text-gray-900 text-sm' },
    content: { class: 'flex-1 overflow-y-auto p-0 flex flex-col' },
    footer:  { class: 'px-3 py-3 border-t border-gray-100 shrink-0' },
    pcCloseButton: {
        root: { class: 'w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer' },
    },
    transition: {
        enterFromClass:   '-translate-x-full',
        enterActiveClass: 'transition-transform duration-300 ease-out',
        enterToClass:     'translate-x-0',
        leaveFromClass:   'translate-x-0',
        leaveActiveClass: 'transition-transform duration-200 ease-in',
        leaveToClass:     '-translate-x-full',
    },
}
</script>

<template>
    <PrimeDrawer v-bind="$attrs" :pt="pt">
        <template v-for="(_, name) in $slots" #[name]="slotProps">
            <slot :name="name" v-bind="slotProps ?? {}" />
        </template>
    </PrimeDrawer>
</template>
