<template>
    <v-container fluid>
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
                <v-card class="elevation-12 without-bottom">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{langMap.company.info}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px" @click="cancelUpdateCompany">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateCompany">
                            {{langMap.main.update}}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                :color="themeColor"
                                :label="langMap.company.name"
                                name="name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="company.name"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>

                            <v-text-field
                                :color="themeColor"
                                :label="langMap.company.description"
                                name="description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="company.description"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>

                            <v-text-field
                                :color="themeColor"
                                :label="langMap.company.company_number"
                                name="company_number"
                                prepend-icon="mdi-message-alert"
                                type="text"
                                v-model="company.company_number"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                            <v-menu
                                :close-on-content-click="false"
                                :nudge-right="40"
                                transition="scale-transition"
                                offset-y
                                min-width="290px"
                                :disabled="!enableToEdit"

                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        :color="themeColor"
                                        v-model="company.registration_date"
                                        :label="langMap.company.registration_date"
                                        name="registration_date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    :color="themeColor"
                                    v-model="company.registration_date"
                                ></v-date-picker>
                            </v-menu>
                        </v-form>
                    </v-card-text>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12 without-bottom">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.company.additional_info}}</v-toolbar-title>
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
                                                v-for="(item, i) in company.emails"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon v-text="item.type.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.email"></v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                                          v-text="localized(item.type)"></v-list-item-subtitle>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon
                                                        small
                                                        @click="editEmail(item)"
                                                    >
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                    <v-icon
                                                        small
                                                        @click="deleteEmail(item.id)"
                                                    >
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                            <v-list-item
                                                v-for="(item, i) in company.phones"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon v-text="item.type.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.phone"></v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                        v-text="localized(item.type)"></v-list-item-subtitle>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon
                                                        small
                                                        @click="editPhone(item)"
                                                    >
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
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
                                                v-for="(item, i) in company.addresses"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon v-text="item.type.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="">
                                                        <span v-if="item.street">{{item.street}}, </span>
                                                        <span v-if="item.street2">{{item.street2}}, </span>
                                                        <span v-if="item.street3">{{item.street3}}</span>
                                                        <br>{{item.postal_code}}&nbsp;&nbsp;{{item.city}},
                                                        <span v-if="item.country">{{localized(item.country)}}</span>
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                        v-text="localized(item.type)"></v-list-item-subtitle>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon
                                                        small
                                                        @click="editAddress(item)"
                                                    >
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
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
                                    <v-expansion-panels
                                        multiple
                                    >
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{this.$store.state.lang.lang_map.main.new_email}}
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
                                                                v-model="emailForm.email"
                                                                :label="langMap.main.email"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-value="id"
                                                                v-model="emailForm.email_type"
                                                                :items="emailTypes"
                                                                :label="langMap.main.type"
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
                                                            @click="submitNewData(company.id, emailForm, 'addEmail')"
                                                        >
                                                            <v-icon>mdi-plus</v-icon>
                                                        </v-btn>
                                                    </div>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.main.new_phone}}
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
                                                                :label="langMap.main.phone"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-text="name"
                                                                item-value="id"
                                                                v-model="phoneForm.phone_type"
                                                                :items="phoneTypes"
                                                                :label="langMap.main.type"
                                                                dense
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-list-item-icon>
                                                                        <v-icon small v-text="data.item.icon"></v-icon>
                                                                    </v-list-item-icon>
                                                                    <v-list-item-content>
                                                                        {{ localized(data.item) }}
                                                                    </v-list-item-content>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-list-item-icon>
                                                                        <v-icon small v-text="data.item.icon"></v-icon>
                                                                    </v-list-item-icon>
                                                                    <v-list-item-content>
                                                                        {{ localized(data.item) }}
                                                                    </v-list-item-content>
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
                                                            @click="submitNewData(company.id, phoneForm, 'addPhone')"
                                                        >
                                                            <v-icon>mdi-plus</v-icon>
                                                        </v-btn>
                                                    </div>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.main.new_address}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-12" class="pa-1">
                                                            <v-text-field
                                                                no-resize
                                                                rows="3"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.street"
                                                                :label="langMap.main.address_line1"
                                                                dense
                                                            ></v-text-field>
                                                            <v-text-field
                                                                no-resize
                                                                rows="3"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.street2"
                                                                :label="langMap.main.address_line2"
                                                                dense
                                                            ></v-text-field>
                                                            <v-text-field
                                                                no-resize
                                                                rows="3"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.street3"
                                                                :label="langMap.main.address_line3"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.postal_code"
                                                                :label="langMap.main.postal_code"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.city"
                                                                :label="langMap.main.city"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-select
                                                                :rules="['Required']"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-value="id"
                                                                v-model="addressForm.address.country_id"
                                                                :items="countries"
                                                                :label="langMap.main.country"
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
                                                        <v-col cols="6" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-text="name"
                                                                item-value="id"
                                                                v-model="addressForm.address_type"
                                                                :items="addressTypes"
                                                                :label="langMap.main.type"
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
                                                            @click="submitNewData(company.id, addressForm, 'addAddress')"
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
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.company.company_contacts}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="company.employees"
                            class="elevation-1"
                            item-key="id"
                            show-expand
                            :footer-props="footerProps"
                            dense
                            @click:row="showRolesModal"
                        >
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <p></p>
                                    <p v-if="item.user_data.emails && item.user_data.emails.length > 0"><strong>{{langMap.main.email}}:</strong>
                                    </p>
                                    <p v-if="item.user_data.emails && item.user_data.emails.length > 0"
                                       v-for="emailItem in item.user_data.emails"><v-icon small dense left v-if="emailItem.type">{{emailItem.type.icon}}</v-icon> {{ emailItem.email }}
                                        <span v-if="emailItem.type">({{ localized(emailItem.type) }})</span></p>
                                    <p v-if="item.user_data.phones && item.user_data.phones.length > 0"><strong>{{langMap.main.phone}}:</strong>
                                    </p>
                                    <p v-if="item.user_data.phones && item.user_data.phones.length > 0"
                                       v-for="phoneItem in item.user_data.phones"><v-icon small dense left v-if="phoneItem.type">{{phoneItem.type.icon}}</v-icon> {{ phoneItem.phone }}
                                        <span v-if="phoneItem.type">({{ localized(phoneItem.type) }})</span></p>
                                    <p v-if="item.user_data.addresses && item.user_data.addresses.length > 0">
                                        <strong>{{langMap.main.address}}:</strong></p>
                                    <p v-if="item.user_data.addresses && item.user_data.addresses.length > 0"
                                       v-for="addressItem in item.user_data.addresses"><v-icon small dense left v-if="addressItem.type">{{addressItem.type.icon}}</v-icon>
                                        <span v-if="addressItem.street">{{ addressItem.street }}, </span>
                                        <span v-if="addressItem.street2">{{ addressItem.street2 }}, </span>
                                        <span v-if="addressItem.street3">{{ addressItem.street3 }}</span>
                                        <br>{{addressItem.postal_code }}&nbsp;&nbsp;{{ addressItem.city }},
                                        <span v-if="addressItem.country">{{localized(addressItem.country)}}</span>
                                        <span v-if="addressItem.type">({{ localized(addressItem.type) }})</span></p>
                                </td>
                            </template>
                            <template v-slot:item.user_data="{ item }">
                                <div class="justify-center" v-if="item.user_data">
                                    {{ item.user_data.full_name }}
                                </div>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn :disabled="!item.user_data.is_active" @click.native.stop="sendInvite(item)" icon
                                               v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                            >
                                                mdi-email-alert
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{langMap.company.resend_invite}}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn @click.native.stop="showRolesModal(item)" icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                            >
                                                mdi-pencil
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{langMap.company.edit_contact}}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn :disabled="checkEmployeeRoleByIds(item, [2])"
                                               @click.native.stop="showRemoveEmployeeDlg(item)" icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{langMap.company.delete_contact}}</span>
                                </v-tooltip>
                            </template>

                        </v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{langMap.company.new_contact}}
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                                <v-text-field
                                                    class="col-md-4"
                                                    :color="themeColor"
                                                    :label="langMap.main.name"
                                                    name="name"
                                                    type="text"
                                                    v-model="employeeForm.name"
                                                    required
                                                    dense
                                                    hide-details
                                                ></v-text-field>
                                            <v-text-field
                                                v-model="employeeForm.middle_name"
                                                :color="themeColor"
                                                :error-messages="errors.middle_name"
                                                :label="this.$store.state.lang.lang_map.main.middle_name"
                                                class="col-md-4"
                                                dense
                                                lazy-validation
                                                name="middle_name"
                                                type="text"
                                                hide-details
                                            ></v-text-field>
                                            <v-text-field
                                                v-model="employeeForm.surname"
                                                :color="themeColor"
                                                :error-messages="errors.surname"
                                                :label="this.$store.state.lang.lang_map.main.last_name"
                                                class="col-md-4"
                                                dense
                                                lazy-validation
                                                name="surname"
                                                type="text"
                                                hide-details
                                            ></v-text-field>
                                                <v-text-field
                                                    class="col-md-6"
                                                    :color="themeColor"
                                                    :label="langMap.main.email"
                                                    name="email"
                                                    type="text"
                                                    dense
                                                    v-model="employeeForm.email"
                                                    required
                                                    hide-details
                                                ></v-text-field>
                                                <v-select
                                                    class="col-md-6"
                                                    :label="langMap.main.roles"
                                                    :color="themeColor"
                                                    :item-color="themeColor"
                                                    item-value="id"
                                                    :items="roles"
                                                    dense
                                                    v-model="employeeForm.role_id"
                                                    hide-details
                                                >
                                                    <template slot="selection" slot-scope="data">
                                                        {{ langMap.roles[data.item.name] }}
                                                    </template>
                                                    <template slot="item" slot-scope="data">
                                                        {{ langMap.roles[data.item.name] }}
                                                    </template>
                                                </v-select>
                                                <v-checkbox
                                                    style="margin-top: 0!important;"
                                                    class="col-md-6"
                                                    :label="langMap.main.give_access + '?'"
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
                                            :color="themeColor"
                                            @click="addEmployee"
                                        >
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>
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
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.company.social_info}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-list
                            dense
                            subheader
                        >
                            <v-list-item-group :color="themeColor">
                                <v-list-item
                                    v-for="(item) in company.socials"
                                    :key="item.id"
                                >
                                    <v-list-item-icon v-if="item.type"><v-icon left v-text="item.type.icon"></v-icon></v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title
                                            v-text="item.social_link"></v-list-item-title>
                                        <v-list-item-subtitle v-if="item.type"
                                            v-text="localized(item.type)"></v-list-item-subtitle>
                                    </v-list-item-content>
                                    <v-list-item-action>
                                        <v-icon
                                            small
                                            @click="editSocial(item)"
                                        >
                                            mdi-pencil
                                        </v-icon>
                                    </v-list-item-action>
                                    <v-list-item-action>
                                        <v-icon
                                            small
                                            @click="deleteSocial(item.id)"
                                        >
                                            mdi-delete
                                        </v-icon>
                                    </v-list-item-action>
                                </v-list-item>

                            </v-list-item-group>
                        </v-list>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{langMap.company.new_social_item}}
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
                                                    v-model="socialForm.social_link"
                                                    :label="langMap.main.link"
                                                    dense
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="6" class="pa-1">
                                                <v-select
                                                    :color="themeColor"
                                                    :item-color="themeColor"
                                                    item-value="id"
                                                    v-model="socialForm.social_type"
                                                    :items="socialTypes"
                                                    :label="langMap.main.type"
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
                                                @click="submitNewData(company.id, socialForm, 'addSocial')"
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
        <v-row>
            <v-col md="6">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{ langMap.product.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.product.add_new }}
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content style="padding-bottom: 0">
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.name"
                                                    name="product_name"
                                                    type="text"
                                                    v-model="productForm.product_name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.description"
                                                    name="product_description"
                                                    type="text"
                                                    v-model="productForm.product_description"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                :color="themeColor"
                                                @click="addProduct"
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="productHeaders"
                            :items="company.products"
                            class="elevation-1"
                            dense
                            item-key="id"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon @click="showProduct(item.product_data)">
                                            <v-icon
                                                small
                                            >
                                                mdi-eye
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.customer.show_product }}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon @click="showDeleteProductDlg(item)">
                                            <v-icon
                                                small
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.customer.unlink_product }}</span>
                                </v-tooltip>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col md="6">
                <v-spacer></v-spacer>
                <v-card class="elevation-12 without-bottom">
                    <v-toolbar dense :color="themeColor" dark flat>
                        <v-toolbar-title>{{langMap.company.product_categories}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-treeview
                                        open-on-click
                                        :items="productCategoriesTree"
                                        activatable
                                        item-key="id"
                                    >
                                        <template v-slot:prepend="{ item }">
                                            <v-icon v-if="item.children.length">mdi-folder</v-icon>
                                            <v-icon v-else>mdi-file</v-icon>
                                        </template>
                                        <template v-slot:append="{ item }">
                                            <v-btn
                                                icon
                                                small
                                                @click="deleteProductCategory(item.id)"
                                            >
                                                <v-icon>mdi-trash-can</v-icon>
                                            </v-btn>
                                        </template>

                                    </v-treeview>

                                    <v-expansion-panels>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.company.new_product_category}}
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
                                                                v-model="productCategoryForm.name"
                                                                :label="langMap.main.name"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-text="name"
                                                                item-value="id"
                                                                v-model="productCategoryForm.parent_id"
                                                                :items="productCategoriesFlat"
                                                                :label="langMap.company.parent_product_category"
                                                                dense
                                                            >
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            dark
                                                            fab
                                                            right
                                                            bottom
                                                            small
                                                            :color="themeColor"
                                                            @click="submitNewData(company.id, productCategoryForm, 'addProductCategory')"
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
                        <span class="headline">{{langMap.company.update_info}}: {{singleUserForm.user.name}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-select
                                :label="langMap.main.role"
                                :color="themeColor"
                                :item-color="themeColor"
                                item-value="id"
                                :items="roles"
                                :disabled="!checkRoleByIds([1,2,3])"
                                v-model="singleUserForm.role_ids"
                                multiple
                            >
                                <template slot="selection" slot-scope="data">
                                    {{ langMap.roles[data.item.name] }}
                                </template>
                                <template slot="item" slot-scope="data">
                                    {{ langMap.roles[data.item.name] }}
                                </template>
                            </v-select>
                            <v-expansion-panels
                                :disabled="!checkRoleByIds([1,2,3])"
                            >
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        {{langMap.company.user_info}}
                                        <template v-slot:actions>
                                            <v-icon color="submit">mdi-plus</v-icon>
                                        </template>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-form>
                                            <v-row>
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.title_before_name"
                                                    name="title_before_name"
                                                    prepend-icon="mdi-book-account-outline"
                                                    type="text"
                                                    v-model="singleUserForm.user.title_before_name"
                                                    :error-messages="errors.title_before_name"
                                                    lazy-validation
                                                    class="col-md-6"
                                                    dense
                                                ></v-text-field>
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.title"
                                                    name="title"
                                                    prepend-icon="mdi-book-account-outline"
                                                    type="text"
                                                    v-model="singleUserForm.user.title"
                                                    :error-messages="errors.title"
                                                    lazy-validation
                                                    class="col-md-6"
                                                    dense
                                                ></v-text-field>
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.name"
                                                    name="name"
                                                    prepend-icon="mdi-book-account-outline"
                                                    type="text"
                                                    v-model="singleUserForm.user.name"
                                                    :error-messages="errors.name"
                                                    lazy-validation
                                                    required
                                                    class="col-md-6"
                                                    dense
                                                ></v-text-field>
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.last_name"
                                                    name="surname"
                                                    prepend-icon="mdi-book-account-outline"
                                                    type="text"
                                                    v-model="singleUserForm.user.surname"
                                                    :error-messages="errors.surname"
                                                    lazy-validation
                                                    class="col-md-6"
                                                    dense
                                                ></v-text-field>
                                                <v-btn
                                                    dark
                                                    fab
                                                    right
                                                    bottom
                                                    small
                                                    :color="themeColor"
                                                    @click="updateUser"
                                                >
                                                    <v-icon>mdi-plus</v-icon>
                                                </v-btn>
                                            </v-row>
                                        </v-form>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        {{langMap.company.additional_info}}
                                        <template v-slot:actions>
                                            <v-icon color="submit">mdi-plus</v-icon>
                                        </template>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-form>
                                            <v-row>
                                                <v-col class="col-md-12">
                                                    <v-list
                                                        dense
                                                        subheader
                                                    >
                                                        <v-list-item-group :color="themeColor">
                                                            <v-list-item
                                                                v-for="(item, i) in singleUserForm.user.emails"
                                                                :key="item.id"
                                                            >
                                                                <v-list-item-icon v-if="item.type">
                                                                    <v-icon v-text="item.type.icon"></v-icon>
                                                                </v-list-item-icon>
                                                                <v-list-item-content>
                                                                    <v-list-item-title v-text="item.email"></v-list-item-title>
                                                                    <v-list-item-subtitle v-if="item.type"
                                                                                          v-text="localized(item.type)"></v-list-item-subtitle>
                                                                </v-list-item-content>
                                                                <v-list-item-action>
                                                                    <v-icon
                                                                        small
                                                                        @click="editEmail(item)"
                                                                    >
                                                                        mdi-pencil
                                                                    </v-icon>
                                                                </v-list-item-action>
                                                                <v-list-item-action v-if="item.email_type === 1">
                                                                    <v-icon small :title="langMap.profile.login_email">
                                                                        mdi-lock
                                                                    </v-icon>
                                                                </v-list-item-action>
                                                                <v-list-item-action v-else>
                                                                    <v-icon small @click="deleteEmail(item.id)">
                                                                        mdi-delete
                                                                    </v-icon>
                                                                </v-list-item-action>
                                                            </v-list-item>
                                                            <v-list-item
                                                                v-for="(item, i) in singleUserForm.user.phones"
                                                                :key="item.id"
                                                            >
                                                                <v-list-item-icon v-if="item.type"><v-icon left v-text="item.type.icon"></v-icon></v-list-item-icon>
                                                                <v-list-item-content>
                                                                    <v-list-item-title
                                                                        v-text="item.phone"></v-list-item-title>
                                                                    <v-list-item-subtitle v-if="item.type"
                                                                        v-text="localized(item.type)"></v-list-item-subtitle>
                                                                </v-list-item-content>
                                                                <v-list-item-action>
                                                                    <v-icon
                                                                        small
                                                                        @click="editPhone(item)"
                                                                    >
                                                                        mdi-pencil
                                                                    </v-icon>
                                                                </v-list-item-action>
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
                                                                v-for="(item, i) in singleUserForm.user.addresses"
                                                                :key="item.id"
                                                            >
                                                                <v-list-item-icon v-if="item.type"><v-icon left v-text="item.type.icon"></v-icon></v-list-item-icon>
                                                                <v-list-item-content>
                                                                    <v-list-item-title v-text="">
                                                                        <span v-if="item.street">{{item.street}}<br></span>
                                                                        <span v-if="item.street2">{{item.street2}}<br></span>
                                                                        <span v-if="item.street3">{{item.street3}}<br></span>
                                                                        {{item.postal_code}} {{item.city}}
                                                                        <span v-if="item.country">{{localized(item.country)}}</span>
                                                                    </v-list-item-title>
                                                                    <v-list-item-subtitle v-if="item.type"
                                                                        v-text="localized(item.type)"></v-list-item-subtitle>
                                                                </v-list-item-content>
                                                                <v-list-item-action>
                                                                    <v-icon
                                                                        small
                                                                        @click="editAddress(item)"
                                                                    >
                                                                        mdi-pencil
                                                                    </v-icon>
                                                                </v-list-item-action>
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
                                                                {{this.$store.state.lang.lang_map.main.new_email}}
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
                                                                                v-model="userEmailForm.email"
                                                                                :label="langMap.main.email"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="6" class="pa-1">
                                                                            <v-select
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                item-value="id"
                                                                                v-model="userEmailForm.email_type"
                                                                                :items="emailTypes"
                                                                                :label="langMap.main.type"
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
                                                                            @click="submitNewData(singleUserForm.user.id, userEmailForm, 'addEmail')"
                                                                        >
                                                                            <v-icon>mdi-plus</v-icon>
                                                                        </v-btn>
                                                                    </div>
                                                                </v-form>
                                                            </v-expansion-panel-content>
                                                        </v-expansion-panel>

                                                        <v-expansion-panel>
                                                            <v-expansion-panel-header>
                                                                {{langMap.main.new_phone}}
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
                                                                                v-model="userPhoneForm.phone"
                                                                                :label="langMap.main.phone"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="6" class="pa-1">
                                                                            <v-select
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                item-value="id"
                                                                                v-model="userPhoneForm.phone_type"
                                                                                :items="phoneTypes"
                                                                                :label="langMap.main.type"
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
                                                                            @click="submitNewData(singleUserForm.user.id, userPhoneForm, 'addPhone')"
                                                                        >
                                                                            <v-icon>mdi-plus</v-icon>
                                                                        </v-btn>
                                                                    </div>
                                                                </v-form>
                                                            </v-expansion-panel-content>
                                                        </v-expansion-panel>
                                                        <v-expansion-panel>
                                                            <v-expansion-panel-header>
                                                                {{langMap.main.new_address}}
                                                                <template v-slot:actions>
                                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                                </template>
                                                            </v-expansion-panel-header>
                                                            <v-expansion-panel-content>
                                                                <v-form>
                                                                    <div class="row">
                                                                        <v-col cols="md-12" class="pa-1">
                                                                            <v-text-field
                                                                                no-resize
                                                                                rows="3"
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                v-model="userAddressForm.address.street"
                                                                                :label="langMap.main.address_line1"
                                                                                dense
                                                                            ></v-text-field>
                                                                            <v-text-field
                                                                                no-resize
                                                                                rows="3"
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                v-model="userAddressForm.address.street2"
                                                                                :label="langMap.main.address_line2"
                                                                                dense
                                                                            ></v-text-field>
                                                                            <v-text-field
                                                                                no-resize
                                                                                rows="3"
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                v-model="userAddressForm.address.street3"
                                                                                :label="langMap.main.address_line3"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-6" class="pa-1">
                                                                            <v-text-field
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                v-model="userAddressForm.address.postal_code"
                                                                                :label="langMap.main.postal_code"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-6" class="pa-1">
                                                                            <v-text-field
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                v-model="userAddressForm.address.city"
                                                                                :label="langMap.main.city"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-6" class="pa-1">
                                                                            <v-select
                                                                                :rules="['Required']"
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                item-value="id"
                                                                                v-model="userAddressForm.address.country_id"
                                                                                :items="countries"
                                                                                :label="langMap.main.country"
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
                                                                        <v-col cols="6" class="pa-1">
                                                                            <v-select
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                item-value="id"
                                                                                v-model="userAddressForm.address_type"
                                                                                :items="addressTypes"
                                                                                :label="langMap.main.type"
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
                                                                            @click="submitNewData(singleUserForm.user.id, userAddressForm, 'addAddress')"
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
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>

                            <v-checkbox
                                :label="langMap.main.give_access"
                                :color="themeColor"
                                :disabled="!checkRoleByIds([1,2,3])"
                                v-model="singleUserForm.user.is_active"
                                @change="changeIsActive(singleUserForm.user)"
                                hide-details
                            ></v-checkbox>
                        </v-container>
                        <!--                        <small>*indicates required field</small>-->
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text @click="rolesDialog = false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateRole">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updatePhoneDlg" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_phone}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="md-6" class="pa-1">
                                    <v-text-field :color="themeColor" :item-color="themeColor" v-model="phoneForm.phone" :label="langMap.main.phone" dense></v-text-field>
                                </v-col>
                                <v-col cols="md-6" class="pa-1">
                                    <v-select :color="themeColor" :item-color="themeColor"
                                              v-model="phoneForm.phone_type" :items="phoneTypes" item-value="id"
                                              dense :label="langMap.main.type">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updatePhoneDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updatePhoneDlg=false; updatePhone()">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateSocialDlg" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_social}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="md-6" class="pa-1">
                                    <v-text-field :color="themeColor" :item-color="themeColor" v-model="socialForm.social_link" :label="langMap.main.link" dense></v-text-field>
                                </v-col>
                                <v-col cols="md-6" class="pa-1">
                                    <v-select :color="themeColor" :item-color="themeColor"
                                              v-model="socialForm.social_type" :items="socialTypes" item-value="id"
                                              dense :label="langMap.main.type">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateSocialDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateSocialDlg=false; updateSocial()">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateAddressDlg" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_address}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="md-12" class="pa-1">
                                    <v-text-field
                                        no-resize
                                        rows="3"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.street"
                                        :label="langMap.main.address_line1"
                                        dense
                                    ></v-text-field>
                                    <v-text-field
                                        no-resize
                                        rows="3"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.street2"
                                        :label="langMap.main.address_line2"
                                        dense
                                    ></v-text-field>
                                    <v-text-field
                                        no-resize
                                        rows="3"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.street3"
                                        :label="langMap.main.address_line3"
                                        dense
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="md-6" class="pa-1">
                                    <v-text-field
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.postal_code"
                                        :label="langMap.main.postal_code"
                                        dense
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="md-6" class="pa-1">
                                    <v-text-field
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.city"
                                        :label="langMap.main.city"
                                        dense
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="md-6" class="pa-1">
                                    <v-select
                                        :rules="['Required']"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        item-value="id"
                                        v-model="addressForm.address.country_id"
                                        :items="countries"
                                        :label="langMap.main.country"
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
                                <v-col cols="6" class="pa-1">
                                    <v-select
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        item-value="id"
                                        v-model="addressForm.address_type"
                                        :items="addressTypes"
                                        :label="langMap.main.type"
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
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateAddressDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateAddressDlg=false; updateAddress()">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateEmailDlg" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_email}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="md-6" class="pa-1">
                                    <v-text-field :color="themeColor" :item-color="themeColor" v-model="emailForm.email" :label="langMap.main.email" dense></v-text-field>
                                </v-col>
                                <v-col cols="md-6" class="pa-1">
                                    <v-select v-if="emailForm.email_type === 1" readonly :color="themeColor" :item-color="themeColor"
                                              v-model="emailForm.email_type" :items="emailTypes" item-value="id"
                                              dense :label="langMap.main.type">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                    <v-select v-else :color="themeColor" :item-color="themeColor"
                                              v-model="emailForm.email_type" :items="emailTypes" item-value="id"
                                              dense :label="langMap.main.type">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateEmailDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateEmailDlg=false; updateEmail()">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="deleteProductDlg" persistent max-width="480">
                <v-card>
                    <v-card-title>{{langMap.customer.unlink_product}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="deleteProductDlg = false">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteProductDlg = false; deleteProduct(selectedProductId)">
                            {{langMap.main.delete}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="removeEmployeeDlg" persistent max-width="480">
                <v-card>
                    <v-card-title>{{langMap.company.delete_employee_msg}}</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeEmployeeDlg = false">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="removeEmployeeDlg = false; removeEmployee(selectedEmployee)">
                            {{langMap.main.delete}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

        </v-row>
    </v-container>
</template>


<script>
    import Sidebar from "../../components/Sidebar";
    import EventBus from "../../components/EventBus";

    export default {

        data() {
            return {
                snackbar: false,
                actionColor: '',
                themeColor: this.$store.state.themeColor,
                snackbarMessage: '',
                tooltip: false,
                enableToEdit: false,
                errors: [],
                footerProps: {
                    itemsPerPage: 10,
                    disableItemsPerPage: true,
                    itemsPerPageText: this.$store.state.lang.lang_map.main.items_per_page
                },
                isCompanyUpdated: false,
                langMap: this.$store.state.lang.lang_map,
                headers: [
                    {text: '', value: 'data-table-expand'},
                    {text: `${this.$store.state.lang.lang_map.company.user}`, value: 'user_data'},
                    {text: `${this.$store.state.lang.lang_map.main.roles}`, value: 'role_names'},
                    {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
                ],
                productForm: {
                    product_name: '',
                    product_description: '',
                },
                productHeaders: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'product_data.id',
                    },
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'product_data.name'},
                    {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'product_data.description'},
                    {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
                ],
                deleteProductDlg: false,
                selectedProductId: null,
                companyIsLoaded: false,
                company: {
                    name: '',
                    company_number: '',
                    description: '',
                    registration_date: '',
                    phones: [],
                    addresses: [],
                    socials: [],
                    emails: [],
                    employees: [
                        {
                            user_id: '',
                            company_id: '',
                            roles: [],
                            user_data: {
                                phones: [],
                                addresses: [],
                            }
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
                singleUserForm: {
                    user: '',
                    role_ids: [],
                    company_user_id: '',
                    phones: [],
                    addresses: [],
                    emails: []
                },
                phoneForm: {
                    id: '',
                    entity_id: '',
                    entity_type: 'App\\Company',
                    phone: '',
                    phone_type: ''
                },
                addressForm: {
                    id: '',
                    entity_id: '',
                    entity_type: 'App\\Company',
                    address: {
                        street: '',
                        street2: '',
                        street3: '',
                        postal_code: '',
                        city: '',
                        country_id: ''
                    },
                    address_type: ''
                },
                socialForm: {
                    id: '',
                    entity_id: '',
                    entity_type: 'App\\Company',
                    social_link: '',
                    social_type: ''
                },
                emailForm: {
                    entity_id: '',
                    entity_type: 'App\\Company',
                    email: '',
                    email_type: ''
                },
                userPhoneForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    phone: '',
                    phone_type: ''
                },
                userAddressForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    address: {
                        street: '',
                        street2: '',
                        street3: '',
                        postal_code: '',
                        city: '',
                        country_id: ''
                    },
                    address_type: ''
                },
                userSocialForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    social_link: '',
                    social_type: ''
                },
                userEmailForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    email: '',
                    email_type: ''
                },
                productCategoryForm: {
                    name: '',
                    company_id: '',
                    parent_id: '',
                },
                phoneTypes: [],
                addressTypes: [],
                socialTypes: [],
                emailTypes: [],
                countries: [],
                productCategoriesFlat: [],
                productCategoriesTree: [],
                selectedProductCategoryId: null,
                updatePhoneDlg: false,
                updateAddressDlg: false,
                updateSocialDlg: false,
                updateEmailDlg: false,
                removeEmployeeDlg: false,
                selectedEmployee: null
            }
        },
        mounted() {
            this.getCompany();
            this.getRoles();
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
            this.getEmailTypes();
            this.getCountries();
            this.getProductCategoriesTree();
            this.getProductCategoriesFlat();
            this.productCategoryForm.company_id = this.$route.params.id;
            this.employeeForm.company_id = this.$route.params.id;
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
            getCompany() {
                this.companyIsLoaded = false
                axios.get(`/api/company/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.company = response.data;
                        this.$store.state.pageName = this.company.name
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;                    }

                });
            },
            getRoles() {
                axios.get('/api/roles').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.roles = response.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;                    }

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
            getProductCategoriesTree() {
                axios.get(`/api/company/${this.$route.params.id}/product_categories/tree`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.productCategoriesTree = response.data;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });

            },
            getProductCategoriesFlat() {
                axios.get(`/api/company/${this.$route.params.id}/product_categories/flat`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.productCategoriesFlat = [{
                            id: null,
                            name: `${this.$store.state.lang.lang_map.main.none}`,
                            parent_id: null
                        }].concat(response.data);
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            addProductCategory() {
                this.snackbar = false;
                axios.post(`/api/company/${this.$route.params.id}/product_category`, this.productCategoryForm).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getProductCategoriesTree();
                        this.getProductCategoriesFlat();
                        this.productCategoryForm.parent_id = '';
                        this.productCategoryForm.name = '';
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.company.product_category_created}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });

            },
            deleteProductCategory(id) {
                this.snackbar = false;
                axios.delete(`/api/product_category/${id}`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.selectedProductCategoryId = null;
                        this.getProductCategoriesTree();
                        this.getProductCategoriesFlat();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.company.product_category_deleted}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });

            },
            addProduct() {
                // console.log(this.productForm)
                axios.post('/api/product', this.productForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                    } else {
                        console.log('error')
                    }
                });
            },
            showProduct(item) {
                this.$router.push(`/product/${item.id}`)
            },
            showDeleteProductDlg(item)
            {
                this.selectedProductId = item.id;
                this.deleteProductDlg = true;
            },
            deleteProduct(productId)
            {
                axios.delete(`/api/product/${productId}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.selectedProductId = null;
                        this.snackbarMessage = this.langMap.customer.product_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
            addEmployee() {
                axios.post(`/api/company/${this.$route.params.id}/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.customer_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
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
                        this.getCompany();
                        this.snackbarMessage = this.langMap.company.company_updated;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.enableToEdit = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            cancelUpdateCompany () {
                this.getCompany();
                this.enableToEdit = false;
            },
            showRolesModal(item) {
                this.rolesDialog = true
                this.singleUserForm.user = item.user_data
                this.singleUserForm.role_ids = []
                this.singleUserForm.company_user_id = item.id
                item.roles.forEach(role => {
                    this.singleUserForm.role_ids.push(role.id)
                })
                // console.log(item);
            },
            updateRole() {
                axios.patch(`/api/roles`, this.singleUserForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.rolesDialog = false
                        this.snackbarMessage = this.langMap.company.role_updated;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }

                });
            },
            showRemoveEmployeeDlg(item)
            {
                this.selectedEmployee = item;
                this.removeEmployeeDlg = true;
            },
            removeEmployee(item) {
                axios.delete(`/api/company/employee/${item.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.rolesDialog = false
                        this.snackbarMessage = this.langMap.company.employee_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }

                });
            },
            getPhoneTypes() {
                axios.get(`/api/phone_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.phoneTypes = response.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            getSocialTypes() {
                axios.get(`/api/social_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.socialTypes = response.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            getAddressTypes() {
                axios.get(`/api/address_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.addressTypes = response.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            submitNewData(id, data, method) {
                data.entity_id = id
                this[method](data)
            },
            updateUser() {
                axios.post('/api/user', this.singleUserForm.user).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            addPhone(form) {
                axios.post('/api/phone', form).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.phone_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                    return true
                });
            },
            updatePhone() {
                axios.patch(`/api/phone/${this.phoneForm.id}`, this.phoneForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.phoneForm.id = '';
                        this.getCompany();
                        this.snackbarMessage = this.langMap.company.phone_updated;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                    return true
                });
            },
            deletePhone(id) {
                axios.delete(`/api/phone/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.phone_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            addSocial(form) {
                axios.post('/api/social', form).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.social_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            updateSocial() {
                axios.patch(`/api/social/${this.socialForm.id}`, this.socialForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.socialForm.id = '';
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.social_updated;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            deleteSocial(id) {
                axios.delete(`/api/social/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.social_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            addAddress(form) {
                axios.post('/api/address', form).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.address_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            updateAddress() {
                axios.patch(`/api/address/${this.addressForm.id}`, this.addressForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.addressForm.id = '';
                        this.addressForm.address.street = '';
                        this.addressForm.address.street2 = '';
                        this.addressForm.address.street3 = '';
                        this.addressForm.address.postal_code = '';
                        this.addressForm.address.city = '';
                        this.addressForm.address.country_id = '';
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.address_updated;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            deleteAddress(id) {
                axios.delete(`/api/address/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.address_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            changeIsActive(item) {
                let request = {}
                request.user_id = item.id
                request.is_active = item.is_active
                axios.post(`/api/user/is_active`, request).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = item.is_active ? this.langMap.company.employee_activated : this.langMap.company.employee_deactivated;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                    }
                });
            },
            sendInvite(item) {
                let request = {}
                request.user_id = item.user_data.id
                request.role_id = item.roles[0].id
                axios.post(`/api/user/invite`, request).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.invitation_sent
                        this.actionColor = 'success'
                        this.snackbar = true;
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
            },
            checkEmployeeRoleByIds(employee, ids) {
                let roleExists = false;
                ids.forEach(id => {
                    if (roleExists === false) {
                        employee.roles.forEach(role => {
                            if (roleExists === false) {
                                roleExists = role.id === id
                            }
                        })
                    }
                });
                return roleExists
            },
            editPhone(item) {
                this.updatePhoneDlg = true;

                this.phoneForm.id = item.id;
                this.phoneForm.phone = item.phone;
                this.phoneForm.phone_type = item.type ? item.type.id : 0;
            },
            editSocial(item) {
                this.updateSocialDlg = true;

                this.socialForm.id = item.id;
                this.socialForm.social_link = item.social_link;
                this.socialForm.social_type = item.type ? item.type.id : 0;
            },
            editAddress(item) {
                this.updateAddressDlg = true;

                this.addressForm.id = item.id;
                this.addressForm.address.street = item.street;
                this.addressForm.address.street2 = item.street2;
                this.addressForm.address.street3 = item.street3;
                this.addressForm.address.postal_code = item.postal_code;
                this.addressForm.address.city = item.city;
                this.addressForm.address.country_id = item.country ? item.country.id : 0;
                this.addressForm.address_type = item.type ? item.type.id : 0;
            },
            getEmailTypes() {
                axios.get(`/api/email_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.emailTypes = response.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            addEmail(form) {
                axios.post('/api/email', form).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = this.langMap.company.email_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
            updateEmail() {
                axios.patch(`/api/email/${this.emailForm.id}`, this.emailForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.emailForm.id = '';
                        this.getCompany();
                        this.snackbarMessage = this.langMap.company.email_updated;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                    return true
                });
            },
            deleteEmail(id) {
                axios.delete(`/api/email/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.emailForm.email = ''
                        this.snackbarMessage = this.langMap.company.email_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
            editEmail(item) {
                this.updateEmailDlg = true;

                this.emailForm.id = item.id;
                this.emailForm.email = item.email;
                this.emailForm.email_type = item.type ? item.type.id : 0;
            }
        },
        watch: {
            companyUpdates(value) {
                this.companyIsLoaded = true;
                // console.log(this.singleUserForm.user);
                if (this.singleUserForm.user) {
                    this.singleUserForm.user = this.company.employees.find(x => x.user_id === this.singleUserForm.user.id).user_data;
                }
            }
        },
        computed: {
            companyUpdates: function () {
                return this.company
            },
        }
    }
</script>
