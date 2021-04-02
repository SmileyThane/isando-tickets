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
                <v-dialog
                    v-model="dialogSave"
                    persistent
                    max-width="600px"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            :color="themeBgColor"
                            :style="{ color: invertColor(themeBgColor) }"
                            v-bind="attrs"
                            v-on="on"
                        >
                            Save
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title class="headline">
                            Save report
                        </v-card-title>
                        <v-card-text>
                            <div class="d-flex flex-column">
                                <div class="d-inline-block">
                                    <v-text-field
                                        placeholder="Report name"
                                        required
                                    ></v-text-field>
                                </div>
                                <div class="d-inline-block">
                                    <v-list dense>
                                        <v-subheader>REPORTS</v-subheader>
                                        <v-list-item-group
                                            color="primary"
                                        >
                                            <v-list-item
                                            >
                                                <v-list-item-content>
                                                    <v-list-item-title></v-list-item-title>
                                                </v-list-item-content>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                </div>
                            </div>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="green darken-1"
                                text
                                @click="dialogSave = false"
                            >
                                Close
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </div>
        </div>
        <div class="d-flex flex-row">
            <!-- PERIOD -->
            <div class="d-inline-flex flex-glow-0 mr-2" style="width: 550px; min-width: 550px; max-height: 55px">
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
                                    <a class="text-decoration-none" @click="setPeriod('today')" :style="{ color: themeBgColor }">Today</a>
                                    <a class="text-decoration-none" @click="setPeriod('yesterday')" :style="{ color: themeBgColor }">Yesterday</a>
                                    <a class="text-decoration-none" @click="setPeriod('last7days')" :style="{ color: themeBgColor }">Last 7 days</a>
                                    <a class="text-decoration-none" @click="setPeriod('last28days')" :style="{ color: themeBgColor }">Last 28 days</a>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('thisWeek')" :style="{ color: themeBgColor }">This week</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastWeek')" :style="{ color: themeBgColor }">Last week</a>
                                    <a class="text-decoration-none" @click="setPeriod('thisMonth')" :style="{ color: themeBgColor }">This month</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastMonth')" :style="{ color: themeBgColor }">Last month</a>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('thisQuarter')" :style="{ color: themeBgColor }">This quarter</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastQuarter')" :style="{ color: themeBgColor }">Last quarter</a>
                                    <a class="text-decoration-none" @click="setPeriod('thisYear')" :style="{ color: themeBgColor }">This year</a>
                                    <a class="text-decoration-none" @click="setPeriod('totalTime')" :style="{ color: themeBgColor }">Total time</a>
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
                                <div class="d-inline-flex">
                                    <vc-date-picker
                                        ref="calendar"
                                        v-model.sync="builder.period"
                                        :value.sync="builder.period"
                                        is-range
                                        no-title
                                        :step="1"
                                        :columns="2"
                                        mode="range"
                                        @input="activePeriod = null; genPreview()"
                                    ></vc-date-picker>
                                </div>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
            </div>
            <!-- ROUNDING -->
            <div class="d-inline-flex flex-glow-1 mx-2 hidden-sm-and-up" style="width: 100%">
                <v-select
                    prepend-inner-icon="mdi-approximately-equal"
                    placeholder="Rounding up of times"
                    outlined
                    :items="roundingItems"
                    item-text="text"
                    item-value="value"
                    v-model="builder.round"
                    disabled
                >
                </v-select>
            </div>
            <!-- SORTING  -->
            <div class="d-inline-flex flex-glow-1 ml-2" style="width: 100%">
                <v-select
                    :prepend-inner-icon="builder.sort.icon"
                    placeholder="Sorting"
                    outlined
                    :items="sortingItems"
                    item-text="text"
                    item-value="value"
                    v-model="builder.sort"
                    return-object
                    @input="genPreview"
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
            class="d-flex px-6 py-2 flex-row"
            outlined
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
                    :color="themeBgColor"
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
                    :color="themeBgColor"
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
        <!-- FILTERING. Generated automatically -->
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
                            :style="{ color: themeBgColor }"
                            v-for="filter in availableFilters"
                            :key="filter.value"
                            @click="toggleFilter(filter)"
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
                                    item-text="name"
                                    item-value="id"
                                    outlined
                                    :multiple="filter.multiply"
                                    dense
                                    clearable
                                    style="max-width: 900px; width: 100%"
                                    v-model="filter.selected"
                                    @input="genPreview"
                                ></v-select>
                                <v-btn
                                    color="danger"
                                    icon
                                    class="mx-4"
                                    @click="toggleFilter(filter)"
                                >
                                    <v-icon>mdi-close-thick</v-icon>
                                </v-btn>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </v-card>
        <!-- CHARTS -->
        <div
            class="d-flex py-2 mt-3 flex-row"
            v-if="doughnutData || barData"
        >
            <div class="d-inline-block flex-grow-0 mr-2">
                <div class="d-flex flex-column align-stretch">
                    <v-card
                        class="d-inline-flex mb-2 align-content-start pa-6"
                        outlined
                    >
                        <div class="d-flex flex-column">
                            <v-icon x-large class="d-inline-flex">mdi-clock-outline</v-icon>
                            <span class="d-inline-block text-center">Total time</span>
                            <span class="d-inline-block text-center">
                                {{ helperConvertSecondsToTimeFormat(totalTime) }}
                            </span>
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
                            <span class="d-inline-block text-center">
                                <span v-if="currentCurrency">{{currentCurrency.slug}}</span> {{totalRevenue}}
                            </span>
                        </div>
                    </v-card>
                </div>
            </div>
            <v-card
                class="d-inline-block flex-grow-1 ml-2"
                outlined
            >
                <div class="d-flex flex-row">
                    <div class="d-inline-flex px-3 py-3">
                        <DoughnutChart
                            :data="doughnutData"
                            :options="chart.options"
                        ></DoughnutChart>
                    </div>
                    <div class="d-inline-flex px-3 py-3">
                        <BarChart
                            v-if="barData"
                            :data="barData"
                            :options="chart.options"
                        ></BarChart>
                    </div>
                </div>
            </v-card>
        </div>
        <!-- DATA -->
        <v-card class="d-flex flex-column">

            <v-treeview
                :items="reportData.entities"
                item-children="children"
                item-key="name"
                dense
                open-on-click
            >
                <template v-slot:label="{ item, selected, active, open }">
                    <div v-if="item.children" style="font-size: small">
                        {{ item.name }}
                    </div>
                    <div v-else class="d-flex flex-row">
                        <table border="0" cellspacing="0" cellpadding="5" width="100%" style="font-size: small">
                            <tbody>
                                <tr>
                                    <td class="pa-2" align="right" width="10%">
                                        {{ moment(item.date_from).format('DD MMM YYYY') }}
                                    </td>
                                    <td class="pa-2" align="left" width="15%">
                                        <span v-if="item.user">{{ item.user.full_name }}</span><span v-else>None</span>
                                    </td>
                                    <td class="pa-2" align="left">
                                        <span v-if="item.entity">{{ item.entity.name }}</span>
                                        <v-icon v-if="item.entity && item.service" class="ma-1" x-small>mdi-checkbox-blank-circle</v-icon>
                                        <span v-if="item.service">{{ item.service.name }}</span>
                                        <v-icon v-if="(item.service && item.description) || (item.entity && item.description)" class="ma-1" x-small>mdi-checkbox-blank-circle</v-icon>
                                        <span v-if="item.description">{{ item.description }}</span>
                                    </td>
                                    <td class="pa-2" align="right" width="10%">
                                        <v-chip
                                            v-for="tag in item.tags"
                                            :key="tag.id"
                                            :color="tag.color"
                                            :text-color="invertColor(tag.color)"
                                            v-text="tag.name"
                                            small
                                        ></v-chip>
                                    </td>
                                    <td class="pa-2" align="center" width="5%">
                                        <span v-if="item.billable">Billable</span>
                                        <span v-else>Non-billable</span>
                                    </td>
                                    <td class="pa-2" align="right" width="5%">
                                        {{ helperConvertSecondsToTimeFormat(helperCalculatePassedTime(item.date_from, item.date_to), false) }}
                                    </td>
                                    <td class="pa-2" align="right" width="5%">
                                        <span v-if="currentCurrency">
                                            {{ currentCurrency.slug }}
                                        </span> {{ parseFloat(item.revenue).toFixed(2) }}
                                    </td>
                                    <td class="pa-2" align="center" width="5%">
                                        <v-dialog
                                            v-model="dialogEdit"
                                            width="500"
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-btn
                                                    icon
                                                    v-bind="attrs"
                                                    v-on="on"
                                                    x-small
                                                    @click="openEditDialog(item)"
                                                >
                                                    <v-icon>mdi-pencil</v-icon>
                                                </v-btn>
                                            </template>

                                            <v-card>
                                                <v-card-title>
                                                    Edit
                                                </v-card-title>
                                                <v-card-text>
                                                    <div class="d-flex flex-row">
                                                        <div class="d-flex-inline">
                                                            <TimeField
                                                                v-model="editForm.date_from"
                                                                style="max-width: 100px; height: 40px"
                                                                label="Date start"
                                                                placeholder="hh:mm"
                                                                format="HH:mm"
                                                            ></TimeField>
                                                        </div>
                                                        <div class="d-flex-inline">
                                                            <TimeField
                                                                v-model="editForm.date_to"
                                                                style="max-width: 100px; height: 40px"
                                                                label="Date to"
                                                                placeholder="hh:mm"
                                                                format="HH:mm"
                                                            ></TimeField>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-row">
                                                        <div class="d-flex-inline">
                                                            <v-checkbox
                                                                v-model="editForm.billable"
                                                                label="Billable"
                                                            ></v-checkbox>
                                                        </div>
                                                    </div>
                                                </v-card-text>
                                                <v-divider></v-divider>
                                                <v-card-actions>
                                                    <v-spacer></v-spacer>
                                                    <v-btn
                                                        color="error"
                                                        text
                                                        @click="closeEditDialog"
                                                    >
                                                        Cancel
                                                    </v-btn>
                                                    <v-btn
                                                        color="success"
                                                        text
                                                        @click="saveChanges(item); closeEditDialog()"
                                                    >
                                                        Change
                                                    </v-btn>
                                                </v-card-actions>
                                            </v-card>
                                        </v-dialog>
                                        <v-btn
                                            icon
                                            x-small
                                            @click="removeTrack(item.id)"
                                        >
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </v-treeview>

            <v-card-actions
                class="white justify-center"
                v-if="reportData.entities && reportData.entities.length"
            >
                <v-dialog
                    v-model="dialogExportPDF"
                    persistent
                    max-width="600px"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            :color="themeBgColor"
                            :style="{ color: invertColor(themeBgColor) }"
                            v-bind="attrs"
                            v-on="on"
                            class="mx-3"
                        >
                            <v-icon>mdi-file-pdf-box-outline</v-icon>
                            Create PDF
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">Create PDF</span>
                        </v-card-title>
                        <v-card-text>
                            <!-- FORM -->
                            <div class="d-flex flex-column">
                                <div class="d-inline-block">
                                    <v-text-field
                                        label="Name of the report"
                                        v-model="report.pdf.name"
                                    ></v-text-field>
                                </div>
                                <div class="d-inline-block">
                                    <v-text-field
                                        label="Co-workers"
                                        v-model="report.pdf.coworkers"
                                    ></v-text-field>
                                </div>
                                <div class="d-inline-block">
                                    <v-text-field
                                        label="Period"
                                        v-model="report.pdf.periodText"
                                    ></v-text-field>
                                </div>
                                <div class="d-inline-block">
                                    <v-select
                                        :items="report.groupItems"
                                        item-text="text"
                                        item-value="value"
                                        label="Group"
                                        v-model="report.pdf.groupSel"
                                    >
                                    </v-select>
                                </div>
                            </div>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="error"
                                text
                                @click="dialogExportPDF = false"
                            >
                                Cancel
                            </v-btn>
                            <v-btn
                                color="success"
                                text
                                @click="createFile('pdf')"
                            >
                                Create
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <v-dialog
                    v-model="dialogExportCSV"
                    persistent
                    max-width="600px"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            :color="themeBgColor"
                            :style="{ color: invertColor(themeBgColor) }"
                            v-bind="attrs"
                            v-on="on"
                            class="mx-3"
                        >
                            <v-icon>mdi-file-delimited-outline</v-icon>
                            CSV Export
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">CSV Export</span>
                        </v-card-title>
                        <v-card-text>
                            <div class="d-flex flex-column">
                                <div class="d-inline-block">
                                    <v-select
                                        :items="csvFormGroupingEntries"
                                        v-model="report.csv.group"
                                        item-value="text"
                                        item-text="text"
                                        return-object
                                        label="Grouped times"
                                    ></v-select>
                                </div>
