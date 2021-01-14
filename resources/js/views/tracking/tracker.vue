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

        <template>
            <v-card flat>
                <v-container fluid>
                    <v-row class="child-flex">
                        <v-toolbar>
                            <v-col md="4" sm="4">
                                <v-text-field
                                    outlined
                                    dense
                                    hide-details="auto"
                                    placeholder="What are you working on?"
                                ></v-text-field>
                            </v-col>
                            <v-spacer v-if="panel.activePanel"></v-spacer>
                            <v-col md="3" sm="4" class="text-right">
                                <v-btn
                                    tile
                                    small
                                    text
                                    :color="themeColor"
                                >
                                    <v-icon center>
                                        mdi-plus-circle-outline
                                    </v-icon>
                                    &nbsp;&nbsp;Project
                                </v-btn>
                                <v-btn
                                    icon
                                    x-small
                                    fab
                                    :color="themeColor"
                                >
                                    <v-icon center>
                                        mdi-tag-outline
                                    </v-icon>
                                </v-btn>
                                <v-btn
                                    icon
                                    x-small
                                    fab
                                    :color="themeColor"
                                >
                                    <v-icon center>
                                        mdi-currency-usd
                                    </v-icon>
                                </v-btn>
                            </v-col>
                            <v-col md="1" v-if="panel.activePanel">
                                <v-text-field
                                    placeholder="00:00:00"
                                    dense
                                    hide-details="auto"
                                ></v-text-field>
                            </v-col>
                            <v-col md="1" v-if="panel.activePanel">
                                <v-btn
                                    tile
                                    small
                                    :color="themeColor"
                                    style="color: white"
                                >
                                    Start
                                </v-btn>
                            </v-col>
                            <v-col md="1" v-if="!panel.activePanel">
                                <v-menu
                                    ref="menu"
                                    v-model="panel.timeFromPicker"
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    :return-value.sync="timeFrom"
                                    transition="scale-transition"
                                    offset-y
                                    max-width="290px"
                                    min-width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="timeFrom"
                                            label="From"
                                            placeholder="HH:MM"
                                            prepend-icon="mdi-clock-time-four-outline"
                                            v-bind="attrs"
                                            v-on="on"
                                            hide-details="auto"
                                            style="max-width: 100px"
                                        ></v-text-field>
                                    </template>
                                    <v-time-picker
                                        dense
                                        v-if="panel.timeFromPicker"
                                        v-model="timeFrom"
                                        full-width
                                        @click:minute="$refs.menu.save(timeFrom)"
                                    ></v-time-picker>
                                </v-menu>
                            </v-col>
                            <v-col md="1" v-if="!panel.activePanel">
                                <v-menu
                                    ref="menu"
                                    v-model="panel.timeToPicker"
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    :return-value.sync="timeTo"
                                    transition="scale-transition"
                                    offset-y
                                    max-width="290px"
                                    min-width="290px"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="timeTo"
                                            label="To"
                                            placeholder="HH:MM"
                                            prepend-icon="mdi-clock-time-four-outline"
                                            v-bind="attrs"
                                            v-on="on"
                                            hide-details="auto"
                                            style="max-width: 100px"
                                        ></v-text-field>
                                    </template>
                                    <v-time-picker
                                        v-if="panel.timeToPicker"
                                        v-model="timeTo"
                                        full-width
                                        @click:minute="$refs.menu.save(timeTo)"
                                    ></v-time-picker>
                                </v-menu>
                            </v-col>
                            <v-col md="1" v-if="!panel.activePanel">
                                <v-menu
                                    v-model="panel.createDatePicker"
                                    :close-on-content-click="false"
                                    :nudge-right="40"
                                    transition="scale-transition"
                                    offset-y
                                    min-width="auto"
                                    left
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="createDate"
                                            label="Date"
                                            placeholder="YYYY-MM-DD"
                                            prepend-icon="mdi-calendar"
                                            v-bind="attrs"
                                            v-on="on"
                                            hide-details="auto"
                                            style="max-width: 1050px"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="createDate"
                                        @input="panel.createDatePicker = false"
                                    ></v-date-picker>
                                </v-menu>
                            </v-col>
                            <v-col md="1" v-if="!panel.activePanel">
                                <v-btn
                                    small
                                    tile
                                    :color="themeColor"
                                    style="color: white"
                                >
                                    Add
                                </v-btn>
                            </v-col>
                            <v-col md="1" sm="2">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            fab
                                            x-small
                                            :dark="panel.activePanel"
                                            :color="themeColor"
                                            @click="panel.activePanel=true"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                            <v-icon>mdi-clock</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Timer</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            fab
                                            x-small
                                            :dark="!panel.activePanel"
                                            :color="themeColor"
                                            @click="panel.activePanel=false"
                                            v-bind="attrs"
                                            v-on="on"
                                        >
                                            <v-icon>mdi-format-list-bulleted-square</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Manual</span>
                                </v-tooltip>
                            </v-col>
                        </v-toolbar>
                    </v-row>
                </v-container>
            </v-card>
        </template>

        <template>
            <v-row>
                <v-col
                    cols="12"
                    offset-md="7"
                    md="4"
                    offset-lg="9"
                    lg="2"
                >
                    <v-menu
                        ref="panel.dateRangePicker"
                        v-model="panel.dateRangePicker"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        max-width="290px"
                        min-width="auto"
                        class="float-right"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="dateRangeText"
                                label="Date range"
                                persistent-hint
                                prepend-icon="mdi-calendar"
                                v-bind="attrs"
                                v-on="on"
                                hide-details="auto"
                            ></v-text-field>
                        </template>
                        <v-date-picker
                            v-model="dateRange"
                            range
                            no-title
                            @input="dateRangeHandler()"
                        ></v-date-picker>
                    </v-menu>
                </v-col>
            </v-row>
        </template>

        <br>

        <template>
            <v-expansion-panels>
                <v-expansion-panel
                    isActive="true">
                    <v-expansion-panel-header
                        :color="themeColor"
                        style="color: white"
                    >
                        Date
                    </v-expansion-panel-header>
                    <v-expansion-panel-content>
                        <template>
                            <div>
                                <v-data-table
                                    :items="tracking"
                                >
                                    <template v-slot:item.name="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.name"
                                            @save="save"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            {{ props.item.name }}
                                            <template v-slot:input>
                                                <v-text-field
                                                    v-model="props.item.name"
                                                    :rules="[max25chars]"
                                                    label="Edit"
                                                    single-line
                                                    counter
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
                                    </template>
                                    <template v-slot:item.iron="props">
                                        <v-edit-dialog
                                            :return-value.sync="props.item.iron"
                                            large
                                            persistent
                                            @save="save"
                                            @cancel="cancel"
                                            @open="open"
                                            @close="close"
                                        >
                                            <div>{{ props.item.iron }}</div>
                                            <template v-slot:input>
                                                <div class="mt-4 title">
                                                    Update Iron
                                                </div>
                                                <v-text-field
                                                    v-model="props.item.iron"
                                                    :rules="[max25chars]"
                                                    label="Edit"
                                                    single-line
                                                    counter
                                                    autofocus
                                                ></v-text-field>
                                            </template>
                                        </v-edit-dialog>
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

