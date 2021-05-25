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

<!--        <v-overlay :value="loading">-->
<!--            <v-progress-circular-->
<!--                indeterminate-->
<!--                size="64"-->
<!--            ></v-progress-circular>-->
<!--        </v-overlay>-->

<!--        panel-->
        <template>
            <v-card flat>
                <v-toolbar>
                    <!-- Timer mode-->
                    <div class="d-flex align-start flex-wrap flex-row" style="width: 100%" v-if="mode">
                        <div class="mx-2 align-self-center">
                            <v-select
                                :items="$store.getters['Services/getServices']"
                                :placeholder="langMap.tracking.tracker.service_type"
                                hide-details
                                item-text="name"
                                item-value="id"
                                v-model="timerPanel.service"
                                return-object
                                style="max-width: 150px;"
                            ></v-select>
                        </div>
                        <div class="flex-grow-1 align-self-center">
                            <v-text-field
                                outlined
                                dense
                                hide-details="auto"
                                :placeholder="langMap.tracking.tracker.timer_panel_description"
                                v-model="timerPanel.description"
                                style="max-width: 1000px"
                            ></v-text-field>
                        </div>
                        <div class="mx-3 align-self-center">
                            <ProjectBtn
                                key="timerPanelProjectKey"
                                :color="themeBgColor"
                                :onChoosable="handlerProjectTimerPanel"
                                v-model="timerPanel.entity"
                            ></ProjectBtn>
                        </div>
                        <div class="mx-2 align-self-center">
                            <TagBtn
                                key="timerPanelTagKey"
                                :color="themeBgColor"
                                :onChoosable="handlerTagsTimerPanel"
                                v-model="timerPanel.tags"
                            >
                            </TagBtn>
                        </div>
                        <div class="mx-2 align-self-center">
                            <v-btn
                                v-if="$helpers.auth.checkPermissionByIds([46])"
                                fab
                                :icon="!timerPanel.billable"
                                x-small
                                :color="themeBgColor"
                                @click="timerPanel.billable = !timerPanel.billable"
                            >
                                <v-icon center v-bind:class="{ 'white--text': timerPanel.billable }">
                                    mdi-currency-usd
                                </v-icon>
                            </v-btn>
                        </div>
                        <div class="mx-3 align-self-center" style="max-width: 100px">
                            <v-text-field
                                v-model="timerPanel.passedSeconds"
                                placeholder="00:00:00"
                                dense
                                hide-details="auto"
                                readonly
                            ></v-text-field>
                        </div>
                        <div class="mx-3 align-self-center">
                            <v-btn
                                tile
                                small
                                :color="!timerPanel.start ? themeBgColor : 'error'"
                                style="color: white"
                                @click="actionStartNewTrack()"
                            >
                                <span v-if="!timerPanel.start">{{ langMap.tracking.tracker.start }}</span>
                                <span v-if="timerPanel.start">{{ langMap.tracking.tracker.stop }}</span>
                            </v-btn>
                        </div>
                        <div class="mx-1 d-flex flex-column">
                            <v-tooltip top>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        v-if="!$helpers.auth.checkPermissionByIds([47])"
                                        fab
                                        x-small
                                        :icon="!mode"
                                        :color="themeBgColor"
                                        @click="mode=true"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon v-bind:class="{ 'white--text': mode }">mdi-clock</v-icon>
                                    </v-btn>
                                </template>
                                <span v-text="langMap.tracking.tracker.timer"></span>
                            </v-tooltip>
                            <v-tooltip top>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        v-if="!$helpers.auth.checkPermissionByIds([47])"
                                        fab
                                        x-small
                                        :icon="mode"
                                        :color="themeBgColor"
                                        @click="mode=false"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon v-bind:class="{ 'white--text': !mode }">mdi-format-list-bulleted-square</v-icon>
                                    </v-btn>
                                </template>
                                <span v-text="langMap.tracking.tracker.manual"></span>
                            </v-tooltip>
                        </div>
                    </div>
                    <!-- Manual mode-->
                    <div class="d-flex align-start flex-wrap flex-row" style="width: 100%" v-if="!$helpers.auth.checkPermissionByIds([47]) && !mode">
                        <div class="mx-2 align-self-center">
                            <v-select
                                :items="$store.getters['Services/getServices']"
                                :placeholder="langMap.tracking.tracker.service_type"
                                hide-details
                                item-text="name"
                                item-value="id"
                                v-model="manualPanel.service"
                                return-object
                                style="max-width: 150px;"
                            ></v-select>
                        </div>
                        <div class="flex-grow-1 align-self-center">
                            <v-text-field
                                outlined
                                dense
                                hide-details="auto"
                                :placeholder="langMap.tracking.tracker.timer_panel_description"
                                v-model="manualPanel.description"
                                style="max-width: 1000px"
                            ></v-text-field>
                        </div>
                        <div class="mx-1 align-self-center">
                            <ProjectBtn
                                key="manualPanelProjectKey"
                                :color="themeBgColor"
                                :onChoosable="handlerProjectManualPanel"
                                v-model="manualPanel.entity"
                            ></ProjectBtn>
                        </div>
                        <div class="mx-1 align-self-center">
                            <TagBtn
                                key="manualPanelTagKey"
                                :color="themeBgColor"
                                :onChoosable="handlerTagsManualPanel"
                                v-model="manualPanel.tags"
                            >
                            </TagBtn>
                        </div>
                        <div class="mx-1 align-self-center">
                            <v-btn
                                v-if="$helpers.auth.checkPermissionByIds([46])"
                                :icon="!manualPanel.billable"
                                x-small
                                :color="themeBgColor"
                                fab
                                @click="manualPanel.billable = !manualPanel.billable"
                            >
                                <v-icon center v-bind:class="{ 'white--text': manualPanel.billable }">
                                    mdi-currency-usd
                                </v-icon>
                            </v-btn>
                        </div>
                        <div class="mx-3 align-self-center">
                            <TimeField
                                v-model="manualPanel.date_from"
                                style="max-width: 100px"
                                :label="langMap.tracking.tracker.from"
                                placeholder="hh:mm"
                                format="HH:mm"
                            ></TimeField>
                        </div>
                        <div class="mx-3 align-self-center">
                            <TimeField
                                v-model="manualPanel.date_to"
                                style="max-width: 100px"
                                :label="langMap.tracking.tracker.to"
                                placeholder="hh:mm"
                                format="HH:mm"
                                :min="manualPanel.date_from"
                            ></TimeField>
                        </div>
                        <div class="mx-1 align-self-center">
                            <v-menu
                                v-model="createDatePicker"
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
                                        v-model="manualFormattedDate"
                                        :label="langMap.tracking.tracker.date"
                                        placeholder="yyyy-mm-dd"
                                        prepend-icon="mdi-calendar"
                                        v-bind="attrs"
                                        v-on="on"
                                        hide-details="auto"
                                        class="date-picker__without-line mt-1"
                                        style="min-width: 100px; max-width: 130px"
                                        readonly
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    first-day-of-week="1"
                                    dense
                                    v-model="manualFormattedDate"
                                    @input="createDatePicker = false"
                                ></v-date-picker>
                            </v-menu>
                        </div>
                        <div class="mx-1 align-self-center">
                            <v-text-field
                                v-model="timeAdd"
                                placeholder="00:00:00"
                                dense
                                readonly
                                hide-details="auto"
                                style="max-width: 80px; text-align: center"
                            ></v-text-field>
                        </div>
                        <div class="mx-3 align-self-center">
                            <v-btn
                                small
                                tile
                                :color="themeBgColor"
                                :loading="loadingCreateTrack"
                                :disabled="loadingCreateTrack"
                                style="color: white"
                                @click="actionCreateTrack()"
                            >
                                {{langMap.tracking.tracker.add}}
                            </v-btn>
                        </div>
                        <div class="mx-1 d-flex flex-column">
                            <v-tooltip top>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        fab
                                        x-small
                                        :icon="!mode"
                                        :color="themeBgColor"
                                        @click="mode=true"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon v-bind:class="{ 'white--text': mode }">mdi-clock</v-icon>
                                    </v-btn>
                                </template>
                                <span v-text="langMap.tracking.tracker.timer"></span>
                            </v-tooltip>
                            <v-tooltip top>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        fab
                                        x-small
                                        :icon="mode"
                                        :color="themeBgColor"
                                        @click="mode=false"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        <v-icon v-bind:class="{ 'white--text': !mode }">mdi-format-list-bulleted-square</v-icon>
                                    </v-btn>
                                </template>
                                <span v-text="langMap.tracking.tracker.manual"></span>
                            </v-tooltip>
                        </div>
                    </div>
                </v-toolbar>
            </v-card>
        </template>

