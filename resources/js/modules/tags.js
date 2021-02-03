export default {
    namespaced: true,
    state: {
        tags: []
    },
    actions: {
        getTagList({commit}) {
            axios.get('/api/tags')
                .then(({ data: { success, data: tags }}) => {
                    if (success) {
                        commit('GET_TAGS', tags)
                    }
                })
        },
        createTag({commit, dispatch}, tag) {
            return axios.post('/api/tags', tag)
                .then(({ data: { success, data: tag } }) => {
                    if (success) {
                        dispatch('getTagList');
                        return tag;
                    }
                })
        }
    },
    mutations: {
        GET_TAGS(state, tags) {
            state.tags = tags
        }
    },
    getters: {
        getTags(state) {
            return state.tags
        }
    }
}

