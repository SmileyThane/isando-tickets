<template>
    <v-navigation-drawer
        v-model="drawer"
        :mini-variant="localDrawer"
        app
    >
        <v-list-item v-if="this.navbarStyle == 2 && this.companyLogo">
            <v-list-item-icon>
                <v-img contain max-height="3em" max-width="30" :src="companyLogo"></v-img>
            </v-list-item-icon>
            <v-list-item-content>
                <v-list-item-title class="title">
                    {{ companyName }} | {{ this.$store.state.lang.lang_map.main.ticketing }}
                </v-list-item-title>
                <v-list-item-subtitle>
                </v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>
        <v-list-item v-else-if="this.navbarStyle == 3 && this.companyLogo">
            <v-list-item-content>
                <v-list-item-icon>
                    <v-img contain :src="companyLogo"></v-img>
                </v-list-item-icon>
                <v-list-item-title class="title">
                    {{ companyName }} | {{ this.$store.state.lang.lang_map.main.ticketing }}
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-list-item v-else-if="this.navbarStyle == 4 && this.companyLogo">
            <v-list-item-content>
                <v-list-item-icon>
                    <v-img contain :src="companyLogo"></v-img>
                </v-list-item-icon>
            </v-list-item-content>
        </v-list-item>
        <v-list-item v-else>
            <v-list-item-content>
                <v-list-item-title class="title">
                    {{ companyName }} | {{ this.$store.state.lang.lang_map.main.ticketing }}
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
        <v-divider>&nbsp;</v-divider>
        <v-list dense>
            <v-list-item link to="/home">
                <v-list-item-action>
                    <v-icon>mdi-home</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.home}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <hr>
            <v-list-group
                prepend-icon="mdi-account"
                :value="sidebarGroups"
                :color="themeColor"
                multiple
            >
                <template
                    v-slot:activator
                >
                    <v-list-item-content>
                        <v-list-item-title>{{customers}} - CRM</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item link to="/customer" v-if="checkRoleByIds([1,2,3])">
                    <v-list-item-action>
                        <v-icon>mdi-factory</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.customers}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/employee" v-if="checkRoleByIds([1,2,3])">
                    <v-list-item-action>
                        <v-icon>mdi-badge-account-horizontal-outline</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.individuals}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/product">
                    <v-list-item-action>
                        <v-icon> mdi-monitor-clean</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.products}}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-group>
            <v-list-item link to="/team" v-if="checkRoleByIds([1,2,3])">
                <v-list-item-action>
                    <v-icon>mdi-account-box-multiple-outline</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.teams}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
        <hr>
        <v-list dense>
            <v-list-group
                prepend-icon="mdi-ticket-account"
                :value="sidebarGroups"
                :color="themeColor"
                multiple
            >
                <template
                    v-slot:activator
                >
                    <v-list-item-content>
                        <v-list-item-title>{{ticket}}</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item link to="/tickets">
                    <v-list-item-action>
                        <v-icon>mdi-format-list-numbered</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.ticket_list}}
                            {{this.$store.state.lang.lang_map.sidebar.list}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/ticket_create">
                    <v-list-item-action>
                        <v-icon>mdi-shape-rectangle-plus</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.create_ticket}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/">
                    <v-list-item-action>
                        <v-icon>mdi-email-send-outline</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.notify_customers}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-group>
            <v-list-item link to="/">
                <v-list-item-action>
                    <v-icon>mdi-book-open-page-variant</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.knowledge_base}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
        </v-list>
        <hr>
        <v-list dense>
        <v-list-group
            prepend-icon="mdi-cog"
            :value="sidebarGroups"
            :color="themeColor"
            multiple

        >
            <template
                v-slot:activator
            >
                <v-list-item-content>

                    <v-list-item-title>{{settings}}</v-list-item-title>
                </v-list-item-content>
            </template>
            <v-list-item link to="/company">
                <v-list-item-action>
                    <v-icon>mdi-office-building</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.companies}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/settings/system" v-if="checkRoleByIds([1,2,3])">
                <v-list-item-action>
                    <v-icon>mdi-folder-cog-outline</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.system_settings}}
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/" v-if="checkRoleByIds([1,2,3])">
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
                companyName: '',
                companyLogo: '',
                navbarStyle: 1,
                localDrawer: null,
                drawer: true,
                show: true,
                ticket: '',
                customers: '',
                settings: '',
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
            },
            ticket: function () {
                this.ticket = this.$store.state.lang.lang_map.sidebar.ticket
                this.customers = this.$store.state.lang.lang_map.sidebar.customers
                this.settings = this.$store.state.lang.lang_map.sidebar.settings
            }
        },
        mounted() {
            this.getCompanyName();
            this.getCompanyLogo();
            this.getCompanySettings();
            this.ticket = this.$store.state.lang.lang_map.sidebar.ticket;
            this.customers = this.$store.state.lang.lang_map.sidebar.customers;
            this.settings = this.$store.state.lang.lang_map.sidebar.settings;
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
                            this.companyName = response.data.data
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
                document.title = this.companyName + ' | ' + this.$store.state.lang.lang_map.sidebar[this.$route.name]
            }
        }
    }
</script>
