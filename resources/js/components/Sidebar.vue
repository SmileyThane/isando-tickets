<template>
    <v-navigation-drawer
        v-model="localDrawer"
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
                    <v-list-item-title>Home</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/company">
                <v-list-item-action>
                    <v-icon>mdi-office-building</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.main.company}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/customer" v-if="checkRoleByIds([1,2,3])">
                <v-list-item-action>
                    <v-icon>mdi-account-network</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.main.customer}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/product">
                <v-list-item-action>
                    <v-icon> mdi-monitor-clean</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.main.product}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/team" v-if="checkRoleByIds([1,2,3])">
                <v-list-item-action>
                    <v-icon>mdi-account-box-multiple-outline</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.main.team}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/employee" v-if="checkRoleByIds([1,2,3])">
                <v-list-item-action>
                    <v-icon>mdi-card-account-details-outline</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>{{this.$store.state.lang.lang_map.main.individuals}}</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-group
                prepend-icon="mdi-ticket-account"
                value="true"
                active-class="green--text"
                color="green"
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
                        <v-list-item-title>{{this.$store.state.lang.lang_map.main.tickets}}
                            {{this.$store.state.lang.lang_map.main.list}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/ticket_create">
                    <v-list-item-action>
                        <v-icon>mdi-shape-rectangle-plus</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{this.$store.state.lang.lang_map.main.create}}
                            {{this.$store.state.lang.lang_map.main.ticket}}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list-group>
            <!--            <v-list-item link to="/user">-->
            <!--                <v-list-item-action>-->
            <!--                    <v-icon>mdi-account</v-icon>-->
            <!--                </v-list-item-action>-->
            <!--                <v-list-item-content>-->
            <!--                    <v-list-item-title>User</v-list-item-title>-->
            <!--                </v-list-item-content>-->
            <!--            </v-list-item>-->
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
            show: true,
            ticket: ''
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
            }
        },
        mounted() {
            this.getCompanyName()
            this.ticket = this.$store.state.lang.lang_map.main.ticket
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
                document.title = this.companyName + ' | ' + this.$store.state.lang.lang_map.main[this.$route.name]
            }
        }
    }
</script>
