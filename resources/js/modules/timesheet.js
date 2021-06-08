export default {
    namespaced: true,
    state: {
        timesheet: [],
        params: {}
    },
    actions: {
        getTimesheet({ commit }, { start, end }) {
            const queryParams = new URLSearchParams({
                start, end
            });
            commit('SET_TIMESHEET', []);
            return axios.get(`/api/tracking/timesheet?${queryParams.toString()}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success }}) => {
                    if (success) {
                        commit('SET_TIMESHEET', data);
                        commit('SET_PARAMS', { start, end });
                    }
                    return success;
                });
        },
        createTimesheet({ dispatch, state }, { project, service, mon, tue, wed, thu, fri, sat, sun }) {
            return axios.post('/api/tracking/timesheet', { project, service, mon, tue, wed, thu, fri, sat, sun }, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success }}) => {
                    if (success) {
                        dispatch('getTimesheet', state.params);
                    }
                    return success;
                });
        },
        removeTimesheet ({ dispatch, state }, id) {
            return axios.delete(`/api/tracking/timesheet/${id}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success } }) => {
                    if (success) {
                        dispatch('getTimesheet', state.params);
                    }
                    return success;
                })
        },
        updateTimesheet({ state, commit, dispatch }, { id, timesheet: { project_id, billable, status, times } }) {
            return axios.patch(`/api/tracking/timesheet/${id}`, { id, project_id, billable, status, times }, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success }}) => {
                    if (success) {
                        commit('UPDATE_ITEM', data);
                    }
                    return success;
                })
        },
        submitTimesheetByIds({ commit }, { ids, status, approver_id, note }) {
            return axios.patch('/api/tracking/timesheet/submit', { ids, status, approver_id, note }, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        data.map(i => {
                            commit('UPDATE_ITEM', i);
                        });
                    }
                    return success;
                })
        },
        remindTimesheet ({ commit }, { ids }) {
            return axios.post('/api/tracking/timesheet/remind', { ids })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        return data;
                    }
                    return success;
                })
        }
    },
    mutations: {
        UPDATE_ITEM(state, item) {
            const foundIndexOfTimesheet = state.timesheet.findIndex(t => t.id === item.id);
            state.timesheet.splice(foundIndexOfTimesheet, 1, item);
        },
        SET_TIMESHEET(state, timesheet) {
            state.timesheet = timesheet
        },
        SET_PARAMS(state, params) {
            state.params = params;
        }
    },
    getters: {
        getTimesheet(state) {
            return state.timesheet;
        }
    }
}
