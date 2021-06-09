import _ from 'lodash';
import * as ColorHelper from '../helpers/color';

export default {
    namespaced: true,
    state: {
        coworkers: [],
        params: {},
        teams: [],
        managedTeams: [],
        teamManagers: [],
    },
    actions: {
        getTeams({ commit }, { search, sort_by, sort_val, per_page, page }) {
            let params = {};
            if (search) params = { ...params, search };
            if (sort_by) params = { ...params, sort_by };
            if (sort_val) params = { ...params, sort_val };
            if (per_page) params = { ...params, per_page };
            if (page) params = { ...params, page };
            const queryParams = new URLSearchParams(params);
            return axios.get('/api/team?' + queryParams.toString(), { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('SET_PARAMS', params);
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
        },
        getTeamManagers({ commit }) {
            return axios.get(`/api/tracking/team_managers`)
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('SET_TEAM_MANAGERS', data);
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
        },
        SET_TEAM_MANAGERS(state, params) {
            state.teamManagers = params;
        }
    },
    getters: {
        getCoworkers(state) {
            return _.sortBy(state.coworkers, item => {
                return item.full_name.toLowerCase();
            })
                .map(item => ({ ...item, color: ColorHelper.genRandomColor() }));
        },
        getTeams(state) {
            return state.teams
        },
        getManagedTeams(state) {
            return state.managedTeams
        },
        getTeamManagers(state) {
            return state.teamManagers;
        }
    }
}

