export default {
    namespaced: true,
    state: {
        services: [],
        search: null
    },
    actions: {
        getServicesList({commit}, { search }) {
            commit('SET_SEARCH', search);
            const queryParams = new URLSearchParams({
                search: search ?? ''
            });
            axios.get(`/api/services?${queryParams.toString()}`)
                .then(({ data: { success, data: services } }) => {
                    if (success) {
                        commit('GET_SERVICES', services)
                    }
                })
        },
        createService({commit, dispatch, state}, {name}) {
            return axios.post('/api/services', {name})
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getServicesList', { search: state.search })
                        return data
                    }
                })
        },
        updateService({commit, dispatch, state}, {id, name}) {
            return axios.patch(`/api/services/${id}`, {name})
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getServicesList', { search: state.search })
                        return data
                    }
                })
        },
        deleteService({commit, dispatch, state}, serviceId) {
            return axios.delete(`/api/services/${serviceId}`)
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getServicesList', { search: state.search })
                    }
                    return success
                })
        }
    },
    mutations: {
        GET_SERVICES(state, services) {
            state.services = services
        },
        SET_SEARCH(state, search) {
            state.search = search
        }
    },
    getters: {
        getServices(state) {
            return state.services
        },
        getSearch(state) {
            return state.search
        }
    }
}

