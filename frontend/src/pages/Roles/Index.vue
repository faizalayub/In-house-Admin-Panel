<template>
    <div>
        <Teleport to="#layout-header-actions">
            <Button label="New Role" icon="pi pi-plus" @click="showCreate = true" />
        </Teleport>

        <Card>
          <template #content>
            <DataTable
                :value="roles.data"
                :loading="loading"
                lazy
                :sortField="filters.sort"
                :sortOrder="filters.dir === 'asc' ? 1 : -1"
                @sort="onSort"
            >
                <Column field="name" header="Role" sortable>
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <span class="inline-block w-1.5 h-1.5 rounded-full bg-primary-end shrink-0" />
                            <span class="font-semibold text-gray-800">{{ data.name }}</span>
                        </div>
                    </template>
                </Column>
                <Column field="permissions_count" header="Permissions" sortable>
                    <template #body="{ data }">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-50 text-primary tabular-nums">
                            {{ data.permissions_count }}
                        </span>
                    </template>
                </Column>
                <Column field="users_count" header="Users" sortable>
                    <template #body="{ data }">
                        <span class="text-gray-500 tabular-nums">{{ data.users_count }}</span>
                    </template>
                </Column>
                <Column>
                    <template #body="{ data }">
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <RouterLink :to="{ name: 'roles.show', params: { id: data.id } }">
                                <button class="inline-flex items-center gap-1 px-3 py-0.5 rounded text-xs font-medium text-primary hover:bg-red-50 transition-colors">
                                    <i class="pi pi-cog text-xs" /> Configure
                                </button>
                            </RouterLink>
                            <button
                                class="w-7 h-7 flex items-center justify-center rounded text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors"
                                @click="confirmDelete(data)"
                            >
                                <i class="pi pi-trash text-xs" />
                            </button>
                        </div>
                    </template>
                </Column>
                <template #empty>
                    <div class="py-8 text-center">
                        <i class="pi pi-shield text-4xl block mb-3 text-gray-200" />
                        <p class="text-sm text-gray-400">No roles yet.</p>
                    </div>
                </template>
            </DataTable>
            <TablePagination v-if="roles.total" :paginator="roles" label="roles" @page="goPage" />
          </template>
        </Card>

        <Dialog v-model:visible="showCreate" header="Create Role" modal class="max-w-sm">
            <form class="flex flex-col gap-4" @submit.prevent="createRole">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700">Role name</label>
                    <InputText v-model="createName" placeholder="e.g. admin, editor, viewer" autofocus :invalid="!!createError" fluid />
                    <small v-if="createError" class="text-red-500">{{ createError }}</small>
                </div>
                <div class="flex justify-end gap-2">
                    <Button label="Cancel" severity="secondary" @click="showCreate = false" />
                    <Button label="Create" type="submit" :loading="saving" />
                </div>
            </form>
        </Dialog>

        <Dialog v-model:visible="showDelete" header="Delete Role" modal class="max-w-sm">
            <p class="text-gray-600 mb-0.5">Delete role <strong class="text-gray-900">{{ deleteTarget?.name }}</strong>?</p>
            <p class="text-sm text-gray-400">This will remove it from all users who have this role.</p>
            <template #footer>
                <Button label="Cancel" severity="secondary" @click="showDelete = false" />
                <Button label="Delete" severity="danger" :loading="deleting" @click="doDelete" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useToast } from 'primevue/usetoast'
import Column from 'primevue/column'
import { useLayout } from '../../composables/useLayout.js'
import TablePagination from '../../components/TablePagination.vue'
import api from '../../api/index.js'

const { setLayout } = useLayout()
setLayout('Roles', 'Manage roles and assign permissions')

const toast = useToast()
const roles   = ref({ data: [], total: 0 })
const loading = ref(true)
const filters = reactive({ sort: 'name', dir: 'asc', page: 1 })

const showCreate   = ref(false)
const showDelete   = ref(false)
const deleteTarget = ref(null)
const createName   = ref('')
const createError  = ref('')
const saving       = ref(false)
const deleting     = ref(false)

async function load() {
    loading.value = true
    try {
        const { data } = await api.getRoles(filters)
        roles.value = data.roles
    } finally {
        loading.value = false
    }
}

function onSort({ sortField, sortOrder }) {
    filters.sort = sortField
    filters.dir  = sortOrder === 1 ? 'asc' : 'desc'
    filters.page = 1
    load()
}

function goPage(p) { filters.page = p; load() }

async function createRole() {
    createError.value = ''
    saving.value = true
    try {
        await api.createRole({ name: createName.value })
        showCreate.value = false
        createName.value = ''
        toast.add({ severity: 'success', summary: 'Success', detail: 'Role created.', life: 3000 })
        load()
    } catch (e) {
        createError.value = e.response?.data?.errors?.name ?? 'Failed to create role.'
    } finally {
        saving.value = false
    }
}

function confirmDelete(role) { deleteTarget.value = role; showDelete.value = true }

async function doDelete() {
    deleting.value = true
    try {
        await api.deleteRole(deleteTarget.value.id)
        showDelete.value = false
        toast.add({ severity: 'success', summary: 'Success', detail: 'Role deleted.', life: 3000 })
        load()
    } finally {
        deleting.value = false
    }
}

onMounted(load)
</script>
