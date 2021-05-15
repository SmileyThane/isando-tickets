<template>
    <v-container fluid>
        <v-snackbar :bottom="true" :right="true" v-model="snackbar" :color="actionColor">
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
                                        <v-text-field @input="debounceGetCustomers" v-model="customersSearch" :color="themeBgColor"
                                                      :label="langMap.main.search" class="mx-4"></v-text-field>
                                    </v-col>
                                    <v-col sm="12" md="2">
                                        <v-select
                                            class="mx-4"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            v-model="options.itemsPerPage"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:item.id="{item}">
                                <span v-if="item.employee">{{item.employee.user_data.id}}</span>
                                <span v-else>{{item.id}}</span>
                            </template>
                            <template v-slot:item.type="{item}">
                                <v-icon v-if="item.employee" :title="langMap.main.individual">mdi-account</v-icon>
                                <v-icon v-else :title="langMap.main.customer">mdi-factory</v-icon>
                            </template>
                            <template v-slot:item.name="{item}">
                                <span v-if="item.employee">{{item.employee.user_data.full_name}}</span>
                                <span v-else>{{item.name}}</span>
                            </template>
                            <template v-slot:item.email="{item}">
                                <span v-if="item.employee && item.employee.user_data.contact_email && item.employee.user_data.contact_email.email">
                                    <v-icon v-if="item.employee.user_data.contact_email.type" x-small dense v-text="item.employee.user_data.contact_email.type.icon" :title="$helpers.i18n.localized(item.employee.user_data.contact_email.type)"></v-icon>
                                    {{item.employee.user_data.contact_email.email}}
                                </span>
                                <span v-else-if="item.contact_email && item.contact_email.email">
                                    <v-icon v-if="item.contact_email.type" x-small dense v-text="item.contact_email.type.icon" :title="$helpers.i18n.localized(item.contact_email.type)"></v-icon>
                                    {{item.contact_email.email}}
                                </span>
                                <span v-else>&nbsp;</span>
                            </template>
                            <template v-slot:item.phone="{item}">
                                <span v-if="item.employee && item.employee.user_data.contact_phone && item.employee.user_data.contact_phone.phone">
                                    <v-icon v-if="item.employee.user_data.contact_phone.type" x-small dense v-text="item.employee.user_data.contact_phone.type.icon" :title="$helpers.i18n.localized(item.employee.user_data.contact_phone.type)"></v-icon>
                                    {{item.employee.user_data.contact_phone.phone}}
                                </span>
                                <span v-else-if="item.contact_phone && item.contact_phone.phone">
                                    <v-icon v-if="item.contact_phone.type" x-small dense v-text="item.contact_phone.type.icon" :title="$helpers.i18n.localized(item.contact_phone.type)"></v-icon>
                                    {{item.contact_phone.phone}}
                                </span>
                                <span v-else>&nbsp;</span>
                            </template>
                            <template v-slot:item.city="{item}">
                                <span v-if="item.employee && item.employee.user_data.addresses && item.employee.user_data.addresses.length > 0">
                                    <v-icon v-if="item.employee.user_data.addresses[0].type" x-small dense v-text="item.employee.user_data.addresses[0].type.icon" :title="$helpers.i18n.localized(item.employee.user_data.addresses[0].type)"></v-icon>
                                    {{item.employee.user_data.addresses[0].city}}
                                </span>
                                <span v-else-if="item.addresses && item.addresses.length > 0">
                                    <v-icon v-if="item.addresses[0].type" x-small dense v-text="item.addresses[0].type.icon" :title="$helpers.i18n.localized(item.addresses[0].type)"></v-icon>
                                    {{item.addresses[0].city}}
                                </span>
                                <span v-else>&nbsp;</span>
                            </template>
                            <template v-slot:item.country="{item}">
                                <span v-if="item.employee && item.employee.user_data.addresses && item.employee.user_data.addresses.length > 0 && item.employee.user_data.addresses[0].country" :title="$helpers.i18n.localized(item.employee.user_data.addresses[0].country)">
                                    {{item.employee.user_data.addresses[0].country.iso_3166_2}}
                                </span>
                                <span v-else-if="item.addresses && item.addresses.length > 0 && item.addresses[0].country" :title="$helpers.i18n.localized(item.addresses[0].country)">
                                    {{item.addresses[0].country.iso_3166_2}}
                                </span>
                                <span v-else>&nbsp;</span>
                            </template>
                            <template v-slot:item.is_active="{item}">
                                <v-icon v-if="item.employee" class="justify-center">
                                    {{item.employee.user_data.status === 1 ? 'mdi-check-circle-outline' : 'mdi-cancel'}}
                                </v-icon>
                                <v-icon v-else class="justify-center">
                                    {{item.is_active === 1 ? 'mdi-check-circle-outline' : 'mdi-cancel'}}
                                </v-icon>
                            </template>
                            <template v-slot:footer>
                                <v-pagination :color="themeBgColor"
                                              v-model="options.page"
                                              :length="lastPage"
                                              circle
                                              :page="options.page"
                                              :total-visible="5"
                                >
                                </v-pagination>
                            </template>
                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
    </v-container>
