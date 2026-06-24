<template>
    <div>
        <Teleport to="#layout-header-actions">
            <div class="flex items-center gap-2">
                <Button label="Bulk create" icon="pi pi-objects-column" severity="secondary" @click="openBulk()" />
                <Button label="New Permission" icon="pi pi-plus" @click="openCreate()" />
            </div>
        </Teleport>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 mb-3">
            <div class="flex items-center gap-2">
                <p class="text-sm text-gray-500">
                    <strong class="text-gray-800 font-semibold">{{ featurePaginator.total ?? 0 }}</strong> features ·
                    <strong class="text-gray-800 font-semibold">{{ filteredPermissionCount }}</strong> permissions
                    <template v-if="filters.search">
                        matching "<span class="font-medium text-gray-700">{{ filters.search }}</span>"
                    </template>
                </p>
                <button
                    v-if="totalUnassigned > 0 || filters.unassigned"
                    class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-lg text-xs font-medium border transition-colors"
                    :class="filters.unassigned ? 'bg-red-50 text-primary border-primary' : 'bg-white text-gray-500 border-gray-200 hover:border-primary hover:text-primary'"
                    @click="toggleUnassigned"
                >
                    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] font-bold" :class="filters.unassigned ? 'bg-red-100 text-primary' : 'bg-gray-100 text-gray-500'">{{ totalUnassigned }}</span>
                    unassigned
                    <i v-if="filters.unassigned" class="pi pi-times text-[10px]" />
                </button>
            </div>
            <IconField>
                <InputIcon class="pi pi-search" />
                <InputText v-model="searchInput" placeholder="Filter features..." @input="onSearch" />
            </IconField>
        </div>

        <Card v-if="totalPermissions === 0">
          <template #content>
            <div class="px-6 py-8 text-center">
                <i class="pi pi-key text-5xl block mb-3 text-gray-200" />
                <p class="font-medium text-gray-500 mb-0.5">No permissions yet</p>
                <p class="text-sm text-gray-400">Use <strong>Bulk create</strong> to generate a full feature set at once.</p>
            </div>
          </template>
        </Card>

        <Card v-else>
          <template #content>
            <div class="grid grid-cols-[100px_1fr_32px] sm:grid-cols-[160px_1fr_32px] gap-3 px-3 py-1.5 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                <span>Feature</span><span>Permissions</span><span />
            </div>
            <div class="divide-y divide-gray-100">
                <div v-if="featurePaginator.total === 0" class="px-3 py-8 text-center text-sm text-gray-400">
                    <template v-if="filters.unassigned">All permissions are assigned.</template>
                    <template v-else>No features match "<span class="font-medium text-gray-600">{{ filters.search }}</span>"</template>
                </div>
                <div
                    v-for="(permissions, feature) in groupedPermissions"
                    :key="feature"
                    class="grid grid-cols-[100px_1fr_32px] sm:grid-cols-[160px_1fr_32px] gap-3 px-3 py-3 items-start group/row hover:bg-gray-50/50 transition-colors"
                >
                    <div class="flex items-center gap-1.5 pt-0.5 flex-wrap">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-violet-400 shrink-0 mt-0.5" />
                        <code class="text-xs font-semibold text-gray-700 truncate">{{ feature }}</code>
                        <span class="text-gray-300 text-xs shrink-0">{{ permissions.length }}</span>
                        <span v-if="unassignedInGroup(permissions) > 0" class="text-[10px] font-semibold text-primary-end shrink-0">· {{ unassignedInGroup(permissions) }} new</span>
                    </div>
                    <div class="flex flex-wrap gap-1">
                        <span
                            v-for="perm in permissions"
                            :key="perm.id"
                            class="group/chip relative inline-flex items-center px-1.5 py-0.5 rounded text-xs font-mono transition-colors cursor-default select-none"
                            :class="deletingId === perm.id ? 'bg-red-100 text-red-500' : isUnassigned(perm.id) ? 'bg-red-50 text-primary ring-1 ring-inset ring-primary/30 hover:bg-red-100 hover:text-red-600 hover:ring-red-300' : 'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-500'"
                        >
                            {{ actionLabel(perm.name) }}
                            <button class="ml-0.5 leading-none opacity-0 group-hover/chip:opacity-100 transition-opacity text-red-400 hover:text-red-600 font-bold" @click="confirmDelete(perm)">×</button>
                        </span>
                    </div>
                    <button class="opacity-0 group-hover/row:opacity-100 transition-opacity text-primary-end hover:text-primary mt-0.5" @click="openCreate(feature)">
                        <i class="pi pi-plus text-xs" />
                    </button>
                </div>
            </div>
            <TablePagination v-if="featurePaginator.total" :paginator="featurePaginator" label="features" :search="filters.search" @page="goPage" />
          </template>
        </Card>

        <!-- Create single -->
        <Dialog v-model:visible="showCreate" header="Create Permission" modal class="max-w-md">
            <form class="flex flex-col gap-4" @submit.prevent="createPermission">
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700">Feature <span class="text-gray-400 font-normal">(optional)</span></label>
                    <InputText v-model="createForm.feature" placeholder="e.g. posts, users, reports" fluid />
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-gray-700">Action</label>
                    <InputText v-model="createForm.action" placeholder="e.g. view, create, edit, delete" fluid :invalid="!!createErrors.action" />
                    <small v-if="createErrors.action" class="text-red-500">{{ createErrors.action }}</small>
                </div>
                <div v-if="createForm.action" class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-lg">
                    <span class="text-xs text-gray-500">Will create:</span>
                    <code class="text-xs font-mono font-semibold text-gray-800 bg-white border border-gray-200 px-1.5 py-0.5 rounded">{{ previewName }}</code>
                </div>
                <div class="flex justify-end gap-2">
                    <Button label="Cancel" severity="secondary" @click="showCreate = false" />
                    <Button label="Create" type="submit" :loading="createSaving" />
                </div>
            </form>
        </Dialog>

        <!-- Bulk create -->
        <Dialog v-model:visible="showBulk" modal class="max-w-xl">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center shrink-0">
                        <i class="pi pi-objects-column text-primary text-sm" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Bulk Create Permissions</p>
                        <p class="text-xs text-gray-400">Generate a full feature set in one step</p>
                    </div>
                </div>
            </template>
            <form id="bulk-form" class="flex flex-col gap-6" @submit.prevent="bulkCreate">
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex w-5 h-5 rounded-full bg-primary-gradient text-white text-[10px] font-bold items-center justify-center shrink-0">1</span>
                        <label class="text-sm font-semibold text-gray-700">Feature / module name</label>
                    </div>
                    <InputText v-model="bulkForm.feature" placeholder="posts, users, invoices…" class="font-mono" fluid autofocus :invalid="!!bulkErrors.feature" />
                    <small v-if="bulkErrors.feature" class="text-red-500">{{ bulkErrors.feature }}</small>
                    <p class="text-xs text-gray-400">Permissions will be named <code class="font-mono bg-gray-100 px-1 py-0.5 rounded text-gray-600">{{ bulkForm.feature.trim() || 'feature' }}.action</code></p>
                </div>
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex w-5 h-5 rounded-full bg-primary-gradient text-white text-[10px] font-bold items-center justify-center shrink-0">2</span>
                            <label class="text-sm font-semibold text-gray-700">Actions</label>
                            <span class="text-xs text-gray-400 tabular-nums">{{ bulkForm.actions.length }} selected</span>
                        </div>
                        <div class="flex items-center gap-0.5">
                            <button v-for="preset in actionPresets" :key="preset.label" type="button" class="px-1.5 py-0.5 rounded text-xs font-medium transition-colors" :class="isPresetActive(preset.actions) ? 'bg-red-100 text-primary' : 'text-gray-400 hover:bg-gray-100 hover:text-gray-700'" @click="applyPreset(preset.actions)">{{ preset.label }}</button>
                            <span class="w-px h-3 bg-gray-200 mx-0.5" />
                            <button type="button" class="px-1.5 py-0.5 rounded text-xs text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors" @click="bulkForm.actions = []">None</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                        <label
                            v-for="action in availableActions"
                            :key="action.key"
                            class="group flex items-center gap-3 px-3 py-2 rounded-xl border transition-all cursor-pointer"
                            :class="bulkForm.actions.includes(action.key) ? 'border-primary bg-red-50' : 'border-gray-100 bg-gray-50 hover:border-red-200 hover:bg-white'">

                            <Checkbox v-model="bulkForm.actions" :value="action.key" />

                            <i :class="[`pi ${action.icon}`, bulkForm.actions.includes(action.key) ? 'text-primary' : 'text-gray-400 group-hover:text-primary-end']" class="text-sm transition-colors shrink-0" />

                            <span class="text-sm font-medium transition-colors" :class="bulkForm.actions.includes(action.key) ? 'text-primary' : 'text-gray-600'">{{ action.label }}</span>
                        </label>
                    </div>
                    <small v-if="bulkErrors.actions" class="text-red-500">{{ bulkErrors.actions }}</small>
                </div>
                <div v-if="bulkForm.feature.trim() && bulkForm.actions.length" class="rounded-xl bg-gray-50 border border-dashed border-gray-200 px-3 py-3">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Will create {{ bulkForm.actions.length }} permission{{ bulkForm.actions.length !== 1 ? 's' : '' }}</p>
                    <div class="flex flex-wrap gap-1">
                        <code v-for="a in bulkForm.actions" :key="a" class="text-xs font-mono bg-white border border-gray-200 px-1.5 py-0.5 rounded text-gray-700">{{ bulkForm.feature.trim() }}.{{ a }}</code>
                    </div>
                </div>
            </form>
            <template #footer>
                <Button label="Cancel" severity="secondary" @click="closeBulk" />
                <Button type="submit" form="bulk-form" :label="bulkForm.actions.length ? `Create ${bulkForm.actions.length} permission${bulkForm.actions.length !== 1 ? 's' : ''}` : 'Select actions first'" icon="pi pi-sparkles" :loading="bulkSaving" :disabled="!bulkForm.feature.trim() || !bulkForm.actions.length" />
            </template>
        </Dialog>

        <!-- Delete confirm -->
        <Dialog v-model:visible="showDelete" header="Delete Permission" modal class="max-w-sm">
            <p class="text-gray-600 mb-0.5">Delete <code class="font-mono text-sm font-semibold text-gray-900">{{ deleteTarget?.name }}</code>?</p>
            <p class="text-sm text-gray-400">It will be removed from all roles and users.</p>
            <template #footer>
                <Button label="Cancel" severity="secondary" @click="showDelete = false" />
                <Button label="Delete" severity="danger" :loading="deleting" @click="doDelete" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast'
