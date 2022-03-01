export default {
    namespaced: true,
    state: {
        focus_priorities: [],
        focus_priority: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({ commit }) {
            return axios.get(`/api/ir/focus_priorities`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('setActionTypes', data)
                        return data;
                    }
                    return []
                })
        },
        calAdd({ commit }, data) {
            return axios.post(`/api/ir/focus_priority`, data,{ retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('addActionType', data)
                        return data;
                    }
                    return []
                })
        },
        calEdit({ commit, state }) {
            return axios.put(`/api/ir/focus_priority/${state.focus_priority.id}`, state.focus_priority,{ retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('editActionType', data)
                        return data;
                    }
                    return []
                })
        },
        calDelete({ commit }, id) {
            return axios.delete(`/api/ir/focus_priority/${id}`, { retry: 5, retryDelay: 1000 })
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
        setActionTypes: (state, data) => state.focus_priorities = data,
        addActionType: (state, data) => state.focus_priorities.push(data),
        editActionType: (state, data) => state.focus_priorities.splice(state.focus_priorities.findIndex(i=>i.id === data.id), 1, data),
        deleteActionType: (state, id) => state.focus_priorities.splice(state.focus_priorities.findIndex(i=>i.id === id), 1),
    },
    getters: {
        getItems: state => state.focus_priorities,
        getItem: (state, id) => state.focus_priorities.find(i => i.id === id),
    },
}
