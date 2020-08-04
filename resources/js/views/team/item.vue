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
                        <v-toolbar-title>Basic info</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateTeam">Save</v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                color="green"
                                label="Name"
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
                                label="Description"
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
<!--                    <v-card-actions>-->
<!--                        <v-spacer></v-spacer>-->
<!--                        <v-btn color="green" style="color: white;" @click="updateTeam">Save</v-btn>-->
<!--                    </v-card-actions>-->
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
                        <v-toolbar-title>Team members</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="team.employees"
                            :items-per-page="25"
                            class="elevation-1"
                        ></v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    New Team Member
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
                                                    label="To"
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
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'employee.id',
                    },
                    {text: 'name', value: 'employee.user_data.name'},
                    {text: 'email', value: 'employee.user_data.email'},
                    {text: 'roles', value: 'employee.role_names'},
                    {text: 'Actions', value: ''},
                ],
                team: {
                    team_name: '',
                    team_description: '',
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
            this.getCompanies()
            this.employeeForm.team_id = this.$route.params.id
            console.log(this.employeeForm.team_id);
        },
        methods: {
            getTeam() {
                axios.get(`/api/team/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.team = response.data
                        this.team.team_name = response.data.name
                        this.team.team_description = response.data.description
                    } else {
                        console.log('error')
                    }

                });
            },
            getCompanies() {
                axios.get('/api/company').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.companies = response.data.data[0]
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
