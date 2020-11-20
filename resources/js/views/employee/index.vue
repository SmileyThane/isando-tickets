<template>
    <v-container fluid>
        <v-snackbar
            :bottom="true"
            :color="actionColor"
            :right="true"
            v-model="snackbar"
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
                                                :error-messages="employeeErrors.name"
                                                :label="langMap.main.name"
                                                name="name"
                                                required
                                                type="text"
                                                v-model="employeeForm.name"
                                            ></v-text-field>
                                        </div>
                                        <div class="col-md-4">
                                            <v-text-field
                                                :color="themeColor"
                                                :error-messages="employeeErrors.email"
                                                :label="langMap.main.email"
                                                name="email"
                                                required
                                                type="email"
                                                v-model="employeeForm.email"
                                            ></v-text-field>
                                        </div>
                                        <div class="col-md-4">
                                            <v-autocomplete
                                                :color="themeColor"
                                                :error-messages="employeeErrors.client_id"
                                                :items="customers"
                                                :label="langMap.customer.customer"
                                                :placeholder="this.$store.state.lang.lang_map.main.search"
                                                hide-no-data
                                                hide-selected
                                                item-text="name"
                                                item-value="id"
                                                v-model="employeeForm.client_id"
                                            ></v-autocomplete>
                                        </div>
                                        <v-btn
                                            :color="themeColor"
                                            @click="addEmployee"
                                            bottom
                                            dark
                                            fab
                                            right
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
                            :expanded.sync="expanded"
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="contacts"
                            :loading="loading"
                            :loading-text="langMap.main.loading"
                            :options.sync="options"
                            :server-items-length="totalEmployees"
                            :single-expand="singleExpand"
                            @click:row="showItem"
                            class="elevation-1"
                            hide-default-footer
                            show-expand
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col md="10" sm="12">
                                        <v-text-field :color="themeColor" :label="langMap.main.search"
                                                      @input="getEmployees"
                                                      class="mx-4" v-model="employeesSearch"></v-text-field>
                                    </v-col>
                                    <v-col md="2" sm="12">
                                        <v-select
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            @change="updateItemsCount"
                                            class="mx-4"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:footer>
                                <v-pagination :color="themeColor"
                                              :length="lastPage"
                                              :page="options.page"
                                              :total-visible="5"
                                              circle
                                              v-model="options.page"
                                >
                                </v-pagination>
                            </template>
                            <template v-slot:item.employee="{ item }">
                                <div @click="showItem(item)" class="justify-center" v-if="item">
                                    {{item.employee.user_data.full_name }}
                                </div>
                            </template>
                            <template v-slot:item.email="{item}">
                                <span v-if="item.employee.user_data.contact_email && item.employee.user_data.contact_email.email">
                                    <v-icon v-if="item.employee.user_data.contact_email.type" x-small dense v-text="item.employee.user_data.contact_email.type.icon" :title="localized(item.employee.user_data.contact_email.type)"></v-icon>
                                    {{item.employee.user_data.contact_email.email}}
                                </span>
                                <span v-else>&nbsp;</span>
                            </template>
                            <template v-slot:item.employee.user_data.is_active="{ item }">
                                <v-icon @click="showItem(item)" class="justify-center" v-if="item">
                                    {{
                                    item.employee.user_data.is_active === 1 ?
                                    'mdi-check-circle-outline' :
                                    'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:item.employee.user_data.status="{ item }">
                                <v-icon @click="showItem(item)" class="justify-center" v-if="item">
                                    {{
                                        item.employee.user_data.status === 1 ?
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
                                    <p><strong> {{langMap.main.actions}}:</strong></p>
                                    <p>
                                        <v-btn
                                            @click="showItem(item)"
                                            color="grey"
                                            dark
                                            fab
                                            x-small
                                        >
                                            <v-icon
                                            >
                                                mdi-eye
                                            </v-icon>
                                        </v-btn>

                                        <v-btn
                                            @click="removeEmployeeProcess(item)"
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
        <template>
            <v-dialog max-width="480" persistent v-model="removeEmployeeDialog">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.delete_selected}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn @click="removeEmployeeDialog = false" color="grey darken-1" text>
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn @click="removeEmployee(selectedEmployeeId)" color="red darken-1" text>
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
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'employee.user_data.name'},
                    {text: `${this.$store.state.lang.lang_map.main.email}`, value: 'employee.user_data.email'},
                    {
                        text: `${this.$store.state.lang.lang_map.individuals.is_active}`,
                        value: 'employee.user_data.is_active'
                    },
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
            localized(item, field = 'name') {
                let locale = this.$store.state.lang.locale.replace(/^([^_]+).*$/, '$1');
                return item[field + '_' + locale] ? item[field + '_' + locale] : item[field];
            },
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
