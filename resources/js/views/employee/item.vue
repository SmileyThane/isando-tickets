<template>


    <v-container fluid>
        <v-row>
            <v-snackbar
                :bottom="true"
                :right="true"
                v-model="snackbar"
                :color="actionColor"
            >
                {{ snackbarMessage }}
            </v-snackbar>
            <v-col class="col-md-6">
                <v-card class="elevation-6">
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Basic info</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateUser">Update
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-text-field
                                    color="green"
                                    label="Title before name"
                                    name="title_before_name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.title_before_name"
                                    :error-messages="errors.title_before_name"
                                    lazy-validation
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Title"
                                    name="title"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.title"
                                    :error-messages="errors.title"
                                    lazy-validation
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Name"
                                    name="name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.name"
                                    :error-messages="errors.name"
                                    lazy-validation
                                    required
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Surname"
                                    name="surname"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.surname"
                                    :error-messages="errors.surname"
                                    lazy-validation
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Email"
                                    name="email"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.email"
                                    required
                                    :error-messages="errors.email"
                                    lazy-validation
                                    class="col-md-12"
                                    readonly
                                    dense
                                ></v-text-field>
                            </v-row>
                        </v-form>
                        <v-checkbox
                            label="Give access to the system"
                            color="success"
                            v-model="userData.is_active"
                            @change="changeIsActive(userData)"
                            hide-details
                        ></v-checkbox>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col md="6">
                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Assigned companies</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="companies"
                            class="elevation-1"
                            item-key="id"
                            :footer-props="footerProps"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-btn
                                    color="grey"
                                    dark
                                    @click="showCompany(item)"
                                    fab
                                    x-small
                                >
                                    <v-icon>
                                        mdi-eye
                                    </v-icon>
                                </v-btn>

                                <v-btn @click="showRolesModal(item)"
                                       dark
                                       color="green"
                                       fab
                                       x-small>
                                    <v-icon
                                        small
                                    >
                                        mdi-pencil
                                    </v-icon>
                                </v-btn>
                            </template>
                        </v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    New сustomer assignment
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="md-12" class="pa-1">
                                                <v-autocomplete
                                                    v-model="employeeForm.client_id"
                                                    :items="customers"
                                                    :error-messages="employeeForm.id"
                                                    color="green"
                                                    hide-no-data
                                                    hide-selected
                                                    item-text="name"
                                                    item-value="id"
                                                    label="Client"
                                                    placeholder="Start typing to Search"
                                                ></v-autocomplete>
                                            </v-col>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                small
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
                <v-spacer>
                    &nbsp;
                </v-spacer>
            </v-col>
            <v-col offset-md="6">
                <v-card class="elevation-6">
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Additional info</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list
                                        dense
                                        subheader
                                    >
                                        <v-list-item-group color="green">
                                            <v-list-item
                                                v-for="(item, i) in userData.phones"
                                                :key="item.id"
                                            >
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.phone"></v-list-item-title>
                                                    <v-list-item-subtitle
                                                        v-text="item.type.name"></v-list-item-subtitle>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon
                                                        small
                                                        @click="deletePhone(item.id)"
                                                    >
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                            <v-list-item
                                                v-for="(item, i) in userData.addresses"
                                                :key="item.id"
                                            >
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="">{{item.address}}
                                                        {{item.address_line_2}} {{item.address_line_3}}
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle
                                                        v-text="item.type.name"></v-list-item-subtitle>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon
                                                        small
                                                        @click="deleteAddress(item.id)"
                                                    >
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                </v-col>
                                <v-col class="col-md-12">
                                    <v-expansion-panels>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                New phone
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="phoneForm.phone"
                                                                label="Phone"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-select
                                                                color="green"
                                                                item-color="green"
                                                                item-text="name"
                                                                item-value="id"
                                                                v-model="phoneForm.phone_type"
                                                                :items="phoneTypes"
                                                                label="Type"
                                                                dense
                                                            ></v-select>
                                                        </v-col>
                                                        <v-btn
                                                            dark
                                                            fab
                                                            right
                                                            bottom
                                                            small
                                                            color="green"
                                                            @click="addPhone"
                                                        >
                                                            <v-icon>mdi-plus</v-icon>
                                                        </v-btn>
                                                    </div>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                New address
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-12" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="addressForm.address.address"
                                                                label="Address"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="addressForm.address.address_line_2"
                                                                label="Address line 2"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="addressForm.address.postal_code"
                                                                label="Postal code"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-3" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="addressForm.address.city"
                                                                label="City"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-3" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="addressForm.address.country"
                                                                label="Country"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-select
                                                                color="green"
                                                                item-color="green"
                                                                item-text="name"
                                                                item-value="id"
                                                                v-model="addressForm.address_type"
                                                                :items="addressTypes"
                                                                label="Type"
                                                                dense
                                                            ></v-select>
                                                        </v-col>
                                                        <v-btn
                                                            dark
                                                            fab
                                                            right
                                                            bottom
                                                            small
                                                            color="green"
                                                            @click="addAddress"
                                                        >
                                                            <v-icon>mdi-plus</v-icon>
                                                        </v-btn>
                                                    </div>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                    </v-expansion-panels>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-dialog v-model="rolesDialog" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">Update info for {{singleUserForm.user.name}}</span>
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
                                v-model="singleUserForm.role_ids"
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
                phoneHeaders: [
                    {text: 'Phone', sortable: false, value: 'phone'},
                    {text: 'Type', value: 'type.name'},
                    {text: '', value: 'actions', sortable: false},

                ],
                addressHeaders: [
                    {text: '', value: 'data-table-expand'},
                    {text: 'Address', value: 'address'},
                    {text: 'Type', value: 'type.name'},
                    {text: '', value: 'actions', sortable: false},

                ],
                headers: [

                    {text: 'name', value: 'clients.name'},
                    // {text: 'email', value: 'user_data.email'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                footerProps: {
                    itemsPerPage: 10,
                    disableItemsPerPage: true,
                },
                companies: [],
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                errors: [],
                enableToEdit: false,
                userData: {
                    id: '',
                    title: '',
                    title_before_name: '',
                    surname: '',
                    country: '',
                    anredeform: '',
                    lang: '',
                    name: "",
                    email: "",
                    password: "",
                    phones: [],
                    addresses: []
                },
                singleUserForm: {
                    user: '',
                    role_ids: [],
                    company_user_id: ''
                },
                roles: [],
                rolesDialog: false,
                employeeForm: {
                    client_id: null,
                    company_user_id: null
                },
                phoneForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    phone: '',
                    phone_type: ''
                },
                addressForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    address: {
                        address: '',
                        address_line_2: '',
                        address_line_3: '',
                        city: '',
                        country: ''
                    },
                    address_type: ''
                },
                phoneTypes: [],
                addressTypes: [],
                isCustomersLoading: false,
                customers: []
            }
        },
        mounted() {
            this.getUser();
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getRoles()
            this.getClients()
            // if (localStorage.getItem('auth_token')) {
            //     this.$router.push('tickets')
            // }
        },
        methods: {
            getUser() {
                axios.get(`/api/user/find/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.userData = response.data
                        this.companies = this.userData.employee.assigned_to_clients.length > 0 ? this.userData.employee.assigned_to_clients : this.userData.employee.companies
                        console.log(this.companies);
                        this.employeeForm.company_user_id = this.userData.employee.id
                    }
                });
            },
            updateUser(e) {
                e.preventDefault()
                this.snackbar = false;
                axios.post('/api/user', this.userData).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true
                        this.enableToEdit = false
                    } else {
                        this.errors = response.error
                    }
                });
            },
            getPhoneTypes() {
                axios.get(`/api/phone_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.phoneTypes = response.data
                    }
                });
            },
            getAddressTypes() {
                axios.get(`/api/address_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.addressTypes = response.data
                    }
                });
            },
            addPhone() {
                this.phoneForm.entity_id = this.userData.id
                axios.post('/api/phone', this.phoneForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
                        this.snackbarMessage = 'Phone update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = 'Phone update error'
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
            deletePhone(id) {
                axios.delete(`/api/phone/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
                        this.phoneForm.phone = ''
                        this.snackbarMessage = 'Phone delete successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = 'Phone delete error'
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
            addAddress() {
                this.addressForm.entity_id = this.userData.id
                if (this.addressForm.address.city !== '' && this.addressForm.address.country !== '') {
                    this.addressForm.address.address_line_3 = `${this.addressForm.address.city}, ${this.addressForm.address.country}`
                } else {
                    this.addressForm.address.address_line_3 = `${this.addressForm.address.city}${this.addressForm.address.country}`
                }
                if (this.addressForm.address.postal_code) {
                    this.addressForm.address.address += ` ${this.addressForm.address.postal_code} `
                }
                axios.post('/api/address', this.addressForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
                        this.snackbarMessage = 'Address update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = 'Address update error'
                        this.actionColor = 'error'
                        this.snackbar = true;

                    }
                });
            },
            deleteAddress(id) {
                axios.delete(`/api/address/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
                        this.error.push('Delete successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
                        this.snackbarMessage = 'Address delete error'
                        this.actionColor = 'error'
                        this.snackbar = true;

                    }
                });
            },
            showCompany(item) {
                this.$router.push(`/customer/${item.clients.id}`)
            },
            changeIsActive(item) {
                let request = {}
                request.user_id = item.id
                request.is_active = item.is_active
                axios.post(`/api/user/is_active`, request).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
                        this.snackbarMessage = item.is_active ? 'Contact activated' : 'Contact deactivated'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                    }
                });
            },
            showRolesModal(item) {
                this.rolesDialog = true
                this.singleUserForm.user = this.userData
                this.singleUserForm.role_ids = []
                this.singleUserForm.company_user_id = item.company_user_id
                this.userData.employee.roles.forEach(role => {
                    this.singleUserForm.role_ids.push(role.id)
                })
                // console.log(item);
            },
            getRoles() {
                this.roles = []
                axios.get('/api/roles').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.roles.push(response.data[response.data.length - 1])
                    } else {
                        console.log('error')
                    }

                });
            },
            updateRole() {
                axios.patch(`/api/roles`, this.singleUserForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.rolesDialog = false
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        console.log('error')
                    }

                });
            },
            getClients() {
                this.isCustomersLoading = true
                axios.get(`/api/client`)
                    .then(
                        response => {
                            response = response.data
                            this.customers = response.data.data
                            this.isCustomersLoading = false
                        });
            },
            addEmployee() {
                axios.post(`/api/client/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
                        this.snackbarMessage = 'Customer was attached successfully'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        console.log('error')
                    }

                });
            },
        }
    }
</script>
