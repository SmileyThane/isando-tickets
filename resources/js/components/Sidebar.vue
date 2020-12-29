<template>
    <v-navigation-drawer
        :mini-variant="localDrawer"
        app
        v-model="drawer"
    >
        <v-list-item v-if="this.navbarStyle === 2 && this.companyLogo">
            <v-list-item-icon style="margin-right: 16px!important;">
                <v-img :src="companyLogo" contain max-height="3em" max-width="30"></v-img>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title class="title">
                    {{ firstAlias }}
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item v-else-if="this.navbarStyle === 3 && this.companyLogo">
            <v-list-item-icon style="margin-right: 16px!important;">
                <v-img :src="companyLogo" contain max-height="3em" max-width="30"></v-img>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title class="title">
                    {{ firstAlias }}
                </v-list-item-title>
                <v-list-item-subtitle>
                    {{ secondAlias }}
                </v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>
        <v-list-item v-else-if="this.navbarStyle === 4 && this.companyLogo">
            <v-list-item-content>
                <v-list-item-icon>
                    <v-img :src="companyLogo" contain></v-img>
                </v-list-item-icon>
            </v-list-item-content>
        </v-list-item>
        <v-list-item v-else>
            <v-list-item-content>
                <v-list-item-title class="title">
                    {{ firstAlias }}
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-divider>&nbsp;</v-divider>
        <v-list dense>
            <v-list-item
                :style="'background-color:' + themeColor + ';'"
                @click.stop="localDrawer = !localDrawer"
                v-show="localDrawer"
            >
                <v-list-item-action>
                    <v-icon> mdi-menu</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title></v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item
                dense
                :style="'background-color:' + themeColor + ';'"
                link to="/home">
                <v-list-item-action>
                    <v-icon>mdi-home</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.home}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
        <v-divider></v-divider>
        <v-list dense>
            <v-list-group
                :style="'background-color: ' + themeColor + ';'"
                :value="sidebarGroups"
                color="white"
                multiple
                prepend-icon="mdi-badge-account-horizontal-outline"
                v-if="checkRoleByIds([1,2,3,4,5])"
            >
                <template
                    v-slot:activator
                >
                    <v-list-item-content>
                        <v-list-item-title>{{customers}} - CRM</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/all"
                >
                    <v-list-item-action>
                        <v-icon>mdi-contacts-outline</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.all}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/customer"
                    v-if="checkRoleByIds([1,2,3,4,5])"
                >
                    <v-list-item-action>
                        <v-icon>mdi-factory</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.customers}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/employee"
                    v-if="checkRoleByIds([1,2,3,4,5])"
                >
                    <v-list-item-action>
                        <v-icon>mdi-account</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.individuals}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/product"
                    v-if="checkRoleByIds([1,2,3,4,5])"
                >
                    <v-list-item-action>
                        <v-icon> mdi-monitor-clean</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.products}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-group>
        </v-list>
        <v-divider
            v-if="checkRoleByIds([1,2,3,4,5])"
        >&nbsp;</v-divider>
        <v-list dense>
            <v-list-group
                :style="'background-color: ' + themeColor + ';'"
                :value="sidebarGroups"
                color="white"
                multiple
                prepend-icon="mdi-ticket-account"
            >
                <template
                    v-slot:activator
                >
                    <v-list-item-content>
                        <v-list-item-title>{{ticket}}</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/tickets"
                >
                    <v-list-item-action>
                        <v-icon>mdi-format-list-numbered</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.ticket_list}}
                            {{this.$store.state.lang.lang_map.sidebar.list}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/ticket_create"
                >
                    <v-list-item-action>
                        <v-icon>mdi-shape-rectangle-plus</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.create_ticket}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-group>
        </v-list>
        <v-divider></v-divider>
        <v-list dense>
            <v-list-group
                :style="'background-color: ' + themeColor + ';'"
                :value="sidebarGroups"
                color="white"
                multiple
                prepend-icon="mdi-email-alert-outline"
            >
                <template
                    v-slot:activator
                >
                    <v-list-item-content>
                        <v-list-item-title>{{notifications}}</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/notify"
                >
                    <v-list-item-action>
                        <v-icon>mdi-email-send-outline</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.notify_customers}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/notify_history"
                >
                    <v-list-item-action>
                        <v-icon>mdi-history</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.notifications_history}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-group>
        </v-list>
        <v-divider></v-divider>
        <v-list dense>
            <v-list-item
                dense
                :style="'background-color:' + themeColor + ';'"
                link
                to="/">
                <v-list-item-action>
                    <v-icon>mdi-book-open-page-variant</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>
                        {{this.$store.state.lang.lang_map.sidebar.knowledge_base}}
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
        <v-divider>&nbsp;</v-divider>
        <v-list dense>
            <v-list-group
                :style="'background-color: ' + themeColor + ';'"
                :value="sidebarGroups"
                color="white"
                multiple
                prepend-icon="mdi-cog"

            >
                <template
                    v-slot:activator
                >
                    <v-list-item-content
                        :color="themeColor"
                    >
                        <v-list-item-title>{{settings}}</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/company">
                    <v-list-item-action>
                        <v-icon>mdi-office-building</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.companies}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/team"
                    v-if="checkRoleByIds([1,2,3,4,5])"
                >
                    <v-list-item-action>
                        <v-icon>mdi-account-box-multiple-outline</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.teams}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/settings/system"
                    v-if="checkRoleByIds([1,2,3])"
                >
                    <v-list-item-action>
                        <v-icon>mdi-folder-cog-outline</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.system_settings}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    :color="themeColor" link
                    style="background-color:white;"
                    to="/"
                    v-if="checkRoleByIds([1,2,3])"
                >
                    <v-list-item-action>
                        <v-icon>mdi-cogs</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.general_settings}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-group>
        </v-list>
    </v-navigation-drawer>
