<script setup>
import PrimePaginator from 'primevue/paginator'

defineOptions({ inheritAttrs: false })

const pt = {
    paginatorContainer:      { class: '' },
    root:                    { class: 'flex items-center justify-between flex-wrap gap-3 px-6 py-3 bg-white border-t border-gray-100 text-sm text-gray-600' },
    content:                 { class: 'flex items-center gap-1' },
    contentStart:            { class: '' },
    first:                   ({ context }) => ({
        class: [
            'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
            context.disabled ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer',
        ],
    }),
    firstIcon:               { class: '' },
    prev:                    ({ context }) => ({
        class: [
            'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
            context.disabled ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer',
        ],
    }),
    prevIcon:                { class: '' },
    next:                    ({ context }) => ({
        class: [
            'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
            context.disabled ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer',
        ],
    }),
    nextIcon:                { class: '' },
    last:                    ({ context }) => ({
        class: [
            'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
            context.disabled ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer',
        ],
    }),
    lastIcon:                { class: '' },
    pages:                   { class: 'flex items-center gap-1' },
    page:                    ({ context }) => ({
        class: [
            'w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-all cursor-pointer',
            context.active ? 'bg-primary-gradient text-white font-semibold' : 'hover:bg-gray-100',
        ],
    }),
    current:                 { class: 'text-sm text-gray-500' },
    pcRowPerPageDropdown:    { class: '' },
    pcJumpToPageDropdown:    { class: '' },
    pcJumpToPageInputText:   { class: '' },
    contentEnd:              { class: '' },
}
</script>

<template>
    <PrimePaginator v-bind="$attrs" :pt="pt" />
</template>
