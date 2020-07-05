<template>
    <v-app id="inspire">
        <v-overlay :value="!isLoaded">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <sidebar v-if="isLoaded && is_authorized === true" v-model="drawer"></sidebar>
        <app-header v-if="isLoaded && is_authorized === true" v-model="drawer"></app-header>
        <v-content v-if="isLoaded">
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
            is_authorized: localStorage.getItem('auth_token') !== null,
            isLoaded: false
        }),
        components: {
            'sidebar': Sidebar,
            'appFooter': AppFooter,
            'appHeader': Header
        },
        watch:{
            currentRoles(value) {
                this.isLoaded = true;
                // console.log(`val ${value}`);
            }
        },
        computed: {
            currentRoles: function() {
                return this.$store.state.roles
            },
        }
    }
</script>
