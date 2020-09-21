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
                                                    color="green"
                                                    :label="langMap.main.name"
                                                    name="company_name"
                                                    type="text"
                                                    v-model="clientForm.client_name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    color="green"
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
                                                    color="green"
                                                    item-color="green"
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
                                                color="green"
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
                                        <v-text-field @input="getClients" v-model="customersSearch" color="green"
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
                    <v-card-title class="headline">{{langMap.main.delete_selected}}?</v-card-title>
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
    export default {

        data() {
            return {
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                totalCustomers: 0,
                lastPage: 0,
                loading: 'green',
                langMap: this.$store.state.lang.lang_map,
                expanded: [],
                singleExpand: false,
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
                        value: 'id',
                    },
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name'},
                    {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'description'},
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
                suppliers: [],
            }
        },
        mounted() {
            this.getClients()
            this.getSuppliers()
        },
        methods: {
            getClients() {
                this.loading = "green"
                console.log(this.options);
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id'
                    this.options.sortDesc[0] = false
                }
                if (this.totalCustomers < this.options.itemsPerPage) {
                    this.options.page = 1
                }
                axios.get(`api/client?
                    search=${this.customersSearch}&
                    sort_by=${this.options.sortBy[0]}&
                    sort_val=${this.options.sortDesc[0]}&
                    per_page=${this.options.itemsPerPage}&
                    page=${this.options.page}`)
                    .then(
                        response => {
                            response = response.data
                            this.customers = response.data.data
                            console.log(this.customers);
                            this.totalCustomers = response.data.total
                            this.lastPage = response.data.last_page
                            this.loading = false
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
                        console.log('error')
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
                        console.log('error')
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
                        this.snackbarMessage = 'Company was deleted '
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.removeCustomerDialog = false
                    } else {
                        this.snackbarMessage = 'Company delete error'
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
            updateItemsCount(value) {
                this.options.itemsPerPage = value
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
