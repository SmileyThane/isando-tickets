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
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-header>
                                {{langMap.individuals.add_new}}
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
                                                name="name"
                                                type="text"
                                                v-model="employeeForm.name"
                                                :error-messages="employeeErrors.name"
                                                required
                                            ></v-text-field>
                                        </div>
                                        <div class="col-md-4">
                                            <v-text-field
                                                :color="themeColor"
                                                :label="langMap.main.email"
                                                name="email"
                                                type="email"
                                                v-model="employeeForm.email"
                                                :error-messages="employeeErrors.email"
                                                required
                                            ></v-text-field>
                                        </div>
                                        <div class="col-md-4">
                                            <v-autocomplete
                                                v-model="employeeForm.client_id"
                                                :items="customers"
                                                :error-messages="employeeErrors.client_id"
                                                :color="themeColor"
                                                hide-no-data
                                                hide-selected
                                                item-text="name"
                                                item-value="id"
                                                :label="langMap.customer.customer"
                                                :placeholder="this.$store.state.lang.lang_map.main.search"
                                            ></v-autocomplete>
                                        </div>
                                        <v-btn
                                            dark
                                            fab
                                            right
                                            bottom
                                            :color="themeColor"
                                            @click="addEmployee"
                                        >
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>
                                    </div>
                                </v-form>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panels>
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-data-table
                            show-expand
                            :headers="headers"
                            :items="contacts"
                            :single-expand="singleExpand"
                            :expanded.sync="expanded"
                            :options.sync="options"
                            :server-items-length="totalEmployees"
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
                                        <v-text-field @input="getEmployees" v-model="employeesSearch" :color="themeColor"
                                                      :label="langMap.main.search" class="mx-4"></v-text-field>
                                    </v-col>
                                    <v-col sm="12" md="2">
                                        <v-select
                                            class="mx-4"
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
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
                            <template v-slot:item.employee="{ item }">
                                <div @click="showItem(item)" class="justify-center" v-if="item">{{
                                    item.employee.user_data.name }} {{
                                    item.employee.user_data.surname }}
                                </div>
                            </template>
                            <template v-slot:item.employee.user_data.is_active="{ item }">
                                <div @click="showItem(item)" class="justify-center" v-if="item">{{
                                    item.employee.user_data.is_active === 1 ? 'yes' : 'no' }}
                                </div>
                            </template>
                            <template v-slot:item.employee.user_data.status="{ item }">
                                <div @click="showItem(item)" class="justify-center" v-if="item">{{
                                    item.employee.user_data.status === 1 ? 'Active' : 'Inactive' }}
                                </div>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <p><strong> {{langMap.main.actions}}:</strong></p>
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
                                            @click="removeEmployeeProcess(item)"
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
            <v-dialog v-model="removeEmployeeDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.delete_selected}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeEmployeeDialog = false">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="removeEmployee(selectedEmployeeId)">
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
                totalEmployees: 0,
                lastPage: 0,
                loading: this.themeColor,
                expanded: [],
                singleExpand: false,
                isLoading: false,
                selectedEmployeeId: null,
                removeEmployeeDialog: false,
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
                langMap: this.$store.state.lang.lang_map,
                headers: [
                    {text: '', value: 'data-table-expand'},
                    {
                        text: 'ID',
                        align: 'start',
                        value: 'id',
                    },
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'employee'},
                    {text: `${this.$store.state.lang.lang_map.main.email}`, value: 'employee.user_data.email'},
                    {text: `${this.$store.state.lang.lang_map.individuals.is_active}`, value: 'employee.user_data.is_active'},
                    {text: `${this.$store.state.lang.lang_map.individuals.status}`, value: 'employee.user_data.status'},
                    {text: `${this.$store.state.lang.lang_map.main.client}`, value: 'clients.name'},
                ],
                employeesSearch: '',
                employeeErrors: [],
                contacts: [],
                customersSearch: '',
                customers: [],
                employeeForm: {
                    name: '',
                    email: '',
                    client_id: '',
                    is_active: false
                },
                suppliers: []
            }
        },
        mounted() {
            this.getEmployees();
            this.getClients();
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            });
        },
        methods: {
            getEmployees() {
                this.loading = this.themeColor
                // console.log(this.options);
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
                            this.contacts = response.data.data
                            this.totalEmployees = response.data.total
                            this.lastPage = response.data.last_page
                            this.loading = false
                        });
            },
            getClients() {
                this.isLoading = true
                axios.get(`/api/client`)
                    .then(
                        response => {
                            response = response.data
                            this.customers = response.data.data
                            this.isLoading = false
                        });
            },
            addEmployee() {
                axios.post(`/api/client/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getEmployees()
                        this.snackbarMessage = 'Contact was added successfully'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        console.log('error')
                        this.employeeErrors = response.error
                    }

                });
            },
            removeEmployeeProcess(item) {
                this.selectedEmployeeId = item.id
                this.removeEmployeeDialog = true
            },
            removeEmployee(id) {
                axios.delete(`/api/client/employee/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getEmployees()
                        this.rolesDialog = false
                        // this.snackbarMessage = 'Contact was removed'
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.removeEmployeeDialog = false
                    } else {
                        console.log('error')
                    }

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
