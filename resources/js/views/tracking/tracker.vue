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
                    <v-row>
                        <v-col cols="11" lg="11" md="10" sm="10" v-if="mode">
<!--                            Timer mode-->
                            <v-row>
                                <v-col cols="4" lg="4" md="4">
                                    <v-text-field
                                        outlined
                                        dense
                                        hide-details="auto"
                                        placeholder="What are you working on?"
                                        v-model="timerPanel.description"
                                    ></v-text-field>
                                </v-col>
                                <v-spacer></v-spacer>
                                <v-col cols="2" lg="3" md="3" class="text-right">
                                    <ProjectBtn
                                        key="timerPanelProjectKey"
                                        :color="themeColor"
                                        :onChoosable="handlerProjectTimerPanel"
                                        v-model="timerPanel.project"
                                    ></ProjectBtn>

                                    <TagBtn
                                        key="timerPanelTagKey"
                                        :color="themeColor"
                                        :onChoosable="handlerTagsTimerPanel"
                                        v-model="timerPanel.tags"
                                    >
                                    </TagBtn>

                                    <v-btn
                                        fab
                                        :icon="!timerPanel.billable"
                                        x-small
                                        :color="themeColor"
                                        @click="timerPanel.billable = !timerPanel.billable"
                                    >
                                        <v-icon center v-bind:class="{ 'white--text': timerPanel.billable }">
                                            mdi-currency-usd
                                        </v-icon>
                                    </v-btn>
                                </v-col>
                                <v-col cols="1" lg="2" md="2">
                                    <v-text-field
                                        v-model="timerPanel.passedSeconds"
                                        placeholder="00:00:00"
                                        dense
                                        hide-details="auto"
                                        readonly
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="1" lg="1" md="1">
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

                        <v-col cols="11" lg="11" md="10" sm="10" v-if="!mode">
