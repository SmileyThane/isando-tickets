<template>
    <v-container>

        <v-data-table
            :headers="headers"
            :items="tickets"
            :single-expand="singleExpand"
            :expanded.sync="expanded"
            item-key="id"
            show-expand
            class="elevation-1"
        >
            <template v-slot:top>
                <v-toolbar flat>
                    <v-toolbar-title></v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-switch v-model="singleExpand" label="Single expand" color="green" class="mt-2"></v-switch>
                </v-toolbar>
            </template>
            <template v-slot:item.status.name="{ item }">
                <v-chip :color="item.status.color" dark>{{ item.status.name }}</v-chip>
            </template>
            <template v-slot:item.assigned_person="{ item }">
                <div class="justify-center" v-if="item.assigned_person">{{ item.assigned_person.user_data.name }} {{ item.assigned_person.user_data.surname }}</div>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon
                    small
                    class="mr-2"
                    @click="showItem(item)"
                >
                    mdi-eye
                </v-icon>
                <v-icon
                    small
                    @click="showItem(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
                <td :colspan="headers.length">
                    <p></p>
<!--                    <p><strong>From:</strong> {{ item.from.name }}</p>-->
<!--                    <p><strong>To:</strong> {{ item.to.name }}</p>-->
<!--                    <p><strong>Description:</strong> {{ item.description }}</p>-->
                    <p><strong>Contact name:</strong> {{ item.contact ? item.contact.user_data.name : '' }}</p>
                    <p><strong>Contact email:</strong> {{ item.contact ? item.contact.user_data.email : '' }}</p>
                    <p><strong>Due date:</strong> {{ item.due_date }}</p>
                    <p><strong>Access details:</strong> {{ item.access_details }}</p>
                </td>
            </template>
        </v-data-table>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                clientId: 6,
                expanded: [],
                singleExpand: false,
                headers: [
                    {text: '', value: 'data-table-expand'},
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'id',
                    },
                    {text: 'Status', value: 'status.name'},
                    {text: 'Priority', value: 'priority.name'},
                    {text: 'Company from', value: 'from.name'},
                    {text: 'Assigned to', value: 'assigned_person'},
                    {text: 'Product', value: 'product.name'},
                    {text: 'Title', value: 'name'},
                    {text: 'Last update', value: 'last_update'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                tickets: [],
            }
        },
        mounted() {
            this.getTickets()
        },
        methods: {
            getTickets() {
                axios.get('api/ticket').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.tickets = response.data.data
                    } else {
                        console.log('error')
                    }

                });
            },
            showItem(item) {
                this.$router.push(`/ticket/${item.id}`)
            },
            checkRoleByIds(ids) {
                let roleExists = false;
                ids.forEach(id => {
                    if (roleExists === false) {
                        roleExists = this.$store.state.roles.includes(id)
                    }
                });
                return roleExists
            }
        }
    }
</script>
