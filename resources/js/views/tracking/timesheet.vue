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
                mandatory
            >
                <v-btn
                    @click="selected = []; expandedManagerData = []"
                >
                    {{ langMap.tracking.timesheet.time_tracked }}
                </v-btn>
                <v-btn
                    @click="selected = []; expandedManagerData = []"
                >
                    {{ langMap.tracking.timesheet.approval_pending }}
                </v-btn>
                <v-btn
                    @click="selected = []; expandedManagerData = []"
                >
                    {{ langMap.tracking.timesheet.rejected }}
                </v-btn>
                <v-btn
                    @click="selected = []; expandedManagerData = []"
                >
                    {{ langMap.tracking.timesheet.approved }}
                </v-btn>
                <v-btn
                    v-if="isManager"
                    @click="selected = []; expandedManagerData = []"
                >
                    {{ langMap.tracking.timesheet.approval_requests }}
                </v-btn>
            </v-btn-toggle>

            <div class="d-flex" v-if="[STATUS_APPROVAL_REQUESTS].indexOf(typeOfItems) !== -1">
                <v-select
                    label="Filter by status"
                    class="d-inline-flex mt-4 ml-16"
                    v-model="approvalRequestFilter"
                    :items="approvalRequestFilterItems"
                    item-text="text"
                    item-value="value"
                    clearable
                    multiple
                ></v-select>
            </div>
            <v-spacer></v-spacer>

            <v-btn
                :color="themeBgColor"
                :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                class="mx-2"
                small
                :disabled="!exportAvailable"
                @click="exportTimesheet()"
            >
                Export
            </v-btn>
<!--            <v-dialog-->
<!--                v-model="exportDialog"-->
<!--                width="500"-->
<!--            >-->
<!--                <template v-slot:activator="{ on, attrs }">-->
<!--                    <v-btn-->
<!--                        :color="themeBgColor"-->
<!--                        :style="{ color: $helpers.color.invertColor(themeBgColor)}"-->
<!--                        v-bind="attrs"-->
<!--                        v-on="on"-->
<!--                        class="mx-2"-->
<!--                        small-->
<!--                    >-->
<!--                        Export-->
<!--                    </v-btn>-->
<!--                </template>-->

<!--                <v-card>-->
<!--                    <v-card-title class="grey lighten-2">-->
<!--                        Export to PDF-->
<!--                    </v-card-title>-->

<!--                    <v-card-text>-->
<!--                        <br>-->
<!--                        <p>Choose period:</p>-->
<!--                        <div class="d-flex">-->
<!--                            <v-menu class="d-inline-flex flex-grow-1"-->
<!--                                    v-model="exportDialogFrom"-->
<!--                                    :close-on-content-click="false"-->
<!--                                    :nudge-right="40"-->
<!--                                    transition="scale-transition"-->
<!--                                    offset-y-->
<!--                                    min-width="auto"-->
<!--                            >-->
<!--                                <template v-slot:activator="{ on, attrs }">-->
<!--                                    <v-text-field-->
<!--                                        v-model="exportParams.from"-->
<!--                                        label="From"-->
<!--                                        prepend-icon="mdi-calendar"-->
<!--                                        readonly-->
<!--                                        v-bind="attrs"-->
<!--                                        v-on="on"-->
<!--                                    ></v-text-field>-->
<!--                                </template>-->
<!--                                <v-date-picker-->
<!--                                    v-model="exportParams.from"-->
<!--                                    @input="exportDialogFrom = false"-->
<!--                                ></v-date-picker>-->
<!--                            </v-menu>-->
<!--                            <v-menu class="d-inline-flex flex-grow-1"-->
<!--                                    v-model="exportDialogTo"-->
<!--                                    :close-on-content-click="false"-->
<!--                                    :nudge-right="40"-->
<!--                                    transition="scale-transition"-->
<!--                                    offset-y-->
<!--                                    min-width="auto"-->
<!--                            >-->
<!--                                <template v-slot:activator="{ on, attrs }">-->
<!--                                    <v-text-field-->
<!--                                        v-model="exportParams.to"-->
<!--                                        label="To"-->
<!--                                        prepend-icon="mdi-calendar"-->
<!--                                        readonly-->
<!--                                        v-bind="attrs"-->
<!--                                        v-on="on"-->
<!--                                    ></v-text-field>-->
<!--                                </template>-->
<!--                                <v-date-picker-->
<!--                                    v-model="exportParams.to"-->
<!--                                    @input="exportDialogTo = false"-->
<!--                                ></v-date-picker>-->
<!--                            </v-menu>-->
<!--                        </div>-->
<!--                        <p>Select a types of timesheet to export:</p>-->
<!--                        <v-checkbox-->
<!--                            class="my-0"-->
<!--                            dense-->
<!--                            v-model="exportParams.tracked"-->
<!--                            value="tracked"-->
<!--                            label="Tracked"-->
<!--                        ></v-checkbox>-->
<!--                        <v-checkbox-->
<!--                            class="my-0"-->
<!--                            dense-->
<!--                            v-model="exportParams.pending"-->
<!--                            value="pending"-->
<!--                            label="Pending/Request"-->
<!--                        ></v-checkbox>-->
<!--                        <v-checkbox-->
<!--                            class="my-0"-->
<!--                            dense-->
<!--                            v-model="exportParams.rejected"-->
<!--                            value="rejected"-->
<!--                            label="Rejected"-->
<!--                        ></v-checkbox>-->
<!--                        <v-checkbox-->
<!--                            class="my-0"-->
<!--                            dense-->
<!--                            v-model="exportParams.archived"-->
<!--                            value="archived"-->
<!--                            label="Archived/Approved"-->
<!--                        ></v-checkbox>-->
<!--                    </v-card-text>-->

<!--                    <v-divider></v-divider>-->

