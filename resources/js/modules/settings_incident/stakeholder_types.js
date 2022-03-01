export default {
    namespaced: true,
    state: {
        stakeholder_types: [],
        stakeholder_type: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({ commit }) {
            return axios.get(`/api/ir/stakeholder_types`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('setActionTypes', data)
                        return data;
                    }
                    return []
                })
                .catch(e)
        },
        calAdd({ commit }, data) {
            return axios.post(`/api/ir/stakeholder_type`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('addActionType', data)
                        return data;
                    }
                    return []
                })
                .catch(e)
        },
        calEdit({ commit }, data) {
            return axios.put(`/api/ir/stakeholder_type/${data.id}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('editActionType', data)
                        return data;
                    }
                    return []
                })
                .catch(e)
        },
        calDelete({ commit }, id) {
            return axios.delete(`/api/ir/stakeholder_type/${id}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('deleteActionType', id)
                        return data;
                    }
                    return []
                })
                .catch(e)
        }
    },
    mutations: {
        setActionTypes: (state, data) => state.stakeholder_types = data,
        addActionType: (state, data) => state.stakeholder_types.push(data),
        editActionType: (state, data) => state.stakeholder_types.splice(state.stakeholder_types.findIndex(i=>i.id === data.id), 1, data),
        deleteActionType: (state, id) => state.stakeholder_types.splice(state.stakeholder_types.findIndex(i=>i.id === id), 1),
    },
    getters: {
        getItems: state => state.stakeholder_types,
        getItem: (state, id) => state.stakeholder_types.find(i => i.id === id),
    },
}
