export default {
    namespaced: true,
    state: {
        settings: {}
    },
    actions: {
        getSettings({ commit }) {
            return axios.get('/api/tracking/settings')
                .then(({ data: { data, success }}) => {
                    if (success) {
                        commit('SET_SETTINGS', data);
                    }
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
        updateTrack({commit}, { id, date_from, date_to, billable, tags }) {
            return axios.patch(`/api/tracking/tracker/${id}`, {date_from, date_to, billable, tags})
                .then(({ data: { data, success } }) => {
                    return success;
                });
        }
    },
    mutations: {
        SET_SETTINGS(state, settings) {
            state.settings = settings
        },
    },
    getters: {
        getSettings(state) {
            return state.settings;
        }
    }
}

