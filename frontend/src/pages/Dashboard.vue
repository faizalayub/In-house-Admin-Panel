<template>
    <div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <StatCard icon="pi pi-users" icon-bg="bg-blue-50" icon-color="text-blue-500" :value="stats.total_users" label="Total users">
                <template #badge><RouterLink to="/users" class="text-xs text-gray-400 hover:text-primary transition-colors">View →</RouterLink></template>
                <template #footer>
                    <p v-if="stats.unassigned_users > 0" class="mt-1.5 text-xs text-amber-600 font-medium flex items-center gap-1"><i class="pi pi-exclamation-triangle text-xs" />{{ stats.unassigned_users }} with no access</p>
                    <p v-else class="mt-1.5 text-xs text-green-600 font-medium flex items-center gap-1"><i class="pi pi-check-circle text-xs" />All users have access</p>
                </template>
            </StatCard>
            <StatCard icon="pi pi-shield" icon-bg="bg-red-50" icon-color="text-primary" :value="stats.total_roles" label="Roles defined">
                <template #badge><RouterLink to="/roles" class="text-xs text-gray-400 hover:text-primary transition-colors">View →</RouterLink></template>
                <template #footer>
                    <p v-if="stats.empty_roles > 0" class="mt-1.5 text-xs text-amber-600 font-medium flex items-center gap-1"><i class="pi pi-exclamation-triangle text-xs" />{{ stats.empty_roles }} empty role{{ stats.empty_roles !== 1 ? 's' : '' }}</p>
                    <p v-else class="mt-1.5 text-xs text-green-600 font-medium flex items-center gap-1"><i class="pi pi-check-circle text-xs" />All roles configured</p>
                </template>
            </StatCard>
            <StatCard icon="pi pi-key" icon-bg="bg-violet-50" icon-color="text-violet-500" :value="stats.total_permissions" label="Permissions">
                <template #badge><RouterLink to="/permissions" class="text-xs text-gray-400 hover:text-primary transition-colors">View →</RouterLink></template>
                <template #footer>
                    <p class="mt-1.5 text-xs text-gray-400 flex items-center gap-1"><i class="pi pi-th-large text-xs" />{{ stats.feature_count }} feature{{ stats.feature_count !== 1 ? 's' : '' }}<template v-if="stats.orphaned_permissions > 0"> · <span class="text-amber-500 font-medium">{{ stats.orphaned_permissions }} unassigned</span></template></p>
                </template>
            </StatCard>
            <StatCard :icon-bg="stats.coverage === 100 ? 'bg-green-50' : stats.coverage >= 50 ? 'bg-amber-50' : 'bg-red-50'" icon="pi pi-chart-pie" :icon-color="stats.coverage === 100 ? 'text-green-500' : stats.coverage >= 50 ? 'text-amber-500' : 'text-red-500'" label="Role coverage">
                <template #badge><span class="text-xs font-semibold" :class="stats.coverage === 100 ? 'text-green-600' : stats.coverage >= 50 ? 'text-amber-600' : 'text-red-600'">{{ stats.coverage }}%</span></template>
                <template #value>{{ stats.coverage }}<span class="text-sm font-normal text-gray-400">%</span></template>
                <template #footer>
                    <div class="mt-1.5 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500" :class="stats.coverage === 100 ? 'bg-green-500' : stats.coverage >= 50 ? 'bg-amber-400' : 'bg-red-400'" :style="{ width: `${stats.coverage}%` }" />
                    </div>
                </template>
            </StatCard>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_260px] gap-5">
            <Card>
                <template #header>
                    <p class="text-sm font-semibold text-gray-800">Role breakdown</p>
                    <RouterLink to="/roles" class="text-xs text-primary font-medium transition-colors">Manage roles →</RouterLink>
                </template>
                <template #content>
                    <div v-if="overviewRoles.length === 0" class="px-3 py-8 text-center">
                        <i class="pi pi-shield text-4xl block mb-3 text-gray-200" />
                        <p class="text-sm text-gray-400">No roles yet.</p>
                        <RouterLink to="/roles" class="text-xs text-primary hover:underline">Create your first role</RouterLink>
                    </div>
                    <table v-else class="w-full text-sm">
                        <thead><tr class="border-b border-gray-100 bg-gray-50"><th class="px-3 py-1.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Role</th><th class="px-3 py-1.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Users</th><th class="px-3 py-1.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wide">Permissions</th><th class="px-3 py-1.5" /></tr></thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="role in overviewRoles" :key="role.id" class="hover:bg-gray-50/60 transition-colors group">
                                <td class="px-3 py-3"><div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-primary-end inline-block shrink-0" /><span class="font-semibold text-gray-800">{{ role.name }}</span></div></td>
                                <td class="px-3 py-3"><div class="flex items-center gap-2"><span class="text-sm font-medium text-gray-700 tabular-nums w-5 text-right">{{ role.users_count }}</span><div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden max-w-24"><div class="h-full bg-primary-end rounded-full" :style="{ width: userBarWidth(role.users_count) }" /></div><span class="text-xs text-gray-400 tabular-nums">/ {{ stats.total_users }}</span></div></td>
                                <td class="px-3 py-3"><span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium tabular-nums" :class="role.permissions_count === 0 ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-primary'">{{ role.permissions_count }} <span class="ml-0.5 font-normal opacity-70">perm{{ role.permissions_count !== 1 ? 's' : '' }}</span></span></td>
                                <td class="px-3 py-3"><div class="opacity-0 group-hover:opacity-100 transition-opacity"><RouterLink :to="`/roles/${role.id}`" class="inline-flex items-center gap-1 px-3 py-0.5 rounded text-xs font-medium text-primary hover:bg-red-50 transition-colors"><i class="pi pi-cog text-xs" /> Configure</RouterLink></div></td>
                            </tr>
                        </tbody>
                    </table>
                </template>
            </Card>

            <div class="flex flex-col gap-4">
                <Card>
                    <template #header>
                        <p class="text-sm font-semibold text-gray-800">System health</p>
                    </template>
                    <template #content>
                        <div class="divide-y divide-gray-50">
                            <div class="flex items-start gap-3 px-3 py-3"><i class="text-base mt-0.5 shrink-0" :class="stats.unassigned_users === 0 ? 'pi pi-check-circle text-green-500' : 'pi pi-exclamation-circle text-amber-500'" /><div class="min-w-0"><p class="text-xs font-medium text-gray-700">Unassigned users</p><p class="text-xs text-gray-400 mt-0.5"><template v-if="stats.unassigned_users === 0">All users have roles</template><RouterLink v-else to="/users" class="text-amber-600 hover:underline font-medium">{{ stats.unassigned_users }} user{{ stats.unassigned_users !== 1 ? 's' : '' }} need roles</RouterLink></p></div></div>
                            <div class="flex items-start gap-3 px-3 py-3"><i class="text-base mt-0.5 shrink-0" :class="stats.empty_roles === 0 ? 'pi pi-check-circle text-green-500' : 'pi pi-exclamation-circle text-amber-500'" /><div class="min-w-0"><p class="text-xs font-medium text-gray-700">Empty roles</p><p class="text-xs text-gray-400 mt-0.5"><template v-if="stats.empty_roles === 0">All roles have permissions</template><RouterLink v-else to="/roles" class="text-amber-600 hover:underline font-medium">{{ stats.empty_roles }} role{{ stats.empty_roles !== 1 ? 's' : '' }} with no permissions</RouterLink></p></div></div>
                            <div class="flex items-start gap-3 px-3 py-3"><i class="text-base mt-0.5 shrink-0" :class="stats.orphaned_permissions === 0 ? 'pi pi-check-circle text-green-500' : 'pi pi-info-circle text-sky-400'" /><div class="min-w-0"><p class="text-xs font-medium text-gray-700">Unassigned permissions</p><p class="text-xs text-gray-400 mt-0.5"><template v-if="stats.orphaned_permissions === 0">All permissions are in use</template><RouterLink v-else to="/permissions" class="text-sky-600 hover:underline font-medium">{{ stats.orphaned_permissions }} not assigned to any role</RouterLink></p></div></div>
                        </div>
                    </template>
                </Card>
                <Card>
                    <template #header>
                        <p class="text-sm font-semibold text-gray-800">Quick actions</p>
                    </template>
                    <template #content>
                        <div class="p-3 flex flex-col gap-1.5">
                            <RouterLink to="/roles" class="w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-sm text-gray-700 hover:bg-red-50 hover:text-primary transition-colors"><i class="pi pi-shield text-sm text-primary-end shrink-0" />Manage roles</RouterLink>
                            <RouterLink to="/permissions" class="w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-sm text-gray-700 hover:bg-red-50 hover:text-primary transition-colors"><i class="pi pi-key text-sm text-violet-400 shrink-0" />Manage permissions</RouterLink>
                            <RouterLink to="/users" class="w-full flex items-center gap-2.5 px-3 py-1.5 rounded-lg text-sm text-gray-700 hover:bg-red-50 hover:text-primary transition-colors"><i class="pi pi-users text-sm text-blue-400 shrink-0" />Manage users</RouterLink>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useLayout } from '../composables/useLayout.js'
import StatCard from '../components/StatCard.vue'
import api from '../api/index.js'

const { setLayout } = useLayout()
setLayout('Permission Manager', 'Manage roles, permissions and users')

const stats        = ref({ total_users: 0, total_roles: 0, total_permissions: 0, unassigned_users: 0, empty_roles: 0, orphaned_permissions: 0, coverage: 0, feature_count: 0 })
const overviewRoles = ref([])

async function load() {
    const { data } = await api.getDashboard()
    stats.value         = data.stats
    overviewRoles.value = data.overviewRoles
}

function userBarWidth(count) {
    if (!stats.value.total_users) return '0%'
    return `${Math.round((count / stats.value.total_users) * 100)}%`
}

onMounted(load)
</script>
