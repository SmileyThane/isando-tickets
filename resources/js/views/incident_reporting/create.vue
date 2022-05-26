<template>
    <v-container fluid>
        <v-row>
            <v-col style="border-right: 1px solid gray" cols="12" md="6" lg="4">

                <v-row>
                    <v-col cols="6" class="text-left">
                        <v-text-field class="float-left" id="incident_number" placeholder="Number" dense hide-details readonly />
                    </v-col>
                    <v-col cols="6" class="text-right">
                        <label class="float-right" for="incident_number">STATUS</label>
                    </v-col>
                    <v-col cols="12">
                        <h4 class="heading headline">Incident 3</h4>
                        <v-tabs v-model="tab">
                            <v-tab>General</v-tab>
                            <v-tab>Team</v-tab>
                            <v-tab>Version</v-tab>
                        </v-tabs>
                        <v-tabs-items v-model="tab">
                            <v-tab-item>
                                <v-card flat>
                                    <v-row no-gutters>
                                        <v-col cols="5" class="pr-2 pt-2">
                                            <v-text-field placeholder="Version" class="mb-2" hide-details dense outlined />
                                        </v-col>
                                        <v-col cols="5" class="px-2 pt-2">
                                            <v-menu
                                                ref="menu"
                                                v-model="menu"
                                                :close-on-content-click="false"
                                                transition="scale-transition"
                                                offset-y
                                                min-width="auto"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        placeholder="Valid till"
                                                        class="mb-2"
                                                        hide-details
                                                        dense outlined
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    :color="themeBgColor"
                                                    :active-picker.sync="activePicker"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @change="save"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col cols="2" class="text-right pt-2">
                                            <v-btn icon :color="themeBgColor">
                                                <v-icon>mdi-pencil</v-icon>
                                            </v-btn>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-select placeholder="Stage" class="mb-2" hide-details dense outlined></v-select>
                                            <v-select placeholder="Risk event" class="mb-2" hide-details dense outlined></v-select>
                                            <v-select placeholder="Natural disasters" class="mb-2" hide-details dense outlined></v-select>
                                            <v-select placeholder="All organizations" class="mb-2" hide-details dense outlined></v-select>
                                            <v-checkbox label="Include child organizations" class="mb-2" hide-details dense outlined />
                                            <v-select placeholder="Medium" class="mb-2" hide-details dense outlined></v-select>
                                            <v-textarea placeholder="Description" class="mb-2" hide-details dense outlined></v-textarea>
                                            <v-text-field placeholder="Source" class="mb-2" hide-details dense outlined />
                                        </v-col>
                                        <v-col cols="4">
                                            <v-menu
                                                ref="menu"
                                                v-model="menu"
                                                :close-on-content-click="false"
                                                transition="scale-transition"
                                                offset-y
                                                min-width="auto"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        placeholder="Occurred on"
                                                        class="mb-2"
                                                        hide-details
                                                        dense outlined
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    :color="themeBgColor"
                                                    :active-picker.sync="activePicker"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @change="save"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-menu
                                                ref="menu"
                                                v-model="menu"
                                                :close-on-content-click="false"
                                                transition="scale-transition"
                                                offset-y
                                                min-width="auto"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        placeholder="Detected on"
                                                        class="mb-2"
                                                        hide-details
                                                        dense outlined
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    :color="themeBgColor"
                                                    :active-picker.sync="activePicker"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @change="save"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-menu
                                                ref="menu"
                                                v-model="menu"
                                                :close-on-content-click="false"
                                                transition="scale-transition"
                                                offset-y
                                                min-width="auto"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-text-field
                                                        placeholder="Reported on"
                                                        class="mb-2"
                                                        hide-details
                                                        dense outlined
                                                        v-bind="attrs"
                                                        v-on="on"
                                                    ></v-text-field>
                                                </template>
                                                <v-date-picker
                                                    :color="themeBgColor"
                                                    :active-picker.sync="activePicker"
                                                    :max="(new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)"
                                                    min="1950-01-01"
                                                    @change="save"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                    </v-row>
                                </v-card>
                            </v-tab-item>
                            <v-tab-item></v-tab-item>
                            <v-tab-item></v-tab-item>
                        </v-tabs-items>
                    </v-col>
                </v-row>

            </v-col>
            <v-col style="border-right: 1px solid gray" cols="12" md="6" lg="4">
                <h4 class="heading headline">Incident tasks</h4>
                <v-row no-gutters>
                    <v-row>
                        <v-col cols="12">
                            <v-chip
                                label
                                outlined
                                v-for="(item, index) in ['Monitoring', 'Handling']"
                                :key="index"
                                class="mr-2"
                            >{{ item }}</v-chip>
                        </v-col>
                        <v-col cols="12">
                            <v-card
                                class="d-inline-flex flex-column mr-2 mb-2"
                                max-width="170"
                                min-width="170"
                                outlined
                                v-for="(taskGroup, index) in taskGroups"
                                :key="index"
                            >
                                <v-list-item three-line>
                                    <v-list-item-content>
                                        <div class="">
                                            <span class="float-left">{{ taskGroup.name }}</span>
                                            <span class="float-right" v-if="taskGroup.total">{{ taskGroup.completed }}/{{ taskGroup.total }}</span>
                                        </div>
                                    </v-list-item-content>
                                </v-list-item>
                                <div class="clearfix"></div>
                                <v-card-actions>
                                    <v-btn
                                        outlined
                                        rounded
                                        text
                                    >
                                        {{ taskGroup.tag }}
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                        <v-col cols="12">
                            <v-row no-gutters style="border-bottom: 1px solid gray">
                                <v-col cols="5"></v-col>
                                <v-col cols="2">Progress</v-col>
                                <v-col cols="2">Deadline</v-col>
                                <v-col cols="2">Assigned to</v-col>
                                <v-col cols="1"></v-col>
                            </v-row>
                            <v-row v-for="(task, index) in tasks" :key="index" style="border-bottom: 1px solid gray">
                                <v-col cols="5">{{ task.name }}</v-col>
                                <v-col cols="2">
                                    <v-chip
                                        class="text-uppercase"
                                        outlined small
                                        :color="task.color"
                                    >{{ task.progress }}</v-chip>
                                </v-col>
                                <v-col cols="2">{{ task.deadline }}</v-col>
                                <v-col cols="2">{{ task.assignedTo }}</v-col>
                                <v-col cols="1">
                                    <v-icon v-if="task.email">mdi-email</v-icon>
                                </v-col>
                            </v-row>
                        </v-col>
                    </v-row>
                </v-row>
            </v-col>
            <v-col cols="12" md="6" lg="4">
                <v-row no-gutters>
                    <v-col cols="12">
                        <h4 class="heading headline float-left">Log of Actions</h4>
                        <v-spacer></v-spacer>
                        <v-menu
                            bottom
                            left
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    dark
                                    icon
                                    v-bind="attrs"
                                    v-on="on"
                                    :color="themeBgColor"
                                    class="float-right"
                                >
                                    <v-icon>mdi-dots-vertical</v-icon>
                                </v-btn>
                            </template>

                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>Menu 1</v-list-item-title>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-title>Menu 2</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </v-col>
                </v-row>
                <v-row v-for="(action, index) in actions" :key="index" style="border-bottom: 1px solid gray">
                    <v-col cols="8" sm="12" md="12" lg="8">{{ action.name }}</v-col>
                    <v-col cols="4" sm="12" md="12" lg="4">{{ action.date }}</v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";

