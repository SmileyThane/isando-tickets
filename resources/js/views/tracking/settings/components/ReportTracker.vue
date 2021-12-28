<template>
    <div>
        <div class="d-flex flex-grow-1 flex-row">
            <div class="d-inline-block flex-grow-1" style="width: 33%; min-width: 550px">
                <v-expansion-panels
                    v-model="isActive"
                    accordion
                    flat
                    v-click-outside="closePeriodSelect"
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
                                {{ period.start ? moment(period.start).format('ddd D MMM YYYY') : '...' }} -
                                {{ moment(period.end).format('ddd D MMM YYYY') }}
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
                                        v-model.sync="period"
                                        :value.sync="period"
                                        is-range
                                        no-title
                                        :step="1"
                                        :columns="2"
                                        mode="range"
                                        :disabled-dates='{ weekdays: [3,4,5,6,7] }'
                                        @input="closePeriodSelect"
                                    ></vc-date-picker>
                                </div>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
            <div class="d-inline-block flex-grow-1" style="width: 33%">
                <client-select
                    :items="$store.getters['Clients/getClients']"
                    :selected="clients"
                    @select="clients = $event"
                ></client-select>
            </div>
            <div class="d-inline-block flex-grow-1" style="width: 33%">
                <coworker-select
                    :items="$store.getters['Team/getCoworkers']"
                    :selected="coworkers"
                    @select="coworkers = $event"
                ></coworker-select>
            </div>
        </div>
        <div class="d-flex flex-grow-1 flex-row">
            <div class="d-inline-block flex-grow-1" style="width: 33%; min-width: 550px">
                <v-radio-group
                    class="mt-2 pt-0"
                    v-model="source"
                    label="Source"
                    row
                >
                    <v-radio
                        key="tracker"
                        label="Tracker"
                        value="tracker"
                    ></v-radio>
                    <v-radio
                        key="timesheet"
                        label="Timesheet"
                        value="timesheet"
                    ></v-radio>
                </v-radio-group>
            </div>
            <div class="d-inline-block flex-grow-1 text-center" style="width: 33%">

            </div>
            <div class="d-inline-block flex-grow-1" style="width: 33%">
                <v-btn
                    class="mt-1 mx-4"
                    :color="themeBgColor"
                    @click="genReport"
                    :loading="loadingBtn"
                >Generate report</v-btn>
            </div>
        </div>
    </div>
</template>

<script>
import ClientSelect from './UI/ClientSelect';
import CoworkerSelect from './UI/CoworkerSelect';
import moment from 'moment-timezone';

export default {
    name: 'report-tracker',
    components: {
        ClientSelect,
        CoworkerSelect,
    },
    data () {
        return {
            dateFormat: 'YYYY-MM-DD',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            snackbar: false,
            isActive: null,
            period: {
                start: moment().subtract(1, 'months').startOf('weeks').format('YYYY-MM-DDTHH:mm:ss'),
                end: moment().endOf('weeks').format('YYYY-MM-DDTHH:mm:ss')
            },
            source: 'tracker',
            clients: [],
            coworkers: [],
            grouping: [],
            loadingBtn: false,
        };
    },
    created () {
        this.period = {
            start: moment().subtract(1, 'months').startOf('weeks').format('YYYY-MM-DDTHH:mm:ss'),
            end: moment().endOf('weeks').format('YYYY-MM-DDTHH:mm:ss')
        };
    },
    methods: {
        closePeriodSelect () {
            this.isActive = null;
        },
        async setPeriod (periodKey = 'today') {
            let start = null;
            let end = null;
            switch (periodKey) {
                case 'today':
                    start = moment().startOf('weeks');
                    end = moment().endOf('weeks');
                    break;
                case 'yesterday':
                    start = moment().subtract(1, 'days').startOf('weeks');
                    end = moment().subtract(1, 'days').endOf('weeks');
                    break;
                case 'last7days':
                    start = moment().weekday(1).subtract(7, 'days').startOf('weeks');
                    end = moment().weekday(1).subtract(1, 'days').endOf('weeks');
                    break;
                case 'last28days':
                    start = moment().subtract(28, 'days').startOf('weeks');
                    end = moment().subtract(1, 'days').endOf('weeks');
                    break;
                case 'thisWeek':
                    start = moment().isoWeekday(1).startOf('weeks');
                    end = moment().isoWeekday(1).endOf('weeks');
                    break;
                case 'lastWeek':
                    start = moment().weekday(1).startOf('weeks').subtract(1, 'weeks');
                    end = moment().weekday(1).endOf('weeks').subtract(1, 'weeks');
                    break;
                case 'thisMonth':
                    start = moment().startOf('months').startOf('weeks');
                    end = moment().endOf('months').endOf('weeks');
                    break;
                case 'lastMonth':
                    start = moment().startOf('months').subtract(1, 'months').startOf('weeks');
                    end = moment().endOf('months').subtract(1, 'months').endOf('weeks');
                    break;
                case 'thisQuarter':
                    start = moment().startOf('quarters').startOf('weeks');
                    end = moment().endOf('quarters').endOf('weeks');
                    break;
                case 'lastQuarter':
                    start = moment().startOf('quarters').subtract(1, 'quarters').startOf('weeks');
                    end = moment().endOf('quarters').subtract(1, 'quarters').endOf('weeks');
                    break;
                case 'thisYear':
                    start = moment().startOf('years').startOf('weeks');
                    end = moment().endOf('years').endOf('weeks');
                    break;
                default:
                    // total time
                    start = null;
                    end = moment().endOf('years').endOf('weeks');
            }
            this.period.start = start ? moment(start).format(this.dateFormat) : start;
            this.period.end = moment(end).format(this.dateFormat);
            const calendar = this.$refs.calendar;
            if (calendar) {
                await calendar.updateValue({
                    start: this.period.start ? moment(this.period.start).format(this.dateFormat) : moment('2020-01-01').format(this.dateFormat),
                    end: moment(this.period.end).format(this.dateFormat),
                }, {
                    formatInput: true,
                    hidePopover: false,
                });
                calendar.$refs.calendar.showPageRange({
                    from: this.period.start,
                    to: this.period.end,
                });
            }
            this.closePeriodSelect();
        },
        genReport () {
            this.loadingBtn = true;
            axios.post(`/api/tracking/settings/report/${this.source}`, {
                start: this.period.start,
                end: this.period.end,
                clients: this.clients,
                coworkers: this.coworkers,
            }, {
                responseType: 'blob'
            })
                .then(res => {
                    const url = window.URL.createObjectURL(new Blob([res.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `Report_${this.source}.csv`);
                    document.body.appendChild(link);
                    link.click();
                })
                .catch(err => {
                    this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                })
                .finally(() => {
                    this.loadingBtn = false;
                })
        }
    }
};
</script>

<style scoped>
>>>.v-expansion-panel {
    border-color: rgba(0, 0, 0, 0.42);
    border-width: 1px;
    border-style: solid;
}
</style>
