<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" lg="4" md="6" style="border-right: 1px solid gray" class="mt-2">
                <h4 class="heading">{{ $store.getters['IncidentReporting/getSelectedIR'].name }}</h4>
                <v-row>
                    <v-col cols="12">
                        <h4 class="heading headline">
                            <v-text-field
                                v-model="$store.getters['IncidentReporting/getSelectedIR'].name"
                                class="mb-2"
                                dense
                                hide-details
                                outlined
                                :label="langMap.main.name"
                            ></v-text-field>
                        </h4>
                        <v-tabs v-model="tab">
                            <v-tab>General</v-tab>
                            <!--                            <v-tab>Team</v-tab>-->
                            <!--                            <v-tab>Version</v-tab>-->
                        </v-tabs>
                        <v-tabs-items v-model="tab">
                            <v-tab-item>
                                <v-card flat>
                                    <v-row no-gutters>
                                        <v-col class="pr-2 pt-2" cols="6">
                                            <v-text-field
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].version"
                                                dense
                                                hide-details
                                                outlined
                                                :label="langMap.ir.ab.version"
                                            />
                                        </v-col>
                                        <v-col class="pt-2" cols="6">
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
                                                        dense
                                                        hide-details
                                                        outlined
                                                        :label="langMap.ir.ab.valid_till"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    first-day-of-week="1"
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].valid_till"
                                                    :color="themeBgColor"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @input="validTillPicker = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <!--                                        <v-col class="text-right pt-2" cols="1">-->
                                        <!--                                            <v-btn-->
                                        <!--                                                v-if="!$store.getters['IncidentReporting/getIsEditable']"-->
                                        <!--                                                :color="themeBgColor"-->
                                        <!--                                                icon-->
                                        <!--                                                @click="setIsEditable"-->
                                        <!--                                            >-->
                                        <!--                                                <v-icon>mdi-pencil</v-icon>-->
                                        <!--                                            </v-btn>-->
                                        <!--                                            <v-btn-->
                                        <!--                                                v-if="$store.getters['IncidentReporting/getIsEditable']"-->
                                        <!--                                                :color="themeBgColor"-->
                                        <!--                                                icon-->
                                        <!--                                                @click="save"-->
                                        <!--                                            >-->
                                        <!--                                                <v-icon>mdi-check</v-icon>-->
                                        <!--                                            </v-btn>-->
                                        <!--                                        </v-col>-->
                                        <v-col class="pt-2" cols="12">
                                            <v-select
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].stage_monitoring_id"
                                                :color="themeBgColor"
                                                :item-color="themeBgColor"
                                                :items="$store.getters['IncidentReporting/getIROptions'].stage_monitorings"
                                                dense
                                                hide-details
                                                item-text="name"
                                                item-value="id"
                                                outlined
                                                :label="langMap.ir.ab.stage_monitoring"
                                                required
                                            ></v-select>
                                        </v-col>
                                        <v-col class="pt-2" cols="12">
                                            <v-select
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
                                                :label="langMap.ir.ab.impact_potentials"
                                                required
                                            ></v-select>
                                        </v-col>
                                        <v-col class="pt-2" cols="12">
                                            <v-select
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
                                                :label="langMap.ir.ab.clients"
                                                required
                                            ></v-select>
                                        </v-col>
                                        <v-col class="pt-2" cols="12">
                                            <v-checkbox
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].with_child_clients"
                                                :aria-disabled="!$store.getters['IncidentReporting/getSelectedIR'].clients.length > 0"
                                                class="mt-0"
                                                :label="langMap.ir.ab.with_child_clients"
                                            >
                                            </v-checkbox>
                                        </v-col>
                                        <v-col class="pt-2" cols="12">
                                            <v-select
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
                                                :label="langMap.ir.ab.importance"
                                                required
                                            ></v-select>
                                        </v-col>
                                        <v-col class="pt-2" cols="12">
                                            <v-select
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].scenario_id"
                                                :color="themeBgColor"
                                                :item-color="themeBgColor"
                                                :items="$store.getters['IncidentReporting/getIR']"
                                                item-value="id"
                                                item-text="name"
                                                dense
                                                hide-details
                                                outlined
                                                :label="langMap.ir.ab.scenarios"
                                                required
                                                @change="setRelatedIR($event)"
                                            >
                                            </v-select>
                                        </v-col>
                                        <v-col class="pt-2" cols="12">
                                            <v-textarea
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].description"
                                                :color="themeBgColor"
                                                :readonly="!$store.getters['IncidentReporting/getIsEditable']"
                                                auto-grow
                                                :label="langMap.main.description"
                                                name="input-7-1"
                                                outlined
                                            ></v-textarea>
                                        </v-col>
                                        <v-col class="pt-2" cols="12">
                                            <v-text-field
                                                v-model="$store.getters['IncidentReporting/getSelectedIR'].source"
                                                dense hide-details outlined
                                                :label="langMap.ir.ab.source"/>
                                        </v-col>
                                        <v-col class="pr-1 pt-2" cols="4">
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
                                                        dense
                                                        hide-details
                                                        outlined
                                                        :label="langMap.ir.ab.occurred_on"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    first-day-of-week="1"
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].occurred_on"
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @input="occurredOnPicker = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col class="pr-1 pt-2" cols="4">
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
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined
                                                        :label="langMap.ir.ab.valid_till"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    first-day-of-week="1"
                                                    v-model="$store.getters['IncidentReporting/getSelectedIR'].detected_on"
                                                    :active-picker.sync="validTillPicker"
                                                    :color="themeBgColor"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @input="detectedOnPicker = false"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col class="pt-2" cols="4">
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
                                                        class="mb-2"
                                                        dense
                                                        hide-details
                                                        outlined
                                                        :label="langMap.ir.ab.reported_on"
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    first-day-of-week="1"
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
<!--                            <v-tab-item></v-tab-item>-->
<!--                            <v-tab-item></v-tab-item>-->
                        </v-tabs-items>
                        <v-btn
                            :color="themeBgColor"
                            class="mb-1"
                            :style="`color: ${themeFgColor}`"
                            @click="save()">
                            Save
                        </v-btn>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="12" lg="4" md="6" style="border-right: 1px solid gray" class="mt-2">
                <h4 class="heading">{{ langMap.ir.ab.title }}</h4>
                <ListActionBoards></ListActionBoards>
                <ListActions></ListActions>
            </v-col>
            <v-col cols="12" lg="4" md="6" class="mt-2">
                <v-row no-gutters>
                    <v-col cols="12">
                        <h4 class="heading">{{ langMap.ir.ab.log_of_actions }}</h4>
                        <v-spacer></v-spacer>
                    </v-col>
                </v-row>
                <ListLogs></ListLogs>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import ListActionBoards from "./components/list-action-boards.vue";
