import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/',              redirect: { name: 'dashboard' } },
        { path: '/dashboard',     name: 'dashboard',         component: () => import('../pages/Dashboard.vue') },
        { path: '/roles',         name: 'roles',             component: () => import('../pages/Roles/Index.vue') },
        { path: '/roles/:id',     name: 'roles.show',        component: () => import('../pages/Roles/Show.vue') },
        { path: '/permissions',   name: 'permissions',       component: () => import('../pages/Permissions/Index.vue') },
        { path: '/users',         name: 'users',             component: () => import('../pages/Users/Index.vue') },
        { path: '/users/:id',     name: 'users.show',        component: () => import('../pages/Users/Show.vue') },
    ],
})

export default router
