<template>
    <v-container>
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
                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{ langMap.team.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateTeam">
                            {{ langMap.main.update }}
                        </v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                color="green"
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
                                color="green"
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
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{ langMap.team.members }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="team.employees"
                            class="elevation-1"
                            :footer-props="footerProps"
                        ></v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.team.new_member }}
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="md-12">
                                                <v-autocomplete
                                                    color="green"
                                                    item-color="green"
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
                                                color="green"
                                                @click="addEmployee"
                                            >
                                                <v-icon>mdi-plus</v-icon>
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
    </v-container>
</template>


<script>
    export default {

        data() {
            return {
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                enableToEdit: false,
                langMap: this.$store.state.lang.lang_map,
                footerProps: {
                    itemsPerPage: 10,
                    disableItemsPerPage: true,
                    itemsPerPageText: this.$store.state.lang.lang_map.main.items_per_page
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
                    {text: `${this.$store.state.lang.lang_map.main.roles}`, value: 'employee.role_names'},
                    {text: `${this.$store.state.lang.lang_map.main.actions}`, value: ''},
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
            this.getTeam()
            this.employeeForm.team_id = this.$route.params.id
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
                    } else {
                        console.log('error')
                    }

                });
            },
            getCompanies() {
                console.log(this.team);
                axios.get(`/api/company/${this.team.team_owner_id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.companies = response.data
                        console.log(this.companies);
                    } else {
                        console.log('error')
                    }

                });
            },
            addEmployee() {
                // console.log(this.employeeForm);
                axios.post(`/api/team/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getTeam()
                    } else {
                        console.log('error')
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
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        console.log('error')
                    }

                });
            }
        }
    }
</script>
