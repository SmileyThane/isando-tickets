<template>
    <v-container fluid>
        <v-snackbar
            :bottom="true"
            :right="true"
            v-model="snackbar"
            :color="actionColor"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-data-table
                            :headers="headers"
                            :items="customers"
                            :options.sync="options"
                            :server-items-length="totalCustomers"
                            :loading="loading"
                            :footer-props="footerProps"
                            class="elevation-1"
                            hide-default-footer
                            :loading-text="langMap.main.loading"
                            @click:row="showItem"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col sm="12" md="10">
                                        <v-text-field @input="getClients" v-model="customersSearch" :color="themeColor"
                                                      :label="langMap.main.search" class="mx-4"></v-text-field>
                                    </v-col>
                                    <v-col sm="12" md="2">
                                        <v-select
                                            class="mx-4"
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            v-model="options.itemsPerPage"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:footer>
                                <v-pagination :color="themeColor"
                                              v-model="options.page"
                                              :length="lastPage"
                                              circle
                                              :page="options.page"
                                              :total-visible="5"
                                >
                                </v-pagination>
                            </template>
                            <template v-slot:item.has_tracker="{ item }">
                                <v-icon @click="showItem(item)" class="justify-center" v-if="item">
                                    {{
                                    item.has_tracker === 1 ?
                                    'mdi-check-circle-outline' :
                                    'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:item.has_ticketing="{ item }">
                                <v-icon @click="showItem(item)" class="justify-center" v-if="item">
                                    {{
                                        item.has_ticketing === 1 ?
                                            'mdi-check-circle-outline' :
                                            'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:item.has_ixarma="{ item }">
                                <v-icon @click="showItem(item)" class="justify-center" v-if="item">
                                    {{
                                        item.has_ixarma === 1 ?
                                            'mdi-check-circle-outline' :
                                            'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:item.has_ixarma_app="{ item }">
                                <v-icon @click="showItem(item)" class="justify-center" v-if="item">
                                    {{
                                        item.has_ixarma_app === 1 ?
                                            'mdi-check-circle-outline' :
                                            'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:item.is_active="{ item }">
                                <v-icon @click="showItem(item)" class="justify-center" v-if="item">
                                    {{
                                        item.is_active === 1 ?
                                            'mdi-check-circle-outline' :
                                            'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
        <template>
            <v-dialog v-model="removeCustomerDialog" persistent max-width="480">
                <v-card>
                    <v-card-title>{{langMap.main.delete_selected}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeCustomerDialog = false">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteCustomer(selectedCustomerId)">
                            {{langMap.main.delete}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
    </v-container>
</template>


<script>
    import EventBus from "../../components/EventBus";

    export default {

        data() {
            return {
                snackbar: false,
                actionColor: '',
                themeColor: this.$store.state.themeColor,
                snackbarMessage: '',
                totalCustomers: 0,
                lastPage: 0,
                loading: this.themeColor,
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
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name'},
                    {text: 'iXarma app', value: 'has_ixarma'},
                    {text: 'iXarma app', value: 'has_ixarma_app'},
                    {text: 'Ticketing', value: 'has_ticketing'},
                    {text: 'Time traking', value: 'has_tracker'},
                    {text: `${this.$store.state.lang.lang_map.customer.active}`, value: 'is_active'},
                ],
                customersSearch: '',
                customers: [],
                removeCustomerDialog: false,
                selectedCustomerId: null,
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
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            });
        },
        methods: {
            getClients() {
                this.loading = this.themeColor
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id'
                    this.options.sortDesc[0] = false
                }
                if (this.totalCustomers < this.options.itemsPerPage) {
                    this.options.page = 1
                }
                axios.get('api/custom_license', {
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
            showItem(item) {
                this.$router.push(`/custom_license/${item.id}`)
            },
            updateItemsCount(value) {
                this.options.itemsPerPage = value
                localStorage.itemsPerPage = value;
                this.options.page = 1
            },
        },
        watch: {
            options: {
                handler() {
                    this.getClients()
                },
                deep: true,
            },
        },
    }
</script>
