export default {
    namespaced: true,
    state: {
        process_states: [],
        process_state: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({ commit }) {
            return axios.get(`/api/ir/process_states`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('setActionTypes', data)
                        return data;
                    }
                    return []
                })
        },
        callAdd({ commit }, data) {
            return axios.post(`/api/ir/process_state`, data,{ retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('addActionType', data)
                        return data;
                    }
                    return []
                })
        },
        callEdit({ commit, state }) {
            return axios.put(`/api/ir/process_state/${state.process_state.id}`, state.process_state,{ retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('editActionType', data)
                        return data;
                    }
                    return []
                })
        },
        callDelete({ commit }, id) {
            return axios.delete(`/api/ir/process_state/${id}`, { retry: 5, retryDelay: 1000 })
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
        setActionTypes: (state, data) => state.process_states = data,
        addActionType: (state, data) => state.process_states.push(data),
        editActionType: (state, data) => state.process_states.splice(state.process_states.findIndex(i=>i.id === data.id), 1, data),
        deleteActionType: (state, id) => state.process_states.splice(state.process_states.findIndex(i=>i.id === id), 1),
        selectItem: (state, id) => state.process_state = state.process_states.find(i => i.id === id),
        unSelectItem: (state) => state.process_state = {
            id: null,
            name: '',
        },
        setName: (state, name) => state.process_state.name = name,
    },
    getters: {
        getItems: state => state.process_states,
        getItem: (state, id) => state.process_state,
    },
}
