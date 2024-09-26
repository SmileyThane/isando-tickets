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
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    <span style="font-size: 12px;">{{ langMap.customer.add_new }}</span>
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <v-text-field
                                                    v-model="clientForm.client_name"
                                                    :color="themeBgColor"
                                                    :error-messages="errors.name"
                                                    :label="langMap.main.name"
                                                    name="company_name"
                                                    required
                                                    type="text"
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-2">
                                                <v-text-field
                                                    v-model="clientForm.company_external_id"
                                                    :color="themeBgColor"
                                                    :error-messages="errors.company_external_id"
                                                    :label="langMap.main.client_number"
                                                    name="number"
                                                    prepend-icon="mdi-numeric"
                                                    type="text"
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-2">
                                                <v-text-field
                                                    v-model="clientForm.company_external_id"
                                                    :color="themeBgColor"
                                                    :error-messages="errors.company_external_id"
                                                    :label="langMap.company.company_number"
                                                    name="number"
                                                    prepend-icon="mdi-numeric"
                                                    type="text"
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    v-model="clientForm.client_description"
                                                    :color="themeBgColor"
                                                    :error-messages="errors.description"
                                                    :label="langMap.main.description"
                                                    name="company_description"
                                                    required
                                                    type="text"
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-2">
                                                <v-select
                                                    v-model="clientForm.supplier_object"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :items="suppliers"
                                                    :label="langMap.customer.supplier"
                                                    item-text="name"
                                                    item-value="item"
                                                />
                                            </div>

                                            <v-btn
                                                :color="themeBgColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                @click="addClient"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                    mdi-plus
                                                </v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-data-table
                            :expanded.sync="expanded"
                            :footer-props="footerProps"
                            :headers="filteredHeaders"
                            :items="customers"
                            :loading="loading"
                            :loading-text="langMap.main.loading"
                            :options.sync="options"
                            :server-items-length="totalCustomers"
                            :single-expand="singleExpand"
                            class="elevation-1"
                            fixed-header
                            hide-default-footer
                            show-expand
                            @click:row="showItem"
                        >
                            <template v-slot:top>
                                <v-row class="mt-2">
                                    <v-col cols="4" class="pb-0">
                                        <v-text-field v-model="customersSearch" :color="themeBgColor"
                                                      :label="langMap.main.search"
                                                      style="font-size: 12px;"
                                                      class="mx-4" @input="debounceGetClients" clearable></v-text-field>
                                    </v-col>
                                    <v-col cols="4" class="pb-0">
                                        <v-select v-model="searchWhere" :color="themeBgColor" :items="searchOptions"
                                                  item-text="name" item-value="id" label="Search in"  multiple clearable
                                                  style="font-size: 12px;"
                                                  :menu-props="{ bottom: true, offsetY: true }"
                                                  class="search_in_select"
                                                  v-on:change="debounceGetClients"/>
                                    </v-col>
                                    <v-col cols="2">
                                        <v-select
                                            v-model="options.itemsPerPage"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            :menu-props="{ bottom: true, offsetY: true }"
                                            class="mx-4 d-flex"
                                            @change="updateItemsCount"
                                            style="font-size: 12px;"
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="2">
                                        <v-pagination v-model="options.page"
                                                      :color="themeBgColor"
                                                      :length="lastPage"
                                                      :page="options.page"
                                                      :total-visible="0"
                                                      class="mx-4 mt-2 d-flex"
                                                      style="font-size: 12px;"
                                                      circle
                                        >
                                        </v-pagination>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:item.logo="{ item }">
                                <v-img :src="item.logo_url" style="max-width: 10em; max-height: 2em"/>
                            </template>
                            <template v-slot:item.email="{item}">
                                <span v-if="item.contact_email && item.contact_email.email">
                                    <v-icon v-if="item.contact_email.type"
                                            :title="$helpers.i18n.localized(item.contact_email.type)" dense
                                            x-small
                                            v-text="item.contact_email.type.icon"></v-icon>
                                    {{ item.contact_email.email }}
                                </span>
                                <span v-else>&nbsp;</span>
                            </template>
                            <template v-slot:item.contact_phone="{item}">
                                <span v-if="item.contact_phone" style="display: flex; align-items: center; gap: 8px;">
                                    <v-icon v-if="item.contact_phone.type"
                                            :title="$helpers.i18n.localized(item.contact_phone.type)" dense
                                            x-small
                                            v-text="item.contact_phone.type.icon"></v-icon>
                                    {{ item.contact_phone.phone }}
                                    <br>
                                </span>
                            </template>
                            <template v-slot:item.client_filter_groups="{item}">
                                <span v-if="item.client_filter_groups.length" style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
                                    <v-icon x-small
                                    >mdi mdi-account-multiple</v-icon>
                                    <p v-for="(group, index) in item.client_filter_groups.slice(0, 6)" :key="group.id" class="mb-0">
                                        <span v-if="index !== item.client_filter_groups.slice(0, 6).length - 1  && group.data !== null">{{ group.data.name + ', ' }}</span>
                                        <span v-if="index === item.client_filter_groups.slice(0, 6).length - 1 && group.data !== null">
                                            {{ group.data.name + `${item.client_filter_groups.slice(0, 6).length < item.client_filter_groups.length ? ' ...' : ''}`}}
                                        </span>
                                    </p>
                                </span>
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
                            <template v-slot:item.is_active="{ item }">
                                <v-icon v-if="item" :style="item.is_active === 1 ?'color:#95C13D;' : 'color:red;'"
                                        class="justify-center"
                                        @click="showItem(item)"
                                >
                                    {{
                                        item.is_active === 1 ?
                                            'mdi-check-circle-outline' :
                                            'mdi-cancel'
                                    }}
                                </v-icon>
                            </template>
                            <template v-slot:item.supplier_name="{item}">
                                <span v-if="item.supplier_name" style="display: flex; align-items: center; gap: 8px;">
                                    {{ item.supplier_name }}
                                    <br>
                                </span>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <p><strong>{{ langMap.main.actions }}:</strong></p>
                                    <p>
                                        <v-btn
                                            color="grey"
                                            dark
                                            fab
                                            x-small
                                            @click="showItem(item)"
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
                                            @click="deleteProcess(item)"
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
            <v-dialog v-model="removeCustomerDialog" max-width="480" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.main.delete_selected }}?
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeCustomerDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteCustomer(selectedCustomerId)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
    </v-container>
