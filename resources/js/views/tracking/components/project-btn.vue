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
            style="max-width: 400px"
        >
            <template>
                <v-combobox
                    v-bind="$attrs"
                    v-on="$listeners"
                    v-model="selectedProject"
                    :hide-no-data="!search"
                    :search-input.sync="search"
                    hide-selected
                    label="Choose project"
                    return-object
                    item-text="name"
                    item-id="id"
                    solo
                    autofocus
                    clearable
                    @input="onUpdate"
                >
                    <template v-slot:no-data>
                        <v-list-item>
                            <span class="subheading">Create&nbsp;</span>
                            "{{ search }}"
                        </v-list-item>
                    </template>
                    <template v-slot:selection="{ attrs, item, parent, selected }">
                        <span v-if="item.id">
                            {{ item.name }}
                        </span>
                        <v-progress-circular
                            v-if="!item.id"
                            indeterminate
                            :size="20"
                            :color="color"
                        ></v-progress-circular>
                    </template>
                    <template v-slot:item="{ parent, item, on, attrs }">
                        {{ item.name }}
                    </template>
                </v-combobox>
            </template>
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
            search: '',
            isLoadingProject: false,
            nameLimit: 20,
            projects: [],
            isCreatingProject: false
        };
    },
    created() {
        this.debounceGetProjects = _.debounce(this.__getProjects, 1000);
    },
    mounted() {
        // this.debounceGetProjects()
    },
    methods: {
        __getProjects() {
            if (this.isLoadingProject) return;
            this.isLoadingProject = true;
            return axios.get(`/api/tracking/projects`)
                .then(({ data }) => {
                    if (data.success) {
                        this.projects = data.data.data;
                        return data.data.data;
                    }
                    return null;
                })
                .catch(e => {
                    console.log(e);
                    this.debounceGetProjects();
                })
            .finally(() => ( this.isLoadingProject = false ))
        },
        __createProject(name) {
            if (typeof name !== 'string') return;
            return axios.post('/api/tracking/projects', {name})
                .then(({ data }) => {
                    if (data.success) {
                        const createdProject = data.data;
                        if (typeof this.selectedProject === 'string' && this.selectedProject === createdProject.name) {
                            this.selectedProject = createdProject;
                        }
                        this.debounceGetProjects();
                        return createdProject;
                    }
                    return null;
                })
                .catch(e => ( console.log(e) ));
        },
        onUpdate(item) {
            if (typeof item === 'string') {
                this.__createProject(item);
                this.search = '';
            }
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
        }
    },
    watch: {

    }
};
</script>
