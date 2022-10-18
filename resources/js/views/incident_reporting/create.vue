<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" lg="4" md="6" style="border-right: 1px solid gray">
                <v-row>
                    <v-col class="text-left" cols="6">
                        <v-text-field id="incident_number" class="float-left" dense hide-details placeholder="Number"
                                      readonly/>
                    </v-col>
                    <v-col class="text-right" cols="6">
                        <label class="float-right" for="incident_number">STATUS</label>
                    </v-col>
                    <v-col cols="12">
                        <h4 class="heading headline">
                            <v-text-field
                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                v-model="$store.getters['IncidentReporting/getSelectedIR'].name"
                                class="mb-2"
                                dense
                                hide-details
                                outlined placeholder="Name"
                                v-bind="attrs"
                                v-on="on"
                            ></v-text-field>
                        </h4>
                        <v-tabs v-model="tab">
                            <v-tab>General</v-tab>
                            <v-tab>Team</v-tab>
<!--                            <v-tab>Version</v-tab>-->
                        </v-tabs>
                        <v-tabs-items v-model="tab">
                            <v-tab-item>
                                <v-card flat>
                                    <v-row no-gutters>
                                        <v-col class="pr-2 pt-2" cols="5">
                                            <v-text-field
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].version"
                                                class="mb-2" dense hide-details outlined
                                                placeholder="Version"/>
                                        </v-col>
                                        <v-col class="px-2 pt-2" cols="5">
                                            <v-menu
                                                ref="menu"
                                                v-model="menu"
                                                min-width="auto"
                                                offset-y
                                                transition="scale-transition"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                        v-model="$store.getters['IncidentReporting/getSelectedIR'].valid_till"
                                                        :color="themeBgColor"
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined placeholder="Valid till"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].valid_till"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @input="validTillPicker = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col class="text-right pt-2" cols="2">
                                            <v-btn
                                                v-if="!$store.getters['IncidentReporting/getIsEditable']"
                                                @click="setIsEditable"
                                                :color="themeBgColor"
                                                icon
                                            >
                                                <v-icon>mdi-pencil</v-icon>
                                            </v-btn>
                                            <v-btn
                                                v-if="$store.getters['IncidentReporting/getIsEditable']"
                                                @click="save"
                                                :color="themeBgColor"
                                                icon
                                            >
                                                <v-icon>mdi-check</v-icon>
                                            </v-btn>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-select
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].stage_monitoring_id"
                                                :color="themeBgColor"
                                                :item-color="themeBgColor"
                                                :items="$store.getters['IncidentReporting/getIROptions'].stage_monitorings"
                                                class=""
                                                dense
                                                hide-details
                                                item-text="name"
                                                item-value="id"
                                                outlined
                                                placeholder="Valid from Stage Monitoring"
                                                required
                                            ></v-select>
                                            <v-select
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].impact_potential_id"
                                                :color="themeBgColor"
                                                :item-color="themeBgColor"
                                                :items="$store.getters['IncidentReporting/getIROptions'].impact_potentials"
                                                class=""
                                                dense
                                                hide-details
                                                item-text="name"
                                                item-value="id"
                                                outlined
                                                required
                                            ></v-select>
                                            <v-select
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].clients"
                                                :color="themeBgColor"
                                                :item-color="themeBgColor"
                                                :items="$store.getters['RiskRepository/getClients']"
                                                class=""
                                                dense
                                                hide-details
                                                item-text="name"
                                                item-value="id"
                                                multiple
                                                outlined
                                                placeholder="Clients"
                                                required
                                            ></v-select>
                                            <v-checkbox
                                                :aria-disabled="!$store.getters['IncidentReporting/getSelectedIR'].clients.length > 0"
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].with_child_clients"
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                class="mt-0"
                                                label="Include child organizations"
                                            >
                                            </v-checkbox>
                                            <v-select
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].priority_id"
                                                :color="themeBgColor"
                                                :item-color="themeBgColor"
                                                :items="$store.getters['IncidentReporting/getIROptions'].priorities"
                                                class=""
                                                dense
                                                hide-details
                                                item-text="name"
                                                item-value="id"
                                                outlined
                                                placeholder="Importance"
                                                required
                                            ></v-select>
                                            <br>
                                            <v-textarea
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].description"
                                                :color="themeBgColor"
                                                :readonly="!$store.getters['IncidentReporting/getIsEditable']"
                                                auto-grow
                                                label="Description"
                                                name="input-7-1"
                                                outlined
                                            ></v-textarea>
                                            <v-text-field
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].source"
                                                class="mb-2" dense hide-details outlined
                                                placeholder="Source"/>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-menu
                                                ref="menu"
                                                v-model="occurredOnMenu"
                                                min-width="auto"
                                                offset-y
                                                transition="scale-transition"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                        v-model="$store.getters['IncidentReporting/getSelectedIR'].occurred_on"
                                                        :color="themeBgColor"
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined placeholder="Valid till"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].occurred_on"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @input="occurredOnPicker = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-menu
                                                ref="menu"
                                                v-model="detectedOnMenu"
                                                min-width="auto"
                                                offset-y
                                                transition="scale-transition"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                        v-model="$store.getters['IncidentReporting/getSelectedIR'].detected_on"
                                                        :color="themeBgColor"
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined placeholder="Valid till"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].detected_on"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @input="detectedOnPicker = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-menu
                                                ref="menu"
                                                v-model="reportedOnMenu"
                                                min-width="auto"
                                                offset-y
                                                transition="scale-transition"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                        v-model="$store.getters['IncidentReporting/getSelectedIR'].reported_on"
                                                        :color="themeBgColor"
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined placeholder="Valid till"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].reported_on"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @input="reportedOnPicker = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                    </v-row>
                                </v-card>
                            </v-tab-item>
                            <v-tab-item></v-tab-item>
                            <v-tab-item></v-tab-item>
                        </v-tabs-items>
                    </v-col>
                </v-row>

            </v-col>
            <v-col cols="12" lg="4" md="6" style="border-right: 1px solid gray">
                <h4 class="heading headline">Incident tasks</h4>
                <v-row no-gutters>
                    <v-row>
                        <v-col cols="12">
                            <v-chip
                                v-for="(item, index) in ['Monitoring', 'Handling']"
                                :key="index"
                                class="mr-2"
                                label
                                outlined
                            >{{ item }}
                            </v-chip>
                        </v-col>
                        <v-col cols="12">
                            <v-card
                                v-for="(taskGroup, index) in taskGroups"
                                :key="index"
                                class="d-inline-flex flex-column mr-2 mb-2"
                                max-width="170"
                                min-width="170"
                                outlined
                            >
                                <v-list-item three-line>
                                    <v-list-item-content>
                                        <div class="">
                                            <span class="float-left">{{ taskGroup.name }}</span>
                                            <span v-if="taskGroup.total" class="float-right">{{
                                                    taskGroup.completed
                                                }}/{{ taskGroup.total }}</span>
                                        </div>
                                    </v-list-item-content>
                                </v-list-item>
                                <div class="clearfix"></div>
                                <v-card-actions>
                                    <v-btn
                                        outlined
                                        rounded
                                        text
                                    >
                                        {{ taskGroup.tag }}
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                        <v-col cols="12">
                            <v-row no-gutters style="border-bottom: 1px solid gray">
                                <v-col cols="5"></v-col>
                                <v-col cols="2">Progress</v-col>
                                <v-col cols="2">Deadline</v-col>
                                <v-col cols="2">Assigned to</v-col>
                                <v-col cols="1"></v-col>
                            </v-row>
                            <v-row v-for="(task, index) in tasks" :key="index" style="border-bottom: 1px solid gray">
                                <v-col cols="5">{{ task.name }}</v-col>
                                <v-col cols="2">
                                    <v-chip
                                        :color="task.color"
                                        class="text-uppercase" outlined
                                        small
                                    >{{ task.progress }}
                                    </v-chip>
                                </v-col>
                                <v-col cols="2">{{ task.deadline }}</v-col>
                                <v-col cols="2">{{ task.assignedTo }}</v-col>
                                <v-col cols="1">
                                    <v-icon v-if="task.email">mdi-email</v-icon>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-row>
            </v-col>
            <v-col cols="12" lg="4" md="6">
                <v-row no-gutters>
                    <v-col cols="12">
                        <h4 class="heading headline float-left">Log of Actions</h4>
                        <v-spacer></v-spacer>
                        <v-menu
                            bottom
                            left
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    :color="themeBgColor"
                                    class="float-right"
                                    dark
                                    icon
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>mdi-dots-vertical</v-icon>
                                </v-btn>
                            </template>

                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>Menu 1</v-list-item-title>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Menu 2</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-col>
                </v-row>
                <v-row v-for="(action, index) in actions" :key="index" style="border-bottom: 1px solid gray">
                    <v-col cols="8" lg="8" md="12" sm="12">{{ action.name }}</v-col>
                    <v-col cols="4" lg="4" md="12" sm="12">{{ action.date }}</v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";

