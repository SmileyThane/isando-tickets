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
            :items="$store.getters['Projects/getProjects']"
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
                        <v-text-field
                            @input="debounceGetProjects"
                            v-model="trackingProjectSearch"
                            :color="themeColor"
                            :label="langMap.main.search"
                            class="mx-4"
                            clearable
                        ></v-text-field>
                    </v-col>
                    <v-col sm="12" md="2">
                        <v-select
                            class="mx-4"
                            :color="themeColor"
                            :item-color="themeColor"
                            :items="footerProps.itemsPerPageOptions"
                            :label="langMap.main.items_per_page"
                            v-model="options.itemsPerPage"
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
                                :items="$store.getters['Products/getProducts']"
                                item-text="name"
                                item-value="id"
                                :label="langMap.tracking.create_project.product"
                                v-model="project.productId"
                            ></v-select>
                            <v-select
                                :items="$store.getters['Clients/getClients']"
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
                                color="red darken-1"
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
import _ from "lodash";

export default {
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeColor: this.$store.state.themeColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            trackingProjectSearch: '',
            totalProjects: 0,
            lastPage: 0,
            loading: false,
            dialog: false,
            options: {
                page: 1,
                sortDesc: [true],
                sortBy: ['id'],
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            headers: [
                {text: `${this.$store.state.lang.lang_map.tracking.name}`, value: 'name'},
                {text: `${this.$store.state.lang.lang_map.tracking.client}`, value: 'client.name'},
                {text: `${this.$store.state.lang.lang_map.tracking.tracked}`, value: 'tracked'},
                {text: `${this.$store.state.lang.lang_map.tracking.amount}`, value: 'amount'},
                {text: `${this.$store.state.lang.lang_map.tracking.progress}`, value: 'progress'}
            ],
            project: {
                name: '',
                clientId: null,
                client: null,
                productId: null,
                product: null,
                color: '#' + Math.floor(Math.random()*16777215).toString(16).substr(0, 6)
            },
            colorMenu: false
        }
    },
    created() {
        this.debounceGetProjects = _.debounce(this.__getProjects, 1000);
        this.debounceGetProducts = _.debounce(this.__getProducts, 1000);
        this.debounceGetClients = _.debounce(this.__getClients, 1000);
    },
    mounted() {
        this.debounceGetProducts();
        this.debounceGetClients();
        let self = this;
        EventBus.$on('update-theme-color', function (color) {
            self.themeColor = color;
        });
    },
    methods: {
        __getProjects() {
            this.loading = true;
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id'
                this.options.sortDesc[0] = true
            }
            if (this.totalTickets < this.options.itemsPerPage) {
                this.options.page = 1
            }
            this.$store.dispatch('Projects/getProjectList', {
                per_page: this.footerProps.itemsPerPage,
                page: this.options.page,
                search: this.trackingProjectSearch,
                direction: this.options.sortDesc[0],
                column: this.options.sortBy[0]
            })
                .then(({ data, total, last_page }) => {
                    this.projects = data
                    this.totalProjects = total
                    this.lastPage = last_page
                    this.loading = false
                });
        },
        __getClients() {
            this.$store.dispatch('Clients/getClientList', {});
        },
        __getProducts() {
            this.$store.dispatch('Products/getProductList', {});
        },
        updateItemsCount(value) {
            this.footerProps.itemsPerPage = value
            localStorage.itemsPerPage = value;
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
            this.$store.dispatch('Projects/createProject', this.project);
            this.resetProject();
        }
    },
    watch: {
        footerProps: {
            handler() {
                this.debounceGetProjects();
            },
            deep: true,
        },
        options: {
            handler() {
                this.debounceGetProjects();
            },
            deep: true,
        },
        productId: function () {
            const index = this.$store.getters['Products/getProducts'].findIndex(i => i.id === this.productId);
            this.product = this.$store.getters['Products/getProducts'][index];
        },
        clientId: function () {
            const index = this.$store.getters['Clients/getClients'].findIndex(i => i.id === this.clientId);
            this.client = this.$store.getters['Clients/getClients'][index];
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
