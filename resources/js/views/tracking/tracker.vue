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

<!--        panel-->
        <template>
            <v-card flat>
                <v-toolbar>
                    <v-row class="child-flex">
                        <v-col cols="11" md="11" sm="10" v-if="mode">
<!--                            Timer mode-->
                            <v-row>
                                <v-col cols="12" md="4" sm="8">
                                    <v-text-field
                                        outlined
                                        dense
                                        hide-details="auto"
                                        placeholder="What are you working on?"
                                        v-model="timerPanel.description"
                                    ></v-text-field>
                                </v-col>
                                <v-spacer></v-spacer>
                                <v-col cols="12" md="2" sm="4" class="text-right">
                                    <template>
                                        <v-menu
                                            ref="menuProject"
                                            v-model="menuProject"
                                            :close-on-content-click="false"
                                            :nudge-width="200"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-btn
                                                    tile
                                                    small
                                                    text
                                                    :color="themeColor"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                >
                                                    <v-icon center v-if="!timerPanel.project">
                                                        mdi-plus-circle-outline
                                                    </v-icon>
                                                    <span v-if="!timerPanel.project">
                                    &nbsp;&nbsp;Project
                                </span>
                                                    <span v-if="timerPanel.project">
                                    {{timerPanel.project.name}}
                                </span>
                                                </v-btn>
                                            </template>
                                            <v-card>
                                                <v-autocomplete
                                                    v-model="timerPanel.project"
                                                    :items="getFilteredProjects"
                                                    :loading="isLoadingSearchProject"
                                                    :search-input.sync="search"
                                                    color="white"
                                                    hide-no-data
                                                    hide-selected
                                                    item-text="name"
                                                    item-value="id"
                                                    label="Projects"
                                                    placeholder="Start typing to Search"
                                                    prepend-icon="mdi-database-search"
                                                    return-object
                                                ></v-autocomplete>
                                            </v-card>
                                        </v-menu>
                                    </template>

                                    <v-btn
                                        icon
                                        x-small
                                        fab
                                        :color="themeColor"
                                    >
                                        <v-icon center>
                                            mdi-tag-outline
                                        </v-icon>
                                    </v-btn>
                                    <v-btn
                                        fab
                                        :icon="!timerPanel.billable"
                                        x-small
                                        :color="themeColor"
                                        @click="timerPanel.billable = !timerPanel.billable"
                                    >
                                        <v-icon center>
                                            mdi-currency-usd
                                        </v-icon>
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" md="1">
                                    <v-text-field
                                        v-model="timerPanel.passedSeconds"
                                        placeholder="00:00:00"
                                        dense
                                        hide-details="auto"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" md="1">
                                    <v-btn
                                        tile
                                        small
                                        :color="!timerPanel.start ? themeColor : 'error'"
                                        style="color: white"
                                        @click="actionStartNewTrack()"
                                    >
                                        <span v-if="!timerPanel.start">Start</span>
                                        <span v-if="timerPanel.start">Stop</span>
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-col>

                        <v-col cols="11" md="11" sm="10" v-if="!mode">
