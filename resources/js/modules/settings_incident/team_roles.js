export default {
    namespaced: true,
    state: {
        team_roles: [],
        team_role: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({commit}) {
            return axios.get(`/api/ir/team_roles`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('setTeamRoles', data)
                        return data;
                    }
                    return []
                })
        },
        callAdd({commit}, data) {
            return axios.post(`/api/ir/team_role`, data, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('addTeamRole', data)
                        return data;
                    }
                    return []
                })
        },
        callEdit({commit, state}) {
            return axios.put(`/api/ir/team_role/${state.team_role.id}`, state.team_role, {
                retry: 5,
                retryDelay: 1000
            })
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('editTeamRole', data)
                        return data;
                    }
                    return []
                })
        },
        callDelete({commit}, id) {
            return axios.delete(`/api/ir/team_role/${id}`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('deleteTeamRole', id)
                        return data;
                    }
                    return []
                })
        }
    },
    mutations: {
        setTeamRoles: (state, data) => state.team_roles = data,
        addTeamRole: (state, data) => state.team_roles.push(data),
        editTeamRole: (state, data) => state.team_roles.splice(state.team_roles.findIndex(i => i.id === data.id), 1, data),
        deleteTeamRole: (state, id) => state.team_roles.splice(state.team_roles.findIndex(i => i.id === id), 1),
        selectItem: (state, id) => state.team_role = state.team_roles.find(i => i.id === id),
        unSelectItem: (state) => state.team_role = {
            id: null,
            name: '',
        },
        setName: (state, name) => state.team_role.name = name,
    },
    getters: {
        getItems: state => state.team_roles,
        getItem: (state, id) => state.team_role,
    },
}
