<template>
    <v-container fluid>
        <v-row style="height: 85vh;">
            <v-col cols="12" lg="4" md="6" style="border-right: 1px solid gray">
                <v-row>
                    <v-col cols="12">
                        <h4 class="heading headline">
                            <v-text-field
                                v-model="$store.getters['IncidentReporting/getSelectedIR'].name"
                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                class="mb-2"
                                dense
                                hide-details
                                outlined placeholder="Name"


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
                                        <v-col class="pr-2 pt-2" cols="6">
                                            <v-text-field
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].version"
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
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
                                                        v-model="$store.getters['IncidentReporting/getSelectedIR'].valid_till"
                                                        :color="themeBgColor"
                                                        :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined placeholder="Valid till"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].valid_till"
                                                    :color="themeBgColor"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @input="validTillPicker = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col class="text-right pt-2" cols="1">
                                            <v-btn
                                                v-if="!$store.getters['IncidentReporting/getIsEditable']"
                                                :color="themeBgColor"
                                                icon
                                                @click="setIsEditable"
                                            >
                                                <v-icon>mdi-pencil</v-icon>
                                            </v-btn>
                                            <v-btn
                                                v-if="$store.getters['IncidentReporting/getIsEditable']"
                                                :color="themeBgColor"
                                                icon
                                                @click="save"
                                            >
                                                <v-icon>mdi-check</v-icon>
                                            </v-btn>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-select
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].stage_monitoring_id"
                                                :color="themeBgColor"
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
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
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].impact_potential_id"
                                                :color="themeBgColor"
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
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
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].clients"
                                                :color="themeBgColor"
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
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
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].with_child_clients"
                                                :aria-disabled="!$store.getters['IncidentReporting/getSelectedIR'].clients.length > 0"
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                class="mt-0"
                                                label="Include child organizations"
                                            >
                                            </v-checkbox>
                                            <v-select
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].priority_id"
                                                :color="themeBgColor"
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
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
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].source"
                                                :disabled="!$store.getters['IncidentReporting/getIsEditable']"
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
                                                        v-model="$store.getters['IncidentReporting/getSelectedIR'].occurred_on"
                                                        :color="themeBgColor"
                                                        :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined placeholder="Occurred on"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].occurred_on"
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
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
                                                        v-model="$store.getters['IncidentReporting/getSelectedIR'].detected_on"
                                                        :color="themeBgColor"
                                                        :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined placeholder="Valid till"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].detected_on"
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
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
                                                        v-model="$store.getters['IncidentReporting/getSelectedIR'].reported_on"
                                                        :color="themeBgColor"
                                                        :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined placeholder="Reported on"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].reported_on"
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
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
                            <v-card
                                v-for="(taskGroup, index) in $store.getters['IncidentReporting/getIR']"
                                :key="index"
                                class="d-inline-flex flex-column mr-2 mb-2"
                                max-width="170"
                                min-width="170"
                                outlined
                            >
                                <v-list-item three-line>
                                    <v-list-item-content>
                                        <div class="">
                                            <span v-if="taskGroup.type_id === 2" class="float-left">Scenario:</span>
                                            <span v-if="taskGroup.type_id === 1" class="float-left">Action board:</span>
                                            <br>
                                            <strong class="float-left">{{ taskGroup.name }}</strong>
                                        </div>
                                    </v-list-item-content>
                                </v-list-item>
                                <div class="clearfix"></div>
                                <v-card-actions>
                                    <v-btn
                                        outlined
                                        rounded
                                        text
                                        @click="showList(taskGroup)"
                                    >
                                        {{ 'show' }}
                                    </v-btn>
                                    <v-btn
                                        icon
                                        outlined
                                        rounded
                                        text
                                        @click="addToList(taskGroup)"
                                    >
                                        <v-icon>mdi-plus</v-icon>

                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                        <v-col v-if="actionsList.length > 0" cols="12">
                            <h3>Actions</h3>
                            <v-row no-gutters style="border-bottom: 1px solid gray">
                                <v-col cols="4">Name</v-col>
                                <v-col cols="3">Deadline</v-col>
                                <v-col cols="4">Assigned to</v-col>
                                <v-col cols="1"></v-col>
                            </v-row>
                            <v-row v-for="(task, index) in actionsList" :key="index" no-gutters
                                   style="border-bottom: 1px solid gray">
                                <v-col cols="4">{{ task.name }}</v-col>
                                <v-col cols="3">
                                    {{
                                        task.deadline_time_indicator
                                    }}
                                    &nbsp;
                                    {{
                                        task.deadline_time_value
                                    }}
                                    &nbsp;
                                    {{
                                        task.deadline_time_parameter
                                    }}
                                </v-col>
                                <v-col cols="4">{{ task.assignee ? task.assignee.user_data.email : '' }}</v-col>
                                <v-col cols="1">
                                    <v-btn
                                        icon
                                        outlined
                                        rounded
                                        style="margin-bottom: 12px;"
                                        text
                                        @click="addToList(task)"
                                    >
                                        <v-icon>mdi-plus</v-icon>

                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col v-if="actionBoardsList.length > 0" cols="12">
                            <h3>Action Boards</h3>
                            <v-row no-gutters style="border-bottom: 1px solid gray">
                                <v-col cols="4">Name</v-col>
                                <v-col cols="3">Status</v-col>
                                <v-col cols="3">Valid till</v-col>
                                <v-col cols="2">Version</v-col>
                                <v-col cols="1"></v-col>
                            </v-row>
                            <v-row v-for="(task, index) in actionBoardsList" :key="index" no-gutters
                                   style="border-bottom: 1px solid gray">
                                <v-col cols="4">{{ task.name }}</v-col>
                                <v-col cols="3">{{ task.status ? task.status.name : '' }}</v-col>
                                <v-col cols="3">{{ task.valid_till }}</v-col>
                                <v-col cols="1">{{ task.version }}</v-col>
                                <v-col cols="1">
                                    <v-btn
                                        icon
                                        outlined
                                        rounded
                                        style="margin-bottom: 12px;"
                                        text
                                        @click="addToList(task)"
                                    >
                                        <v-icon>mdi-plus</v-icon>

                                    </v-btn>
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
                        <!--                        <v-menu-->
                        <!--                            bottom-->
                        <!--                            left-->
                        <!--                        >-->
                        <!--                            <template>-->
                        <!--                                <v-btn-->
                        <!--                                    :color="themeBgColor"-->
                        <!--                                    class="float-right"-->
                        <!--                                    dark-->
                        <!--                                    icon-->
                        <!--                                >-->
                        <!--                                    <v-icon>mdi-dots-vertical</v-icon>-->
                        <!--                                </v-btn>-->
                        <!--                            </template>-->

                        <!--                            <v-list>-->
                        <!--                                <v-list-item>-->
                        <!--                                    <v-list-item-title>Menu 1</v-list-item-title>-->
                        <!--                                </v-list-item>-->
                        <!--                                <v-list-item>-->
                        <!--                                    <v-list-item-title>Menu 2</v-list-item-title>-->
                        <!--                                </v-list-item>-->
                        <!--                            </v-list>-->
                        <!--                        </v-menu>-->
                    </v-col>
                </v-row>
                <v-row v-for="(action, index) in finalList" :key="index" style="border-bottom: 1px solid gray">
                    <v-col cols="8" lg="8" md="12" sm="12">{{ action.name }}</v-col>
                    <!--                    <v-col cols="4" lg="4" md="12" sm="12">{{ action.date }}</v-col>-->
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
            menu: false,
            occurredOnMenu: false,
            detectedOnMenu: false,
            reportedOnMenu: false,
            activePicker: false,
            validTillPicker: false,
            occurredOnPicker: false,
            detectedOnPicker: false,
            reportedOnPicker: false,
            actionsList: [],
            actionBoardsList: [],
            finalList: []

        }
    },
    mounted() {
        const that = this
        let IR = null;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        if (this.$route.params.id) {
            IR = this.$store.getters['IncidentReporting/getSelectedIR']
            this.finalList = IR.actions
        } else {
            this.$store.commit('IncidentReporting/setIsEditable', true);
        }
        this.$store.commit('IncidentReporting/setIRType', -3);
        this.$store.dispatch('IncidentReporting/callGetIR').then(() => {
            this.$store.dispatch('IncidentReporting/callSetSelectedIR', IR)
        });
        this.$store.commit('IncidentReporting/setIRType', 3);
        this.$store.dispatch('RiskRepository/callGetClients');
        this.$store.dispatch('SettingsIncident/ActionBoardStatuses/callList');
        this.$store.dispatch('IncidentReporting/callGetEmployees');
        this.$store.dispatch('IncidentReporting/callGetIROptions');
        this.$store.dispatch('IncidentReporting/callGetIRActions');

    },
    methods: {
        setIsEditable() {
            this.$store.dispatch('IncidentReporting/callSetIsEditable', true)
        },
        save() {
            this.$store.dispatch('IncidentReporting/callStoreIR', false)
            // this.$store.dispatch('IncidentReporting/callSetIsEditable', false)
            // this.$router.push(`/incident_reporting/list`)
        },
        showList(item) {
            this.$store.commit('IncidentReporting/setRelatedIR', item)
            this.actionsList = this.$store.getters['IncidentReporting/getRelatedIR'].actions
            this.actionBoardsList = this.$store.getters['IncidentReporting/getRelatedIR'].action_boards
        },
        addToList(item) {
            if (item.actions !== undefined && item.actions.length > 0) {
                this.finalList = [...this.finalList, ...item.actions]
            }
            if (item.action_boards !== undefined && item.action_boards.length > 0) {
                for (const key in item.action_boards) {
                    this.finalList = [...this.finalList, ...item.action_boards[key].actions]
                }
            }
            if (item.action_boards === undefined && item.actions === undefined) {
                this.finalList = [...this.finalList, ...[item]]
            }
            this.$store.getters['IncidentReporting/getSelectedIR'].actions = this.finalList
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

.row {
    margin-bottom: 12px;
}
</style>