</template>

<style scoped>
.v-data-table /deep/ .sticky-header {
    position: sticky;
}

.v-data-table /deep/ .v-data-table__wrapper {
    overflow: unset;
    z-index: 5;
    position: relative;
    background: #fff;
}

.v-data-table /deep/ .text-start {
    padding: 0 8px;
}

.v-data-table /deep/ .text-center {
    padding: 0 8px;
}

.search_in_select /deep/ .v-input__icon--clear button {
    color: #95c13d;
}
</style>

<script>
import EventBus from "../../components/EventBus";
import _ from "lodash";

export default {
    props: {
        page: {
            type: [String, Number],
            default: 1
        }
    },
    data() {
        return {
            snackbar: false,
            actionColor: '',
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            type: this.$route.query.type,
            totalCustomers: 0,
            lastPage: 0,
            loading: this.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
            expanded: [],
            singleExpand: false,
            options: {
                page: this.$route.query.page ? parseInt(this.$route.query.page) : 1,
                sortDesc: [false],
                sortBy: ['name'],
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [
                    {
                        'text': 10,
                        'value': 10,
                    },
                    {
                        'text': 20,
                        'value': 20,
                    },
                    {
                        'text': 50,
                        'value': 50,
                    },
                    {
                        'text': 100,
                        'value': 100,
                    },
                    {
                        'text': this.$store.state.lang.lang_map.sidebar.all,
                        'value': 10000,
                    }
                ],
            },
            headers: [
                {text: '', value: 'data-table-expand'},
                {
                    text: 'ID',
                    align: 'start',
                    value: 'id',
                },
                {
                    text: `${this.$store.state.lang.lang_map.company.logo}`,
                    value: 'logo',
                    align: 'center',
                    sortable: false
                },
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name'},
                {text: `${this.$store.state.lang.lang_map.main.client_number}`, value: 'company_external_id'},
                {text: this.$store.state.lang.lang_map.main.email, value: 'email', sortable: false},
                {text: this.$store.state.lang.lang_map.main.phone, value: 'contact_phone', sortable: false, width: '150px'},
                {text: `${this.$store.state.lang.lang_map.main.client_groups}`, value: 'client_filter_groups'},
                {text: `${this.$store.state.lang.lang_map.customer.active}`, value: 'is_active'},
                {text: `${this.$store.state.lang.lang_map.customer.supplier}`, value: 'supplier_name'},
                {text: `${this.$store.state.lang.lang_map.main.last_activity}`, value: 'last_activity'},
            ],
            customersSearch: '',
            customersSearchOption: '',
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
            errors: {},
            currency: {
                symbol: ''
            },
            searchWhere: [1, 2, 3, 4, 5, 6],
            searchOptions: [
                {id: 1, name: this.$store.state.lang.lang_map.kb.search_in_company_name},
                {id: 2, name: this.$store.state.lang.lang_map.kb.search_in_client_number},
                {id: 3, name: this.$store.state.lang.lang_map.kb.search_in_email},
                {id: 4, name: this.$store.state.lang.lang_map.kb.search_in_description},
                {id: 5, name: this.$store.state.lang.lang_map.kb.search_in_supplier},
                {id: 6, name: this.$store.state.lang.lang_map.kb.search_in_client_groups},
            ],
        }
    },
    created() {
        this.debounceGetClients = _.debounce(this.getClients, 500);
    },
    mounted() {
        this.updatePageFromRoute();
        this.getSuppliers();
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        updatePageFromRoute() {
            if (this.page) {
                this.getClients();
            }
        },
        getClients() {
            this.customers = [];
            this.loading = this.themeBgColor
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id'
                this.options.sortDesc[0] = false
            }
            if (this.totalCustomers > 0 && this.totalCustomers < this.options.itemsPerPage) {
                this.options.page = 1
            }
            axios.get(`/api/client?type=${this.$route.query.type}`, {
                params: {
                    search: this.customersSearch,
                    sort_by: this.options.sortBy[0],
                    sort_val: this.options.sortDesc[0],
                    per_page: this.options.itemsPerPage,
                    page: this.page,
                    company_name: this.searchWhere.includes(1) ? 1 : 0,
                    client_number: this.searchWhere.includes(2) ? 1 : 0,
                    email: this.searchWhere.includes(3) ? 1 : 0,
                    description: this.searchWhere.includes(4) ? 1 : 0,
                    supplier: this.searchWhere.includes(5) ? 1 : 0,
                    client_groups: this.searchWhere.includes(6) ? 1 : 0,
                }
            }).then(response => {
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
            }).finally(() => {
                this.loading = false
            });
        },
        addClient() {
            this.clientForm.supplier_type = Object.keys(this.clientForm.supplier_object).shift()
            this.clientForm.supplier_id = Object.values(this.clientForm.supplier_object).shift()
            axios.post('/api/client', this.clientForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.clientForm = {}
                    this.showItem(response.data)
                } else {
                    this.errors = response.error
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        getSuppliers() {
            axios.get('/api/supplier').then(response => {
                response = response.data
                if (response.success === true) {
                    this.suppliers = response.data
                    this.clientForm.supplier_object = this.suppliers[0].item
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
        searchWhere(value) {
            if(!value.length) {
                this.searchWhere = [1]
            }
        },
        customersSearch(val){
            if(val.length && !this.searchWhere.length) {
                this.searchWhere = [1]
            }
        },
        options: {
            handler() {
                this.getClients();
            },
            deep: true,
        },
        'options.page'(value) {
            if (value !== this.$route.query.page) {
                this.$router.push({
                    name: 'customers',
                    query: { ...this.$route.query, page: value, },
                });
            }
        },
        loading() {
            this.$parent.$parent.$refs.container.scrollTop = 0
        },
        page(newValue) {
            this.options.page = parseInt(newValue) ?? 1;
        },
        $route() {
            this.getClients()
        },

    },
    computed: {
        filteredHeaders() {
            if (this.$route.query.type === 'suppliers') {
                return this.headers.filter((header) => header.value !== 'supplier_name');
            }
            return this.headers;
        },
    },
}
</script>