<!--        Team selector   -->
<!--        dateRangePicker -->
        <template>
            <div class="d-flex flex-row mr-16">
                <div class="d-inline-flex flex-grow-0">
                    <v-select
                        v-if="hasPermission([42, 90])"
                        placeholder="Choose team members"
                        :items="teamEmployee"
                        item-value="id"
                        item-text="name"
                        v-model="teamFilter"
                        hide-details
                        multiple
                        clearable
                        single-line
                        style="min-width: 300px"
                    >
                        <template v-slot:item="{ parent, item, on, attrs }">
                            <div class="d-flex">
                                <div class="d-inline-flex">
                                    <Avatar :user="item" :color="getUserColor(item.id)"></Avatar>
                                </div>
                                <div class="d-inline-flex mt-3 ma-4">
                                    {{ item.full_name }}
                                </div>
                            </div>
                        </template>
                    </v-select>
                </div>
                <div class="d-inline-flex flex-grow-1" style="width: 100%;">&nbsp;</div>
                <div class="d-inline-flex flex-grow-0">
                    <v-menu
                        ref="dateRangePicker"
                        v-model="dateRangePicker"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                        class="float-right"
                        nudge-left="200px"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="dateRangeText"
                                v-bind="attrs"
                                v-on="on"
                                hide-details="auto"
                                rounded
                                readonly
                                prepend-inner-icon="mdi-calendar"
                                :style="{
                                    'border-style': 'solid',
                                    'border-color': themeBgColor,
                                    'border-width': '2px',
                                    'min-width': '280px',
                                    'max-width': '300px',
                                    'max-height': '36px',
                                }"
                                class="py-0 mt-5 mb-n3 dateRangePicker"
                            ></v-text-field>
                        </template>
                        <v-card>
                            <v-row>
                                <v-col cols="4" offset="1" class="pt-6 mb-n3">
                                    <v-text-field
                                        readonly
                                        :label="langMap.tracking.tracker.period_from"
                                        v-model="periodStart"
                                        prepend-icon="mdi-calendar"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="4" offset="2" class="pt-6 mb-n3">
                                    <v-text-field
                                        readonly
                                        :label="langMap.tracking.tracker.period_to"
                                        v-model="periodEnd"
                                        prepend-icon="mdi-calendar"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                            <vc-date-picker
                                v-model="dateRange"
                                is-range
                                :step="1"
                                :columns="2"
                                mode="date"
                                @input="handlerDateRange"
                            ></vc-date-picker>
                        </v-card>
                    </v-menu>
                </div>
            </div>
        </template>

        <br>

