export default {
    namespaced: true,
    state: {
        selectedIR: {
            id: 0,
            with_child_clients: false,
            version: '',
            categories: [],
            clients: [],
            actions: [],
            child_versions: [],
            priority_id: null,
            stage_monitoring_id: null,
            access_id: null,
            description: '',
            stage_monitoring: null
        },
        selectedIRAction: {
            id: null,
            name: '',
            description: '',
            type_id: 0,
            deadline_time_indicator: null,
            deadline_time_value: null,
            deadline_time_parameter: null,
            user_id: null
        },
        isEditable: false,
        manageActionDlg: false,
        IR: [],
        IRActions: [],
        options: {
            categories: [],
            priorities: [],
            states: [],
            accesses: [],
            stage_monitorings: [],
            actions: {
                deadline_time_parameters: [],
                deadline_time_indicators: []
            }
        },
        search: '',
        searchWhere: [1, 2, 3],
        activeTags: [],
    },
    actions: {
        callGetIR({commit}) {
            return axios.get(`/api/ir`, {
                params: {}
            }).then(({status, data: {data, success}}) => {
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
        callGetIRActions({commit}) {
            return axios.get(`/api/ir/actions`, {
                params: {}
            }).then(({status, data: {data, success}}) => {
                if (status === 200 && success) {
                    commit('setIRActions', data)
                    return Promise.resolve(data)
                }
                commit('setIRActions', [])

                return Promise.reject([])
            });
        },
        callGetIROptions({commit}) {
            return axios.get(`/api/ir/options`)
                .then(({status, data: {data, success}}) => {
                    if (status === 200 && success) {
                        commit('setIROptions', data)

                        return Promise.resolve(data)
                    }
                    commit('setIROptions', [])

                    return Promise.reject([])
                });
        },
        callDeleteIR({commit, dispatch}, id) {
            return axios.delete(`/api/ir/${id}`, {
                params: {}
            }).then(({status, data: {data, success}}) => {
                if (status === 200 && success) {
                    dispatch('callGetIR')
                }
                commit('setIR', [])

                return Promise.reject([])
            });
        },
        callStoreIR({commit, dispatch, state}, incrementVersion) {
            let method = 'post'
            let url = `/api/ir`
            if (state.selectedIR.id) {
                method = 'put'
                url += `/${state.selectedIR.id}`
                if (incrementVersion === true) {
                    method = 'post'
                    url += '/clone'
                }
            }

            return axios({method, url, data: state.selectedIR})
                .then(({status, data: {data, success}}) => {
                    if (status === 200 && success) {
                        commit('setSelectedIR', data)
                        dispatch('callSetIsEditable', false)
                        return Promise.resolve(data)
                    }
                    dispatch('callGetIR')

                    return Promise.reject([])
                });

        },
        callStoreIRAction({commit, dispatch, state}) {
            let method = 'post'
            let url = `/api/ir/actions`
            if (state.selectedIRAction.id) {
                method = 'put'
                url += `/${state.selectedIR.id}`
            }

            return axios({method, url, data: state.selectedIRAction})
                .then(({status, data: {data, success}}) => {
                    if (status === 200 && success) {
                        dispatch('callSetManageActionDlg', false)
                        return Promise.resolve(data)
                    }
                    dispatch('callSetManageActionDlg', true)

                    return Promise.reject([])
                });

        },
        callSetSelectedIR({commit}, data) {
            if (data === null) {
                data = {
                    with_child_clients: false,
                    version: '',
                    categories: [],
                    clients: [],
                    actions: [],
                    priority_id: null,
                    access_id: null,
                    stage_monitoring_id: null,
                    description: '',
                    stage_monitoring: null
                }
            }
            commit('setSelectedIR', data)
        },
        callSetIsEditable({commit}, data) {
            commit('setIsEditable', data)
        },
        callSetManageActionDlg({commit}, data) {
            commit('setManageActionDlg', data)
        }
    },
    mutations: {
        setIR: (state, data) => state.IR = data,
        setIRActions: (state, data) => state.IRActions = data,
        setIROptions: (state, data) => state.options = data,
        setIsEditable: (state, data) => state.isEditable = data,
        setManageActionDlg: (state, data) => state.manageActionDlg = data,
        setSelectedIR: (state, data) => {
            state.selectedIR = data
        },
        setSearch: (state, data) => state.search = data,
        setSearchWhere: (state, data) => state.searchWhere = data,
        setActiveTags: (state, data) => state.activeTags = data,
    },
    getters: {
        getIR: state => state.IR,
        getIRActions: state => state.IRActions,
        getIROptions: state => state.options,
        getIsEditable: state => state.isEditable,
        getManageActionDlg: state => state.manageActionDlg,
        getSelectedIR: state => state.selectedIR,
        getSelectedIRAction: state => state.selectedIRAction,
        getSearch: state => state.search,
        getSearchWhere: state => state.searchWhere,
        getActiveTags: state => state.activeTags,
    }
}

