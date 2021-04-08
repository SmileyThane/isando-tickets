export default {
    namespaced: true,
    state: {
        languages: []
    },
    actions: {
        getLanguageList({commit, state}) {
            if (state.languages.length) return state.languages;
            return axios.get(`/api/lang`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        commit('GET_LANGUAGES', data)
                        return data;
                    }
                })
        }
    },
    mutations: {
        GET_LANGUAGES(state, languages) {
            state.languages = languages
        }
    },
    getters: {
        getLanguages(state) {
            return state.languages
        },
        getLanguageById(state, id) {
            return state.languages.find(i => i.id === id);
        },
    }
}

