<template>
    <div>
        <Teleport to="#layout-header-actions">
            <div class="flex items-center gap-2">
                <RouterLink to="/roles">
                    <Button label="Back" icon="pi pi-arrow-left" severity="secondary" />
                </RouterLink>
                <Button label="Save changes" icon="pi pi-check" :loading="saving" @click="savePermissions" />
            </div>
        </Teleport>

        <div v-if="loading" class="text-center py-12 text-gray-400">Loading…</div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-[1fr_220px] gap-5">
            <Card>
              <template #content>
                <div class="grid grid-cols-[100px_1fr_auto] sm:grid-cols-[160px_1fr_auto] gap-3 px-3 py-1.5 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                    <span>Feature</span><span>Permissions</span><span />
                </div>
                <div v-if="Object.keys(groupedPermissions).length === 0" class="px-3 py-8 text-center">
                    <i class="pi pi-key text-4xl block mb-3 text-gray-200" />
                    <p class="text-sm text-gray-400">No permissions exist yet. <RouterLink to="/permissions" class="text-primary hover:underline">Create permissions first</RouterLink></p>
                </div>
                <div class="divide-y divide-gray-100">
                    <PermissionFeatureRow
                        v-for="(permissions, feature) in groupedPermissions"
                        :key="feature"
                        :feature="feature"
                        :permissions="permissions"
                        v-model="selectedPermissions"
                    />
                </div>
              </template>
            </Card>

            <div class="flex flex-col gap-3">
                <Card>
                  <template #content>
                    <div class="p-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Summary</p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Role</span>
                                <span class="font-semibold text-gray-800">{{ role?.name }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Selected</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-red-50 text-primary tabular-nums">
                                    {{ selectedPermissions.length }} / {{ totalPermissions }}
                                </span>
                            </div>
                        </div>
                    </div>
                  </template>
                </Card>

                <Card>
                  <template #content>
                    <div class="p-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Rename role</p>
                        <div class="flex flex-col gap-2">
                            <InputText v-model="newName" :placeholder="role?.name" fluid />
                            <Button
                                label="Rename"
                                severity="secondary"
                                size="small"
                                :loading="renaming"
                                :disabled="newName === role?.name || !newName.trim()"
                                fluid
                                @click="renameRole"
                            />
                        </div>
                    </div>
                  </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watchEffect, onMounted } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import { useLayout } from '../../composables/useLayout.js'
import PermissionFeatureRow from '../../components/PermissionFeatureRow.vue'
import api from '../../api/index.js'

const { setLayout } = useLayout()

const route = useRoute()
const toast = useToast()
const loading = ref(true)
const saving  = ref(false)
const renaming = ref(false)
const role = ref(null)
const groupedPermissions = ref({})
const selectedPermissions = ref([])
const newName = ref('')

const totalPermissions = computed(() =>
    Object.values(groupedPermissions.value).reduce((sum, p) => sum + p.length, 0)
)

watchEffect(() => {
    setLayout(`Role: ${role.value?.name ?? ''}`, 'Assign permissions to this role')
})

async function load() {
    loading.value = true
    try {
        const { data } = await api.getRole(route.params.id)
        role.value = data.role
        groupedPermissions.value = data.groupedPermissions
        selectedPermissions.value = [...data.rolePermissionIds]
        newName.value = data.role.name
    } finally {
        loading.value = false
    }
}

async function savePermissions() {
    saving.value = true
    try {
        await api.updateRole(role.value.id, { permissions: selectedPermissions.value })
        toast.add({ severity: 'success', summary: 'Saved', detail: 'Permissions updated.', life: 3000 })
    } finally {
        saving.value = false
    }
}

async function renameRole() {
    renaming.value = true
    try {
        await api.updateRole(role.value.id, { name: newName.value })
        role.value.name = newName.value
        toast.add({ severity: 'success', summary: 'Renamed', detail: `Role renamed to "${newName.value}".`, life: 3000 })
    } finally {
        renaming.value = false
    }
}

onMounted(load)
</script>
