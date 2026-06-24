<template>
    <div class="grid grid-cols-[100px_1fr_auto] sm:grid-cols-[160px_1fr_auto] gap-3 items-start px-3 py-3 hover:bg-gray-50/40 transition-colors">
        <!-- Feature name + count -->
        <div class="flex items-center gap-1.5 pt-0.5">
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-primary-end shrink-0" />
            <code class="text-xs font-semibold text-gray-700 truncate">{{ feature }}</code>
            <span class="text-xs text-gray-300 tabular-nums shrink-0">{{ selectedCount }}/{{ permissions.length }}</span>
        </div>

        <!-- Toggle chips -->
        <div class="flex flex-wrap gap-1">
            <button
                v-for="perm in permissions"
                :key="perm.id"
                :title="perm.name"
                class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-xs font-mono transition-all cursor-pointer select-none"
                :class="isSelected(perm.id)
                    ? 'bg-primary-gradient text-white hover:brightness-90'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                @click="toggle(perm.id)"
            >
                <i v-if="isSelected(perm.id)" class="pi pi-check text-[10px] leading-none" />
                {{ actionLabel(perm.name) }}
            </button>
        </div>

        <!-- Select / Deselect all -->
        <button
            class="text-xs font-medium whitespace-nowrap pt-0.5 transition-colors"
            :class="allSelected ? 'text-gray-400 hover:text-gray-600' : 'text-primary hover:text-primary'"
            @click="toggleAll"
        >
            {{ allSelected ? 'Deselect all' : 'Select all' }}
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    feature: { type: String, required: true },
    permissions: { type: Array, required: true },
    modelValue: { type: Array, required: true },
})

const emit = defineEmits(['update:modelValue'])

const selectedCount = computed(() =>
    props.permissions.filter(p => props.modelValue.includes(p.id)).length
)

const allSelected = computed(() =>
    props.permissions.length > 0 && props.permissions.every(p => props.modelValue.includes(p.id))
)

function isSelected(id) {
    return props.modelValue.includes(id)
}

function actionLabel(name) {
    return name.includes('.') ? name.split('.').slice(1).join('.') : name
}

function toggle(id) {
    const next = [...props.modelValue]
    const idx = next.indexOf(id)
    if (idx === -1) next.push(id)
    else next.splice(idx, 1)
    emit('update:modelValue', next)
}

function toggleAll() {
    if (allSelected.value) {
        emit('update:modelValue', props.modelValue.filter(
            id => !props.permissions.find(p => p.id === id)
        ))
    } else {
        const next = [...props.modelValue]
        props.permissions.forEach(p => {
            if (!next.includes(p.id)) next.push(p.id)
        })
        emit('update:modelValue', next)
    }
}
</script>
