export default {
    namespaced: true,
    state: {
        kbTypes: [],
    },
    actions: {
        getKbTypes({commit}) {
            return axios.get('/api/kb/types', {retry: 3, retryDelay: 1000})
                .then(({data: {data}}) => {
                    commit('setKbTypes', data);
                });
        },
        deleteKbType({commit, dispatch}, id) {
            return axios.delete(`/api/kb/${id}`)
                .then(({data: {success}}) => {
                    dispatch('getKbTypes');
                    return success;
                });
        },
        createKbType({commit, dispatch}, data) {
            return axios.post('/api/kb', data)
                .then(({data: {success}}) => {
                    dispatch('getKbTypes');
                    return success;
                })
        },
        updateKbType({commit, dispatch}, data) {
            return axios.put(`/api/kb/${data.id}`, data)
                .then(({data: {success}}) => {
                    dispatch('getKbTypes');
                    return success;
                });
        }
    },
    mutations: {
        setKbTypes: (state, kbTypes) => state.kbTypes = kbTypes,
    },
    getters: {
        getKbTypes: state => state.kbTypes,
    }
}

