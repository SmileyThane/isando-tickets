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
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{langMap.customer.add_new}}
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.name"
                                                    name="company_name"
                                                    type="text"
                                                    v-model="clientForm.client_name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.description"
                                                    name="company_description"
                                                    type="text"
                                                    v-model="clientForm.client_description"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-select
                                                    :label="langMap.customer.supplier"
                                                    :color="themeColor"
                                                    :item-color="themeColor"
                                                    item-text="name"
                                                    item-value="item"
                                                    v-model="clientForm.supplier_object"
                                                    :items="suppliers"
                                                />
                                            </div>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                :color="themeColor"
                                                @click="addClient"
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-data-table
                            show-expand
                            :headers="headers"
                            :items="customers"
                            :single-expand="singleExpand"
                            :expanded.sync="expanded"
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
                            <template v-slot:item.logo="{ item }">
                                <v-avatar
                                    size="2em"
                                    color="grey darken-1"
                                    tile
                                    v-if="item.logo_url"
                                >
                                    <v-img :src="item.logo_url" />
                                </v-avatar>
                                <v-icon v-else large>mdi-account-circle</v-icon>
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
                            <template v-slot:item.is_active="{ item }">
                                <v-icon @click="showItem(item)" class="justify-center" v-if="item">
                                    {{
                                    item.is_active === 1 ?
                                    'mdi-check-circle-outline' :
                                    'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <p><strong>{{langMap.main.actions}}:</strong></p>
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
                                            @click="deleteProcess(item)"
                                            fab
                                            x-small
                                        >
                                            <v-icon>
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </p>
                                </td>
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
                    {text: '', value: 'data-table-expand'},
                    {
                        text: 'ID',
                        align: 'start',
                        value: 'id',
                    },
                    {text: `${this.$store.state.lang.lang_map.main.logo}`, value: 'logo', align: 'center', sortable: false},
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name'},
                    {text: `${this.$store.state.lang.lang_map.company.short_name}`, value: 'short_name'},
                    {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'description'},
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
            this.getSuppliers();
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            });
        },
        methods: {
            getClients() {
                this.loading = this.themeColor
                // console.log(this.options);
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id'
                    this.options.sortDesc[0] = false
                }
                if (this.totalCustomers < this.options.itemsPerPage) {
                    this.options.page = 1
                }
                axios.get('api/client', {
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
            addClient() {
                this.clientForm.supplier_type = Object.keys(this.clientForm.supplier_object.item)[0]
                this.clientForm.supplier_id = Object.values(this.clientForm.supplier_object.item)[0]
                axios.post('/api/client', this.clientForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClients()
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
                // console.log(this.clientForm);
            },
            getSuppliers() {
                axios.get('api/supplier').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.suppliers = response.data
                        this.clientForm.supplier_object = this.suppliers[0]
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
            showItem(item) {
                this.$router.push(`/customer/${item.id}`)
            },
            deleteProcess(item) {
                this.selectedCustomerId = item.id
                this.removeCustomerDialog = true
            },
            deleteCustomer(id) {
                axios.delete(`/api/client/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClients()
                        this.snackbarMessage = this.langMap.company.company_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.removeCustomerDialog = false
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
