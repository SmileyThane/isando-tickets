export default {
    namespaced: true,
    state: {
        coworkers: [],
        params: {},
        teams: [],
        managedTeams: []
    },
    actions: {
        getTeams({ commit }, { search, sort_by, sort_val, per_page, page }) {
            const queryParams = new URLSearchParams({
                search, sort_by, sort_val, per_page, page
            });
            return axios.get('/api/team?' + queryParams.toString(), { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('SET_PARAMS', { search, sort_by, sort_val, per_page, page });
                        commit('SET_TEAMS', data);
                    }
                    return success;
                })
        },
        getCoworkers({commit}, { search }) {
            const queryParams = new URLSearchParams({
                search: search ?? ''
            });
            axios.get(`/api/tracking/coworkers?${queryParams.toString()}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data: coworkers } }) => {
                    if (success) {
                        commit('GET_COWORKERS', coworkers)
                    }
                })
        },
        getManagedTeams({ commit }, { withEmployee }) {
            if (!withEmployee) withEmployee = false;
            return axios.get(`/api/tracking/managed_teams?withEmployee=${withEmployee}`)
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('SET_MANAGED_TEAMS', data);
                    }
                    return success;
                });
        }
    },
    mutations: {
        GET_COWORKERS(state, coworkers) {
            state.coworkers = coworkers
        },
        SET_TEAMS(state, teams) {
            state.teams = teams
        },
        SET_MANAGED_TEAMS(state, managedTeams) {
            state.managedTeams = managedTeams
        },
        SET_PARAMS(state, params) {
            state.params = params
        }
    },
    getters: {
        getCoworkers(state) {
            return state.coworkers
        },
        getTeams(state) {
            return state.teams
        },
        getManagedTeams(state) {
            return state.managedTeams
        }
    }
}

