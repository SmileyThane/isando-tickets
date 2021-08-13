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
                <v-tab
                    v-if="$helpers.auth.checkPermissionByIds([79])"
                    :key="1"
                >{{ langMap.tracking.settings.general }}</v-tab>
                <v-tab
                    v-if="$helpers.auth.checkPermissionByIds([75])"
                    :key="2"
                >{{ langMap.tracking.settings.services }}</v-tab>
            </v-tabs>

            <v-tabs-items v-model="tab">
                <v-tab-item :key="1" v-if="$helpers.auth.checkPermissionByIds([79])">
                    <div class="d-flex flex-row">
                        <div class="d-inline-flex flex-grow-1 ma-4" style="width: 100%">
                            <!--Column 1-->
                            <div class="d-flex mb-auto flex-column" style="width: 100%">
                                <div class="d-inline-flex">
                                    <v-card class="elevation-12 without-bottom" style="width: 100%">
                                        <v-toolbar :color="themeBgColor" dark dense flat>
                                            <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.tracking.settings.general }}</v-toolbar-title>
                                        </v-toolbar>
                                        <v-card-text class="mb-4">
                                            <v-checkbox
                                                :disabled="!$helpers.auth.checkPermissionByIds([80])"
                                                v-model="enableTimesheet"
                                                :label="langMap.tracking.settings.enable_timesheet"
                                            ></v-checkbox>
                                            <div>
                                                Use one of the three options below for description of the time tracked:
                                                <br>
                                                <v-btn-toggle
                                                    v-model="toggleProjectType"
                                                    mandatory
                                                    dense
                                                >
                                                    <v-btn small>
                                                        Projects
                                                    </v-btn>

                                                    <v-btn small>
                                                        Departments
                                                    </v-btn>

                                                    <v-btn small>
                                                        Profit centres
                                                    </v-btn>
                                                </v-btn-toggle>
                                            </div>
                                        </v-card-text>
                                    </v-card>
                                </div>
                                <v-spacer>&nbsp;</v-spacer>
                                <div class="d-inline-flex">
                                    <v-card class="elevation-12 without-bottom" style="width: 100%">
                                        <v-toolbar :color="themeBgColor" dark dense flat>
                                            <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.tracking.settings.custom_rounding }}</v-toolbar-title>
                                        </v-toolbar>
                                        <v-card-text>

                                            <v-expansion-panels>
                                                <v-expansion-panel
                                                    v-for="item in this.$store.getters['Tracking/getSettings'].settings.customRounding"
                                                    :key="item.key"
                                                >
                                                    <v-expansion-panel-header v-slot="{ open }">
                                                        <div class="d-flex flex-row">
                                                            <div class="d-inline-flex" style="width: 40%">
                                                                {{ item.name }}
                                                            </div>
                                                            <div class="d-inline-flex" style="width: 80%">
                                                                <v-fade-transition leave-absolute>
                                                                    <span v-if="open"></span>
                                                                    <div class="d-flex flex-row"
                                                                         v-else
                                                                         style="width: 100%"
                                                                    >
                                                                        <div class="d-inline-flex mx-4">

                                                                        </div>
                                                                    </div>
                                                                </v-fade-transition>
                                                            </div>
                                                        </div>
                                                    </v-expansion-panel-header>
                                                    <v-expansion-panel-content>
                                                        <v-text-field
                                                            v-model="item.name"
                                                            placeholder="Name"
                                                        ></v-text-field>
                                                        <v-text-field
                                                            type="number"
                                                            v-model="item.seconds"
                                                            label="Time in minutes"
                                                            step="1"
                                                        ></v-text-field>
                                                        <v-select
                                                            v-model="item.direction"
                                                            :items="availableDirections"
                                                            item-text="text"
                                                            item-value="value"
                                                            label="Direction"
                                                        ></v-select>
                                                        <div class="d-flex flex-row-reverse">
                                                            <v-btn
                                                                right
                                                                color="success"
                                                                class="mx-2"
                                                                @click="actionUpdateCustomRounding(item)"
                                                            >
                                                                Update
                                                            </v-btn>
                                                            <v-btn
                                                                right
                                                                color="error"
                                                                class="mx-2"
                                                                @click="actionDeleteCustomRounding(item)"
                                                            >
                                                                Remove
                                                            </v-btn>
                                                        </div>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>

                                                <v-spacer>&nbsp;</v-spacer>

                                                <v-expansion-panel
                                                    :key="0"
                                                >
                                                    <v-expansion-panel-header v-slot="{ open }">
                                                        <div class="d-flex flex-row">
                                                            <div class="d-inline-flex" style="width: 40%">
                                                                Create new
                                                            </div>
                                                            <div class="d-inline-flex" style="width: 80%">
                                                                <v-fade-transition leave-absolute>
                                                                    <span v-if="open"></span>
                                                                    <div class="d-flex flex-row"
                                                                         v-else
                                                                         style="width: 100%"
                                                                    >
                                                                        <div class="d-inline-flex mx-4">

                                                                        </div>
                                                                    </div>
                                                                </v-fade-transition>
                                                            </div>
                                                        </div>
                                                    </v-expansion-panel-header>
                                                    <v-expansion-panel-content>
                                                        <v-text-field
                                                            v-model="forms.customRounding.name"
                                                            placeholder="Name"
                                                        ></v-text-field>
                                                        <v-text-field
                                                            type="number"
                                                            v-model="forms.customRounding.seconds"
                                                            label="Time in minutes"
                                                            step="1"
                                                        ></v-text-field>
                                                        <v-select
                                                            v-model="forms.customRounding.direction"
                                                            :items="availableDirections"
                                                            item-text="text"
                                                            item-value="value"
                                                            label="Direction"
                                                        ></v-select>
                                                        <div class="d-flex flex-row-reverse">
                                                            <v-btn
                                                                right
                                                                color="success"
                                                                class="mx-2"
                                                                @click="actionCreateCustomRounding()"
                                                            >
                                                                Create
                                                            </v-btn>
                                                            <v-btn
                                                                right
                                                                color="error"
                                                                class="mx-2"
                                                                @click="resetForm"
                                                            >
                                                                Cancel
                                                            </v-btn>
                                                        </div>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                            </v-expansion-panels>

                                        </v-card-text>
                                    </v-card>
                                </div>
                            </div>
                        </div>
                        <div class="d-inline-flex flex-grow-1 ma-4" style="width: 100%">
                            <!--Column 2-->
                            <v-card class="elevation-12 without-bottom" style="width: 100%" v-if="enableTimesheet">
                                <v-toolbar :color="themeBgColor" dark dense flat>
                                    <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.tracking.settings.timesheet }}</v-toolbar-title>
                                </v-toolbar>
                                <v-card-text>
                                    <v-expansion-panels>
                                        <v-expansion-panel
                                            v-for="item in this.$store.getters['Tracking/getSettings'].settings.timesheetWeek"
                                            :key="item.dayOfWeek"
                                        >
                                            <v-expansion-panel-header v-slot="{ open }">
                                                <div class="d-flex flex-row">
                                                    <div class="d-inline-flex" style="width: 40%">
                                                        {{ daysOfWeekTitle[item.dayOfWeek] }}
                                                    </div>
                                                    <div class="d-inline-flex" style="width: 80%">
                                                        <v-fade-transition leave-absolute>
                                                            <span v-if="open">When do you want to work?</span>
                                                            <div class="d-flex flex-row"
                                                                v-else
                                                                style="width: 100%"
                                                            >
                                                                <div class="d-inline-flex mx-4">
                                                                    {{ moment(item.workTime.start).format('HH:mm') || 'Not set' }} - {{ moment(item.lunchTime.start).format('HH:mm') || 'Not set' }}
                                                                </div>
                                                                <div class="d-inline-flex mx-4">
                                                                    {{ moment(item.lunchTime.end).format('HH:mm') || 'Not set' }} - {{ moment(item.workTime.end).format('HH:mm') || 'Not set' }}
                                                                </div>
                                                            </div>
                                                        </v-fade-transition>
                                                    </div>
                                                </div>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <div class="d-flex flex-column">
                                                    <div class="d-inline-flex d-flex flex-row">
                                                        <div class="d-inline-flex flex-grow-0" style="width: 30%; font-weight: bold">Work time</div>
                                                        <div class="d-inline-flex flex-grow-1 mx-4">
                                                            <TimeField
                                                                v-if="$helpers.auth.checkPermissionByIds([81])"
                                                                style="max-width: 100px"
                                                                placeholder="hh:mm"
                                                                format="HH:mm"
                                                                v-model="item.workTime.start"
                                                                @input="debounceSaveSettings"
                                                            ></TimeField>
                                                            <span v-else style="max-width: 100px" class="d-inline-flex flex-grow-1 mx-4">
                                                                {{moment(item.workTime.start).format('HH:mm')}}
                                                            </span>
                                                            <TimeField
                                                                v-if="$helpers.auth.checkPermissionByIds([81])"
                                                                style="max-width: 100px"
                                                                placeholder="hh:mm"
                                                                format="HH:mm"
                                                                v-model="item.workTime.end"
                                                                @input="debounceSaveSettings"
                                                            ></TimeField>
                                                            <span v-else style="max-width: 100px" class="d-inline-flex flex-grow-1 mx-4">
                                                                {{moment(item.workTime.end).format('HH:mm')}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-inline-flex d-flex flex-row">
                                                        <div class="d-inline-flex flex-grow-0" style="width: 30%; font-weight: bold">Lunch time</div>
                                                        <div class="d-inline-flex flex-grow-1 mx-4">
                                                            <TimeField
                                                                v-if="$helpers.auth.checkPermissionByIds([81])"
                                                                style="max-width: 100px"
                                                                placeholder="hh:mm"
                                                                format="HH:mm"
                                                                v-model="item.lunchTime.start"
                                                                @input="debounceSaveSettings"
                                                            ></TimeField>
                                                            <span v-else style="max-width: 100px" class="d-inline-flex flex-grow-1 mx-4">
                                                                {{moment(item.lunchTime.start).format('HH:mm')}}
                                                            </span>
                                                            <TimeField
                                                                v-if="$helpers.auth.checkPermissionByIds([81])"
                                                                style="max-width: 100px"
                                                                placeholder="hh:mm"
                                                                format="HH:mm"
                                                                v-model="item.lunchTime.end"
                                                                @input="debounceSaveSettings"
                                                            ></TimeField>
                                                            <span v-else style="max-width: 100px" class="d-inline-flex flex-grow-1 mx-4">
                                                                {{moment(item.lunchTime.end).format('HH:mm')}}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                    </v-expansion-panels>
                                </v-card-text>
                            </v-card>
                        </div>
                    </div>
                </v-tab-item>

                <v-tab-item :key="2" v-if="$helpers.auth.checkPermissionByIds([75])">
                    <v-card flat>
                        <v-toolbar flat>
                            <v-dialog
                                v-if="$helpers.auth.checkPermissionByIds([76])"
                                v-model="dialogServices"
                                width="500"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        v-if="$helpers.auth.checkPermissionByIds([76])"
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
                                v-if="$helpers.auth.checkPermissionByIds([75])"
                                dense
                                :headers="headers.services"
                                :items="$store.getters['Services/getServices']"
                                :items-per-page="15"
                                class="elevation-1"
                            >
                                <template v-slot:item.name="props">
                                    <v-edit-dialog
                                        v-if="$helpers.auth.checkPermissionByIds([77])"
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
                                    <span
                                        v-else
                                    >
                                        {{props.item.name}}
                                    </span>
                                </template>
                                <template v-slot:item.actions="props">
                                    <v-btn
                                        v-if="$helpers.auth.checkPermissionByIds([78])"
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

<style scoped>

</style>

<script>
import EventBus from "../../components/EventBus";
import _ from 'lodash';
import moment from 'moment-timezone';
import TagBtn from './components/tag-btn';
import TimeField from './components/time-field';

export default {
    components: {TagBtn, TimeField},
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            tab: 0,
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
                },
                customRounding: {
                    name: '',
                    seconds: 1,
                    direction: 'nearest'
                },
            },
            availableDirections: [
                {
                    value: 'up',
                    text: 'up',
                },
                {
                    value: 'nearest',
                    text: 'nearest',
                },
                {
                    value: 'down',
                    text: 'down',
                },
            ],
            searchService: null,
            daysOfWeekTitle: [
                this.$store.state.lang.lang_map.tracking.timesheet.monday,
                this.$store.state.lang.lang_map.tracking.timesheet.tuesday,
                this.$store.state.lang.lang_map.tracking.timesheet.wednesday,
                this.$store.state.lang.lang_map.tracking.timesheet.thursday,
                this.$store.state.lang.lang_map.tracking.timesheet.friday,
                this.$store.state.lang.lang_map.tracking.timesheet.saturday,
                this.$store.state.lang.lang_map.tracking.timesheet.sunday
            ]
        }
    },
    created() {
        this.debounceGetServices = _.debounce(this.__getServices, 1000);
        this.debounceGetSettings = _.debounce(this.__getSettings, 1000);
        this.debounceSaveSettings = _.debounce(this.__saveSettings, 1000);
        moment.updateLocale(this.$store.state.lang.short_code, {
            week: {
                dow: 1,
            },
        });
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
            this.forms.customRounding = {
                name: '',
                seconds: 1,
                direction: 'nearest'
            }
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
        },
        __saveSettings() {
            this.$store.dispatch('Tracking/saveSettings');
        },
        actionCreateCustomRounding() {
            this.$store.dispatch('Tracking/createCustomRounding', this.forms.customRounding);
            this.resetForm();
        },
        actionUpdateCustomRounding(item) {
            this.$store.dispatch('Tracking/updateCustomRounding', item);
        },
        actionDeleteCustomRounding(item) {
            this.$store.dispatch('Tracking/deleteCustomRounding', item);
        },
    },
    computed: {
        enableTimesheet: {
            get() {
                const { settings } = this.$store.getters['Tracking/getSettings'];
                return settings && settings.enableTimesheet ? settings.enableTimesheet : false;
            },
            async set(val) {
                await this.$store.commit('Tracking/SET_TOGGLE_TIMESHEET', val);
                await this.debounceSaveSettings();
            }
        },
        toggleProjectType: {
            get() {
                const { settings } = this.$store.getters['Tracking/getSettings'];
                return settings && settings.projectType ? settings.projectType : 0;
            },
            async set(val) {
                await this.$store.commit('Tracking/SET_PROJECT_TYPE', val);
                await this.debounceSaveSettings();
            }
        },
    },
    watch: {

    }
}
</script>
