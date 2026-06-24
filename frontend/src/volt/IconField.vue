<script setup>
import PrimeIconField from 'primevue/iconfield'

defineOptions({ inheritAttrs: false })

const pt = {
    root: { class: 'relative flex items-center' },
}
</script>

<template>
    <PrimeIconField v-bind="$attrs" :pt="pt">
        <slot />
    </PrimeIconField>
</template>
