export default {
    namespaced: true,
    state: {
        projects: []
    },
    actions: {
        getProjectList({commit}, params) {
            if (params && !params.search) params.search = '';
            const queryParams = new URLSearchParams(params);
            return axios.get(`/api/tracking/projects?${queryParams.toString()}`)
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('GET_PROJECTS', data.data)
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
        }
    },
    getters: {
        getProjects(state) {
            return state.projects
        }
    }
}

