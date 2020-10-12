<template>
    <v-container>
        <v-snackbar :bottom="true" :right="true" v-model="snackbar" :color="actionColor">
            {{ snackbarMessage }}
        </v-snackbar>
        <div class="row">
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar dense color="green" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.phone_types}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group color="green">
                                            <v-list-item
                                                v-for="(item, i) in phoneTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-if="item.name in langMap.phone_types" v-text="langMap.phone_types[item.name]"></v-list-item-title>
                                                    <v-list-item-title v-else v-text="item.name"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon small @click="showUpdateTypeDialog(item, 'updatePhoneType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                    <v-icon small @click="deletePhoneType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.system_settings.new_phone_type}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field color="green" item-color="green" v-model="phoneTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-text-field color="green" item-color="green" v-model="phoneTypeForm.icon" :label="langMap.main.icon" dense :append-outer-icon="phoneTypeForm.icon"></v-text-field>
                                                        </v-col>
                                                        <v-btn dark fab right bottom small color="green" @click="submitNewData(phoneTypeForm, 'addPhoneType')">
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

                <v-spacer></v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar dense color="green" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.social_types}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group color="green">
                                            <v-list-item
                                                v-for="(item, i) in socialTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-if="item.name in langMap.social_types" v-text="langMap.social_types[item.name]"></v-list-item-title>
                                                    <v-list-item-title v-else v-text="item.name"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon small @click="showUpdateTypeDialog(item, 'updateSocialType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                    <v-icon small @click="deleteSocialType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.system_settings.new_social_type}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field color="green" item-color="green" v-model="socialTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-text-field color="green" item-color="green" v-model="socialTypeForm.icon" :label="langMap.main.icon" dense :append-outer-icon="socialTypeForm.icon"></v-text-field>
                                                        </v-col>
                                                        <v-btn dark fab right bottom small color="green" @click="submitNewData(socialTypeForm, 'addSocialType')">
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
                    <v-toolbar dense color="green" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.address_types}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group color="green">
                                            <v-list-item
                                                v-for="(item, i) in addressTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-if="item.name in langMap.address_types" v-text="langMap.address_types[item.name]"></v-list-item-title>
                                                    <v-list-item-title v-else v-text="item.name"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon small @click="showUpdateTypeDialog(item, 'updateAddressType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                <v-icon small @click="deleteAddressType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.system_settings.new_address_type}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field color="green" item-color="green" v-model="addressTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-text-field color="green" item-color="green" v-model="addressTypeForm.icon" :label="langMap.main.icon" dense :append-outer-icon="addressTypeForm.icon"></v-text-field>
                                                        </v-col>
                                                        <v-btn dark fab right bottom small color="green" @click="submitNewData(addressTypeForm, 'addAddressType')">
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
                <v-spacer></v-spacer>
            </div>
        </div>

        <v-row justify="center">
            <v-dialog v-model="updateTypeDialog" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.system_settings.update_type_info}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="md-6" class="pa-1">
                                    <v-text-field color="green" item-color="green" v-model="updateTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                </v-col>
                                <v-col cols="6" class="pa-1">
                                    <v-text-field color="green" item-color="green" v-model="updateTypeForm.icon" :label="langMap.main.icon" dense :append-outer-icon="updateTypeForm.icon"></v-text-field>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text @click="updateTypeDialog=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn color="green" text @click="updateTypeDialog=false; submitNewData(updateTypeForm, updateTypeForm.method)">{{langMap.main.save}}</v-btn>
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
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                tooltip: false,
                updateTypeDialog: false,
                errors: [],
                langMap: this.$store.state.lang.lang_map,
                phoneTypeForm: {
                    entity_id: '',
                    entity_type: 'App\\PhoneType',
                    name: '',
                    icon: 'mdi-phone'
                },
                socialTypeForm: {
                    entity_id: '',
                    entity_type: 'App\\SocialType',
                    name: '',
                    icon: 'mdi-web'
                },
                addressTypeForm: {
                    entity_id: '',
                    entity_type: 'App\\AddressType',
                    name: '',
                    icon: 'mdi-map-marker'
                },
                updateTypeForm: {
                    id: '',
                    name: '',
                    icon: '',
                    title: '',
                    method: ''
                },
                phoneTypes: [],
                addressTypes: [],
                socialTypes: [],
                countries: []
            }
        },
        mounted() {
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
            this.getCountries();
        },
        methods: {
            getCountries() {
                axios.get('/api/countries').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.countries = response.data
                    }
                });
            },
            getPhoneTypes() {
                axios.get(`/api/phone_types`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.phoneTypes = response.data
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            },
            addPhoneType(form) {
                axios.post('/api/phone_type', form).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getPhoneTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.phone_type_created}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                    return true
                });
            },
            updatePhoneType(form) {
                axios.patch(`/api/phone_type/${form.id}`, form).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getPhoneTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.phone_type_updated}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                    return true
                });
            },
            deletePhoneType(id) {
                axios.delete(`/api/phone_type/${id}`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getPhoneTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.phone_type_deleted}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                });
            },
            getSocialTypes() {
                axios.get(`/api/social_types`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.socialTypes = response.data
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            },
            addSocialType(form) {
                axios.post('/api/social_type', form).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getSocialTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.social_type_created}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                    return true
                });
            },
            updateSocialType(form) {
                axios.patch(`/api/social_type/${form.id}`, form).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getSocialTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.social_type_updated}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                    return true
                });
            },
            deleteSocialType(id) {
                axios.delete(`/api/social_type/${id}`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getSocialTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.social_type_deleted}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                });
            },
            getAddressTypes() {
                axios.get(`/api/address_types`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.addressTypes = response.data;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            },
            addAddressType(form) {
                axios.post('/api/address_type', form).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getAddressTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.address_type_created}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                    return true
                });
            },
            updateAddressType(form) {
                axios.patch(`/api/address_type/${form.id}`, form).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getAddressTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.address_type_updated}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                    return true
                });
            },
            deleteAddressType(id) {
                axios.delete(`/api/address_type/${id}`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getAddressTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.address_type_deleted}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.main.generic_error}`;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                });
            },
            submitNewData(data, method) {
                this[method](data);
            },
            showUpdateTypeDialog(data, method) {
                this.updateTypeForm.id = data.id;
                this.updateTypeForm.name = data.name;
                this.updateTypeForm.icon = data.icon;
                this.updateTypeForm.title = '';
                this.updateTypeForm.method = method;

                this.updateTypeDialog = true;
            }
        }
    }
</script>