<!--                            Manual mode-->
                            <v-row>
                                <v-col cols="12" md="4" sm="8">
                                    <v-text-field
                                        outlined
                                        dense
                                        hide-details="auto"
                                        placeholder="What are you working on?"
                                        v-model="manualPanel.description"
                                    ></v-text-field>
                                </v-col>
                                <v-spacer></v-spacer>
                                <v-col cols="12" md="2" sm="4" class="text-right">
                                    <template>
                                        <v-menu
                                            ref="menuProject"
                                            v-model="menuProject"
                                            :close-on-content-click="false"
                                            :nudge-width="200"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-btn
                                                    tile
                                                    small
                                                    text
                                                    :color="themeColor"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                >
                                                    <v-icon center v-if="!manualPanel.project">
                                                        mdi-plus-circle-outline
                                                    </v-icon>
                                                    <span v-if="!manualPanel.project">
                                    &nbsp;&nbsp;Project
                                </span>
                                                    <span v-if="manualPanel.project">
                                    {{manualPanel.project.name}}
                                </span>
                                                </v-btn>
                                            </template>
                                            <v-card>
                                                <v-autocomplete
                                                    v-model="manualPanel.project"
                                                    :items="getFilteredProjects"
                                                    :loading="isLoadingSearchProject"
                                                    :search-input.sync="search"
                                                    color="white"
                                                    hide-no-data
                                                    hide-selected
                                                    item-text="name"
                                                    item-value="id"
                                                    label="Projects"
                                                    placeholder="Start typing to Search"
                                                    prepend-icon="mdi-database-search"
                                                    return-object
                                                ></v-autocomplete>
                                            </v-card>
                                        </v-menu>
                                    </template>

                                    <v-btn
                                        icon
                                        x-small
                                        fab
                                        :color="themeColor"
                                    >
                                        <v-icon center>
                                            mdi-tag-outline
                                        </v-icon>
                                    </v-btn>
                                    <v-btn
                                        :icon="!manualPanel.billable"
                                        x-small
                                        :color="themeColor"
                                        fab
                                        @click="manualPanel.billable = !manualPanel.billable"
                                    >
                                        <v-icon center>
                                            mdi-currency-usd
                                        </v-icon>
                                    </v-btn>
                                </v-col>
                                <v-col cols="12" md="1">
                                    <template>
                                        <v-menu
                                            ref="menuFrom"
                                            v-model="timeFromPicker"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            max-width="290px"
                                            min-width="290px"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    dense
                                                    v-model="timeFrom"
                                                    label="From"
                                                    placeholder="hh:mm"
                                                    prepend-icon="mdi-clock-time-four-outline"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    hide-details="auto"
                                                    style="max-width: 100px"
                                                    @blur="setTimeFromHandler()"
                                                ></v-text-field>
                                            </template>
                                            <v-time-picker
                                                dense
                                                v-if="timeFromPicker"
                                                v-model="timeFrom"
                                                full-width
                                                @click:minute="$refs.menuFrom.save(timeFrom)"
                                            ></v-time-picker>
                                        </v-menu>
                                    </template>
                                </v-col>
                                <v-col cols="12" md="1">
                                    <template>
                                        <v-menu
                                            ref="menuTo"
                                            v-model="timeToPicker"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            max-width="290px"
                                            min-width="290px"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    dense
                                                    v-model="timeTo"
                                                    label="To"
                                                    placeholder="hh:mm"
                                                    prepend-icon="mdi-clock-time-four-outline"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    hide-details="auto"
                                                    style="max-width: 100px"
                                                    @blur="setTimeToHandler()"
                                                ></v-text-field>
                                            </template>
                                            <v-time-picker
                                                dense
                                                v-if="timeToPicker"
                                                v-model="timeTo"
                                                full-width
                                                @click:minute="$refs.menuTo.save(timeTo)"
                                            ></v-time-picker>
                                        </v-menu>
                                    </template>
                                </v-col>
                                <v-col cols="12" md="1">
                                    <template>
                                        <v-menu
                                            v-model="createDatePicker"
                                            :close-on-content-click="false"
                                            :nudge-right="40"
                                            transition="scale-transition"
                                            offset-y
                                            min-width="auto"
                                            left
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-text-field
                                                    dense
                                                    v-model="date"
                                                    label="Date"
                                                    placeholder="yyyy-mm-dd"
                                                    prepend-icon="mdi-calendar"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    hide-details="auto"
                                                    style="max-width: 1050px"
                                                    @blur="setDateHandler()"
                                                ></v-text-field>
                                            </template>
                                            <v-date-picker
                                                dense
                                                v-model="date"
                                                @input="createDatePicker = false"
                                            ></v-date-picker>
                                        </v-menu>
                                    </template>
                                </v-col>
                                <v-col cols="12" md="1">
                                    <v-text-field
                                        v-model="timeAdd"
                                        placeholder="00:00:00"
                                        dense
                                        readonly
                                        hide-details="auto"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12" md="1">
                                    <v-btn
                                        small
                                        tile
                                        :color="themeColor"
                                        :loading="loadingCreateTrack"
                                        :disabled="loadingCreateTrack"
                                        style="color: white"
                                        @click="actionCreateTrack()"
                                    >
                                        Add
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-col>
                        <v-col cols="1" md="1" sm="2" class="pt-6">
                            <v-tooltip top>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        fab
                                        x-small
                                        :icon="!mode"
                                        :color="themeColor"
                                        @click="mode=true"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon>mdi-clock</v-icon>
                                    </v-btn>
                                </template>
                                <span>Timer</span>
                            </v-tooltip>
                            <v-tooltip top>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        fab
                                        x-small
                                        :icon="mode"
                                        :color="themeColor"
                                        @click="mode=false"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon>mdi-format-list-bulleted-square</v-icon>
                                    </v-btn>
                                </template>
                                <span>Manual</span>
                            </v-tooltip>
                        </v-col>
                    </v-row>
                </v-toolbar>
            </v-card>
        </template>

