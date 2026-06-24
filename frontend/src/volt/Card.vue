<script setup>
import PrimeCard from 'primevue/card'

defineOptions({ inheritAttrs: false })

const pt = {
    root:     { class: 'bg-white border border-gray-200 rounded-xl overflow-hidden' },
    header:   { class: 'flex items-center justify-between px-3 py-3 bg-gray-50 border-b border-gray-100' },
    body:     { class: '' },
    caption:  { class: '' },
    title:    { class: 'font-semibold text-gray-800 text-sm' },
    subtitle: { class: 'text-sm text-gray-500' },
    content:  { class: '' },
    footer:   { class: 'px-3 py-3 border-t border-gray-100' },
}
</script>

<template>
    <PrimeCard v-bind="$attrs" :pt="pt">
        <template v-for="(_, name) in $slots" #[name]="slotProps">
            <slot :name="name" v-bind="slotProps ?? {}" />
        </template>
    </PrimeCard>
</template>
