<template>
    <div>
        <Teleport to="#layout-header-actions">
            <IconField>
                <InputIcon class="pi pi-search" />
                <InputText v-model="searchInput" placeholder="Search name or email…" @input="onSearch" />
            </IconField>
        </Teleport>

        <Card>
          <template #content>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th v-for="col in columns" :key="col.field"
                                class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap"
                                :class="col.sortable ? 'cursor-pointer select-none hover:bg-gray-100 transition-colors' : ''"
                                @click="col.sortable && sort(col.field)"
                            >
                                <div class="flex items-center gap-1">
                                    {{ col.label }}
                                    <i v-if="col.sortable" :class="sortIcon(col.field)" />
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-if="loading">
                            <td :colspan="columns.length" class="px-3 py-8 text-center text-gray-400">Loading…</td>
                        </tr>
                        <tr v-else-if="users.data?.length === 0">
                            <td :colspan="columns.length" class="px-3 py-8 text-center">
                                <i class="pi pi-users text-4xl block mb-3 text-gray-200" />
                                <p class="text-sm text-gray-400">{{ filters.search ? `No users match "${filters.search}"` : 'No users found.' }}</p>
                            </td>
                        </tr>
                        <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50/60 transition-colors group">
                            <td class="px-3 py-3">
                                <p class="font-semibold text-gray-800 leading-tight">{{ user.name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ user.email }}</p>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex flex-wrap gap-1">
                                    <span v-for="role in user.roles" :key="role" class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-50 text-primary">{{ role }}</span>
                                    <span v-if="user.roles.length === 0" class="text-xs text-gray-300">—</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 tabular-nums">
                                <span class="text-gray-700 font-medium">{{ user.direct_permissions_count }}</span>
                                <span class="text-gray-400 text-xs"> direct</span>
                                <span class="text-gray-300 mx-0.5">·</span>
                                <span class="text-gray-500 text-xs">{{ user.all_permissions_count }} total</span>
                            </td>
                            <td class="px-3 py-3 text-xs text-gray-400 whitespace-nowrap">{{ user.joined }}</td>
                            <td class="px-3 py-3">
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <RouterLink :to="`/users/${user.id}`">
                                        <button class="inline-flex items-center gap-1 px-3 py-0.5 rounded text-xs font-medium text-primary hover:bg-red-50 transition-colors">
                                            <i class="pi pi-user-edit text-xs" /> Manage
                                        </button>
                                    </RouterLink>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <TablePagination v-if="users.total" :paginator="users" label="users" :search="filters.search" @page="goPage" />
          </template>
        </Card>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useLayout } from '../../composables/useLayout.js'
import TablePagination from '../../components/TablePagination.vue'
import api from '../../api/index.js'

const { setLayout } = useLayout()
setLayout('Users', 'Assign roles and permissions to users')

const users = ref({ data: [], total: 0 })
const loading = ref(true)
const filters = reactive({ sort: 'name', dir: 'asc', search: '', page: 1 })
const searchInput = ref('')
let searchTimer = null

const columns = [
    { label: 'User',        field: 'name',       sortable: true  },
    { label: 'Roles',       field: 'roles',      sortable: false },
    { label: 'Permissions', field: 'perms',      sortable: false },
    { label: 'Joined',      field: 'created_at', sortable: true  },
    { label: '',            field: 'actions',    sortable: false },
]

function sortIcon(field) {
    if (filters.sort !== field) return 'pi pi-sort-alt text-gray-300 text-xs'
    return filters.dir === 'asc' ? 'pi pi-sort-amount-up-alt text-primary text-xs' : 'pi pi-sort-amount-down-alt text-primary text-xs'
}

async function load() {
    loading.value = true
    try {
        const { data } = await api.getUsers(filters)
        users.value = data.users
    } finally {
        loading.value = false
    }
}

function sort(field) {
    filters.dir  = filters.sort === field && filters.dir === 'asc' ? 'desc' : 'asc'
    filters.sort = field
    filters.page = 1
    load()
}

function onSearch() {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => { filters.search = searchInput.value; filters.page = 1; load() }, 350)
}

function goPage(p) { filters.page = p; load() }

onMounted(load)
</script>
