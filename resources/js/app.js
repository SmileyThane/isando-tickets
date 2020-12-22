import './bootstrap';
import Vue from "vue";
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'
import App from './views/App'
import Routes from './routes'
import store from './store'
import { TiptapVuetifyPlugin } from 'tiptap-vuetify'
import 'tiptap-vuetify/dist/main.css'
import 'vuetify/dist/vuetify.min.css'
import '../css/custom.css'

Vue.use(Vuetify)
Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: Routes
});


router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        axios.get('/api/auth/check').then(response => {
            if (response.data === null || response.data.success === false) {
                localStorage.setItem('auth_token', null)
            }
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
Vue.use(TiptapVuetifyPlugin, {
    vuetify,
    iconsGroup: 'mdi'
})
const app = new Vue({
    el: '#app',
    components: {App},
    vuetify,
    router,
    store,
    created() {
        this.$store.state.roles = this.$store.state.lang = null
        store.dispatch('getLanguage');
        store.dispatch('getRoles');
        store.dispatch('getThemeColor');
    }
});
