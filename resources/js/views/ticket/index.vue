<template>
    <v-container>
        <v-snackbar
            :bottom="true"
            :right="true"
            v-model="snackbar"
            :color="actionColor"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <v-data-table
            show-expand
            :headers="headers"
            :items="tickets"
            :single-expand="singleExpand"
            :expanded.sync="expanded"
            item-key="id"
            :options.sync="options"
            :server-items-length="totalTickets"
            :loading="loading"
            :footer-props="footerProps"
            class="elevation-4"
            hide-default-footer
            fixed-header
            :loading-text="langMap.main.loading"
            @click:row="showItem"
        >
            <template v-slot:top>

                <v-row>
                    <v-col sm="12" md="10">
                        <v-text-field @input="getTickets" v-model="ticketsSearch" color="green"
                                      :label="langMap.main.search" class="mx-4"></v-text-field>
                    </v-col>
                    <v-col sm="12" md="2">
                        <v-select
                            class="mx-4"
                            color="green"
                            item-color="green"
                            :items="footerProps.itemsPerPageOptions"
                            :label="langMap.main.items_per_page"
                            @change="updateItemsCount"
                        ></v-select>
                    </v-col>
                </v-row>
            </template>
            <template v-slot:footer>
                <v-pagination color="green"
                              v-model="options.page"
                              :length="lastPage"
                              circle
                              :page="options.page"
                              :total-visible="5"
                >
                </v-pagination>
            </template>
            <template v-slot:item.status.name="{ item }">
                <v-badge @click="showItem(item)" inline dot :color="item.status.color">{{ langMap.ticket_statuses[item.status.name] }}</v-badge>
            </template>
            <template v-slot:item.priority.name="{ item }">
                <v-badge @click="showItem(item)" inline dot :color="item.priority.color">{{ langMap.ticket_priorities[item.priority.name] }}
                </v-badge>
            </template>
            <template v-slot:item.assigned_person="{ item }">
                <div @click="showItem(item)" class="justify-center" v-if="item.assigned_person">{{
                    item.assigned_person.user_data.name }} {{
                    item.assigned_person.user_data.surname }}
                </div>
            </template>
            <template v-slot:item.actions="{ item }">


            </template>
            <template v-slot:expanded-item="{ headers, item }">

                <td :colspan="headers.length">
                    <v-spacer>
                        &nbsp;
                    </v-spacer>
                    <p><strong>{{langMap.ticket.contact_name}}:</strong> {{ item.contact ? item.contact.user_data.name : '' }}</p>
                    <p><strong>{{langMap.ticket.contact_email}}:</strong> {{ item.contact ? item.contact.user_data.email : '' }}</p>
                    <p><strong>{{langMap.ticket.due_date}}:</strong> {{ item.due_date }}</p>
                    <p><strong>{{langMap.ticket.access_details}}:</strong> {{ item.access_details }}</p>
                    <p>
                        <v-btn
                            color="grey"
                            dark
                            @click="showItem(item)"
                            fab
                            x-small
                        >
                            <v-icon
                            >
                                mdi-eye
                            </v-icon>
                        </v-btn>

                        <v-btn
                            color="error"
                            dark
                            @click="ticketDeleteProcess(item)"
                            fab
                            x-small
                        >
                            <v-icon
                            >
                                mdi-delete
                            </v-icon>
                        </v-btn>
                    </p>
                </td>

            </template>
        </v-data-table>
        <template>
            <v-dialog v-model="removeTicketDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.delete_selected}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeTicketDialog = false">{{langMap.main.cancel}}</v-btn>
                        <v-btn color="red darken-1" text @click="deleteTicket(selectedticketId)">{{langMap.main.delete}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
    </v-container>
</template>

<script>
    export default {
        data() {
            return {
                langMap: this.$store.state.lang.lang_map,
                clientId: 6,
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                expanded: [],
                singleExpand: false,
                totalTickets: 0,
                lastPage: 0,
                loading: 'green',
                options: {
                    page: 1,
                    sortDesc: [true],
                    sortBy: ['id']
                },
                footerProps: {
                    itemsPerPage: 10,
                    showFirstLastPage: true,
                    itemsPerPageOptions: [10, 25, 50, 100],
                },
                headers: [
                    {text: '', value: 'data-table-expand'},
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'id',
                    },
                    {text: `${this.$store.state.lang.lang_map.ticket.status}`, value: 'status.name'},
                    {text: `${this.$store.state.lang.lang_map.ticket.priority}`, value: 'priority.name'},
                    {text: `${this.$store.state.lang.lang_map.ticket.company_from}`, value: 'from.name'},
                    {text: `${this.$store.state.lang.lang_map.ticket.company_to + ' ' + this.$store.state.lang.lang_map.main.user}`, value: 'assigned_person'},
                    {text: `${this.$store.state.lang.lang_map.ticket.product_name}`, value: 'product.name'},
                    {text: `${this.$store.state.lang.lang_map.ticket.title}`, value: 'name'},
                    {text: `${this.$store.state.lang.lang_map.ticket.last_update}`, value: 'last_update'},
                ],
                ticketsSearch: '',
                tickets: [],
                removeTicketDialog: false,
                selectedticketId: null
            }
        },
        mounted() {
            this.getTickets()
        },
        methods: {
            getTickets() {
                this.loading = "green"
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id'
                    this.options.sortDesc[0] = true
                }
                if (this.totalTickets < this.options.itemsPerPage) {
                    this.options.page = 1
                }
                axios.get(`api/ticket?
                search=${this.ticketsSearch}&
                sort_by=${this.manageSortableField(this.options.sortBy[0])}&
                sort_val=${this.options.sortDesc[0]}&
                per_page=${this.options.itemsPerPage}&
                page=${this.options.page}`)
                    .then(
                        response => {
                            response = response.data
                            this.tickets = response.data.data
                            this.totalTickets = response.data.total
                            this.lastPage = response.data.last_page
                            this.loading = false
                        });
            },
            showItem(item) {
                this.$router.push(`/ticket/${item.id}`)
            },
            ticketDeleteProcess(item) {
                this.selectedticketId = item.id
                this.removeTicketDialog = true
            },
            deleteTicket(id) {
                axios.delete(`/api/ticket/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getTickets()
                        this.snackbarMessage = 'Ticket was deleted '
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.removeTicketDialog = false
                    } else {
                        this.snackbarMessage = 'Ticket delete error'
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
            checkRoleByIds(ids) {
                let roleExists = false;
                ids.forEach(id => {
                    if (roleExists === false) {
                        roleExists = this.$store.state.roles.includes(id)
                    }
                });
                return roleExists
            },
            updateItemsCount(value) {
                this.options.itemsPerPage = value
                this.options.page = 1
                // console.log(value)
            },
            manageSortableField(value) {
                if (value === 'last_update') return 'updated_at'
                if (value === 'status.name') return 'status_id'
                if (value === 'assigned_person') return 'to_company_user_id'
                if (value === 'product.name') return 'to_product_id'
                if (value === 'name') return 'name'
                if (value === 'from.name') return 'from_entity_id'
                if (value === 'to.name') return 'to_entity_id'
                if (value === 'priority.name') return 'priority_id'
                return value
            }
        },
        watch: {
            options: {
                handler() {
                    this.getTickets()
                },
                deep: true,
            },
        },
    }
</script>
