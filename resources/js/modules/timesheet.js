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
        createTimesheet({ dispatch, state }, { project, mon, tue, wed, thu, fri, sat, sun }) {
            return axios.post('/api/tracking/timesheet', { project, mon, tue, wed, thu, fri, sat, sun }, { retry: 5, retryDelay: 1000 })
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
        }
    },
    mutations: {
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
