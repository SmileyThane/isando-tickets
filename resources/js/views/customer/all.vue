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
                                        <v-text-field @input="getCustomers()" v-model="customersSearch" :color="themeColor"
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
                            <template v-slot:item.id="{item}">
                                <span v-if="item.employee">{{item.employee.user_data.id}}</span>
                                <span v-else>{{item.id}}</span>
                            </template>
                            <template v-slot:item.type="{item}">
                                <span v-if="item.employee">{{langMap.main.individual}}</span>
                                <span v-else>{{langMap.main.customer}}</span>
                            </template>
                            <template v-slot:item.name="{item}">
                                <span v-if="item.employee">{{item.employee.user_data.full_name}}</span>
                                <span v-else>{{item.name}}</span>
                            </template>
                            <template v-slot:item.email="{item}">
                                <span v-if="item.employee">{{item.employee.user_data.email}}</span>
                                <span v-else>&nbsp;</span>
                            </template>
                            <template v-slot:item.description="{item}">
                                <span v-if="item.clients">{{item.clients.name}}</span>
                                <span v-else>{{item.description}}</span>
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
                themeColor: this.$store.state.themeColor,
                totalCustomers: 0,
                lastPage: 0,
                loading: this.themeColor,
                langMap: this.$store.state.lang.lang_map,
                options: {
                    page: 1,
                    sortDesc: [false],
                    sortBy: ['name']
                },
                footerProps: {
                    itemsPerPage: 10,
                    showFirstLastPage: true,
                    itemsPerPageOptions: [10, 25, 50, 100]
                },
                headers: [
                    {text: 'ID', align: 'end', value: 'id', sortable: false},
                    {text: this.$store.state.lang.lang_map.main.type, value: 'type', sortable: false},
                    {text: this.$store.state.lang.lang_map.main.name, value: 'name'},
                    {text: this.$store.state.lang.lang_map.main.email, value: 'email'},
                    {text: this.$store.state.lang.lang_map.main.description, value: 'description'}
                ],
                customersSearch: '',
                customers: []
            }
        },
        mounted() {
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            });
        },
        methods: {
            getCustomers() {
                this.loading = this.themeColor;
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id';
                    this.options.sortDesc[0] = false;
                }
                if (this.totalCustomers < this.options.itemsPerPage) {
                    this.options.page = 1;
                }
                axios.get('api/all', {
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
