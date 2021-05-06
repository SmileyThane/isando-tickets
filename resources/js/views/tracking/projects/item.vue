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

        <br>
        <template>
            <v-card
                elevation="2"
                outlined
                shaped
            >
                <v-list-item three-line>
                    <v-list-item-content>
                        <div class="overline mb-4">
                            {{ langMap.tracking.create_project.project }}
                        </div>
                        <v-list-item-title class="headline mb-1">
                            <span v-if="project">
                                {{ project.name }}
                            </span>
                        </v-list-item-title>
                        <v-list-item-subtitle v-if="project.client">
                            {{project.client.name}}
                        </v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
            </v-card>
        </template>
        <br>
        <template v-if="$helpers.auth.checkPermissionByIds([56])">
            <v-tabs
                v-model="tab"
                :color="themeBgColor"
            >
<!--                <v-tab>{{ langMap.tracking.create_project.status }}</v-tab>-->
                <v-tab>{{ langMap.tracking.create_project.settings }}</v-tab>

                <v-tabs-items
                    v-model="tab"
                >
<!--                    <v-tab-item>-->
<!--                        <v-alert-->
<!--                            outlined-->
<!--                            type="info"-->
<!--                        >-->
<!--                            Coming soon-->
<!--                        </v-alert>-->
<!--                    </v-tab-item>-->
                    <v-tab-item>
                        <v-container fluid>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Name</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <v-text-field
                                        v-if="$helpers.auth.checkPermissionByIds([57])"
                                        v-model="project.name"
                                        @blur="actionSave()"
                                    ></v-text-field>
                                    <div class="mt-3" v-else>{{project.name}}</div>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Product</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <template>
                                        <v-autocomplete
                                            v-if="$helpers.auth.checkPermissionByIds([57])"
                                            v-model="project.product"
                                            :items="getFilteredProducts"
                                            :loading="isLoadingSearchProduct"
                                            :search-input.sync="searchProduct"
                                            color="white"
                                            item-text="name"
                                            item-value="id"
                                            placeholder="Start typing to Search"
                                            return-object
                                            @change="actionSave()"
                                        ></v-autocomplete>
                                        <div class="mt-3" v-else>{{project.product.name}}</div>
                                    </template>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Client</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <template>
                                        <v-autocomplete
                                            v-if="$helpers.auth.checkPermissionByIds([57])"
                                            v-model="project.client"
                                            :items="getFilteredClients"
                                            :loading="isLoadingSearchClient"
                                            :search-input.sync="searchClient"
                                            color="white"
                                            item-text="name"
                                            item-value="id"
                                            placeholder="Start typing to Search"
                                            return-object
                                            @change="actionSave()"
                                        ></v-autocomplete>
                                        <div class="mt-3" v-else>{{project.client.name}}</div>
                                    </template>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Color</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <v-text-field
                                        v-if="$helpers.auth.checkPermissionByIds([57])"
                                        v-model="project.color"
                                        hide-details
                                        class="ma-0 pa-0"
                                        solo
                                        @blur="actionSave()"
                                    >
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
                                    <span
                                        v-else
                                        class="mt-3 d-inline-flex"
                                        style="width: 30px; height: 30px"
                                        :style="{ 'background-color': project.color }"
                                    ></span>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="2" lg="1" class="text-right">
                                    <v-subheader class="float-right">Billable by default</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <v-switch
                                        :disabled="!$helpers.auth.checkPermissionByIds([57])"
                                        v-model="project.billable_by_default"
                                        @change="actionSave()"
                                    ></v-switch>
                                </v-col>
                            </v-row>
                            <v-row class="d-flex flex-row" v-if="$helpers.auth.checkPermissionByIds([59])">
                                <v-col cols="2" lg="1" class="text-right">
                                    <v-subheader class="float-right">Project billable rate</v-subheader>
                                </v-col>
                                <div class="d-inline-flex">
                                    <div class="d-flex flex-row">
