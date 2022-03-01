export default {
    namespaced: true,
    state: {
        resource_types: [],
        resource_type: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({ commit }) {
            return axios.get(`/api/ir/resource_types`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('setActionTypes', data)
                        return data;
                    }
                    return []
                })
        },
        calAdd({ commit }, data) {
            return axios.post(`/api/ir/resource_type`, data, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('addActionType', data)
                        return data;
                    }
                    return []
                })
        },
        calEdit({ commit, state }) {
            return axios.put(`/api/ir/resource_type/${state.resource_type.id}`, state.resource_type,{ retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('editActionType', data)
                        return data;
                    }
                    return []
                })
        },
        calDelete({ commit }, id) {
            return axios.delete(`/api/ir/resource_type/${id}`, { retry: 5, retryDelay: 1000 })
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
        setActionTypes: (state, data) => state.resource_types = data,
        addActionType: (state, data) => state.resource_types.push(data),
        editActionType: (state, data) => state.resource_types.splice(state.resource_types.findIndex(i=>i.id === data.id), 1, data),
        deleteActionType: (state, id) => state.resource_types.splice(state.resource_types.findIndex(i=>i.id === id), 1),
    },
    getters: {
        getItems: state => state.resource_types,
        getItem: (state, id) => state.resource_types.find(i => i.id === id),
    },
}
