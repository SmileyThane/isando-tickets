import './bootstrap';
import Vue from "vue";
import Vuetify from 'vuetify'
import VueRouter from 'vue-router'
import App from './views/App'
import Routes from './routes'
import store from './store'
import moment from 'moment'
import VCalendar from 'v-calendar'
import 'vuetify/dist/vuetify.min.css'
import '../css/custom.css'
import PerfectScrollbar from 'vue2-perfect-scrollbar'
import 'vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css'
import Tinymce from "./components/Tinymce";
import helpers from './helpers';
import Sortable from 'sortablejs';
import 'primeicons/primeicons.css';

Vue.component('Tinymce', Tinymce)
Vue.use(PerfectScrollbar)
Vue.use(Vuetify)
Vue.use(VueRouter)
Vue.use(VCalendar, {
    componentPrefix: 'vc',
    firstDayOfWeek: 2,  // Monday
})
Vue.directive('sortable-data-table', function (el, binding, vnode) {
    const options = {
        animation: 150,
        onUpdate: function (event) {
            // console.log(event.newDraggableIndex, event.oldDraggableIndex);
            vnode.child.$emit('sorted', event)
        },
        draggable: 'tr:not(.static)',
        filter: '.static',
        onMove: event => {
            return !event.related.classList.contains('.static');
        }
    }
    Sortable.create(el.getElementsByTagName('tbody')[0], options)
});
Vue.prototype.moment = moment
Vue.use({
    install: () => {
        Vue.helpers = helpers;
        Vue.prototype.$helpers = helpers;
    }
});

const router = new VueRouter({
    scrollBehavior() {
        return {x: 0, y: 0};
    },
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

const app = new Vue({
    el: '#app',
    components: {App},
    vuetify,
    router,
    store,
    created() {
        this.$store.state.roles =
            this.$store.state.permissions =
                this.$store.state.lang = null;
        store.dispatch('getLanguage');
        store.dispatch('getThemeBgColor');
        store.dispatch('getAppVersion');
        this.loadRolesPermissions();
        store.dispatch('getMainCompany');
    },
    methods: {
        async loadRolesPermissions() {
            await store.dispatch('getPermissions');
            await store.dispatch('KbTypes/getKbTypes');
            await store.dispatch('getRoles');
        }
    }
});
