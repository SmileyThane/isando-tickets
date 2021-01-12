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

        <v-data-table
            :headers="headers"
            :items="projects"
            item-key="id"
            :options.sync="options"
            :server-items-length="totalProjects"
            :loading="loading"
            :footer-props="footerProps"
            class="elevation-4"
            hide-default-footer
            fixed-header
            :loading-text="langMap.main.loading"
            @click:row="showItem"
        >
            <template v-slot:top>

                <v-row>
                    <v-col sm="12" md="8">
                        <v-text-field @input="getProjects" v-model="trackingProjectSearch" :color="themeColor"
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
                    <v-col sm="12" md="2" class="pt-6">
                        <v-btn
                            style="color: white;"
                            :color="themeColor"
                            :item-color="themeColor"
                            elevation="2"
                            @click="dialog=true"
                        >{{ langMap.tracking.create_project.btn_title }}</v-btn>
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
            <template v-slot:expanded-item="{ headers, item }">
                <td :colspan="headers.length">
                    More info about {{ item.name }}
                </td>
            </template>
        </v-data-table>

        <template>
            <v-row justify="center">
                <v-dialog
                    v-model="dialog"
                    persistent
                    max-width="520"
                >
                    <v-card>
                        <v-card-title class="headline">
                            {{ langMap.tracking.create_project.modal_title }}
                        </v-card-title>
                        <v-card-text>
                            <v-text-field
                                :color="themeColor"
                                :item-color="themeColor"
                                :label="langMap.tracking.create_project.name"
                                v-model="project.name"
                            ></v-text-field>
                            <v-select
                                :items="products"
                                item-text="name"
                                item-value="id"
                                :label="langMap.tracking.create_project.product"
                                v-model="project.productId"
                            ></v-select>
                            <v-select
                                :items="clients"
                                item-text="name"
                                item-value="id"
                                :label="langMap.tracking.create_project.client"
                                v-model="project.clientId"
                            ></v-select>
                            <v-text-field v-model="project.color" hide-details class="ma-0 pa-0" solo>
                                <template v-slot:append>
                                    <v-menu v-model="colorMenu" top nudge-bottom="105" nudge-left="16" :close-on-content-click="false">
                                        <template v-slot:activator="{ on }">
                                            <div :style="switchColor" v-on="on" />
                                        </template>
                                        <v-card>
                                            <v-card-text class="pa-0">
                                                <v-color-picker v-model="project.color" flat />
                                            </v-card-text>
                                        </v-card>
                                    </v-menu>
                                </template>
                            </v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="green darken-1"
                                text
                                @click="resetProject()"
                            >
                                {{ langMap.tracking.create_project.cancel }}
                            </v-btn>
                            <v-btn
                                color="green darken-1"
                                text
                                @click="createProject()"
                            >
                                {{ langMap.tracking.create_project.create }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-row>
        </template>

    </v-container>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeColor: this.$store.state.themeColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            trackingProjectSearch: '',
            projects: [],
            totalProjects: 0,
            lastPage: 0,
            loading: false,
            dialog: false,
            options: {
                page: 1,
                sortDesc: [true],
                sortBy: ['id']
            },
            footerProps: {
                itemsPerPage: 10,
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            headers: [
                {text: '', value: 'data-table-expand'},
                // {
                //     text: 'ID',
                //     align: 'start',
                //     sortable: false,
                //     value: 'id',
                // },
                {text: `${this.$store.state.lang.lang_map.tracking.name}`, value: 'name'},
                {text: `${this.$store.state.lang.lang_map.tracking.client}`, value: 'client.name'},
                {text: `${this.$store.state.lang.lang_map.tracking.tracked}`, value: 'tracked'},
                {text: `${this.$store.state.lang.lang_map.tracking.amount}`, value: 'amount'},
                {text: `${this.$store.state.lang.lang_map.tracking.progress}`, value: 'progress'}
            ],
            clients: [],
            products: [],
            project: {
                name: '',
                clientId: null,
                productId: null,
                color: '#000000'
            },
            colorMenu: false
        }
    },
    mounted() {
        this.getClients();
        this.getProducts();
        let self = this;
        EventBus.$on('update-theme-color', function (color) {
            self.themeColor = color;
        });
    },
    methods: {
        getProjects() {
            this.loading = this.themeColor
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id'
                this.options.sortDesc[0] = true
            }
            if (this.totalTickets < this.options.itemsPerPage) {
                this.options.page = 1
            }
            const queryParams = new URLSearchParams({
                per_page: this.footerProps.itemsPerPage,
                page: this.options.page,
                search: this.trackingProjectSearch
            });
            axios.get(`/api/tracking/projects?${queryParams.toString()}`)
                .then(response => {
                    response = response.data
                    this.projects = response.data.data
                    this.totalProjects = response.data.total
                    this.lastPage = response.data.last_page
                    this.loading = false
                });
        },
        getClients() {
            axios.get('/api/tracking/clients')
            .then(({data}) => {
                this.clients = data.success;
                this.loading = false
            });
        },
        getProducts() {
            axios.get('/api/tracking/products')
            .then(({data}) => {
                this.products = data.success;
                this.loading = false
            });
        },
        updateItemsCount(value) {
            this.footerProps.itemsPerPage = value
            this.options.page = 1
        },
        showItem(item) {
            this.$router.push(`/tracking/projects/${item.id}`)
        },
        resetProject() {
            this.project = {
                name: '',
                clientId: null,
                color: '#000000'
            };
            this.dialog = false;
        },
        createProject() {
            axios.post('/api/tracking/projects', this.project)
            .then(response => {
                response = response.data
                if (response.success === true) {
                    this.getProjects();
                    this.resetProject();
                } else {
                    console.log('error')
                }
            });
        }
    },
    watch: {
        options: {
            handler() {
                this.getProjects()
            },
            deep: true,
        }
    },
    computed: {
        switchColor() {
            const { project: { color }, menu } = this
            return {
                backgroundColor: color,
                cursor: 'pointer',
                height: '30px',
                width: '30px',
                borderRadius: menu ? '50%' : '4px',
                transition: 'border-radius 200ms ease-in-out'
            }
        }
    }
}
</script>
