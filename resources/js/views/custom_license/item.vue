<template>
    <v-container fluid>
        <v-snackbar
            v-model="snackbar"
            :bottom="true"
            :color="actionColor"
            :right="true"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <div class="row">
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.company.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" :color="themeFgColor" @click="enableToEdit = true">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px"
                               @click="enableToEdit = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="clientUpdate">
                            {{ langMap.main.update }}
                        </v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                v-model="client.client_name"
                                :color="themeBgColor"
                                :disabled="!checkRoleByIds([1,2,3])"
                                :label="langMap.company.name"
                                :readonly="!enableToEdit"
                                dense
                                name="client_name"
                                prepend-icon="mdi-rename-box"
                                required
                                type="text"
                            ></v-text-field>
                            <v-text-field
                                v-model="client.client_description"
                                :color="themeBgColor"
                                :label="langMap.company.description"
                                :readonly="!enableToEdit"
                                dense
                                name="client_description"
                                prepend-icon="mdi-comment-text"
                                required
                                type="text"
                            ></v-text-field>
                        </v-form>
                        <v-checkbox
                            v-model="client.is_active"
                            :label="this.$store.state.lang.lang_map.customer.active"
                            color="success"
                            hide-details
                            @change="changeIsActiveClient(client)"
                        ></v-checkbox>
                        <v-divider></v-divider>
                        <br>
                        <label>Connection links</label>
                        <v-text-field
                            v-for="(id, key) in client.connection_links"
                            :key="key"
                            v-model="client.connection_links[key]"
                            :color="themeBgColor"
                            :label="langMap.company.link"
                            :readonly="!enableToEdit"
                            dense
                            prepend-icon="mdi-link"
                            required
                            type="text"
                        >
                            <template v-slot:append>
                                <v-icon
                                    color="red"
                                    @click="removeConnectionLink(key)"
                                >
                                    mdi-cancel
                                </v-icon>
                            </template>
                        </v-text-field>
                        <v-btn
                            v-if="enableToEdit"
                            color="green"
                            outlined
                            @click="addConnectionLink"
                        >
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </v-card-text>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ 'License Users' }}</v-toolbar-title>
                    </v-toolbar>
                    <div class="card-body">
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="licenseUserHeaders"
                            :items="licenseUsers"
                            :loading-text="langMap.main.loading"
                            :options.sync="options"
                            class="elevation-1"
                            hide-default-footer
                            @click:row="showAssignDialog"
                        >
                            <template v-slot:item.lastActivationChange="{ item }">
                                {{
                                    item.lastActivationChange > 0 ?
                                        moment(item.lastActivationChange).format('DD-MM-YYYY') : ''
                                }}
                            </template>
                            <template v-slot:item.trialExpirationAtString="{ item }">
                                {{
                                    moment(item.trialExpirationAtString).format('DD-MM-YYYY')
                                }}
                            </template>
                            <template v-slot:item.licensed="{ item }">
                                <v-btn
                                    outlined
                                    @click.stop.prevent="manageLicenseUsers(item.id, item.licensed)"
                                >
                                    <v-icon
                                        :color="item.licensed ? 'green' :'red'"
                                    >
                                        {{ item.licensed ? 'mdi-check-circle-outline' : 'mdi-cancel' }}
                                    </v-icon>
                                </v-btn>
                            </template>
                        </v-data-table>
                    </div>
                </v-card>

            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">
                            <span class="text-left">{{ langMap.sidebar.custom_license }} </span>

                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEditLicense" @click="enableToEditLicense = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEditLicense" color="white" style="color: black; margin-right: 10px"
                               @click="enableToEditLicense = !enableToEditLicense; getLicense();">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEditLicense" color="white" style="color: black;" @click="updateLicense()">
                            {{ langMap.main.update }}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-row>
                            <v-col md="6">
                                <v-list
                                    flat
                                >
                                    <v-list-item-group
                                        :color="themeBgColor"
                                    >
                                        <v-list-item
                                        >
                                            <v-list-item-icon>
                                                <v-icon>mdi-account-group</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="'Total users'"></v-list-item-title>
                                                <v-list-item-subtitle
                                                    v-text="license.usersAllowed"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-account-multiple-minus</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="'Assigned users'"></v-list-item-title>
                                                <v-list-item-subtitle v-text="usersAssigned"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-account-multiple-plus</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="'Users available'"></v-list-item-title>
                                                <v-list-item-subtitle v-text="license.usersLeft"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-license</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="'Active'"></v-list-item-title>
                                                <v-list-item-subtitle v-text="license.active"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon> mdi-calendar-weekend-outline</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="'Trial days'"></v-list-item-title>
                                                <v-text-field
                                                    style="max-width: 50px"
                                                    v-model="license.trialPeriodDays"
                                                    :color="themeBgColor"
                                                    v-if="enableToEditLicense"
                                                    dense
                                                    name="trial_days"
                                                    type="text"
                                                ></v-text-field>
                                                <v-list-item-subtitle v-if="!enableToEditLicense" v-text="license.trialPeriodDays"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list-item-group>
                                </v-list>
                            </v-col>
                            <v-col md="6">
                                <v-date-picker
                                    v-model="license.expiresAt"
                                    :color="themeBgColor"
                                    :first-day-of-week="1"
                                    :locale="calendarLocale"
                                    :readonly="!enableToEditLicense"
                                ></v-date-picker>
                            </v-col>
                        </v-row>
                        <v-expand-transition>
                            <v-card
                                v-if="enableToEditLicense"
                                class="mx-auto"
                                outlined
                            >
                                <div class="overline mx-2">

                                </div>
                                <v-btn
                                    v-if="enableToEditLicense"
                                    :color="license.active ? 'red' :'green'"
                                    class="ma-2"
                                    dark
                                    @click="license.active = !license.active"
                                >
                                    {{ license.active ? 'suspend' : 'renew' }}
                                </v-btn>
                                <div class="overline mx-2">
                                    additional licenses
                                </div>
                                <span v-for="(licenseValue, index) in licenseValues" :key="index">
                                <v-btn
                                    class="ma-2"
                                    color="grey darken-1"
                                    outlined
                                    @click="appendLicenseItems(licenseValue)">
                                    {{ 'up to ' + licenseValue }}
                                </v-btn>
                                <br v-if="index === (licenseValues.length/2) - 1">
                            </span>
                            </v-card>
                        </v-expand-transition>
                    </v-card-text>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.notification.history
                            }}
                        </v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-list
                            dense
                        >
                            <v-list-item
                                v-for="(item, index) in licenseHistory"
                                :key="index"
                                v-if="item.diff.length > 0"
                            >
                                <v-list-item-title >
                                    {{ item.diff }}
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </div>
        </div>
        <v-dialog v-model="assignCompanyDialog" max-width="480" persistent>
            <v-card>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.main.assign }}
                </v-card-title>
                <v-card-text>
                    <v-select :color="themeBgColor" :item-color="themeBgColor"
                              v-model="reassignedUserForm.company_id" :items="customers"
                              item-value="id"
                              item-text="name"
                              dense :label="langMap.main.company">
                    </v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="assignCompanyDialog = false">
                        {{ langMap.main.cancel }}
                    </v-btn>
                    <v-btn color="red darken-1" text @click="assignToIxarmaCompany">
                        {{ langMap.main.assign }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>


<script>
import EventBus from "../../components/EventBus";

export default {

    data() {

        return {
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
            calendarLocale: this.$store.state.lang.locale.replace("_", "-").toLowerCase(),
            licenseUserHeaders: [
                // {text: 'id', value: 'id'},
                {text: `username`, value: 'username'},
                {text: `phone`, value: 'phoneNumber', sortable: false},
                {text: `platform`, value: 'platform', sortable: false},
                {text: `licensed`, value: 'licensed', sortable: false},
                {text: `active`, value: 'active', sortable: false},
                {text: `expired_at`, value: 'trialExpirationAtString', sortable: false},
                {text: `last_activation`, value: 'lastActivationChange', sortable: false},
            ],
            menu2: false,
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            errors: [],
            languages: [],
            options: {
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10,
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            enableToEdit: false,
            enableToEditLicense: false,
            rolesDialog: false,
            licenseHistory: [],
            licenseUsers: [],
            singleUserForm: {
                user: '',
                role_ids: [],
                company_user_id: 0
            },
            clientIsLoaded: false,
            employees: [],
            licenseValues: [10, 20, 50, 100, 500, 1000],
            usersAssigned: 0,
            selectedDate: new Date().toISOString().substr(0, 10),
            client: {
                client_name: '',
                client_description: '',
                connection_links: [""],
                products: [
                    {
                        product_data: {}
                    }
                ],
                phones: [],
                addresses: [],
                socials: [],
                employees: [
                    {
                        employee: {
                            user_id: '',
                            company_id: '',
                            roles: [],
                            user_data: {
                                phones: [],
                                addresses: [],
                            }

                        }
                    }
                ]
            },
            employeeForm: {
                company_user_id: 0,
                description: '',
                name: '',
                email: '',
                client_id: 0,
                is_active: false
            },
            reassignedUserForm: {
                user_id: '',
                company_id: ''
            },
            assignCompanyDialog: false,
            customers: [],
            contactInfoModal: false,
            contactInfoEditBtn: false,
            roles: [
                {
                    id: '',
                    name: ''
                }
            ],
            productsSearch: '',
            license: {},
            products: [],
            phoneTypes: [],
            addressTypes: [],
            socialTypes: [],
            emailTypes: [],
            countries: [],
            updatePhoneDlg: false,
            updateAddressDlg: false,
            updateSocialDlg: false,
            updateEmailDlg: false,
            contactInfoForm: null,
            deleteProductDlg: false,
            selectedProductId: null
        }
    },
    mounted() {
        this.getClient();
        this.getLicenseUsers();
        this.getRoles();
        this.getLanguages();
        this.getCountries();
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        localized(item, field = 'name') {
            let locale = this.$store.state.lang.locale.replace(/^([^_]+).*$/, '$1');
            return item[field + '_' + locale] ? item[field + '_' + locale] : item[field];
        },
        getClient() {
            axios.get(`/api/client/${this.$route.params.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.client = response.data
                    this.client.client_name = response.data.name
                    this.client.client_description = response.data.description
                    this.$store.state.pageName = this.client.client_name
                    this.getLicense()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        getLicense() {
            axios.get(`/api/custom_license/${this.$route.params.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.license = response.data.limits
                    this.client.connection_links = response.data.info !== null ? response.data.info.serverUrls : [""]
                    this.license.expiresAt = this.moment(response.data.limits.expiresAt).format('YYYY-MM-DD')
                    this.usersAssigned = this.license.usersAllowed - this.license.usersLeft;
                    this.getLicenseHistory();
                    console.log(this.client.connection_links);
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        getLicenseHistory() {
            axios.get(`/api/custom_license/${this.$route.params.id}/history`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.licenseHistory = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        getLicenseUsers() {
            axios.get(`/api/custom_license/${this.$route.params.id}/users`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.licenseUsers = response.data.entities
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        manageLicenseUsers(remoteId, isLicensed) {
            // console.log(remoteId);
            // console.log(isLicensed);
            axios.get(`/api/custom_license/${this.$route.params.id}/user/${remoteId}/${isLicensed}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getLicense();
                    this.getLicenseUsers();
                    // this.licenseUsers = response.data.entities
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        appendLicenseItems(count = 0) {
            this.license.usersAllowed += count
        },
        updateLicense() {
            axios.put(`/api/custom_license/${this.$route.params.id}/limits`, this.license).then(response => {
                response = response.data
                if (response.success === true) {
                    this.license = response.data
                    this.license.expiresAt = this.moment(response.data.expiresAt).format('YYYY-MM-DD')
                    this.enableToEditLicense = false;
                    this.snackbarMessage = this.license = this.langMap.main.update_successful;
                    this.actionColor = 'success';
                    this.snackbar = true;
                    this.enableToEditLicense = false;
                    this.getLicense()
                    this.getLicenseUsers()
                } else {
                    this.snackbarMessage = this.license = response.error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                    this.enableToEditLicense = false;
                    this.getLicense()
                    this.getLicenseUsers()
                }

            });
        },
        getLanguages() {
            axios.get('/api/lang').then(response => {
                response = response.data
                if (response.success === true) {
                    this.languages = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getRoles() {
            this.roles = []
            axios.get('/api/roles').then(response => {
                response = response.data
                if (response.success === true) {
                    this.roles.push(response.data[response.data.length - 1])
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        getCountries() {
            axios.get('/api/countries').then(response => {
                response = response.data
                if (response.success === true) {
                    this.countries = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
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
        },
        clientUpdate() {
            axios.put(`/api/custom_license/${this.$route.params.id}`, this.client).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient();
                    this.enableToEdit = false;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        addConnectionLink() {
            this.client.connection_links.push(" ");
            console.log(this.client.connection_links);
            this.$forceUpdate();
        },
        removeConnectionLink(id) {
            this.client.connection_links.splice(id, 1);
            console.log(this.client.connection_links);
            this.$forceUpdate();
        },
        getClients() {
            this.loading = this.themeBgColor
            axios.get('/api/client', {
            }).then(response => {
                this.loading = false
                response = response.data
                if (response.success === true) {
                    this.customers = response.data.data
                    this.totalCustomers = response.data.total
                    this.lastPage = response.data.last_page
                    this.reassignedUserForm.company_id = this.client.id
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        showAssignDialog(item) {
            this.reassignedUserForm.user_id = item.id
            this.getClients();
            this.assignCompanyDialog = true
        },
        assignToIxarmaCompany() {
            axios.post('/api/custom_license_unassigned/assign', this.reassignedUserForm, {
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.getLicenseUsers();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
            // console.log(this.reassignedUserForm);
            this.assignCompanyDialog = false
        }
    }
}
</script>
