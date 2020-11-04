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
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{this.$store.state.lang.lang_map.main.profile}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateUser">
                            {{this.$store.state.lang.lang_map.main.update}}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-text-field
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.title_before_name"
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
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.title"
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
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.first_name"
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
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.last_name"
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
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.email"
                                    name="email"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.email"
                                    required
                                    :error-messages="errors.email"
                                    lazy-validation
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    :color="themeColor"
                                    id="password"
                                    :label="this.$store.state.lang.lang_map.main.password"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    type="password"
                                    v-model="userData.password"
                                    :error-messages="errors.password"
                                    lazy-validation
                                    class="col-md-6"
                                    required
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-select
                                    :label="this.$store.state.lang.lang_map.main.country"
                                    :color="themeColor"
                                    class="col-md-6"
                                    :item-color="themeColor"
                                    name="country"
                                    prepend-icon="mdi-map"
                                    item-value="id"
                                    :items="countries"
                                    v-model="userData.country_id"
                                    :error-messages="errors.country_id"
                                    lazy-validation
                                    :readonly="!enableToEdit"
                                    required
                                    dense>
                                <template slot="selection" slot-scope="data">
                                    ({{ data.item.iso_3166_2 }}) {{ localized(data.item) }}
                                </template>
                                <template slot="item" slot-scope="data">
                                    ({{ data.item.iso_3166_2 }}) {{ localized(data.item) }}
                                </template>
                                </v-select>
                                <v-text-field
                                    :color="themeColor"
                                    label="Anredeform"
                                    name="anredeform"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.anredeform"
                                    :error-messages="errors.anredeform"
                                    lazy-validation
                                    class="col-md-6"
                                    required
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-select
                                    :label="this.$store.state.lang.lang_map.main.language"
                                    :color="themeColor"
                                    class="col-md-6"
                                    :item-color="themeColor"
                                    name="language"
                                    prepend-icon="mdi-mail"
                                    item-text="name"
                                    item-value="id"
                                    :items="languages"
                                    v-model="userData.language_id"
                                    :error-messages="errors.language_id"
                                    lazy-validation
                                    :readonly="!enableToEdit"
                                    dense
                                />
                                <v-select
                                    :label="this.$store.state.lang.lang_map.main.timezone"
                                    :color="themeColor"
                                    class="col-md-6"
                                    :item-color="themeColor"
                                    name="timezone"
                                    prepend-icon="mdi-timetable"
                                    item-text="text"
                                    item-value="id"
                                    :items="timezones"
                                    v-model="userData.timezone_id"
                                    :error-messages="errors.timezone_id"
                                    lazy-validation
                                    :readonly="!enableToEdit"
                                    dense
                                />
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col class="col-md-6">
                <v-card class="elevation-6">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>
                            {{this.$store.state.lang.lang_map.individuals.contact_info}}
                        </v-toolbar-title>
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
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in userData.phones"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon v-if="item.type"><v-icon left v-text="item.type.icon"></v-icon></v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.phone"></v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                        v-text="localized(item.type)"></v-list-item-subtitle>
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
                                                <v-list-item-icon v-if="item.type"><v-icon left v-text="item.type.icon"></v-icon></v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="">{{item.address}}
                                                        {{item.address_line_2}} {{item.address_line_3}}
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                        v-text="localized(item.type)"></v-list-item-subtitle>
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
                                                {{this.$store.state.lang.lang_map.main.phone}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="phoneForm.phone"
                                                                :label="this.$store.state.lang.lang_map.main.phone"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-value="id"
                                                                v-model="phoneForm.phone_type"
                                                                :items="phoneTypes"
                                                                :label="this.$store.state.lang.lang_map.main.type"
                                                                dense
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            dark
                                                            fab
                                                            right
                                                            bottom
                                                            small
                                                            :color="themeColor"
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
                                                {{this.$store.state.lang.lang_map.main.address}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-12" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.address"
                                                                :label="this.$store.state.lang.lang_map.main.address_line + ' 1'"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.address_line_2"
                                                                :label="this.$store.state.lang.lang_map.main.address_line + ' 2'"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.postal_code"
                                                                :label="this.$store.state.lang.lang_map.main.postal_code"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-4" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.city"
                                                                :label="this.$store.state.lang.lang_map.main.city"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :item-value="item => localized(item)"
                                                                v-model="addressForm.address.country"
                                                                :items="countries"
                                                                :label="this.$store.state.lang.lang_map.main.country"
                                                                dense
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    ({{ data.item.iso_3166_2 }}) {{ localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    ({{ data.item.iso_3166_2 }}) {{ localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-col cols="3" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-value="id"
                                                                v-model="addressForm.address_type"
                                                                :items="addressTypes"
                                                                :label="this.$store.state.lang.lang_map.main.type"
                                                                dense
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            dark
                                                            fab
                                                            right
                                                            bottom
                                                            small
                                                            :color="themeColor"
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

                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-6">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.system_settings.theme_color}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateUserSettings">
                            {{this.langMap.main.update}}
                        </v-btn>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12">
                                    <v-color-picker
                                        dot-size="25"
                                        mode="hexa"
                                        v-model="themeColor"
                                        :disabled="!enableToEdit"
                                    ></v-color-picker>
                                </v-col>
                                <v-col cols="12">
                                    <v-checkbox
                                        v-model="themeColorDlg"
                                        :value="1"
                                        :label="langMap.individuals.show_speed_panel"></v-checkbox>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

</template>

<script>

    import EventBus from "../../components/EventBus";

    export default {
        data() {
            return {
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                errors: [],
                enableToEdit: false,
                themeColor: this.$store.state.themeColor,
                langMap: this.$store.state.lang.lang_map,
                userData: {
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
                    addresses: [],
                    language_id: '',
                    timezone_id: ''
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
                languages: [],
                timezones: [],
                countries: [],
                themeColorDlg: localStorage.themeColorDlg == 1 ? 0 : 1
            }
        },
        mounted() {
            this.getUser();
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getLanguages();
            this.getTimeZones();
            this.getCountries();
        },
        methods: {
            localized(item, field = 'name') {
                let locale = this.$store.state.lang.locale.replace(/^([^_]+).*$/, '$1');
                return item[field + '_' + locale] ? item[field + '_' + locale] : item[field];
            },
            getUser() {
                axios.get('/api/user').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.userData = response.data
                        // console.log(this.userData);
                    }
                });
            },
            getLanguages() {
                axios.get('/api/lang').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.languages = response.data
                    }
                });
            },
            getTimeZones() {
                axios.get('/api/time_zones').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.timezones = response.data
                    }
                });
            },
            getCountries() {
                axios.get('/api/countries').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.countries = response.data
                    }
                });
            },
            updateUser(e) {
                e.preventDefault()
                this.snackbar = false;
                axios.post('/api/user', this.userData).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.userData.password = ''
                        this.getUser()
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true
                        this.enableToEdit = false
                        window.location.reload()
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
            updateUserSettings() {
                this.snackbar = false;

                axios.post('/api/user/settings', {theme_color: this.themeColor}).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.$store.state.themeColor = this.themeColor;
                        localStorage.themeColor = this.themeColor;
                        EventBus.$emit('update-theme-color', this.themeColor);
                        localStorage.themeColorDlg = this.themeColorDlg == 1 ? 0 : 1;
                        this.enableToEdit = false;
                        window.location.reload()
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                    return true;
                });
            },
            // parseErrors(errorTypes) {
            //     for (let typeIndex in errorTypes) {
            //         let errorType = [];
            //         if (errorTypes.hasOwnProperty(typeIndex)){
            //             errorType = errorTypes[typeIndex]
            //         }
            //         for (let errorIndex in errorType) {
            //             if (errorType.hasOwnProperty(errorIndex)){
            //                 this.error.push(errorType[errorIndex])
            //             }
            //         }
            //     }
            // }
        }
    }
</script>
