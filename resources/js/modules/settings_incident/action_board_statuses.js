export default {
    namespaced: true,
    state: {
        action_board_statuses: [],
        action_board_status: {
            id: null,
            name: '',
        },
    },
    actions: {
        callList({commit}) {
            return axios.get(`/api/ir/action_board_statuses`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('setActionBoardStatuses', data)
                        return data;
                    }
                    return []
                })
        },
        callAdd({commit}, data) {
            return axios.post(`/api/ir/action_board_status`, data, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('addActionBoardStatus', data)
                        return data;
                    }
                    return []
                })
        },
        callEdit({commit, state}) {
            return axios.put(`/api/ir/action_board_status/${state.action_board_status.id}`, state.action_board_status, {
                retry: 5,
                retryDelay: 1000
            })
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('editActionBoardStatus', data)
                        return data;
                    }
                    return []
                })
        },
        callDelete({commit}, id) {
            return axios.delete(`/api/ir/action_board_status/${id}`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('deleteActionBoardStatus', id)
                        return data;
                    }
                    return []
                })
        }
    },
    mutations: {
        setActionBoardStatuses: (state, data) => state.action_board_statuses = data,
        addActionBoardStatus: (state, data) => state.action_board_statuses.push(data),
        editActionBoardStatus: (state, data) => state.action_board_statuses.splice(state.action_board_statuses.findIndex(i => i.id === data.id), 1, data),
        deleteActionBoardStatus: (state, id) => state.action_board_statuses.splice(state.action_board_statuses.findIndex(i => i.id === id), 1),
        selectItem: (state, id) => state.action_board_status = state.action_board_statuses.find(i => i.id === id),
        unSelectItem: (state) => state.action_board_status = {
            id: null,
            name: '',
        },
        setName: (state, name) => state.action_board_status.name = name,
    },
    getters: {
        getItems: state => state.action_board_statuses,
        getItem: (state, id) => state.action_board_status,
    },
}
