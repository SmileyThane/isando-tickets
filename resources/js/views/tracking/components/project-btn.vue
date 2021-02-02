<template>
    <v-menu
        :close-on-content-click="false"
        :nudge-width="200"
        offset-x
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                tile
                small
                text
                :color="color"
                v-bind="attrs"
                v-on="on"
            >
                <span v-if="!selectedProject">
                    <v-icon>
                        mdi-plus-circle-outline
                    </v-icon>
                    &nbsp;&nbsp;Project
                </span>
                <span v-if="selectedProject">
                    {{ selectedProject.name }}
                </span>
            </v-btn>
        </template>
        <v-card
            max-width="400"
            class="d-flex pa-2"
        >
            <v-row>
                <v-col
                    cols="12"
                    class="mx-auto text-center"
                    max-width="80%"
                >
                    <template>
                        <v-autocomplete
                            v-bind="$attrs"
                            v-on="$listeners"
                            v-model="selectedProject"
                            :hide-no-data="!search"
                            :search-input.sync="search"
                            hide-selected
                            label="Project"
                            placeholder="Start typing to Search"
                            return-object
                            item-text="name"
                            item-id="id"
                            :items="$store.getters['Projects/getProjects']"
                            autofocus
                            clearable
                        >
                            <template v-slot:selection="{ attrs, item, parent, selected }">
                                {{ item.name }}
                            </template>
                            <template v-slot:item="{ parent, item, on, attrs }">
                                {{ item.name }}
                            </template>
                        </v-autocomplete>
                    </template>
                </v-col>
                <v-col cols="4" class="pt-2"><v-divider></v-divider></v-col>
                <v-col cols="4" class="pt-0 text-center">OR</v-col>
                <v-col cols="4" class="pt-2"><v-divider></v-divider></v-col>
                <v-col cols="12" class="text-center">
                    <v-menu
                        v-model="menu"
                        :close-on-content-click="false"
                        :nudge-width="100"
                        offset-y
                    >
                        <template v-slot:activator="{ attrs, on }">
                            <v-btn
                                :color="color"
                                v-bind="attrs"
                                v-on="on"
                                small
                                style="color: white"
                            >
                                <v-icon>
                                    mdi-plus
                                </v-icon> Create new project
                            </v-btn>
                        </template>
                        <v-card max-width="100%">
                            <v-list>
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-list-item-title>Create new project</v-list-item-title>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                            <v-divider></v-divider>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-action>
                                        <v-text-field
                                            v-model="form.name"
                                            label="Project name"
                                            placeholder="Type the project name here"
                                            clearable
                                        ></v-text-field>
                                    </v-list-item-action>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-action>
                                        <v-autocomplete
                                            v-model="form.product"
                                            :items="getFilteredProducts"
                                            :loading="isLoadingSearchProduct"
                                            :search-input.sync="searchProduct"
                                            color="white"
                                            item-text="name"
                                            item-value="id"
                                            label="Product"
                                            placeholder="Start typing to Search"
                                            return-object
                                            clearable
                                        ></v-autocomplete>
                                    </v-list-item-action>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-action>
                                        <v-autocomplete
                                            v-model="form.client"
                                            :items="getFilteredClients"
                                            :loading="isLoadingSearchClient"
                                            :search-input.sync="searchClient"
                                            color="white"
                                            item-text="name"
                                            item-value="id"
                                            label="Client"
                                            placeholder="Start typing to Search"
                                            return-object
                                            clearable
                                        ></v-autocomplete>
                                    </v-list-item-action>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-action>
                                        <v-text-field
                                            v-model="form.color"
                                            hide-details
                                            class="ma-0 pa-0"
                                            solo
                                            label="Color"
                                        >
                                            <template v-slot:append>
                                                <v-menu v-model="colorMenu" top nudge-bottom="105" nudge-left="16" :close-on-content-click="false">
                                                    <template v-slot:activator="{ on }">
                                                        <div :style="switchColor" v-on="on" />
                                                    </template>
                                                    <v-card>
                                                        <v-card-text class="pa-0">
                                                            <v-color-picker v-model="form.color" flat />
                                                        </v-card-text>
                                                    </v-card>
                                                </v-menu>
                                            </template>
                                        </v-text-field>
                                    </v-list-item-action>
                                </v-list-item>
                            </v-list>
                            <v-card-actions>
                                <v-spacer></v-spacer>

                                <v-btn
                                    color="error"
                                    text
                                    @click="resetNewProjectForm(); menu = false"
                                >
                                    Cancel
                                </v-btn>
                                <v-btn
                                    color="success"
                                    text
                                    @click="createNewProject(); menu = false"
                                >
                                    Save
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-menu>
                </v-col>
            </v-row>

        </v-card>
    </v-menu>
</template>

<script>

import _ from 'lodash';

export default {
    props: {
        color: {
            type: String,
            default: '#ffffff'
        },
        onChoosable: {
            type: Function
        },
        value: {
            required: true
        }
    },
    data: () => {
        return {
            menu: false,
            search: '',
            isLoadingProject: false,
            isLoadingSearchProduct: false,
            isLoadingSearchClient: false,
            nameLimit: 20,
            isCreatingProject: false,
            searchProduct: null,
            searchClient: null,
            colorMenu: false,
            form: {
                name: '',
                product: null,
                client: null,
                color: '#000000'
            }
        };
    },
    created() {
        this.debounceGetProjects = _.debounce(this.__getProjects, 1000);
        this.debounceGetProducts = _.debounce(this.__getProducts, 1000);
        this.debounceGetClients = _.debounce(this.__getClients, 1000);
    },
    mounted() {
        // this.debounceGetProducts();
        // this.debounceGetProjects()
    },
    methods: {
        __getProjects() {
            this.$store.dispatch('Projects/getProjectList');
        },
        __getProducts() {
            this.$store.dispatch('Products/getProductList', { search: this.searchProduct });
        },
        __getClients() {
            this.$store.dispatch('Clients/getClientList', { search: this.searchClient });
        },
        resetNewProjectForm() {
            this.form = {
                name: '',
                product: null,
                client: null,
                color: '#000000'
            };
        },
        createNewProject() {
            this.$store.dispatch('Projects/createProject', this.form);
            this.resetNewProjectForm();
        }
    },
    computed: {
        selectedProject: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            }
        },
        getFilteredProducts() {
            return this.$store.getters["Products/getProducts"].map(entry => {
                const name = entry.name.length > this.nameLimit
                    ? entry.name.slice(0, this.nameLimit) + '...'
                    : entry.name

                return Object.assign({}, entry, { name })
            })
        },
        getFilteredClients() {
            return this.$store.getters['Clients/getClients'].map(entry => {
                const name = entry.name.length > this.nameLimit
                    ? entry.name.slice(0, this.nameLimit) + '...'
                    : entry.name

                return Object.assign({}, entry, { name })
            })
        },
        switchColor() {
            const { form: { color }, colorMenu } = this
            return {
                backgroundColor: color,
                cursor: 'pointer',
                height: '30px',
                width: '30px',
                borderRadius: colorMenu ? '50%' : '4px',
                transition: 'border-radius 200ms ease-in-out'
            }
        }
    },
    watch: {

    }
};
</script>
