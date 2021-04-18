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

        <v-toolbar dense flat>
            <v-btn-toggle
                dense
                v-model="typeOfItems"
                :background-color="themeBgColor"
            >
                <v-btn>
                    {{ langMap.tracking.timesheet.time_tracked }}
                </v-btn>

                <v-btn>
                    {{ langMap.tracking.timesheet.approval_pending }}
                </v-btn>

                <v-btn>
                    {{ langMap.tracking.timesheet.unsubmitted }}
                </v-btn>

                <v-btn>
                    {{ langMap.tracking.timesheet.archived }}
                </v-btn>
            </v-btn-toggle>

            <v-spacer></v-spacer>
        </v-toolbar>

        <v-spacer>&nbsp;</v-spacer>

        <div class="d-flex flex-row">
            <div class="d-inline-flex flex-grow-1 mt-2 mx-4">
                {{ dateRangeText }}
            </div>
            <div class="d-inline-flex flex-grow-1">
                <v-select
                    class="mx-4"
                    v-model="filterProject"
                    :items="$store.getters['Projects/getProjects']"
                    item-value="id"
                    item-text="name"
                    :label="langMap.tracking.timesheet.filter"
                    dense
                    style="max-width: 200px"
                    clearable
                ></v-select>
            </div>
            <div class="d-inline-flex flex-grow-1 mx-4">
                <div class="d-flex flex-row">
                    <div class="d-inline-flex">
                        <v-btn
                            icon
                            @click="date = moment(date).subtract(1, 'days').format(dateFormat)"
                        >
                            <v-icon>mdi-chevron-left</v-icon>
                        </v-btn>
                    </div>
                    <div class="d-inline-flex">
                        <v-menu
                            ref="menu"
                            v-model="menuDate"
                            transition="scale-transition"
                            :close-on-content-click="false"
                            offset-y
                            min-width="auto"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="selectedDateText"
                                    prepend-icon="mdi-calendar"
                                    readonly
                                    hide-details
                                    v-bind="attrs"
                                    v-on="on"
                                    class="mt-0 pt-0"
                                    style="max-width: 120px"
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="date"
                                no-title
                                first-day-of-week="1"
                                @input="menuDate = false"
                            >
                            </v-date-picker>
                        </v-menu>
                    </div>
                    <div class="d-inline-flex">
                        <v-btn
                            icon
                            @click="date = moment(date).add(1, 'days').format(dateFormat)"
                        >
                            <v-icon>mdi-chevron-right</v-icon>
                        </v-btn>
                    </div>
                </div>
            </div>
            <div class="d-inline-flex flex-grow-1 mx-4">
                <v-radio-group
                    class="mt-0"
                    dense
                    row
                    hide-details
                    v-model="viewType"
                >
                    <v-radio
                        class="d-inline-flex"
                        value="daily"
                        :label="langMap.tracking.timesheet.daily"
                    ></v-radio>
                    <v-radio
                        class="d-inline-flex"
                        value="weekly"
                        :label="langMap.tracking.timesheet.weekly"
                    ></v-radio>
                </v-radio-group>
            </div>
        </div>

        <v-data-table
            v-if="viewType === 'daily'"
            :headers="dailyHeaders"
            show-select
            single-select
            :loading="loading"
            loading-text="Loading... Please wait"
            v-model="selected"
        >
            <template v-slot:footer>
                213
            </template>
        </v-data-table>
        <v-data-table
            v-if="viewType === 'weekly'"
            :headers="weeklyHeaders"
            show-select
            single-select
            :items="getTimesheet"
            :loading="loading"
            loading-text="Loading... Please wait"
            v-model="selected"
        >
            <template v-slot:header.mon="{ header }">
                {{header.text}}<br>
                {{$helpers.time.convertSecToTime(header.time)}}
            </template>
            <template v-slot:header.tue="{ header }">
                {{header.text}}<br>
                {{$helpers.time.convertSecToTime(header.time)}}
            </template>
            <template v-slot:header.wed="{ header }">
                {{header.text}}<br>
                {{$helpers.time.convertSecToTime(header.time)}}
            </template>
            <template v-slot:header.thu="{ header }">
                {{header.text}}<br>
                {{$helpers.time.convertSecToTime(header.time)}}
            </template>
            <template v-slot:header.fri="{ header }">
                {{header.text}}<br>
                {{$helpers.time.convertSecToTime(header.time)}}
            </template>
            <template v-slot:header.sat="{ header }">
                {{header.text}}<br>
                {{$helpers.time.convertSecToTime(header.time)}}
            </template>
            <template v-slot:header.sun="{ header }">
                {{header.text}}<br>
                {{$helpers.time.convertSecToTime(header.time)}}
            </template>
            <template v-slot:footer="{ props, on, headers, widths }">
                <v-spacer>&nbsp;</v-spacer>
                <div class="d-flex flex-row" v-if="typeOfItems === 0">
                    <div
                        class="d-inline-flex"
                        v-for="(header, index) in headers"
                        :key="index"
                        :style="{ width: header.width ? header.width : '1%', 'min-width': '56px' }"
                    >
                        <template v-if="header.value === 'data-table-select'">
                            <div class="text-center" style="width: 100%;">
                                <v-btn
                                    dark
                                    :color="themeBgColor"
                                    @click="createTimesheet"
                                    class="mx-2"
                                >
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </div>
                        </template>
                        <template v-if="header.value === 'project.name'">
                            <div class="mt-1 px-4" style="width: 100%">
                                <ProjectBtn
                                    :color="themeBgColor"
                                    v-model="form.project"
                                ></ProjectBtn>
                            </div>
                        </template>
                        <template v-if="header.value === 'mon'">
                            <div class="mx-4" style="width: 100%">
                                <TimeField
                                    v-model="form.mon"
                                    style="max-width: 100px"
                                    placeholder="hh:mm"
                                    format="HH:mm"
                                ></TimeField>
                            </div>
                        </template>
                        <template v-if="header.value === 'tue'">
                            <div class="mx-4" style="width: 100%">
                                <TimeField
                                    v-model="form.tue"
                                    style="max-width: 100px"
                                    placeholder="hh:mm"
                                    format="HH:mm"
                                ></TimeField>
                            </div>
                        </template>
                        <template v-if="header.value === 'wed'">
                            <div class="mx-4" style="width: 100%">
                                <TimeField
                                    v-model="form.wed"
                                    style="max-width: 100px"
                                    placeholder="hh:mm"
                                    format="HH:mm"
                                ></TimeField>
                            </div>
                        </template>
                        <template v-if="header.value === 'thu'">
                            <div class="mx-4" style="width: 100%">
                                <TimeField
                                    v-model="form.thu"
                                    style="max-width: 100px"
                                    placeholder="hh:mm"
                                    format="HH:mm"
                                ></TimeField>
                            </div>
                        </template>
                        <template v-if="header.value === 'fri'">
                            <div class="mx-4" style="width: 100%">
                                <TimeField
                                    v-model="form.fri"
                                    style="max-width: 100px"
                                    placeholder="hh:mm"
                                    format="HH:mm"
                                ></TimeField>
                            </div>
                        </template>
                        <template v-if="header.value === 'sat'">
                            <div class="mx-4" style="width: 100%">
                                <TimeField
                                    v-model="form.sat"
                                    style="max-width: 100px"
                                    placeholder="hh:mm"
                                    format="HH:mm"
                                ></TimeField>
                            </div>
                        </template>
                        <template v-if="header.value === 'sun'">
                            <div class="mx-4" style="width: 100%">
                                <TimeField
                                    v-model="form.sun"
                                    style="max-width: 100px"
                                    placeholder="hh:mm"
                                    format="HH:mm"
                                ></TimeField>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
            <template v-slot:item.project.name="{ isMobile, item, header, value }">
                <span v-if="item.project">
                    <span>{{ item.project.name }}</span> / <span>{{ item.project.client.name }}</span>
                </span>
            </template>
            <template v-slot:item.mon="{ isMobile, item, header, value }">
                <template v-if="typeOfItems === 0">
                    <span v-if="item.times && item.times[0]">
                    <TimeField
                        v-model="moment(item.times[0].dateTime).format()"
                        style="max-width: 100px"
                        placeholder="hh:mm"
                        format="HH:mm"
                    ></TimeField>
                </span>
                </template>
                <template v-else>
                    {{ moment(item.times[0].dateTime).format('HH:mm') }}
                </template>
            </template>
            <template v-slot:item.tue="{ isMobile, item, header, value }">
                <template v-if="typeOfItems === 0">
                    <span v-if="item.times && item.times[1]">
                    <TimeField
                        v-model="moment(item.times[1].dateTime).format()"
                        style="max-width: 100px"
                        placeholder="hh:mm"
                        format="HH:mm"
                    ></TimeField>
                </span>
                </template>
                <template v-else>
                    {{ moment(item.times[1].dateTime).format('HH:mm') }}
                </template>
            </template>
            <template v-slot:item.wed="{ isMobile, item, header, value }">
                <template v-if="typeOfItems === 0">
                    <span v-if="item.times && item.times[2]">
                    <TimeField
                        v-model="moment(item.times[2].dateTime).format()"
                        style="max-width: 100px"
                        placeholder="hh:mm"
                        format="HH:mm"
                    ></TimeField>
                </span>
                </template>
                <template v-else>
                    {{ moment(item.times[2].dateTime).format('HH:mm') }}
                </template>
            </template>
            <template v-slot:item.thu="{ isMobile, item, header, value }">
                <template v-if="typeOfItems === 0">
                    <span v-if="item.times && item.times[3]">
                        <TimeField
                            v-model="moment(item.times[3].dateTime).format()"
                            style="max-width: 100px"
                            placeholder="hh:mm"
                            format="HH:mm"
                        ></TimeField>
                    </span>
                </template>
                <template v-else>
                    {{ moment(item.times[3].dateTime).format('HH:mm') }}
                </template>
            </template>
            <template v-slot:item.fri="{ isMobile, item, header, value }">
                <template v-if="typeOfItems === 0">
                    <span v-if="item.times && item.times[4]">
                        <TimeField
                            v-model="moment(item.times[4].dateTime).format()"
                            style="max-width: 100px"
                            placeholder="hh:mm"
                            format="HH:mm"
                        ></TimeField>
                    </span>
                </template>
                <template v-else>
                    {{ moment(item.times[4].dateTime).format('HH:mm') }}
                </template>
            </template>
            <template v-slot:item.sat="{ isMobile, item, header, value }">
                <template v-if="typeOfItems === 0">
                    <span v-if="item.times && item.times[5]">
                    <TimeField
                        v-model="moment(item.times[5].dateTime).format()"
                        style="max-width: 100px"
                        placeholder="hh:mm"
                        format="HH:mm"
                    ></TimeField>
                </span>
                </template>
                <template v-else>
                    {{ moment(item.times[5].dateTime).format('HH:mm') }}
                </template>
            </template>
            <template v-slot:item.sun="{ isMobile, item, header, value }">
                <template v-if="typeOfItems === 0">
                    <span v-if="item.times && item.times[6]">
                        <TimeField
                            v-model="moment(item.times[6].dateTime).format()"
                            style="max-width: 100px"
                            placeholder="hh:mm"
                            format="HH:mm"
                        ></TimeField>
                    </span>
                </template>
                <template v-else>
                    {{ moment(item.times[6].dateTime).format('HH:mm') }}
                </template>
            </template>
            <template v-slot:item.total="{ isMobile, item, header, value }">
                <span v-if="item.totalTime">
                    {{ $helpers.time.convertSecToTime(item.totalTime, false) }}
                </span>
                <span v-else>00:00</span>
            </template>
        </v-data-table>

        <v-toolbar dense flat style="background-color: #f0f0f0;
    border-color: #f0f0f0;">
            <v-btn
                v-if="selected.length"
                color="error"
            >
                Remove selected
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
                :color="themeBgColor"
                :style="{ color: $helpers.color.invertColor(themeBgColor)}"
            >
                {{ langMap.tracking.timesheet.submit_for_approval }}
            </v-btn>

            <span class="mx-4">
                Total time: {{ $helpers.time.convertSecToTime(totalTime, false) }} hrs
            </span>
        </v-toolbar>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import moment from 'moment-timezone';
