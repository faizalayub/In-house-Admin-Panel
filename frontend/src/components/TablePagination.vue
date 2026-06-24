<template>
    <div
        v-if="paginator.total > 0"
        class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 px-3 py-3 border-t border-gray-100 bg-gray-50/50"
    >
        <p class="text-xs text-gray-400">
            Showing
            <span class="font-medium text-gray-600">{{ paginator.from }}–{{ paginator.to }}</span>
            of
            <span class="font-medium text-gray-600">{{ paginator.total }}</span>
            {{ label }}
            <template v-if="search">
                for "<span class="font-medium text-gray-600">{{ search }}</span>"
            </template>
        </p>

        <div class="flex items-center gap-0.5">
            <button
                v-if="paginator.current_page > 1"
                class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-100 text-gray-500 transition-colors"
                @click="emit('page', paginator.current_page - 1)"
            >
                <i class="pi pi-chevron-left text-xs" />
            </button>
            <span v-else class="w-7 h-7 flex items-center justify-center text-gray-300">
                <i class="pi pi-chevron-left text-xs" />
            </span>

            <template v-for="p in pageLinks" :key="p">
                <span v-if="p === '...'" class="w-7 h-7 flex items-center justify-center text-xs text-gray-400">…</span>
                <button
                    v-else
                    class="w-7 h-7 flex items-center justify-center rounded text-xs transition-colors"
                    :class="p === paginator.current_page ? 'bg-primary-gradient text-white font-semibold' : 'text-gray-600 hover:bg-gray-100'"
                    @click="emit('page', p)"
                >{{ p }}</button>
            </template>

            <button
                v-if="paginator.current_page < paginator.last_page"
                class="w-7 h-7 flex items-center justify-center rounded hover:bg-gray-100 text-gray-500 transition-colors"
                @click="emit('page', paginator.current_page + 1)"
            >
                <i class="pi pi-chevron-right text-xs" />
            </button>
            <span v-else class="w-7 h-7 flex items-center justify-center text-gray-300">
                <i class="pi pi-chevron-right text-xs" />
            </span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    paginator: { type: Object, required: true },
    label:     { type: String, default: 'records' },
    search:    { type: String, default: '' },
})

const emit = defineEmits(['page'])

const pageLinks = computed(() => {
    const last = props.paginator.last_page
    const cur  = props.paginator.current_page
    if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)
    const pages = new Set([1, last, cur, cur - 1, cur + 1].filter(p => p >= 1 && p <= last))
    return [...pages].sort((a, b) => a - b)
})
</script>
