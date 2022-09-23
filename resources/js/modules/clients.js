import _ from 'lodash';

export default {
    namespaced: true,
    state: {
        clients: []
    },
    actions: {
        getClientList({commit, state}, {
            search, force = false,
            coworkers = null, projects = null, services = null, tag = null, billable = null
        }) {
            if (state.clients.length && !search && !force) {
                console.log(state.clients.length, search, !search, search, !search);
                return state.clients;
            } else {
                const queryParams = new URLSearchParams({
                    search: search ?? '',
                    coworkers, projects, services, tag, billable
                });
                axios.get(`/api/tracking/clients?${queryParams.toString()}`, {retry: 5, retryDelay: 1000})
                    .then(({data: {success, data: {data: clients}}}) => {
                        if (success) {
                            commit('GET_CLIENTS', clients)
                        }
                    })
            }
        }
    },
    mutations: {
        GET_CLIENTS(state, clients) {
            state.clients = clients
        }
    },
    getters: {
        getClients(state) {
            return _.sortBy(state.clients, item => {
                return item.name.toLowerCase();
            });
        }
    }
}

