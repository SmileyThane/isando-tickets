<template>
    <v-app id="inspire">
        <sidebar v-model="drawer"></sidebar>
        <app-header v-model="drawer"></app-header>

        <v-content>
<!--            <v-container-->
<!--                class="fill-height"-->
<!--                fluid-->
<!--            >-->
                <router-view></router-view>
<!--            </v-container>-->
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
        }),
        components: {
            'sidebar': Sidebar,
            'appFooter': AppFooter,
            'appHeader': Header
        }
    }
</script>
