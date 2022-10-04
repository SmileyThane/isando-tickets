import moment from 'moment-timezone';
import helpers from '../helpers';

export default {
    namespaced: true,
    state: {
        settings: {
            company: null,
            currency: null,
            email: null,
            settings: {
                enableTimesheet: false,
                timesheetWeek: [],
                customRounding: [],
                projectType: 0,
            }
        },
        reports: [],
        trackers: [],
    },
    actions: {
        getSettings({commit, state}) {
            if (state.settings.length) {
                return state.settings;
            } else {
                return axios.get('/api/ttmanaging/settings', {retry: 5, retryDelay: 1000})
                    .then(({data: {data, success}}) => {
                        if (success) {
                            if (
                                !data.settings?.timesheetWeek
                                || (data.settings?.timesheetWeek && !data.settings?.timesheetWeek.length)
                            ) {
                                if (!data.settings) {
                                    data.settings = {
                                        enableTimesheet: false,
                                        timesheetWeek: [],
                                        customRounding: [],
                                        projectType: 0,
                                    };
                                }
                                data.settings.timesheetWeek = [];
                                for (let i = 0; i < 7; i++) {
                                    data.settings.timesheetWeek.push({
                                        dayOfWeek: i,
                                        workTime: {
                                            start: moment().startOf('days').set({hours: 8}).format(),
                                            end: moment().startOf('days').set({hours: 18}).format(),
                                        },
                                        lunchTime: {
                                            start: moment().startOf('days').set({hours: 12}).format(),
                                            end: moment().startOf('days').set({hours: 13}).format(),
                                        }
                                    });
                                }
                            }
                            commit('SET_SETTINGS', data);
                        }
                        return success;
                    });
            }
        },
        updateSettings({commit, dispatch}, {currency, settings}) {
            return axios.patch('/api/ttmanaging/settings', {currency, settings}, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        dispatch('getSettings');
                    }
                });
        },
        saveSettings({commit, dispatch, state}) {
            return axios.patch('/api/ttmanaging/settings', state.settings, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        dispatch('getSettings');
                    }
                });
        },
        updateTrack({commit}, {
            id,
            date_from,
            date_to,
            billable,
            tags,
            entity,
            entity_id,
            entity_type,
            service,
            status
        }) {
            return axios.patch(
                `/api/ttmanaging/ttmanager/${id}`
                , {date_from, date_to, billable, tags, entity, service, entity_id, entity_type, status}
                , {retry: 5, retryDelay: 1000}
            )
                .then(({data: {data, success}}) => {
                    return success;
                });
        },
        deleteTrack({commit}, {id}) {
            return axios.delete(`/api/ttmanaging/ttmanager/${id}`, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    return success;
                })
        },
        createReport({commit, dispatch}, {name, configuration}) {
            return axios.post('/api/ttmanaging/reports', {name, configuration}, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    dispatch('getReports');
                });
        },
        deleteReport({commit, dispatch}, {id}) {
            return axios.delete(`/api/ttmanaging/reports/${id}`, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        dispatch('getReports');
                        return null;
                    }
                });
        },
        getReports({commit}) {
            return axios.get(`/api/ttmanaging/reports`, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        commit('SET_REPORTS', data);
                        return data;
                    }
                });
        },
        getReport({commit, dispatch}, {id}) {
            return axios.get(`/api/ttmanaging/reports/${id}`, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        return data;
                    }
                });
        },
        createCustomRounding({commit, dispatch, state}, data) {
            if (data.name && data.seconds && data.direction) {
                data.key = `custom_${data.seconds}_${data.direction}_${Date.now()}`
                commit('CREATE_CUSTOM_ROUNDING', data);
                dispatch('saveSettings');
            }
        },
        updateCustomRounding({commit, dispatch}, data) {
            if (data.name && data.seconds && data.direction) {
                commit('UPDATE_CUSTOM_ROUNDING', data);
                dispatch('saveSettings');
            }
        },
        deleteCustomRounding({commit, dispatch}, data) {
            commit('DELETE_CUSTOM_ROUNDING', data);
            dispatch('saveSettings');
        },
        getTrackers({commit}, {offset = 0, filters = {}}) {
            const query = new URLSearchParams();
            Object.keys(filters).map(field => {
                query.append(field, filters[field]);
            });
            query.append('offset', offset.toString());
            return axios.get(`/api/ttmanaging/ttmanager?${query.toString()}`, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (offset > 0) {
                        commit('ADD_TRACKERS', data);
                    } else {
                        commit('SET_TRACKERS', data);
                    }
                    return data;
                })
        },
        createTrack({commit}, data) {
            return axios.post('/api/ttmanaging/ttmanager', data)
                .then(({data: {data}, success}) => {
                    if (success) {
                        commit('ADD_TRACKERS', data);
                    }
                    return data;
                });
        },
        duplicateTrack({commit}, id) {
            return axios.post(`/api/ttmanaging/ttmanager/${id}/duplicate`)
                .then(({data}) => {
                    commit('ADD_TRACKERS', data.data);
                    return data;
                });
        }
    },
    mutations: {
        SET_TOGGLE_TIMESHEET(state, timesheet) {
            state.settings.settings.enableTimesheet = timesheet;
        },
        SET_PROJECT_TYPE(state, type) {
            state.settings.settings.projectType = type;
        },
        SET_TIMESHEET_WEEK(state, timesheet) {
            state.settings.settings.timesheetWeek = timesheet;
        },
        SET_SETTINGS(state, settings) {
            state.settings = settings
        },
        SET_REPORTS(state, reports) {
            state.reports = reports;
        },
        CREATE_CUSTOM_ROUNDING(state, rounding) {
            if (!state.settings.settings.customRounding) {
                state.settings.settings.customRounding = [];
            }
            state.settings.settings.customRounding.push(rounding);
        },
        UPDATE_CUSTOM_ROUNDING(state, rounding) {
            const index = state.settings.settings.customRounding.findIndex(i => i.key === rounding.key);
            rounding = {...rounding, key: `custom_${rounding.seconds}_${rounding.direction}_${Date.now()}`};
            state.settings.settings.customRounding[index] = rounding;
        },
        DELETE_CUSTOM_ROUNDING(state, rounding) {
            const index = state.settings.settings.customRounding.findIndex(i => i.key === rounding.key);
            state.settings.settings.customRounding.splice(index, 1);
        },
        SET_TRACKERS(state, trackers) {
            state.trackers = trackers;
        },
        ADD_TRACKERS(state, trackers) {
            state.trackers = state.trackers.concat(trackers);
        },
        CLEAR_TRACKERS(state) {
            state.trackers = [];
        },
        UPDATE_TIME(state) {
            state.trackers.filter(i => i.status === 0).forEach(tracker => {
                const index = state.trackers.indexOf(tracker);
                state.trackers[index].passed = helpers.time.getSecBetweenDates(tracker.date_from, moment(), true);
            });
        }
    },
    getters: {
        getSettings(state) {
            return state.settings;
        },
        getReports(state) {
            return state.reports;
        },
        getTrackers(state) {
            return state.trackers.sort((a, b) => (moment(a.date_from).isAfter(b.date_from)));
        }
    }
}