export default {
    name: 'incident_reporting_create',
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            tab: 0,
            actions: [
                {
                    name: 'Crisis team meeting scheduled',
                    date: '2021.12.15',
                },
                {
                    name: 'Crisis Reporter confirmed',
                    date: '2021.12.15',
                },
                {
                    name: 'Crisis Leader confirmed',
                    date: '2021.12.15',
                },
                {
                    name: 'Incident recorded',
                    date: '2021.12.15',
                },
            ],
            taskGroups: [
                {
                    name: 'Initial tasks',
                    total: 6,
                    completed: 5,
                    tag: 'Monitoring'
                },
                {
                    name: 'Activate backup location',
                    total: null,
                    completed: null,
                    tag: 'Handling'
                },
                {
                    name: 'Communication tasks',
                    total: null,
                    completed: null,
                    tag: 'Handling'
                },
                {
                    name: 'Activate backup location',
                    total: null,
                    completed: null,
                    tag: 'Handling'
                },
                {
                    name: 'Communication tasks',
                    total: null,
                    completed: null,
                    tag: 'Handling'
                },
            ],
            tasks: [
                {
                    name: 'Task 1',
                    progress: 'complete',
                    color: 'green',
                    deadline: '2021.12.15',
                    assignedTo: '',
                    email: true
                },
                {
                    name: 'Task 2',
                    progress: 'in progress',
                    color: 'primary',
                    deadline: null,
                    assignedTo: '',
                    email: ''
                },
                {
                    name: 'Task 3',
                    progress: 'not started',
                    color: 'gray',
                    deadline: '2021.12.15',
                    assignedTo: '',
                    email: ''
                },
                {
                    name: 'Task 4',
                    progress: 'in progress',
                    color: 'primary',
                    deadline: '2021.12.15',
                    assignedTo: '',
                    email: ''
                },
                {
                    name: 'Task 5',
                    progress: 'not started',
                    color: 'gray',
                    deadline: null,
                    assignedTo: '',
                    email: ''
                },
                {
                    name: 'Task 6',
                    progress: 'not started',
                    color: 'gray',
                    deadline: null,
                    assignedTo: '',
                    email: ''
                },
            ],
            menu: false,
            activePicker: false,
        }
    },
    mounted() {
        const that = this
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        save: function () {

        }
    }
}
</script>

<style scoped>
*,
>>> .v-input,
>>> label,
>>> .v-btn__content {
    font-size: 12px;
}
.heading {
    font-size: 16px;
}
.clearfix {
    clear: both;
}
</style>
