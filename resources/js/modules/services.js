import _ from 'lodash';

export default {
    namespaced: true,
    state: {
        services: [],
        search: null
    },
    actions: {
        getServicesList({commit, state}, {
            search, force = false,
            billable = null, clients = null, projects = null, coworkers = null, tag = null
        }) {
            if (state.services.length && !search && !force) {
                if (search && search.length) {
                    return state.services.filter(i => i.name.includes(search))
                }
                return state.services;
            } else {
                commit('SET_SEARCH', search);
                const queryParams = new URLSearchParams({
                    search: search ?? '',
                    billable, clients, projects, coworkers, tag
                });
                axios.get(`/api/services?${queryParams.toString()}`, {retry: 5, retryDelay: 1000})
                    .then(({data: {success, data: services}}) => {
                        if (success) {
                            commit('GET_SERVICES', services)
                        }
                    })
            }
        },
        createService({commit, dispatch, state}, {name}) {
            return axios.post('/api/services', {name}, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getServicesList', {search: state.search})
                        return data
                    }
                })
        },
        updateService({commit, dispatch, state}, {id, name}) {
            return axios.patch(`/api/services/${id}`, {name}, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getServicesList', {search: state.search})
                        return data
                    }
                })
        },
        deleteService({commit, dispatch, state}, serviceId) {
            return axios.delete(`/api/services/${serviceId}`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        dispatch('getServicesList', {search: state.search})
                    }
                    return success
                })
        }
    },
    mutations: {
        GET_SERVICES(state, services) {
            state.services = services
        },
        SET_SEARCH(state, search) {
            state.search = search
        }
    },
    getters: {
        getServices(state) {
            return _.sortBy(state.services, item => item.name.toLowerCase());
        },
        getSearch(state) {
            return state.search
        }
    }
}

