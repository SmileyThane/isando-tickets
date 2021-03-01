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
        <v-alert
            outlined
            type="info"
        >
            Coming soon
        </v-alert>
        <div class="d-flex flex-row">
            <div class="d-inline-flex flex-grow-1">
                <v-select
                    placeholder="Choose report"
                    outlined
                    v-model="builder.report"
                ></v-select>
            </div>
            <div class="d-inline-flex mx-16">
                <v-btn
                    :color="themeColor"
                    :style="{ color: invertColor(themeColor) }"
                >Save</v-btn>
            </div>
        </div>
        <div class="d-flex flex-row">
            <div class="d-inline-flex flex-glow-1 mr-2" style="width: 40%; max-height: 55px">
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
                                    Time period:
                                </div>
                                <div class="d-inline-flex float-right flex-grow-1 text-center">
                                    <v-fade-transition leave-absolute>
                                        <span v-if="open" class="d-block flex-grow-1">Choose period</span>
                                        <span v-else class="d-block flex-grow-1">
                                            {{ builder.period.start ? moment(builder.period.start).format('ddd D MMM YYYY') : '...' }} -
                                            {{ moment(builder.period.end).format('ddd D MMM YYYY') }}
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
                                    <a class="text-decoration-none" @click="setPeriod('today')" :style="{ color: themeColor }">Today</a>
                                    <a class="text-decoration-none" @click="setPeriod('yesterday')" :style="{ color: themeColor }">Yesterday</a>
                                    <a class="text-decoration-none" @click="setPeriod('last7days')" :style="{ color: themeColor }">Last 7 days</a>
                                    <a class="text-decoration-none" @click="setPeriod('last28days')" :style="{ color: themeColor }">Last 28 days</a>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('thisWeek')" :style="{ color: themeColor }">This week</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastWeek')" :style="{ color: themeColor }">Last week</a>
                                    <a class="text-decoration-none" @click="setPeriod('thisMonth')" :style="{ color: themeColor }">This month</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastMonth')" :style="{ color: themeColor }">Last month</a>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('thisQuarter')" :style="{ color: themeColor }">This quarter</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastQuarter')" :style="{ color: themeColor }">Last quarter</a>
                                    <a class="text-decoration-none" @click="setPeriod('thisYear')" :style="{ color: themeColor }">This year</a>
                                    <a class="text-decoration-none" @click="setPeriod('totalTime')" :style="{ color: themeColor }">Total time</a>
                                </div>
                            </div>
                            <div class="d-flex flex-row">
                                <v-divider class="d-inline-flex flex-grow-1 my-3"></v-divider>
                            </div>
                            <div class="d-flex flex-row">
                                <div class="d-inline-flex flex-grow-1">
                                    Or select a custom period:
                                </div>
                            </div>
                            <div class="d-flex flex-row">
                                <div class="d-inline-flex flex-grow-1">
                                    <v-date-picker
                                        no-title
                                        v-model="builder.period.start"
                                    ></v-date-picker>
                                </div>
                                <div class="d-inline-flex flex-grow-1">
                                    <v-date-picker
                                        no-title
                                        v-model="builder.period.end"
                                        @input="activePeriod = null"
                                    ></v-date-picker>
                                </div>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
            <div class="d-inline-flex flex-glow-1 mx-2" style="width: 30%">
                <v-select
                    prepend-inner-icon="mdi-approximately-equal"
                    placeholder="Rounding up of times"
                    outlined
                    :items="roundingItems"
                    item-text="text"
                    item-value="value"
                    v-model="builder.round"
                >
                </v-select>
            </div>
            <div class="d-inline-flex flex-glow-1 ml-2" style="width: 30%">
                <v-select
                    :prepend-inner-icon="builder.sort.icon"
                    placeholder="Sorting"
                    outlined
                    :items="sortingItems"
                    item-text="text"
                    item-value="value"
                    v-model="builder.sort"
                    return-object
                >
                    <template v-slot:item="{ parent, item, on, attrs }">
                        <span>
                            <v-icon>{{item.icon}}</v-icon>
                            {{item.text}}
                        </span>
                    </template>
                </v-select>
            </div>
        </div>
        <v-card
            outlined
            class="d-flex flex-row px-6 py-2"
        >
            <div class="d-inline-flex align-center" style="min-width: 200px">
                Group entries by...
            </div>
            <div class="d-inline-flex">
                <v-btn
                    outlined
                    :color="themeColor"
                    class="square"
                    x-small
                >
                    <v-icon>mdi-calendar-today</v-icon>
                    Day
                </v-btn>
            </div>
            <v-spacer></v-spacer>
            <div class="d-inline-flex">
                <v-btn
                    outlined
                    :color="themeColor"
                    class="square"
                    x-small
                >
                    <v-icon>mdi-format-quote-open</v-icon>
                    Description
                </v-btn>
            </div>
            <div class="d-inline-flex">
                <v-btn
                    outlined
                    :color="themeColor"
                    class="square"
                    x-small
                >
                    <v-icon>mdi-calendar-week</v-icon>
                    Week
                </v-btn>
            </div>
            <div class="d-inline-flex">
                <v-btn
                    outlined
                    :color="themeColor"
                    class="square"
                    x-small
                >
                    <v-icon>mdi-currency-usd</v-icon>
                    Billability
                </v-btn>
            </div>
            <div class="d-inline-flex">
                <v-btn
                    outlined
                    :color="themeColor"
                    class="square"
                    x-small
                >
                    <v-icon>mdi-calendar-month</v-icon>
                    Month
                </v-btn>
            </div>
        </v-card>
    </v-container>
