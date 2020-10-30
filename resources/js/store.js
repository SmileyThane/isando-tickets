import Vue from 'vue'
import Vuex from 'vuex'
import EventBus from "./components/EventBus";

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        roles: {},
        lang: {},
        pageName: '',
        themeColor: '#60695D'
    },
    getters: {
        roles: state => [state.roles, state.lang, state.pageName, state.themeColor]
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
        setThemeColor(state, themeColor) {
            state.themeColor = themeColor;
            EventBus.$emit('update-theme-color', themeColor);
        }
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
        getThemeColor({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/api/main_company_settings')
                    .then(result => {
                        if (result.data.success === true && result.data.data.theme_color !== null)
                            commit('setThemeColor', result.data.data.theme_color);
                        resolve();
                    })

                    .catch(error => {
                        reject(error.response && error.response.data.message || 'Error.');
                    });
            });
        }
    }
})