<script>
import EventBus from "../../components/EventBus";
import moment from "moment";

export default {
    data() {
        return {
            dateFormat: 'YYYY-MM-DD',
            langMap: this.$store.state.lang.lang_map,
            themeColor: this.$store.state.themeColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            loading: false,
            panel: {
                activePanel: true,
                timeFromPicker: false,
                timeToPicker: false,
                dateRangePicker: false,
                createDatePicker: false
            },
            timeFrom: null,
            timeTo: null,
            createDate: null,
            dateRange: [],
            tracking: [],
            options: {
                page: 1,
                sortDesc: [true],
                sortBy: ['id']
            },
            footerProps: {
                itemsPerPage: 10,
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            }
        }
    },
    created() {
        this.dateRange = [
            moment().subtract(1, 'days').format(this.dateFormat),
            moment().format(this.dateFormat)
        ];
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
    },
    methods: {
        getTracking() {
            this.loading = this.themeColor
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id'
                this.options.sortDesc[0] = true
            }
            if (this.totalTickets < this.options.itemsPerPage) {
                this.options.page = 1
            }
            const queryParams = new URLSearchParams({
                per_page: this.footerProps.itemsPerPage,
                page: this.options.page,
                dateFrom: this.dateRange[0] || null,
                dateTo: this.dateRange[1] || null
            });
            axios.get(`/api/tracking/tracker?${queryParams.toString()}`)
                .then(response => {
                    response = response.data
                    this.projects = response.data.data
                    this.totalProjects = response.data.total
                    this.lastPage = response.data.last_page
                    this.loading = false
                });
        },
        dateRangeHandler() {
            if (this.dateRange.length === 2) {
                this.dateRange.sort();
                this.panel.dateRangePicker = false;
                this.getTracking();
            }
        }
    },
    computed: {
        dateRangeText () {
            return this.dateRange.join(' ~ ')
        },
    },
    watch: {

    },
}
</script>
