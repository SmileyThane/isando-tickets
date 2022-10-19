export default {
    namespaced: true,
    state: {
        selectedIR: {
            id: 0,
            with_child_clients: false,
            version: '',
            valid_till: '',
            updated_by: {
                name: '',
                surname: ''
            },
            categories: [],
            clients: [],
            actions: [],
            action_boards: [],
            child_versions: [],
            priority_id: null,
            impact_potential_id: null,
            stage_monitoring_id: null,
            access_id: null,
            description: '',
            stage_monitoring: null,
            status: {
                name: ''
            },
            source: '',
            reported_on: null,
            detected_on: null,
            occurred_on: null

        },
        relatedIR: {
            id: 0,
            with_child_clients: false,
            version: '',
            valid_till: '',
            updated_by: {
                name: '',
                surname: ''
            },
            categories: [],
            clients: [],
            actions: [],
            action_boards: [],
            child_versions: [],
            priority_id: null,
            impact_potential_id: null,
            stage_monitoring_id: null,
            access_id: null,
            description: '',
            stage_monitoring: null,
            status: {
                name: ''
            },
            source: '',
            reported_on: null,
            detected_on: null,
            occurred_on: null

        },
        IRType: 1,
        selectedIRAction: {
            id: null,
            name: '',
            description: '',
            type_id: 0,
            deadline_time_indicator: 'after',
            deadline_time_value: null,
            deadline_time_parameter: null,
            user_id: null
        },
        isEditable: false,
        manageActionDlg: false,
        IR: [],
        employees: [],
        IRActions: [],
        options: {
            categories: [],
            priorities: [],
            states: [],
            accesses: [],
            impact_potentials: [],
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
        callGetIR({commit, dispatch, state}) {
            return axios.get(`/api/ir/ab/${state.IRType}`, {
                params: {}
            }).then(({status, data: {data, success}}) => {
                if (status === 200 && success) {
                    commit('setIR', data)
                    if (data.length > 0) {
                        const item = state.selectedIR.id ?
                            data.find(element => element.id = state.selectedIR.id) :
                            data[0]
                        commit('setSelectedIR', item)
                    }

                    return Promise.resolve(data)
                }
                commit('setIR', [])

                return Promise.reject([])
            });
        },
        callGetEmployees({commit, dispatch, state}) {
            return axios.get(`/api/employee`, {
                params: {}
            }).then(({status, data: {data, success}}) => {
                if (status === 200 && success) {
                    commit('setEmployees', data.data)
                    return Promise.resolve(data)
                }
                commit('setEmployees', [])

                return Promise.reject([])
            });
        },
        callGetIRActions({commit, dispatch, state}) {
            return axios.get(`/api/ir/${state.IRType}/actions`, {
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
        callGetIROptions({commit, dispatch, state}) {
            return axios.get(`/api/ir/ab/${state.IRType}/options`)
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
            return axios.delete(`/api/ir/ab/${id}`, {
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
            let url = `/api/ir/ab/${state.IRType}`
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
                        dispatch('callSetIsEditable', false)
                        dispatch('callSetSelectedIR', data)

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
            } else {
                if (state.selectedIR.id) {
                    state.selectedIRAction.action_board_id = state.selectedIR.id
                }
            }

            return axios({method, url, data: state.selectedIRAction})
                .then(({status, data: {data, success}}) => {
                    if (status === 200 && success) {
                        dispatch('callSetManageActionDlg', false)
                        dispatch('callGetIRActions')
                        dispatch('callGetIR')

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
                    valid_till: '',
                    updated_by: {
                        name: '',
                        surname: ''
                    },
                    categories: [],
                    clients: [],
                    actions: [],
                    child_versions: [],
                    priority_id: null,
                    impact_potential_id: null,
                    stage_monitoring_id: null,
                    access_id: null,
                    description: '',
                    stage_monitoring: null,
                    status: {
                        name: ''
                    }
                }
            }
            commit('setSelectedIR', data)
        },
        callSetIsEditable({commit}, data) {
            commit('setIsEditable', data)
        },
        callSetManageActionDlg({commit}, data) {
            commit('setManageActionDlg', data)
        },
        callSetIRType({commit, dispatch}, data) {
            commit('setIRType', data)
            dispatch('callGetIR')

        }
    },
    mutations: {
        setIR: (state, data) => state.IR = data,
        setIRType: (state, data) => state.IRType = data,
        setEmployees: (state, data) => state.employees = data,
        setIRActions: (state, data) => state.IRActions = data,
        setIROptions: (state, data) => state.options = data,
        setIsEditable: (state, data) => state.isEditable = data,
        setManageActionDlg: (state, data) => state.manageActionDlg = data,
        setSelectedIR: (state, data) => {
            state.selectedIR = data
        },
        setRelatedIR: (state, data) => {
            state.relatedIR = data
        },
        setSearch: (state, data) => state.search = data,
        setSearchWhere: (state, data) => state.searchWhere = data,
        setActiveTags: (state, data) => state.activeTags = data,
    },
    getters: {
        getIR: state => state.IR,
        getIRType: state => state.IRType,
        getEmployees: state => state.employees,
        getIRActions: state => state.IRActions,
        getIROptions: state => state.options,
        getIsEditable: state => state.isEditable,
        getManageActionDlg: state => state.manageActionDlg,
        getSelectedIR: state => state.selectedIR,
        getRelatedIR: state => state.relatedIR,
        getSelectedIRAction: state => state.selectedIRAction,
        getSearch: state => state.search,
        getSearchWhere: state => state.searchWhere,
        getActiveTags: state => state.activeTags,
    }
}

