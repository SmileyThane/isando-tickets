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
            <!-- PERIOD -->
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
            <!-- ROUNDING -->
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
            <!-- SORTING  -->
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
        <!-- GROUPING -->
        <v-card
            outlined
            class="d-flex px-6 py-2 flex-row"
        >
            <div class="d-inline-flex align-center order-first" style="min-width: 150px">
                Group entries by...
            </div>
            <draggable
                :options="{ group:'grouping' }"
                v-model="builder.group"
                @start="draggable = true"
                @end="draggable = false"
                :style="{ 'min-width': '45%' }"
                class="d-inline-flex order-2 dragNDrop"
                :class="{ 'active': draggable }"
            >
                <v-btn
                    v-for="groupItem in builder.group"
                    :key="groupItem.value"
                    outlined
                    :color="themeColor"
                    class="square d-inline-flex mr-3 ml-2"
                    x-small
                    style="border-color: rgba(0,0,0,0)"
                    @dblclick="dblClickSelectGroupItem(groupItem)"
                >
                    <v-icon>{{groupItem.icon}}</v-icon>
                    {{groupItem.text}}
                </v-btn>
            </draggable>
            <draggable
                :options="{ group:'grouping' }"
                v-model="groupItems"
                @start="draggable = true"
                @end="draggable = false"
                :style="{ 'min-width': '40%' }"
                class="d-inline-block order-last dragNDrop common"
                :class="{ 'active': draggable }"
            >
                <v-btn
                    v-for="groupItem in groupItems"
                    :key="groupItem.value"
                    outlined
                    :color="themeColor"
                    class="square d-inline-flex mr-3 ml-2"
                    x-small
                    text
                    style="border-color: rgba(0,0,0,0)"
                    @dblclick="dblClickSelectGroupItem(groupItem)"
                >
                    <v-icon>{{groupItem.icon}}</v-icon>
                    {{groupItem.text}}
                </v-btn>
            </draggable>
        </v-card>
        <v-card
            outlined
            class="d-flex px-6 py-2 mt-3 flex-row"
        >
            <div class="d-inline-flex">
                Filter entries:
            </div>
            <div class="d-inline-flex flex-grow-1 mx-4">
                <div class="d-flex flex-column" style="width: 100%">
                    <div class="d-flex flex-row">
                        <a
                            class="mx-2 d-inline-flex"
                            :class="{ 'text-decoration-underline': builder.filters.find(i => i.value === filter.value) }"
                            :style="{ color: themeColor }"
                            v-for="filter in availableFilters"
                            :key="filter.value"
                            @click="activateFilter(filter)"
                        >
                            {{ filter.text }}
                        </a>
                        <div class="d-inline-flex flex-grow-1"></div>
                    </div>
                    <div
                        class="d-inline-block mx-2 mt-3"
                        v-if="builder.filters.length"
                    >
                        <div class="d-flex flex-column">
                            <div
                                class="d-inline-flex"
                                v-for="filter in builder.filters"
                                :key="filter.value"
                            >
                                <v-select
                                    :items="$store.getters[filter.store] || filter.items"
                                    :label="filter.text"
                                    return-object
                                    item-text="name"
                                    item-value="id"
                                    outlined
                                    :multiple="filter.multiply"
                                    dense
                                    clearable
                                    style="max-width: 900px; width: 100%"
                                ></v-select>
                                <v-btn
                                    color="danger"
                                    icon
                                    class="mx-4"
                                    @click="activateFilter(filter)"
                                >
                                    <v-icon>mdi-close-thick</v-icon>
                                </v-btn>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-card>
        <div class="d-flex py-2 mt-3 flex-row">
            <div class="d-inline-block flex-grow-0 mr-2">
                <div class="d-flex flex-column align-stretch">
                    <v-card
                        class="d-inline-flex mb-2 align-content-start pa-6"
                        outlined
                    >
                        <div class="d-flex flex-column">
                            <v-icon x-large class="d-inline-flex">mdi-clock-outline</v-icon>
                            <span class="d-inline-block text-center">Total time</span>
                            <span class="d-inline-block text-center">40:08 h</span>
                        </div>
                    </v-card>
                    <v-card
                        class="d-inline-flex mt-2 align-content-end pa-6"
                        outlined
                        x-large
                    >
                        <div class="d-flex flex-column">
                            <v-icon x-large class="d-inline-flex">mdi-cash-multiple</v-icon>
                            <span class="d-inline-block text-center">Revenue</span>
                            <span class="d-inline-block text-center">0 ₽</span>
                        </div>
                    </v-card>
                </div>
            </div>
            <v-card
                class="d-inline-block flex-grow-1 ml-2"
                outlined
            >
                <v-sparkline
                    :value="chart.value"
                    :gradient="chart.gradient"
                    :smooth="chart.radius || false"
                    :padding="chart.padding"
                    :line-width="chart.width"
                    :stroke-linecap="chart.lineCap"
                    :gradient-direction="chart.gradientDirection"
                    :fill="chart.fill"
                    :type="chart.type"
                    :auto-line-width="chart.autoLineWidth"
                    auto-draw
                ></v-sparkline>
            </v-card>
        </div>
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
.dragNDrop {
    border-width: 1px;
    border-style: dashed;
    border-color: white;
}
.dragNDrop button {
    cursor: move;
}
.dragNDrop.active {
    border-width: 1px;
    border-style: dashed;
    border-color: rgba(0,0,0,.12);
}
.dragNDrop:not(.common) > button:not(:last-of-type):after {
    font: normal normal normal 24px/1 "Material Design Icons";
    text-transform: none !important;
    speak: none;
    line-height: inherit;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    content: "\F0142";
    position: absolute;
    top: 50%;
    right: -25px;
    font-weight: bold;
    color: #a1a3a4;
    transform: translateY(-50%);
}
</style>

