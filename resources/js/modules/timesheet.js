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
        getTimesheet({commit}, {start, end}) {
            const queryParams = new URLSearchParams({
                start, end
            });
            commit('SET_TIMESHEET', []);
            return axios.get(`/api/ttmanaging/timesheet?${queryParams.toString()}`, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        commit('SET_TIMESHEET', data);
                        commit('SET_PARAMS', {start, end});
                    }
                    return success;
                });
        },
        createTimesheet({dispatch, state}, {entity_id, entity_type, service, mon, tue, wed, thu, fri, sat, sun}) {
            return axios.post('/api/ttmanaging/timesheet', {
                entity_id,
                entity_type,
                service,
                mon,
                tue,
                wed,
                thu,
                fri,
                sat,
                sun
            }, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        dispatch('getTimesheet', state.params);
                    }
                    return success;
                });
        },
        removeTimesheet({dispatch, state}, id) {
            return axios.delete(`/api/ttmanaging/timesheet/${id}`, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        const index = state.timesheet.findIndex(i => i.id === id);
                        state.timesheet.splice(index, 1);
                        // dispatch('getTimesheet', state.params);
                    }
                    return success;
                })
        },
        updateTimesheet({state, commit, dispatch}, {
            id,
            timesheet: {entity_id, entity_type, billable, status, times, service}
        }) {
            return axios.patch(`/api/ttmanaging/timesheet/${id}`, {
                id,
                entity_id,
                entity_type,
                billable,
                status,
                times,
                service: service ? service.id : null
            }, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        commit('UPDATE_ITEM', data);
                    }
                    return success;
                })
        },
        submitTimesheetByIds({commit}, {ids, status, approver_id, note}) {
            return axios.patch('/api/ttmanaging/timesheet/submit', {ids, status, approver_id, note}, {
                retry: 5,
                retryDelay: 1000
            })
                .then(({data: {success, data}}) => {
                    if (success) {
                        data.map(i => {
                            commit('UPDATE_ITEM', i);
                        });
                    }
                    return success;
                })
        },
        remindTimesheet({commit}, {ids}) {
            return axios.post('/api/ttmanaging/timesheet/remind', {ids})
                .then(({data: {success, data}}) => {
                    if (success) {
                        return data;
                    }
                    return success;
                })
        },
        getCountTimesheetForApproval({commit}) {
            axios.get('/api/ttmanaging/timesheet/approval')
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('SET_COUNT_FOR_APPROVAL', data.count);
                    }
                });
        },
        getAllGroupedByStatus({commit}, {userId}) {
            axios.get('/api/ttmanaging/timesheet/status')
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('SET_GROUPED_TIMESHEET', {data, userId});
                    }
                });
        },
        copyLastWeek({state, dispatch}, {from, to}) {
            const query = new URLSearchParams({
                from, to
            });
            axios.post('/api/ttmanaging/timesheet/copy_last_week?' + query.toString())
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getTimesheet', state.params);
                    }
                });
        },
        getTimesheetTemplates({commit}) {
            axios.get('/api/ttmanaging/timesheet/templates')
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('SET_TIMESHEET_TEMPLATES', data);
                    }
                });
        },
        saveAsTemplate({state, dispatch}, {items, data}) {
            axios.post('/api/ttmanaging/timesheet/templates', {items, data})
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getTimesheetTemplates');
                    }
                });
        },
        loadTemplate({state, dispatch}, {id, start, end}) {
            axios.post(`/api/ttmanaging/timesheet/templates/${id}`, {start, end})
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getTimesheet', state.params);
                    }
                });
        },
        removeTemplate({state, dispatch}, id) {
            axios.delete(`/api/ttmanaging/timesheet/templates/${id}`)
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getTimesheetTemplates', state.params);
                    }
                });
        },
        saveOrdering({state}) {
            return axios.patch(`/api/ttmanaging/timesheet/ordering`, {
                ids: state.timesheet.sort((a, b) => a.ordering - b.ordering).map(i => i.id),
            }, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    return success;
                });
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
        },
        SET_COUNT_FOR_APPROVAL(state, params) {
            state.countForApproval = params;
        },
        SET_GROUPED_TIMESHEET(state, {data, userId}) {
            state.timesheetArchived = data.filter(i => i.user_id === userId && i.status === 3);
            state.timesheetPending = data.filter(i => i.user_id === userId && i.status === 1);
            state.timesheetRejected = data.filter(i => i.user_id === userId && i.status === 2);
            state.timesheetRequest = data.filter(i => ((i.approver_id === null || i.approver_id === userId) && i.status !== 0));
        },
        SET_TIMESHEET_TEMPLATES(state, data) {
            state.timesheet_templates = data;
        },
        SET_STATUS(state, ids, status) {
            state.timesheet.filter(i => ids.includes(i)).map(i => i.status = status);
            state.timesheetPending.filter(i => ids.includes(i)).map(i => i.status = status);
            state.timesheetRejected.filter(i => ids.includes(i)).map(i => i.status = status);
            state.timesheetArchived.filter(i => ids.includes(i)).map(i => i.status = status);
            state.timesheetRequest.filter(i => ids.includes(i)).map(i => i.status = status);
        },
        SET_ORDERING(state, {oldIndex, newIndex}) {
            try {
                state.timesheet[oldIndex - 1].ordering = newIndex;
                state.timesheet.splice(newIndex - 1, 0, state.timesheet.splice(oldIndex - 1, 1)[0]);
            } catch (e) {
                console.log('Ordering error');
            }
        }
    },
    getters: {
        getTimesheet(state, commit) {
            return state.timesheet
                .sort((a, b) => a.ordering - b.ordering)
                .map((item, index) => {
                    state.timesheet[index].ordering = index + 1;
                    return item;
                });
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
        getTimesheetTemplates(state) {
            return state.timesheet_templates;
        }
    }
}
