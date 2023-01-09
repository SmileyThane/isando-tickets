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
                        <v-data-table
                            :expanded.sync="expanded"
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="companies"
                            :loading="loading"
                            :loading-text="langMap.main.loading"
                            :options.sync="options"
                            :server-items-length="totalCompanies"
                            :single-expand="singleExpand"
                            class="elevation-1"
                            hide-default-footer
                            show-expand
                            @click:row="showItem"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col md="10" sm="12">
                                        <v-text-field v-model="companiesSearch" :color="themeBgColor"
                                                      :label="langMap.main.search"
                                                      class="mx-4" @input="debounceGetCompanies"></v-text-field>
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
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
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
            <v-dialog v-model="removeCompanyDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{langMap.main.delete_selected}} {{langMap.main.company}}?
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeCompanyDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" disabled text @click="deleteCompany(selectedCompanyId)">
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
                clientId: 6,
                snackbar: false,
                actionColor: '',
                themeFgColor: this.$store.state.themeFgColor,
                themeBgColor: this.$store.state.themeBgColor,
                snackbarMessage: '',
                totalCompanies: 0,
                lastPage: 0,
                loading: this.themeBgColor,
                langMap: this.$store.state.lang.lang_map,
                expanded: [],
                singleExpand: false,
                options: {
                    page: 1,
                    sortDesc: [false],
                    sortBy: ['name'],
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
                        sortable: false,
                        value: 'id',
                    },
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name'},
                    {text: `${this.$store.state.lang.lang_map.company.company_number}`, value: 'company_number'},
                    {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'description'},
                ],
                companiesSearch: '',
                companies: [],
                removeCompanyDialog: false,
                selectedCompanyId: null
            }
        },
        mounted() {
            this.getCompanies();
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeBgColor = color;
            });
        },
        created() {
            this.debounceGetCompanies = _.debounce(this.getCompanies, 1000);
        },
        methods: {
            getCompanies() {
                axios.get('/api/company', {
                    params: {
                        search: this.companiesSearch,
                        sort_by: this.options.sortBy[0],
                        sort_val: this.options.sortDesc[0],
                        per_page: this.options.itemsPerPage,
                        page: this.options.page
                    }
                }).then(
                    response => {
                        response = response.data
                        this.companies = response.data.data
                        this.totalCompanies = response.data.total
                        this.lastPage = response.data.last_page
                        this.loading = false
                    });
            },
            showItem(item) {
                let route = this.$store.state.roles.includes(this.clientId) ? '/customer' : '/company';
                this.$router.push(`${route}/${item.id}`)
            },
            deleteProcess(item) {
                this.selectedCompanyId = item.id
                this.removeCompanyDialog = true
            },
            deleteCompany(id) {
                axios.delete(`/api/company/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompanies()
                        this.snackbarMessage = this.langMap.company.company_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.removeCompanyDialog = false
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
                    this.getCompanies()
                },
                deep: true,
            }
        }
    }
</script>
