export default {
    namespaced: true,
    state: {
        event_types: [],
        event_type: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({ commit }) {
            return axios.get(`/api/ir/event_types`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('setActionTypes', data)
                        return data;
                    }
                    return []
                })
        },
        calAdd({ commit }, data) {
            return axios.post(`/api/ir/event_type`, data,{ retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('addActionType', data)
                        return data;
                    }
                    return []
                })
        },
        calEdit({ commit, state }) {
            return axios.put(`/api/ir/event_type/${state.event_type.id}`, state.event_type, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('editActionType', data)
                        return data;
                    }
                    return []
                })
        },
        calDelete({ commit }, id) {
            return axios.delete(`/api/ir/event_type/${id}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('deleteActionType', id)
                        return data;
                    }
                    return []
                })
        }
    },
    mutations: {
        setActionTypes: (state, data) => state.event_types = data,
        addActionType: (state, data) => state.event_types.push(data),
        editActionType: (state, data) => state.event_types.splice(state.event_types.findIndex(i=>i.id === data.id), 1, data),
        deleteActionType: (state, id) => state.event_types.splice(state.event_types.findIndex(i=>i.id === id), 1),
    },
    getters: {
        getItems: state => state.event_types,
        getItem: (state, id) => state.event_types.find(i => i.id === id),
    },
}
