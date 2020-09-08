<template>
    <v-app-bar
        app
        color="green"
        dark
    >
        <v-app-bar-nav-icon @click.stop="localDrawer = !localDrawer"></v-app-bar-nav-icon>
        <v-toolbar-title>{{this.$store.state.lang.lang_map.sidebar[this.$route.name] }}</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-menu
            left
            bottom
        >
            <template v-slot:activator="{ on }">
                <v-btn icon v-on="on">
                    <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
            </template>

            <v-list>
                <v-list-item link to="/user">
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.profile}}</v-list-item-title>
                    <v-list-item-action>
                        <v-icon>mdi-account-settings</v-icon>
                    </v-list-item-action>
                </v-list-item>
                <v-list-item link @click="logout">
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.logout}}</v-list-item-title>
                    <v-list-item-action>
                        <v-icon>mdi-logout</v-icon>
                    </v-list-item-action>
                </v-list-item>
            </v-list>
        </v-menu>
        <v-chip
            class="ma-3"
            color="white"
            text-color="black"
        >
            <v-avatar left>
                <v-icon>mdi-account-circle</v-icon>
            </v-avatar>
            <v-label >{{username}}</v-label>

        </v-chip>
    </v-app-bar>
</template>

<script>
    export default {
        name: "Header",
        props: {value: {type: Boolean}},
        data: () => ({
            username: localStorage.getItem('name'),
            localDrawer: null
        }),
        watch: {
            value: function () {
                this.localDrawer = this.value

            },
            localDrawer: function () {
                this.$emit('input', this.localDrawer)
            }
        },
        mounted() {
            this.getUsername()
        },
        methods: {
            getUsername() {
                axios.get('/api/user').then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.username = response.data.name;
                    }
                });
            },
            logout(e) {
                e.preventDefault()
                localStorage.removeItem('auth_token')
                window.open('/login', '_self')
            }
        }

    }
</script>

<!--<style scoped>-->

<!--</style>-->
