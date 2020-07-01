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
                        <v-toolbar-title>Customer information</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                color="green"
                                label="Name"
                                name="client_name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="client.client_name"
                                required
                            ></v-text-field>
                            <v-text-field
                                color="green"
                                label="Description"
                                name="client_description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="client.client_description"
                                required
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="green" style="color: white;" @click="updateClient">Save</v-btn>
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
                        <v-toolbar-title>Customer contacts</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <div class="card-text">
                        <v-data-table
                            :headers="headers"
                            :items="client.employees"
                            :items-per-page="25"
                            class="elevation-1"
                        ></v-data-table>
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
                                    <div class="col-md-6">
                                        <v-text-field
                                            color="green"
                                            label="Name"
                                            name="name"
                                            type="text"
                                            v-model="employeeForm.name"
                                            required
                                        ></v-text-field>
                                    </div>
                                    <div class="col-md-6">
                                        <v-text-field
                                            color="green"
                                            label="Email"
                                            name="email"
                                            type="text"
                                            v-model="employeeForm.email"
                                            required
                                        ></v-text-field>
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
                        value: 'employee.id',
                    },
                    {text: 'email', value: 'employee.user_data.email'},
                    {text: 'roles', value: 'employee.role_names'},
                    {text: 'Actions', value: ''},
                ],
                client: {
                    client_name: '',
                    client_description: '',
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
                    name: '',
                    email: '',
                    client_id: ''
                },
                roles: [
                    {
                        id: '',
                        name: ''
                    }
                ]

            }
        },
        mounted() {
            this.getClient()
            this.getRoles()
            this.employeeForm.client_id = this.$route.params.id
        },
        methods: {
            getClient() {
                axios.get(`/api/client/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.client = response.data
                        this.client.client_name = response.data.name
                        this.client.client_description = response.data.description
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
                axios.post(`/api/client/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                    } else {
                        console.log('error')
                    }

                });
            },
            updateClient() {
                axios.patch(`/api/client/${this.$route.params.id}`, this.client).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.client.client_name = response.data.name
                        this.client.client_description = response.data.description
                    } else {
                        console.log('error')
                    }

                });
            }
        }
    }
</script>
