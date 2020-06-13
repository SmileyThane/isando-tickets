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
            <template v-slot:expanded-item="{ headers, item }">
                <td :colspan="headers.length">
                    <p></p>
                    <p><strong>From:</strong> {{ item.from.name }}</p>
                    <p><strong>To:</strong> {{ item.to.name }}</p>
                    <p><strong>Description:</strong> {{ item.description }}</p>
                    <p><strong>Contact name:</strong> {{ item.contact.user_data.name }}</p>
                    <p><strong>Contact email:</strong> {{ item.contact.user_data.email }}</p>
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
                    {text: 'Product', value: 'product.name'},
                    {text: 'Title', value: 'name'},
                    {text: 'Last update', value: 'last_update'},
                    {text: 'Actions', value: ''},
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
            }
        }
    }
</script>
