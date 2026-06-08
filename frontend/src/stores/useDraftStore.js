import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useDraftStore = defineStore('draft', () => {
    const players = ref([])

    const setPlayers = (list) => {
        players.value = list ?? []
    }

    const addPlayer = (player) => {
        players.value.push(player)
    }

    const removePlayer = (playerId) => {
        players.value = players.value.filter(p => p.id !== playerId)
    }

    const reset = () => {
        players.value = []
    }

    return { players, setPlayers, addPlayer, removePlayer, reset }
})