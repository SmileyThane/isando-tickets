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
                let override = false;
                let color = '';
                if (localStorage.themeColor) {
                    commit('setThemeColor', color);
                    resolve();
                }
                axios.get('/api/main_company_settings').then(response => {
                    response = response.data;
                    if (response.success === true && response.data.theme_color) {
                        color = response.data.hasOwnProperty('theme_color') ? response.data.theme_color : '#4caf50';
                        override = response.data.hasOwnProperty('override_user_theme') ? response.data.override_user_theme : false;

                        if (override) {
                            localStorage.themeColor = color;
                            commit('setThemeColor', color);
                            resolve();
                        } else {
                            axios.get('/api/user/settings').then(response => {
                                response = response.data;
                                if (response.success === true && response.data.hasOwnProperty('theme_color')) {
                                    localStorage.themeColor = response.data.theme_color;
                                    commit('setThemeColor', response.data.theme_color);
                                    resolve();
                                } else {
                                    localStorage.themeColor = color;
                                    commit('setThemeColor', color);
                                    resolve();
                                }
                            }).catch(error => {
                                reject(error.response && error.response.data.message || 'Error.');
                            });
                        }
                    }
                }).catch(error => {
                    reject(error.response && error.response.data.message || 'Error.');
                });
            });
        }
    }
})