<!--                    <v-card-actions>-->
<!--                        <v-spacer></v-spacer>-->
<!--                        <v-btn-->
<!--                            color="error"-->
<!--                            text-->
<!--                            @click="exportDialog = false"-->
<!--                        >-->
<!--                            Cancel-->
<!--                        </v-btn>-->
<!--                        <v-btn-->
<!--                            color="success"-->
<!--                            text-->
<!--                            @click="exportDialog = false; exportTimesheet()"-->
<!--                        >-->
<!--                            Export-->
<!--                        </v-btn>-->
<!--                    </v-card-actions>-->
<!--                </v-card>-->
<!--            </v-dialog>-->
        </v-toolbar>

        <v-spacer>&nbsp;</v-spacer>

        <div class="d-flex flex-row" v-if="[STATUS_TRACKED].indexOf(typeOfItems) !== -1">
            <div class="d-inline-flex flex-grow-0 mx-4"
                 v-if="[STATUS_TRACKED].indexOf(typeOfItems) !== -1"
             >
                <v-checkbox
                    class="mt-1"
                    label="View and Edit services"
                    v-model="showEditServices"
                    hide-details
                ></v-checkbox>
            </div>
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
                >
                    <template v-slot:item="{ item }">
                        <div class="d-flex" style="width: 100%">
                            <div class="d-inline-flex flex-grow-1">{{ item.name }}</div>
                            <div class="d-inline-flex flex-grow-0">
                                <v-icon v-if="item.is_favorite">mdi-star</v-icon>
                            </div>
                        </div>
                    </template>
                </v-select>
            </div>
            <div class="d-inline-flex flex-grow-1">
                <v-select
                    class="mx-4"
                    v-model="filterStatus"
                    :items="getStatuses()"
                    item-value="value"
                    item-text="text"
                    :label="langMap.tracking.timesheet.filter + ' by status'"
                    dense
                    style="max-width: 200px"
                    clearable
                    multiple
                >
                </v-select>
            </div>
            <div class="d-inline-flex flex-grow-1 mx-4">
                <div class="d-flex flex-row">
                    <div class="d-inline-flex">
                        <v-btn
                            icon
                            @click="date = moment(date).subtract(viewType === 'daily' ? 1 : 7, 'days').format(dateFormat)"
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
                            @click="date = moment(date).add(viewType === 'daily' ? 1 : 7, 'days').format(dateFormat)"
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
                    v-if="[STATUS_TRACKED].indexOf(typeOfItems) !== -1"
                >
                    <v-radio
                        v-if="false"
                        class="d-inline-flex"
                        value="daily"
                        disabled
                        :label="langMap.tracking.timesheet.daily"
                    ></v-radio>
                    <v-radio
                        v-if="false"
                        class="d-inline-flex"
                        value="weekly"
                        :label="langMap.tracking.timesheet.weekly"
                    ></v-radio>
                </v-radio-group>
            </div>
        </div>

        <!-- DAILY VIEW. TIME TRACKED -->
        <v-data-table
            v-if="viewType === 'daily'"
            :headers="dailyHeaders"
            show-select
            single-select
            :loading="loading"
            loading-text="Loading... Please wait"
            v-model="selected"
            :items-per-page="countRecordsOnTable"
        >
            <template v-slot:footer>
                Daily view
            </template>
        </v-data-table>

        <!-- WEEKLY VIEW. TIME TRACKED -->
        <template v-if="[STATUS_APPROVAL_PENDING, STATUS_REJECTED, STATUS_APPROVAL_REQUESTS, STATUS_ARCHIVED].indexOf(typeOfItems) !== -1
            || [STATUS_APPROVAL_REQUESTS].indexOf(typeOfItems) !== -1 && isManager">
            <v-data-table
                :headers="weeklyManagerHeaders"
                :single-select="singleSelect"
                :items="getTimesheetForManager"
                :loading="loading"
                loading-text="Loading... Please wait"
                :expanded="expandedManagerData"
                hide-default-footer
                show-expand
                :items-per-page="countRecordsOnTable"
            >
                <template v-slot:item.user.full_name="{ item }">
                    <span v-if="item && item.user">{{ item.user.full_name }}</span>
                </template>
                <template v-slot:item.number="{ item }">
                    {{ item.number }}
                </template>
                <template v-slot:item.weekNumber="{ item }">
                    {{ moment(item.from).isoWeek() }}
                </template>
                <template v-slot:item.from="{ item }">
                    {{ moment(item.from).format('DD/MM/YYYY') }}
                </template>
                <template v-slot:item.to="{ item }">
                    {{ moment(item.to).format('DD/MM/YYYY') }}
                </template>
                <template v-slot:item.submitted_on="{ item }">
                    <span v-if="item.submitted_on">{{ moment(item.submitted_on).format(dateFormat) }}</span>
                </template>
                <template v-slot:item.approver="{ item }">
                    <span v-if="item && item.approver">
                        {{ item.approver.full_name }}
                    </span>
                </template>
                <template v-slot:item.total_time="{ item }">
                    {{ $helpers.time.convertSecToTime(item.items.reduce((acc, curr) => acc + curr.total_time, 0)) }}
                </template>
                <template v-slot:item.data-table-expand="{ index, item }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                :color="themeBgColor"
                                dark
                                icon
                                v-bind="attrs"
                                v-on="on"
                                @click="expandItem(item)"
                            >
                                <v-icon>mdi-file-find</v-icon>
                            </v-btn>
                        </template>
                        <span>Preview timesheet</span>
                    </v-tooltip>
                </template>
                <template v-slot:expanded-item="{ headers, item }">
                    <td :colspan="headers.length" class="mx-0 px-0">
                        <v-data-table
                            :headers="calculateHeaders(item.items)"
                            :items="item.items"
                            hide-default-footer
                            class="grey lighten-4"
                            :single-select="singleSelect"
                            :show-select="[STATUS_REJECTED].indexOf(typeOfItems) !== -1"
                            v-model="selected"
                            :items-per-page="countRecordsOnTable"
                        >
                            <template v-slot:item.is_manually="{ isMobile, item, header, value }">
                                <span v-if="item.is_manually">
                                    added manually
                                </span>
                                <span v-else>tracked</span>
                            </template>
                            <template v-slot:item.mon="{ isMobile, item, header, value }">
                                <template v-if="[STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                                    <span v-if="item.times && item.times[0]">
                                        <TimeField
                                            v-model="moment(item.times[0].dateTime).format()"
                                            style="max-width: 100px"
                                            placeholder="hh:mm"
                                            format="HH:mm"
                                            @input="saveChanges(item, 0, $event)"
                                            class="time-field__small"
                                            hide-calendar="false"
                                        ></TimeField>
                                    </span>
                                </template>
                                <template v-else>
                                    {{ moment(item.times[0].dateTime).format('HH:mm') }}
                                </template>
                            </template>
                            <template v-slot:item.tue="{ isMobile, item, header, value }">
                                <template v-if="[STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                                    <span v-if="item.times && item.times[1]">
                                        <TimeField
                                            v-model="moment(item.times[1].dateTime).format()"
                                            style="max-width: 100px"
                                            placeholder="hh:mm"
                                            format="HH:mm"
                                            @input="saveChanges(item, 1, $event)"
                                            class="time-field__small"
                                            hide-calendar="false"
                                        ></TimeField>
                                    </span>
                                </template>
                                <template v-else>
                                    {{ moment(item.times[1].dateTime).format('HH:mm') }}
                                </template>
                            </template>
                            <template v-slot:item.wed="{ isMobile, item, header, value }">
                                <template v-if="[STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                                    <span v-if="item.times && item.times[2]">
                                        <TimeField
                                            v-model="moment(item.times[2].dateTime).format()"
                                            style="max-width: 100px"
                                            placeholder="hh:mm"
                                            format="HH:mm"
                                            @input="saveChanges(item, 2, $event)"
                                            class="time-field__small"
                                            hide-calendar="false"
                                        ></TimeField>
                                    </span>
                                </template>
                                <template v-else>
                                    {{ moment(item.times[2].dateTime).format('HH:mm') }}
                                </template>
                            </template>
                            <template v-slot:item.thu="{ isMobile, item, header, value }">
                                <template v-if="[STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                                    <span v-if="item.times && item.times[3]">
                                        <TimeField
                                            v-model="moment(item.times[3].dateTime).format()"
                                            style="max-width: 100px"
                                            placeholder="hh:mm"
                                            format="HH:mm"
                                            @input="saveChanges(item, 3, $event)"
                                            class="time-field__small"
                                            hide-calendar="false"
                                        ></TimeField>
                                    </span>
                                </template>
                                <template v-else>
                                    {{ moment(item.times[3].dateTime).format('HH:mm') }}
                                </template>
                            </template>
                            <template v-slot:item.fri="{ isMobile, item, header, value }">
                                <template v-if="[STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                                    <span v-if="item.times && item.times[4]">
                                        <TimeField
                                            v-model="moment(item.times[4].dateTime).format()"
                                            style="max-width: 100px"
                                            placeholder="hh:mm"
                                            format="HH:mm"
                                            @input="saveChanges(item, 4, $event)"
                                            class="time-field__small"
                                            hide-calendar="false"
                                        ></TimeField>
                                    </span>
                                </template>
                                <template v-else>
                                    {{ moment(item.times[4].dateTime).format('HH:mm') }}
                                </template>
                            </template>
                            <template v-slot:item.sat="{ isMobile, item, header, value }">
                                <template v-if="[STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                                    <span v-if="item.times && item.times[5]">
                                        <TimeField
                                            v-model="moment(item.times[5].dateTime).format()"
                                            style="max-width: 100px"
                                            placeholder="hh:mm"
                                            format="HH:mm"
                                            @input="saveChanges(item, 5, $event)"
                                            class="time-field__small"
                                            hide-calendar="false"
                                        ></TimeField>
                                    </span>
                                </template>
                                <template v-else>
                                    {{ moment(item.times[5].dateTime).format('HH:mm') }}
                                </template>
                            </template>
                            <template v-slot:item.sun="{ isMobile, item, header, value }">
                                <template v-if="[STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                                    <span v-if="item.times && item.times[6]">
                                        <TimeField
                                            v-model="moment(item.times[6].dateTime).format()"
                                            style="max-width: 100px"
                                            placeholder="hh:mm"
                                            format="HH:mm"
                                            @input="saveChanges(item, 6, $event)"
                                            class="time-field__small"
                                            hide-calendar="false"
                                        ></TimeField>
                                    </span>
                                </template>
                                <template v-else>
                                    {{ moment(item.times[6].dateTime).format('HH:mm') }}
                                </template>
                            </template>
                            <template v-slot:item.total="{ isMobile, item, header, value }">
                                {{ $helpers.time.convertSecToTime(item.total_time, false) }}
                            </template>
                            <template v-slot:item.note="{ isMobile, item, header, value }">
                                <v-edit-dialog
                                    v-if="item.note"
                                    large
                                    save-text="OK"
                                >
                                    <v-btn
                                        color="primary"
                                        icon
                                        v-if="item.note"
                                    >
                                        <v-icon>mdi-information-outline</v-icon>
                                    </v-btn>
                                    <template v-slot:input>
                                        <div class="mt-4" style="max-width: 500px; min-width: 500px; min-height: 100px; overflow-y: auto;">
                                            {{ item.note }}
                                        </div>
                                    </template>
                                </v-edit-dialog>
                            </template>
                        </v-data-table>
                    </td>
                </template>
                <template v-slot:item.remind="{ item }" v-if="[STATUS_APPROVAL_PENDING].indexOf(typeOfItems) !== -1">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                :color="themeBgColor"
                                dark
                                icon
                                v-bind="attrs"
                                v-on="on"
                                small
                                @click="remindTimesheet(item)"
                                :loading="loadingBtn"
                            >
                                <v-icon>mdi-bell</v-icon>
                            </v-btn>
                        </template>
                        <div style="width: 100%; text-align: center">Remind</div>
                        <div>(Resend request for approval)</div>
                    </v-tooltip>
                </template>
                <template v-slot:item.edit="{ item }" v-if="[STATUS_APPROVAL_PENDING, STATUS_REJECTED].indexOf(typeOfItems) !== -1">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                :color="themeBgColor"
                                dark
                                icon
                                v-bind="attrs"
                                v-on="on"
                                @click="cancelTimesheet(item)"
                                small
                            >
                                <v-icon>mdi-file-edit</v-icon>
                            </v-btn>
                        </template>
                        <div style="width: 100%; text-align: center">Edit</div>
                        <div>(Take back request and edit)</div>
                    </v-tooltip>
                </template>
                <template v-slot:item.resubmit="{ item }" v-if="[STATUS_REJECTED].indexOf(typeOfItems) !== -1">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                :color="themeBgColor"
                                dark
                                icon
                                v-bind="attrs"
                                v-on="on"
                                @click="selected = item.items; sendingApprovalDialog = true"
                                small
                            >
                                <v-icon>mdi-send</v-icon>
                            </v-btn>
                        </template>
                        <div style="width: 100%; text-align: center">Re-submit</div>
                        <div>(Re-submit for approval)</div>
                    </v-tooltip>
                </template>

                <template v-slot:item.approve="{ item }" v-if="[STATUS_APPROVAL_REQUESTS].indexOf(typeOfItems) !== -1">
                    <v-btn
                        v-if="item.status === 'pending'"
                        color="success"
                        dark
                        @click="approveTimesheet(item)"
                        small
                        :loading="loadingBtn"
                    >
                        Approve
                    </v-btn>
                    <v-chip color="success" outlined v-if="item.status==='archived'">Approved</v-chip>
                    <v-chip color="error" outlined v-if="item.status==='rejected'">Rejected</v-chip>
                </template>
                <template v-slot:item.reject="{ item }" v-if="[STATUS_APPROVAL_REQUESTS].indexOf(typeOfItems) !== -1">
                    <v-dialog
                        v-if="item.status === 'pending'"
                        v-model="rejectManagerDialog"
                        persistent
                        max-width="490"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                v-if="item.status === 'pending'"
                                color="error"
                                dark
                                v-bind="attrs"
                                v-on="on"
                                small
                                :loading="loadingBtn"
                            >
                                Reject
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title class="text-h5">
                                Reject timesheet?
                            </v-card-title>
                            <v-card-text>
                                <v-textarea
                                    v-model="rejectReason"
                                    label="Reason"
                                ></v-textarea>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    color="red darken-1"
                                    text
                                    @click="rejectManagerDialog = false"
                                >
                                    Cancel
                                </v-btn>
                                <v-btn
                                    color="green darken-1"
                                    text
                                    @click="rejectTimesheet(item); rejectManagerDialog = false;"
                                >
                                    Reject
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </template>
            </v-data-table>
        </template>
        <template v-else>
            <v-data-table
                v-if="viewType === 'weekly'"
                :headers="weeklyHeaders"
                :show-select="[STATUS_TRACKED,STATUS_APPROVAL_PENDING,STATUS_REJECTED].indexOf(typeOfItems) !== -1"
                :single-select="singleSelect"
                :items="getTimesheet"
                :loading="loading"
                loading-text="Loading... Please wait"
                v-model="selected"
                hide-default-footer
                :items-per-page="countRecordsOnTable"
            >
                <template v-slot:body.prepend="{ headers }">
                    <tr class="highlight">
                        <td :class="{'text-end': header.time >= 0}" v-for="header in headers" :style="{
                        width: header.width,
                        maxWidth: header.width,
                        minWidth: header.width
                    }">
                            <template v-if="header.value === 'entity.name'">
                                Total time
                            </template>
                            <span v-if="header.time >= 0">{{ $helpers.time.convertSecToTime(header.time, false) }}</span>
                        </td>
                    </tr>
                </template>
                <template v-slot:body.append="{ headers }"
                          v-if="[STATUS_ARCHIVED, STATUS_APPROVAL_REQUESTS, STATUS_REJECTED].indexOf(typeOfItems) === -1"
                >
                    <tr>
                        <td
                            :class="{ 'text-end': ['data-table-select', 'entity.name'].indexOf(header.value) === -1}"
                            v-for="header in headers"
                            :style="{
                                width: header.width,
                                maxWidth: header.width,
                                minWidth: header.width
                        }">
                            <template v-if="header.value === 'data-table-select'">
                                <div class="text-center" style="width: 100%;">
                                    <v-btn
                                        dark
                                        :color="themeBgColor"
                                        @click="createTimesheet"
                                        class="mx-2"
                                        small
                                    >
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>
                            </template>
                            <template v-if="header.value === 'entity.name'">
                                <div class="mt-1 px-4" style="width: 100%">
                                    <ProjectBtn
                                        :color="themeBgColor"
                                        v-model="form.entity"
                                    ></ProjectBtn>
                                </div>
                            </template>
                            <template v-if="header.value === 'service' && showEditServices">
                                <div class="mt-1 pl-4" style="width: 100%">
                                    <v-edit-dialog
                                        :return-value="form.service"
                                        :ref="`editDialogService`"
                                    >
                                        <span v-if="form.service">
                                            {{ $store.getters['Services/getServices'].find(i => i.id === form.service).name }}
                                        </span>
                                        <span v-else class="text--secondary">Add service</span>
                                        <template v-slot:input>
                                            <v-select
                                                :items="$store.getters['Services/getServices']"
                                                item-value="id"
                                                item-text="name"
                                                v-model="form.service"
                                                clearable
                                                @input="$refs[`editDialogService`][0].isActive = false"
                                            >
                                            </v-select>
                                        </template>
                                    </v-edit-dialog>
                                </div>
                            </template>
                            <template v-if="header.value === 'mon'">
                                <div class="" style="width: 100%">
                                    <TimeField
                                        v-model="form.mon"
                                        style="max-width: 100px"
                                        placeholder="hh:mm"
                                        format="HH:mm"
                                        class="time-field__small"
                                        hide-calendar="false"
                                    ></TimeField>
                                </div>
                            </template>
                            <template v-if="header.value === 'tue'">
                                <div class="" style="width: 100%">
                                    <TimeField
                                        v-model="form.tue"
                                        style="max-width: 100px"
                                        placeholder="hh:mm"
                                        format="HH:mm"
                                        class="time-field__small"
                                        hide-calendar="false"
                                    ></TimeField>
                                </div>
                            </template>
                            <template v-if="header.value === 'wed'">
                                <div class="" style="width: 100%">
                                    <TimeField
                                        v-model="form.wed"
                                        style="max-width: 100px"
                                        placeholder="hh:mm"
                                        format="HH:mm"
                                        class="time-field__small"
                                        hide-calendar="false"
                                    ></TimeField>
                                </div>
                            </template>
                            <template v-if="header.value === 'thu'">
                                <div class="" style="width: 100%">
                                    <TimeField
                                        v-model="form.thu"
                                        style="max-width: 100px"
                                        placeholder="hh:mm"
                                        format="HH:mm"
                                        class="time-field__small"
                                        hide-calendar="false"
                                    ></TimeField>
                                </div>
                            </template>
                            <template v-if="header.value === 'fri'">
                                <div class="" style="width: 100%">
                                    <TimeField
                                        v-model="form.fri"
                                        style="max-width: 100px"
                                        placeholder="hh:mm"
                                        format="HH:mm"
                                        class="time-field__small"
                                        hide-calendar="false"
                                    ></TimeField>
                                </div>
                            </template>
                            <template v-if="header.value === 'sat'">
                                <div class="" style="width: 100%">
                                    <TimeField
                                        v-model="form.sat"
                                        style="max-width: 100px"
                                        placeholder="hh:mm"
                                        format="HH:mm"
                                        class="time-field__small"
                                        hide-calendar="false"
                                    ></TimeField>
                                </div>
                            </template>
                            <template v-if="header.value === 'sun'">
                                <div class="" style="width: 100%">
                                    <TimeField
                                        v-model="form.sun"
                                        style="max-width: 100px"
                                        placeholder="hh:mm"
                                        format="HH:mm"
                                        class="time-field__small"
                                        hide-calendar="false"
                                    ></TimeField>
                                </div>
                            </template>
                            <template v-if="header.value === 'total'">
                                <div class="" style="width: 100%">
                                    {{ $helpers.time.convertSecToTime(formTotalTime, false) }}
                                </div>
                            </template>
                        </td>
                    </tr>
                </template>
                <template v-slot:header.data-table-select="{ header }"></template>
                <template v-slot:header.mon="{ header }">
                    {{ header.text }}<br>
                    {{ header.date }}
                </template>
                <template v-slot:header.tue="{ header }">
                    {{ header.text }}<br>
                    {{ header.date }}
                </template>
                <template v-slot:header.wed="{ header }">
                    {{ header.text }}<br>
                    {{ header.date }}
                </template>
                <template v-slot:header.thu="{ header }">
                    {{ header.text }}<br>
                    {{ header.date }}
                </template>
                <template v-slot:header.fri="{ header }">
                    {{ header.text }}<br>
                    {{ header.date }}
                </template>
                <template v-slot:header.sat="{ header }">
                    {{ header.text }}<br>
                    {{ header.date }}
                </template>
                <template v-slot:header.sun="{ header }">
                    {{ header.text }}<br>
                    {{ header.date }}
                </template>

                <template v-slot:item.data-table-select="{ item }">
                    <v-simple-checkbox
                        v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 || ([STATUS_APPROVAL_PENDING].indexOf(typeOfItems) !== -1 && canApproval(item))"
                        :value="!!selected.find(i => i.id === item.id)"
                        @click="toggleTableItem(item)"
                    ></v-simple-checkbox>
                </template>
                <template v-slot:item.entity.name="{ isMobile, item, header, value }">
                    <ProjectBtn
                        :key="item.id"
                        :color="item.entity && item.entity.color ? item.entity.color : themeBgColor"
                        v-model="item.entity"
                        @input="updateProject(item)"
                    ></ProjectBtn>
                </template>
                <template v-slot:item.status="{ isMobile, item, header, value }">
                    <v-chip v-if="value === 'archived'" small color="success">{{ langMap.tracking.timesheet.approved }}</v-chip>
                    <v-chip v-if="value === 'rejected'" small color="error">{{ langMap.tracking.timesheet.rejected }}</v-chip>
                    <v-chip v-if="value === 'pending'" small color="primary">{{ langMap.tracking.timesheet.approval_pending }}</v-chip>
                    <v-chip v-if="value === 'tracked'" small>{{ langMap.tracking.timesheet.tracked }}</v-chip>
                </template>
                <template v-slot:item.number="{ isMobile, item, header, value }">
                    <template v-if="value">
                        #{{ value }}
                    </template>
                </template>
                <template v-slot:item.service="{ isMobile, item, header, value }">
                    <v-edit-dialog
                        :return-value="item.service"
                        @save="updateService(item)"
                        @close="updateService(item)"
                        :ref="`editDialog${item.id}`"
                    >
                        <span v-if="item.service">{{ item.service.name }}</span>
                        <span v-else class="text--secondary">Add service</span>
                        <template v-slot:input>
                            <v-select
                                :items="$store.getters['Services/getServices']"
                                item-value="id"
                                item-text="name"
                                v-model="item.service"
                                clearable
                                return-object
                                @input="$refs[`editDialog${item.id}`].isActive = false"
                            ></v-select>
                        </template>
                    </v-edit-dialog>
                </template>
                <template v-slot:item.is_manually="{ isMobile, item, header, value }">
                    <span v-if="item.is_manually">
                        added manually
                    </span>
                    <span v-else>tracked</span>
                </template>
                <template v-slot:item.billable="{ isMobile, item, header, value }">
                    <v-btn
                        v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually"
                        fab
                        :icon="!item.billable"
                        x-small
                        :color="themeBgColor"
                        @click="item.billable = !item.billable; saveTimesheet(item)"
                        class="elevation-0"
                    >
                        <v-icon center v-bind:class="{ 'white--text': item.billable }">
                            mdi-currency-usd
                        </v-icon>
                    </v-btn>
                    <v-icon
                        v-else-if="item.billable"
                        center
                        small
                    >
                        mdi-currency-usd
                    </v-icon>
                </template>
                <template v-slot:item.total="{ isMobile, item, header, value }">
                    <span v-if="item.total_time">
                        {{ $helpers.time.convertSecToTime(item.total_time, false) }}
                    </span>
                    <span v-else>00:00</span>
                </template>
                <template v-slot:item.mon="{ isMobile, item, header, value }">
                    <template v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                    <span v-if="item.times && item.times[0]">
                        <TimeField
                            v-model="moment(item.times[0].dateTime).format()"
                            style="max-width: 100px"
                            placeholder="hh:mm"
                            format="HH:mm"
                            @input="saveChanges(item, 0, $event)"
                            class="time-field__small"
                            hide-calendar="false"
                        ></TimeField>
                    </span>
                    </template>
                    <template v-else>
                        {{ moment(item.times[0].dateTime).format('HH:mm') }}
                    </template>
                </template>
                <template v-slot:item.tue="{ isMobile, item, header, value }">
                    <template v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                    <span v-if="item.times && item.times[1]">
                    <TimeField
                        v-model="moment(item.times[1].dateTime).format()"
                        style="max-width: 100px"
                        placeholder="hh:mm"
                        format="HH:mm"
                        @input="saveChanges(item, 1, $event)"
                        class="time-field__small"
                        hide-calendar="false"
                    ></TimeField>
                </span>
                    </template>
                    <template v-else>
                        {{ moment(item.times[1].dateTime).format('HH:mm') }}
                    </template>
                </template>
                <template v-slot:item.wed="{ isMobile, item, header, value }">
                    <template v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                    <span v-if="item.times && item.times[2]">
                    <TimeField
                        v-model="moment(item.times[2].dateTime).format()"
                        style="max-width: 100px"
                        placeholder="hh:mm"
                        format="HH:mm"
                        @input="saveChanges(item, 2, $event)"
                        class="time-field__small"
                        hide-calendar="false"
                    ></TimeField>
                </span>
                    </template>
                    <template v-else>
                        {{ moment(item.times[2].dateTime).format('HH:mm') }}
                    </template>
                </template>
                <template v-slot:item.thu="{ isMobile, item, header, value }">
                    <template v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                    <span v-if="item.times && item.times[3]">
                        <TimeField
                            v-model="moment(item.times[3].dateTime).format()"
                            style="max-width: 100px"
                            placeholder="hh:mm"
                            format="HH:mm"
                            @input="saveChanges(item, 3, $event)"
                            class="time-field__small"
                            hide-calendar="false"
                        ></TimeField>
                    </span>
                    </template>
                    <template v-else>
                        {{ moment(item.times[3].dateTime).format('HH:mm') }}
                    </template>
                </template>
                <template v-slot:item.fri="{ isMobile, item, header, value }">
                    <template v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                    <span v-if="item.times && item.times[4]">
                        <TimeField
                            v-model="moment(item.times[4].dateTime).format()"
                            style="max-width: 100px"
                            placeholder="hh:mm"
                            format="HH:mm"
                            @input="saveChanges(item, 4, $event)"
                            class="time-field__small"
                            hide-calendar="false"
                        ></TimeField>
                    </span>
                    </template>
                    <template v-else>
                        {{ moment(item.times[4].dateTime).format('HH:mm') }}
                    </template>
                </template>
                <template v-slot:item.sat="{ isMobile, item, header, value }">
                    <template v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                    <span v-if="item.times && item.times[5]">
                    <TimeField
                        v-model="moment(item.times[5].dateTime).format()"
                        style="max-width: 100px"
                        placeholder="hh:mm"
                        format="HH:mm"
                        @input="saveChanges(item, 5, $event)"
                        class="time-field__small"
                        hide-calendar="false"
                    ></TimeField>
                </span>
                    </template>
                    <template v-else>
                        {{ moment(item.times[5].dateTime).format('HH:mm') }}
                    </template>
                </template>
                <template v-slot:item.sun="{ isMobile, item, header, value }">
                    <template v-if="[STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1 && item.is_manually">
                    <span v-if="item.times && item.times[6]">
                        <TimeField
                            v-model="moment(item.times[6].dateTime).format()"
                            style="max-width: 100px"
                            placeholder="hh:mm"
                            format="HH:mm"
                            @input="saveChanges(item, 6, $event)"
                            class="time-field__small"
                            hide-calendar="false"
                        ></TimeField>
                    </span>
                    </template>
                    <template v-else>
                        {{ moment(item.times[6].dateTime).format('HH:mm') }}
                    </template>
                </template>
                <template v-slot:item.note="{ isMobile, item, header, value }">
                    <v-edit-dialog
                        v-if="item.note"
                        large
                        save-text="OK"
                    >
                        <v-btn
                            color="primary"
                            icon
                            v-if="item.note"
                        >
                            <v-icon>mdi-information-outline</v-icon>
                        </v-btn>
                        <template v-slot:input>
                            <div class="mt-4" style="max-width: 500px; min-width: 500px; min-height: 100px; overflow-y: auto;">
                                {{ item.note }}
                            </div>
                        </template>
                    </v-edit-dialog>
                </template>
            </v-data-table>
        </template>

        <v-toolbar dense flat style="background-color: #f0f0f0; border-color: #f0f0f0;">
            <v-btn
                v-if="[STATUS_TRACKED].indexOf(typeOfItems) !== -1"
                small
                class="mx-2"
                :color="themeBgColor"
                :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                @click="selected.length ? selected = [] : selected = getTimesheet"
            >
                <span v-if="selected.length">Unselect all</span>
                <span v-else>Select all</span>
            </v-btn>
            <v-btn
                v-if="selected.length && [STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1"
                class="mx-2"
                small
                color="error"
                @click="removeTimesheet"
                :disabled="blockActions"
            >
                Remove selected
            </v-btn>
            <v-spacer></v-spacer>
            <div>
                <v-dialog
                    v-model="saveTemplateDialog"
                    width="500"
                    v-if="selected.length && [STATUS_TRACKED].indexOf(typeOfItems) !== -1"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            :color="themeBgColor"
                            :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                            v-if="selected.length && [STATUS_TRACKED].indexOf(typeOfItems) !== -1"
                            v-bind="attrs"
                            v-on="on"
                            class="mx-2"
                            small
                        >
                            Save as template
                        </v-btn>
                    </template>

                    <v-card>
                        <v-card-title class="grey lighten-2">
                            Save as template
                        </v-card-title>

                        <v-card-text>
                            <br>
                            <v-text-field
                                label="Template name"
                                v-model="newTemplate.name"
                            ></v-text-field>
                            <v-switch
                                v-model="newTemplate.components"
                                label="Projects"
                                value="projects"
                            ></v-switch>
                            <v-switch
                                v-model="newTemplate.components"
                                label="Services"
                                value="services"
                            ></v-switch>
                            <v-switch
                                v-model="newTemplate.components"
                                label="Hours"
                                value="hours"
                            ></v-switch>
                        </v-card-text>

                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="error"
                                text
                                @click="saveTemplateDialog = false; resetSaveAsTemplate()"
                            >
                                Cancel
                            </v-btn>
                            <v-btn
                                color="success"
                                text
                                @click="saveTemplateDialog = false; saveAsTemplate()"
                            >
                                Save
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <v-dialog
                    v-model="loadTemplateDialog"
                    width="500"
                    v-if="[STATUS_TRACKED].indexOf(typeOfItems) !== -1"
                >
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            :color="themeBgColor"
                            :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                            v-bind="attrs"
                            v-on="on"
                            class="mx-2"
                            small
                            v-if="[STATUS_TRACKED].indexOf(typeOfItems) !== -1"
                        >
                            Load template
                        </v-btn>
                    </template>

                    <v-card>
                        <v-card-title class="grey lighten-2">
                            Load template
                        </v-card-title>

                        <v-card-text>
                            <br>
                            <perfect-scrollbar>
                                <v-list dense style="max-height: 400px">
                                    <v-list-item-group
                                        v-model="selectedTemplate"
                                        color="primary"
                                    >
                                        <v-list-item
                                            v-for="(item, i) in $store.getters['Timesheet/getTimesheetTemplates']"
                                            :key="i"
                                        >
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    #{{item.id}}. {{item.name}}
                                                </v-list-item-title>
                                            </v-list-item-content>
                                            <v-list-item-action @click="removeTemplate(item.id)">
                                                <v-icon color="error">mdi-trash-can-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list-item-group>
                                </v-list>
                            </perfect-scrollbar>
                        </v-card-text>

                        <v-divider></v-divider>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="error"
                                text
                                @click="loadTemplateDialog = false"
                            >
                                Cancel
                            </v-btn>
                            <v-btn
                                color="success"
                                text
                                :disabled="selectedTemplate === undefined"
                                @click="loadTemplateDialog = false; loadTemplate()"
                            >
                                Load
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <v-btn
                    v-if="[STATUS_TRACKED].indexOf(typeOfItems) !== -1"
                    class="mx-2"
                    small
                    :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                    :color="themeBgColor"
                    @click="copyLastWeek"
                >
                    Copy previous week
                </v-btn>
            </div>
            <v-spacer></v-spacer>
            <v-btn
                :color="themeBgColor"
                :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                v-if="selected.length && [STATUS_REJECTED].indexOf(typeOfItems) !== -1"
                class="mx-2"
                small
                @click="submitItems('tracked')"
            >
                <span>Back to edit</span>
            </v-btn>
            <v-dialog
                v-model="sendingApprovalDialog"
                width="500"
                v-if="selected.length && [STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1"
                @click:outside="closeSendingApprovalDialog"
                @keydown="closeSendingApprovalDialog"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        :color="themeBgColor"
                        :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                        v-if="selected.length && [STATUS_TRACKED,STATUS_REJECTED].indexOf(typeOfItems) !== -1"
                        v-bind="attrs"
                        v-on="on"
                        class="mx-2"
                        small
                        :disabled="blockActions"
                    >
                        <span v-if="typeOfItems === STATUS_TRACKED">{{ langMap.tracking.timesheet.submit_for_approval }}</span>
                        <span v-else>{{ langMap.tracking.timesheet.resubmit_for_approval }}</span>
                    </v-btn>
                </template>

                <v-card>
                    <v-card-title class="grey lighten-2">
                        Submit selected for approval
                    </v-card-title>

                    <v-card-text>
                        <br>
                        <v-select
                            v-model="selectedApprover"
                            :items="$store.getters['Team/getTeamManagers']"
                            item-text="full_name"
                            item-value="id"
                            label="Select approver"
                            clearable
                        ></v-select>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="error"
                            text
                            @click="selected=[]; sendingApprovalDialog = false"
                        >
                            Cancel
                        </v-btn>
                        <v-btn
                            color="success"
                            text
                            @click="sendingApprovalDialog = false; submitItems('pending')"
                        >
                            Submit
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog
                v-model="approvalDialog"
                width="500"
                v-if="canApprove"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        :color="themeBgColor"
                        :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                        v-if="canApprove"
                        v-bind="attrs"
                        v-on="on"
                        class="mx-2"
                        small
                    >
                        {{ langMap.tracking.timesheet.approve_selected }}
                    </v-btn>
                </template>

                <v-card>
                    <v-card-title class="grey lighten-2">
                        Approve selected
                    </v-card-title>

                    <v-card-text>
                        <br>
                        Approve timesheet?
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="error"
                            text
                            @click="approvalDialog = false"
                        >
                            Cancel
                        </v-btn>
                        <v-btn
                            color="success"
                            text
                            @click="approvalDialog = false; submitItems('archived')"
                        >
                            Approve
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!-- DEPRECATED??? -->
            <v-dialog
                v-model="rejectDialog"
                width="500"
                v-if="canApprove"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        color="error"
                        :style="{ color: $helpers.color.invertColor(themeBgColor)}"
                        v-if="canApprove"
                        v-bind="attrs"
                        v-on="on"
                        class="mx-2"
                        small
                    >
                        {{ langMap.tracking.timesheet.reject_selected }}
                    </v-btn>
                </template>

                <v-card>
                    <v-card-title class="grey lighten-2">
                        Reject selected
                    </v-card-title>

                    <v-card-text>
                        <br>
                        Reject timesheet?
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="error"
                            text
                            @click="rejectDialog = false"
                        >
                            Cancel
                        </v-btn>
                        <v-btn
                            color="success"
                            text
                            @click="rejectDialog = false; submitItems('rejected')"
                        >
                            Reject
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <span class="mx-4">
                Total time: {{ $helpers.time.convertSecToTime(totalTime, false) }} hrs
            </span>
        </v-toolbar>

        <v-snackbar
            v-for="undo in undoStack"
            :key="undo.id"
            v-model="undo.id"
            timeout="-1"
        >
            Will be deleted after {{undo.countdown}} sec
            <template v-slot:action="{ attrs }">
                <v-btn
                    :color="themeBgColor"
                    text
                    v-bind="attrs"
                    @click="undoDeleting(undo.id)"
                >
                    Undo
                </v-btn>
            </template>
        </v-snackbar>

    </v-container>
