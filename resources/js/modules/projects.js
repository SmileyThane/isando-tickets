export default {
    namespaced: true,
    state: {
        projects: []
    },
    actions: {
        getProjectList({commit}, { search }) {
            const queryParams = new URLSearchParams({
                search: search ?? ''
            });
            axios.get(`/api/tracking/projects?${queryParams.toString()}`)
                .then(({ data: { success, data: { data: projects } } }) => {
                    if (success) {
                        commit('GET_PROJECTS', projects)
                    }
                })
        },
        createProject({commit, dispatch}, project) {
            axios.post('/api/tracking/projects', project)
                .then(({ data: { success, data: project } }) => {
                    if (success) {
                        dispatch('getProjectList');
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