import ListActions from "./components/list-actions.vue";
import ListLogs from "./components/list-logs.vue";

export default {
    name: 'incident_reporting_create',
    components: {
        ListActionBoards,
        ListActions,
        ListLogs,
    },
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
        }
        this.$store.commit('IncidentReporting/setIRType', 2);
        this.$store.dispatch('IncidentReporting/callGetIR').then(() => {
            this.$store.dispatch('IncidentReporting/callSetSelectedIR', IR).then(() => {
                this.setScenarioId(this.$store.getters['IncidentReporting/getSelectedIR'])
            })
        });
        this.$store.commit('IncidentReporting/setIRType', 3);
        this.$store.dispatch('RiskRepository/callGetClients');
        this.$store.dispatch('SettingsIncident/ActionBoardStatuses/callList');
        this.$store.dispatch('IncidentReporting/callGetEmployees');
        this.$store.dispatch('IncidentReporting/callGetIROptions');
        this.$store.dispatch('IncidentReporting/callGetIRActions');
    },
    watch: {
        $route() {
            this.$store.commit('IncidentReporting/setRelatedIR', null);
            this.$store.dispatch('IncidentReporting/callSetSelectedIR', null)
        }
    },
    methods: {
        save() {
            this.$store.dispatch('IncidentReporting/callStoreIR', false).then()
            this.$router.push(`/incident_reporting/list`)
        },
        setRelatedIR(id) {
            this.$store.commit(
                'IncidentReporting/setRelatedIR',
                this.$store.getters['IncidentReporting/getIR'].find(item => item.id === id)
            );
        },
        setScenarioId(data) {
            if (data.action_boards.length === 0) {
                return
            }
            data.scenario_id = data.action_boards
                .filter(item => item.type_id === 2)
                .reverse()[0]?.id
            if (data.scenario_id) {
                this.$store.commit(
                    'IncidentReporting/setRelatedIR',
                    this.$store.getters['IncidentReporting/getIR'].find(item => item.id === data.scenario_id)
                )
            }
        },
    },
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
    font-weight: 400;
    letter-spacing: normal;
    font-size: 20px;
    text-align: center;
}

</style>
