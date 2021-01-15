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
            <v-card flat>
                <v-container fluid>
                    <v-row class="child-flex">
                        <v-toolbar>
                            <v-col cols="12" md="4" sm="8">
                                <v-text-field
                                    outlined
                                    dense
                                    hide-details="auto"
                                    placeholder="What are you working on?"
                                    v-model="trackForm.description"
                                ></v-text-field>
                            </v-col>
                            <v-spacer v-if="panel.activePanel"></v-spacer>
                            <v-col cols="12" md="2" sm="4" class="text-right">
                                <v-btn
                                    tile
                                    small
                                    text
                                    :color="themeColor"
                                >
                                    <v-icon center>
                                        mdi-plus-circle-outline
                                    </v-icon>
                                    &nbsp;&nbsp;Project
                                </v-btn>
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
                                    icon
                                    x-small
                                    fab
                                    :color="themeColor"
                                >
                                    <v-icon center>
                                        mdi-currency-usd
                                    </v-icon>
                                </v-btn>
                            </v-col>
                            <v-col cols="12" md="1" v-if="!panel.activePanel">
                                <v-menu
                                    ref="menuFrom"
                                    v-model="panel.timeFromPicker"
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
                                        v-if="panel.timeFromPicker"
                                        v-model="timeFrom"
                                        full-width
                                        @click:minute="$refs.menuFrom.save(timeFrom)"
                                    ></v-time-picker>
                                </v-menu>
                            </v-col>
                            <v-col cols="12" md="1" v-if="!panel.activePanel">
                                <v-menu
                                    ref="menuTo"
                                    v-model="panel.timeToPicker"
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
                                        v-if="panel.timeToPicker"
                                        v-model="timeTo"
                                        full-width
                                        @click:minute="$refs.menuTo.save(timeTo)"
                                    ></v-time-picker>
                                </v-menu>
                            </v-col>
                            <v-col cols="12" md="1" v-if="!panel.activePanel">
                                <v-menu
                                    v-model="panel.createDatePicker"
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
                                        @input="panel.createDatePicker = false"
                                    ></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col cols="12" md="1" v-if="panel.activePanel">
                                <v-text-field
                                    v-model="trackForm.timeStart"
                                    placeholder="00:00:00"
                                    dense
                                    hide-details="auto"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="1" v-if="!panel.activePanel">
                                <v-text-field
                                    v-model="timeAdd"
                                    placeholder="00:00:00"
                                    dense
                                    readonly
                                    hide-details="auto"
                                ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="1" v-if="panel.activePanel">
                                <v-btn
                                    tile
                                    small
                                    :color="themeColor"
                                    style="color: white"
                                    @click="startNewTrack()"
                                >
                                    Start
                                </v-btn>
                            </v-col>
                            <v-col cols="12" md="1" v-if="!panel.activePanel">
                                <v-btn
                                    small
                                    tile
                                    :color="themeColor"
                                    style="color: white"
                                    @click="createTrack()"
                                >
                                    Add
                                </v-btn>
                            </v-col>
                            <v-col cols="12" md="1" sm="2">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            fab
                                            x-small
                                            :icon="!panel.activePanel"
                                            :color="themeColor"
                                            @click="panel.activePanel=true"
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
                                            :icon="panel.activePanel"
                                            :color="themeColor"
                                            @click="panel.activePanel=false"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                            <v-icon>mdi-format-list-bulleted-square</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Manual</span>
                                </v-tooltip>
                            </v-col>
                        </v-toolbar>
                    </v-row>
                </v-container>
            </v-card>
        </template>

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
                        ref="panel.dateRangePicker"
                        v-model="panel.dateRangePicker"
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
                                    :items="getFilteredTracking(panelDate)"
                                >
                                    <template v-slot:item.passed="{ item }">
                                        <span v-text="convertSecondsToTimeFormat(item.passed)"></span>
                                    </template>
                                    <template v-slot:item.description="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.description"
                                            @save="save(props.item.id, 'description')"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            {{ props.item.description }}
                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.description"
                                                    :rules="[validators.max255chars]"
                                                    label="Edit"
                                                    single-line
                                                    counter
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
                                            large
                                            @save="save(props.item.id, 'date_to')"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            {{ moment(props.item.date_to).format(timeFormat) }}
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
                                    <template v-slot:item.iron="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.projectId"
                                            large
                                            persistent
                                            @save="save(props.item.id, 'project')"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            <div>{{ props.item.projectId }}</div>
                                            <template v-slot:input>
                                                <div class="mt-4 title">
                                                    Update Project
                                                </div>
                                                <v-text-field
                                                    v-model="props.item.projectId"
                                                    :rules="[validators.max255chars]"
                                                    label="Edit"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
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
            loading: false,
            panels: [0],
            panel: {
                activePanel: true,
                timeFromPicker: false,
                timeToPicker: false,
                dateRangePicker: false,
                createDatePicker: false
            },
            dateRange: [],
            headers: [
                {
                    text: 'Description',
                    align: 'start',
                    value: 'description'
                },
                {
                    text: 'Company',
                    value: 'project.client.name'
                },
                {
                    text: 'Project name',
                    value: 'project.name'
                },
                {
                    text: 'Tag',
                    value: ''
                },
                {
                    text: 'Start',
                    value: 'date_from'
                },
                {
                    text: 'End',
                    value: 'date_to'
                },
                {
                    text: 'Passed',
                    value: 'passed'
                },
                {
                    text: 'Actions',
                    value: 'actions'
                }
            ],
            tracking: [],
            timeFrom: null,
            timeTo: null,
            date: null,
            validators: {
                max255chars: v => v.length <= 255 || 'Input too long!'
            },
            trackForm: {
                description: null,
                projectId: null,
                tags: [],
                billable: false,
                dateFrom: moment().format(this.timeFormat),
                dateTo: moment().format(this.timeFormat),
                date: moment().format(this.dateFormat),
                status: 'started',
                timeStart: '00:00:00'
            }
        }
    },
    created: function () {
        this.debounceGetTacking = _.debounce(this.getTracking, 500);
        this.dateRange = [
            moment().subtract(1, 'days').format(this.dateFormat),
            moment().format(this.dateFormat)
        ];
        this.timeFrom = moment().add(1, 'minutes').format(this.timeFormat);
        this.timeTo = moment().add(15, 'minutes').format(this.timeFormat);
        this.date = moment().format(this.dateFormat);
    },
    mounted() {
        this.debounceGetTacking();
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
    },
    methods: {
        getTracking() {
            this.loading = true;
            const queryParams = new URLSearchParams({
                dateFrom: this.dateRange[0] || null,
                dateTo: this.dateRange[1] || null
            });
            axios.get(`/api/tracking/tracker?${queryParams.toString()}`)
                .then(({ data }) => {
                    this.tracking = data.data;
                    this.loading = false;
                });
        },
        dateRangeHandler() {
            if (this.dateRange.length === 2) {
                this.dateRange.sort();
                this.panel.dateRangePicker = false;
                this.debounceGetTacking();
            }
        },
        // parseTime(time) {
        //     if (!time) return null
        //     time = this.addZeros(time.replace(/\D+/g, ''), 4);
        //     return time.slice(0, 2), ':', time.slice(2);
        // },
        addZeros(num, len) {
            while((""+num).length < len) num = "0" + num;
            return num.toString();
        },
        resetTrack() {
            this.trackForm = {
                description: null,
                projectId: null,
                tags: [],
                billable: false,
                dateFrom: moment().format(this.timeFormat),
                dateTo: moment().format(this.timeFormat),
                date: moment().format(this.dateFormat),
                status: 'started',
                timeStart: '00:00:00'
            };
        },
        createTrack() {
            this.loading = true;
            this.trackForm.status = 'stopped';
            axios.post('/api/tracking/tracker', this.trackForm)
                .then(({ data }) => {
                    console.log(data);
                    this.loading = false;
                    this.debounceGetTacking();
                    this.resetTrack();
                });
        },
        startNewTrack() {
            this.loading = true;
            this.trackForm.status = 'started';
            //TODO
            this.debounceGetTacking();
            this.resetTrack();
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
                str = this.addZeros(str, 4);
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
                str = this.addZeros(str, 4);
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
        convertSecondsToTimeFormat(seconds) {
            const x = Math.floor(seconds / 60 / 60 / 60);
            const h = Math.floor(seconds / 60 / 60) - (x * 60);
            const m = Math.floor(seconds / 60) - (h * 60);
            const s = '00';
            return `${h}:${m}:${s}`;
        },
        save (id, fieldName) {
            if (['date_from', 'date_to'].indexOf(fieldName)) {
                const foundIndex = this.tracking.findIndex(function(i) {
                    return i.id === id;
                });
                const foundElement = this.tracking[foundIndex];
                this.tracking[foundIndex].passed = this.calculatePassedTime(foundElement.date_from, foundElement.date_to);
            }
        },
        cancel () {
            //TODO
        },
        open () {
            //TODO
        },
        close () {
            console.log('Dialog closed')
            //TODO
        },
        calculatePassedTime(dateFrom, dateTo) {
            if (moment(dateFrom) > moment(dateTo)) {
                dateTo = moment(dateTo).add(1, 'day');
            }
            return  moment(dateTo).diff(moment(dateFrom), 'seconds');
        },
        getFilteredTracking(date) {
            const self = this;
            return this.tracking.filter(function(item) {
               return moment(item.date_from).format(self.dateFormat) === date;
            });
        }
    },
    computed: {
        timeAdd () {
            if (moment(this.trackForm.dateFrom) > moment(this.trackForm.dateTo)) {
                this.trackForm.dateTo = moment(this.trackForm.dateTo).add(1, 'day');
            }
            const seconds = this.calculatePassedTime(this.trackForm.dateFrom, this.trackForm.dateTo);
            return this.convertSecondsToTimeFormat(seconds);
        },
        dateRangeText () {
            return this.dateRange.join(' ~ ')
        },
        getPanelDates () {
            const items = this.tracking.reduce(function (acc, item) {
                const date = moment(item.date_from).format('YYYY-MM-DD');
                return [...acc, date.toString()];
            }, []);
            return [...new Set(items)].sort().reverse();
        },
        getItems () {
            return this.tracking;
        }
    },
    watch: {
        timeFrom: function () {
            this.trackForm.dateFrom = moment(this.date)
                .hours(this.timeFrom.toString().split(':')[0])
                .minutes(this.timeFrom.toString().split(':')[1]);
        },
        timeTo: function () {
            this.trackForm.dateTo = moment(this.date)
                .hours(this.timeTo.toString().split(':')[0])
                .minutes(this.timeTo.toString().split(':')[1]);
        },
        date: function () {
            this.trackForm.date = moment(this.date);
            this.trackForm.dateFrom = moment(this.date)
                .hours(this.timeFrom.toString().split(':')[0])
                .minutes(this.timeFrom.toString().split(':')[1]);
            let dayToAdding = 0;
            if (moment(this.trackForm.dateTo).format(this.dateFormat) > moment(this.trackForm.dateFrom).format(this.dateFormat)) {
                dayToAdding = 1;
            }
            this.trackForm.dateTo = moment(this.date)
                .add(dayToAdding, 'day')
                .hours(this.timeTo.toString().split(':')[0])
                .minutes(this.timeTo.toString().split(':')[1]);
        }
    },
}
</script>
