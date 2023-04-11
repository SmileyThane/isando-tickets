export default {
    namespaced: true,
    state: {
        activityTypes: [],
        search: null
    },
    actions: {
        getActivityTypes({commit}) {
            return axios.get(`/api/activities/types`, {retry: 5, retryDelay: 1000})
                .then(({data: {success, data}}) => {
                    if (success) {
                        commit('GET_ACTIVITY_TYPES', data)
                    }
                    return success;
                })
        },
        createActivityType({commit, dispatch, state}, {name}) {
            console.log(name)
            return axios.post(`/api/activities/types`, {name: name}, {retry: 5, retryDelay: 1000})
                .then(({data: {success}}) => {
                    if (success) {
                        dispatch('getActivityTypes');
                    }
                    return success;
                })
        },
        updateActivityType({commit, dispatch, state}, {id, name}) {
            return axios.patch(`/api/activities/types/${id}`, {name: name}, {retry: 5, retryDelay: 1000})
                .then(({data: {success}}) => {
                    if (success) {
                        dispatch('getActivityTypes');
                    }
                    return success;
                })
        },
        deleteActivityType({commit, dispatch, state}, id) {
            return axios.delete(`/api/activities/types/${id}`, {retry: 5, retryDelay: 1000})
                .then(({data: {success}}) => {
                    if (success) {
                        dispatch('getActivityTypes');
                    }
                    return success;
                })
        }
    },
    mutations: {
        GET_ACTIVITY_TYPES(state, activityTypes) {
            state.activityTypes = activityTypes
        }
    },
    getters: {
        getActivityTypes(state) {
            return state.activityTypes;
        }
    }
}

