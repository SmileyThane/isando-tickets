export default {
    namespaced: true,
    state: {
        timesheet: [],
        timesheetPending: [],
        timesheetRejected: [],
        timesheetArchived: [],
        timesheetRequest: [],
        params: {},
        countForApproval: 0,
        timesheet_templates: [],
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
        createTimesheet({ dispatch, state }, { entity_id, entity_type, service, mon, tue, wed, thu, fri, sat, sun }) {
            return axios.post('/api/tracking/timesheet', { entity_id, entity_type, service, mon, tue, wed, thu, fri, sat, sun }, { retry: 5, retryDelay: 1000 })
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
        updateTimesheet({ state, commit, dispatch }, { id, timesheet: { entity_id, entity_type, billable, status, times, service } }) {
            return axios.patch(`/api/tracking/timesheet/${id}`, { id, entity_id, entity_type, billable, status, times, service: service ? service.id : null }, { retry: 5, retryDelay: 1000 })
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
        },
        getCountTimesheetForApproval({ commit }) {
            axios.get('/api/tracking/timesheet/approval')
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('SET_COUNT_FOR_APPROVAL', data.count);
                    }
                });
        },
        getAllGroupedByStatus({ commit }, { userId }) {
            console.log(userId);
            axios.get('/api/tracking/timesheet/status')
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('SET_GROUPED_TIMESHEET', { data, userId });
                    }
                });
        },
        copyLastWeek({ state, dispatch }) {
            axios.post('/api/tracking/timesheet/copy_last_week')
                .then(({ data: { success, data } }) => {
                    if (success) {
                        dispatch('getTimesheet', state.params);
                    }
                });
        },
        getTimesheetTemplates({commit}) {
            axios.get('/api/tracking/timesheet/templates')
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('SET_TIMESHEET_TEMPLATES', data);
                    }
                });
        },
        saveAsTemplate({ state, dispatch }, { items, data }) {
            axios.post('/api/tracking/timesheet/templates', { items, data })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        dispatch('getTimesheetTemplates');
                    }
                });
        },
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
        },
        SET_COUNT_FOR_APPROVAL(state, params) {
            state.countForApproval = params;
        },
        SET_GROUPED_TIMESHEET(state, { data, userId }) {
            state.timesheetArchived = data.filter(i => i.user_id === userId && i.status === 'archived');
            state.timesheetPending = data.filter(i => i.user_id === userId && i.status === 'pending');
            state.timesheetRejected = data.filter(i => i.user_id === userId && i.status === 'rejected');
            state.timesheetRequest = data.filter(i => (i.approver_id === null || i.approver_id === userId) && i.status === 'pending');
        },
        SET_TIMESHEET_TEMPLATES(state, data) {
            state.timesheet_templates = data;
        },
    },
    getters: {
        getTimesheet(state) {
            return state.timesheet;
        },
        getCountTimesheetForApproval(state) {
            return state.countForApproval;
        },
        getPendingTimesheet(state) {
            return state.timesheetPending;
        },
        getRejectedTimesheet(state) {
            return state.timesheetRejected;
        },
        getRequestTimesheet(state) {
            return state.timesheetRequest;
        },
        getArchivedTimesheet(state) {
            return state.timesheetArchived;
        },
        getTimesheetTemplates (state) {
            return state.timesheet_templates;
        }
    }
}
