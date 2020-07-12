<template>
    <v-container>

        <div class="row">
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Company information</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                color="green"
                                label="Name"
                                name="name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="company.name"
                                required
                            ></v-text-field>
                            <v-text-field
                                color="green"
                                label="Description"
                                name="description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="company.description"
                                required
                            ></v-text-field>

                            <v-text-field
                                color="green"
                                label="Company number"
                                name="company_number"
                                prepend-icon="mdi-message-alert"
                                type="text"
                                v-model="company.company_number"
                                required
                            ></v-text-field>
                            <v-menu
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        color="green"
                                        v-model="company.registration_date"
                                        label="Date of registration"
                                        name="registration_date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    color="green"
                                    v-model="company.registration_date"
                                ></v-date-picker>
                            </v-menu>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="green" style="color: white;" @click="updateCompany">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">

                    <v-toolbar
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Company contacts</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <div class="card-text">
                        <v-data-table
                            :headers="headers"
                            :items="company.employees"
                            :items-per-page="25"
                            class="elevation-1"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="showRolesModal(item)"
                                >
                                    mdi-account-settings-outline
                                </v-icon>
                                <v-icon
                                    small
                                    @click="removeEmployee(item)"
                                >
                                    mdi-delete
                                </v-icon>

                            </template>

                        </v-data-table>
                    </div>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header>
                            Add New Contact
                            <template v-slot:actions>
                                <v-icon color="submit">mdi-plus</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-form>
                                <div class="row">
                                    <div class="col-md-4">
                                        <v-text-field
                                            color="green"
                                            label="Name"
                                            name="name"
                                            type="text"
                                            v-model="employeeForm.name"
                                            required
                                        ></v-text-field>
                                    </div>
                                    <div class="col-md-4">
                                        <v-text-field
                                            color="green"
                                            label="Email"
                                            name="email"
                                            type="text"
                                            v-model="employeeForm.email"
                                            required
                                        ></v-text-field>
                                    </div>
                                    <div class="col-md-4">
                                        <v-select
                                            label="Role"
                                            color="green"
                                            item-color="green"
                                            item-text="name"
                                            item-value="id"
                                            :items="roles"
                                            v-model="employeeForm.role_id"
                                        />
                                    </div>
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

            </div>
        </div>
        <v-row justify="center">
            <v-dialog v-model="rolesDialog" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">Update role for {{newRoleForm.name}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-select
                                label="Role"
                                color="green"
                                item-color="green"
                                item-text="name"
                                item-value="id"
                                :items="roles"
                                v-model="newRoleForm.role_ids"
                                multiple
                            />
                        </v-container>
<!--                        <small>*indicates required field</small>-->
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text @click="rolesDialog = false">Close</v-btn>
                        <v-btn color="green" text @click="updateRole">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </v-container>
</template>


<script>
    export default {

        data() {
            return {
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'id',
                    },
                    {text: 'name', value: 'user_data.name'},
                    {text: 'email', value: 'user_data.email'},
                    {text: 'roles', value: 'role_names'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                company: {
                    name: '',
                    company_number: '',
                    description: '',
                    registration_date: '',
                    employees: [
                        {
                            user_id: '',
                            company_id: '',
                            roles: [],
                            user_data: ''
                        }
                    ]
                },
                employeeForm: {
                    name: '',
                    email: '',
                    role_id: '',
                    company_id: ''
                },
                roles: [
                    {
                        id: '',
                        name: ''
                    }
                ],
                rolesDialog: false,
                newRoleForm:{
                    name:'',
                    role_ids: [],
                    company_user_id:''
                }

            }
        },
        mounted() {
            this.getCompany()
            this.getRoles()
            this.employeeForm.company_id = this.$route.params.id
        },
        methods: {
            getCompany() {
                axios.get(`/api/company/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.company = response.data
                    } else {
                        console.log('error')
                    }

                });
            },
            getRoles() {
                axios.get('/api/roles').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.roles = response.data
                    } else {
                        console.log('error')
                    }

                });
            },
            addEmployee() {
                axios.post(`/api/company/${this.$route.params.id}/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                    } else {
                        console.log('error')
                    }

                });
            },
            updateCompany() {
                axios.post(`/api/company/${this.$route.params.id}`, this.company).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.company = response.data
                    } else {
                        console.log('error')
                    }

                });
            },
            showRolesModal(item) {
                this.rolesDialog = true
                this.newRoleForm.name = item.user_data.name
                this.newRoleForm.role_ids = []
                this.newRoleForm.company_user_id = item.id
                item.roles.forEach(role => {
                    this.newRoleForm.role_ids.push(role.id)
                })
                // console.log(item);
            },
            updateRole() {
                axios.patch(`/api/roles`, this.newRoleForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.rolesDialog = false
                    } else {
                        console.log('error')
                    }

                });
            },
            removeEmployee(item) {
                axios.delete(`/api/company/employee/${item.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.rolesDialog = false
                    } else {
                        console.log('error')
                    }

                });
            }
        }
    }
</script>