import { useLayout } from '../../composables/useLayout.js'
import TablePagination from '../../components/TablePagination.vue'
import api from '../../api/index.js'

const { setLayout } = useLayout()
setLayout('Permissions', 'Manage permissions grouped by feature')

const toast = useToast()

const groupedPermissions      = ref({})
const featurePaginator        = ref({ total: 0, current_page: 1, last_page: 1 })
const filteredPermissionCount = ref(0)
const totalPermissions        = ref(0)
const assignedPermissionIds   = ref([])
const totalUnassigned         = ref(0)
const filters = reactive({ search: '', unassigned: false, page: 1 })
const searchInput = ref('')
let searchTimer = null

const assignedSet = computed(() => new Set(assignedPermissionIds.value))
const isUnassigned = (id) => !assignedSet.value.has(id)
const unassignedInGroup = (perms) => perms.filter(p => isUnassigned(p.id)).length
const actionLabel = (name) => name.includes('.') ? name.split('.').slice(1).join('.') : name

const showCreate  = ref(false)
const showBulk    = ref(false)
const showDelete  = ref(false)
const createSaving = ref(false)
const bulkSaving   = ref(false)
const deleting     = ref(false)
const deletingId   = ref(null)
const deleteTarget = ref(null)
const createErrors = ref({})
const bulkErrors   = ref({})
const createForm = reactive({ feature: '', action: '' })
const bulkForm   = reactive({ feature: '', actions: ['view', 'create', 'edit', 'delete'] })

