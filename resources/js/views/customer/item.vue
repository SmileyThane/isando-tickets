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
                        color="green"
                        dense
                        dark
                        flat
                    >
                        <v-toolbar-title>Basic info</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateClient">Save
                        </v-btn>

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
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                            <v-text-field
                                color="green"
                                label="Description"
                                name="client_description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="client.client_description"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <!--                    <v-card-actions>-->
                    <!--                        <v-spacer></v-spacer>-->
                    <!--                        <v-btn color="green" style="color: white;" @click="updateClient">Save</v-btn>-->
                    <!--                    </v-card-actions>-->
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12">
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
                                <v-col class="col-md-6">
                                    <v-data-table
                                        :headers="phoneHeaders"
                                        :items="client.phones"
                                        hide-default-footer
                                        class="elevation-1"
                                    >
                                        <template v-slot:item.actions="{ item }">
                                            <v-icon
                                                small
                                                @click="deletePhone(item.id)"
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </template>
                                    </v-data-table>
                                </v-col>
                                <v-col class="col-md-6">
                                    <v-data-table
                                        :headers="addressHeaders"
                                        :items="client.addresses"
                                        hide-default-footer
                                        show-expand
                                        class="elevation-1"
                                    >
                                        <template v-slot:expanded-item="{ headers, item }">
                                            <td :colspan="headers.length">
                                                <p></p>
                                                <p><strong>Address line 2:</strong> {{ item.address_line_2 }}
                                                </p>
                                                <p><strong>Address line 3:</strong> {{ item.address_line_3 }}
                                                </p>
                                            </td>
                                        </template>
                                        <template v-slot:item.actions="{ item }">
                                            <v-icon
                                                small
                                                @click="deleteAddress(item.id)"
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </template>
                                    </v-data-table>
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
                                                        <v-col cols="md-6" class="pa-1">
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
            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Customer contacts</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="client.employees"
                            :items-per-page="25"
                            class="elevation-1"
                            item-key="id"
                            show-expand
                        >
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <p></p>
                                    <p><strong>E-mail:</strong></p>
                                    <p>{{ item.employee.user_data.email }}</p>
                                    <p><strong>Phone(s):</strong></p>
                                    <p v-for="phoneItem in item.employee.user_data.phones">{{ phoneItem.phone }} ({{
                                        phoneItem.type.name }})</p>
