<template>
    <v-container fluid>
        <v-snackbar
            v-model="snackbar"
            :bottom="true"
            :color="actionColor"
            :right="true"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="unassignedUsers"
                            :loading="loading"
                            :loading-text="langMap.main.loading"
                            :options.sync="options"
                            :server-items-length="totalUnassignedUsers"
                            class="elevation-1"
                            hide-default-footer
                            @click:row="showAssignDialog"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col md="10" sm="12">
                                        <v-text-field v-model="customersSearch" :color="themeBgColor"
                                                      :label="langMap.main.search"
                                                      class="mx-4" @input="getClients"></v-text-field>
                                    </v-col>
                                    <v-col md="2" sm="12">
                                        <v-select
                                            v-model="options.itemsPerPage"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            class="mx-4"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:footer>
                                <v-pagination v-model="options.page"
                                              :color="themeBgColor"
                                              :length="lastPage"
                                              :page="options.page"
                                              :total-visible="5"
                                              circle
                                >
                                </v-pagination>
                            </template>
                            <template v-slot:item.lastActivationChange="{ item }">
                                {{
                                    item.lastActivationChange > 0 ?
                                        moment(item.lastActivationChange).format('DD-MM-YYYY') : ''
                                }}
                            </template>
                            <template v-slot:item.licensed="{ item }">
                                <v-icon>{{ item.licensed ? 'mdi-check-circle-outline' : 'mdi-cancel' }}</v-icon>
                            </template>
                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
        <v-dialog v-model="assignCompanyDialog" max-width="480" persistent>
            <v-card>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.main.assign }}
                </v-card-title>
                <v-card-text>
                    <v-select :color="themeBgColor" :item-color="themeBgColor"
                              v-model="unassignedUserForm.company_id" :items="customers"
                              item-value="id"
                              item-text="name"
                              dense :label="langMap.main.company">
                    </v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="assignCompanyDialog = false">
                        {{ langMap.main.cancel }}
                    </v-btn>
                    <v-btn color="red darken-1" text @click="assignToIxarmaCompany">
                        {{ langMap.main.assign }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>


<script>
import EventBus from "../../components/EventBus";

export default {

    data() {
        return {
            snackbar: false,
            actionColor: '',
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            totalCustomers: 0,
            lastPage: 0,
            loading: this.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
            expanded: [],
            singleExpand: false,
            options: {
                page: 1,
                sortDesc: [false],
                sortBy: ['id'],
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            headers: [
                {text: 'ID', value: 'id'},
                {text: `${this.$store.state.lang.lang_map.custom_license.username}`, value: 'username'},
                {text: `${this.$store.state.lang.lang_map.main.phone}`, value: 'phoneNumber', sortable: false},
                {text: `${this.$store.state.lang.lang_map.ticket.ip_address}`, value: 'serverIp'},
                {text: `${this.$store.state.lang.lang_map.custom_license.organisation}`, value: 'orgPath'},
                {text: `${this.$store.state.lang.lang_map.custom_license.platform}`, value: 'platform', sortable: false},
                {text: `${this.$store.state.lang.lang_map.custom_license.licensed}`, value: 'licensed', sortable: false},
                {text: `${this.$store.state.lang.lang_map.custom_license.active}`, value: 'active', sortable: false},
                {text: `${this.$store.state.lang.lang_map.custom_license.expired_at}`, value: 'trialExpirationAtString', sortable: false},
            ],
            customersSearch: '',
            customers: [],
            unassignedUsers: [],
            unassignedUserForm: {
                user_id: '',
                company_id: ''
            },
            totalUnassignedUsers: 0,
            removeCustomerDialog: false,
            assignCompanyDialog: false,
            clientForm: {
                client_name: '',
                client_description: '',
                supplier_object: '',
                supplier_type: '',
                supplier_id: ''
            },
            suppliers: []
        }
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.getClients();
    },
    methods: {
        getUnassignedUsers() {
            this.loading = this.themeBgColor
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id'
                this.options.sortDesc[0] = false
            }
            if (this.totalCustomers < this.options.itemsPerPage) {
                this.options.page = 1
            }
            axios.get('/api/custom_license_unassigned', {
                params: {
                    search: this.customersSearch,
                    sort_by: this.options.sortBy[0],
                    sort_val: this.options.sortDesc[0],
                    per_page: this.options.itemsPerPage,
                    page: this.options.page
                }
            }).then(response => {
                this.loading = false
                response = response.data
                if (response.success === true) {
                    this.unassignedUsers = response.data.entities
                    this.totalUnassignedUsers = response.data.total
                    this.lastPage = response.data.last_page
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        getClients() {
            this.loading = this.themeBgColor
            axios.get('/api/client', {
            }).then(response => {
                this.loading = false
                response = response.data
                if (response.success === true) {
                    this.customers = response.data.data
                    this.totalCustomers = response.data.total
                    this.lastPage = response.data.last_page
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value
            localStorage.itemsPerPage = value;
            this.options.page = 1
        },
        showAssignDialog(item) {
            this.unassignedUserForm.user_id = item.id
            this.assignCompanyDialog = true
        },
        assignToIxarmaCompany() {
            axios.post('/api/custom_license_unassigned/assign', this.unassignedUserForm, {
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
            // console.log(this.unassignedUserForm);
            this.assignCompanyDialog = false
        }
    },
    watch: {
        options: {
            handler() {
                this.getUnassignedUsers()
            },
            deep: true,
        },
    },
}
</script>
