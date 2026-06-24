<template>
    <div>
        <Teleport to="#layout-header-actions">
            <div class="flex items-center gap-2">
                <RouterLink to="/users">
                    <Button label="Back" icon="pi pi-arrow-left" severity="secondary" />
                </RouterLink>
                <Button label="Save changes" icon="pi pi-check" :loading="saving" @click="save" />
            </div>
        </Teleport>

        <div v-if="loading" class="text-center py-12 text-gray-400">Loading…</div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-[1fr_220px] gap-5">
            <div class="flex flex-col gap-4">
                <Card>
                  <template #header>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Roles</p>
                    <span class="text-xs text-gray-400">{{ selectedRoles.length }} assigned</span>
                  </template>
                  <template #content>
                    <div v-if="allRoles.length === 0" class="px-3 py-6 text-center text-sm text-gray-400">
                        No roles yet. <RouterLink to="/roles" class="text-primary hover:underline">Create roles first.</RouterLink>
                    </div>
                    <div v-else class="flex flex-wrap gap-2 px-3 py-3">
                        <button
                            v-for="role in allRoles"
                            :key="role.id"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all cursor-pointer select-none"
                            :class="selectedRoles.includes(role.id) ? 'bg-primary-gradient text-white hover:brightness-90' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                            @click="toggleRole(role.id)"
                        >
                            <i v-if="selectedRoles.includes(role.id)" class="pi pi-check text-xs" />
                            {{ role.name }}
                        </button>
                    </div>
                  </template>
                </Card>

                <Card>
                  <template #content>
                    <div class="grid grid-cols-[100px_1fr_auto] sm:grid-cols-[160px_1fr_auto] gap-3 px-3 py-1.5 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                        <span>Feature</span>
                        <span>Direct permissions <span class="normal-case font-normal text-gray-300">— overrides roles</span></span>
                        <span />
                    </div>
                    <div v-if="Object.keys(groupedPermissions).length === 0" class="px-3 py-8 text-center text-sm text-gray-400">
                        No permissions exist yet. <RouterLink to="/permissions" class="text-primary hover:underline">Create permissions first.</RouterLink>
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
            </div>

            <div class="flex flex-col gap-3">
                <Card>
                  <template #content>
                    <div class="p-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Summary</p>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Roles</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-red-50 text-primary tabular-nums">{{ selectedRoles.length }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Direct perms</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-700 tabular-nums">{{ selectedPermissions.length }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-t border-gray-100 pt-1.5 mt-1.5">
                                <span class="text-gray-500">Effective total</span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-semibold bg-violet-50 text-violet-700 tabular-nums">{{ effectivePermissions.length }}</span>
                            </div>
                        </div>
                    </div>
                  </template>
                </Card>

                <Card>
                  <template #header>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Effective access</p>
                        <p class="text-xs text-gray-400 mt-0.5">via roles + direct</p>
                    </div>
                  </template>
                  <template #content>
                    <div v-if="effectivePermissions.length === 0" class="px-3 py-6 text-center text-xs text-gray-400">No access yet</div>
                    <div v-else class="divide-y divide-gray-50">
                        <div v-for="(perms, feature) in effectiveByFeature" :key="feature" class="px-3 py-3">
                            <div class="flex items-center justify-between mb-1.5">
                                <code class="text-xs font-semibold text-gray-700">{{ feature }}</code>
                                <span class="text-xs text-gray-300 tabular-nums">{{ perms.length }}</span>
                            </div>
                            <div class="flex flex-wrap gap-0.5">
                                <span v-for="perm in perms" :key="perm" :title="perm" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono bg-violet-50 text-violet-700">{{ actionLabel(perm) }}</span>
                            </div>
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
const user = ref(null)
const allRoles = ref([])
const groupedPermissions = ref({})
const selectedRoles = ref([])
const selectedPermissions = ref([])
const effectivePermissions = ref([])

const effectiveByFeature = computed(() =>
    effectivePermissions.value.reduce((acc, name) => {
        const feature = name.includes('.') ? name.split('.')[0] : 'general'
        if (!acc[feature]) acc[feature] = []
        acc[feature].push(name)
        return acc
    }, {})
)

const actionLabel = (name) => name.includes('.') ? name.split('.').slice(1).join('.') : name

watchEffect(() => {
    setLayout(user.value?.name ?? '', user.value?.email ?? '')
})

async function load() {
    loading.value = true
    try {
        const { data } = await api.getUser(route.params.id)
        user.value = data.user
        allRoles.value = data.allRoles
        groupedPermissions.value = data.groupedPermissions
        selectedRoles.value = [...data.userRoleIds]
        selectedPermissions.value = [...data.userDirectPermissionIds]
        effectivePermissions.value = data.effectivePermissions
    } finally {
        loading.value = false
    }
}

function toggleRole(id) {
    const i = selectedRoles.value.indexOf(id)
    if (i === -1) selectedRoles.value.push(id)
    else selectedRoles.value.splice(i, 1)
}

async function save() {
    saving.value = true
    try {
        await api.updateUser(user.value.id, { roles: selectedRoles.value, permissions: selectedPermissions.value })
        toast.add({ severity: 'success', summary: 'Saved', detail: 'User updated.', life: 3000 })
        load()
    } finally {
        saving.value = false
    }
}

onMounted(load)
</script>