<!--                            Manual mode-->
                            <v-row>
                                <v-col cols="4" lg="3" md="3" sm="8">
                                    <v-text-field
                                        outlined
                                        dense
                                        hide-details="auto"
                                        placeholder="What are you working on?"
                                        v-model="manualPanel.description"
                                    ></v-text-field>
                                </v-col>
                                <v-spacer></v-spacer>
                                <v-col cols="2" lg="3" md="3" sm="4" class="text-right">
                                    <ProjectBtn
                                        key="manualPanelProjectKey"
                                        :color="themeColor"
                                        :onChoosable="handlerProjectManualPanel"
                                        v-model="manualPanel.project"
                                    ></ProjectBtn>

                                    <TagBtn
                                        key="manualPanelTagKey"
                                        :color="themeColor"
                                        :onChoosable="handlerTagsManualPanel"
                                        v-model="manualPanel.tags"
                                    >
                                    </TagBtn>

                                    <v-btn
                                        :icon="!manualPanel.billable"
                                        x-small
                                        :color="themeColor"
                                        fab
                                        @click="manualPanel.billable = !manualPanel.billable"
                                    >
                                        <v-icon center v-bind:class="{ 'white--text': manualPanel.billable }">
                                            mdi-currency-usd
                                        </v-icon>
                                    </v-btn>
                                </v-col>
                                <v-col cols="1" lg="1" md="1">
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
                                                <TimeField
                                                    v-model="manualPanel.date_from"
                                                    style="max-width: 100px"
                                                    label="From"
                                                    placeholder="hh:mm"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    format="HH:mm"

                                                ></TimeField>
                                            </template>
                                            <v-time-picker
                                                dense
                                                v-if="timeFromPicker"
                                                v-model="manualPanel.date_from"
                                                full-width
                                                @click:minute="$refs.menuFrom.save(manualPanel.date_from)"
                                            ></v-time-picker>
                                        </v-menu>
                                    </template>
                                </v-col>
                                <v-col cols="1" lg="1" md="1">
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
                                                <TimeField
                                                    v-model="manualPanel.date_to"
                                                    style="max-width: 100px"
                                                    label="To"
                                                    placeholder="hh:mm"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    format="HH:mm"

                                                ></TimeField>
                                            </template>
                                            <v-time-picker
                                                dense
                                                v-if="timeToPicker"
                                                v-model="manualPanel.date_to"
                                                full-width
                                                @click:minute="$refs.menuTo.save(manualPanel.date_to)"
                                            ></v-time-picker>
                                        </v-menu>
                                    </template>
                                </v-col>
                                <v-col cols="2" lg="2" md="2">
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
                                                    @blur="handlerSetDate()"
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
                                <v-col cols="1" lg="1" md="1">
                                    <v-text-field
                                        v-model="timeAdd"
                                        placeholder="00:00:00"
                                        dense
                                        readonly
                                        hide-details="auto"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="1" lg="1" md="1">
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
                        <v-col cols="1" lg="1" md="2" sm="2">
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
                                        <v-icon v-bind:class="{ 'white--text': mode }">mdi-clock</v-icon>
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
                                        <v-icon v-bind:class="{ 'white--text': !mode }">mdi-format-list-bulleted-square</v-icon>
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
                            @input="handlerDateRange()"
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
                                    :headers="headers"
                                    :items="filterTracking(panelDate)"
                                    :items-per-page="15"
                                    class="elevation-1"
                                >
                                    <template v-slot:item.description="props">
                                        <v-edit-dialog

                                            @save="save(props.item, 'description')"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="save(props.item, 'description')"
                                        >
                                            <span class="text--secondary" v-if="!props.item.description">
                                                Add description
                                            </span>
                                            {{ props.item.description }}
                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.description"
                                                    label="Description"
                                                    hint="Description"
                                                    single-line
                                                    counter
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.project.name="props">
                                        <v-edit-dialog
                                            @save="save(props.item, 'project', props.item.project)"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="save(props.item, 'project', props.item.project)"
                                        >
                                            <ProjectBtn
                                                :key="props.item.id"
                                                :color="themeColor"
                                                v-model="props.item.project"
                                                @blur="save(props.item, 'project', props.item.project)"
                                                @input="save(props.item, 'project', props.item.project)"
                                            ></ProjectBtn>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.tags="props">
                                        <TagBtn
                                            :key="props.item.id"
                                            :color="themeColor"
                                            v-model="props.item.tags"
                                            @blur="save(props.item, 'tags', props.item.tags)"
                                        ></TagBtn>
                                    </template>
                                    <template v-slot:item.billable="props">
                                        <v-btn
                                            fab
                                            :icon="!props.item.billable"
                                            x-small
                                            :color="themeColor"
                                            @click="props.item.billable = !props.item.billable; save(props.item, 'billable')"
                                        >
                                            <v-icon center v-bind:class="{ 'white--text': props.item.billable }">
                                                mdi-currency-usd
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <template v-slot:item.date_from="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.date_from"
                                        >
                                            {{ moment(props.item.date_from).format(timeFormat) }}
                                            <template v-slot:input>
                                                <TimeField
                                                    v-model="props.item.date_from"
                                                    style="max-width: 100px; height: 40px"
                                                    label="From"
                                                    placeholder="hh:mm"
                                                    format="HH:mm"
                                                    @input="save(props.item, 'date_from', props.item.date_from)"
                                                ></TimeField>
                                            </template>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.date_to="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.date_to"
                                            v-if="props.item.status == 'stopped'"
                                        >
                                            <span v-if="props.item.date_to && props.item.status == 'stopped'">
                                                {{ moment(props.item.date_to).format(timeFormat) }}
                                            </span>
                                            <template v-slot:input>
                                                <TimeField
                                                    v-model="props.item.date_to"
                                                    style="max-width: 100px; height: 40px"
                                                    label="To"
                                                    placeholder="hh:mm"
                                                    format="HH:mm"
                                                    @input="save(props.item, 'date_to', props.item.date_to)"
                                                ></TimeField>
                                            </template>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.passed="props">
                                        <span v-text="helperConvertSecondsToTimeFormat(props.item.passed)"></span>
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
                                                    <v-icon class="white--text">mdi-pause</v-icon>
                                                </v-btn>
                                                <v-btn
                                                    depressed
                                                    :color="themeColor"
                                                    v-if="props.item.status == 'stopped'"
                                                    @click="actionStartTrackingAsId(props.item.id)"
                                                >
                                                    <v-icon class="white--text">mdi-play-outline</v-icon>
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
                                                            <v-list-item-group
                                                                color="primary"
                                                            >
                                                                <v-list-item>
                                                                    <v-list-item-content>
                                                                        <v-list-item-title
                                                                            @click="actionDuplicateTracking(props.item.id)"
                                                                        >
                                                                            Duplicate
                                                                        </v-list-item-title>
                                                                    </v-list-item-content>
                                                                </v-list-item>
                                                                <v-list-item>
                                                                    <v-list-item-content>
                                                                        <v-list-item-title
                                                                            @click="actionDeleteTracking(props.item.id)"
                                                                            style="color: red"
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
import moment from "moment-timezone";
import _ from "lodash";
import ProjectBtn from "./components/project-btn";
import TagBtn from "./components/tag-btn";
import TimeField from "./components/time-field";

