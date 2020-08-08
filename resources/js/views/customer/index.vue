<template>
    <v-container>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    Add New Client
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
                                                    label="Name"
                                                    name="company_name"
                                                    type="text"
                                                    v-model="clientForm.client_name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    color="green"
                                                    label="Description"
                                                    name="company_description"
                                                    type="text"
                                                    v-model="clientForm.client_description"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-select
                                                    label="Supplier"
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
                            :headers="headers"
                            :items="customers"
                            :options.sync="options"
                            :server-items-length="totalCustomers"
                            :loading="loading"
                            :footer-props="footerProps"
                            class="elevation-1"
                            hide-default-footer
                            fixed-header
                            loading-text="Give me a second..."
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col  sm="12" md="7">
                                        <v-text-field @input="getClients" v-model="customersSearch" color="green"
                                                      label="Search..." class="mx-4"></v-text-field>
                                    </v-col>
                                    <v-col  sm="12" md="3">
                                        <div class="text-xs-center pt-2">
                                            <v-pagination color="green"
                                                          item-color="green"
                                                          v-model="options.page"
                                                          :length="lastPage"
                                                          circle
                                                          :page="options.page"
                                                          :total-visible="5"
                                            >
                                            </v-pagination>
                                        </div>
                                    </v-col>
                                    <v-col  sm="12" md="2">
                                        <v-select
                                            color="green"
                                            item-color="green"
                                            :items="footerProps.itemsPerPageOptions"
                                            label="Items per page"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>

                                </v-row>
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
                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
    </v-container>
</template>


<script>
    export default {

        data() {
            return {
                totalCustomers: 0,
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
                    {
                        text: 'ID',
                        align: 'start',
                        value: 'id',
                    },
                    {text: 'name', value: 'name'},
                    {text: 'Description', value: 'description'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                customersSearch: '',
                customers: [],
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
                axios.post('api/client', this.clientForm).then(response => {
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
