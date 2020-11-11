<template>
    <v-app id="inspire">
        <v-overlay :value="!isLoaded">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <sidebar v-if="isLoaded && isAuthorized === true" v-model="drawer"></sidebar>
        <app-header v-if="isLoaded && isAuthorized === true" v-model="drawer"></app-header>
        <v-main v-if="isLoaded" :style="isAuthorized === false ? 'background-image: url(/login_bg.jpg); background-size: cover;' : ''">
            <speed-panel v-if="isLoaded && isAuthorized === true" v-model="drawer"></speed-panel>

            <router-view></router-view>
        </v-main>
        <appFooter v-if="isAuthorized === true"></appFooter>
    </v-app>
</template>

<script>
    import Sidebar from '../components/Sidebar'
    import AppFooter from '../components/AppFooter'
    import Header from '../components/Header'
    import SpeedPanel from '../components/SpeedPanel';

    export default {
        props: {
            source: String,
        },
        data: () => ({
            drawer: false,
            isAuthorized: localStorage.getItem('auth_token') !== null,
            isLoaded: false
        }),
        components: {
            'sidebar': Sidebar,
            'appFooter': AppFooter,
            'appHeader': Header,
            'speedPanel': SpeedPanel
        },
        watch: {
            checkPreloaded(value) {
                this.isLoaded = true;
                // console.log(`val ${value}`);
            }
        },
        computed: {
            checkPreloaded: function () {
                return this.$store.state.roles && this.$store.state.lang
            }
        }
    }
</script>
