export default {
    namespaced: true,
    state: {
        settings: {},
        reports: [],
    },
    actions: {
        getSettings({ commit }) {
            return axios.get('/api/tracking/settings')
                .then(({ data: { data, success }}) => {
                    if (success) {
                        commit('SET_SETTINGS', data);
                    }
                    return success;
                });
        },
        updateSettings({ commit, dispatch }, { currency }) {
            return axios.patch('/api/tracking/settings', {currency})
                .then(({ data: { data, success }}) => {
                    if (success) {
                        dispatch('getSettings');
                    }
                });
        },
        updateTrack({commit}, { id, date_from, date_to, billable, tags, entity, entity_id, entity_type, service, status }) {
            return axios.patch(`/api/tracking/tracker/${id}`, {date_from, date_to, billable, tags, entity, service, entity_id, entity_type, status})
                .then(({ data: { data, success } }) => {
                    return success;
                });
        },
        deleteTrack({commit}, {id}) {
            return axios.delete(`/api/tracking/tracker/${id}`)
                .then(({ data: { data, success } }) => {
                    return success;
                })
        },
        createReport({commit, dispatch}, {name, configuration}) {
            return axios.post('/api/tracking/reports', {name, configuration})
                .then(({ data: { data, success } }) => {
                    dispatch('getReports');
                });
        },
        deleteReport({commit, dispatch}, {id}) {
            return axios.delete(`/api/tracking/reports/${id}`)
                .then(({ data: { data, success } }) => {
                    if (success) {
                        dispatch('getReports');
                        return null;
                    }
                });
        },
        getReports({commit}) {
            return axios.get(`/api/tracking/reports`)
                .then(({ data: { data, success } }) => {
                    if (success) {
                        commit('SET_REPORTS', data);
                        return data;
                    }
                });
        },
        getReport({commit, dispatch}, {id}) {
            return axios.get(`/api/tracking/reports/${id}`)
                .then(({ data: { data, success } }) => {
                    if (success) {
                        return data;
                    }
                });
        }
    },
    mutations: {
        SET_SETTINGS(state, settings) {
            state.settings = settings
        },
        SET_REPORTS(state, reports) {
            state.reports = reports;
        },
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

