export default {
    namespaced: true,
    state: {
        action_types: [],
        action_type: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({ commit }) {
            return axios.get(`/api/ir/action_types`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('setActionTypes', data)
                        return data;
                    }
                    return []
                })
        },
        calAdd({ commit }, data) {
            return axios.post(`/api/ir/action_type`, data,{ retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('addActionType', data)
                        return data;
                    }
                    return []
                })
        },
        calEdit({ commit, state }) {
            return axios.put(`/api/ir/action_type/${state.action_type.id}`, state.action_type,{ retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('editActionType', data)
                        return data;
                    }
                    return []
                })
        },
        calDelete({ commit }, id) {
            return axios.delete(`/api/ir/action_type/${id}`, { retry: 5, retryDelay: 1000 })
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
        setActionTypes: (state, data) => state.action_types = data,
        addActionType: (state, data) => state.action_types.push(data),
        editActionType: (state, data) => state.action_types.splice(state.action_types.findIndex(i=>i.id === data.id), 1, data),
        deleteActionType: (state, id) => state.action_types.splice(state.action_types.findIndex(i=>i.id === id), 1),
    },
    getters: {
        getItems: state => state.action_types,
        getItem: (state, id) => state.action_types.find(i => i.id === id),
    },
}
