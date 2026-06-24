<script setup>
import PrimePanel from 'primevue/panel'

defineOptions({ inheritAttrs: false })

const pt = {
    root:             { class: 'bg-white border border-gray-200 rounded-xl overflow-hidden' },
    header:           { class: 'flex items-center justify-between px-6 py-3 bg-gray-50 border-b border-gray-100' },
    title:            { class: 'font-semibold text-gray-800 text-sm' },
    headerActions:    { class: 'flex items-center gap-1' },
    pcToggleButton:   {
        root: {
            class: [
                'w-6 h-6 flex items-center justify-center rounded text-gray-400',
                'hover:text-gray-600 hover:bg-gray-200 transition-colors cursor-pointer',
            ],
        },
        icon: { class: 'text-xs' },
    },
    contentContainer: { class: '' },
    contentWrapper:   { class: 'transition-all duration-200' },
    content:          { class: 'p-6' },
    footer:           { class: '' },
    transition:       {},
}
</script>

<template>
    <PrimePanel v-bind="$attrs" :pt="pt">
        <template v-for="(_, name) in $slots" #[name]="slotProps">
            <slot :name="name" v-bind="slotProps ?? {}" />
        </template>
    </PrimePanel>
</template>
