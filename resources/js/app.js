/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import Vue from "vue";
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'
import App from './views/App'
import Routes from './routes'
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify)
Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: Routes
});


router.beforeEach((to, from, next) => {

    if (to.matched.some(record => record.meta.requiresAuth)) {
        axios.get('api/user').then(response => {
            response = response.data
            response.success === false ?
                localStorage.setItem('auth_token', null) :
                localStorage.setItem('name', response.data.name)
        });
        if (localStorage.getItem('auth_token') === null) {
            next({path: '/login'})

        }
        next()
    } else {
        next()
    }
})

const vuetify = new Vuetify();
const app = new Vue({
    el: '#app',
    components: {App},
    vuetify,
    router
});