<!--        dateRangePicker-->
        <template>
            <v-row>
                <v-col
                    cols="12"
                    offset-md="7"
                    md="4"
                    offset-lg="9"
                    lg="2"
                >
                    <v-menu
                        ref="dateRangePicker"
                        v-model="dateRangePicker"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                        class="float-right"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="dateRangeText"
                                label="Date range"
                                persistent-hint
                                prepend-icon="mdi-calendar"
                                v-bind="attrs"
                                v-on="on"
                                hide-details="auto"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="dateRange"
                            range
                            no-title
                            @input="dateRangeHandler()"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
            </v-row>
        </template>

        <br>

<!--        dataTable-->
        <template>
            <v-expansion-panels
                v-model="panels"
                multiple
            >
                <v-expansion-panel
                    v-for="(panelDate, index) in getPanelDates"
                    :key="index"
                >
                    <v-expansion-panel-header
                        :color="themeColor"
                        style="color: white"
                    >
                        <span v-if="moment(panelDate).format(dateFormat) === moment().format(dateFormat)">Today</span>
                        <span v-else>{{ moment(panelDate).format('ddd, DD MMM YYYY')}}</span>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <template>
                            <div>
                                <v-data-table
                                    hide-default-footer
                                    :headers="headers"
                                    :items="filterTracking(panelDate)"
                                    :items-per-page="20"
                                    class="elevation-1"
                                >
                                    <template v-slot:item.description="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.description"
                                            @save="save(props.item.id, 'description')"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            <span class="text--secondary" v-if="!props.item.description">
                                                Add description
                                            </span>
                                            {{ props.item.description }}
                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.description"
                                                    :rules="[validators.max255chars]"
                                                    label="Description"
                                                    hint="Description"
                                                    single-line
                                                    counter
                                                    @blur="forceSave(props.item, 'description', props.item.description)"
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.project="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.project"
                                            persistent
                                            @save="save(props.item.id, 'project')"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            <span class="text--secondary" v-if="!props.item.project">
                                                Add project
                                            </span>
                                            <div>{{ props.item.project }}</div>
                                            <template v-slot:input>
                                                <div class="mt-4 title">
                                                    Update Project
                                                </div>
                                                <v-text-field
                                                    v-model="props.item.project"
                                                    :rules="[validators.max255chars]"
                                                    label="Edit"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.date_from="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.date_from"
                                            @save="save(props.item.id, 'date_from')"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            {{ moment(props.item.date_from).format(timeFormat) }}
                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.date_from"
                                                    :rules="[validators.max255chars]"
                                                    label="Edit"
                                                    single-line
                                                    counter
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.date_to="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.date_to"
                                            @save="save(props.item.id, 'date_to')"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                            v-if="props.item.status == 'stopped'"
                                        >
                                            <span v-if="props.item.date_to && props.item.status == 'stopped'">
                                                {{ moment(props.item.date_to).format(timeFormat) }}
                                            </span>
                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.date_to"
                                                    :rules="[validators.max255chars]"
                                                    label="Edit"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.passed="{ item }">
                                        <span v-text="helperConvertSecondsToTimeFormat(item.passed)"></span>
                                    </template>
                                    <template v-slot:item.actions="props">
                                        <v-row>
                                            <v-col cols="6">
                                                <v-btn
                                                    depressed
                                                    color="error"
                                                    v-if="props.item.status == 'started'"
                                                    @click="actionStopTracking(props.item.id)"
                                                >
                                                    <v-icon>mdi-pause</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    depressed
                                                    :color="themeColor"
                                                    v-if="props.item.status == 'stopped'"
                                                >
                                                    <v-icon>mdi-play-outline</v-icon>
                                                </v-btn>
                                            </v-col>
                                            <v-col cols="6">
                                                <v-menu
                                                    bottom
                                                    left
                                                >
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-btn
                                                            :color="themeColor"
                                                            icon
                                                            v-bind="attrs"
                                                            v-on="on"
                                                        >
                                                            <v-icon>mdi-dots-vertical</v-icon>
                                                        </v-btn>
                                                    </template>
                                                    <v-card
                                                        class="mx-auto"
                                                        max-width="300"
                                                        tile
                                                    >
                                                        <v-list dense>
                                                            <v-subheader>Actions</v-subheader>
                                                            <v-list-item-group
                                                                color="primary"
                                                            >
                                                                <v-list-item>
                                                                    <v-list-item-icon>
                                                                        <v-icon>mdi-content-duplicate</v-icon>
                                                                    </v-list-item-icon>
                                                                    <v-list-item-content>
                                                                        <v-list-item-title
                                                                            @click="actionDuplicateTracking(props.item.id)"
                                                                        >
                                                                            Duplicate
                                                                        </v-list-item-title>
                                                                    </v-list-item-content>
                                                                </v-list-item>
                                                                <v-list-item>
                                                                    <v-list-item-icon>
                                                                        <v-icon>mdi-trash-can-outline</v-icon>
                                                                    </v-list-item-icon>
                                                                    <v-list-item-content>
                                                                        <v-list-item-title
                                                                            @click="actionDeleteTracking(props.item.id)"
                                                                        >
                                                                            Delete
                                                                        </v-list-item-title>
                                                                    </v-list-item-content>
                                                                </v-list-item>
                                                            </v-list-item-group>
                                                        </v-list>
                                                    </v-card>
                                                </v-menu>
                                            </v-col>
                                        </v-row>
                                    </template>
                                </v-data-table>

                            </div>
                        </template>
                    </v-expansion-panel-content>
                </v-expansion-panel>
            </v-expansion-panels>
        </template>

    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import moment from "moment";