</template>

<script>
    import EventBus from './EventBus.vue';

    export default {
        name: "Sidebar",
        props: {value: {type: Boolean}},
        data() {
            return {
                firstAlias: '',
                secondAlias: '',
                companyLogo: '',
                navbarStyle: 1,
                localDrawer: null,
                drawer: true,
                show: true,
                ticket: '',
                customers: '',
                settings: '',
                notifications: '',
                sidebarGroups: [],
                themeColor: this.$store.state.themeColor
            }
        },
        watch: {
            value: function () {
                this.localDrawer = this.value
            },
            localDrawer: function () {
                this.$emit('input', this.localDrawer)
            },
            $route(to, from) {
                this.changeAppTitle();
            }
        },
        mounted() {
            this.getCompanyName();
            this.getCompanyLogo();
            this.getCompanySettings();

            this.ticket = this.$store.state.lang.lang_map.sidebar.ticket;
            this.customers = this.$store.state.lang.lang_map.sidebar.customers
            this.notifications = this.$store.state.lang.lang_map.sidebar.notifications
            this.settings = this.$store.state.lang.lang_map.sidebar.settings

            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            });
            EventBus.$on('update-navbar-style', function (style) {
                that.navbarStyle = style;
            });
            EventBus.$on('update-navbar-logo', function (logo) {
                that.companyLogo = logo;
            });
        },
        methods: {
            checkRoleByIds(ids) {
                let roleExists = false;
                ids.forEach(id => {
                    if (roleExists === false) {
                        roleExists = this.$store.state.roles.includes(id)
                    }
                });
                return roleExists
            },
            getCompanyName() {
                axios.get(`/api/main_company_name`)
                    .then(
                        response => {
                            this.firstAlias = response.data.data.first_alias
                            this.secondAlias = response.data.data.second_alias
                            this.changeAppTitle();
                        });
            },
            getCompanyLogo() {
                axios.get(`/api/main_company_logo`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.companyLogo = response.data;
                    }
                });
            },
            getCompanySettings() {
                axios.get(`/api/main_company_settings`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.navbarStyle = response.data.hasOwnProperty('navbar_style') ? response.data.navbar_style : 1;
                    }
                });
            },
            changeAppTitle() {
                this.$store.state.pageName = this.$store.state.lang.lang_map.sidebar[this.$route.name]
                document.title = this.firstAlias + ' | ' + this.$store.state.lang.lang_map.sidebar[this.$route.name]
            }
        }
    }
</script>
