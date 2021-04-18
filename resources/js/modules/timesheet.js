export default {
    namespaced: true,
    state: {
        timesheet: [],
        params: {}
    },
    actions: {
        getTimesheet({ commit }, { start, end, project = 0, type = 0 }) {
            const queryParams = new URLSearchParams({
                start, end, project, type
            });
            commit('SET_TIMESHEET', []);
            return axios.get(`/api/tracking/timesheet?${queryParams.toString()}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success }}) => {
                    if (success) {
                        commit('SET_TIMESHEET', data);
                        commit('SET_PARAMS', { start, end, project, type });
                    }
                    return success;
                });
        },
        createTimesheet({ dispatch, state }, { project, mon, tue, wed, thu, fri, sat, sun }) {
            return axios.post('/api/tracking/timesheet', { project, mon, tue, wed, thu, fri, sat, sun }, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success }}) => {
                    if (success) {
                        dispatch('getTimesheet', state.params);
                        return success;
                    }
                    return success;
                });
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
