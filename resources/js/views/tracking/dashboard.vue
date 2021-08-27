<template>
    <v-container fluid class="tracking-dashboard">
        <v-snackbar
            :bottom="true"
            :right="true"
            v-model="snackbar"
            :color="actionColor"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <div class="d-flex">
            <div class="d-inline-flex flex-grow-1 mb-2" style="max-width: 550px;">
                <v-expansion-panels
                    v-model="activePeriod"
                    accordion
                    flat
                    v-click-outside="onClickOutsideHandler"
                >
                    <v-expansion-panel
                        key="timeperiod"
                    >
                        <v-expansion-panel-header v-slot="{ open }">
                            <div class="d-flex flex-row">
                                <div class="d-inline-flex">
                                    {{ langMap.tracking.report.time_period }}:
                                </div>
                                <div class="d-inline-flex float-right flex-grow-1 text-center">
                                    <v-fade-transition leave-absolute>
                                        <span v-if="open" class="d-block flex-grow-1">{{ langMap.tracking.report.choose_period }}</span>
                                        <span v-else class="d-block flex-grow-1">
                                            {{ periodDate.start ? moment(periodDate.start).format('ddd D MMM YYYY') : '...' }} -
                                            {{ moment(periodDate.end).format('ddd D MMM YYYY') }}
                                        </span>
                                    </v-fade-transition>
                                </div>
                            </div>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content
                            class="elevation-"
                        >
                            <div class="d-flex flex-row">
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('today')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_today }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('yesterday')" :style="{ color: themeBgColor }">{{langMap.tracking.report.period_yesterday}}</a>
                                    <a class="text-decoration-none" @click="setPeriod('last7days')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_last7days }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('last28days')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_last28days }}</a>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('thisWeek')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_this_week }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastWeek')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_last_week }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('thisMonth')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_this_month }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastMonth')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_last_month }}</a>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('thisQuarter')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_this_quarter }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastQuarter')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_last_quarter }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('thisYear')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_this_year }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('totalTime')" :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_total_time }}</a>
                                </div>
                            </div>
                            <div class="d-flex flex-row">
                                <v-divider class="d-inline-flex flex-grow-1 my-3"></v-divider>
                            </div>
                            <div class="d-flex flex-row">
                                <div class="d-inline-flex flex-grow-1">
                                    {{langMap.tracking.report.select_custom_period}}:
                                </div>
                            </div>
                            <div class="d-flex flex-row">
                                <div class="d-inline-flex">
                                    <vc-date-picker
                                        ref="calendar"
                                        v-model.sync="periodDate"
                                        :value.sync="periodDate"
                                        is-range
                                        no-title
                                        :step="1"
                                        :columns="2"
                                        mode="range"
                                        @input="activePeriod = null; getData()"
                                    ></vc-date-picker>
                                </div>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
            <div class="d-inline-flex flex-grow-1">
                <v-select
                    :items="getTeams"
                    item-value="id"
                    item-text="name"
                    outlined
                    clearable
                    placeholder="Choose team"
                    v-model="selectedTeam"
                    @change="getData()"
                ></v-select>
            </div>
        </div>
        <div class="d-flex">
            <div class="d-inline-flex flex-grow-1 px-4">
                <v-skeleton-loader
                    max-width="500"
                    type="card"
                >
                    <template v-slot:default>
                        <v-card
                            width="100%"
                            outlined
                        >
                            <v-card-title>Total time per service</v-card-title>
                            <BarChart
                                :data="totalTimeByServices"
                                :options="charts.options.services"
                            ></BarChart>
                        </v-card>
                    </template>
                </v-skeleton-loader>
            </div>
            <div class="d-inline-flex flex-grow-1 px-4">
                <v-skeleton-loader
                    max-width="500"
                    type="card"
                >
                    <template v-slot:default>
                        <v-card
                            width="100%"
                            outlined
                        >
                            <v-card-title>Total time per {{getTrackingProjectLabel}}</v-card-title>
                            <DoughnutChart
                                :data="totalTimeByProjects"
                                :options="charts.options.projects"
                            ></DoughnutChart>
                        </v-card>
                    </template>
                </v-skeleton-loader>
            </div>
            <div class="d-inline-flex flex-grow-1 px-4">
                <v-skeleton-loader
                    max-width="500"
                    type="card"
                >
                    <template v-slot:default>
                        <v-card
                            width="100%"
                            height="100%"
                            outlined
                        >
                            <v-card-title>Top {{ getTrackingProjectsLabel }}</v-card-title>
                            <template v-if="data.topProjects.length">
                                <div
                                    v-for="(item, index) in data.topProjects"
                                    :key="item.id"
                                    class="d-flex flex-grow-1 flex-column mb-4 mx-4 mt-0"
                                    style="min-width: 400px;"
                                >
                                    <div class="flex-grow-1 text-left" style="width: 100%; overflow: hidden">
                                        <strong>{{index+1}}.</strong>
                                        <strong v-if="!item.project_name">Without {{ getTrackingProjectLabel }}</strong>
                                        <strong v-else>
                                            <span v-if="item.project_name">{{item.project_name}}</span>
                                            / <span v-if="item.client_name">{{item.client_name}}</span>
                                        </strong>
                                    </div>
                                    <div class="d-flex flex-row" style="width: 100%">
                                        <div class="d-inline-flex flex-grow-1 text-left" style="width: 50%">
                                            Total time: {{$helpers.time.convertSecToTime(item.duration, false)}} h
                                        </div>
                                        <div
                                            class="d-inline-flex flex-row-reverse flex-grow-1 text-right"
                                            style="width: 50%"
                                            v-if="$helpers.auth.checkPermissionByIds([59])"
                                        >
                                            <span v-if="currentCurrency">{{currentCurrency.symbol}}</span>
                                            <span>Revenue:&nbsp;{{$helpers.numbers.numberFormat(item.revenue, 2)}}&nbsp;</span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div
                                v-else
                                class="font-italic top-projects__info"
                            >
                                No billable {{getTrackingProjectLabel}}s were tracked in the selected period.
                            </div>
                        </v-card>
                    </template>
                </v-skeleton-loader>
            </div>
        </div>
        <v-spacer>&nbsp;</v-spacer>
        <div class="d-flex">
            <div class="d-inline-flex flex-grow-1" style="width: 100%">
                <v-card
                    style="width: 100%"
                    outlined
                >
                    <v-data-table
                        style="width: 100%"
                        :headers="headers.lastActivity"
                        :items="data.lastActivity"
                    >
                        <template v-slot:item.team="{ item }">
                            <span v-if="item.name">{{item.name}}</span>
                            <span v-else>No team</span>
                        </template>
                        <template v-slot:item.tracker="{ item }">
                            <span v-if="item.tracker && item.tracker.entity">{{item.tracker.entity.name}}</span>
                            <span v-else>No {{ getTrackingProjectLabel }}</span>
                        </template>
                        <template v-slot:item.recently_duration="{ item }">
                            <span v-if="item.tracker">{{$helpers.time.convertSecToTime(item.tracker.passed, false)}}</span>
                        </template>
                        <template v-slot:item.recently_time="{ item }">
                            <span v-if="item.tracker && item.tracker.date_from">
                                {{$helpers.dates.someXAgo(item.tracker.date_from)}}
                            </span>
                        </template>
                        <template v-slot:item.total_time="{ item }">
                            <div class="fullProgress d-flex">
                                <div
                                    class="part d-inline-flex"
                                    v-for="(user, index) in calculateProgressPart(item.users)"
                                    :key="index"
                                    :style="{ width: `${user.percent}%`, backgroundColor: getColor() }"
                                >
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-spacer
                                                v-bind="attrs"
                                                v-on="on"
                                            >&nbsp;</v-spacer>
                                        </template>
                                        <div>{{user.name}} {{user.surname}} ({{user.percent}}%)</div>
                                        <hr>
                                        <div
                                            v-for="(line, ind) in user.sortedLines"
                                            :key="ind"
                                        >
                                            {{$helpers.time.convertSecToTime(line.seconds, false)}}h {{line.entity}}
                                        </div>
                                    </v-tooltip>
                                </div>
                            </div>
                        </template>
                    </v-data-table>
                </v-card>
            </div>
        </div>
    </v-container>
