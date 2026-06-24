import axios from 'axios'

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:9188/api/permission-gui',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
})

export default {
    // Dashboard
    getDashboard: (params) => api.get('/dashboard', { params }),

    // Roles
    getRoles:    (params) => api.get('/roles', { params }),
    getRole:     (id)     => api.get(`/roles/${id}`),
    createRole:  (data)   => api.post('/roles', data),
    updateRole:  (id, data) => api.patch(`/roles/${id}`, data),
    deleteRole:  (id)     => api.delete(`/roles/${id}`),

    // Permissions
    getPermissions:    (params) => api.get('/permissions', { params }),
    createPermission:  (data)   => api.post('/permissions', data),
    bulkCreatePermissions: (data) => api.post('/permissions/bulk', data),
    deletePermission:  (id)     => api.delete(`/permissions/${id}`),

    // Users
    getUsers:   (params)    => api.get('/users', { params }),
    getUser:    (id)        => api.get(`/users/${id}`),
    updateUser: (id, data)  => api.patch(`/users/${id}`, data),
}
