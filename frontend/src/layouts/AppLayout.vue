<template>
    <div class="min-h-screen flex bg-app-bg">
        <Toast position="top-right" />

        <!-- Mobile: PrimeVue Drawer (only opens via hamburger, which is md:hidden) -->
        <Drawer v-model:visible="sidebarOpen" position="left" :showCloseIcon="false">
            <template #header>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-primary-gradient rounded-lg flex items-center justify-center">
                        <i class="pi pi-lock text-white text-sm" />
                    </div>
                    <span class="font-semibold text-gray-900 text-sm">Permission Manager</span>
                </div>
            </template>

            <nav class="flex-1 px-3 py-3 space-y-4 overflow-y-auto">
                <div v-for="group in navGroups" :key="group.label">
                    <p class="px-3 mb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">{{ group.label }}</p>
                    <RouterLink
                        v-for="item in group.items"
                        :key="item.name"
                        :to="{ name: item.name }"
                        :class="['flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors w-full', isActive(item.name) ? 'bg-red-50 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900']"
                        @click="sidebarOpen = false"
                    >
                        <i :class="['text-base', item.icon]" />
                        {{ item.label }}
                    </RouterLink>
                </div>
            </nav>
        </Drawer>

        <!-- Desktop: static sidebar -->
        <aside class="hidden md:flex flex-col w-64 shrink-0 bg-white border-r border-gray-200">
            <div class="px-6 py-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-primary-gradient rounded-lg flex items-center justify-center">
                        <i class="pi pi-lock text-white text-sm" />
                    </div>
                    <span class="font-semibold text-gray-900 text-sm">Permission Manager</span>
                </div>
            </div>
            <nav class="flex-1 px-3 py-3 space-y-4 overflow-y-auto">
                <div v-for="group in navGroups" :key="group.label">
                    <p class="px-3 mb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-widest">{{ group.label }}</p>
                    <RouterLink
                        v-for="item in group.items"
                        :key="item.name"
                        :to="{ name: item.name }"
                        :class="['flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors w-full', isActive(item.name) ? 'bg-red-50 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900']"
                    >
                        <i :class="['text-base', item.icon]" />
                        {{ item.label }}
                    </RouterLink>
                </div>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-gray-200 px-4 sm:px-8 py-3 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        class="md:hidden p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors shrink-0"
                        @click="sidebarOpen = true"
                    >
                        <i class="pi pi-bars text-base" />
                    </button>
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold text-gray-900 truncate">{{ layout.title.value }}</h1>
                        <p v-if="layout.subtitle.value" class="text-sm text-gray-500 mt-0.5 truncate">{{ layout.subtitle.value }}</p>
                    </div>
                </div>
                <div id="layout-header-actions" class="shrink-0"></div>
            </header>

            <main class="flex-1 px-4 sm:px-8 py-6">
                <RouterView v-slot="{ Component }">
                    <Transition name="page" mode="out-in">
                        <component :is="Component" :key="route.path" />
                    </Transition>
                </RouterView>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, RouterView, useRoute } from 'vue-router'
import { useLayout } from '../composables/useLayout.js'

const layout = useLayout()
const route  = useRoute()

const sidebarOpen = ref(false)

function isActive(name) {
    return route.name === name || route.name?.startsWith(name + '.')
}

const navGroups = [
    {
        label: 'Spatie Laravel Permission v8',
        items: [
            { label: 'Overview',    name: 'dashboard',    icon: 'pi pi-home'   },
            { label: 'Roles',       name: 'roles',        icon: 'pi pi-shield' },
            { label: 'Permissions', name: 'permissions',  icon: 'pi pi-key'    },
            { label: 'Users',       name: 'users',        icon: 'pi pi-users'  },
        ],
    },
]
</script>

<style>
.page-enter-active,
.page-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.page-enter-from {
    opacity: 0;
    transform: translateY(8px);
}
.page-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
