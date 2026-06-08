import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useSimulStore = defineStore('simul', () => {
    const matchEvents = ref([])
    const scoreHome = ref(0)
    const scoreAway = ref(0)
    const minute = ref(0)
    const isRunning = ref(false)
    const isFinished = ref(false)
    const homeDraft = ref(null)
    const awayDraft = ref(null)

    const reset = () => {
        matchEvents.value = []
        scoreHome.value = 0
        scoreAway.value = 0
        minute.value = 0
        isRunning.value = false
        isFinished.value = false
    }

    const setDrafts = (home, away) => {
        homeDraft.value = home
        awayDraft.value = away
    }

    const addEvent = (event) => {
        matchEvents.value.push(event)
    }

    const updateScore = (home, away) => {
        scoreHome.value = home
        scoreAway.value = away
    }

    return {
        matchEvents, scoreHome, scoreAway,
        minute, isRunning, isFinished,
        homeDraft, awayDraft,
        reset, setDrafts, addEvent, updateScore
    }
})