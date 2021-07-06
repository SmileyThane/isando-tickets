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
        <div class="d-flex">
            <div class="d-inline-flex flex-grow-1">
                <v-select
                    :items="periodList"
                    item-text="text"
                    item-value="value"
                    v-model="period"
                    @change="getData"
                    dense
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
                            <v-card-title>Total time per project</v-card-title>
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
                            <v-card-title>Top project</v-card-title>
                            <template v-if="data.topProjects.length">
                                <div
                                    v-for="(item, index) in data.topProjects"
                                    :key="item.id"
                                    class="d-flex flex-grow-1 flex-column mb-4 mx-4 mt-0"
                                    style="min-width: 400px;"
                                >
                                    <div class="flex-grow-1 text-left" style="width: 100%; overflow: hidden">
                                        <strong>{{index+1}}.</strong>
                                        <strong v-if="!item.project_name">Without project</strong>
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
                                No billable projects were tracked in the selected period.
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
                            <span v-else>No project</span>
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
                                            v-for="(line, ind) in user.lines"
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

<style>
.top-projects__info {
    position: relative;
    top: 30%;
    margin: 0 auto;
    padding: 0 50px;
    width: 335px;
}
</style>

<script>
import EventBus from "../../components/EventBus";
import BarChart from "./components/bar-chart";
import DoughnutChart from "./components/doughnut-chart";

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
            period: 'thisMonth',
            periodList: [
                {
                    value: 'today',
                    text: 'Today',
                },
                {
                    value: 'thisWeek',
                    text: 'This week',
                },
                {
                    value: 'thisMonth',
                    text: 'This month',
                },
                {
                    value: 'thisQuarter',
                    text: 'This quarter',
                },
                {
                    value: 'thisYear',
                    text: 'This year',
                },
            ],
            data: {
                projects: [],
                reports: [],
                services: [],
                topProjects: [],
                tracking: [],
                lastActivity: [],
            },
            headers: {
                lastActivity: [
                    {
                        text: 'Team',
                        value: 'team',
                    },
                    {
                        text: 'Ticket/Project',
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
            },
        }
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
        this.$store.dispatch('Tracking/getSettings');
    },
    methods: {
        getData() {
            axios.get(`/api/tracking/dashboard?period=${this.period}`)
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
            return Object.values(items).map(i => ({ ...i, percent: (i.count / totalTime * 100).toFixed(2) }));
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
                labels.push((i.client_name ?? 'Unknown client') + "\r\n" + (i.name ?? 'Without project'));
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
        }
    }
}
</script>