const previewName = computed(() => createForm.feature ? `${createForm.feature}.${createForm.action}` : createForm.action)

const availableActions = [
    { key: 'view',    icon: 'pi-eye',         label: 'View'    },
    { key: 'create',  icon: 'pi-plus-circle', label: 'Create'  },
    { key: 'edit',    icon: 'pi-pencil',       label: 'Edit'    },
    { key: 'delete',  icon: 'pi-trash',        label: 'Delete'  },
    { key: 'export',  icon: 'pi-upload',       label: 'Export'  },
    { key: 'import',  icon: 'pi-download',     label: 'Import'  },
    { key: 'approve', icon: 'pi-thumbs-up',    label: 'Approve' },
    { key: 'publish', icon: 'pi-send',         label: 'Publish' },
]
const actionPresets = [
    { label: 'CRUD', actions: ['view', 'create', 'edit', 'delete'] },
    { label: 'Read', actions: ['view'] },
    { label: 'Full', actions: ['view', 'create', 'edit', 'delete', 'export', 'import', 'approve', 'publish'] },
]

async function load() {
    const { data } = await api.getPermissions({
        search:     filters.search || undefined,
        unassigned: filters.unassigned ? 1 : undefined,
        page:       filters.page,
    })
    groupedPermissions.value      = data.groupedPermissions
    featurePaginator.value        = data.featurePaginator
    filteredPermissionCount.value = data.filteredPermissionCount
    totalPermissions.value        = data.totalPermissions
    assignedPermissionIds.value   = data.assignedPermissionIds
    totalUnassigned.value         = data.totalUnassigned
}

