export default {
    namespaced: true,
    state: {
        languages: []
    },
    actions: {
        getLanguageList({commit}) {
            return axios.get(`/api/lang`)
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