</template>


<script>
    import EventBus from "../../components/EventBus";

    export default {

        data() {
            return {
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                themeFgColor: this.$store.state.themeFgColor,
themeBgColor: this.$store.state.themeBgColor,
                totalCustomers: 0,
                lastPage: 0,
                loading: this.themeBgColor,
                langMap: this.$store.state.lang.lang_map,
                options: {
                    page: 1,
                    sortDesc: [false],
                    sortBy: ['name'],
                    itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10
                },
                footerProps: {
                    showFirstLastPage: true,
                    itemsPerPageOptions: [10, 25, 50, 100]
                },
                headers: [
                    {text: 'ID', align: 'end', value: 'id', sortable: false},
                    {text: this.$store.state.lang.lang_map.main.type, value: 'type', sortable: false},
                    {text: this.$store.state.lang.lang_map.main.name, value: 'name'},
                    {text: this.$store.state.lang.lang_map.main.email, value: 'email'},
                    {text: this.$store.state.lang.lang_map.main.phone, value: 'phone'},
                    {text: this.$store.state.lang.lang_map.main.city, value: 'city'},
                    {text: this.$store.state.lang.lang_map.main.country, value: 'country'},
                    {text: this.$store.state.lang.lang_map.main.status, value: 'is_active'}
                ],
                customersSearch: '',
                customers: []
            }
        },
        mounted() {
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeBgColor = color;
            });
        },
        created() {
            this.debounceGetCustomers = _.debounce(this.getCustomers, 1000);
        },
        methods: {
            getCustomers() {
                this.loading = this.themeBgColor;
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id';
                    this.options.sortDesc[0] = false;
                }
                if (this.totalCustomers < this.options.itemsPerPage) {
                    this.options.page = 1;
                }
                axios.get('/api/all', {
                    params: {
                        search: this.customersSearch,
                        sort_by: this.options.sortBy[0],
                        sort_val: this.options.sortDesc[0],
                        per_page: this.options.itemsPerPage,
                        page: this.options.page
                    }
                }).then(response => {
                    this.loading = false;
                    response = response.data;
                            if (response.success === true) {
                                this.customers = response.data.data;
                                this.totalCustomers = response.data.total;
                                this.lastPage = response.data.last_page;
                            } else {
                                this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                                this.errorType = 'error';
                                this.snackbar = true;
                            }
                        });
            },
            showItem(item) {
                if (item.employee) {
                    this.$router.push(`/employee/${item.employee.user_data.id}`);
                } else {
                    this.$router.push(`/customer/${item.id}`);
                }
            },
            updateItemsCount(value) {
                this.options.itemsPerPage = value;
                localStorage.itemsPerPage = value;
                this.options.page = 1;
            }
        },
        watch: {
            options: {
                handler() {
                    this.getCustomers()
                },
                deep: true
            }
        },
    }
</script>
