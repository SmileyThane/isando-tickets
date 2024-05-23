<template>
    <v-app id="inspire">

        <v-overlay :value="!isLoaded">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>

        <sidebar v-if="isLoaded && isAuthorized === true" v-model="drawer"></sidebar>
        <app-header v-if="isLoaded && isAuthorized === true" v-model="drawer"></app-header>


                <perfect-scrollbar :style="`height: ${mainBlockHeight}px; margin-top: 70px;`" ref="mainScrollbar" watchOptions>
                    <v-main v-if="isLoaded"
                            ref="mainBlock"
                            :style="isAuthorized === false ? 'background-image: url(/login_bg.jpg); background-size: cover; height: 100vh;' : 'padding-top:0px'">
                    <router-view></router-view>
                    <speed-panel v-if="isLoaded && isAuthorized === true" v-model="drawer"></speed-panel>
                    <appFooter v-if="isAuthorized === true"></appFooter>
                    </v-main>
                </perfect-scrollbar>



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
        isLoaded: false,
        mainBlockHeight: 0
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
        }
    },
    mounted() {
        this.setMainBlockHeight()
        window.onresize = () => {
            this.setMainBlockHeight()
        }
    },
    computed: {
        checkPreloaded: function () {
            return this.$store.state.roles && this.$store.state.lang
        }
    },
    methods: {
        setMainBlockHeight()
        {
            this.mainBlockHeight = window.innerHeight - 70
        }
    }
}
</script>
<style scoped>
>>> .v-main__wrap {
    max-width: initial;
}
</style>
