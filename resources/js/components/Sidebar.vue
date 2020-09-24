<template>
    <v-navigation-drawer
        v-model="drawer"
        :mini-variant="localDrawer"
        app
    >
        <v-list-item>
            <v-list-item-content>
                <v-list-item-title class="title">
                    {{ companyName }} | Ticketing
                </v-list-item-title>
                <v-list-item-subtitle>
                </v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>

        <v-divider></v-divider>
        <v-list dense>
            <v-list-item link to="/home">
                <v-list-item-action>
                    <v-icon>mdi-home</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.home}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-group
                prepend-icon="mdi-account"
                :value="sidebarGroups"
                active-class="green--text"
                color="green"
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
            <v-list-group
                prepend-icon="mdi-ticket-account"
                active-class="green--text"
                :value="sidebarGroups"
                color="green"
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
            <v-list-group
                prepend-icon="mdi-cog"
                active-class="green--text"
                :value="sidebarGroups"
                color="green"
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
                <v-list-item link to="/" v-if="checkRoleByIds([1,2])">
                    <v-list-item-action>
                        <v-icon>mdi-folder-cog-outline</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.sidebar.system_settings}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/" v-if="checkRoleByIds([1,2])">
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
    export default {
        name: "Sidebar",
        props: {value: {type: Boolean}},
        data: () => ({
            companyName: 'Isando',
            localDrawer: null,
            drawer: true,
            show: true,
            ticket: '',
            customers: '',
            settings: '',
            sidebarGroups: []

        }),
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
            this.getCompanyName()
            this.ticket = this.$store.state.lang.lang_map.sidebar.ticket
            this.customers = this.$store.state.lang.lang_map.sidebar.customers
            this.settings = this.$store.state.lang.lang_map.sidebar.settings
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
            changeAppTitle() {
                document.title = this.companyName + ' | ' + this.$store.state.lang.lang_map.sidebar[this.$route.name]
            }
        }
    }
</script>
