import login from "../views/auth/login.vue";

export default {
    namespaced: true,
    state: {},
    actions: {
        createRole({commit, dispatch, state}, data) {
            return axios.post(`/api/roles`, data)
                .then(({data: {success}}) => {
                    return success;
                })
                .catch(error => {
                    return new Promise.reject(error);
                })
        },
        cloneRole({commit, dispatch, state}, data) {
            return axios.post(`/api/roles/clone`, data)
                .then(({data: {success}}) => {
                    return success;
                })
                .catch(error => {
                    return new Promise.reject(error);
                })
        }
    },
}
