export default {
    namespaced: true,
    state: {
        clients: []
    },
    actions: {
        getClientList({commit}, { search }) {
            const queryParams = new URLSearchParams({
                search: search ?? ''
            });
            axios.get(`/api/tracking/clients?${queryParams.toString()}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data: clients } }) => {
                    if (success) {
                        commit('GET_CLIENTS', clients)
                    }
                })
        }
    },
    mutations: {
        GET_CLIENTS(state, clients) {
            state.clients = clients
        }
    },
    getters: {
        getClients(state) {
            return state.clients
        }
    }
}

