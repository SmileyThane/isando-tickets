import _ from 'lodash';

export default {
    namespaced: true,
    state: {
        tags: []
    },
    actions: {
        getTagList({commit, dispatch, state}) {
            return axios.get('/api/tags')
                .then(({data: {success, data: tags}}) => {
                    if (success) {
                        commit('GET_TAGS', tags)
                        return Promise.resolve(tags)
                    }
                    commit('GET_TAGS', [])
                    return Promise.reject([])
                })
        },
        createTag({commit, dispatch}, tag) {
            return axios.post('/api/tags', tag, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        // dispatch('getTagList');
                        commit('ADD_TAG', data);
                        return data;
                    }
                    return false;
                })
        },
        updateTag({commit, dispatch}, tag) {
            return axios.patch(`/api/tags/${tag.id}`, tag, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        // dispatch('getTagList');
                        commit('UPDATE_TAG', data);
                        return data;
                    }
                    return false;
                })
        },
        deleteTag({commit, dispatch}, tagId) {
            return axios.delete(`/api/tags/${tagId}`, {retry: 5, retryDelay: 1000})
                .then(({data: {success}}) => {
                    if (success) {
                        // dispatch('getTagList');
                        commit('REMOVE_TAG', tagId);
                        return true;
                    }
                    return false;
                });
        },
        addOrUpdateTranslate({commit, dispatch}, {id, lang, name}) {
            if (!lang && !name) return;
            return axios.patch(`/api/tags/${id}/translate`, {lang, name}, {retry: 5, retryDelay: 1000})
                .then(({data: {data, success}}) => {
                    if (success) {
                        // dispatch('getTagList');
                        commit('UPDATE_TAG', data);
                    }
                });
        }
    },
    mutations: {
        GET_TAGS(state, tags) {
            state.tags = tags
        },
        ADD_TAG(state, tag) {
            state.tags.splice(0, 0, tag);
        },
        REMOVE_TAG(state, tagId) {
            const index = state.tags.findIndex(i => i.id === tagId);
            state.tags.splice(index, 1);
        },
        UPDATE_TAG(state, tag) {
            const index = state.tags.findIndex(i => i.id === tag.id);
            state.tags.splice(index, 1, tag);
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

