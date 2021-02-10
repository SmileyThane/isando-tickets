export default {
    namespaced: true,
    state: {
        projects: [],
        treeProjects: []
    },
    actions: {
        getProjectList({commit}, params) {
            if (params && !params.search) params.search = '';
            const queryParams = new URLSearchParams(params);
            return axios.get(`/api/tracking/projects?${queryParams.toString()}`)
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('GET_PROJECTS', data.data)
                        commit('GET_TREE_PROJECTS', data.data)
                        return data;
                    }
                })
        },
        createProject({commit, dispatch}, project) {
            return axios.post('/api/tracking/projects', project)
                .then(({ data: { success, data: project } }) => {
                    if (success) {
                        dispatch('getProjectList');
                        return project;
                    }
                })
        }
    },
    mutations: {
        GET_PROJECTS(state, projects) {
            state.projects = projects
        },
        GET_TREE_PROJECTS(state, projects) {
            let clients = [];
            projects.forEach(i => {
                if (!clients.find(x => x.id === i.client.id)) {
                    clients.push({ ...i.client, projects: [] })
                }
            })
            clients = clients.map(client => {
                client.projects = projects.filter(i => i.client.id === client.id);
                return client;
            })
            state.treeProjects = clients
        }
    },
    getters: {
        getProjects(state) {
            return state.projects
        },
        getTreeProjects(state) {
            return state.treeProjects
        }
    }
}

