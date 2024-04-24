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
        <div class="row">
            <div class="col-md-6">
                <v-card class="elevation-12 without-bottom">
                    <v-toolbar
                        dense
                        :color="themeBgColor"
                        dark
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.team.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" :color="themeFgColor" @click="enableToEdit = true">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px"
                               @click="cancelUpdateTeam">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateTeam">
                            {{ langMap.main.update }}
                        </v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                :color="themeBgColor"
                                :label="langMap.main.name"
                                name="team_name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="team.team_name"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                            <v-text-field
                                :color="themeBgColor"
                                :label="langMap.main.description"
                                name="team_description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="team.team_description"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                </v-card>
            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        :color="themeBgColor"
                        dark
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.team.members }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="team.employees"
                            class="elevation-1"
                            :options.sync="options"
                            :footer-props="footerProps"
                            @update:options="updateItemsPerPage"
                        >
                            <template v-slot:item.is_manager="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn @click="toggleAsManager(item)" icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                                v-if="item.is_manager"
                                            >
                                                mdi-flag-variant
                                            </v-icon>
                                            <v-icon
                                                small
                                                v-else
                                            >
                                                mdi-flag-variant-outline
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span v-if="item.is_manager">{{ langMap.main.unmark_as_manager }}</span>
                                    <span v-else>{{ langMap.main.mark_as_manager }}</span>
                                </v-tooltip>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn @click="removeEmployeeProcess(item)" icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.company.delete_contact }}</span>
                                </v-tooltip>
                            </template>
                        </v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.team.new_member }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="md-12">
                                                <v-autocomplete
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    item-text="user_data.email"
                                                    item-value="id"
                                                    v-model="employeeForm.company_user_id"
                                                    :items="companies.employees"
                                                    :label="langMap.main.email"
                                                ></v-autocomplete>
                                            </v-col>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                :color="themeBgColor"
                                                @click="addEmployee"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                    mdi-plus
                                                </v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>
            </div>
        </div>
        <template>
            <v-dialog v-model="removeEmployeeDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.main.delete_selected }}?
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeEmployeeDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="removeEmployee(selectedEmployeeId)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
    </v-container>
</template>


<script>
import EventBus from "../../components/EventBus";

export default {

    data() {
        return {
            snackbar: false,
            actionColor: '',
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            enableToEdit: false,
            langMap: this.$store.state.lang.lang_map,
            options: {
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10,
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [
                    {
                        'text': 10,
                        'value': 10,
                    },
                    {
                        'text': 20,
                        'value': 20,
                    },
                    {
                        'text': 50,
                        'value': 50,
                    },
                    {
                        'text': 100,
                        'value': 100,
                    },
                    {
                        'text': this.$store.state.lang.lang_map.sidebar.all,
                        'value': 10000,
                    }
                ],
                },
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'employee.id',
                    },
                    {text: `${this.$store.state.lang.lang_map.company.user}`, value: 'employee.user_data.name'},
                    {text: `${this.$store.state.lang.lang_map.main.email}`, value: 'employee.user_data.email'},
                    {text: `${this.$store.state.lang.lang_map.main.team_manager}`, value: 'is_manager'},
                    {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions'},
                ],
                team: {
                    team_name: '',
                    team_description: '',
                    team_owner_id: '',
                    employees: [
                        {
                            employee: {
                                user_id: '',
                                company_id: '',
                                roles: [],
                                user_data: ''

                            }
                        }
                    ]
                },
                removeEmployeeDialog: false,
                selectedEmployeeId: null,
                employeeForm: {
                    company_user_id: '',
                    team_id: ''
                },
                companies: [
                    {
                        id: '',
                        name: '',
                        employees: [
                            {
                                user_data:
                                    {
                                        email: '',
                                        name: ''
                                    }
                            }
                        ]
                    }
                ]
            }
        },
        mounted() {
            this.getTeam();
            this.employeeForm.team_id = this.$route.params.id;
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeBgColor = color;
            });
        },
        methods: {
            getTeam() {
                axios.get(`/api/team/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.team = response.data
                        this.team.team_name = response.data.name
                        this.team.team_description = response.data.description
                        this.getCompanies()
                    }

                });
            },
            getCompanies() {
                axios.get(`/api/company/${this.team.team_owner_id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.companies = response.data
                    }

                });
            },
            addEmployee() {
                axios.post(`/api/team/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getTeam()
                    }

                });
            },
            removeEmployeeProcess(item) {
                this.selectedEmployeeId = item.id
                this.removeEmployeeDialog = true
            },
            removeEmployee(id) {
                axios.delete(`/api/team/employee/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getTeam()
                        this.rolesDialog = false
                        this.snackbarMessage = this.langMap.company.employee_deleted
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.removeEmployeeDialog = false
                    }

                });
            },
            updateTeam() {
                axios.patch(`/api/team/${this.$route.params.id}`, this.team).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.team.team_name = response.data.name
                        this.team.team_description = response.data.description
                        this.enableToEdit = false
                        this.snackbarMessage = this.langMap.main.update_successful
                        this.actionColor = 'success'
                        this.snackbar = true;
                    }

                });
            },
            toggleAsManager(item) {
                axios.post(`/api/team/employee/manager`, {
                    company_user_id: item.company_user_id,
                    team_id: item.team_id
                }).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getTeam()
                    }

                });
            },
            cancelUpdateTeam() {
                this.getTeam();
                this.enableToEdit = false;
            },
        updateItemsPerPage(options) {
            localStorage.itemsPerPage = options.itemsPerPage;
        }
        }
    }
</script>
