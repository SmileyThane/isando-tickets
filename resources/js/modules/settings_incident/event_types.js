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
        callList({commit}) {
            return axios.get(`/api/ir/event_types`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('setActionTypes', data)
                        return data;
                    }
                    return []
                })
        },
        callAdd({commit}, data) {
            return axios.post(`/api/ir/event_type`, data, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('addActionType', data)
                        return data;
                    }
                    return []
                })
        },
        callEdit({commit, state}) {
            return axios.put(`/api/ir/event_type/${state.event_type.id}`, state.event_type, {
                retry: 5,
                retryDelay: 1000
            })
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('editActionType', data)
                        return data;
                    }
                    return []
                })
        },
        callDelete({commit}, id) {
            return axios.delete(`/api/ir/event_type/${id}`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
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
        editActionType: (state, data) => state.event_types.splice(state.event_types.findIndex(i => i.id === data.id), 1, data),
        deleteActionType: (state, id) => state.event_types.splice(state.event_types.findIndex(i => i.id === id), 1),
        selectItem: (state, id) => state.event_type = state.event_types.find(i => i.id === id),
        unSelectItem: (state) => state.event_type = {
            id: null,
            name: '',
        },
        setName: (state, name) => state.event_type.name = name,
    },
    getters: {
        getItems: state => state.event_types,
        getItem: (state, id) => state.event_type,
    },
}