export default {
    components: {
        ProjectBtn,
        TagBtn,
        TimeField
    },
    data() {
        return {
            attemptRepeat: 0,
            tz: {
                name: moment.tz.guess(),
                offset: new Date().getTimezoneOffset()
            },
            dateFormat: 'YYYY-MM-DD',
            timeFormat: 'HH:mm',
            langMap: this.$store.state.lang.lang_map,
            themeColor: this.$store.state.themeColor,
            /* Snackbar */
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
                    width: '25%'
                },
                {
                    text: 'Company',
                    value: 'project.client.name',
                    width: '20%'
                },
                {
                    text: 'Project name',
                    value: 'project.name',
                    width: '20%'
                },
                {
                    text: 'Tag',
                    value: 'tags',
                    width: '5%'
                },
                {
                    text: 'Billable',
                    value: 'billable',
                    width: '5%'
                },
                {
                    text: 'Start',
                    value: 'date_from',
                    width: '5%'
                },
                {
                    text: 'End',
                    value: 'date_to',
                    width: '5%'
                },
                {
                    text: 'Passed',
                    value: 'passed',
                    width: '5%'
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
            manualPanel: {
                description: null,
                project: null,
                tags: [],
                billable: false,
                date_from: moment().format(),
                date_to: moment().add(15, 'minutes').format(),
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
            globalTimer: null,
            isLoadingTags: false,
            isLoadingProject: false
        }
    },
    created: function () {
        this.debounceGetTracking = _.debounce(this.__getTracking, 1000);
        this.dateRange = [
            moment().subtract(1, 'days').format(this.dateFormat),
            moment().format(this.dateFormat)
        ];
        this.timeFrom = moment().format();
        this.timeTo = moment().add(15, 'minutes').format();
        this.date = moment().format(this.dateFormat);
    },
    mounted() {
        this.__globalTimer();
        this.debounceGetTracking();
        this.$store.dispatch('Projects/getProjectList', { search: null });
        this.$store.dispatch('Products/getProductList', { search: null });
        this.$store.dispatch('Clients/getClientList', { search: null });
        this.$store.dispatch('Tags/getTagList');
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
            if (this.attemptRepeat > 5) return;
            this.loading = true;
            const queryParams = new URLSearchParams({
                date_from: this.dateRange[0] || null,
                date_to: this.dateRange[1] || null
            });
            return axios.get(`/api/tracking/tracker?${queryParams.toString()}`)
                .then(({ data }) => {
                    this.tracking = data.data;
                    this.loading = false;
                    this.attemptRepeat = 0;
                    return data;
                })
                .catch(e => {
                    this.attemptRepeat++;
                    this.debounceGetTracking();
                });
        },
        __createTracking(data) {
            this.loadingCreateTrack = true;
            return axios.post('/api/tracking/tracker', data)
                .then(({ data }) => {
                    if (!data.success) {
                        if (data.error) {
                            this.debounceGetTracking();
                            this.resetManualPanel();
                            this.loadingCreateTrack = false;
                            const error = Object.keys(data.error)[0];
                            this.snackbarMessage = data.error[error].pop();
                            this.actionColor = 'error'
                            this.snackbar = true;
                        }
                        return false;
                    }
                    this.debounceGetTracking();
                    this.resetManualPanel();
                    this.loadingCreateTrack = false;
                    return data;
                });
        },
        __updateTrackingById(id, data) {
            this.loadingUpdateTrack = true;
            return axios.patch(`/api/tracking/tracker/${id}`, data)
                .then(({ data }) => {
                    if (!data.success) {
                        if (data.error) {
                            this.debounceGetTracking();
                            this.resetManualPanel();
                            this.loadingUpdateTrack = false;
                            const error = Object.keys(data.error)[0];
                            this.snackbarMessage = data.error[error].pop();
                            this.actionColor = 'error'
                            this.snackbar = true;
                        }
                        return false;
                    }
                    this.debounceGetTracking();
                    this.resetManualPanel();
                    this.loadingUpdateTrack = false;
                    return data;
                });
        },
        __deleteTrackingById(id) {
            this.loadingDeleteTrack = true;
            return axios.delete(`/api/tracking/tracker/${id}`)
                .finally(e => {
                    this.debounceGetTracking();
                    this.loadingDeleteTrack = false;
                });
        },
        __duplicateTracking(id) {
            return axios.post(`/api/tracking/tracker/${id}/duplicate`)
                .then(({ data }) => {
                    this.debounceGetTracking();
                    return data;
                });
        },
        actionCreateTrack() {
            this.manualPanel.status = 'stopped';
            let data = this.manualPanel;
            data.date = data.date_from;
            data.date_from = moment(`${data.date_from}`).utc();
            data.date_to = moment(`${data.date_to}`).utc();
            this.__createTracking(data);
        },
        actionStartNewTrack() {
            // start
            if (!this.timerPanel.start) {
                this.timerPanel.trackId = null;
                this.timerPanel.status = 'started';
                this.timerPanel.start = moment().utc();
                this.timerPanel.date = moment().utc();
                this.timerPanel.date_from = moment().utc();
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
                this.timerPanel.date_to = moment().utc();
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
        actionStartTrackingAsId(trackerId) {
            this.__duplicateTracking(trackerId)
                .then(data => {
                    if (data.success) {
                        this.__updateTrackingById(data.data.id, {
                            date_from: moment(),
                            date_to: null,
                            status: 'started'
                        });
                    }
                });
        },
        helperAddZeros(num, len) {
            while((""+num).length < len) num = "0" + num;
            return num.toString();
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
        helperCalculatePassedTime(date_from, date_to) {
            if (moment(date_from) > moment(date_to)) {
                date_to = moment(date_to).add(1, 'day');
            }
            return moment(date_to).diff(moment(date_from), 'seconds');
        },
        resetManualPanel() {
            this.manualPanel = {
                description: null,
                projectId: null,
                tags: [],
                billable: false,
                date_from: moment().format(),
                date_to: moment().add(15, 'minutes').format(),
                date: moment().format(this.dateFormat),
                status: 'started',
                timeStart: this.helperConvertSecondsToTimeFormat(this.helperCalculatePassedTime(moment().format(), moment().add(15, 'minutes').format()))
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
        handlerDateRange() {
            if (this.dateRange.length === 2) {
                this.dateRange.sort();
                this.dateRangePicker = false;
                this.debounceGetTracking();
            }
        },
        // handlerSetTimeFrom() {
        //     if (!this.timeFrom) {
        //         this.timeFrom = moment().format(this.timeFormat);
        //     }
        //     if (/([0-9]{1,}:[0-9]{2})/.test(this.timeFrom)) {
        //         return this.timeFrom;
        //     }
        //     if (/(\d{1,4})/.test(this.timeFrom)) {
        //         let str = this.timeFrom.toString().slice(0,4);
        //         str = this.helperAddZeros(str, 4);
        //         this.timeFrom = str.slice(0,2) + ':' + str.slice(-2);
        //         return this.timeFrom;
        //     }
        //     this.timeFrom = moment().format(this.timeFormat);
        //     return this.timeFrom;
        // },
        // handlerSetTimeTo() {
        //     if (!this.timeTo) {
        //         this.timeTo = moment().format(this.timeFormat);
        //     }
        //     if (/([0-9]{1,}:[0-9]{2})/.test(this.timeTo)) {
        //         return this.timeTo;
        //     }
        //     if (/(\d{1,4})/.test(this.timeTo)) {
        //         let str = this.timeTo.toString().slice(0,4);
        //         str = this.helperAddZeros(str, 4);
        //         this.timeTo = str.slice(0,2) + ':' + str.slice(-2);
        //         return this.timeTo;
        //     }
        //     this.timeTo = moment().format(this.timeFormat);
        //     return this.timeTo;
        // },
        handlerSetDate() {
            if (!this.date || ! /([0-9]{4}-[0-9]{1,2}-[0-9]{1,2})/.test(this.date)) {
                this.date = moment().format(this.dateFormat);
            }
            return this.date;
        },
        handlerProjectTimerPanel(data) {
            this.timerPanel.project = data.project;
        },
        handlerProjectManualPanel(data) {
            this.manualPanel.project = data.project;
        },
        handlerTagsTimerPanel(data) {
            this.timerPanel.tags  = data.tags;
        },
        handlerTagsManualPanel(data) {
            this.manualPanel.tags  = data.tags;
        },
        filterTracking(date) {
            const self = this;
            return this.tracking.filter(function(item) {
                return moment(item.date_from).format(self.dateFormat) === date;
            });
        },
        save (item, fieldName, newValue = null) {
            if (['date_from', 'date_to'].indexOf(fieldName)) {
                item.passed = this.helperCalculatePassedTime(item.date_from, item.date_to);
            }
            if (newValue) {
                item[fieldName] = newValue;
            }
            this.__updateTrackingById(item.id, item);
        },
        cancel () {
            //TODO
        },
        open () {
            //TODO
        },
        close () {
            //TODO
        }
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
            return this.$store.getters['Projects/getProjects'].map(entry => {
                const name = entry.name.length > this.nameLimit
                    ? entry.name.slice(0, this.nameLimit) + '...'
                    : entry.name

                return Object.assign({}, entry, { name })
            })
        }
    },
    watch: {
        timeFrom: function () {
            this.manualPanel.date_from = this.timeFrom;
        },
        timeTo: function () {
            this.manualPanel.date_to = this.timeTo;
        },
        date: function () {
            this.manualPanel.date = moment(this.date);
            this.manualPanel.date_from = this.timeFrom;
            let dayToAdding = 0;
            if (moment(this.manualPanel.date_to).format(this.dateFormat) > moment(this.manualPanel.date_from).format(this.dateFormat)) {
                dayToAdding = 1;
            }
            this.manualPanel.date_to = moment(this.timeTo)
                .add(dayToAdding, 'day');
        },
        search () {
            this.$store.dispatch('Projects/getProjectList', { search: this.search });
        },
        globalTimer: function () {
            // Update DataTable passed field
            this.tracking.filter(i => {
                return i.status === 'started';
            }).forEach(i => {
                const index = this.tracking.indexOf(i);
                this.tracking[index].passed = this.helperCalculatePassedTime(i.date_from, moment());
            });
            // Update timerPanel
            if (this.timerPanel.start) {
                const seconds = moment().diff(moment(this.timerPanel.start), 'seconds');
                this.timerPanel.passedSeconds = this.helperConvertSecondsToTimeFormat(seconds);
            }
        }
    },
}
</script>