function onSearch() {
    clearTimeout(searchTimer)
    searchTimer = setTimeout(() => { filters.search = searchInput.value; filters.page = 1; load() }, 350)
}
function toggleUnassigned() { filters.unassigned = !filters.unassigned; filters.page = 1; load() }
function goPage(p) { filters.page = p; load() }

function openCreate(feature = '') { createForm.feature = feature; createForm.action = ''; createErrors.value = {}; showCreate.value = true }
function openBulk(feature = '')   { bulkForm.feature = feature; bulkForm.actions = ['view', 'create', 'edit', 'delete']; bulkErrors.value = {}; showBulk.value = true }
function closeBulk() { showBulk.value = false; bulkForm.feature = ''; bulkForm.actions = ['view', 'create', 'edit', 'delete']; bulkErrors.value = {} }
function isPresetActive(actions) { return actions.length === bulkForm.actions.length && actions.every(a => bulkForm.actions.includes(a)) }
function applyPreset(actions) { bulkForm.actions = [...actions] }
function confirmDelete(perm) { deleteTarget.value = perm; showDelete.value = true }

async function createPermission() {
    createErrors.value = {}
    createSaving.value = true
    try {
        await api.createPermission(createForm)
        showCreate.value = false
        toast.add({ severity: 'success', summary: 'Created', detail: `Permission created.`, life: 3000 })
        load()
    } catch (e) {
        createErrors.value = e.response?.data?.errors ?? {}
    } finally {
        createSaving.value = false
    }
}

async function bulkCreate() {
    bulkErrors.value = {}
    bulkSaving.value = true
    try {
        const { data } = await api.bulkCreatePermissions(bulkForm)
        closeBulk()
        toast.add({ severity: 'success', summary: 'Created', detail: data.message, life: 3000 })
        load()
    } catch (e) {
        bulkErrors.value = e.response?.data?.errors ?? {}
    } finally {
        bulkSaving.value = false
    }
}

async function doDelete() {
    deletingId.value = deleteTarget.value.id
    deleting.value = true
    try {
        await api.deletePermission(deleteTarget.value.id)
        showDelete.value = false
        toast.add({ severity: 'success', summary: 'Deleted', detail: 'Permission deleted.', life: 3000 })
        load()
    } finally {
        deleting.value = false
        deletingId.value = null
    }
}

onMounted(load)
</script>
