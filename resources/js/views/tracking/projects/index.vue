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
            v-if="$helpers.auth.checkPermissionByIds([54,55,56])"
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
                            :color="themeBgColor"
                            :label="langMap.main.search"
                            class="mx-4"
                            clearable
                        ></v-text-field>
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
                    <v-col sm="12" md="2" class="pt-6">
                        <v-btn
                            v-if="$helpers.auth.checkPermissionByIds([53])"
                            style="color: white;"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            elevation="2"
                            @click="dialog=true"
                        >{{ langMap.tracking.create_project.btn_title }}</v-btn>
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
            <template v-slot:item.name="props">
                {{ props.item.name }} <v-chip small v-if="props.item.status === 'archived'">Archived</v-chip>
            </template>
            <template v-slot:item.is_favorite="props">
                <v-icon v-if="props.item.is_favorite" @click.stop="toggleFavorite(props.item)">mdi-star</v-icon>
                <v-icon v-else @click.stop="toggleFavorite(props.item)">mdi-star-outline</v-icon>
            </template>
            <template v-slot:item.tracked="props">
                {{ $helpers.time.convertSecToTime(props.item.tracked, false) }}
            </template>
            <template v-slot:item.revenue="props">
                <span v-if="currentCurrency">
                    {{ currentCurrency.slug }}
                </span> {{ props.item.revenue }}
            </template>
            <template v-slot:item.actions="props">
                <v-btn
                    v-if="$helpers.auth.checkPermissionByIds([58]) && props.item.status==='archived'"
                    icon
                    @click.stop="toggleArchive(props.item)"
                >
                    <v-icon>mdi-archive-arrow-up-outline</v-icon>
                </v-btn>
                <v-btn
                    v-else-if="$helpers.auth.checkPermissionByIds([58])"
                    icon
                    @click.stop="toggleArchive(props.item)"
                >
                    <v-icon>mdi-archive-arrow-down-outline</v-icon>
                </v-btn>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
                <td :colspan="headers.length">
                    {{ langMap.tracking.create_project.more_info }} {{ item.name }}
                </td>
            </template>
        </v-data-table>

        <template>
            <v-row justify="center">
                <v-dialog
                    v-if="$helpers.auth.checkPermissionByIds([53])"
                    v-model="dialog"
                    persistent
                    max-width="520"
                >
                    <v-card v-if="$helpers.auth.checkPermissionByIds([53])">
                        <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                            {{ langMap.tracking.create_project.modal_title }}
                        </v-card-title>
                        <v-card-text>
                            <v-text-field
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                label="Project name*"
                                v-model="project.project"
                            ></v-text-field>
                            <v-text-field
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                label="Department*"
                                v-model="project.department"
                            ></v-text-field>
                            <v-text-field
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                label="Profit center*"
                                v-model="project.profit_center"
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
                                :label="langMap.tracking.create_project.client + '*'"
                                v-model="project.clientId"
                            ></v-select>
                            <v-select
                                :items="getTeams"
                                item-text="name"
                                item-value="id"
                                :label="langMap.tracking.create_project.team"
                                v-model="project.teamId"
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
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
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
                itemsPerPage: this.$helpers.localStorage.getKey('itemsPerPage', 'tracking') ?? 10
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            headers: [
                {text: `${this.$store.state.lang.lang_map.tracking.name}`, value: 'name'},
                {text: `${this.$store.state.lang.lang_map.tracking.client}`, value: 'client.name'},
                {text: `${this.$store.state.lang.lang_map.tracking.team}`, value: 'team.name'},
                {text: ``, value: 'is_favorite'},
                {text: `${this.$store.state.lang.lang_map.tracking.tracked}`, value: 'tracked'},
                {text: `${this.$store.state.lang.lang_map.tracking.revenue}`, value: 'revenue'},
                {text: ``, value: 'actions'}
            ],
            project: {
                project: '',
                department: '',
                profit_center: '',
                clientId: null,
                client: null,
                teamId: null,
                team: null,
                productId: null,
                product: null,
                color: this.$helpers.color.genRandomColor()
            },
            colorMenu: false
        }
    },
    created() {
        this.debounceGetProjects = _.debounce(this.__getProjects, 1000);
        this.debounceGetProducts = _.debounce(this.__getProducts, 1000);
        this.debounceGetClients = _.debounce(this.__getClients, 1000);
        this.debounceGetSettings = _.debounce(this.__getSettings, 1000);
        this.debounceGetTeams = _.debounce(this.__getTeams, 1000);
        this.options.itemsPerPage = this.$helpers.localStorage.getKey('itemsPerPage', 'tracking') ?? 10;
        this.footerProps.itemsPerPage = this.$helpers.localStorage.getKey('itemsPerPage', 'tracking') ?? 10;
    },
    mounted() {
        this.debounceGetProducts();
        this.debounceGetClients();
        this.debounceGetSettings();
        this.debounceGetTeams();
        let self = this;
        EventBus.$on('update-theme-color', function (color) {
            self.themeBgColor = color;
        });
        this.options.itemsPerPage = this.$helpers.localStorage.getKey('itemsPerPage', 'tracking');
        this.footerProps.itemsPerPage = this.$helpers.localStorage.getKey('itemsPerPage', 'tracking');
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
                per_page: this.footerProps.itemsPerPage ?? 10,
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
        __getTeams() {
            this.$store.dispatch('Team/getTeams', {
                search: null,
                sort_by: 'id',
                sort_val: false,
                per_page: 500,
                page: 1,
            });
        },
        __getProducts() {
            this.$store.dispatch('Products/getProductList', {});
        },
        __getSettings() {
            this.$store.dispatch('Tracking/getSettings');
        },
        updateItemsCount(value) {
            this.footerProps.itemsPerPage = value
            this.$helpers.localStorage.storeKey('itemsPerPage', value, 'tracking');
            this.options.page = 1
        },
        showItem(item) {
            if (this.$helpers.auth.checkPermissionByIds([56])) {
                this.$router.push(`/tracking/projects/${item.id}`)
            }
        },
        resetProject() {
            this.project = {
                project: '',
                department: '',
                profit_center: '',
                clientId: null,
                teamId: null,
                color: '#000000'
            };
            this.dialog = false;
        },
        createProject() {
            this.$store.dispatch('Projects/createProject', this.project);
            this.resetProject();
        },
        toggleFavorite(project) {
            this.$store.dispatch('Projects/toggleFavorite', project);
        },
        toggleArchive(project) {
            this.$store.dispatch('Projects/toggleArchive', project);
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
        },
        teamId: function () {
            const index = this.$store.getters['Team/getTeams'].findIndex(i => i.id === this.teamId);
            this.team = this.$store.getters['Team/getTeams'][index];
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
        },
        currentCurrency() {
            const settings = this.$store.getters['Tracking/getSettings'];
            return settings.currency ?? null;
        },
        getTeams() {
            if (this.$store.getters['Team/getTeams'] && this.$store.getters['Team/getTeams'].data) {
                return this.$store.getters['Team/getTeams'].data;
            }
            return [];
        },
    }
}
</script>
