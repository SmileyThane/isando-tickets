import _ from 'lodash';

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
        getTeams({commit}, {search, sort_by, sort_val, per_page, page}) {
            let params = {};
            if (search) params = {...params, search};
            if (sort_by) params = {...params, sort_by};
            if (sort_val) params = {...params, sort_val};
            if (per_page) params = {...params, per_page};
            if (page) params = {...params, page};
            const queryParams = new URLSearchParams(params);
            return axios.get('/api/team?' + queryParams.toString(), {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('SET_PARAMS', params);
                        commit('SET_TEAMS', data);
                    }
                    return success;
                })
        },
        getCoworkers({commit, state}, {
            search, force = false,
            billable = null, clients = null, projects = null, services = null, tag = null
        }) {
            if (state.coworkers.length && !search && !force) {
                return state.coworkers;
            } else {
                const queryParams = new URLSearchParams({
                    search: search ?? '',
                    clients, projects, services, tag, billable
                });
                axios.get(`/api/tracking/coworkers?${queryParams.toString()}`, {retry: 5, retryDelay: 1000})
                    .then(({data: {success, data: coworkers}}) => {
                        if (success) {
                            commit('GET_COWORKERS', coworkers)
                        }
                    })
            }
        },
        getManagedTeams({commit, state}, {withEmployee = false}) {
            if (!withEmployee) withEmployee = false;
            if (state.managedTeams.length) {
                return state.managedTeams;
            } else {
                return axios.get(`/api/tracking/managed_teams?withEmployee=${withEmployee}`)
                    .then(({data: {success, data}}) => {
                        if (success) {
                            commit('SET_MANAGED_TEAMS', data);
                        }
                        return success;
                    });
            }
        },
        getTeamManagers({commit, state}) {
            if (state.teamManagers.length) {
                return state.teamManagers;
            } else {
                return axios.get(`/api/tracking/team_managers`)
                    .then(({data: {success, data}}) => {
                        if (success) {
                            commit('SET_TEAM_MANAGERS', data);
                        }
                        return success;
                    });
            }
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
            });
        },
        getTeams(state) {
            return state.teams
        },
        getManagedTeams(state) {
            return state.managedTeams
        },
        getEmployeesManagedTeams(state) {
            const empl = state.managedTeams.map(team => {
                team.employees.map(e => {
                    empl.push(e.employee.user_data);
                });
            });
            return _.sortBy(empl, item => {
                return item.full_name.toLowerCase();
            })
        },
        getTeamManagers(state) {
            return state.teamManagers;
        }
    }
}

