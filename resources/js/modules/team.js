export default {
    namespaced: true,
    state: {
        coworkers: []
    },
    actions: {
        getCoworkers({commit}, { search }) {
            const queryParams = new URLSearchParams({
                search: search ?? ''
            });
            axios.get(`/api/tracking/coworkers?${queryParams.toString()}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data: coworkers } }) => {
                    if (success) {
                        commit('GET_COWORKERS', coworkers)
                    }
                })
        }
    },
    mutations: {
        GET_COWORKERS(state, coworkers) {
            state.coworkers = coworkers
        }
    },
    getters: {
        getCoworkers(state) {
            return state.coworkers
        }
    }
}

