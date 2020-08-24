<template>
    <v-container>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-data-table
                            show-expand
                            :headers="headers"
                            :items="customers"
                            :single-expand="singleExpand"
                            :expanded.sync="expanded"
                            :options.sync="options"
                            :server-items-length="totalEmployees"
                            :loading="loading"
                            :footer-props="footerProps"
                            class="elevation-1"
                            hide-default-footer
                            loading-text="Give me a second..."
                            @click:row="showItem"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col sm="12" md="10">
                                        <v-text-field @input="getEmployees" v-model="employeesSearch" color="green"
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
                            <template v-slot:item.employee="{ item }">
                                <div @click="showItem(item)" class="justify-center" v-if="item">{{
                                    item.employee.user_data.name }} {{
                                    item.employee.user_data.surname }}
                                </div>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <p><strong>Actions:</strong></p>
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
    </v-container>
</template>


<script>
    export default {

        data() {
            return {
                totalEmployees: 0,
                lastPage: 0,
                loading: 'green',
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
                    {text: 'Name', value: 'employee'},
                    {text: 'Email', value: 'employee.user_data.email'},
                    {text: 'Client', value: 'clients.name'},
                ],
                employeesSearch: '',
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
            this.getEmployees()
        },
        methods: {
            getEmployees() {
                this.loading = "green"
                console.log(this.options);
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id'
                    this.options.sortDesc[0] = false
                }
                if (this.totalEmployees < this.options.itemsPerPage) {
                    this.options.page = 1
                }
                axios.get(`api/employee?
                    search=${this.employeesSearch}&
                    sort_by=${this.options.sortBy[0]}&
                    sort_val=${this.options.sortDesc[0]}&
                    per_page=${this.options.itemsPerPage}&
                    page=${this.options.page}`)
                    .then(
                        response => {
                            response = response.data
                            this.customers = response.data.data
                            console.log(this.customers);
                            this.totalEmployees = response.data.total
                            this.lastPage = response.data.last_page
                            this.loading = false
                        });
            },
            showItem(item) {
                this.$router.push(`/employee/${item.employee.user_data.id}`)
            },
            updateItemsCount(value) {
                this.options.itemsPerPage = value
                this.options.page = 1
            },
        },
        watch: {
            options: {
                handler() {
                    this.getEmployees()
                },
                deep: true,
            },
        },
    }
</script>