export default {
    name: 'incident_reporting_create',
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            tab: 0,
            actions: [
                {
                    name: 'Crisis team meeting scheduled',
                    date: '2021.12.15',
                },
                {
                    name: 'Crisis Reporter confirmed',
                    date: '2021.12.15',
                },
                {
                    name: 'Crisis Leader confirmed',
                    date: '2021.12.15',
                },
                {
                    name: 'Incident recorded',
                    date: '2021.12.15',
                },
            ],
            taskGroups: [
                {
                    name: 'Initial tasks',
                    total: 6,
                    completed: 5,
                    tag: 'Monitoring'
                },
                {
                    name: 'Activate backup location',
                    total: null,
                    completed: null,
                    tag: 'Handling'
                },
                {
                    name: 'Communication tasks',
                    total: null,
                    completed: null,
                    tag: 'Handling'
                },
                {
                    name: 'Activate backup location',
                    total: null,
                    completed: null,
                    tag: 'Handling'
                },
                {
                    name: 'Communication tasks',
                    total: null,
                    completed: null,
                    tag: 'Handling'
                },
            ],
            tasks: [
                {
                    name: 'Task 1',
                    progress: 'complete',
                    color: 'green',
                    deadline: '2021.12.15',
                    assignedTo: '',
                    email: true
                },
                {
                    name: 'Task 2',
                    progress: 'in progress',
                    color: 'primary',
                    deadline: null,
                    assignedTo: '',
                    email: ''
                },
                {
                    name: 'Task 3',
                    progress: 'not started',
                    color: 'gray',
                    deadline: '2021.12.15',
                    assignedTo: '',
                    email: ''
                },
                {
                    name: 'Task 4',
                    progress: 'in progress',
                    color: 'primary',
                    deadline: '2021.12.15',
                    assignedTo: '',
                    email: ''
                },
                {
                    name: 'Task 5',
                    progress: 'not started',
                    color: 'gray',
                    deadline: null,
                    assignedTo: '',
                    email: ''
                },
                {
                    name: 'Task 6',
                    progress: 'not started',
                    color: 'gray',
                    deadline: null,
                    assignedTo: '',
                    email: ''
                },
            ],
            menu: false,
            occrredOnMenu: false,
            detectedOnMenu: false,
            reportedOnMenu: false,
            activePicker: false,
            validTillPicker: false,
            occurredOnPicker: false,
            detectedOnPicker: false,
            reportedOnPicker: false
        }
    },
    mounted() {
        const that = this
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.$store.dispatch('RiskRepository/callGetClients');
        this.$store.dispatch('SettingsIncident/ActionBoardStatuses/callList');
        this.$store.dispatch('IncidentReporting/callGetEmployees');
        this.$store.dispatch('IncidentReporting/callGetIROptions');
        this.$store.dispatch('IncidentReporting/callGetIRActions');
        this.$store.dispatch('IncidentReporting/callSetSelectedIR', null)
        this.$store.dispatch('IncidentReporting/callSetIRType', 3);
    },
    methods: {
        setIsEditable() {
            this.$store.dispatch('IncidentReporting/callSetIsEditable', true)
        },
        save() {
            this.$store.dispatch('IncidentReporting/callStoreIR', false)
            this.$store.dispatch('IncidentReporting/callSetIsEditable', false)
            this.$router.push(`/incident_reporting/list`)
        }
    }
}
</script>

<style scoped>
*,
>>> .v-input,
>>> label,
>>> .v-btn__content {
    font-size: 12px;
}

.heading {
    font-size: 16px;
}

.clearfix {
    clear: both;
}
</style>
