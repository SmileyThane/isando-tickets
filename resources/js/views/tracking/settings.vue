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

        <template>
            <v-tabs v-model="tab">
                <v-tab :key="1">{{ langMap.tracking.settings.general }}</v-tab>
                <v-tab :key="2">{{ langMap.tracking.settings.services }}</v-tab>
            </v-tabs>

            <v-tabs-items v-model="tab">
                <v-tab-item :key="1">
                    <v-card flat>
                        <v-card-text>
                            <div class="d-flex flex-column">
                                <div class="d-inline-flex">
                                    <!--Column 1-->
                                    <v-checkbox
                                        v-model="enableTimesheet"
                                        :label="langMap.tracking.settings.enable_timesheet"
                                    ></v-checkbox>
                                </div>
                                <div class="d-inline-flex">
                                    <!--Column 2-->
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-tab-item>

                <v-tab-item :key="2">
                    <v-card flat>
                        <v-toolbar flat>
                            <v-dialog
                                v-model="dialogServices"
                                width="500"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        :color="themeBgColor"
                                        style="color: white"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        {{ langMap.tracking.settings.create_service }}
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                                        {{ langMap.tracking.settings.create_service_title }}
                                    </v-card-title>

                                    <v-card-text>
                                        <v-text-field
                                            :label="langMap.tracking.settings.name"
                                            v-model="forms.services.name"
                                            required
                                        ></v-text-field>
                                    </v-card-text>

                                    <v-divider></v-divider>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            color="error"
                                            text
                                            @click="resetForm(); dialogServices = false"
                                        >
                                            {{ langMap.tracking.settings.cancel }}
                                        </v-btn>
                                        <v-btn
                                            color="success"
                                            text
                                            @click="createService(); dialogServices = false"
                                        >
                                            {{ langMap.tracking.settings.create }}
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </v-toolbar>
                        <v-card-text>
                            <v-data-table
                                dense
                                :headers="headers.services"
                                :items="$store.getters['Services/getServices']"
                                :items-per-page="15"
                                class="elevation-1"
                            >
                                <template v-slot:item.name="props">
                                    <v-edit-dialog
                                        @save="saveService(props.item)"
                                        @cancel="saveService(props.item)"
                                        @open="saveService(props.item)"
                                        @close="saveService(props.item)"
                                    >
                                        {{ props.item.name }}
                                        <template v-slot:input>
                                            <v-text-field
                                                v-model="props.item.name"
                                                :label="langMap.tracking.settings.name"
                                                :hint="langMap.tracking.settings.name"
                                                single-line
                                                counter
                                            ></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </template>
                                <template v-slot:item.actions="props">
                                    <v-btn
                                        icon
                                        :color="themeBgColor"
                                        @click="removeService(props.item.id)"
                                    >
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs-items>
        </template>

    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import _ from 'lodash';
import TagBtn from './components/tag-btn';

export default {
    components: {TagBtn},
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            tab: 1,
            headers: {
                services: [
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.service_name,
                        align: 'start',
                        sortable: true,
                        value: 'name',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.actions,
                        sortable: false,
                        value: 'actions',
                    }
                ]
            },
            dialogServices: false,
            forms: {
                services: {
                    name: ''
                }
            },
            searchService: null
        }
    },
    created() {
        this.debounceGetServices = _.debounce(this.__getServices, 1000);
        this.debounceGetSettings = _.debounce(this.__getSettings, 1000);
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.debounceGetServices();
        this.debounceGetSettings();
    },
    methods: {
        __getServices() {
            this.$store.dispatch('Services/getServicesList', { search: this.searchService });
        },
        __getSettings() {
            this.$store.dispatch('Tracking/getSettings');
        },
        createService() {
            this.$store.dispatch('Services/createService', this.forms.services)
                .then(service => {
                    if (service) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_created_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.resetForm();
                    }
                });
        },
        resetForm() {
            this.forms.services = {
                name: ''
            };
        },
        removeService(serviceId) {
            this.$store.dispatch('Services/deleteService', serviceId)
                .then(result => {
                    if (result) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_deleted_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_removal_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        saveService (item) {
            this.$store.dispatch('Services/updateService', item);
        }
    },
    computed: {
        enableTimesheet: {
            get() {
                const { settings } = this.$store.getters['Tracking/getSettings'];
                return settings && settings.enableTimesheet ? settings.enableTimesheet : false;
            },
            set(val) {
                let { settings } = this.$store.getters['Tracking/getSettings'];
                if (!settings) {
                    settings = {};
                }
                console.log(settings);
                settings = { ...settings, enableTimesheet: val };
                this.$store.dispatch('Tracking/updateSettings', { settings });
            }
        }
    }
}
</script>