<script>
import EventBus from "../../components/EventBus";
import * as Helper from "./helper";
import moment from "moment-timezone";
import draggable from "vuedraggable";

const gradients = [
    ['#222'],
    ['#42b3f4'],
    ['red', 'orange', 'yellow'],
    ['purple', 'violet'],
    ['#00c6ff', '#F0F', '#FF0'],
    ['#f72047', '#ffd200', '#1feaea'],
];

export default {
    components: {
        draggable
    },
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            activePeriod: null,
            draggable: false,
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
                },
                group: [],
                filters: []
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
            ],
            groupItems: [
                {
                    icon: "mdi-calendar-today",
                    text: "Day",
                    value: "day"
                },
                {
                    icon: "mdi-format-quote-open",
                    text: "Description",
                    value: "description"
                },
                {
                    icon: "mdi-calendar-week",
                    text: "Week",
                    value: "week"
                },
                {
                    icon: "mdi-currency-usd",
                    text: "Billability",
                    value: "billability"
                },
                {
                    icon: "mdi-calendar-month",
                    text: "Month",
                    value: "month"
                }
            ],
            availableFilters: [
                {
                    value: 'coworkers',
                    text: 'Co-workers',
                    store: '',
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'clients&projects',
                    text: 'Clients&projects',
                    store: 'Projects/getProjects',
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'clients',
                    text: 'Clients',
                    store: 'Clients/getClients',
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'services',
                    text: 'Services',
                    store: 'Services/getServices',
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'billable',
                    text: 'Billable',
                    store: null,
                    items: [
                        {
                            id: 1,
                            name: 'Billable'
                        },
                        {
                            id: 0,
                            name: 'Non-billable'
                        }
                    ],
                    selected: null,
                    multiply: false
                }
            ],
            chart: {
                width: 2,
                radius: 10,
                padding: 8,
                lineCap: 'round',
                gradient: [this.$store.state.themeColor],
                value: [0, 2, 5, 9, 5, 10, 3, 5, 0, 0, 1, 8, 2, 9, 0],
                gradientDirection: 'top',
                gradients,
                fill: false,
                type: 'trend',
                autoLineWidth: false,
            }
        }
    },
    created() {
        moment.updateLocale(this.$store.state.lang.short_code, {
            week: {
                dow: 1,
            },
        });
        this.$store.dispatch('Clients/getClientList', { search: null });
        this.$store.dispatch('Services/getServicesList', { search: null });
        this.$store.dispatch('Projects/getProjectList', { search: null });
    },
    mounted() {
        // this.getTickets()
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
       EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
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
        },
        dblClickSelectGroupItem(groupItem) {
            this.groupItems = this.groupItems.concat(this.builder.group);
            this.groupItems = this.groupItems.filter(i => i.value !== groupItem.value);
            this.builder.group = [];
            this.builder.group.push(groupItem);
        },
        activateFilter(filter) {
            const index = this.builder.filters.findIndex(i => i.value === filter.value);
            if (index < 0) {
                filter.selected = [];
                this.builder.filters.push(filter);
            } else {
                this.builder.filters.splice(index, 1);
            }
        }
    },
    computed: {},
}
</script>
