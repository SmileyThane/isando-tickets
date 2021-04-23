import moment from 'moment-timezone';

export default {
    namespaced: true,
    state: {
        settings: {
            company: null,
            currency: null,
            email: null,
            settings: {
                enableTimesheet: false,
                timesheetWeek: []
            }
        },
        reports: [],
    },
    actions: {
        getSettings({ commit }) {
            return axios.get('/api/tracking/settings', { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success }}) => {
                    if (success) {
                        if (
                          !data.settings?.timesheetWeek
                          || (data.settings?.timesheetWeek && !data.settings?.timesheetWeek.length)
                        ) {
                            if (!data.settings) {
                                data.settings = {
                                    enableTimesheet: false,
                                    timesheetWeek: []
                                };
                            }
                            data.settings.timesheetWeek = [];
                            for (let i = 0; i < 7; i++) {
                                data.settings.timesheetWeek.push({
                                    dayOfWeek: i,
                                    workTime: {
                                        start: moment().startOf('days').set({ hours: 8 }).format(),
                                        end: moment().startOf('days').set({ hours: 18 }).format(),
                                    },
                                    lunchTime: {
                                        start: moment().startOf('days').set({ hours: 12 }).format(),
                                        end: moment().startOf('days').set({ hours: 13 }).format(),
                                    }
                                });
                            }
                        }
                        commit('SET_SETTINGS', data);
                    }
                    return success;
                });
        },
        updateSettings({ commit, dispatch }, { currency, settings }) {
            return axios.patch('/api/tracking/settings', {currency, settings}, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success }}) => {
                    if (success) {
                        dispatch('getSettings');
                    }
                });
        },
        saveSettings({ commit, dispatch, state }) {
            return axios.patch('/api/tracking/settings', state.settings, { retry: 5, retryDelay: 1000 })
              .then(({ data: { data, success }}) => {
                  if (success) {
                      dispatch('getSettings');
                  }
              });
        },
        updateTrack({commit}, { id, date_from, date_to, billable, tags, entity, entity_id, entity_type, service, status }) {
            return axios.patch(
                `/api/tracking/tracker/${id}`
                , {date_from, date_to, billable, tags, entity, service, entity_id, entity_type, status}
                , { retry: 5, retryDelay: 1000 }
                )
                .then(({ data: { data, success } }) => {
                    return success;
                });
        },
        deleteTrack({commit}, {id}) {
            return axios.delete(`/api/tracking/tracker/${id}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success } }) => {
                    return success;
                })
        },
        createReport({commit, dispatch}, {name, configuration}) {
            return axios.post('/api/tracking/reports', {name, configuration}, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success } }) => {
                    dispatch('getReports');
                });
        },
        deleteReport({commit, dispatch}, {id}) {
            return axios.delete(`/api/tracking/reports/${id}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success } }) => {
                    if (success) {
                        dispatch('getReports');
                        return null;
                    }
                });
        },
        getReports({commit}) {
            return axios.get(`/api/tracking/reports`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success } }) => {
                    if (success) {
                        commit('SET_REPORTS', data);
                        return data;
                    }
                });
        },
        getReport({commit, dispatch}, {id}) {
            return axios.get(`/api/tracking/reports/${id}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success } }) => {
                    if (success) {
                        return data;
                    }
                });
        }
    },
    mutations: {
        SET_TOGGLE_TIMESHEET (state, timesheet) {
            state.settings.settings.enableTimesheet = timesheet;
        },
        SET_TIMESHEET_WEEK (state, timesheet) {
            state.settings.settings.timesheetWeek = timesheet;
        },
        SET_SETTINGS(state, settings) {
            state.settings = settings
        },
        SET_REPORTS(state, reports) {
            state.reports = reports;
        }
    },
    getters: {
        getSettings(state) {
            return state.settings;
        },
        getReports(state) {
            return state.reports;
        }
    }
}

