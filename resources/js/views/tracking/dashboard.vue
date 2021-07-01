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
                        :items="getReports"
                        :headers="headers.reports"
                    ></v-data-table>
                </v-card>
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
                        :items="getCurrentUserTracking"
                        :headers="headers.tracking"
                    >
                        <template v-slot:item.project="{item}">
                            <span v-if="item.entity">{{ item.entity.name }}</span>
                        </template>
                        <template v-slot:item.description="{item}">
                            {{ item.description }}
                        </template>
                        <template v-slot:item.date_from="{item}">
                            {{ moment(item.date_from).format('DD.MM.YYYY HH:mm:ss') }}
                        </template>
                        <template v-slot:item.date_to="{item}">
                            <span v-if="item.date_to">
                                {{ moment(item.date_to).format('DD.MM.YYYY HH:mm:ss') }}
                            </span>
                        </template>
                    </v-data-table>
                </v-card>
            </div>
        </div>
    </v-container>
</template>

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
            data: {},
            headers: {
                reports: [
                    {
                        text: 'ID',
                        value: 'id',
                    },
                    {
                        text: 'Name',
                        value: 'name',
                    },
                ],
                tracking: [
                    {
                        text: 'ID',
                        value: 'id',
                    },
                    {
                        text: 'Description',
                        value: 'description',
                    },
                    {
                        text: 'Project',
                        value: 'entity.name',
                    },
                    {
                        text: 'From',
                        value: 'date_from',
                    },
                    {
                        text: 'To',
                        value: 'date_to',
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
    },
    methods: {
        getData() {
            axios.get(`/api/tracking/dashboard?period=${this.period}`)
            .then(({ data }) => {
                if (data) {
                    this.data = data;
                }
            })
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
        getReports() {
            return this.data.reports ?? [];
        },
        getCurrentUserTracking() {
            return this.data.tracking ?? [];
        },
    }
}
</script>
