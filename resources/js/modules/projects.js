import _ from 'lodash';

export default {
    namespaced: true,
    state: {
        filter: null,
        projects: [],
        treeProjects: []
    },
    actions: {
        getProjectList({commit}, params) {
            commit('SET_FILTER', params);
            if (params && !params.search) params.search = '';
            const queryParams = new URLSearchParams(params);
            return axios.get(`/api/tracking/projects?${queryParams.toString()}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('GET_PROJECTS', data.data)
                        commit('GET_TREE_PROJECTS', data.data)
                        return data;
                    }
                })
        },
        createProject({commit, dispatch}, project) {
            return axios.post('/api/tracking/projects', project, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data: project } }) => {
                    if (success) {
                        dispatch('getProjectList');
                        return project;
                    }
                })
        },
        toggleFavorite({commit, dispatch, state}, project) {
            return axios.patch(`/api/tracking/projects/${project.id}/favorite`, null, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success } }) => {
                    if (success) {
                        dispatch('getProjectList', state.filter);
                    }
                });
        },
        toggleArchive({commit, dispatch, state}, project) {
            return axios.patch(`/api/tracking/projects/${project.id}/archive`, null, { retry: 5, retryDelay: 1000 })
                .then(({ data: { data, success } }) => {
                    if (success) {
                        dispatch('getProjectList', state.filter);
                    }
                });
        },
        removeArchive({commit, dispatch, state}, project) {
            return axios.delete(`/api/tracking/projects/${project.id}`)
                .then(({ data: { data, success } }) => {
                    if (success) {
                        dispatch('getProjectList', state.filter);
                    }
                });
        }
    },
    mutations: {
        GET_PROJECTS(state, projects) {
            state.projects = projects.sort((a,b) => b.is_favorite - a.is_favorite);
        },
        GET_TREE_PROJECTS(state, projects) {
            let clients = [];
            projects.forEach(i => {
                if (!clients.find(x => x.id === i.client.id)) {
                    clients.push({ ...i.client, projects: [] })
                }
            })
            clients = clients.map(client => {
                client.projects = projects.filter(i => i.client.id === client.id)
                    .sort((a,b) => (a.name ? a.name.toLowerCase() : a.name) - (b.name ? b.name.toLowerCase() : b.name))
                    .sort((a,b) => b.is_favorite - a.is_favorite);
                return client;
            })
            state.treeProjects = clients
        },
        SET_FILTER(state, param) {
            state.filter = param;
        }
    },
    getters: {
        getProjects(state) {
            return _.sortBy(_.sortBy(state.projects, ['name']), item => !item.is_favorite);
        },
        getTreeProjects(state) {
            return state.treeProjects
        }
    }
}

