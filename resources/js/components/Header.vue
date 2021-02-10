<template>
    <v-app-bar
        :color="themeColor"
        app
        dark
    >
        <v-app-bar-nav-icon
            v-show="!localDrawer"
            @click.stop="localDrawer = !localDrawer"
        ></v-app-bar-nav-icon>

        <v-icon
            @click="back()"
        >
            mdi-arrow-left
        </v-icon>
        <v-spacer></v-spacer>
        <v-toolbar-title
        >{{ this.$store.state.pageName }}
        </v-toolbar-title>
        <v-spacer></v-spacer>
        <!--        <v-text-field-->
        <!--            class="text-center"-->
        <!--            dense-->
        <!--            hide-details="auto"-->
        <!--            :label="searchLabel"-->
        <!--        >-->
        <!--            <template slot="append">-->
        <!--                <v-menu-->
        <!--                    rounded-->
        <!--                    transition="slide-y-transition"-->
        <!--                    bottom-->
        <!--                >-->
        <!--                    <template v-slot:activator="{ on: menu, attrs }">-->
        <!--                        <v-btn-->
        <!--                            text-->
        <!--                            v-bind="attrs"-->
        <!--                            v-on="{...menu}"-->
        <!--                        >-->
        <!--                            <v-icon color="white">$expand</v-icon>-->
        <!--                        </v-btn>-->
        <!--                    </template>-->
        <!--                    <v-list-->
        <!--                        dense-->
        <!--                    >-->
        <!--                        <v-list-item-->
        <!--                            link-->
        <!--                            v-for="item in searchCategories"-->
        <!--                            :key="item.id"-->
        <!--                            @click="selectSearchCategory(item)"-->
        <!--                        >-->
        <!--                            <v-list-item-title>-->
        <!--                                {{ item.name }}-->
        <!--                            </v-list-item-title>-->
        <!--                        </v-list-item>-->
        <!--                    </v-list>-->
        <!--                </v-menu>-->
        <!--            </template>-->
        <!--        </v-text-field>-->
        <v-menu
            bottom
            left
        >
            <template v-slot:activator="{ on }">
                <v-btn v-on="on" icon>
                    <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
            </template>

            <v-list>
                <v-list-item link to="/user">
                    <v-list-item-title>{{ this.$store.state.lang.lang_map.sidebar.profile }}</v-list-item-title>
                    <v-list-item-action>
                        <v-icon>mdi-account-settings</v-icon>
                    </v-list-item-action>
                </v-list-item>
                <v-list-item link @click="logout">
                    <v-list-item-title>{{ this.$store.state.lang.lang_map.sidebar.logout }}</v-list-item-title>
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
                <v-img :src="avatar" />
            </v-avatar>
            <v-label class="d-sm-none d-md-flex">{{ username }}</v-label>

        </v-chip>
    </v-app-bar>
</template>

<script>
import EventBus from "./EventBus";

export default {
    name: "Header",
    props: {value: {type: Boolean}},
    data() {
        return {
            username: localStorage.getItem('name') + ' ' + localStorage.getItem('surname'),
            avatar: localStorage.getItem('avatar'),
            localDrawer: null,
            searchCategories: [
                {
                    id: 1,
                    name: 'ID'
                },
                {
                    id: 2,
                    name: 'Subject'
                },
                {
                    id: 3,
                    name: 'Contact'
                }
            ],
            searchLabel: this.$store.state.lang.lang_map.main.search,
            themeColor: this.$store.state.themeColor,
        }
    },
    watch: {
        value: function () {
            this.localDrawer = this.value
        },
        localDrawer: function () {
            this.$emit('input', this.localDrawer)
        }
    },
    mounted() {
        this.getUsername();
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
    },
    methods: {
        getUsername() {
            axios.get('/api/user').then(response => {
                response = response.data;
                if (response.success === true) {
                    this.username = response.data.name + ' ' + response.data.surname;
                    this.avatar = response.data.avatar_url;
                }
            });
        },
        back() {
            window.history.back();
        },
        logout(e) {
            e.preventDefault()
            localStorage.removeItem('auth_token')
            localStorage.removeItem('themeColor')
            window.open('/login', '_self')
        },
        selectSearchCategory(item) {
            this.searchLabel = item.name
        }
    }
}
</script>
