import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        roles: {},
        lang: {},
        pageName: ''
    },
    getters: {
        roles: state => [state.roles, state.lang, state.pageName]
    },
    mutations: {
        setRoles(state, roles) {
            state.roles = roles;
        },
        setLang(state, lang) {
            state.lang = lang;
        },
        setPageName(state, pageName) {
            state.pageName = pageName;
        },
    },
    actions: {
        getRoles({commit}) {
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
        getLanguage({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/api/lang/map')
                    .then(result => {
                        if (result.data.success === true && result.data.data.lang_map !== null)
                            commit('setLang', result.data.data);
                        resolve();
                    })

                    .catch(error => {
                        reject(error.response && error.response.data.message || 'Error.');
                    });
            });
        },
    }
})
