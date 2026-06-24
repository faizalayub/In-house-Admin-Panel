<script setup>
import PrimeInputIcon from 'primevue/inputicon'

defineOptions({ inheritAttrs: false })

const pt = {
    root: { class: 'absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none z-10' },
}
</script>

<template>
    <PrimeInputIcon v-bind="$attrs" :pt="pt">
        <slot />
    </PrimeInputIcon>
</template>