</template>

<style>
.v-expansion-panel {
    border-color: rgba(0, 0, 0, 0.42);
    border-width: 1px;
    border-style: solid;
}
.v-btn.square {
    min-width: 100px !important;
    min-height: 64px !important;
}
.v-btn.square i {
    width: 100%;
    padding-bottom: 5px;
}
.v-btn.square > span {
    flex-direction: column;
}
</style>

<script>
import EventBus from "../../components/EventBus";
import * as Helper from "./helper";
import moment from "moment-timezone";

export default {
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeColor: this.$store.state.themeColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            activePeriod: null,
            builder: {
                reportName: 'New report',
                report: null,
                period: {
                    start: moment().subtract(1, 'months').format('YYYY-MM-DD'),
                    end: moment().format('YYYY-MM-DD')
                },
                round: 0,
                sort: {
                    icon: 'mdi-sort-alphabetical-ascending',
                    value: 'alph-asc',
                    text: 'A - Z and chronologically'
                }
            },
            roundingItems: [
                {
                    value: 0,
                    text: 'Rounding up of times'
                },
                {
                    value: 1,
                    text: 'Rounding to full min.'
                },
                {
                    value: 5,
                    text: 'Rounding to full 5 min.'
                },
                {
                    value: 6,
                    text: 'Rounding to full 6 min.'
                },
                {
                    value: 10,
                    text: 'Rounding to full 10 min.'
                },
                {
                    value: 12,
                    text: 'Rounding to full 12 min.'
                },
                {
                    value: 15,
                    text: 'Rounding to full 15 min.'
                },
                {
                    value: 30,
                    text: 'Rounding to full 30 min.'
                },
                {
                    value: 45,
                    text: 'Rounding to full 45 min.'
                },
                {
                    value: 60,
                    text: 'Rounding to full hours'
                }
            ],
            sortingItems: [
                {
                    icon: 'mdi-sort-alphabetical-ascending',
                    value: 'alph-asc',
                    text: 'A - Z and chronologically'
                },
                {
                    icon: 'mdi-sort-alphabetical-descending',
                    value: 'chron-desc',
                    text: 'A - Z and chronologically descending'
                },
                {
                    icon: 'mdi-sort-numeric-descending',
                    value: 'duration-desc',
                    text: 'Duration, descending'
                },
                {
                    icon: 'mdi-sort-numeric-ascending',
                    value: 'duration-asc',
                    text: 'Duration, ascending'
                },
                {
                    icon: 'mdi-sort-descending',
                    value: 'revenue-desc',
                    text: 'Revenue, descending'
                },
                {
                    icon: 'mdi-sort-ascending',
                    value: 'revenue-asc',
                    text: 'Revenue, ascending'
                },
            ]
        }
    },
    created() {
        moment.updateLocale('en', {
            week: {
                dow: 1,
            },
        });
    },
    mounted() {
        // this.getTickets()
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
    },
    methods: {
        invertColor(hex, bw = true) {
            return Helper.invertColor(hex.substr(0, 7), bw);
        },
        setPeriod(periodKey = 'today') {
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
            this.builder.period.start = start ? start.format('YYYY-MM-DD') : start;
            this.builder.period.end = end.format('YYYY-MM-DD');
            this.activePeriod = null;
        },
        onClickOutsideHandler () {
            this.activePeriod = null
        }
    },
    watch: {

    },
}
</script>
