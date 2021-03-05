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
        <template>
            <v-tabs
                v-model="tab"
                :color="themeBgColor"
            >
                <v-tab>{{ langMap.tracking.create_project.status }}</v-tab>
                <v-tab>{{ langMap.tracking.create_project.settings }}</v-tab>

                <v-tabs-items
                    v-model="tab"
                >
                    <v-tab-item>
                        <v-alert
                            outlined
                            type="info"
                        >
                            Coming soon
                        </v-alert>
                    </v-tab-item>
                    <v-tab-item>
                        <v-container fluid>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Name</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <v-text-field
                                        v-model="project.name"
                                        @blur="actionSave()"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Product</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <template>
                                        <v-autocomplete
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
                                    </template>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Color</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <v-text-field
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
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Billable by default</v-subheader>
                                </v-col>
                                <v-col cols="10" lg="4" md="6">
                                    <v-switch
                                        v-model="project.billable_by_default"
                                        @change="actionSave()"
                                    ></v-switch>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="2" lg="1">
                                    <v-subheader class="float-right">Project billable rate</v-subheader>
                                </v-col>
                                <v-col cols="2">
                                    <v-text-field
                                        full-width
                                        type="number"
                                        v-model="project.rate"
                                        @blur="actionSave()"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="2">
                                    <v-dialog
                                        v-model="rateDialog"
                                        width="500"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
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
                                                            v-model="rateFrom"
                                                        >
                                                            <v-radio
                                                                name="rate_from"
                                                                label="Time entries from"
                                                                value="1"
                                                            ></v-radio>
                                                            <v-radio
                                                                name="rate_from"
                                                                label="All past and future time entries"
                                                                value="2"
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
                                                                    v-model="project.rate_from_date"
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
                                                    :color="themeBgColor"
                                                    text
                                                    @click="rateDialog = false"
                                                >
                                                    Cancel
                                                </v-btn>
                                                <v-btn
                                                    color="primary"
                                                    text
                                                    @click="rateDialog = false"
                                                >
                                                    Save
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </v-col>
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
                rate_from_date: moment()
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
    },
    methods: {
        __getProject(projectId) {
            axios.get(`/api/tracking/projects/${projectId}`)
            .then(({data}) => {
                if (data.success) {
                    this.project = data.data;
                }
            });
        },
        __getProducts() {
            if (this.isLoadingSearchProduct) return;
            this.isLoadingSearchProduct = true;
            const queryParams = new URLSearchParams({
                search: this.searchProduct ?? ''
            });
            axios.get(`/api/tracking/products?${queryParams.toString()}`)
                .then(({data}) => {
                    this.products = data.data;
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
                    this.clients = data.data;
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
        actionSave() {
            this.__updateProjectById(this.project.id, this.project);
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
        }
    }
}
</script>