</template>

<style scoped>
tr.highlight {
    background-color: #f0f0f0;
    border-color: #f0f0f0;
    font-weight: bold;
}

>>> .time-field__small input {
    font-size: 14px;
}

>>> .time-field__small button .v-icon {
    font-size: 14px;
    width: 14px;
    height: 14px;
}
>>> td.text-end .time-field__small {
    float: right;
}
</style>

<script>
import EventBus from '../../components/EventBus';
import moment from 'moment-timezone';
import _ from 'lodash';
import ProjectBtn from './components/project-btn';
import TimeField from './components/time-field';

export default {
    components: {
        ProjectBtn,
        TimeField,
    },
    data () {
        return {
            countRecordsOnTable: 1000,
            dateFormat: 'YYYY-MM-DD',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            STATUS_TRACKED: 0,
            STATUS_APPROVAL_PENDING: 1,
            STATUS_REJECTED: 2,
            STATUS_ARCHIVED: 3,
            STATUS_APPROVAL_REQUESTS: 4,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            dateRangePicker: false,
            dateRange: {
                start: null,
                end: null,
            },
            typeOfItems: 0,
            viewType: 'weekly',
            date: null,
            menuDate: false,
            filterProject: 0,
            filterStatus: [],
            form: {
                entity: null,
                entity_id: null,
                entity_type: null,
                service: null,
                mon: moment().startOf('days').format(),
                tue: moment().startOf('days').format(),
                wed: moment().startOf('days').format(),
                thu: moment().startOf('days').format(),
                fri: moment().startOf('days').format(),
                sat: moment().startOf('days').format(),
                sun: moment().startOf('days').format(),
            },
            loading: false,
            selected: [],
            singleSelect: false,
            sendingApprovalDialog: false,
            approvalDialog: false,
            rejectDialog: false,
            rejectManagerDialog: false,
            expandedManagerData: [],
            selectedApprover: null,
            rejectReason: '',
            dialogNotes: {},
            showEditServices: false,
            loadingBtn: false,
            undoStack: [],
            deletedItems: [],
            newTemplate: {
                name: '',
                components: [],
            },
            saveTemplateDialog: false,
            loadTemplateDialog: false,
            selectedTemplate: undefined,
            approvalRequestFilterItems: [
                {
                    text: 'Requests',
                    value: 'pending'
                },
                {
                    text: 'Approved',
                    value: 'archived'
                },
                {
                    text: 'Rejected',
                    value: 'rejected'
                },
            ],
            approvalRequestFilter: ['pending'],
            exportDialog: false,
            exportDialogFrom: false,
            exportDialogTo: false,
            exportParams: {
                from: moment().startOf('week').format('YYYY-MM-DD'),
                to: moment().endOf('week').format('YYYY-MM-DD'),
                tracked: null,
                pending: 'pending',
                rejected: 'rejected',
                archived: 'archived',
            },
        }
    },
    created () {
        moment.updateLocale(this.$store.state.lang.short_code, {
            week: {
                dow: 1,
            },
        });
        this.debounceGetTimesheet = _.debounce(this._getTimesheet, 3000);
        this.debounceGetProjects = _.debounce(this._getProjects, 1000);
        this.debounceGetTickets = _.debounce(this._getTickets, 1000);
        this.debounceGetManagedTeams = _.debounce(this._getManagedTeams, 1000);
        this.debounceGetTeamManagers = _.debounce(this._getTeamManagers, 1000);
        this.debounceGetCurrentUser = _.debounce(this._getCurrentUser, 1000);
        this.debounceGetServices = _.debounce(this._getServices, 1000);
        this.debounceGetTimesheetTemplates = _.debounce(this._getTimesheetTemplates, 1000);
        this.date = moment().format(this.dateFormat);
    },
    mounted () {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.debounceGetCurrentUser();
        this.debounceGetTimesheet();
        this.debounceGetProjects();
        this.debounceGetTickets();
        this.debounceGetManagedTeams();
        this.resetTimesheet();
        this.debounceGetTeamManagers();
        this.debounceGetServices();
        this.debounceGetTimesheetTemplates();
        this.$store.dispatch('Clients/getClientList', { search: null });
        this.$store.dispatch('Products/getProductList', { search: null });
    },
    methods: {
        async _getTimesheet () {
            this.loading = true;
            await this.$store.dispatch('Timesheet/getTimesheet', {
                ...this.dateRange,
            });
            await this.$store.dispatch('Timesheet/getAllGroupedByStatus', { userId: this.currentUser ? this.currentUser.id : null });
            this.loading = false;
            this.resetTimesheet();
        },
        async _getManagedTeams () {
            this.loading = true;
            await this.$store.dispatch('Team/getManagedTeams', { withEmployee: false });
            this.loading = false;
            this.resetTimesheet();
        },
        _getTeamManagers() {
            this.$store.dispatch('Team/getTeamManagers');
        },
        _getProjects () {
            this.$store.dispatch('Projects/getProjectList', { search: null });
        },
        _getTickets () {
            this.$store.dispatch('Tickets/getTicketList', { search: null });
        },
        _getCurrentUser () {
            this.$store.dispatch('getCurrentUser');
        },
        _getServices() {
            this.$store.dispatch('Services/getServicesList', { search: '' });
        },
        _getTimesheetTemplates() {
            this.$store.dispatch('Timesheet/getTimesheetTemplates');
        },
        createTimesheet () {
            if (this.form.entity) {
                this.form.entity_id = this.form.entity.id;
                this.form.entity_type = this.form.entity.from ? 'App\\Ticket' : 'App\\TrackingProject';
            }
            if (this.form.entity && this.form.entity_id && this.form.entity_type) {
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
            this.selected = [];
        },
        resetTimesheet () {
            this.selected = [];
            this.form = {
                entity: null,
                entity_id: null,
                entity_type: null,
                service: null,
                mon: moment(this.periodStart).startOf('days').format(),
                tue: moment(this.periodStart).add(1, 'days').startOf('days').format(),
                wed: moment(this.periodStart).add(2, 'days').startOf('days').format(),
                thu: moment(this.periodStart).add(3, 'days').startOf('days').format(),
                fri: moment(this.periodStart).add(4, 'days').startOf('days').format(),
                sat: moment(this.periodStart).add(5, 'days').startOf('days').format(),
                sun: moment(this.periodStart).add(6, 'days').startOf('days').format(),
            };
        },
        timeBetween (end) {
            const start = moment(end).startOf('days')
            return this.$helpers.time.getSecBetweenDates(start, end);
        },
        removeTimesheet () {
            if (this.selected.length) {
                const self = this;
                const id = Date.now();
                this.undoStack.push({
                    id,
                    timer: setInterval(() => {
                        const index = self.undoStack.findIndex(i => i.id === id);
                        if (!self.undoStack[index]) return;
                        if (self.undoStack[index].countdown <= 1) {
                            clearInterval(self.undoStack[index].timer);
                            this.removeTimesheetConfirm(self.undoStack[index].items);
                            self.undoStack.splice(index, 1);
                        } else {
                            self.undoStack[index].countdown--;
                        }
                    }, 1000),
                    items: this.selected.map(i => i.id),
                    countdown: 10,
                });
            }
            this.selected = [];
        },
        removeTimesheetConfirm (items) {
            Promise.all(items.map(id => {
                this.$store.dispatch('Timesheet/removeTimesheet', id);
            }))
                .then( () => {
                    this.$store.dispatch('Timesheet/getAllGroupedByStatus', { userId: this.currentUser.id });
                });
        },
        undoDeleting(id) {
            const index = this.undoStack.findIndex(i => i.id === id);
            if (!this.undoStack[index]) return;
            clearInterval(this.undoStack[index].timer);
            this.undoStack.splice(index, 1);
        },
        saveTimesheet (item) {
            return this.$store.dispatch('Timesheet/updateTimesheet', { id: item.id, timesheet: item })
                .then(res => {
                    if (!res) {
                        this.snackbarMessage = 'Error';
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                    // this.debounceGetTimesheet();
                });
        },
        saveChanges (item, index, newValue) {
            item.times[index].dateTime = newValue;
            item.times[index].time = moment(newValue).format('HH:mm:ss');
            return this.saveTimesheet(item);
        },
        submitItems (status) {
            if (this.selected.length) {
                this.$store.dispatch('Timesheet/submitTimesheetByIds', {
                    ids: this.selected.map(i => i.id),
                    status,
                    approver_id: this.selectedApprover,
                })
                    .then(() => this.debounceGetTimesheet());
            }
            this.selected = [];
        },
        canApproval (item) {
            return item.team_id && this.$store.getters['Team/getManagedTeams'].find(i => i.id === item.team_id);
        },
        toggleTableItem (item) {
            this.selected.find(i => i.id === item.id)
                ? this.selected.splice(this.selected.findIndex(i => i.id === item.id), 1)
                : this.selected.push(item);
        },
        expandItem(item) {
            if (this.expandedManagerData.indexOf(item) !== -1) {
                this.expandedManagerData = [];
            } else {
                this.expandedManagerData.splice(0, 1, item);
            }
        },
        calculateHeaders(items) {
            let days = [0, 0, 0, 0, 0, 0, 0];
            items.map(timesheet => {
                for (let i = 0; i < 7; i++) {
                    days[i] += this.timeBetween(timesheet.times[i].dateTime);
                }
            });
            let dates = [];
            for (let i = moment(this.dateRange.start); moment(i).diff(moment(this.dateRange.end)) <= 0; i.add(1, 'day')) {
                dates.push(i.format('MMM DD'));
            }
            let headers = [
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.projects,
                    align: 'start',
                    value: 'entity.name',
                    width: '20%',
                }
            ];
            if ([this.STATUS_TRACKED].indexOf(this.typeOfItems) !== -1) {
                headers.push({
                        text: '',
                        align: 'start',
                        value: 'status',
                        width: '3%',
                    },
                    {
                        text: '',
                        align: 'start',
                        value: 'number',
                        width: '3%',
                    });
            }
            if (this.showEditServices) {
                headers.push({
                    text: '',
                    align: 'end',
                    value: 'service',
                    width: '10%',
                });
            }
            headers = headers.concat([{
                    text: '',
                    align: 'start',
                    value: 'is_manually',
                    width: '3%',
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.mon,
                    align: 'end',
                    value: 'mon',
                    width: '10%',
                    sortable: false,
                    time: days[0],
                    date: dates[0],
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.tue,
                    align: 'end',
                    value: 'tue',
                    width: '10%',
                    sortable: false,
                    time: days[1],
                    date: dates[1],
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.wed,
                    align: 'end',
                    value: 'wed',
                    width: '10%',
                    sortable: false,
                    time: days[2],
                    date: dates[2],
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.thu,
                    align: 'end',
                    value: 'thu',
                    width: '10%',
                    sortable: false,
                    time: days[3],
                    date: dates[3],
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.fri,
                    align: 'end',
                    value: 'fri',
                    width: '10%',
                    sortable: false,
                    time: days[4],
                    date: dates[4],
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.sat,
                    align: 'end',
                    value: 'sat',
                    width: '10%',
                    sortable: false,
                    time: days[5],
                    date: dates[5],
                },
                {
                    text: this.$store.state.lang.lang_map.tracking.timesheet.sun,
                    align: 'end',
                    value: 'sun',
                    width: '10%',
                    sortable: false,
                    time: days[6],
                    date: dates[6],
                },
                {
                    text: 'Total',
                    align: 'end',
                    value: 'total',
                    width: '3%',
                    time: days.reduce((acc, val) => acc + val, 0),
                },
            ]);
            if ([this.STATUS_REJECTED].indexOf(this.typeOfItems) !== -1) {
                headers.push({ text: 'Note', align: 'start', value: 'note', width: '10%' });
                return headers;
            }
            return headers;
        },
        rejectTimesheet(item) {
            this.loadingBtn = true;
            this.$store.dispatch('Timesheet/submitTimesheetByIds', {
                ids: item.items.map(i => i.id),
                status: 'rejected',
                approver_id: this.currentUser.id,
                note: this.rejectReason,
            })
                .then(() => {
                    this.loadingBtn = false;
                    this.$store.dispatch('Timesheet/getCountTimesheetForApproval');
                    this.debounceGetTimesheet();
                    return null;
                });
            this.rejectReason = '';
        },
        approveTimesheet(item) {
            this.loadingBtn = true;
            this.$store.dispatch('Timesheet/submitTimesheetByIds', {
                ids: item.items.map(i => i.id),
                status: 'archived',
                approver_id: this.currentUser.id,
            })
                .then(() => {
                    this.loadingBtn = false;
                    this.$store.dispatch('Timesheet/getCountTimesheetForApproval');
                    this.debounceGetTimesheet();
                    return null;
                });
            this.rejectReason = '';
        },
        cancelTimesheet(item) {
            this.$store.dispatch('Timesheet/submitTimesheetByIds', {
                ids: item.items.map(i => i.id),
                status: 'tracked',
            })
                .then(() => this.debounceGetTimesheet());
            this.rejectReason = '';
        },
        remindTimesheet(item) {
            this.loadingBtn = true;
            this.$store.dispatch('Timesheet/remindTimesheet', { ids: item.items.map(i => i.id) })
                .then(() => {
                    this.loadingBtn = false;
                    return null;
                });
            this.snackbarMessage = 'Success';
            this.actionColor = 'success'
            this.snackbar = true;
        },
        updateService(item) {
            item.service_id = item.service ? item.service.id : null;
            this.$store.dispatch('Timesheet/updateTimesheet', {
                id: item.id,
                timesheet: {
                    service: item.service
                }
            });

        },
        updateProject(item) {
            this.$store.dispatch('Timesheet/updateTimesheet', {
                id: item.id,
                timesheet: {
                    entity_id: item.entity.id,
                    entity_type: item.entity.from ? 'App\\Ticket' : 'App\\TrackingProject',
                }
            });

        },
        chooseProjectHandler(data) {
            console.log(data);
            this.form.entity_id = data.project && data.project.id ? data.project.id : null;
            this.form.entity_type = data.project && data.project.from ? 'App\\Ticket' : 'App\\TrackingProject';
        },
        closeSendingApprovalDialog(e) {
            if (e.key && e.key === 'Escape') {
                this.selected = [];
            } else if (!e.key) {
                this.selected = [];
            }
        },
        copyLastWeek() {
            this.$store.dispatch('Timesheet/copyLastWeek', {
                from: this.dateRange.start,
                to: this.dateRange.end,
            })
            .then(() => {
                this.date = moment().format(this.dateFormat);
            });
        },
        resetSaveAsTemplate() {
            this.newTemplate.name = '';
            this.newTemplate.components = [];
            this.selected = [];
        },
        saveAsTemplate() {
            if (this.selected.length) {
                this.$store.dispatch('Timesheet/saveAsTemplate', { items: this.selected.map(i => i.id), data: this.newTemplate })
                    .then(() => this.resetSaveAsTemplate());
            }
        },
        loadTemplate() {
            const templates = this.$store.getters['Timesheet/getTimesheetTemplates'];
            if (templates[this.selectedTemplate]) {
                const id = templates[this.selectedTemplate].id;
                this.$store.dispatch('Timesheet/loadTemplate', {
                    id,
                    start: this.dateRange.start,
                    end: this.dateRange.end
                });
            }
        },
        removeTemplate(id) {
            this.$store.dispatch('Timesheet/removeTemplate', id);
        },
        exportTimesheet() {
            const selected = (this.selected.length ? this.selected : this.expandedManagerData).map(i => i.id);
            if (selected.length) {
                axios.post(`/api/tracking/timesheet/export?format=pdf`, {
                    selected,
                    status: this.typeOfItems,
                }, {
                    responseType: 'blob'
                })
                    .then(res => {
                        const url = window.URL.createObjectURL(new Blob([res.data]));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', `Report.pdf`);
                        document.body.appendChild(link);
                        link.click();
                        this.exportDialog = false;
                    })
                    .catch(err => {
                        console.log(err);
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    })
            }
        },
        getStatuses() {
            return [
                {
                    value: 'tracked',
                    text: this.langMap.tracking.timesheet.tracked,
                },
                {
                    value: 'pending',
                    text: this.langMap.tracking.timesheet.approval_pending,
                },
                {
                    value: 'rejected',
                    text: this.langMap.tracking.timesheet.rejected,
                },
                {
                    value: 'archived',
                    text: this.langMap.tracking.timesheet.approved,
                },
            ];
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
            this._getTimesheet();
        },
        undoStack () {
            this.deletedItems = this.undoStack.reduce((acc, curr) => acc.concat(curr.items), []);
        },
    },
    computed: {
        exportAvailable () {
            return !!(this.selected.length ? this.selected : this.expandedManagerData).map(i => i.id).length;
        },
        currentUser () {
            return this.$store.getters['getCurrentUser'];
        },
        currentStatus () {
            let status = 'tracked';
            switch (this.typeOfItems) {
                case this.STATUS_TRACKED:
                    status = 'tracked';
                    break;
                case this.STATUS_APPROVAL_PENDING:
                    status = 'pending';
                    break;
                case this.STATUS_REJECTED:
                    status = 'rejected';
                    break;
                case this.STATUS_ARCHIVED:
                    status = 'archived';
                    break;
                case this.STATUS_APPROVAL_REQUESTS:
                    status = 'request';
                    break;
            }
            return status;
        },
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
        weeklyManagerHeaders () {
            const headers = [
                {
                    text: 'Team member',
                    align: 'center',
                    value: 'user.full_name',
                },
                {
                    text: 'Timesheet number',
                    align: 'center',
                    value: 'number',
                },
                {
                    text: 'Week number',
                    align: 'center',
                    value: 'weekNumber',
                },
                {
                    text: 'From',
                    align: 'center',
                    value: 'from',
                },
                {
                    text: 'To',
                    align: 'center',
                    value: 'to',
                },
                {
                    text: 'Submitted on',
                    align: 'center',
                    value: 'submitted_on',
                },
                {
                    text: 'Approver',
                    align: 'center',
                    value: 'approver',
                },
                {
                    text: 'Total time',
                    align: 'center',
                    value: 'total_time',
                },
                { text: '', value: 'data-table-expand', width: '3%' },
            ];
            if (this.typeOfItems === this.STATUS_APPROVAL_PENDING) {
                headers.push({ text: '', value: 'remind', width: '3%' });
            }
            if ([this.STATUS_APPROVAL_PENDING, this.STATUS_REJECTED].indexOf(this.typeOfItems) !== -1) {
                headers.push({ text: '', value: 'edit', width: '3%' });
            }
            if ([this.STATUS_REJECTED].indexOf(this.typeOfItems) !== -1) {
                headers.push({ text: '', value: 'resubmit', width: '3%' });
            }
            if (this.typeOfItems === this.STATUS_APPROVAL_REQUESTS) {
                headers.push({ text: '', value: 'approve', width: '3%' });
                headers.push({ text: '', value: 'reject', width: '3%' });
            }
            return headers;
        },
        weeklyHeaders () {
            return this.calculateHeaders(this.getTimesheet);
        },
        getTimesheet () {
            const user = this.currentUser;
            const timesheet = this.$store.getters['Timesheet/getTimesheet']
                .filter(i => !this.deletedItems.includes(i.id));
            const self = this;
            timesheet.map(i => {
                self.dialogNotes[i.id] = false;
            });
            if (this.filterProject) {
                return timesheet
                    .filter(i => i.user_id === user.id)
                    .filter(i => i.entity_id && i.entity.id === this.filterProject);
            }
            return timesheet
                .filter(i => {
                    if (user) {
                        return i.user_id === user.id;
                    }
                    return true;
                })
                .filter(i => {
                    if (self.filterStatus && self.filterStatus.length) {
                        return self.filterStatus.indexOf(i.status) !== -1;
                    }
                    return true;
                });
        },
        getTimesheetForManager() {
            const user = this.currentUser;
            let timesheet = [];
            if (this.currentStatus === 'pending') {
                timesheet = this.$store.getters['Timesheet/getPendingTimesheet']
                    .filter(i => !this.deletedItems.includes(i.id));
            }
            if (this.currentStatus === 'rejected') {
                timesheet = this.$store.getters['Timesheet/getRejectedTimesheet']
                    .filter(i => !this.deletedItems.includes(i.id));
            }
            if (this.currentStatus === 'request') {
                timesheet = this.$store.getters['Timesheet/getRequestTimesheet']
                    .filter(i => !this.deletedItems.includes(i.id))
                    .filter(i => {
                        if (this.approvalRequestFilter.length) {
                            return this.approvalRequestFilter.indexOf(i.status) !== -1;
                        }
                        return true;
                    });
            }
            if (this.currentStatus === 'archived') {
                timesheet = this.$store.getters['Timesheet/getArchivedTimesheet'];
            }
            // timesheet = this.$store.getters['Timesheet/getTimesheet']
            //     .filter(i => !this.deletedItems.includes(i.id))
            //     .filter(i => i.user_id === user.id || i.approver_id === user.id)
            //     .filter(i => {
            //         if (this.currentStatus === 'pending') {
            //             return i.status === this.currentStatus && i.user_id === user.id;
            //         }
            //         if (this.currentStatus === 'rejected') {
            //             return i.status === this.currentStatus && i.user_id === user.id;
            //         }
            //         if (this.currentStatus === 'request') {
            //             return i.status === 'pending'
            //                 // && this.$store.getters['Team/getManagedTeams'].map(t => t.id).indexOf(i.team_id) !== -1
            //                 && (i.approver_id === null || i.approver_id === this.currentUser.id);
            //         }
            //     });
            return _.uniqBy(timesheet, 'number')
                .map(i => ({ ...i, items: timesheet.filter(t => t.number === i.number) }));
        },
        totalTime () {
            let totalTime = 0;
            this.getTimesheet.map(timesheet => {
                for (let i = 0; i < 7; i++) {
                    totalTime += this.timeBetween(timesheet.times[i].dateTime);
                }
            });
            return totalTime;
        },
        canApprove () {
            if (this.selected.length && this.$store.getters['Team/getManagedTeams'].find(t => this.selected.find(i => i.team_id === t.id))) {
                return this.selected.length && [this.STATUS_APPROVAL_PENDING].indexOf(this.typeOfItems) !== -1;
            }
            return false;
        },
        formTotalTime () {
            return moment(this.form.mon).diff(moment(this.form.mon).startOf('day'), 'seconds')
                + moment(this.form.tue).diff(moment(this.form.tue).startOf('day'), 'seconds')
                + moment(this.form.wed).diff(moment(this.form.wed).startOf('day'), 'seconds')
                + moment(this.form.thu).diff(moment(this.form.thu).startOf('day'), 'seconds')
                + moment(this.form.fri).diff(moment(this.form.fri).startOf('day'), 'seconds')
                + moment(this.form.sat).diff(moment(this.form.sat).startOf('day'), 'seconds')
                + moment(this.form.sun).diff(moment(this.form.sun).startOf('day'), 'seconds');

        },
        isManager() {
            return !!this.$store.getters['Team/getManagedTeams'].length;
        },
        blockActions() {
            if (this.selected.length) {
                let hasFinished = false;
                Promise.all(this.selected.map(i => {
                    if (['archived'].indexOf(i.status) !== -1) {
                        hasFinished = true;
                        return true;
                    }
                }));
                return hasFinished;
            }
            return false;
        },
    }
}
</script>
