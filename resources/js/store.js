import Vue from 'vue'
import Vuex from 'vuex'
import EventBus from "./components/EventBus";
import {Clients, Products, Projects, Tags, Services, Tracking, Team, Tickets} from './modules';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        Tags: Tags,
        Products: Products,
        Projects: Projects,
        Clients: Clients,
        Services: Services,
        Tracking,
        Team,
        Tickets
    },
    state: {
        roles: {},
        permissions: {},
        lang: {},
        pageName: '',
        themeFgColor: '#FFFFFF',
        themeBgColor: '#6AA75D',
        appVersion: ''
    },
    getters: {
        roles: state => [state.roles, state.permissions, state.lang, state.pageName, state.themeFgColor, state.themeBgColor, state.appVersion]
    },
    mutations: {
        setRoles(state, roles) {
            state.roles = roles;
        },
        setPermissions(state, permissions) {
            state.permissions = permissions;
        },
        setLang(state, lang) {
            state.lang = lang;
        },
        setPageName(state, pageName) {
            state.pageName = pageName;
        },
        setThemeFgColor(state, color) {
            state.themeFgColor = color;
            EventBus.$emit('update-theme-fg-color', color);
        },
        setThemeBgColor(state, color) {
            state.themeBgColor = color;
            EventBus.$emit('update-theme-bg-color', color);
        },
        setAppVersion(state, version) {
            state.appVersion = version;
        }
    },
    actions: {
        getRoles({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/api/user/roles/id').then(result => {
                        commit('setRoles', result.data.data);
                        resolve();
                    }).catch(error => {
                        reject(error.response && error.response.data.message || 'Error.');
                    });
            });
        },
        getPermissions({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/api/user/permissions/id').then(result => {
                    commit('setPermissions', result.data.data);
                    resolve();
                }).catch(error => {
                    reject(error.response && error.response.data.message || 'Error.');
                });
            });
        },
        getLanguage({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/api/lang/map').then(result => {
                    if (result.data.success === true && result.data.data.lang_map !== null) {
                        commit('setLang', result.data.data);
                        resolve();
                    } else {
                        console.log('ERROR: broken_lang_object!');
                    }
                }).catch(error => {
                        reject(error.response && error.response.data.message || 'Error.');
                    });
            });
        },
        getThemeBgColor({commit}) {
            return new Promise((resolve, reject) => {
                let override = false;
                let fgColor = '#FFFFFF';
                let bgColor = '#6AA75D';

                let colorIsSet = true;
                if (localStorage.themeFgColor) {
                    commit('setThemeFgColor', localStorage.themeFgColor);
                } else {
                    colorIsSet = false;
                }
                if (localStorage.themeBgColor) {
                    commit('setThemeBgColor', localStorage.themeBgColor);
                } else {
                    if (localStorage.themeBgColor) {
                        commit('setThemeBgColor', localStorage.themeBgColor);
                    } else {
                        colorIsSet = false;
                    }
                }

                if (colorIsSet) {
                    return resolve();
                }

                axios.get('/api/main_company/settings').then(response => {
                    response = response.data;
                    if (response.success === true && response.data.theme_color) {
                        if (response.data.hasOwnProperty('theme_fg_color')) {
                            fgColor = response.data.theme_fg_color;
                        }
                        if (response.data.hasOwnProperty('theme_bg_color')) {
                            bgColor = response.data.theme_bg_color;
                        } else if (response.data.hasOwnProperty('theme_color')) {
                            bgColor = response.data.theme_color;
                        }

                        override = response.data.hasOwnProperty('override_user_theme') ? response.data.override_user_theme : false;

                        if (override) {
                            localStorage.themeFgColor = fgColor;
                            commit('setThemeFgColor', fgColor);

                            localStorage.themeBgColor = bgColor;
                            commit('setThemeBgColor', bgColor);

                            localStorage.themeColorDlg = 1;

                            return resolve();
                        }

                        axios.get('/api/user/settings').then(response => {
                            response = response.data;
                            if (response.success === true) {
                                if (response.data.hasOwnProperty('theme_fg_color')) {
                                    fgColor = response.data.theme_fg_color;
                                }
                                if (response.data.hasOwnProperty('theme_bg_color')) {
                                    bgColor = response.data.theme_bg_color;
                                } else if (response.data.hasOwnProperty('theme_color')) {
                                    bgColor = response.data.theme_color;
                                }
                            }

                            localStorage.themeFgColor = fgColor;
                            commit('setThemeFgColor', fgColor);

                            localStorage.themeBgColor = bgColor;
                            commit('setThemeBgColor', bgColor);

                            return resolve();
                        }).catch(error => {
                            return reject(error.response && error.response.data.message || 'Error.');
                        });
                    }
                }).catch(error => {
                    return reject(error.response && error.response.data.message || 'Error.');
                });
            });
        },
        getAppVersion({commit}) {
            return new Promise((resolve, reject) => {
                axios.get('/api/version').then(result => {
                        if (result.data.success === true)
                            localStorage.appVersion = result.data.data;
                        commit('setAppVersion', result.data.data);
                        resolve();
                    }).catch(error => {
                        reject(error.response && error.response.data.message || 'Error.');
                    });
            });
        },
    }
})