<!--                                    <p><strong>Lang:</strong></p>-->
<!--                                    <p>{{ item.employee.user_data.lang }}</p>-->
                                    <p><strong>Address(es):</strong></p>
                                    <p v-for="addressItem in item.employee.user_data.addresses">{{ addressItem.address
                                        }} {{ addressItem.address_line_2 }} {{ addressItem.address_line_3 }} ({{
                                        addressItem.type.name }})</p>
                                </td>
                            </template>
                            <template v-slot:item.user_data="{ item }">
                                <div class="justify-center" v-if="item.employee.user_data">{{
                                    item.employee.user_data.name }} {{
                                    item.employee.user_data.surname }}
                                </div>
                            </template>
                        </v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    New Contact
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
                                                <v-checkbox
                                                    label="Give access to the system"
                                                    color="success"
                                                    :disabled="!checkRoleByIds([1,2,3])"
                                                    v-model="employeeForm.is_active"
                                                    hide-details
                                                ></v-checkbox>
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
                    </v-card-text>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Social info</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="socialHeaders"
                            :items="client.socials"
                            hide-default-footer
                            hide-default-header
                            class="elevation-1"
                        >
                            <template v-slot:item.social_link="{ item }">
                                <v-list-item link :href="item.social_link">{{ item.social_link }}</v-list-item>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <v-icon
                                    small
                                    @click="deleteSocial(item.id)"
                                >
                                    mdi-delete
                                </v-icon>
                            </template>
                        </v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    New social item
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
                                                    v-model="socialForm.social_link"
                                                    label="Social link"
                                                    dense
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" class="pa-1">
                                                <v-select
                                                    color="green"
                                                    item-color="green"
                                                    item-text="name"
                                                    item-value="id"
                                                    v-model="socialForm.social_type"
                                                    :items="socialTypes"
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
                                                @click="addSocial"
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
                phoneHeaders: [
                    {text: 'Phone', sortable: false, value: 'phone'},
                    {text: 'Type', value: 'type.name'},
                    {text: '', value: 'actions', sortable: false},

                ],
                socialHeaders: [
                    {text: 'Type', value: 'type.name'},
                    {text: 'Link', sortable: false, value: 'social_link'},
                    {text: '', value: 'actions', sortable: false},

                ],
                addressHeaders: [
                    {text: '', value: 'data-table-expand'},
                    {text: 'Address', value: 'address'},
                    {text: 'Type', value: 'type.name'},
                    {text: '', value: 'actions', sortable: false},

                ],
                headers: [
                    {text: '', value: 'data-table-expand'},
                    // {
                    //     text: 'ID',
                    //     align: 'start',
                    //     sortable: false,
                    //     value: 'id',
                    // },
                    {text: 'name', value: 'user_data'},
                    // {text: 'email', value: 'user_data.email'},
                    {text: 'roles', value: 'employee.role_names'},
                    {text: 'Actions', value: ''},
                ],
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                enableToEdit: false,
                client: {
                    client_name: '',
                    client_description: '',
                    phones: [],
                    addresses: [],
                    socials: [],
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
                    client_id: '',
                    is_active: false
                },
                roles: [
                    {
                        id: '',
                        name: ''
                    }
                ],
                phoneForm: {
                    entity_id: '',
                    entity_type: 'App\\Client',
                    phone: '',
                    phone_type: ''
                },
                addressForm: {
                    entity_id: '',
                    entity_type: 'App\\Client',
                    address: {
                        address: '',
                        address_line_2: '',
                        address_line_3: '',
                        city: '',
                        country: ''
                    },
                    address_type: ''
                },
                socialForm: {
                    entity_id: '',
                    entity_type: 'App\\Client',
                    social_link: '',
                    social_type: ''
                },
                phoneTypes: [],
                addressTypes: [],
                socialTypes: []

            }
        },
        mounted() {
            this.getClient()
            this.getRoles()
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
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
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.enableToEdit = false
                    } else {
                        console.log('error')
                    }

                });
            },
            getPhoneTypes() {
                axios.get(`/api/phone_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.phoneTypes = response.data
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;
                    }
                });
            },
            getSocialTypes() {
                axios.get(`/api/social_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.socialTypes = response.data
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;
                    }
                });
            },
            getAddressTypes() {
                axios.get(`/api/address_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.addressTypes = response.data
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;
                    }
                });
            },
            addPhone() {
                this.phoneForm.entity_id = this.client.id
                axios.post('/api/phone', this.phoneForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.error.push('Update successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            deletePhone(id) {
                axios.delete(`/api/phone/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.phoneForm.phone = ''
                        this.error.push('Delete successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            addSocial() {
                this.socialForm.entity_id = this.client.id
                axios.post('/api/social', this.socialForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.error.push('Update successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            deleteSocial(id) {
                axios.delete(`/api/social/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.socialForm.phone = ''
                        this.error.push('Delete successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            addAddress() {
                this.addressForm.entity_id = this.client.id
                if (this.addressForm.address.city !== '' && this.addressForm.address.country !== '') {
                    this.addressForm.address.address_line_3 = `${this.addressForm.address.city}, ${this.addressForm.address.country}`
                } else {
                    this.addressForm.address.address_line_3 = `${this.addressForm.address.city}${this.addressForm.address.country}`
                }
                axios.post('/api/address', this.addressForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.error.push('Update successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            deleteAddress(id) {
                axios.delete(`/api/address/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.error.push('Delete successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            checkRoleByIds(ids) {
                let roleExists = false;
                ids.forEach(id => {
                    if (roleExists === false) {
                        roleExists = this.$store.state.roles.includes(id)
                    }
                });
                return roleExists
            }
        }
    }
</script>
