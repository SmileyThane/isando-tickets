<template>
    <v-navigation-drawer
        v-model="localDrawer"
        app
    >
        <v-list-item>
            <v-list-item-content>
                <v-list-item-title class="title">
                    Isando
                </v-list-item-title>
                <v-list-item-subtitle>
                    ticketing system
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
                    <v-list-item-title>Company</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/customer">
                <v-list-item-action>
                    <v-icon>mdi-account-network</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>Customer</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/product">
                <v-list-item-action>
                    <v-icon> mdi-monitor-clean</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>Product</v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item link to="/team">
                <v-list-item-action>
                    <v-icon>mdi-account-box-multiple-outline</v-icon>
                </v-list-item-action>
                <v-list-item-content>
                    <v-list-item-title>Team</v-list-item-title>
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
                        <v-list-item-title>Ticket</v-list-item-title>
                    </v-list-item-content>
                </template>
                <v-list-item link to="/tickets">
                    <v-list-item-action>
                        <v-icon>mdi-format-list-numbered</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Tickets List</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item link to="/ticket_create">
                    <v-list-item-action>
                        <v-icon>mdi-shape-rectangle-plus</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Create Ticket</v-list-item-title>
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
            localDrawer: null
        }),
        watch: {
            value: function () {
                this.localDrawer = this.value
            },
            localDrawer: function () {
                this.$emit('input', this.localDrawer)
            }
        }
    }
</script>
