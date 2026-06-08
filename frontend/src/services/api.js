import axios from 'axios'
import { useLoadingStore } from '../stores/useLoadingStore'

const api = axios.create({
    baseURL: 'http://localhost/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    }
})

// Intercepteur — ajoute le token auth si connecté + session_token si anonyme
api.interceptors.request.use(config => {
    const authToken = localStorage.getItem('auth_token')
    const sessionToken = localStorage.getItem('session_token')

    if (authToken) {
        config.headers.Authorization = `Bearer ${authToken}`
    }

    if (sessionToken) {
        config.headers['X-Session-Token'] = sessionToken
    }

    return config
})

// Intercepteur réponse — gestion globale des erreurs
api.interceptors.response.use(
    response => {
        useLoadingStore().stop()
        return response
    },
    error => {
        useLoadingStore().stop()

        if (error.response?.status === 401) {
            localStorage.removeItem('auth_token')
            window.location.href = '/login'
        }
        return Promise.reject(error)
    }
)

// Services
export const seasonService = {
    getAll: () => api.get('/seasons'),
    getOne: (id) => api.get(`/seasons/${id}`),
}

export const teamService = {
    getAll: (seasonId) => api.get('/teams', { params: { season_id: seasonId } }),
    getOne: (id) => api.get(`/teams/${id}`),
}

export const playerService = {
    getAll: (params) => api.get('/players', { params }),
    getOne: (id) => api.get(`/players/${id}`),
    roll: (data) => api.post('/players/roll', data),
}

export const positionService = {
    getAll: () => api.get('/positions'),
}

export const draftService = {
    getAll: () => api.get('/drafts'),
    getOne: (id) => api.get(`/drafts/${id}`),
    create: (data) => api.post('/drafts', data),
    update: (id, data) => api.put(`/drafts/${id}`, data),
    delete: (id) => api.delete(`/drafts/${id}`),
    addPlayer: (id, data) => api.post(`/drafts/${id}/players`, data),
    removePlayer: (draftId, playerId) => api.delete(`/drafts/${draftId}/players/${playerId}`),
}

export const authService = {
    register: (data) => api.post('/register', data),
    login: (data) => api.post('/login', data),
    logout: () => api.post('/logout'),
}

export default api