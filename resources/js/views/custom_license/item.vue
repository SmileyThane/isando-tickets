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
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{ langMap.company.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px"
                               @click="cancelUpdateClient">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateClient">
                            {{ langMap.main.update }}
                        </v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                v-model="client.client_name"
                                :color="themeColor"
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
                                :color="themeColor"
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
                    </v-card-text>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{ 'License Users' }}</v-toolbar-title>
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
                        >
                            <template v-slot:item.lastActivationChange="{ item }">
                                {{
                                    item.lastActivationChange > 0 ?
                                        moment(item.lastActivationChange).format('DD-MM-YYYY') : ''
                                }}
                            </template>
                            <template v-slot:item.licensed="{ item }">
                                <v-btn
                                    outlined
                                    @click="manageLicenseUsers(item.id, item.licensed)"
                                >
                                    <v-icon>{{ item.licensed ? 'mdi-check-circle-outline' : 'mdi-cancel' }}</v-icon>
                                </v-btn>
                            </template>
                        </v-data-table>
                    </div>
                </v-card>

            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>
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
                        <p
                            class="ma-2"
                        >
                            <strong>Total users:</strong> {{ license.usersAllowed }}
                            <strong>Assigned users:</strong> {{ usersAssigned }}
                            <strong>Users available:</strong> {{ license.usersLeft }}
                            <strong>Active:</strong> {{ license.active }}
                        </p>

                        <v-menu
                            v-model="menu2"
                            :close-on-content-click="false"
                            :disabled="!enableToEditLicense"
                            :nudge-right="40"
                            class="ma-2"
                            min-width="auto"
                            offset-y
                            transition="scale-transition"
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="license.expiresAt"
                                    v-bind="attrs"
                                    v-on="on"
                                    :color="themeColor"
                                    class="ma-2"
                                    prepend-icon="mdi-calendar"
                                    readonly
                                ></v-text-field>
                            </template>
                            <v-date-picker
                                v-model="license.expiresAt"
                                :color="themeColor"
                                @input="menu2 = false;"
                            ></v-date-picker>
                        </v-menu>
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
                    </v-card-text>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{ langMap.notification.history }}</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-list
                            dense
                        >
                            <v-list-item
                                v-for="(item, index) in licenseHistory"
                                :key="index"
                            >
                                <v-list-item-title>
                                    {{ item.diff }}
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-card-text>
                </v-card>
            </div>
        </div>
    </v-container>
</template>


<script>
import EventBus from "../../components/EventBus";

export default {

    data() {

        return {
            themeColor: this.$store.state.themeColor,
            langMap: this.$store.state.lang.lang_map,
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
        this.getLicense();
        this.getLicenseUsers();
        this.getRoles();
        this.getLanguages();
        this.getCountries();
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
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
                    this.license = response.data
                    this.license.expiresAt = this.moment(response.data.expiresAt).format('YYYY-MM-DD')
                    this.usersAssigned = this.license.usersAllowed - this.license.usersLeft;
                    this.getLicenseHistory();
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
            axios.put(`/api/custom_license/${this.$route.params.id}`, this.license).then(response => {
                response = response.data
                if (response.success === true) {
                    this.license = response.data
                    this.license.expiresAt = this.moment(response.data.expiresAt).format('YYYY-MM-DD')
                    this.enableToEditLicense = false;
                    this.getLicense()
                    this.getLicenseUsers()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
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
    },
    watch: {
        clientUpdates(value) {
            this.clientIsLoaded = true;
            if (this.singleUserForm.user) {
                this.singleUserForm.user = this.client.employees.find(x => x.employee.user_id === this.singleUserForm.user.id).employee.user_data;
            }
        }
    },
    computed: {
        clientUpdates: function () {
            return this.client
        },
    }
}
</script>
