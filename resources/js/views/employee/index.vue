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
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-header>
                                {{ langMap.individuals.add_new }}
                                <template v-slot:actions>
                                    <v-icon color="submit">mdi-plus</v-icon>
                                </template>
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                                <v-form>
                                    <div class="row">
                                        <v-text-field
                                            v-model="employeeForm.name"
                                            :color="themeColor"
                                            :error-messages="employeeErrors.name"
                                            :label="langMap.main.name"
                                            class="col-md-4"
                                            dense
                                            name="name"
                                            required
                                            type="text"
                                        ></v-text-field>
                                        <v-text-field
                                            v-model="employeeForm.middle_name"
                                            :color="themeColor"
                                            :error-messages="employeeErrors.middle_name"
                                            :label="this.$store.state.lang.lang_map.main.middle_name"
                                            class="col-md-4"
                                            dense
                                            lazy-validation
                                            name="middle_name"
                                            type="text"
                                        ></v-text-field>
                                        <v-text-field
                                            v-model="employeeForm.surname"
                                            :color="themeColor"
                                            :error-messages="employeeErrors.surname"
                                            :label="this.$store.state.lang.lang_map.main.last_name"
                                            class="col-md-4"
                                            dense
                                            lazy-validation
                                            name="surname"
                                            type="text"
                                        ></v-text-field>
                                        <v-text-field
                                            v-model="employeeForm.email"
                                            :color="themeColor"
                                            :error-messages="employeeErrors.email"
                                            :label="langMap.main.email"
                                            class="col-md-4"
                                            dense
                                            name="email"
                                            required
                                            type="email"
                                        ></v-text-field>
                                        <v-autocomplete
                                            v-model="employeeForm.client_id"
                                            :color="themeColor"
                                            :error-messages="employeeErrors.client_id"
                                            :items="customers"
                                            :label="langMap.customer.customer"
                                            :placeholder="this.$store.state.lang.lang_map.main.search"
                                            class="col-md-4"
                                            dense
                                            hide-no-data
                                            hide-selected
                                            item-text="name"
                                            item-value="id"
                                        ></v-autocomplete>
                                        <v-autocomplete
                                            v-model="employeeForm.language_id"
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            :items="languages"
                                            :label="this.$store.state.lang.lang_map.main.language"
                                            class="col-md-4"
                                            dense
                                            item-text="name"
                                            item-value="id"
                                            lazy-validation
                                            name="language"
                                        />
                                        <v-btn
                                            :color="themeColor"
                                            bottom
                                            dark
                                            fab
                                            right
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
                            :expanded.sync="expanded"
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="contacts"
                            :loading="loading"
                            :loading-text="langMap.main.loading"
                            :options.sync="options"
                            :server-items-length="totalEmployees"
                            :single-expand="singleExpand"
                            class="elevation-1"
                            hide-default-footer
                            @click:row="showItem"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col md="10" sm="12">
                                        <v-text-field v-model="employeesSearch" :color="themeColor"
                                                      :label="langMap.main.search"
                                                      class="mx-4" @input="getEmployees"></v-text-field>
                                    </v-col>
                                    <v-col md="2" sm="12">
                                        <v-select
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            class="mx-4"
                                            v-model="options.itemsPerPage"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:footer>
                                <v-pagination v-model="options.page"
                                              :color="themeColor"
                                              :length="lastPage"
                                              :page="options.page"
                                              :total-visible="5"
                                              circle
                                >
                                </v-pagination>
                            </template>
                            <template v-slot:item.id="{ item }">
                                <div v-if="item.user_data" class="justify-center" @click="showItem(item)">
                                    {{ item.user_data.id }}
                                </div>
                            </template>
                            <template v-slot:item.avatar="{ item }">
                                <v-avatar
                                    size="2em"
                                    color="grey darken-1"
                                    v-if="item.user_data && (item.user_data.avatar_url || item.user_data.full_name)"
                                >
                                    <v-img v-if="item.user_data.avatar_url" :src="item.user_data.avatar_url" />
                                    <span v-else-if="item.user_data.full_name" class="white--text">
                                            {{ item.user_data.full_name.split(/\s/).reduce((response,word)=> response+=word.slice(0,1),'').substr(0, 2).toLocaleUpperCase() }}
                                        </span>

                                </v-avatar>
                                <v-icon v-else large>mdi-account-circle</v-icon>
                            </template>
                            <template v-slot:item.user_data.emails="{item}">
                                <span v-for="email in item.user_data.emails" v-if="item.user_data.emails.length > 0">
                                    <v-icon v-if="email.type"
                                            :title="localized(email.type)" dense
                                            x-small
                                            v-text="email.type.icon"></v-icon>
                                    {{ email.email }}
                                    <br>
                                </span>

                                <span v-else>&nbsp;</span>
                            </template>
                            <template v-slot:item.user_data.is_active="{ item }">
                                <v-icon v-if="item" @click="showItem(item)">
                                    {{
                                        item.user_data.is_active === 1 ?
                                            'mdi-check-circle-outline' :
                                            'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:item.user_data.status="{ item }">
                                <v-icon v-if="item" @click="showItem(item)">
                                    {{
                                        item.user_data.status === 1 ?
                                            'mdi-check-circle-outline' :
                                            'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:item.assigned_to_clients.clients="{ item }">
                                <span v-for="clientItem in item.assigned_to_clients"
                                      @click="showItem(item)">
                                    <span v-if="clientItem.clients">
                                        {{ clientItem.clients.name }}
                                    </span>
                                    <br>
                                </span>
                                <v-icon v-if="item.assigned_to_clients.length === 0" class="justify-center"
                                        @click="showItem(item)">
                                    mdi-cancel
                                </v-icon>
                            </template>

                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
        <template>
            <v-dialog v-model="removeEmployeeDialog" max-width="480" persistent>
                <v-card>
                    <v-card-title>{{ langMap.main.delete_selected }}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeEmployeeDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="removeEmployee(selectedEmployeeId)">
                            {{ langMap.main.delete }}
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
            languages: [],
            singleExpand: false,
            isLoading: false,
            selectedEmployeeId: null,
            removeEmployeeDialog: false,
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
            langMap: this.$store.state.lang.lang_map,
            headers: [
                {text: '', value: 'data-table-expand'},
                {text: 'ID', align: 'start', value: 'id'},
                {text: `${this.$store.state.lang.lang_map.profile.personal_id}`, value: 'user_data.number'},
                {text: `${this.$store.state.lang.lang_map.profile.avatar}`, value: 'avatar', align: 'center', sortable: false},
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'user_data.name'},
                {text: `${this.$store.state.lang.lang_map.main.last_name}`, value: 'user_data.surname'},
                {text: `${this.$store.state.lang.lang_map.main.email}`, value: 'user_data.emails'},
                {text: `${this.$store.state.lang.lang_map.individuals.is_active}`, value: 'user_data.is_active', align: 'center'},
                {text: `${this.$store.state.lang.lang_map.individuals.status}`, value: 'user_data.status', align: 'center'},
                {text: `${this.$store.state.lang.lang_map.main.client}`, value: 'assigned_to_clients.clients'},
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
        this.getLanguages();
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
            axios.get('api/employee', {
                params: {
                    search: this.employeesSearch,
                    sort_by: this.options.sortBy[0],
                    sort_val: this.options.sortDesc[0],
                    per_page: this.options.itemsPerPage,
                    page: this.options.page
                }
            }).then(
                response => {
                    this.loading = false
                    response = response.data
                    if (response.success === true) {
                        this.contacts = response.data.data
                        this.totalEmployees = response.data.total
                        this.lastPage = response.data.last_page
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.snackbar = true;
                    }
                });
        },
        getClients() {
            this.isLoading = true
            axios.get(`/api/client`)
                .then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.customers = response.data.data
                        this.isLoading = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.snackbar = true;
                    }
                });
        },
        getLanguages() {
            axios.get('/api/lang').then(response => {
                response = response.data
                if (response.success === true) {
                    this.languages = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        addEmployee() {
            axios.post(`/api/client/employee`, this.employeeForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getEmployees()
                    this.snackbarMessage = this.langMap.company.employee_created
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.snackbar = true;
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
                    this.snackbarMessage = this.langMap.company.employee_deleted
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.removeEmployeeDialog = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.snackbar = true;
                }
            });
        },
        showItem(item) {
            this.$router.push(`/employee/${item.user_data.id}`)
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value
            localStorage.itemsPerPage = value;
            this.options.page = 1
        }
    },
    watch: {
        options: {
            handler() {
                this.getEmployees()
            },
            deep: true
        },
    },
}
</script>