<!--                                <div class="d-inline-block">-->
<!--                                    <v-select-->
<!--                                        :items="csvForm.timeFormatItems"-->
<!--                                        v-model="report.csv.timeFormat"-->
<!--                                        label="Time format"-->
<!--                                    ></v-select>-->
<!--                                </div>-->
<!--                                <div class="d-inline-block">-->
<!--                                    <v-select-->
<!--                                        :items="csvForm.dateFormatItems"-->
<!--                                        v-model="report.csv.dateFormat"-->
<!--                                        label="Date format"-->
<!--                                    ></v-select>-->
<!--                                </div>-->
                            </div>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="error"
                                text
                                @click="dialogExportCSV = false"
                            >
                                Cancel
                            </v-btn>
                            <v-btn
                                color="success"
                                text
                                @click="createFile('csv'); dialogExportCSV = false"
                            >
                                Export
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<style>
.border-right {
    border-right: thin solid rgba(0,0,0,.12);
}
.width-10 {
    width: 10%;
    max-width: 10%;
    min-width: 10%;
}
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
import BarChart from "./components/bar-chart";
import DoughnutChart from "./components/doughnut-chart";
import _ from "lodash";
import TimeField from "./components/time-field";

export default {
    components: {
        draggable,
        DoughnutChart,
        BarChart,
        TimeField
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
            activePeriod: null,
            draggable: false,
            builder: {
                reportName: 'Report',
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
                group: [
                    {
                        icon: "mdi-domain",
                        text: "Services",
                        value: "service"
                    }
                ],
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
                    value: 10,
                    text: 'Rounding to full 10 min.'
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
                // {
                //     icon: 'mdi-sort-numeric-descending',
                //     value: 'duration-desc',
                //     text: 'Duration, descending'
                // },
                // {
                //     icon: 'mdi-sort-numeric-ascending',
                //     value: 'duration-asc',
                //     text: 'Duration, ascending'
                // },
                // {
                //     icon: 'mdi-sort-descending',
                //     value: 'revenue-desc',
                //     text: 'Revenue, descending'
                // },
                // {
                //     icon: 'mdi-sort-ascending',
                //     value: 'revenue-asc',
                //     text: 'Revenue, ascending'
                // },
            ],
            groupItems: [
                {
                    icon: "mdi-calendar-today",
                    text: "Day",
                    value: "day"
                },
                {
                    icon: "mdi-folder-account-outline",
                    text: "Projects",
                    value: "project"
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
                    icon: "mdi-account",
                    text: "Clients",
                    value: "client"
                },
                {
                    icon: "mdi-calendar-month",
                    text: "Month",
                    value: "month"
                },
                {
                    icon: "mdi-account-multiple",
                    text: "Co-worker",
                    value: "coworker"
                },
            ],
            availableFilters: [
                {
                    value: 'coworkers',
                    text: 'Co-worker',
                    store: 'Team/getCoworkers',
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'projects',
                    text: 'Projects',
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
                options: {
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
                                return self.helperConvertSecondsToTimeFormat(data.datasets[0].data[tooltipItem.index] * 60 * 60);
                            }
                        }
                    }
                }
            },
            reportData: {
                headers: [
                    { text: 'Description', value: 'description' },
                    { text: 'Client', value: 'project.client.name' },
                    {
                        text: 'Project',
                        align: 'start',
                        sortable: false,
                        value: 'project.name',
                    },
                    { text: 'Billable', value: 'billable' },
                    { text: 'Date from', value: 'date_from' },
                    { text: 'Date to', value: 'date_to' },
                    { text: 'Passed', value: 'passed' },
                    { text: '', value: 'data-table-expand' },
                ],
                entities: []
            },
            dialogExportPDF: false,
            dialogExportCSV: false,
            dialogPrint: false,
            dialogSave: false,
            dialogEdit: false,
            editForm: {
                id: null,
                date_from: null,
                date_to: null,
                billable: null
            },
            report: {
                groupItems: [
                    {
                        value: 1,
                        text: 'Sort chronologically & do not group',
                        items: []
                    },
                    {
                        value: 2,
                        text: 'Group by service (Coming soon)',
                        items: []
                    }
                ],
                pdf: {
                    name: 'Report',
                    coworkers: null,
                    periodText: '',
                    groupSel: 1
                },
                csv: {
                    group: {
                        value: 'all_no_group',
                        text: 'All single entries, sorted based on the grouping',
                        items: []
                    },
                    timeFormat: 4,
                    dateFormat: 2
                }
            },
            csvForm: {
                groupItems: [
                    {
                        value: 'all_no_group',
                        text: 'All single entries, sorted based on the grouping',
                        items: []
                    },
                    {
                        value: 'all_chron',
                        text: 'All single entries, chronologically sorted',
                        items: []
                    }
                ],
                timeFormatItems: [
                    {
                        value: 1,
                        text: 'Time in seconds'
                    },
                    {
                        value: 2,
                        text: 'Time in minutes (mm:ss)'
                    },
                    {
                        value: 3,
                        text: 'Time in minutes (decimal)'
                    },
                    {
                        value: 4,
                        text: 'Time in hours (hh:mm)'
                    },
                    {
                        value: 5,
                        text: 'Time in hours (decimal)'
                    }
                ],
                dateFormatItems: [
                    {
                        value: 1,
                        text: 'Machine-readable (YYYY-MM-DD)'
                    },
                    {
                        value: 2,
                        text: 'In text form (as in the report)'
                    }
                ],
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
        this.$store.dispatch('Team/getCoworkers', { search: null });
        this.debounceGetSettings = _.debounce(this.__getSettings, 1000);
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.setPeriod();
        this.debounceGetSettings();
    },
    methods: {
        __getSettings() {
            this.$store.dispatch('Tracking/getSettings');
        },
        invertColor(hex, bw = true) {
            return Helper.invertColor(hex.substr(0, 7), bw);
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
            this.builder.period.start = start ? moment(start).toDate() : start;
            this.builder.period.end = moment(end).toDate();
            const calendar = this.$refs.calendar;
            if (calendar) {
                await calendar.updateValue({
                    start: this.builder.period.start ? moment(this.builder.period.start).toDate() : moment('2020-01-01').toDate(),
                    end: moment(this.builder.period.end).toDate()
                }, {
                    formatInput: true,
                    hidePopover: false
                });
                calendar.$refs.calendar.showPageRange({
                    from: this.builder.period.start,
                    to: this.builder.period.end
                });
            }
            this.activePeriod = null;
            this.genPreview();
        },
        onClickOutsideHandler() {
            this.activePeriod = null
        },
        dblClickSelectGroupItem(groupItem) {
            this.groupItems = this.groupItems.concat(this.builder.group);
            this.groupItems = this.groupItems.filter(i => i.value !== groupItem.value);
            this.builder.group = [];
            this.builder.group.push(groupItem);
        },
        toggleFilter(filter) {
            const index = this.builder.filters.findIndex(i => i.value === filter.value);
            if (index < 0) {
                filter.selected = [];
                this.builder.filters.push(filter);
            } else {
                this.builder.filters.splice(index, 1);
                if (filter.value === 'coworkers') {
                    this.report.pdf.coworkers = '';
                }
            }
        },
        genPreview() {
            const coworkers = this.builder.filters.find(i => i.value === 'coworkers');
            if (coworkers) {
                const list = this.$store.getters['Team/getCoworkers'];
                this.report.pdf.coworkers = list.filter(x => coworkers.selected.indexOf(x.id) >= 0)
                                                .map(i => i.full_name)
                                                .join(', ');

            }
            axios.post('/api/tracking/reports', this.builder)
                .then(({ data: { data } }) => {
                    this.reportData.entities = data;

                })
                .catch(err => {
                    console.log(err);
                    this.snackbarMessage = 'Something went wrong';
                    this.actionColor = 'error'
                    this.snackbar = true;
                });
        },
        helperAddZeros(num, len) {
            while((""+num).length < len) num = "0" + num;
            return num.toString();
        },
        helperConvertSecondsToTimeFormat(seconds, withSeconds = true) {
            if (!seconds) {
                return `00:00:00`;
            }
            const h = Math.floor(seconds / 60 / 60);
            const m = Math.floor((seconds - h * 60 * 60) / 60);
            const s = seconds - (m * 60) - (h * 60 * 60);
            if (withSeconds) {
                return `${this.helperAddZeros(h.toFixed(0),2)}:${this.helperAddZeros(m.toFixed(0),2)}:${this.helperAddZeros(s.toFixed(0),2)}`;
            }
            return `${this.helperAddZeros(h.toFixed(0),2)}:${this.helperAddZeros(m.toFixed(0),2)}`;
        },
        helperCalculatePassedTime(date_from, date_to) {
            if (moment(date_from) > moment(date_to)) {
                date_to = moment(date_to).add(1, 'day');
            }
            return moment(date_to).diff(moment(date_from), 'seconds');
        },
        calculateTime(entries, seconds = 0) {
            if (!entries) return seconds;
            entries.map(i => {
                if (i.children) {
                    seconds += this.calculateTime(i.children);
                } else {
                    seconds += this.helperCalculatePassedTime(i.date_from, i.date_to);
                }
            });
            return seconds;
        },
        calculateRevenue(entries, revenue = 0) {
            if (!entries) return revenue;
            entries.map(i => {
                if (i.children) {
                    revenue += parseFloat(this.calculateRevenue(i.children));
                } else {
                    if (i.billable) {
                        revenue += parseFloat(i.revenue);
                    }
                }
            });
            return revenue ? parseFloat(revenue).toFixed(2) : 0;
        },
        createFile(format) {
            if (format === 'csv' && this.report.csv.group && this.report.csv.group.value === 'all_chron') {
                this.builder.sort = {
                    icon: 'mdi-sort-alphabetical-ascending',
                    value: 'alph-asc',
                    text: 'A - Z and chronologically'
                };
            }
            axios.post(`/api/tracking/reports?format=${format}`, { ...this.builder, ...this.report[format] }, {
                responseType: 'blob'
            })
                .then(res => {
                    // const filename = res.headers['content-disposition'].split('filename=')[1].split(';')[0];

                    const url = window.URL.createObjectURL(new Blob([res.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `Report.${format}`);
                    document.body.appendChild(link);
                    link.click();
                    this.dialogExportPDF = false;
                    this.dialogExportCSV = false;
                })
                .catch(err => {
                    console.log(err);
                    this.snackbarMessage = 'Something went wrong';
                    this.actionColor = 'error'
                    this.snackbar = true;
                })
        },
        saveChanges() {
            this.$store.dispatch('Tracking/updateTrack', {
                id: this.editForm.id,
                date_from: this.editForm.date_from,
                date_to: this.editForm.date_to,
                billable: this.editForm.billable
            })
                .then(successResult => {
                    if (successResult) {
                        this.genPreview();
                    }
                });
        },
        removeTrack(trackId) {
            // TODO
        },
        closeEditDialog() {
            this.editForm.id = null;
            this.editForm.date_from = null;
            this.editForm.date_to = null;
            this.editForm.billable = null;
            this.dialogEdit = false;
        },
        openEditDialog(item) {
            this.editForm.id = item.id;
            this.editForm.date_from = item.date_from;
            this.editForm.date_to = item.date_to;
            this.editForm.billable = item.billable;
            this.dialogEdit = true;
        }
    },
    computed: {
        totalTime: function() {
            return this.calculateTime(this.reportData.entities);
        },
        totalRevenue: function() {
            return this.calculateRevenue(this.reportData.entities);
        },
        doughnutData: function() {
            if (this.reportData.entities && this.reportData.entities.length) {
                let data = {
                    labels: [],
                    datasets: []
                };
                data.datasets = [];
                let values = [];
                let labels = [];
                this.reportData.entities.map(i => {
                    data.labels.push(i.name ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                    if (i.name) {
                        labels.push(i.name ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                    }
                    if (i.children) {
                        values.push((this.calculateTime(i.children) / 60 / 60).toFixed(2));
                    } else {
                        values.push((this.helperCalculatePassedTime(i.date_from, i.date_to) / 60 / 60).toFixed(2));
                    }
                });
                let colors = [];
                for (let i = 0; i <= values.length - 1; i++) {
                    colors.push(Helper.genRandomColor());
                }
                data.datasets.push({
                    label: labels,
                    backgroundColor: colors,
                    data: values
                });
                return data;
            }
            return null;
        },
        barData: function() {
            if (this.reportData.entities && this.reportData.entities.length) {
                let data = {
                    labels: [],
                    datasets: []
                };
                data.datasets = [];
                let values = [];
                let labels = [];
                let colors = [];
                this.reportData.entities.map(i => {
                    data.labels.push(i.name ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                    if (i.name) {
                        labels.push(i.name ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                    }
                    if (i.children) {
                        values.push(
                            (this.calculateTime(i.children) / 60 / 60).toFixed(2)
                        );
                    } else {
                        values.push(
                            (this.helperCalculatePassedTime(i.date_from, i.date_to) / 60 / 60).toFixed(2)
                        );
                    }
                });
                const c = Helper.genRandomColor();
                for (let i = 0; i <= values.length - 1; i++) {
                    colors.push(c);
                }
                data.datasets.push({
                    label: labels,
                    backgroundColor: colors,
                    data: values
                });
                return data;
            }
            return null;
        },
        csvFormGroupingEntries: function () {
            let items = [];
            let k = this.builder.group.length;
            this.builder.group.map((x, i) => {
                const groups = this.builder.group.filter((v, i) => i < k);
                items.push({
                    text: groups.map(v => v.text).join(' / '),
                    items: groups,
                    value: 'custom'
                });
                k--;
            });
            return items.concat(this.csvForm.groupItems);
        },
        currentCurrency: {
            get: function () {
                const settings = this.$store.getters['Tracking/getSettings'];
                return settings.currency ?? null;
            },
            set: function (currency) {
                this.$store.dispatch('Tracking/updateSettings', {currency});
            }
        }
    },
    watch: {
        'builder.filters': function() {
            this.genPreview();
        },
        'builder.group': function() {
            this.genPreview();
        },
        'builder.period': function () {
            if (this.builder.period.start) {
                if (this.builder.period.start === this.builder.period.end) {
                    this.report.pdf.periodText = `${moment(this.builder.period.start).format('ddd D MMM YYYY')}`;
                } else {
                    this.report.pdf.periodText = `${moment(this.builder.period.start).format('ddd D MMM YYYY')} - ${moment(this.builder.period.end).format('ddd D MMM YYYY')}`;
                }
            } else {
                this.report.pdf.periodText = `... - ${moment(this.builder.period.end).format('ddd D MMM YYYY')}`;
            }
        },
        activePeriod: function () {
            const calendar = this.$refs.calendar;
            if (calendar) {
                if (moment(this.builder.period.start).format('YYYY-MM') !== moment(this.builder.period.end).format('YYYY-MM')) {
                    calendar.$refs.calendar.showPageRange({
                        from: moment(this.builder.period.start).toDate(),
                        to: moment(this.builder.period.end).toDate()
                    });
                } else {
                    calendar.$refs.calendar.showPageRange({
                        from: moment(this.builder.period.start).subtract(1, 'months').toDate(),
                        to: moment(this.builder.period.end).toDate()
                    });
                }
            }
        }
    }
}
</script>
