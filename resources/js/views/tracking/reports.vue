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
                    :placeholder="langMap.tracking.report.choose_report"
                    outlined
                    :items="$store.getters['Tracking/getReports']"
                    item-text="name"
                    item-value="id"
                    v-model="report.selected"
                    @input="selectReport"
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
                            :style="{ color: $helpers.color.invertColor(themeBgColor) }"
                            v-bind="attrs"
                            v-on="on"
                        >
                            {{ langMap.tracking.report.save_btn }}
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title class="headline">
                            {{ langMap.tracking.report.save_report }}
                        </v-card-title>
                        <v-card-text>
                            <div class="d-flex flex-column">
                                <div class="d-inline-block">
                                    <div class="d-flex flex-row">
                                        <div class="d-inline-flex flex-grow-1">
                                            <v-text-field
                                                :placeholder="langMap.tracking.report.report_name"
                                                required
                                                v-model="builder.reportName"
                                            ></v-text-field>
                                        </div>
                                        <div class="d-inline-flex flex-grow-0 pt-3">
                                            <v-btn
                                                :color="themeBgColor"
                                                :style="{ color: $helpers.color.invertColor(themeBgColor) }"
                                                @click="saveReport()"
                                            >
                                                {{ langMap.tracking.report.save_btn }}
                                            </v-btn>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-inline-block" v-if="$store.getters['Tracking/getReports'].length">
                                    <v-list dense>
                                        <v-subheader>{{ langMap.tracking.report.reports }}</v-subheader>
                                        <v-list-item-group
                                            color="primary"
                                        >
                                            <v-list-item
                                                v-for="report in $store.getters['Tracking/getReports']"
                                                :key="report.id"
                                            >
                                                <v-list-item-content>
                                                    <v-list-item-title class="d-flex flex-row">
                                                        <div
                                                            class="d-inline-flex flex-grow-1"
                                                            @click="selectReport(report.id)"
                                                        >
                                                            {{ report.name }}
                                                            <small
                                                                style="color: gray"
                                                            >
                                                                &nbsp;({{ langMap.tracking.report.from }}
                                                                {{ moment(report.created_at).format('DD/MM/YYYY H:m:ss') }})
                                                            </small>
                                                        </div>
                                                        <div class="d-inline-flex flex-grow-0">
                                                            <v-btn
                                                                class="mx-2"
                                                                icon
                                                                x-small
                                                                color="error"
                                                                @click="deleteReport(report.id)"
                                                            >
                                                                <v-icon dark>
                                                                    mdi-delete
                                                                </v-icon>
                                                            </v-btn>
                                                        </div>
                                                    </v-list-item-title>
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
                                {{ langMap.tracking.report.close_btn }}
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
                                    {{ langMap.tracking.report.time_period }}:
                                </div>
                                <div class="d-inline-flex float-right flex-grow-1 text-center">
                                    <v-fade-transition leave-absolute>
                                        <span v-if="open" class="d-block flex-grow-1">{{
                                                langMap.tracking.report.choose_period
                                            }}</span>
                                        <span v-else class="d-block flex-grow-1">
                                            {{
                                                builder.period.start ? moment(builder.period.start).format('ddd D MMM YYYY') : '...'
                                            }} -
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
                                    <a class="text-decoration-none" @click="setPeriod('today')"
                                       :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_today }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('yesterday')"
                                       :style="{ color: themeBgColor }">{{ langMap.tracking.report.period_yesterday }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('last7days')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_last7days
                                        }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('last28days')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_last28days
                                        }}</a>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('thisWeek')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_this_week
                                        }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastWeek')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_last_week
                                        }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('thisMonth')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_this_month
                                        }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastMonth')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_last_month
                                        }}</a>
                                </div>
                                <div class="d-flex flex-column flex-grow-1">
                                    <a class="text-decoration-none" @click="setPeriod('thisQuarter')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_this_quarter
                                        }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('lastQuarter')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_last_quarter
                                        }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('thisYear')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_this_year
                                        }}</a>
                                    <a class="text-decoration-none" @click="setPeriod('totalTime')"
                                       :style="{ color: themeBgColor }">{{
                                            langMap.tracking.report.period_total_time
                                        }}</a>
                                </div>
                            </div>
                            <div class="d-flex flex-row">
                                <v-divider class="d-inline-flex flex-grow-1 my-3"></v-divider>
                            </div>
                            <div class="d-flex flex-row">
                                <div class="d-inline-flex flex-grow-1">
                                    {{ langMap.tracking.report.select_custom_period }}:
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
                                        @input="activePeriod = null; debounceGenPreview(); resetSelectedReport()"
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
                    :placeholder="langMap.tracking.report.rounding_up_of_times"
                    outlined
                    :items="availableRoundingItems"
                    item-text="text"
                    item-value="value"
                    v-model="builder.round"
                    @input="resetSelectedReport()"
                >
                </v-select>
            </div>
            <!-- SORTING  -->
            <div class="d-inline-flex flex-glow-1 ml-2" style="width: 100%">
                <v-select
                    :prepend-inner-icon="builder.sort.icon"
                    :placeholder="langMap.tracking.report.sorting"
                    outlined
                    :items="sortingItems"
                    item-text="text"
                    item-value="value"
                    v-model="builder.sort"
                    return-object
                    @input="debounceGenPreview(); resetSelectedReport()"
                >
                    <template v-slot:item="{ parent, item, on, attrs }">
                        <span>
                            <v-icon>{{ item.icon }}</v-icon>
                            {{ item.text }}
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
                {{ langMap.tracking.report.grouping }}
            </div>
            <draggable
                :options="{ group:'grouping' }"
                v-model="builder.group"
                @start="draggable = true"
                @end="draggable = false"
                :style="{ 'min-width': '45%' }"
                class="d-inline-flex order-2 dragNDrop"
                :class="{ 'active': draggable }"
                @input="resetSelectedReport()"
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
                    <v-icon>{{ groupItem.icon }}</v-icon>
                    {{ groupItem.text }}
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
                @input="resetSelectedReport()"
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
                    <v-icon>{{ groupItem.icon }}</v-icon>
                    {{ groupItem.text }}
                </v-btn>
            </draggable>
        </v-card>
        <!-- FILTERING. Generated automatically -->
        <v-card
            outlined
            class="d-flex px-6 py-2 mt-3 flex-row"
        >
            <div class="d-inline-flex">
                {{ langMap.tracking.report.filter }}:
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
                                    @input="clearFiltersAfter(filter); debounceGenPreview(); resetSelectedReport()"
                                    @change=""
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
        <!-- DATA -->
        <v-card class="d-flex flex-column" style="min-height: 500px">

            <v-overlay
                absolute
                :opacity="0.26"
                :value="loadingGeneratePreview"
                v-if="loadingGeneratePreview"
            >
                <v-progress-circular
                    indeterminate
                    size="64"
                ></v-progress-circular>
            </v-overlay>

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
                                <span class="d-inline-block text-center">{{ langMap.tracking.report.total_time }}</span>
                                <span class="d-inline-block text-center">
                                {{ $helpers.time.convertSecToTime(totalTime, false) }}
                            </span>
                            </div>
                        </v-card>
                        <v-card
                            class="d-inline-flex mt-2 align-content-end pa-6"
                            outlined
                            x-large
                            v-if="$helpers.auth.checkPermissionByIds([69])"
                        >
                            <div class="d-flex flex-column">
                                <v-icon x-large class="d-inline-flex">mdi-cash-multiple</v-icon>
                                <span class="d-inline-block text-center">{{ langMap.tracking.report.revenue }}</span>
                                <span class="d-inline-block text-center">
                                <span
                                    v-if="currentCurrency">{{ currentCurrency.slug }}</span> {{ $helpers.numbers.numberFormat(totalRevenue, 2) }}
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

            <v-treeview
                :items="reportData.entities.g1"
                item-children="children"
                item-key="name"
                dense
                open-on-click
            >
                <template v-slot:label="{ item, selected, active, open }">
                    <div v-if="item.children" style="font-size: small">
                        {{ item.name }} <span v-if="item.client">({{ item.client }})</span>
                    </div>
                    <div v-else class="d-flex flex-row">
                        <table class="v-data-table" :class="item.status === 'started' ? 'success lighten-5' : ''"
                               border="0" cellspacing="0" cellpadding="5" width="100%" style="font-size: small">
                            <tbody>
                            <tr>
                                <td class="pa-2" align="right" width="10%">
                                    {{ moment(item.date_from).format('DD MMM YYYY') }}
                                </td>
                                <td class="pa-2" align="left" width="15%">
                                    <span v-if="item.user">{{ item.user.full_name }}</span><span
                                    v-else>{{ langMap.tracking.report.none }}</span>
                                </td>
                                <td class="pa-2" align="left">
                                        <span
                                            v-if="item.entity"
                                            :style="{ color: item.entity && item.entity.color ? item.entity.color : themeBgColor }"
                                        >
                                            <span v-if="item.entity_type === 'App\\TrackingProject'">{{
                                                    getTrackingProjectLabel
                                                }}: </span>
                                            <span v-if="item.entity_type === 'App\\Ticket'">{{
                                                    langMap.tracking.report.ticket
                                                }}: </span>
                                            {{ item.entity.name }}
                                        </span>
                                    <v-icon v-if="item.entity && item.service" class="ma-1" x-small>mdi-checkbox-blank-circle</v-icon>
                                    <span v-if="item.service">{{ item.service.name }}</span>
                                    <v-icon v-if="(item.service && item.description) || (item.entity && item.description)" class="ma-1" x-small>mdi-checkbox-blank-circle</v-icon>
                                    <span v-if="item.description">
                                            <v-tooltip top>
                                              <template v-slot:activator="{ on, attrs }">
                                                <span
                                                    v-bind="attrs"
                                                    v-on="on"
                                                >{{ $helpers.string.shortenText(item.description, 60) }}</span>
                                              </template>
                                              <span>{{ item.description }}</span>
                                            </v-tooltip>
                                        </span>
                                </td>
                                <td class="pa-2" align="right" width="10%">
                                    <TagField
                                        disabled
                                        readonly
                                        :key="item.id"
                                        :color="themeBgColor"
                                        v-model="item.tags"
                                    ></TagField>
                                </td>
                                <td class="pa-2" align="center" width="5%">
                                    <span v-if="item.billable">{{ langMap.tracking.report.billable }}</span>
                                    <span v-else>{{ langMap.tracking.report.non_billable }}</span>
                                </td>
                                <td class="pa-2" align="right" width="5%">
                                    {{ $helpers.time.convertSecToTime(item.passed, false) }}
                                </td>
                                <td class="pa-2" align="right" width="5%">
                                        <span v-if="$helpers.auth.checkPermissionByIds([69])">
                                            <span v-if="currentCurrency">
                                                {{ currentCurrency.slug }}
                                            </span> {{ $helpers.numbers.numberFormat(item.revenue) }}
                                        </span>
                                </td>
                                <td class="pa-2" align="center" width="5%">
                                        <span
                                            v-if="item.status === 'started'"
                                            @click="stopTrack(item.id)"
                                        >
                                            <v-icon>mdi-stop</v-icon>
                                        </span>
                                    <span class="pl-7" v-if="item.status !== 'started'"></span>
                                    <v-dialog
                                        v-if="isEditable(item)"
                                        v-model="dialogEdit[item.id]"
                                        width="500"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                v-if="isEditable(item)"
                                                icon
                                                v-bind="attrs"
                                                v-on="on"
                                                x-small
                                                @click="openEditDialog(item)"
                                            >
                                                <v-icon>mdi-pencil</v-icon>
                                            </v-btn>
                                        </template>

                                        <v-card v-if="isEditable(item)">
                                            <v-card-title>
                                                {{ langMap.tracking.report.edit }}
                                            </v-card-title>
                                            <v-card-text>
                                                <div class="d-flex flex-row">
                                                    <div class="d-flex-inline flex-grow-1">
                                                        <v-select
                                                            :items="$store.getters['Services/getServices']"
                                                            :label="langMap.tracking.tracker.service_type"
                                                            :placeholder="langMap.tracking.tracker.service_type"
                                                            item-text="name"
                                                            item-value="id"
                                                            v-model="editForm.service"
                                                            return-object
                                                            dense
                                                        ></v-select>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-row">
                                                    <div class="d-flex-inline flex-grow-1 mb-5">
                                                        <ProjectBtn
                                                            :color="themeBgColor"
                                                            v-model="editForm.entity"
                                                            :label="langMap.tracking.report.project_or_Ticket"
                                                        ></ProjectBtn>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-row">
                                                    <div class="d-flex-inline flex-grow-1">
                                                        <TimeField
                                                            v-model="editForm.date_from"
                                                            style="max-width: 100px; height: 40px"
                                                            :label="langMap.tracking.report.date_start"
                                                            placeholder="hh:mm"
                                                            format="HH:mm"
                                                        ></TimeField>
                                                    </div>
                                                    <div class="d-flex-inline flex-grow-1">
                                                        <TimeField
                                                            v-model="editForm.date_to"
                                                            style="max-width: 100px; height: 40px"
                                                            :label="langMap.tracking.report.date_to"
                                                            placeholder="hh:mm"
                                                            format="HH:mm"
                                                        ></TimeField>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-row">
                                                    <div class="d-flex-inline flex-grow-1">
                                                        <TagField
                                                            :key="item.id"
                                                            :color="themeBgColor"
                                                            v-model="editForm.tags"
                                                            width="450"
                                                        ></TagField>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-row">
                                                    <div class="d-flex-inline flex-grow-1">
                                                        <v-checkbox
                                                            :disabled="!isEditable(item)"
                                                            v-model="editForm.billable"
                                                            :label="langMap.tracking.report.billable"
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
                                                    @click="closeEditDialog(item)"
                                                >
                                                    {{ langMap.tracking.report.cancel }}
                                                </v-btn>
                                                <v-btn
                                                    color="success"
                                                    text
                                                    @click="saveChanges(item); closeEditDialog(item)"
                                                >
                                                    {{ langMap.tracking.report.change }}
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                    <v-dialog
                                        v-if="isEditable(item)"
                                        v-model="dialogDelete[item.id]"
                                        persistent
                                        max-width="290"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                v-if="isEditable(item)"
                                                x-small
                                                icon
                                                v-bind="attrs"
                                                v-on="on"
                                            >
                                                <v-icon>mdi-delete</v-icon>
                                            </v-btn>
                                        </template>
                                        <v-card v-if="isEditable(item)">
                                            <v-card-title class="headline">
                                                {{ langMap.tracking.report.are_you_sure }}
                                            </v-card-title>
                                            <v-card-actions>
                                                <v-spacer></v-spacer>
                                                <v-btn
                                                    color="red darken-1"
                                                    text
                                                    @click="dialogDelete[item.id] = false"
                                                >
                                                    {{ langMap.tracking.report.cancel }}
                                                </v-btn>
                                                <v-btn
                                                    color="green darken-1"
                                                    text
                                                    @click="removeTrack(item.id); dialogDelete[item.id] = false"
                                                >
                                                    {{ langMap.tracking.report.confirm }}
                                                </v-btn>
                                            </v-card-actions>
                                        </v-card>
                                    </v-dialog>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </template>
            </v-treeview>

            <v-card-actions
                class="white justify-center"
                v-if="reportData.entities.g1 && reportData.entities.g1.length"
            >
                <v-dialog
                    v-model="dialogExportPDF"
                    persistent
                    max-width="600px"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            :color="themeBgColor"
                            :style="{ color: $helpers.color.invertColor(themeBgColor) }"
                            v-bind="attrs"
                            v-on="on"
                            class="mx-3"
                        >
                            <v-icon>mdi-file-pdf-box-outline</v-icon>
                            {{ langMap.tracking.report.create_pdf }}
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ langMap.tracking.report.create_pdf }}</span>
                        </v-card-title>
                        <v-card-text>
                            <!-- FORM -->
                            <div class="d-flex flex-column">
                                <div class="d-inline-block">
                                    <v-text-field
                                        :label="langMap.tracking.report.name_of_the_report"
                                        v-model="report.pdf.name"
                                    ></v-text-field>
                                </div>
                                <div class="d-inline-block">
                                    <v-text-field
                                        :label="langMap.tracking.report.co_worker"
                                        v-model="report.pdf.coworkers"
                                    ></v-text-field>
                                </div>
                                <div class="d-inline-block">
                                    <v-text-field
                                        :label="langMap.tracking.report.period"
                                        v-model="report.pdf.periodText"
                                    ></v-text-field>
                                </div>
                                <div class="d-inline-block">
                                    <v-select
                                        :items="report.groupItems"
                                        item-text="text"
                                        item-value="value"
                                        :label="langMap.tracking.report.group"
                                        v-model="report.pdf.groupSel"
                                    >
                                    </v-select>
                                </div>
                                <div class="d-inline-block">
                                    <v-combobox
                                        v-model="report.pdf.hideColumns"
                                        :items="report.availableColumns"
                                        return-object
                                        item-value="value"
                                        item-text="text"
                                        :label="langMap.tracking.report.hide_columns"
                                        multiple
                                        outlined
                                        dense
                                        clearable
                                    ></v-combobox>
                                </div>
                                <div class="d-inline-block">
                                    <v-checkbox
                                        class="my-0"
                                        hide-details
                                        v-model="report.pdf.showCover"
                                        :label="langMap.tracking.report.cover_page"
                                    ></v-checkbox>
                                </div>
                                <div class="d-inline-block">
                                    <v-checkbox
                                        :disabled="!$helpers.auth.checkPermissionByIds([69])"
                                        class="my-0"
                                        hide-details
                                        v-model="report.pdf.showRevenue"
                                        :label="langMap.tracking.report.display_revenue"
                                    ></v-checkbox>
                                </div>
                                <div class="d-inline-block">
                                    <v-checkbox
                                        class="my-0"
                                        hide-details
                                        v-model="report.pdf.timeInDecimal"
                                        :label="langMap.tracking.report.display_time_with_decimal"
                                    ></v-checkbox>
                                </div>
                                <div class="d-inline-block">
                                    <v-checkbox
                                        class="my-0"
                                        hide-details
                                        v-model="report.pdf.sendByEmail"
                                        :label="langMap.tracking.report.send_report_by_email"
                                    ></v-checkbox>
                                    <v-text-field
                                        v-if="report.pdf.sendByEmail"
                                        v-model="report.pdf.email"
                                        :label="langMap.tracking.report.recipient_email"
                                    ></v-text-field>
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
                                {{ langMap.tracking.report.create }}
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
                            :style="{ color: $helpers.color.invertColor(themeBgColor) }"
                            v-bind="attrs"
                            v-on="on"
                            class="mx-3"
                        >
                            <v-icon>mdi-file-delimited-outline</v-icon>
                            {{ langMap.tracking.report.csv_export }}
                        </v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="headline">{{ langMap.tracking.report.csv_export }}</span>
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
                                <div class="d-inline-block">
                                    <v-checkbox
                                        class="my-0"
                                        hide-details
                                        v-model="report.csv.timeInDecimal"
                                        :label="langMap.tracking.report.display_time_with_decimal"
                                    ></v-checkbox>
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
                                {{ langMap.tracking.report.export }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<style scoped>
>>>.border-right {
    border-right: thin solid rgba(0,0,0,.12);
}
>>>.width-10 {
    width: 10%;
    max-width: 10%;
    min-width: 10%;
}
>>>.v-expansion-panel {
    border-color: rgba(0, 0, 0, 0.42);
    border-width: 1px;
    border-style: solid;
}
>>>.v-btn.square {
    min-width: 100px !important;
    min-height: 64px !important;
}
>>>.v-btn.square i {
    width: 100%;
    padding-bottom: 5px;
}
>>>.v-btn.square > span {
    flex-direction: column;
}
>>>.dragNDrop {
    border-width: 1px;
    border-style: dashed;
    border-color: white;
}
>>>.dragNDrop button {
    cursor: move;
}
>>>.dragNDrop.active {
    border-width: 1px;
    border-style: dashed;
    border-color: rgba(0,0,0,.12);
}
>>>.dragNDrop:not(.common) > button:not(:last-of-type):after {
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
>>> *:not(.v-icon) {
    font-size: 12px!important;
}
>>> .v-btn__content {
    font-size: 12px!important;
}
</style>

<script>
import EventBus from "../../components/EventBus";
import moment from "moment-timezone";
import draggable from "vuedraggable";
import BarChart from "./components/bar-chart";
import DoughnutChart from "./components/doughnut-chart";
import _ from "lodash";
import TimeField from "./components/time-field";
import TagBtn from "./components/tag-btn";
import TagField from "./components/tag-field";
import ProjectBtn from "./components/project-btn";

export default {
    components: {
        draggable,
        DoughnutChart,
        BarChart,
        TimeField,
        TagBtn,
        TagField,
        ProjectBtn
    },
    data() {
        const self = this;
        return {
            dateFormat: 'YYYY-MM-DD',
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
                period: {
                    start: moment().subtract(1, 'months').format('YYYY-MM-DDTHH:mm:ss'),
                    end: moment().format('YYYY-MM-DDTHH:mm:ss')
                },
                round: 0,
                sort: {
                    icon: 'mdi-sort-alphabetical-ascending',
                    value: 'alph-asc',
                    text: this.$store.state.lang.lang_map.tracking.report.sort_chronologically
                },
                group: [
                    {
                        icon: "mdi-domain",
                        text: this.$store.state.lang.lang_map.tracking.report.services,
                        value: "service"
                    }
                ],
                filters: []
            },
            groupItemsAvailable: [
                {
                    icon: "mdi-calendar-today",
                    text: this.$store.state.lang.lang_map.tracking.report.day,
                    value: "day"
                },
                {
                    icon: "mdi-folder-account-outline",
                    text: this.getTrackingProjectsLabel,
                    value: "project"
                },
                {
                    icon: "mdi-calendar-week",
                    text: this.$store.state.lang.lang_map.tracking.report.week,
                    value: "week"
                },
                {
                    icon: "mdi-currency-usd",
                    text: this.$store.state.lang.lang_map.tracking.report.billability,
                    value: "billability"
                },
                {
                    icon: "mdi-account",
                    text: this.$store.state.lang.lang_map.tracking.report.clients,
                    value: "client"
                },
                {
                    icon: "mdi-calendar-month",
                    text: this.$store.state.lang.lang_map.tracking.report.month,
                    value: "month"
                },
                {
                    icon: "mdi-account-multiple",
                    text: this.$store.state.lang.lang_map.tracking.report.co_worker,
                    value: "coworker"
                },
            ],
            roundingItems: [
                {
                    value: 0,
                    text: this.$store.state.lang.lang_map.tracking.report.rounding_up_of_times
                },
                {
                    value: 1,
                    text: this.$store.state.lang.lang_map.tracking.report.rounding_to_full_min
                },
                {
                    value: 5,
                    text: this.$store.state.lang.lang_map.tracking.report.rounding_to_5_min
                },
                {
                    value: 10,
                    text: this.$store.state.lang.lang_map.tracking.report.rounding_to_10_min
                },
                {
                    value: 15,
                    text: this.$store.state.lang.lang_map.tracking.report.rounding_to_15_min
                },
                {
                    value: 30,
                    text: this.$store.state.lang.lang_map.tracking.report.rounding_to_30_min
                },
                {
                    value: 45,
                    text: this.$store.state.lang.lang_map.tracking.report.rounding_to_45_min
                },
                {
                    value: 60,
                    text: this.$store.state.lang.lang_map.tracking.report.rounding_to_hours
                }
            ],
            sortingItems: [
                {
                    icon: 'mdi-sort-alphabetical-ascending',
                    value: 'alph-asc',
                    text: this.$store.state.lang.lang_map.tracking.report.sort_chronologically
                },
                {
                    icon: 'mdi-sort-alphabetical-descending',
                    value: 'chron-desc',
                    text: this.$store.state.lang.lang_map.tracking.report.sort_chronologically_descending
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
            chart: {
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    percentageInnerCutout : 90,
                    legend: {
                        display: false,
                        position: 'right'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
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
                }
            },
            reportData: {
                headers: [
                    { text: this.$store.state.lang.lang_map.tracking.report.description, value: 'description' },
                    { text: this.$store.state.lang.lang_map.tracking.report.client, value: 'project.client.name' },
                    {
                        text: this.$store.state.lang.lang_map.tracking.report.project,
                        align: 'start',
                        sortable: false,
                        value: 'project.name',
                    },
                    { text: this.$store.state.lang.lang_map.tracking.report.billable, value: 'billable' },
                    { text: this.$store.state.lang.lang_map.tracking.report.date_from, value: 'date_from' },
                    { text: this.$store.state.lang.lang_map.tracking.report.date_to, value: 'date_to' },
                    { text: this.$store.state.lang.lang_map.tracking.report.passed, value: 'passed' },
                    { text: '', value: 'data-table-expand' },
                ],
                entities: {
                    g1: [],
                    g2: [],
                }
            },
            dialogExportPDF: false,
            dialogExportCSV: false,
            dialogPrint: false,
            dialogSave: false,
            dialogEdit: {},
            dialogDelete: {},
            editForm: {
                id: null,
                entity: null,
                service: null,
                date_from: null,
                date_to: null,
                billable: null,
                tags: [],
            },
            report: {
                groupItems: [
                    {
                        value: 1,
                        text: this.$store.state.lang.lang_map.tracking.report.sort_chronologically_not_group,
                        items: []
                    },
                    // {
                    //     value: 2,
                    //     text: this.$store.state.lang.lang_map.tracking.report.group_by_service,
                    //     items: []
                    // }
                ],
                pdf: {
                    name: 'Report',
                    coworkers: null,
                    periodText: '',
                    groupSel: 1,
                    showCover: true,
                    showRevenue: false,
                    timeInDecimal: false,
                    hideColumns: [],
                    sendByEmail: false,
                    email: ''
                },
                csv: {
                    group: {
                        value: 'all_no_group',
                        text: this.$store.state.lang.lang_map.tracking.report.all_entries_sorted_based_grouping,
                        items: []
                    },
                    timeInDecimal: false,
                    timeFormat: 4,
                    dateFormat: 2
                },
                availableColumns: [
                    {
                        value: 'date',
                        text: this.$store.state.lang.lang_map.tracking.report.date
                    },
                    {
                        value: 'start',
                        text: this.$store.state.lang.lang_map.tracking.report.start
                    },
                    {
                        value: 'end',
                        text: this.$store.state.lang.lang_map.tracking.report.end
                    },
                    {
                        value: 'total',
                        text: this.$store.state.lang.lang_map.tracking.report.total
                    },
                    {
                        value: 'client',
                        text: this.$store.state.lang.lang_map.tracking.report.clients
                    },
                    {
                        value: 'coworker',
                        text: this.$store.state.lang.lang_map.tracking.report.co_worker
                    },
                    // {
                    //     value: 'project',
                    //     text: this.$store.state.lang.lang_map.tracking.report.project
                    // },
                    {
                        value: 'service',
                        text: this.$store.state.lang.lang_map.tracking.report.services
                    },
                    {
                        value: 'description',
                        text: this.$store.state.lang.lang_map.tracking.report.description
                    },
                    {
                        value: 'billable',
                        text: this.$store.state.lang.lang_map.tracking.report.billable
                    },
                    // {
                    //     value: 'revenue',
                    //     text: this.$store.state.lang.lang_map.tracking.report.revenue
                    // },
                ],
                name: '',
                selected: null
            },
            csvForm: {
                groupItems: [
                    {
                        value: 'all_no_group',
                        text: this.$store.state.lang.lang_map.tracking.report.all_entries_sorted_based_grouping,
                        items: []
                    },
                    {
                        value: 'all_chron',
                        text: this.$store.state.lang.lang_map.tracking.report.all_entries_chronologically_sorted,
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
            },
            query: null,
            loadingGeneratePreview: false,
        }
    },
    created() {
        moment.updateLocale(this.$store.state.lang.short_code, {
            week: {
                dow: 1,
            },
        });
        moment.tz.setDefault('Etc/UTC');
        this.$store.dispatch('Clients/getClientList', { search: null });
        this.$store.dispatch('Services/getServicesList', { search: null });
        this.$store.dispatch('Projects/getProjectList', { search: null, includeArchives: true });
        this.$store.dispatch('Team/getCoworkers', { search: null });
        this.$store.dispatch('Tags/getTagList', { search: null });
        this.$store.dispatch('Languages/getLanguageList');
        this.debounceGetSettings = _.debounce(this.__getSettings, 1000);
        this.debounceGetReports = _.debounce(this.__getReports, 1000);
        this.debounceGenPreview = _.debounce(this.genPreview, 2000);
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
        this.debounceGetReports();
    },
    methods: {
        __getSettings() {
            this.$store.dispatch('Tracking/getSettings')
                .then(successResult => {
                   if (successResult) {
                       const settings = this.$store.getters['Tracking/getSettings'];
                       if (settings && settings.email) {
                           this.report.pdf.email = settings.email.email;
                       }
                   }
                });
        },
        __getReports() {
            this.$store.dispatch('Tracking/getReports');
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
            this.builder.period.start = start ? moment(start).format(this.dateFormat) : start;
            this.builder.period.end = moment(end).format(this.dateFormat);
            const calendar = this.$refs.calendar;
            if (calendar) {
                await calendar.updateValue({
                    start: this.builder.period.start ? moment(this.builder.period.start).format(this.dateFormat) : moment('2020-01-01').format(this.dateFormat),
                    end: moment(this.builder.period.end).format(this.dateFormat)
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
            this.debounceGenPreview();
            this.resetSelectedReport();
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
                let queryParam = this.createQuery(this.builder.filters);
                if (filter.dispatch) {
                    Object.keys(filter.dispatchParams).map(f => {
                        queryParam = { ...queryParam, [f]: filter.dispatchParams[f] };
                    });
                    this.$store.dispatch(filter.dispatch, queryParam);
                }
                this.builder.filters.push(filter);
            } else {
                this.builder.filters.splice(index, 1);
                if (filter.value === 'coworkers') {
                    this.report.pdf.coworkers = '';
                }
            }
        },
        genPreview() {
            const dateFormat = 'dddd DD/MM/YYYY';
            if (this.builder.period.start) {
                if (this.builder.period.start === this.builder.period.end) {
                    this.report.pdf.periodText = `${moment(this.builder.period.start).format(dateFormat)}`;
                } else {
                    this.report.pdf.periodText = `${moment(this.builder.period.start).format(dateFormat)} - ${moment(this.builder.period.end).format(dateFormat)}`;
                }
            } else {
                this.report.pdf.periodText = `... - ${moment(this.builder.period.end).format(dateFormat)}`;
            }
            const query = this.builder;
            let queryStr = JSON.stringify(query);
            this.loadingGeneratePreview = true;
            axios.post('/api/ttmanaging/reports/generate', query)
                .then(({ data: { data } }) => {
                    this.loadingGeneratePreview = false;
                    if (queryStr === JSON.stringify(this.builder)) {
                        this.reportData.entities = data;
                        this.dialogEdit = {};
                        const self = this;
                        data.g1.map(i => function () {
                            self.dialogEdit[i.id] = false;
                            self.dialogDelete[i.id] = false;
                        });
                        this.report.pdf.coworkers = [...new Set(this.calculateCoworkers(data.g1))].sort().join(', ');
                    }
                })
                .catch(err => {
                    this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                });
        },
        calculateTime(entries, seconds = 0) {
            if (!entries) return seconds;
            entries.map(i => {
                if (i.children) {
                    seconds += this.calculateTime(i.children);
                } else {
                    seconds += i.passed;
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
        calculateCoworkers(entries, coworkers = []) {
            if (!entries) return coworkers;
            entries.map(i => {
                if (i.children) {
                    coworkers = coworkers.concat(this.calculateCoworkers(i.children));
                } else {
                    coworkers.push(i.user.full_name);
                }
            });
            return coworkers;
        },
        createFile(format) {
            if (format === 'csv' && this.report.csv.group && this.report.csv.group.value === 'all_chron') {
                this.builder.sort = {
                    icon: 'mdi-sort-alphabetical-ascending',
                    value: 'alph-asc',
                    text: this.$store.state.lang.lang_map.sort_chronologically
                };
            }
            axios.post(`/api/ttmanaging/reports?format=${format}`, { ...this.builder, ...this.report[format] }, {
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
                    this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                })
        },
        saveChanges() {
            this.$store.dispatch('Tracking/updateTrack', {
                id: this.editForm.id,
                date_from: this.editForm.date_from,
                date_to: this.editForm.date_to,
                billable: this.editForm.billable,
                tags: this.editForm.tags,
                service: this.editForm.service,
                entity: this.editForm.entity,
                entity_id: this.editForm.entity ? this.editForm.entity.id : null,
                entity_type: this.editForm.entity && this.editForm.entity.from ? "App\\Ticket" : "App\\TrackingProject",
            })
                .then(successResult => {
                    if (successResult) {
                        this.debounceGenPreview();
                    }
                });
        },
        removeTrack(id) {
            this.$store.dispatch('Tracking/deleteTrack', {id})
                .then(successResult => {
                    if (successResult) {
                        this.genPreview();
                    }
                })
        },
        stopTrack(id) {
            this.$store.dispatch('Tracking/updateTrack', {
                id: id,
                date_to: moment().format(),
                status: 'stopped'
            })
                .then(successResult => {
                    if (successResult) {
                        this.genPreview();
                    }
                });
        },
        closeEditDialog(item) {
            this.editForm.id = null;
            this.editForm.date_from = null;
            this.editForm.date_to = null;
            this.editForm.billable = null;
            this.editForm.tags = [];
            this.editForm.entity = null;
            this.editForm.service = null;
            this.dialogEdit[item.id] = false;
        },
        openEditDialog(item) {
            this.editForm.id = item.id;
            this.editForm.date_from = item.date_from;
            this.editForm.date_to = item.date_to
                ? moment(item.date_from)
                    .hours(moment(item.date_to).hours())
                    .minutes(moment(item.date_to).minutes())
                    .seconds(moment(item.date_to).seconds())
                : item.date_to;
            this.editForm.billable = item.billable;
            this.editForm.tags = item.tags;
            this.editForm.entity = item.entity;
            this.editForm.service = item.service;
            this.dialogEdit[item.id] = true;
        },
        saveReport() {
            if (this.builder.reportName) {
                this.$store.dispatch('Tracking/createReport', {
                    name: this.builder.reportName,
                    configuration: this.builder
                });
            }
        },
        deleteReport(id) {
            this.$store.dispatch('Tracking/deleteReport', { id });
        },
        selectReport(id) {
            const report = this.$store.getters['Tracking/getReports'].find(i => i.id === id);
            if (report) {
                this.dialogSave = false;
                this.groupItems = this.groupItems.concat(this.builder.group);
                this.builder = report.configuration;
                const groups = this.builder.group.map(g => g.value);
                this.groupItems = this.groupItems.filter(i => groups.indexOf(i.value) === -1);
                this.report.selected = report;
            }
        },
        resetSelectedReport() {
            this.report.selected = null;
            this.debounceGetReports();
        },
        hasPermission(ids) {
            return this.$helpers.auth.checkPermissionByIds(ids);
        },
        isEditable(tracker) {
            if (!this.hasPermission([62, 63, 64])) {
                return false;
            }
            const trackerDiff = moment().diff(tracker.date_from, 'seconds');
            if (
                (this.hasPermission([64]) &&  trackerDiff > 60 * 60 * 24 * 14)
                || (this.hasPermission([63]) &&  trackerDiff > 60 * 60 * 24 * 7)
            ) {
                return false;
            }
            return true;
        },
        substr(str, maxLength) {
            if (str && str.length >= maxLength - 3) {
                return str.substring(0, maxLength - 3) + '...';
            }
            return str;
        },
        normalizeData(entries, items = []) {
            if (!entries) return seconds;
            entries.map(i => {
                if (i.children) {
                    items = items.concat(this.normalizeData(i.children));
                } else {
                    items.push(i);
                }
            });
            return items;
        },
        createQuery(filters) {
            let query = { force: true };
            for (const i in filters) {
                query = { ...query, [filters[i].value]: filters[i].selected };
            }
            return query;
        },
        clearFiltersAfter(filter) {
            const index = this.builder.filters.findIndex(i => i.value === filter.value);
            if (index >= 0) {
                this.builder.filters.splice(index+1, this.builder.filters.length);
            }
        }
    },
    computed: {
        totalTime: function() {
            return this.calculateTime(this.reportData.entities.g1);
        },
        totalRevenue: function() {
            return this.calculateRevenue(this.reportData.entities.g1);
        },
        doughnutData: function() {
            if (this.reportData.entities && this.reportData.entities.g1 && this.reportData.entities.g1.length) {
                let data = {
                    labels: [],
                    datasets: []
                };
                data.datasets = [];
                let values = [];
                let labels = [];
                this.reportData.entities.g1.map(i => {
                    let client = '';
                    if (i.client) {
                        client = this.substr(i.client, 200) + ": \n";
                    }
                    if (i.name) {
                        labels.push(client + this.substr(i.name, 200) ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                        data.labels.push(client + this.substr(i.name, 200) ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                    } else if(i.entity && i.entity.name) {
                        labels.push(client + this.substr(i.entity.name, 200) ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                        data.labels.push(client + this.substr(i.entity.name, 200) ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                    }
                    if (i.children) {
                        values.push((this.calculateTime(i.children) / 60 / 60).toFixed(2));
                    } else {
                        values.push((i.passed / 60 / 60).toFixed(2));
                    }
                });
                let colors = [];
                for (let i = 0; i <= values.length - 1; i++) {
                    colors.push(this.$helpers.color.genRandomColor());
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
            if (this.reportData.entities.g2 && this.reportData.entities.g2.length) {
                let data = {
                    labels: [],
                    datasets: []
                };
                data.datasets = [];
                let values = [];
                let labels = [];
                let colors = [];
                this.reportData.entities.g2.map(i => {
                    let client = '';
                    if (i.client) {
                        client = this.substr(i.client, 17) + ": \n";
                    }
                    if (i.name) {
                        data.labels.push(client + this.substr(i.name, 15) ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                        labels.push(client + this.substr(i.name, 15) ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                    } else if(i.entity && i.entity.name) {
                        data.labels.push(client + this.substr(i.entity.name, 15) ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                        labels.push(client + this.substr(i.entity.name, 15) ?? moment(i.date_from).format('ddd DD MMM YYYY'));
                    }
                    if (i.children) {
                        values.push(
                            (this.calculateTime(i.children) / 60 / 60).toFixed(2)
                        );
                    } else {
                        values.push(
                            (i.passed / 60 / 60).toFixed(2)
                        );
                    }
                });
                const c = this.$helpers.color.genRandomColor();
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
        },
        availableRoundingItems: function () {
            const { settings } = this.$store.getters['Tracking/getSettings'];
            let roundingItems = this.roundingItems;
            if (settings && settings.customRounding) {
                roundingItems = roundingItems.concat(settings.customRounding.map(i => ({
                    value: i.key,
                    text: i.name,
                })))
            }
            return roundingItems;
        },
        availableFilters () {
            return [
                {
                    value: 'coworkers',
                    text: this.$store.state.lang.lang_map.tracking.report.co_worker,
                    store: 'Team/getCoworkers',
                    dispatch: 'Team/getCoworkers',
                    dispatchParams: {},
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'projects',
                    text: this.getTrackingProjectsLabel,
                    store: 'Projects/getProjects',
                    dispatch: 'Projects/getProjectList',
                    dispatchParams: { search: null, includeArchives: true },
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'clients',
                    text: this.$store.state.lang.lang_map.tracking.report.clients,
                    store: 'Clients/getClients',
                    dispatch: 'Clients/getClientList',
                    dispatchParams: {},
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'services',
                    text: this.$store.state.lang.lang_map.tracking.report.services,
                    store: 'Services/getServices',
                    dispatch: 'Services/getServicesList',
                    dispatchParams: {},
                    items: [],
                    selected: [],
                    multiply: true
                },
                {
                    value: 'billable',
                    text: this.$store.state.lang.lang_map.tracking.report.billable,
                    store: null,
                    dispatch: '',
                    dispatchParams: {},
                    items: [
                        {
                            id: 1,
                            name: this.$store.state.lang.lang_map.tracking.report.billable
                        },
                        {
                            id: 0,
                            name: this.$store.state.lang.lang_map.tracking.report.non_billable
                        }
                    ],
                    selected: null,
                    multiply: false
                },
                {
                    value: 'tag',
                    text: this.$store.state.lang.lang_map.tracking.report.tags,
                    store: 'Tags/getTags',
                    dispatch: 'Tags/getTagList',
                    dispatchParams: {},
                    items: [],
                    selected: [],
                    multiply: true
                }
            ];
        },
        groupItems: {
            get: function() {
                const foundItem = this.groupItemsAvailable.find(i => i.value === 'project');
                if (foundItem) foundItem.text = this.getTrackingProjectsLabel;
                return this.groupItemsAvailable;
            },
            set: function(value) {
                this.groupItemsAvailable = value;
            },
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
    },
    watch: {
        'builder.round': function() {
            this.debounceGenPreview();
        },
        'builder.filters': function() {
            this.debounceGenPreview();
        },
        'builder.group': function() {
            this.debounceGenPreview();
        },
        'builder': function () {
            const dateFormat = 'dddd DD/MM/YYYY';
            if (this.builder.period.start) {
                if (this.builder.period.start === this.builder.period.end) {
                    this.report.pdf.periodText = `${moment(this.builder.period.start).format(dateFormat)}`;
                } else {
                    this.report.pdf.periodText = `${moment(this.builder.period.start).format(dateFormat)} - ${moment(this.builder.period.end).format(dateFormat)}`;
                }
            } else {
                this.report.pdf.periodText = `... - ${moment(this.builder.period.end).format(dateFormat)}`;
            }
        },
        activePeriod: function () {
            const calendar = this.$refs.calendar;
            if (calendar) {
                if (moment(this.builder.period.start).format('YYYY-MM') !== moment(this.builder.period.end).format('YYYY-MM')) {
                    calendar.$refs.calendar.showPageRange({
                        from: moment(this.builder.period.start).format(this.dateFormat),
                        to: moment(this.builder.period.end).format(this.dateFormat)
                    });
                } else {
                    calendar.$refs.calendar.showPageRange({
                        from: moment(this.builder.period.start).subtract(1, 'months').format(this.dateFormat),
                        to: moment(this.builder.period.end).format(this.dateFormat)
                    });
                }
            }
        }
    }
}
</script>