<!--        dataTable-->
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
                        :color="themeBgColor"
                        style="color: white"
                    >
                        <span v-if="moment(panelDate).format() === moment().format()">{{langMap.tracking.tracker.today}}</span>
                        <span v-else>{{ moment(panelDate).format('ddd, DD MMM YYYY')}}</span>
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <template>
                            <div>
                                <v-data-table
                                    :headers="headers"
                                    :items="filterTracking(panelDate)"
                                    :items-per-page="15"
                                    class="elevation-1"
                                    style="width: 100%"
                                    calculate-widths
                                    hide-default-header
                                >
                                    <template v-slot:body="props">
                                        <tr v-for="row in props.items">
                                            <td class="pa-3" v-if="hasPermission([42])">
                                                <Avatar :user="row.user" :color="getUserColor(row.user.id)"></Avatar>
                                            </td>
                                            <td
                                                class="pa-3"
                                                :width="headers.find(i => i.value === 'description').width"
                                            >
                                                <v-edit-dialog
                                                    v-if="isEditable(row)"
                                                    @save="debounceSave(row, 'description')"
                                                    @cancel="cancel"
                                                    @open="open"
                                                    @close="debounceSave(row, 'description')"
                                                    :ref="`dialog${row.id}`"
                                                >
                                                    <span v-if="row.service">
                                                        {{ row.service.name }}
                                                        <v-icon x-small>mdi-checkbox-blank-circle</v-icon>
                                                    </span>
                                                    <span class="text--secondary" v-if="!row.description">
                                                        {{ langMap.tracking.tracker.add_description }}
                                                    </span>
                                                    <span v-else>
                                                        {{ $helpers.string.shortenText(row.description, 100) }}
                                                    </span>
                                                    <template v-slot:input>
                                                        <v-textarea
                                                            v-model="row.description"
                                                            :label="langMap.tracking.tracker.description"
                                                            :placeholder="langMap.tracking.tracker.description"
                                                            single-line
                                                            counter
                                                            rows="2"
                                                        ></v-textarea>
                                                        <v-select
                                                            :items="$store.getters['Services/getServices']"
                                                            :label="langMap.tracking.tracker.service_type"
                                                            :placeholder="langMap.tracking.tracker.service_type"
                                                            item-text="name"
                                                            item-value="id"
                                                            v-model="row.service"
                                                            return-object
                                                            dense
                                                            @input="$refs[`dialog${row.id}`][0].isActive = false"
                                                        ></v-select>
                                                        <v-btn
                                                            class="float-right mb-2"
                                                            text
                                                            color="success"
                                                            @click="$refs[`dialog${row.id}`][0].isActive = false"
                                                        >{{ langMap.tracking.tracker.save }}
                                                        </v-btn>
                                                    </template>
                                                </v-edit-dialog>
                                                <div v-else>
                                                    <span v-if="row.service">
                                                        {{ row.service.name }}
                                                        <v-icon x-small>mdi-checkbox-blank-circle</v-icon>
                                                    </span>
                                                    <span class="text--secondary" v-if="!row.description">
                                                        {{ langMap.tracking.tracker.add_description }}
                                                    </span>
                                                    <span v-else>
                                                        {{ row.description }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td
                                                class="pa-3"
                                                :width="headers.find(i => i.value === 'entity').width"
                                            >
                                                <span v-if="row.entity && row.entity.client">
                                                    {{ row.entity.client.name }}
                                                </span>
                                                <span v-else-if="row.entity">{{ row.entity.from_company_name }}</span>
                                            </td>
                                            <td
                                                class="pa-3"
                                                align="center"
                                                :width="headers.find(i => i.value === 'entity.name').width"
                                            >
                                                <v-edit-dialog
                                                    v-if="isEditable(row)"
                                                    @save="debounceSave(row, 'entity', row.entity)"
                                                    @cancel="cancel"
                                                    @open="open"
                                                    @close="debounceSave(row, 'entity', row.entity)"
                                                >
                                                    <ProjectBtn
                                                        :key="row.id"
                                                        :color="row.entity && row.entity.color ? row.entity.color : themeBgColor"
                                                        v-model="row.entity"
                                                        @blur="debounceSave(row, 'entity', row.entity)"
                                                        @input="debounceSave(row, 'entity', row.entity)"
                                                    ></ProjectBtn>
                                                </v-edit-dialog>
                                                <div v-else>
                                                    <div
                                                        v-if="row.entity"
                                                        :style="{color: row.entity && row.entity.color ? row.entity.color : themeBgColor}"
                                                    >
                                                        {{$helpers.string.shortenText(row.entity.name, 35)}}
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="pa-3"
                                                :width="headers.find(i => i.value === 'tags').width"
                                            >
                                                <TagField
                                                    :disabled="!isEditable(row)"
                                                    :key="row.id"
                                                    :color="themeBgColor"
                                                    v-model="row.tags"
                                                    @blur="debounceSave(row, 'tags', row.tags)"
                                                ></TagField>
                                            </td>
                                            <td
                                                class="pa-3"
                                                :width="headers.find(i => i.value === 'billable').width"
                                            >
                                                <v-btn
                                                    v-if="isEditable(row)"
                                                    :disabled="!$helpers.auth.checkPermissionByIds([46])"
                                                    fab
                                                    :icon="!row.billable"
                                                    x-small
                                                    :color="themeBgColor"
                                                    @click="row.billable = !row.billable; debounceSave(row, 'billable')"
                                                >
                                                    <v-icon center v-bind:class="{ 'white--text': row.billable }">
                                                        mdi-currency-usd
                                                    </v-icon>
                                                </v-btn>
                                            </td>
                                            <td
                                                class="pa-3"
                                                :width="headers.find(i => i.value === 'date_from').width"
                                            >
                                                <div class="d-flex flex-row">
                                                    <div class="d-flex-inline">
                                                        <v-edit-dialog
                                                            v-if="isEditable(row)"
                                                            @save=""
                                                            @cancel="cancel"
                                                            @open="open"
                                                            @close=""
                                                        >
                                                            {{ moment(row.date_from).format(timeFormat) }}&nbsp;&mdash;&nbsp;{{moment(row.date_to).format(timeFormat)}}
                                                            <template v-slot:input>
                                                                <div class="d-flex flex-row">
                                                                    <div class="d-inline-flex">
                                                                        <TimeField
                                                                            v-model="row.date_from"
                                                                            style="max-width: 100px; height: 40px"
                                                                            placeholder="hh:mm"
                                                                            format="HH:mm"
                                                                            @input="correctionTime(row.date_from, row.date_to); debounceSave(row, 'date_from', row.date_from)"
                                                                        ></TimeField>
                                                                    </div>
                                                                    <div class="d-inline-flex mt-2">&nbsp;&mdash;&nbsp;</div>
                                                                    <div class="d-inline-flex">
                                                                        <TimeField
                                                                            v-model="row.date_to"
                                                                            style="max-width: 100px; height: 40px"
                                                                            placeholder="hh:mm"
                                                                            format="HH:mm"
                                                                            :min="row.date_from"
                                                                            @input="correctionTime(row.date_from, row.date_to); debounceSave(row, 'date_to', row.date_to)"
                                                                        ></TimeField>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </v-edit-dialog>
                                                        <span v-else>
                                                            {{moment(row.date_from).format('HH:mm')}}&nbsp;&mdash;&nbsp;{{moment(row.date_to).format('HH:mm')}}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td
                                                class="pa-3"
                                                :width="headers.find(i => i.value === 'passed').width"
                                            >
                                                <span v-text="$helpers.time.convertSecToTime(row.passed)"></span>
                                            </td>
                                            <td
                                                class="pa-3"
                                                :width="headers.find(i => i.value === 'date').width"
                                            >
                                                <v-menu
                                                    :disabled="!isEditable(row)"
                                                    v-model="row.date_picker"
                                                    :close-on-content-click="false"
                                                    :nudge-right="40"
                                                    transition="scale-transition"
                                                    offset-y
                                                    min-width="auto"
                                                    left
                                                >
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-btn
                                                            :disabled="!isEditable(row)"
                                                            icon
                                                            v-bind="attrs"
                                                            v-on="on"
                                                            :color="themeBgColor"
                                                        >
                                                            <v-icon>mdi-calendar</v-icon>
                                                        </v-btn>
                                                    </template>
                                                    <v-date-picker
                                                        first-day-of-week="1"
                                                        :disabled="!isEditable(row)"
                                                        dense
                                                        v-model="row.date"
                                                        @input="row.date_picker = false; handlerChangeDate(row)"
                                                    ></v-date-picker>
                                                </v-menu>
                                            </td>
                                            <td
                                                class="pa-3"
                                                :width="headers.find(i => i.value === 'actions').width"
                                            >
                                                <div class="d-flex flex-row">
                                                    <div>
                                                        <v-btn
                                                            depressed
                                                            color="error"
                                                            v-if="row.status == 'started'"
                                                            @click="actionStopTracking(row.id)"
                                                        >
                                                            <v-icon class="white--text">mdi-pause</v-icon>
                                                        </v-btn>
                                                        <v-btn
                                                            depressed
                                                            :color="themeBgColor"
                                                            v-if="row.status == 'stopped'"
                                                            @click="actionStartTrackingAsId(row.id)"
                                                        >
                                                            <v-icon class="white--text">mdi-play-outline</v-icon>
                                                        </v-btn>
                                                    </div>
                                                    <div>
                                                        <v-menu
                                                            bottom
                                                            left
                                                        >
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-btn
                                                                    :color="themeBgColor"
                                                                    icon
                                                                    v-bind="attrs"
                                                                    v-on="on"
                                                                >
                                                                    <v-icon>mdi-dots-vertical</v-icon>
                                                                </v-btn>
                                                            </template>
                                                            <v-card
                                                                class="mx-auto"
                                                                max-width="300"
                                                                tile
                                                            >
                                                                <v-list dense>
                                                                    <v-list-item-group
                                                                        color="primary"
                                                                    >
                                                                        <v-list-item>
                                                                            <v-list-item-content>
                                                                                <v-list-item-title
                                                                                    @click="actionDuplicateTracking(row.id)"
                                                                                >
                                                                                    {{ langMap.tracking.tracker.duplicate }}
                                                                                </v-list-item-title>
                                                                            </v-list-item-content>
                                                                        </v-list-item>
                                                                        <v-list-item v-if="isEditable(row)">
                                                                            <v-list-item-content>
                                                                                <v-list-item-title
                                                                                    @click="actionDeleteTracking(row.id)"
                                                                                    style="color: red"
                                                                                >
                                                                                    {{langMap.tracking.tracker.delete}}
                                                                                </v-list-item-title>
                                                                            </v-list-item-content>
                                                                        </v-list-item>
                                                                    </v-list-item-group>
                                                                </v-list>
                                                            </v-card>
                                                        </v-menu>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
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

<style>
.date-picker__without-line.v-text-field>.v-input__control>.v-input__slot:before,
.date-picker__without-line.v-text-field:not(.v-input__has-state):hover>.v-input__control>.v-input__slot:before,
.date-picker__without-line.v-text-field>.v-input__control>.v-input__slot:after{
    border: none !important;
}
.dateRangePicker input {
    text-align: center;
}
.v-data-table-header {
    /*display: none;*/
}
.v-expansion-panel-header {
    min-height: 40px !important;
    padding: 14px 24px !important;
}
.v-data-table__wrapper tr td.text-start:nth-child(6):after {
    /*content: "—";*/
}
</style>

<script>
import EventBus from "../../components/EventBus";
import moment from "moment-timezone";
import _ from "lodash";
import ProjectBtn from "./components/project-btn";
import TagBtn from "./components/tag-btn";
import TagField from "./components/tag-field";
import TimeField from "./components/time-field";
import Avatar from "../../components/Avatar";

export default {
    components: {
        ProjectBtn,
        TagBtn,
        TagField,
        TimeField,
        Avatar
    },
    data() {
        return {
            loading: false,
            attemptRepeat: 0,
            tz: {
                name: moment.tz.guess(),
                offset: new Date().getTimezoneOffset()
            },
            dateFormat: 'YYYY-MM-DD',
            timeFormat: 'HH:mm',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            /* Snackbar */
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            /* Toolbar */
            mode: true,
            loadingCreateTrack: false,
            loadingUpdateTrack: false,
            loadingDeleteTrack: false,
            date: null,
            menuProject: false,
            search: '',
            isLoadingSearchProject: false,
            createDatePicker: false,
            /* Date range picker */
            dateRangePicker: false,
            dateRange: {},
            /* Data table */
            headers: [
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.description,
                    align: 'start',
                    value: 'description',
                    width: '80%'
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.company,
                    value: 'entity',
                    width: '20%'
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.project_name_ticket_name,
                    value: 'entity.name',
                    width: '20%'
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.tag,
                    value: 'tags',
                    width: '3%'
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.billable,
                    value: 'billable',
                    width: '3%'
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.start,
                    value: 'date_from',
                    width: '4%'
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.end,
                    value: 'date_to',
                    width: '2%'
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.passed,
                    value: 'passed',
                    width: '5%'
                },
                {
                    text: '',
                    value: 'date',
                    width: '3%',
                    sortable: false
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.tracker.actions,
                    value: 'actions',
                    width: '10%',
                    sortable: false
                }
            ],
            panels: [0],
            /* Data */
            tracking: [],
            manualPanel: {
                service: null,
                description: null,
                entity: null,
                entity_type: null,
                tags: [],
                billable: false,
                date_from: moment().format(),
                date_to: moment().add(15, 'minutes').format(),
                date: moment().format(),
                status: 'started',
                timeStart: '00:00:00'
            },
            nameLimit: 255,
            timerPanel: {
                service: null,
                trackId: null,
                passedSeconds: '00:00:00',
                start: null,
                billable: false,
                description: null,
                entity: null,
                entity_type: null,
                tags: [],
                status: 'started',
                date_from: moment(),
                date_to: null,
                date: moment()
            },
            globalTimer: null,
            isLoadingTags: false,
            isLoadingProject: false,
            teamFilter: []
        }
    },
    created: function () {
        moment.updateLocale(this.$store.state.lang.short_code, {
            week: {
                dow: 1,
            },
        });
        this.debounceGetTracking = _.debounce(this.__getTracking, 1000);
        this.debounceSave = _.debounce(this.save, 500);
        if (this.$helpers.localStorage.getKey('dateRange', 'tracking')) {
            this.dateRange = this.$helpers.localStorage.getKey('dateRange', 'tracking');
            if (moment(this.dateRange.end).format(this.dateFormat) === moment().subtract(1, 'days').format(this.dateFormat)) {
                this.dateRange.end = moment();
                this.$helpers.localStorage.storeKey('dateRange', this.dateRange, 'tracking');
            }
        } else {
            this.dateRange = {
                start: moment().subtract(1, 'days').format(this.dateFormat),
                end: moment().format(this.dateFormat)
            };
            this.$helpers.localStorage.storeKey('dateRange', this.dateRange, 'tracking');
        }
        this.date = moment().format(this.dateFormat);
    },
    mounted() {
        this.__globalTimer();
        this.debounceGetTracking();
        this.$store.dispatch('Projects/getProjectList', { search: null });
        this.$store.dispatch('Products/getProductList', { search: null });
        this.$store.dispatch('Clients/getClientList', { search: null });
        this.$store.dispatch('Tags/getTagList');
        this.$store.dispatch('Services/getServicesList', { search: null });
        this.$store.dispatch('Tickets/getTicketList', { search: null });
        this.$store.dispatch('Team/getManagedTeams', { withEmployee: true });
        this.$store.dispatch('Team/getCoworkers', { search: null });
        this.$store.dispatch('Team/getTeams', { search: null });
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        __globalTimer() {
            return setTimeout(() => {
                this.globalTimer = moment();
                this.__globalTimer();
            }, 1000);
        },
        __getTracking() {
            if (this.attemptRepeat > 5) return;
            this.loading = true;
            const queryParams = new URLSearchParams({
                date_from: moment(this.dateRange.start).format(this.dateFormat) || null,
                date_to: moment(this.dateRange.end).format(this.dateFormat) || null
            });
            return axios.get(`/api/tracking/tracker?${queryParams.toString()}`)
                .then(({ data }) => {
                    this.tracking = data.data.map(i => ({
                        ...i,
                        date: moment(i.date_from).format('YYYY-MM-DD'),
                        date_picker: false
                    }));
                    this.loading = false;
                    this.attemptRepeat = 0;
                    return data;
                })
                .catch(e => {
                    this.attemptRepeat++;
                    this.debounceGetTracking();
                });
        },
        __createTracking(data) {
            this.loadingCreateTrack = true;
            return axios.post('/api/tracking/tracker', data)
                .then(({ data }) => {
                    if (!data.success) {
                        if (data.error) {
                            this.debounceGetTracking();
                            this.resetManualPanel();
                            this.loadingCreateTrack = false;
                            const error = Object.keys(data.error)[0];
                            this.snackbarMessage = data.error[error].pop();
                            this.actionColor = 'error'
                            this.snackbar = true;
                        }
                        return false;
                    }
                    this.debounceGetTracking();
                    this.resetManualPanel();
                    this.loadingCreateTrack = false;
                    return data;
                });
        },
        __updateTrackingById(id, data) {
            this.loadingUpdateTrack = true;
            return axios.patch(`/api/tracking/tracker/${id}`, data)
                .then(({ data }) => {
                    if (!data.success) {
                        if (data.error) {
                            this.debounceGetTracking();
                            this.resetManualPanel();
                            this.loadingUpdateTrack = false;
                            this.snackbarMessage = data.error;
                            this.actionColor = 'error'
                            this.snackbar = true;
                        }
                        return false;
                    }
                    this.debounceGetTracking();
                    this.resetManualPanel();
                    this.loadingUpdateTrack = false;
                    return data;
                });
        },
        __deleteTrackingById(id) {
            this.loadingDeleteTrack = true;
            return axios.delete(`/api/tracking/tracker/${id}`)
                .finally(e => {
                    this.debounceGetTracking();
                    this.loadingDeleteTrack = false;
                });
        },
        __duplicateTracking(id) {
            return axios.post(`/api/tracking/tracker/${id}/duplicate`)
                .then(({ data }) => {
                    this.debounceGetTracking();
                    return data;
                });
        },
        actionCreateTrack() {
            this.manualPanel.status = 'stopped';
            let data = this.manualPanel;
            data.date = moment(data.date_from).format();
            data.date_from = moment(data.date_from).utc().format();
            data.date_to = moment(data.date_to).utc().format();
            this.__createTracking(data);
        },
        actionStartNewTrack() {
            // start
            if (!this.timerPanel.start) {
                this.timerPanel.trackId = null;
                this.timerPanel.status = 'started';
                this.timerPanel.start = moment().utc().format();
                this.timerPanel.date = moment().utc().format();
                this.timerPanel.date_from = moment().utc().format();
                this.timerPanel.date_to = null;
                this.timerPanel.timeStart = this.timerPanel.start;
                this.loadingCreateTrack = true;
                this.__createTracking(this.timerPanel)
                    .then(data => {
                        if (data.success) {
                            this.timerPanel.trackId = data.data.id;
                        }
                    });
            } else {
                // stop
                this.timerPanel.date = moment(this.timerPanel.start).format();
                this.timerPanel.date_from = moment(this.timerPanel.start).format();
                this.timerPanel.date_to = moment().utc().format();
                this.timerPanel.timeStart = this.timerPanel.start;
                this.timerPanel.start = null;
                this.timerPanel.status = 'stopped';
                const index = this.tracking.findIndex(i => i.id === this.timerPanel.trackId);
                this.tracking[index].status = 'stopped';
                if (this.timerPanel.trackId) {
                    this.__updateTrackingById(this.timerPanel.trackId, this.timerPanel);
                } else {
                    this.__createTracking(this.timerPanel);
                }
                this.resetTimerPanel();
            }
        },
        actionDuplicateTracking(trackerId) {
            this.__duplicateTracking(trackerId);
        },
        actionDeleteTracking(trackerId) {
            this.__deleteTrackingById(trackerId);
        },
        actionStopTracking(trackerId) {
            if (this.timerPanel.trackId === trackerId) {
                this.timerPanel.start = null;
                this.resetTimerPanel();
                const index = this.tracking.findIndex(i => i.id === trackerId);
                this.tracking[index].status = 'stopped';
                this.tracking[index].date_to = moment().format();
            }
            this.__updateTrackingById(trackerId, {
                status: 'stopped',
                date_to: moment().format()
            });
        },
        actionStartTrackingAsId(trackerId) {
            this.__duplicateTracking(trackerId)
                .then(data => {
                    if (data.success) {
                        this.__updateTrackingById(data.data.id, {
                            date_from: moment().format(),
                            date_to: null,
                            status: 'started'
                        });
                    }
                });
        },
        resetManualPanel() {
            this.manualPanel = {
                ...this.manualPanel,
                service: null,
                description: null,
                projectId: null,
                tags: [],
                billable: false,
                // date_from: moment().format(),
                // date_to: moment().add(15, 'minutes').format(),
                // date: moment().format(this.dateFormat),
                // status: 'started',
                // timeStart: this.helperConvertSecondsToTimeFormat(this.helperCalculatePassedTime(moment().format(), moment().add(15, 'minutes').format()))
            };
        },
        resetTimerPanel() {
            this.timerPanel = {
                ...this.timerPanel,
                service: null,
                trackId: null,
                start: null,
                billable: false,
                description: null,
                project: null,
                tags: [],
                // status: 'started',
                // passedSeconds: '00:00:00',
                // date_from: moment(),
                // date_to: null,
                // date: moment()
            };
        },
        handlerDateRange() {
            this.$helpers.localStorage.storeKey('dateRange', this.dateRange, 'tracking');
            this.dateRangePicker = false;
            this.debounceGetTracking();
        },
        handlerProjectTimerPanel(data) {
            this.timerPanel.entity = data.project;
        },
        handlerProjectManualPanel(data) {
            this.manualPanel.entity = data.project;
        },
        handlerTagsTimerPanel(data) {
            this.timerPanel.tags  = data.tags;
        },
        handlerTagsManualPanel(data) {
            this.manualPanel.tags  = data.tags;
        },
        filterTracking(date) {
            const self = this;
            return this.tracking
                .filter(function(item) {
                    if (self.teamFilter.length) {
                        return self.teamFilter.indexOf(item.user_id) !== -1;
                    }
                    return true;
                })
                .filter(function(item) {
                return moment(item.date_from).format(self.dateFormat) === date;
            });
        },
        save (item, fieldName, newValue = null) {
            if (['date_from', 'date_to'].indexOf(fieldName)) {
                item.passed = this.$helpers.time.getSecBetweenDates(item.date_from, item.date_to, true);
            }
            if (newValue) {
                item[fieldName] = newValue;
            }
            if (fieldName === 'entity') {
                item.entity_id = item.entity && item.entity.id ? item.entity.id : item.entity_id;
                item.entity_type = item.entity && item.entity.from ? 'App\\Ticket' : 'App\\TrackingProject';
            }
            if (moment(item.date_to).isBefore(moment(item.date_from))) {
                item.date_to = moment(item.date_to).set({
                    date: moment(item.date_from).add(1, 'days').date(),
                    year: moment(item.date_from).year(),
                    month: moment(item.date_from).month(),
                }).format();
            }
            this.__updateTrackingById(item.id, item);
        },
        cancel () {
            //TODO
        },
        open () {
            //TODO
        },
        close () {
            //TODO
        },
        handlerChangeDate(item) {
            const date = {
                date: moment(item.date).date(),
                month: moment(item.date).month(),
                year: moment(item.date).year()
            };
            const seconds = this.$helpers.time.getSecBetweenDates(item.date_from, item.date_to, true);
            item.date_from = moment(item.date_from).set(date).format();
            item.date_to = moment(item.date_from).add(seconds, "seconds").format();
            this.debounceSave(item, 'date_from');
        },
        hasPermission(ids) {
            return this.$helpers.auth.checkPermissionByIds(ids);
        },
        isEditable(tracker) {
            if (!this.hasPermission([43, 44, 45])) {
                return false;
            }
            const trackerDiff = moment().diff(tracker.date_from, 'seconds');
            if (
                (this.hasPermission([45]) &&  trackerDiff > 60 * 60 * 24 * 14)
                || (this.hasPermission([44]) &&  trackerDiff > 60 * 60 * 24 * 7)
            ) {
                return false;
            }
            return true;
        },
        getUserColor(userId) {
            const foundItem = this.teamEmployee.find(i => i.id === userId);
            if (foundItem) {
                return foundItem.color;
            }
            return null;
        },
        correctionTime(dateFrom, dateTo) {
            if (moment(dateTo).isBefore(moment(dateFrom))) {
                dateTo = moment(dateTo).set({
                    date: moment(dateFrom).add(1, 'days').date(),
                    year: moment(dateFrom).year(),
                    month: moment(dateFrom).month(),
                }).toISOString();
            }
        }
    },
    computed: {
        timeAdd () {
            if (moment(this.manualPanel.date_from) > moment(this.manualPanel.date_to)) {
                this.manualPanel.date_to = moment(this.manualPanel.date_to).add(1, 'day').format();
            }
            const seconds = this.$helpers.time.getSecBetweenDates(this.manualPanel.date_from, this.manualPanel.date_to, true);
            return this.$helpers.time.convertSecToTime(seconds);
        },
        dateRangeText () {
            const dateFormat = 'DD MMM YYYY';
            return `${moment(this.dateRange.start).format(dateFormat)} - ${moment(this.dateRange.end).format(dateFormat)}`;
        },
        getPanelDates () {
            const self = this;
            const items = this.tracking
                .filter(function(item) {
                    if (self.teamFilter.length) {
                        return self.teamFilter.indexOf(item.user_id) !== -1;
                    }
                    return true;
                }).reduce(function (acc, item) {
                    const date = moment(item.date_from).format('YYYY-MM-DD');
                    return [...acc, date.toString()];
                }, []);
            const panels = [...new Set(items)].sort().reverse();
            this.panels = panels.map((i,k) => k);
            return panels;
        },
        getFilteredProjects() {
            return this.$store.getters['Projects/getProjects'].map(entry => {
                const name = entry.name.length > this.nameLimit
                    ? entry.name.slice(0, this.nameLimit) + '...'
                    : entry.name

                return Object.assign({}, entry, { name })
            })
        },
        periodStart () {
            return moment(this.dateRange.start).format('DD/MM/YYYY');
        },
        periodEnd () {
            return moment(this.dateRange.end).format('DD/MM/YYYY');
        },
        teamEmployee () {
            let empl = [];
            if (this.hasPermission([90])) {
                empl = this.$store.getters['Team/getCoworkers'];
            }
            if (this.hasPermission([42])) {
                this.$store.getters['Team/getManagedTeams'].map(team => {
                    team.employees.map(e => {
                        empl.push({
                            id: e.employee.user_data.id,
                            name: e.employee.user_data.name,
                            surname: e.employee.user_data.surname,
                            middle_name: e.employee.user_data.middle_name,
                            full_name: e.employee.user_data.full_name,
                            avatar_url: e.employee.user_data.avatar_url,
                            color: this.$helpers.color.genRandomColor(),
                        });
                    });
                });
                return _.sortBy(empl, item => {
                    return item.full_name.toLowerCase();
                });
            }
            return empl;
        },
        manualFormattedDate: {
            get() {
                return moment(this.manualPanel.date_from).format(this.dateFormat);
            },
            set(val) {
                this.manualPanel.date = moment(val).format();
                const date = {
                    date: moment(val).date(),
                    month: moment(val).month(),
                    year: moment(val).year()
                };
                this.manualPanel.date_from = moment(this.manualPanel.date_from)
                    .set(date).toISOString();
                this.correctionTime(this.manualPanel.date_from, this.manualPanel.date_to);
            }
        },
    },
    watch: {
        'manualPanel.date_from': function () {
            this.correctionTime(this.manualPanel.date_from, this.manualPanel.date_to);
        },
        date: function () {
            this.manualPanel.date = moment(this.date).format();
            const date = {
                date: moment(this.date).date(),
                month: moment(this.date).month(),
                year: moment(this.date).year()
            };

            let dayToAdding = 0;
            if (moment(this.manualPanel.date_to).format(this.dateFormat) > moment(this.manualPanel.date_from).format(this.dateFormat)) {
                dayToAdding = 1;
            }
            this.manualPanel.date_to = moment(this.manualPanel.date_to)
                .add(dayToAdding, 'day')
                .format();
        },
        search () {
            this.$store.dispatch('Projects/getProjectList', { search: this.search });
        },
        globalTimer: function () {
            // Update DataTable passed field
            this.tracking.filter(i => {
                return i.status === 'started';
            }).forEach(i => {
                const index = this.tracking.indexOf(i);
                this.tracking[index].passed = this.$helpers.time.getSecBetweenDates(i.date_from, moment(), true);
                this.tracking[index].date_picker = this.tracking[index].date_picker ?? false;
            });
            // Update timerPanel
            if (this.timerPanel.start) {
                const seconds = moment().diff(moment(this.timerPanel.start), 'seconds');
                this.timerPanel.passedSeconds = this.$helpers.time.convertSecToTime(seconds);
            }
        },
        'timerPanel.entity': function () {
            this.timerPanel.entity_type = this.timerPanel.entity && this.timerPanel.entity.from ? "App\\Ticket" : 'App\\TrackingProject';
        },
        'manualPanel.entity': function () {
            this.manualPanel.entity_type = this.manualPanel && this.manualPanel.entity.from ? "App\\Ticket" : 'App\\TrackingProject';
        }
    },
}
</script>