import _ from "lodash";

export default {
    data() {
        return {
            dateFormat: 'YYYY-MM-DD',
            timeFormat: 'HH:mm',
            langMap: this.$store.state.lang.lang_map,
            themeColor: this.$store.state.themeColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            /* Toolbar */
            mode: true,
            loadingCreateTrack: false,
            loadingUpdateTrack: false,
            loadingDeleteTrack: false,
            timeFromPicker: false,
            timeToPicker: false,
            timeFrom: null,
            timeTo: null,
            date: null,
            menuProject: false,
            search: '',
            isLoadingSearchProject: false,
            createDatePicker: false,
            /* Date range picker */
            dateRangePicker: false,
            dateRange: [],
            /* Data table */
            headers: [
                {
                    text: 'Description',
                    align: 'start',
                    value: 'description',
                    width: '15%'
                },
                {
                    text: 'Company',
                    value: 'project.client.name',
                    width: '15%'
                },
                {
                    text: 'Project name',
                    value: 'project.name',
                    width: '15%'
                },
                {
                    text: 'Tag',
                    value: '',
                    width: '15%'
                },
                {
                    text: 'Start',
                    value: 'date_from',
                    width: '10%'
                },
                {
                    text: 'End',
                    value: 'date_to',
                    width: '10%'
                },
                {
                    text: 'Passed',
                    value: 'passed',
                    width: '10%'
                },
                {
                    text: 'Actions',
                    value: 'actions',
                    sortable: false
                }
            ],
            panels: [0],
            /* Data */
            tracking: [],
            projects: [],
            /* Validators */
            validators: {
                max255chars: v => v.length <= 255 || 'Input too long!'
            },
            manualPanel: {
                description: null,
                project: null,
                tags: [],
                billable: false,
                date_from: moment(),
                date_to: null,
                date: moment(),
                status: 'started',
                timeStart: '00:00:00'
            },
            nameLimit: 255,
            timerPanel: {
                trackId: null,
                passedSeconds: '00:00:00',
                start: null,
                billable: false,
                description: null,
                project: null,
                tags: [],
                status: 'started',
                date_from: moment(),
                date_to: null,
                date: moment()
            },
            globalTimer: null
        }
    },
    created: function () {
        this.debounceGetTacking = _.debounce(this.__getTracking, 500);
        this.dateRange = [
            moment().subtract(1, 'days').format(this.dateFormat),
            moment().format(this.dateFormat)
        ];
        this.timeFrom = moment().add(1, 'minutes').format(this.timeFormat);
        this.timeTo = moment().add(15, 'minutes').format(this.timeFormat);
        this.date = moment().format(this.dateFormat);
    },
    mounted() {
        this.__globalTimer();
        this.debounceGetTacking();
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
    },
    methods: {
        __globalTimer() {
            return setTimeout(() => {
                this.globalTimer = moment();
                this.__globalTimer();
            }, 1000);
        },
        __getTracking() {
            this.loading = true;
            const queryParams = new URLSearchParams({
                date_from: this.dateRange[0] || null,
                date_to: this.dateRange[1] || null
            });
            return axios.get(`/api/tracking/tracker?${queryParams.toString()}`)
                .then(({ data }) => {
                    this.tracking = data.data;
                    this.loading = false;
                    return data;
                })
                .catch(e => {
                    this.debounceGetTacking();
                });
        },
        __createTracking(data) {
            this.loadingCreateTrack = true;
            return axios.post('/api/tracking/tracker', data)
                .then(({ data }) => {
                    this.debounceGetTacking();
                    this.resetManualPanel();
                    this.loadingCreateTrack = false;
                    return data;
                });
        },
        __updateTrackingById(id, data) {
            this.loadingUpdateTrack = true;
            return axios.put(`/api/tracking/tracker/${id}`, data)
                .then(({ data }) => {
                    if (!data.success) {
                        return false;
                    }
                    this.debounceGetTacking();
                    this.resetManualPanel();
                    this.loadingUpdateTrack = false;
                    return data;
                });
        },
        __deleteTrackingById(id) {
            this.loadingDeleteTrack = true;
            return axios.delete(`/api/tracking/tracker/${id}`)
                .finally(e => {
                    this.debounceGetTacking();
                    this.loadingDeleteTrack = false;
                });
        },
        __duplicateTracking(id) {
            return axios.post(`/api/tracking/tracker/${id}/duplicate`)
                .then(({ data }) => {
                    this.debounceGetTacking();
                    return data;
                });
        },
        dateRangeHandler() {
            if (this.dateRange.length === 2) {
                this.dateRange.sort();
                this.dateRangePicker = false;
                this.debounceGetTacking();
            }
        },
        // parseTime(time) {
        //     if (!time) return null
        //     time = this.helperAddZeros(time.replace(/\D+/g, ''), 4);
        //     return time.slice(0, 2), ':', time.slice(2);
        // },
        helperAddZeros(num, len) {
            while((""+num).length < len) num = "0" + num;
            return num.toString();
        },
        resetManualPanel() {
            this.manualPanel = {
                description: null,
                projectId: null,
                tags: [],
                billable: false,
                date_from: moment().format(this.timeFormat),
                date_to: moment().format(this.timeFormat),
                date: moment().format(this.dateFormat),
                status: 'started',
                timeStart: '00:00:00'
            };
        },
        resetTimerPanel() {
            this.timerPanel = {
                trackId: null,
                passedSeconds: '00:00:00',
                start: null,
                billable: false,
                description: null,
                project: null,
                tags: [],
                status: 'started',
                date_from: moment(),
                date_to: null,
                date: moment()
            };
        },
        actionCreateTrack() {
            this.manualPanel.status = 'stopped';
            this.__createTracking(this.manualPanel);
        },
        actionStartNewTrack() {
            // start
            if (!this.timerPanel.start) {
                this.timerPanel.trackId = null;
                this.timerPanel.status = 'started';
                this.timerPanel.start = moment();
                this.timerPanel.date = moment();
                this.timerPanel.date_from = moment();
                this.timerPanel.date_to = null;
                this.timerPanel.timeStart = this.timerPanel.start;
                this.loadingCreateTrack = true;
                this.__createTracking(this.timerPanel)
                    .then(data => {
                        if (data.success) {
                            this.timerPanel.trackId = data.data.id;
                        }
                    });
            } else {
                // stop
                this.timerPanel.date = this.timerPanel.start;
                this.timerPanel.date_from = this.timerPanel.start;
                this.timerPanel.date_to = moment();
                this.timerPanel.timeStart = this.timerPanel.start;
                this.timerPanel.start = null;
                this.timerPanel.status = 'stopped';
                const index = this.tracking.findIndex(i => i.id === this.timerPanel.trackId);
                this.tracking[index].status = 'stopped';
                if (this.timerPanel.trackId) {
                    this.__updateTrackingById(this.timerPanel.trackId, this.timerPanel);
                } else {
                    this.__createTracking(this.timerPanel);
                }
                this.resetTimerPanel();
            }
        },
        setTimeFromHandler() {
            if (!this.timeFrom) {
                this.timeFrom = moment().format(this.timeFormat);
            }
            if (/([0-9]{1,}:[0-9]{2})/.test(this.timeFrom)) {
                return this.timeFrom;
            }
            if (/(\d{1,4})/.test(this.timeFrom)) {
                let str = this.timeFrom.toString().slice(0,4);
                str = this.helperAddZeros(str, 4);
                this.timeFrom = str.slice(0,2) + ':' + str.slice(-2);
                return this.timeFrom;
            }
            this.timeFrom = moment().format(this.timeFormat);
            return this.timeFrom;
        },
        setTimeToHandler() {
            if (!this.timeTo) {
                this.timeTo = moment().format(this.timeFormat);
            }
            if (/([0-9]{1,}:[0-9]{2})/.test(this.timeTo)) {
                return this.timeTo;
            }
            if (/(\d{1,4})/.test(this.timeTo)) {
                let str = this.timeTo.toString().slice(0,4);
                str = this.helperAddZeros(str, 4);
                this.timeTo = str.slice(0,2) + ':' + str.slice(-2);
                return this.timeTo;
            }
            this.timeTo = moment().format(this.timeFormat);
            return this.timeTo;
        },
        setDateHandler() {
            if (!this.date || ! /([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})/.test(this.date)) {
                this.date = moment().format(this.dateFormat);
            }
            return this.date;
        },
        helperConvertSecondsToTimeFormat(seconds) {
            if (!seconds) {
                return `00:00:00`;
            }
            const h = Math.floor(seconds / 60 / 60);
            const m = Math.floor((seconds - h * 60 * 60) / 60);
            const s = seconds - (m * 60) - (h * 60 * 60);
            return `${this.helperAddZeros(h,2)}:${this.helperAddZeros(m,2)}:${this.helperAddZeros(s,2)}`;
        },
        forceSave(item, fieldName, newValue) {
            const foundIndex = this.tracking.findIndex(function(i) {
                return i.id === item.id;
            });
            this.tracking[foundIndex][fieldName] = newValue;
        },
        save (id, fieldName) {
            const foundIndex = this.tracking.findIndex(i => i.id === id);
            if (['date_from', 'date_to'].indexOf(fieldName)) {
                const {date_from, date_to} = this.tracking[foundIndex];
                this.tracking[foundIndex].passed = this.helperCalculatePassedTime(date_from, date_to);
            }
            this.__updateTrackingById(id, this.tracking[foundIndex]);
        },
        cancel () {
            //TODO
        },
        open () {
            //TODO
        },
        close () {
            //TODO
        },
        helperCalculatePassedTime(date_from, date_to) {
            if (moment(date_from) > moment(date_to)) {
                date_to = moment(date_to).add(1, 'day');
            }
            const seconds = moment(date_to).diff(moment(date_from), 'seconds');
            return seconds;
        },
        filterTracking(date) {
            const self = this;
            return this.tracking.filter(function(item) {
               return moment(item.date_from).format(self.dateFormat) === date;
            });
        },
        actionDuplicateTracking(trackerId) {
            this.__duplicateTracking(trackerId);
        },
        actionDeleteTracking(trackerId) {
            this.__deleteTrackingById(trackerId);
        },
        actionStopTracking(trackerId) {
            if (this.timerPanel.trackId === trackerId) {
                this.timerPanel.start = null;
                this.resetTimerPanel();
                const index = this.tracking.findIndex(i => i.id === trackerId);
                this.tracking[index].status = 'stopped';
                this.tracking[index].date_to = moment();
            }
            this.__updateTrackingById(trackerId, {
                status: 'stopped',
                date_to: moment()
            });
        },
    },
    computed: {
        timeAdd () {
            if (moment(this.manualPanel.date_from) > moment(this.manualPanel.date_to)) {
                this.manualPanel.date_to = moment(this.manualPanel.date_to).add(1, 'day');
            }
            const seconds = this.helperCalculatePassedTime(this.manualPanel.date_from, this.manualPanel.date_to);
            return this.helperConvertSecondsToTimeFormat(seconds);
        },
        dateRangeText () {
            return this.dateRange.join(' ~ ')
        },
        getPanelDates () {
            const items = this.tracking.reduce(function (acc, item) {
                const date = moment(item.date_from).format('YYYY-MM-DD');
                return [...acc, date.toString()];
            }, []);
            const panels = [...new Set(items)].sort().reverse();
            this.panels = panels.map((i,k) => k);
            return panels;
        },
        getItems () {
            return this.tracking;
        },
        getFilteredProjects() {
            return this.projects.map(entry => {
                const name = entry.name.length > this.nameLimit
                    ? entry.name.slice(0, this.nameLimit) + '...'
                    : entry.name

                return Object.assign({}, entry, { name })
            })
        }
    },
    watch: {
        timeFrom: function () {
            this.manualPanel.date_from = moment(this.date)
                .hours(this.timeFrom.toString().split(':')[0])
                .minutes(this.timeFrom.toString().split(':')[1]);
        },
        timeTo: function () {
            this.manualPanel.date_to = moment(this.date)
                .hours(this.timeTo.toString().split(':')[0])
                .minutes(this.timeTo.toString().split(':')[1]);
        },
        date: function () {
            this.manualPanel.date = moment(this.date);
            this.manualPanel.date_from = moment(this.date)
                .hours(this.timeFrom.toString().split(':')[0])
                .minutes(this.timeFrom.toString().split(':')[1]);
            let dayToAdding = 0;
            if (moment(this.manualPanel.date_to).format(this.dateFormat) > moment(this.manualPanel.date_from).format(this.dateFormat)) {
                dayToAdding = 1;
            }
            this.manualPanel.date_to = moment(this.date)
                .add(dayToAdding, 'day')
                .hours(this.timeTo.toString().split(':')[0])
                .minutes(this.timeTo.toString().split(':')[1]);
        },
        search () {
            if (this.projects.length > 0) return;
            if (this.isLoadingSearchProject) return;
            this.isLoadingSearchProject = true;
            const queryParams = new URLSearchParams({
                search: this.search ?? ''
            });
            axios.get(`/api/tracking/projects?${queryParams}`)
                .then(({data}) => {
                    this.projects = data.data.data;
                })
                .finally(() => (this.isLoadingSearchProject = false));
1        },
        globalTimer: function () {
            // Update DataTable passed field
            const tracking = this.tracking.filter(i => {
                return i.status === 'started';
            });
            tracking.forEach(i => {
                const index = this.tracking.indexOf(i);
                this.tracking[index].passed = this.helperCalculatePassedTime(i.date_from, moment());
            });
            if (this.timerPanel.start) {
                const seconds = moment().diff(moment(this.timerPanel.start), 'seconds');
                this.timerPanel.passedSeconds = this.helperConvertSecondsToTimeFormat(seconds);
            }
        }
    },
}
</script>
