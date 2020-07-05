import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        roles: {},
    },
    getters: {
        roles: state => state.roles,
    },
    mutations: {
        setRoles(state, roles) {
            state.roles = roles;
        },
    },
    actions: {
        getRoles({ commit }) {
            return new Promise((resolve, reject) => {
                axios.get('/api/user/roles/id')
                    .then(result => {
                        commit('setRoles', result.data.data);
                        resolve();
                    })
                    .catch(error => {
                        reject(error.response && error.response.data.message || 'Error.');
                    });
            });
        },
    }
})
