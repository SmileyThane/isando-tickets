<template>
    <v-app id="inspire">
        <sidebar v-if="is_authorized === true" v-model="drawer"></sidebar>
        <app-header v-if="is_authorized === true" v-model="drawer"></app-header>
        <v-content>
            <router-view></router-view>
        </v-content>
        <appFooter></appFooter>
    </v-app>
</template>

<script>
    import Sidebar from '../components/Sidebar'
    import AppFooter from '../components/AppFooter'
    import Header from '../components/Header'

    export default {
        props: {
            source: String,
        },
        data: () => ({
            drawer: false,
            is_authorized: localStorage.getItem('auth_token') !== null
        }),
        components: {
            'sidebar': Sidebar,
            'appFooter': AppFooter,
            'appHeader': Header
        }
    }
</script>
