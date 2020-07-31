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
                        <v-toolbar-title>Basic info</v-toolbar-title>
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
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12">
                    <v-toolbar
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
                                        :items="company.phones"
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
                                        :items="company.addresses"
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
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Company contacts</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="company.employees"
                            :items-per-page="25"
                            class="elevation-1"
                            item-key="id"
                            show-expand
                        >
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <p></p>
                                    <p><strong>E-mail:</strong></p>
                                    <p>{{ item.user_data.email }}</p>
                                    <p><strong>Phone(s):</strong></p>
                                    <p v-for="phoneItem in item.user_data.phones">{{ phoneItem.phone }} ({{
                                        phoneItem.type.name }})</p>
                                    <p><strong>Lang:</strong></p>
                                    <p>{{ item.user_data.lang }}</p>
                                    <p><strong>Address(es):</strong></p>
                                    <p v-for="addressItem in item.user_data.addresses">{{ addressItem.address }} {{
                                        addressItem.address_line_2 }} {{ addressItem.address_line_3 }} ({{
                                        addressItem.type.name }})</p>
                                </td>
                            </template>
                            <template v-slot:item.user_data="{ item }">
                                <div class="justify-center" v-if="item.user_data">{{ item.user_data.name }} {{
                                    item.user_data.surname }}
                                </div>
                            </template>
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
                            :items="company.socials"
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
        <v-row justify="center">
            <v-dialog v-model="rolesDialog" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">Update role for {{newRoleForm.user.name}}</span>
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
                                :disabled="!checkRoleByIds([1,2,3])"
                                v-model="newRoleForm.role_ids"
                                multiple
                            />
                            <v-checkbox
                                label="Give access to the system"
                                color="success"
                                :disabled="!checkRoleByIds([1,2,3])"
                                v-model="newRoleForm.user.is_active"
                                @change="changeIsActive(newRoleForm.user)"
                                hide-details
                            ></v-checkbox>
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
                    {text: 'roles', value: 'role_names'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                company: {
                    name: '',
                    company_number: '',
                    description: '',
                    registration_date: '',
                    phones: [],
                    addresses: [],
                    socials: [],
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
                    company_id: '',
                    is_active: false
                },
                roles: [
                    {
                        id: '',
                        name: ''
                    }
                ],
                rolesDialog: false,
                newRoleForm: {
                    user: '',
                    role_ids: [],
                    company_user_id: ''
                },
                phoneForm: {
                    entity_id: '',
                    entity_type: 'App\\Company',
                    phone: '',
                    phone_type: ''
                },
                addressForm: {
                    entity_id: '',
                    entity_type: 'App\\Company',
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
                    entity_type: 'App\\Company',
                    social_link: '',
                    social_type: ''
                },
                phoneTypes: [],
                addressTypes: [],
                socialTypes: []
            }
        },
        mounted() {
            this.getCompany();
            this.getRoles();
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
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
                this.company.phones = null;
                this.company.addresses = null;
                this.company.socials = null;
                this.company.employees = null;
                this.company.clients = null;
                this.company.teams = null;
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
                this.newRoleForm.user = item.user_data
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
                this.phoneForm.entity_id = this.company.id
                axios.post('/api/phone', this.phoneForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
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
                        this.getCompany()
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
                this.socialForm.entity_id = this.company.id
                axios.post('/api/social', this.socialForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
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
                        this.getCompany()
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
                this.addressForm.entity_id = this.company.id
                if (this.addressForm.address.city !== '' && this.addressForm.address.country !== '') {
                    this.addressForm.address.address_line_3 = `${this.addressForm.address.city}, ${this.addressForm.address.country}`
                } else {
                    this.addressForm.address.address_line_3 = `${this.addressForm.address.city}${this.addressForm.address.country}`
                }
                axios.post('/api/address', this.addressForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
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
                        this.getCompany()
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
            changeIsActive(item) {
                let request = {}
                request.user_id = item.user_data.id
                request.is_active = item.user_data.is_active
                axios.post(`/api/user/is_active`, request).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.error.push('Updated successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
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