</template>

<style scoped>
.top-projects__info {
    position: relative;
    top: 30%;
    margin: 0 auto;
    padding: 0 50px;
    width: 335px;
}
.v-expansion-panel:not(.v-expansion-panel--active.v-item--active) {
    max-height: 55px;
}
.v-expansion-panel {
    border: 1px solid rgba(0, 0, 0, 0.38);
}
.v-expansion-panel:hover {
    border: 1px solid rgba(0, 0, 0, 0.78);
}
>>>*:not(.v-icon) {
    font-size: 12px !important;
}
</style>

<style>
.tracking-dashboard .vc-pane-layout *, .tracking-dashboard .v-list-item__content * {
    font-size: 12px !important;
}
</style>

<script>
import EventBus from "../../components/EventBus";
import BarChart from "./components/bar-chart";
import DoughnutChart from "./components/doughnut-chart";
import * as _ from "lodash";
import moment from 'moment-timezone';


export default {
    components: {
        BarChart,
        DoughnutChart,
    },
    data() {
        const self = this;
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            charts: {
                data: {
                    services: {
                        datasets: [],
                        labels: [],
                    },
                    projects: {
                        datasets: [],
                        labels: [],
                    },
                },
                options: {
                    services: {
                        responsive: true,
                        maintainAspectRatio: true,
                        percentageInnerCutout : 90,
                        legend: {
                            display: false,
                            position: 'right'
                        },
                        tooltips: {
                            callbacks: {
                                title: function (tooltipItem, data, a, b) {
                                    return data.labels[tooltipItem[0].index] ?? 'Title';
                                },
                                label: function(tooltipItem, data) {
                                    return self.$helpers.time.convertSecToTime(data.datasets[0].data[tooltipItem.index] * 60 * 60, false);
                                }
                            }
                        }
                    },
                    projects: {
                        responsive: true,
                        maintainAspectRatio: true,
                        percentageInnerCutout : 90,
                        legend: {
                            display: false,
                            position: 'right'
                        },
                        tooltips: {
                            callbacks: {
                                title: function (tooltipItem, data, a, b) {
                                    return data.labels[tooltipItem[0].index] ?? 'Title';
                                },
                                label: function(tooltipItem, data) {
                                    return self.$helpers.time.convertSecToTime(data.datasets[0].data[tooltipItem.index] * 60 * 60, false);
                                }
                            }
                        }
                    },
                },
            },
            periodDate: {
                start: moment().subtract(1, 'months').format('YYYY-MM-DD'),
                end: moment().format('YYYY-MM-DD')
            },
            activePeriod: null,
            selectedTeam: null,
            data: {
                projects: [],
                reports: [],
                services: [],
                topProjects: [],
                tracking: [],
                lastActivity: [],
            },
        }
    },
    created () {
        moment.updateLocale(this.$store.state.lang.short_code, {
            week: {
                dow: 1,
            },
        });
        this.$store.dispatch('Tracking/getSettings');
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.getData();
        this.$store.dispatch('Team/getManagedTeams', { withEmployee: false });
        this.$store.dispatch('Team/getTeams', { search: null });
    },
    methods: {
        onClickOutsideHandler() {
            this.activePeriod = null
        },
        async setPeriod(periodKey = 'today') {
            let start = null;
            let end = null;
            switch (periodKey) {
                case "today":
                    start = moment().startOf('days');
                    end = moment().endOf('days');
                    break;
                case "yesterday":
                    start = moment().subtract(1, 'days').startOf('days');
                    end = moment().subtract(1, 'days').endOf('days');
                    break;
                case "last7days":
                    start = moment().weekday(1).subtract(7, 'days').startOf('days');
                    end = moment().weekday(1).subtract(1, 'days').endOf('days');
                    break;
                case "last28days":
                    start = moment().subtract(28, 'days').startOf('days');
                    end = moment().subtract(1, 'days').endOf('days');
                    break;
                case "thisWeek":
                    start = moment().isoWeekday(1).startOf('weeks');
                    end = moment().isoWeekday(1).endOf('weeks');
                    break;
                case "lastWeek":
                    start = moment().weekday(1).startOf('weeks').subtract(1, 'weeks');
                    end = moment().weekday(1).endOf('weeks').subtract(1, 'weeks');
                    break;
                case "thisMonth":
                    start = moment().startOf('months');
                    end = moment().endOf('months');
                    break;
                case "lastMonth":
                    start = moment().startOf('months').subtract(1, 'months');
                    end = moment().endOf('months').subtract(1, 'months');
                    break;
                case "thisQuarter":
                    start = moment().startOf('quarters');
                    end = moment().endOf('quarters');
                    break;
                case "lastQuarter":
                    start = moment().startOf('quarters').subtract(1, 'quarters');
                    end = moment().endOf('quarters').subtract(1, 'quarters');
                    break;
                case "thisYear":
                    start = moment().startOf('years');
                    end = moment().endOf('years');
                    break;
                default:
                    // total time
                    start = null;
                    end = moment().endOf('years');
            }
            this.periodDate.start = start ? moment(start).toDate() : start;
            this.periodDate.end = moment(end).toDate();
            const calendar = this.$refs.calendar;
            if (calendar) {
                await calendar.updateValue({
                    start: this.periodDate.start ? moment(this.periodDate.start).toDate() : moment('2020-01-01').toDate(),
                    end: moment(this.periodDate.end).toDate()
                }, {
                    formatInput: true,
                    hidePopover: false
                });
                calendar.$refs.calendar.showPageRange({
                    from: this.periodDate.start,
                    to: this.periodDate.end
                });
            }
            this.activePeriod = null;
        },
        getData() {
            const query = new URLSearchParams({
                periodStart: moment(this.periodDate.start).format('YYYY-MM-DD'),
                periodEnd: moment(this.periodDate.end).format('YYYY-MM-DD'),
                team: this.selectedTeam,
            });
            axios.get(`/api/tracking/dashboard?${query.toString()}`)
            .then(({ data }) => {
                if (data) {
                    this.data = data;
                }
            })
        },
        calculateProgressPart(users) {
            let items = {}; let totalTime = 0;
            users.map(user => {
                if (!items[user.user_id]) {
                    items[user.user_id] = {
                        count: 0,
                        lines: [],
                        name: '',
                        surname: '',
                    };
                }
                totalTime += parseInt(user.seconds);
                items[user.user_id].count += parseInt(user.seconds);
                items[user.user_id].lines.push(user);
                items[user.user_id].name = user.name;
                items[user.user_id].surname = user.surname;
            });
            return Object.values(items).map(i => ({
                ...i,
                percent: (i.count / totalTime * 100).toFixed(2),
                sortedLines: _.orderBy(i.lines, [function (o) { return parseInt(o.seconds); }], ['desc']),
            }));
        },
        getColor() {
            return '#' + Math.floor(Math.random()*16777215).toString(16).substr(0, 6);
        }
    },
    watch: {

    },
    computed: {
        totalTimeByServices() {
            if (!this.data || !this.data.services) return {
                labels: [],
                datasets: []
            };
            const values = [];
            const labels = [];
            const colors = [];
            this.data.services.map(i => {
                labels.push(i.name ?? 'Without services');
                values.push((i.duration / 60 / 60).toFixed(2));
            });
            for (let i = 0; i <= values.length - 1; i++) {
                colors.push(this.$helpers.color.genRandomColor());
            }
            return {
                labels,
                datasets: [{
                    label: labels,
                    backgroundColor: colors,
                    data: values
                }]
            };
        },
        totalTimeByProjects() {
            if (!this.data || !this.data.projects) return {
                labels: [],
                datasets: []
            };
            const values = [];
            const labels = [];
            const colors = [];
            this.data.projects.map(i => {
                labels.push((i.client_name ?? 'Unknown client') + "\r\n" + (i.project ?? `Without ${this.getTrackingProjectLabel}`));
                values.push((i.duration / 60 / 60).toFixed(2));
            });
            for (let i = 0; i <= values.length - 1; i++) {
                colors.push(this.$helpers.color.genRandomColor());
            }
            return {
                labels,
                datasets: [{
                    label: labels,
                    backgroundColor: colors,
                    data: values
                }]
            };
        },
        getCurrentUserTracking() {
            return this.data.tracking ?? [];
        },
        currentCurrency() {
            const settings = this.$store.getters['Tracking/getSettings'];
            return settings.currency ?? null;
        },
        getTeams() {
            if (this.$helpers.auth.checkPermissionByIds([91])) {
                return this.$store.getters['Team/getTeams'].data;
            }
            if (this.$helpers.auth.checkPermissionByIds([66])) {
                return this.$store.getters['Team/getManagedTeams'];
            }
            return this.$store.getters['Team/getTeams'].data;
        },
        getTrackingProjectLabel() {
            const { settings } = this.$store.getters['Tracking/getSettings'];
            const projectType = settings && settings.projectType ? settings.projectType : 0;
            switch (projectType) {
                case 1: return this.langMap.tracking.department;
                case 2: return this.langMap.tracking.profit_center;
                default: return this.langMap.tracking.project;
            }
        },
        getTrackingProjectsLabel() {
            const { settings } = this.$store.getters['Tracking/getSettings'];
            const projectType = settings && settings.projectType ? settings.projectType : 0;
            switch (projectType) {
                case 1: return this.langMap.tracking.departments;
                case 2: return this.langMap.tracking.profit_centres;
                default: return this.langMap.tracking.projects;
            }
        },
        headers() {
            return {
                lastActivity: [
                    {
                        text: 'Team',
                        value: 'team',
                    },
                    {
                        text: 'Ticket/' + this.getTrackingProjectLabel,
                        value: 'tracker',
                    },
                    {
                        text: 'Tracked recently',
                        value: 'recently_duration',
                    },
                    {
                        text: '',
                        value: 'recently_time',
                    },
                    {
                        text: 'Total time per team groupped by person',
                        value: 'total_time',
                    },
                ],
            };
        },
    }
}
</script>
