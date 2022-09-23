export default {
    namespaced: true,
    state: {
        impact_potentials: [],
        impact_potential: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({commit}) {
            return axios.get(`/api/ir/impact_potentials`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('setActionTypes', data)
                        return data;
                    }
                    return []
                })
        },
        callAdd({commit}, data) {
            return axios.post(`/api/ir/impact_potential`, data, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('addActionType', data)
                        return data;
                    }
                    return []
                })
        },
        callEdit({commit, state}) {
            return axios.put(`/api/ir/impact_potential/${state.impact_potential.id}`, state.impact_potential, {
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
            return axios.delete(`/api/ir/impact_potential/${id}`, {retry: 5, retryDelay: 1000})
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
        setActionTypes: (state, data) => state.impact_potentials = data,
        addActionType: (state, data) => state.impact_potentials.push(data),
        editActionType: (state, data) => state.impact_potentials.splice(state.impact_potentials.findIndex(i => i.id === data.id), 1, data),
        deleteActionType: (state, id) => state.impact_potentials.splice(state.impact_potentials.findIndex(i => i.id === id), 1),
        selectItem: (state, id) => state.impact_potential = state.impact_potentials.find(i => i.id === id),
        unSelectItem: (state) => state.impact_potential = {
            id: null,
            name: '',
        },
        setName: (state, name) => state.impact_potential.name = name,
    },
    getters: {
        getItems: state => state.impact_potentials,
        getItem: (state, id) => state.impact_potential,
    },
}