<!--                                        rank block-->
                                        <div class="d-inline-flex">
                                            <div class="d-flex flex-column mt-6">
                                                <div class="d-inline-flex mx-2">
                                                    <span
                                                        class=""
                                                        v-if="currentCurrency"
                                                    >
                                                        {{currentCurrency.slug}}
                                                    </span>
                                                    <span class="mx-2">{{ project.rate }}</span>
                                                </div>
                                                <v-divider></v-divider>
                                                <div class="d-inline-flex mt-2 mx-2" v-if="project.rate_from && project.rate_from_date">
                                                    <span
                                                        class=""
                                                        v-if="currentCurrency"
                                                    >
                                                        {{currentCurrency.slug}}
                                                    </span>
                                                    <span class="mx-2">{{ project.rate_from }}</span>
                                                </div>
                                            </div>
                                        </div>
<!--                                        from/to block-->
                                        <div class="d-inline-flex flex-grow-1">
                                            <div class="d-flex flex-column">
                                                <div class="d-inline-flex mx-3">
                                                    <div class="d-flex flex-column"><div class="d-inline-flex" style="min-width: 100px">From</div></div>
                                                    <div class="d-flex flex-column"><div class="d-inline-flex" style="min-width: 100px">To</div></div>
                                                </div>
                                                <div class="d-inline-flex mx-3">
                                                    <div class="d-flex flex-column">
                                                        <div class="d-inline-flex" style="min-width: 100px">The start</div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <div class="d-inline-flex" style="min-width: 100px">
                                                            <span v-if="project.rate_from && project.rate_from_date">{{ moment(project.rate_from_date).subtract(1, 'days').format('DD/MM/YYYY') }}</span>
                                                            <span v-else>Current date</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <v-divider></v-divider>
                                                <div class="d-inline-flex mx-3 mt-2" v-if="project.rate_from_date">
                                                    <div class="d-flex flex-column">
                                                        <div class="d-inline-flex" style="min-width: 100px">
                                                            <span v-if="project.rate_from && project.rate_from_date">{{ moment(project.rate_from_date).format('DD/MM/YYYY') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <div class="d-inline-flex" style="min-width: 100px">
                                                            <span v-if="project.rate_from && moment(project.rate_from_date).isBefore(moment.now())">Current date</span>
                                                            <span v-else-if="project.rate_from">Future dates</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-inline-flex">
                                    <v-dialog
                                        v-if="$helpers.auth.checkPermissionByIds([60])"
                                        v-model="rateDialog"
                                        width="500"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                v-if="$helpers.auth.checkPermissionByIds([60])"
                                                class="ma-2"
                                                outlined
                                                :color="themeBgColor"
                                                v-bind="attrs"
                                                v-on="on"
                                            >
                                                Set rate
                                            </v-btn>
                                        </template>
                                        <v-card>
                                            <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                                                Set rate
                                            </v-card-title>

                                            <v-card-text>

                                                <v-row>
                                                    <v-col cols="12">
                                                        <v-text-field
                                                            v-model="project.rate_from"
                                                            label="What is the new billable rate"
                                                            type="number"
                                                        ></v-text-field>
                                                    </v-col>
                                                </v-row>
                                                <v-row>
                                                    <v-col cols="6">
                                                        <v-radio-group
                                                            v-model="project.rate_type_from"
                                                        >
                                                            <v-radio
                                                                name="rate_from"
                                                                label="Time entries from"
                                                                :value="1"
                                                            ></v-radio>
                                                            <v-radio
                                                                name="rate_from"
                                                                label="All past and future time entries"
                                                                :value="2"
                                                            ></v-radio>
                                                        </v-radio-group>
                                                    </v-col>
                                                    <v-col cols="6">
                                                        <v-menu
                                                            v-model="rateFromMenu"
                                                            :close-on-content-click="false"
                                                            :nudge-right="40"
                                                            transition="scale-transition"
                                                            offset-y
                                                            min-width="auto"
                                                        >
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-text-field
                                                                    v-model="formattedDateFrom"
                                                                    label="Picker without buttons"
                                                                    prepend-icon="mdi-calendar"
                                                                    readonly
                                                                    v-bind="attrs"
                                                                    v-on="on"
                                                                >
                                                                    <template v-slot:append>
                                                                        onwards
                                                                    </template>
                                                                </v-text-field>
                                                            </template>
                                                            <v-date-picker
                                                                :min="moment().add(1, 'days').format('YYYY-MM-DD')"
                                                                v-model="project.rate_from_date"
                                                                @input="rateFromMenu = false"
                                                            ></v-date-picker>
                                                        </v-menu>
                                                    </v-col>
                                                </v-row>

                                            </v-card-text>

                                            <v-divider></v-divider>

                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn
                                                    color="red"
                                                    text
                                                    @click="resetProject(); rateDialog = false"
                                                >
                                                    Cancel
                                                </v-btn>
                                                <v-btn
                                                    color="success"
                                                    text
                                                    @click="actionSave(); rateDialog = false"
                                                >
                                                    Save
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </div>
                            </v-row>
                        </v-container>
                    </v-tab-item>
                </v-tabs-items>
            </v-tabs>
        </template>

    </v-container>
</template>

<script>
import EventBus from "../../../components/EventBus";
import moment from "moment";
import _ from 'lodash';

export default {
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            tab: 1,
            project: {
                id: null,
                name: '',
                color: '',
                client: {
                    id: null,
                    name: ''
                },
                product: {
                    id: null,
                    name: ''
                },
                billable_by_default: false,
                rate: null,
                rate_from: null,
                rate_from_date: moment().add(1, 'days').format('YYYY-MM-DD'),
                rate_type_from: 1
            },
            colorMenu: false,
            rateDialog: false,
            rateFrom: '1',
            rateFromMenu: false,
            products: [],
            isLoadingSearchProduct: false,
            searchProduct: null,
            clients: [],
            isLoadingSearchClient: false,
            searchClient: null,
            nameLimit: 255
        }
    },
    created () {
        this.debounceGetSettings = _.debounce(this.__getSettings, 1000);
    },
    mounted() {
        this.__getProducts();
        this.__getClients();
        this.__getProject(this.$route.params.id);
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.debounceGetSettings();
    },
    methods: {
        __getProject(projectId) {
            axios.get(`/api/tracking/projects/${projectId}`)
            .then(({data}) => {
                if (data.success) {
                    if (!data.data.rate_from_date) data.data.rate_from_date = moment().add(1, 'days').format('YYYY-MM-DD');
                    this.project = {...data.data, rate_type_from: 1};
                }
            });
        },
        __getProducts() {
            if (this.isLoadingSearchProduct) return;
            this.isLoadingSearchProduct = true;
            const queryParams = new URLSearchParams({
                search: this.searchProduct ?? ''
            });
            axios.get(`/api/product?${queryParams.toString()}`)
                .then(({data}) => {
                    this.products = data.data.data;
                })
                .finally(() => (this.isLoadingSearchProduct = false));
        },
        __getClients() {
            if (this.isLoadingSearchClient) return;
            this.isLoadingSearchClient = true;
            const queryParams = new URLSearchParams({
                search: this.searchClient ?? ''
            });
            axios.get(`/api/tracking/clients?${queryParams.toString()}`)
                .then(({data}) => {
                    this.clients = data.data.data;
                })
                .finally(() => (this.isLoadingSearchClient = false));
        },
        __updateProjectById(projectId, data) {
            axios.patch(`/api/tracking/projects/${projectId}`, data)
                .then(({ data }) => {
                    if (data.success) {
                        this.__getProject(projectId);
                    }
                });
        },
        __getSettings() {
            this.$store.dispatch('Tracking/getSettings');
        },
        actionSave() {
            this.__updateProjectById(this.project.id, this.project);
        },
        resetProject() {
            this.__getProject(this.project.id);
        }
    },
    watch: {
        searchProduct () {
            this.__getProducts();
        },
        searchClient () {
            this.__getClients();
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
        getFilteredProducts() {
            return this.products.map(entry => {
                const name = entry.name.length > this.nameLimit
                    ? entry.name.slice(0, this.nameLimit) + '...'
                    : entry.name

                return Object.assign({}, entry, { name })
            })
        },
        getFilteredClients() {
            return this.clients.map(entry => {
                const name = entry.name.length > this.nameLimit
                    ? entry.name.slice(0, this.nameLimit) + '...'
                    : entry.name

                return Object.assign({}, entry, { name })
            })
        },
        currentCurrency () {
            const settings = this.$store.getters['Tracking/getSettings'];
            return settings.currency ?? null;
        },
        formattedDateFrom() {
            if (this.project.rate_from_date)
                return moment(this.project.rate_from_date).format('YYYY-MM-DD');
            return moment().add(1, 'days').format('YYYY-MM-DD');
        }

    }
}
</script>