import _ from 'lodash';
import ProjectBtn from './components/project-btn';
import TimeField from './components/time-field';

export default {
    components: {
        ProjectBtn,
        TimeField
    },
    data() {
        return {
            dateFormat: 'YYYY-MM-DD',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            dateRangePicker: false,
            dateRange: {
                start: null,
                end: null
            },
            typeOfItems: 0,
            viewType: 'weekly',
            date: null,
            menuDate: false,
            filterProject: 0,
            form: {
                project: null,
                mon: moment().startOf('days').format(),
                tue: moment().startOf('days').format(),
                wed: moment().startOf('days').format(),
                thu: moment().startOf('days').format(),
                fri: moment().startOf('days').format(),
                sat: moment().startOf('days').format(),
                sun: moment().startOf('days').format()
            },
            loading: false,
            selected: []
        }
    },
    created () {
        moment.updateLocale(this.$store.state.lang.short_code, {
            week: {
                dow: 1,
            },
        });
        this.debounceGetTimesheet = _.debounce(this._getTimesheet, 1000);
        this.debounceGetProjects = _.debounce(this._getProjects, 1000);
        this.date = moment().format(this.dateFormat);
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.debounceGetTimesheet();
        this.debounceGetProjects();
        this.resetTimesheet();
    },
    methods: {
        async _getTimesheet() {
            this.loading = true;
            await this.$store.dispatch('Timesheet/getTimesheet', {
                ...this.dateRange
            });
            this.loading = false;
            this.resetTimesheet();
        },
        _getProjects() {
            this.$store.dispatch('Projects/getProjectList', { search: null });
        },
        createTimesheet() {
            if (this.form.project) {
                return this.$store.dispatch('Timesheet/createTimesheet', this.form)
                    .then(result => {
                        if (result) {
                            this.resetTimesheet();
                            this.snackbarMessage = 'Success';
                            this.actionColor = 'success'
                            this.snackbar = true;
                        } else {
                            this.snackbarMessage = 'Error';
                            this.actionColor = 'error'
                            this.snackbar = true;
                        }
                    });
            }
        },
        resetTimesheet() {
            this.selected = [];
            this.form = {
                project: null,
                mon: moment(this.periodStart).startOf('days').format(),
                tue: moment(this.periodStart).add(1, 'days').startOf('days').format(),
                wed: moment(this.periodStart).add(2, 'days').startOf('days').format(),
                thu: moment(this.periodStart).add(3, 'days').startOf('days').format(),
                fri: moment(this.periodStart).add(4, 'days').startOf('days').format(),
                sat: moment(this.periodStart).add(5, 'days').startOf('days').format(),
                sun: moment(this.periodStart).add(6, 'days').startOf('days').format()
            };
        },
        timeBetween(end) {
            const start = moment(end).startOf('days')
            return this.$helpers.time.getSecBetweenDates(start, end);
        }
    },
    watch: {
        date () {
            this.dateRange.start =
                this.viewType === 'weekly'
                    ? moment(this.date).startOf('weeks').format(this.dateFormat)
                    : moment(this.date).startOf('days').format(this.dateFormat);
            this.dateRange.end =
                this.viewType === 'weekly'
                    ? moment(this.date).endOf('weeks').format(this.dateFormat)
                    : moment(this.date).endOf('days').format(this.dateFormat);
        },
        'dateRange.start' () {
            this.debounceGetTimesheet();
        }
    },
    computed: {
        periodStart () {
            return moment(this.date).startOf('weeks');
        },
        periodEnd () {
            return moment(this.date).endOf('weeks');
        },
        dateRangeText () {
            const dateFormat = 'DD MMM YYYY';
            if (this.viewType === 'weekly') {
                return `${moment(this.periodStart).format(dateFormat)} - ${moment(this.periodEnd).format(dateFormat)}`;
            }
            return moment(this.date).format(dateFormat);
        },
        selectedDateText () {
            if (this.date === moment().format(this.dateFormat)) {
                return 'Today';
            }
            return moment(this.date).format(this.dateFormat);
        },
        dailyHeaders () {
            return [];
        },
        weeklyHeaders () {
            let days = [0,0,0,0,0,0,0];
            this.getTimesheet.map(timesheet => {
                for (let i = 0; i < 7; i++) {
                    days[i] += this.timeBetween(timesheet.times[i].dateTime);
                }
            });
            return [
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.projects,
                    align: 'start',
                    value: 'project.name',
                    width: '20%'
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.mon,
                    align: 'start',
                    value: 'mon',
                    width: '10%',
                    sortable: false,
                    time: days[0]
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.tue,
                    align: 'start',
                    value: 'tue',
                    width: '10%',
                    sortable: false,
                    time: days[1]
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.wed,
                    align: 'start',
                    value: 'wed',
                    width: '10%',
                    sortable: false,
                    time: days[2]
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.thu,
                    align: 'start',
                    value: 'thu',
                    width: '10%',
                    sortable: false,
                    time: days[3]
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.fri,
                    align: 'start',
                    value: 'fri',
                    width: '10%',
                    sortable: false,
                    time: days[4]
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.sat,
                    align: 'start',
                    value: 'sat',
                    width: '10%',
                    sortable: false,
                    time: days[5]
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.sun,
                    align: 'start',
                    value: 'sun',
                    width: '10%',
                    sortable: false,
                    time: days[6]
                },
                {
                    text: 'Total',
                    align: 'center',
                    value: 'total'
                },
            ];
        },
        getTimesheet() {
            if (this.filterProject) {
                return this.$store.getters['Timesheet/getTimesheet']
                    .filter(i => i.project && i.project.id === this.filterProject);
            }
            let status = 'tracked';
            switch (this.typeOfItems) {
                case 1: status = 'pending'; break;
                case 2: status = 'unsubmitted'; break;
                case 3: status = 'archived'; break;
            }
            return this.$store.getters['Timesheet/getTimesheet']
                .filter(i => i.status === status );
        },
        totalTime() {
            let totalTime = 0;
            this.getTimesheet.map(timesheet => {
                for (let i = 0; i < 7; i++) {
                    totalTime += this.timeBetween(timesheet.times[i].dateTime);
                }
            });
            return totalTime;
        }
    }
}
</script>
