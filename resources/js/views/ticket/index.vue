<template>
    <v-container>
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
            loading-text="Give me a second..."
        >
            <template v-slot:top>

                <v-row>
                    <v-col sm="12" md="10">
                        <v-text-field @input="getTickets" v-model="ticketsSearch" color="green"
                                      label="Search..." class="mx-4"></v-text-field>
                    </v-col>
                     <v-col sm="12" md="2">
                            <v-select
                                class="mx-4"
                                color="green"
                                item-color="green"
                                :items="footerProps.itemsPerPageOptions"
                                label="Items per page"
                                @change="updateItemsCount"
                            ></v-select>
                    </v-col>
<!--                                        <v-col sm="12">-->
<!--                                            <v-switch v-model="singleExpand" label="Single expand" color="green" class="mt-2"></v-switch>-->
<!--                                        </v-col>-->

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
                <v-badge inline dot :color="item.status.color">{{ item.status.name }}</v-badge>
            </template>
            <template v-slot:item.priority.name="{ item }">
                <v-badge inline dot :color="item.priority.color">{{ item.priority.name }}</v-badge>
            </template>
            <template v-slot:item.assigned_person="{ item }">
                <div class="justify-center" v-if="item.assigned_person">{{ item.assigned_person.user_data.name }} {{
                    item.assigned_person.user_data.surname }}
                </div>
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
                totalTickets: 0,
                lastPage: 0,
                loading: 'green',
                options: {
                    page: 1,
                    sortDesc: [false],
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
                    {text: 'Status', value: 'status.name'},
                    {text: 'Priority', value: 'priority.name'},
                    {text: 'Company from', value: 'from.name'},
                    {text: 'Assigned to', value: 'assigned_person'},
                    {text: 'Product', value: 'product.name'},
                    {text: 'Title', value: 'name'},
                    {text: 'Last update', value: 'last_update'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                ticketsSearch: '',
                tickets: [],
            }
        },
        mounted() {
            this.getTickets()
        },
        methods: {
            getTickets() {
                this.loading = "green"
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'last_update'
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
