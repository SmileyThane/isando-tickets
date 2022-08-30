export default {
    namespaced: true,
    state: {
        selectedIR: {
            id: 0,
            version: '',
            categories: [],
            clients: [],
            actions: [],
            description: '',
            stage_monitoring: null
        },
        IR: [],
        search: '',
        searchWhere: [1,2,3],
        activeTags: [],
    },
    actions: {
        callGetIR({ commit }) {
            return axios.get(`/api/ir`, {
                params: {}
            }).then(({ status, data: { data, success } }) => {
                if (status === 200 && success) {
                    commit('setIR', data)
                    if (data.length > 0) {
                        commit('setSelectedIR', data[0])
                    }
                    return Promise.resolve(data)
                }
                commit('setIR', [])
                return Promise.reject([])
            });
        },
        callDeleteIR({ commit, dispatch }, id) {
            return axios.delete(`/api/ir/${id}`, {
                params: {}
            }).then(({ status, data: { data, success } }) => {
                if (status === 200 && success) {
                    dispatch('callGetIR')
                }
                commit('setIR', [])
                return Promise.reject([])
            });
        },
        callSetSelectedIR({commit}, data) {
            commit('setSelectedIR', data)
        }
    },
    mutations: {
        setIR: (state, data) => state.IR = data,
        setSelectedIR: (state, data) => {
            state.selectedIR = data
        },
        setSearch: (state, data) => state.search = data,
        setSearchWhere: (state, data) => state.searchWhere = data,
        setActiveTags: (state, data) => state.activeTags = data,
    },
    getters: {
        getIR: state => state.IR,
        getSelectedIR: state => state.selectedIR,
        getSearch: state => state.search,
        getSearchWhere: state => state.searchWhere,
        getActiveTags: state => state.activeTags,
    }
}

