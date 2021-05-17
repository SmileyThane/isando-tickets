import _ from 'lodash';

export default {
    namespaced: true,
    state: {
        tags: []
    },
    actions: {
        getTagList({commit}) {
            axios.get('/api/tags', { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data: tags }}) => {
                    if (success) {
                        commit('GET_TAGS', tags)
                    }
                })
        },
        createTag({commit, dispatch}, tag) {
            return axios.post('/api/tags', tag, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        dispatch('getTagList');
                        return data;
                    }
                    return false;
                })
        },
        updateTag({commit, dispatch}, tag) {
            return axios.patch(`/api/tags/${tag.id}`, tag, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success, data } }) => {
                    if (success) {
                        dispatch('getTagList');
                        return data;
                    }
                    return false;
                })
        },
        deleteTag({commit, dispatch}, tagId) {
            return axios.delete(`/api/tags/${tagId}`, { retry: 5, retryDelay: 1000 })
                .then(({ data: { success } }) => {
                   if (success) {
                       dispatch('getTagList');
                       return true;
                   }
                   return false;
                });
        },
        addOrUpdateTranslate({commit, dispatch}, { id, lang, name}) {
            if (!lang && !name) return ;
            return axios.patch(`/api/tags/${id}/translate`, {lang, name}, { retry: 5, retryDelay: 1000 })
                .then(({data: { data, success }}) => {
                    if (success) {
                        dispatch('getTagList');
                    }
                });
        }
    },
    mutations: {
        GET_TAGS(state, tags) {
            state.tags = tags
        }
    },
    getters: {
        getTags(state, rootGetters, rootState) {
            const tags = state.tags.map(item => {
                const foundTranslation = item.translates.find(i => i.lang === rootState.lang.locale);
                item.name = foundTranslation ? foundTranslation.name : item.name;
                return item;
            });
            return _.sortBy(tags, item => item.name.toLowerCase());
        }
    }
}

