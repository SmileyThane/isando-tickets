<template>
    <v-menu
        :close-on-content-click="false"
        :nudge-width="200"
        offset-y
        v-model="shown"
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
            style="overflow: hidden"
        >
            <v-expansion-panels
                v-model="panels"
            >
                <v-expansion-panel>
                    <v-expansion-panel-header>
                        Choose project
                    </v-expansion-panel-header>
                    <v-expansion-panel-content
                        style="min-height: 380px; max-height: 380px"
                    >
                        <v-text-field
                            label="Project"
                            placeholder="Start typing to Search"
                            autofocus
                            clearable
                            v-model="search"
                            style="max-width: 90%"
                        >
                        </v-text-field>
                        <perfect-scrollbar>
                            <v-treeview
                                :items="$store.getters['Projects/getTreeProjects']"
                                item-children="projects"
                                item-text="name"
                                item-key="id"
                                dense
                                class="text-left"
                                :selected-color="color"
                                activatable
                                hoverable
                                open-on-click
                                return-object
                                style="min-height: 300px; max-height: 300px;"
                                @update:active="selectProject"
                            >
                                <template v-slot:prepend="{ item }">
                                    <v-icon small v-if="item.supplier_type === 'App\\Company'">
                                        mdi-factory
                                    </v-icon>
                                    <v-icon v-else>mdi-folder-account-outline</v-icon>
                                </template>
                                <template v-slot:label="{ item }">
                                    <span v-if="item.projects">
                                        {{ item.name }}
                                    </span>
                                    <span v-else>
                                    {{ item.name }}
                                    <small>({{ item.product.name }})</small>
                                </span>
                                </template>
                            </v-treeview>
                        </perfect-scrollbar>
                    </v-expansion-panel-content>
                </v-expansion-panel>
                <v-expansion-panel>
                    <v-expansion-panel-header>
                        Create new project
                    </v-expansion-panel-header>
                    <v-expansion-panel-content
                        style="min-height: 380px; max-height: 380px;"
                    >
                        <v-list>
                            <v-list-item>
                                <v-text-field
                                    v-model="form.name"
                                    label="Project name"
                                    placeholder="Type the project name here"
                                    clearable
                                    required
                                    full-width
                                ></v-text-field>
                            </v-list-item>
                            <v-list-item>
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
                                    required
                                ></v-autocomplete>
                            </v-list-item>
                            <v-list-item>
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
                                    required
                                ></v-autocomplete>
                            </v-list-item>
                            <v-list-item>
                                <v-text-field
                                    v-model="form.color"
                                    hide-details
                                    class="ma-0 pa-0"
                                    solo
                                    label="Color"
                                    required
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
                                :disabled="!createProjectValid"
                                @click="createNewProject"
                            >
                                Save
                            </v-btn>
                        </v-card-actions>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>
        </v-card>
    </v-menu>
</template>

<style scoped>
>>>.v-treeview--dense .v-treeview-node__root {
    min-height: 1.1em;
}
>>>.v-treeview-node__root .v-icon {
    font-size: 20px;
}
>>>.v-treeview--dense .v-treeview-node__label {
    max-width: 80%;
}
</style>

<style src="vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css"/>

<script>

import _ from 'lodash';
import { PerfectScrollbar } from 'vue2-perfect-scrollbar';
import * as Helper from "../helper";

export default {
    components: {
        PerfectScrollbar
    },
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
            shown: false,
            panels: 0,
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
                color: Helper.genRandomColor()
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
            this.$store.dispatch('Projects/getProjectList', { search: this.search });
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
                color: Helper.genRandomColor()
            };
            this.panels = 0;
        },
        createNewProject() {
            if (this.createProjectValid) {
                this.menu = false;
                this.$store.dispatch('Projects/createProject', this.form)
                    .then(project => (this.selectedProject = project));
                this.resetNewProjectForm();
            }
        },
        selectProject(project) {
            this.selectedProject = project.shift();
            this.shown = false;
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
        },
        createProjectValid() {
            return this.form.name && this.form.product && this.form.client && this.form.color;
        }
    },
    watch: {
        search() {
            this.debounceGetProjects();
        }
    }
};
</script>
