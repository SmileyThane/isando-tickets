<template>
    <v-container fluid>
        <v-snackbar :bottom="true" :right="true" v-model="snackbar" :color="actionColor">
            {{ snackbarMessage }}
        </v-snackbar>
        <div class="row">
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar :color="themeColor" dark dense flat>
                        <v-toolbar-title>{{langMap.system_settings.display}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateCompanySettings">
                            {{langMap.main.update}}
                        </v-btn>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-label>{{langMap.system_settings.navbar_style}}</v-label>
                                    <v-radio-group dense v-model="companySettings.navbar_style" :readonly="!enableToEdit">
                                        <v-radio :color="themeColor" :label="langMap.system_settings.navbar_no_logo" :value="1"></v-radio>
                                        <v-radio :color="themeColor" :label="langMap.system_settings.navbar_logo" :value="2"></v-radio>
                                        <v-radio :color="themeColor" :label="langMap.system_settings.navbar_logo_2" :value="3"></v-radio>
                                        <v-radio :color="themeColor" :label="langMap.system_settings.navbar_only_logo" :value="4"></v-radio>
                                    </v-radio-group>
                                </v-col>
                            </v-row>
                        </v-form>

                        <v-spacer>&nbsp;</v-spacer>

                        <v-form>
                            <v-row>
                                <v-col class="col-md-4">
                                    <v-img contain :src="companyLogo"></v-img>
                                </v-col>
                                <v-col class="col-md-8">
                                    <v-label>{{langMap.company.logo}}</v-label>
                                    <v-file-input
                                        :color="themeColor"
                                        :placeholder="langMap.company.logo"
                                        accept="image/*"
                                        show-size
                                        prepend-icon="mdi-camera"
                                        :disabled="!enableToEdit"
                                        dense
                                        v-model="companyNewLogo"
                                    >
                                    </v-file-input>
                                </v-col>
                            </v-row>
                        </v-form>

                        <v-spacer>&nbsp;</v-spacer>

                        <v-form>
                            <v-row>
                                <v-col class="col-md-6">
                                    <v-label>{{langMap.system_settings.company_theme_color}}</v-label>
                                    <v-color-picker
                                        dot-size="25"
                                        mode="hexa"
                                        v-model="companySettings.theme_color"
                                        :disabled="!enableToEdit"
                                    ></v-color-picker>
                                </v-col>
                                <v-col class="col-md-6">
                                    <v-checkbox
                                        :color="themeColor"
                                        :readonly="!enableToEdit"
                                        :label="langMap.system_settings.override_user_theme_color"
                                        :value="true"
                                        v-model="companySettings.override_user_theme"
                                    >
                                    </v-checkbox>
                                    <p>{{ langMap.system_settings.override_user_theme_color_hint }}</p>
                                </v-col>
                            </v-row>
                        </v-form>

                        <v-spacer>&nbsp;</v-spacer>

                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-label>{{langMap.system_settings.ticket_number_format}}</v-label>
                                    <v-row>
                                        <v-col class="col-md-3">
                                            <v-text-field
                                                :label="langMap.system_settings.ticket_number_format_prefix"
                                                :color="themeColor"
                                                dense
                                                v-model="ticketNumberFormat.prefix"
                                                :readonly="!enableToEdit"
                                                counter="6"
                                                maxlength="6"
                                                @change="updateTicketNumber()"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col class="col-md-2">
                                            <v-select
                                                :label="langMap.system_settings.ticket_number_format_delimiter"
                                                :color="themeColor"
                                                dense
                                                v-model="ticketNumberFormat.delimiter1"
                                                :items="ticketNumberFormat.delimiters"
                                                :readonly="!enableToEdit"
                                                @change="updateTicketNumber()"
                                            >
                                            </v-select>
                                        </v-col>
                                        <v-col class="col-md-3">
                                            <v-select
                                                :label="langMap.system_settings.ticket_number_format_date"
                                                :color="themeColor"
                                                dense
                                                v-model="ticketNumberFormat.date"
                                                :items="ticketNumberFormat.dates"
                                                :readonly="!enableToEdit"
                                                @change="updateTicketNumber()"
                                            >
                                            </v-select>
                                        </v-col>
                                        <v-col class="col-md-2">
                                            <v-select
                                                :label="langMap.system_settings.ticket_number_format_delimiter"
                                                :color="themeColor"
                                                dense
                                                v-model="ticketNumberFormat.delimiter2"
                                                :items="ticketNumberFormat.delimiters"
                                                :readonly="!enableToEdit"
                                                @change="updateTicketNumber()"
                                            >
                                            </v-select>
                                        </v-col>
                                        <v-col class="col-md-2">
                                            <v-select
                                                :label="langMap.system_settings.ticket_number_format_sequence"
                                                :color="themeColor"
                                                dense
                                                v-model="ticketNumberFormat.suffix"
                                                :items="ticketNumberFormat.suffixes"
                                                :readonly="!enableToEdit"
                                                @change="updateTicketNumber()"
                                            >
                                            </v-select>
                                        </v-col>
                                    </v-row>
                                    <p>{{langMap.system_settings.ticket_number_example}} <strong>{{ticketNumber}}</strong></p>
                                    <p>{{langMap.system_settings.ticket_number_format_help}}</p>
                                </v-col>
                            </v-row>
                        </v-form>

                        <v-spacer>&nbsp;</v-spacer>

                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-label>{{langMap.system_settings.default_timezone}}</v-label>
                                    <v-select claass="mx-4" :color="themeColor" dense
                                              v-model="companySettings.timezone"
                                              :items="timezones"
                                              item-text="text"
                                              item-value="id"
                                              :item-color="themeColor"
                                              :readonly="!enableToEdit"
                                    >
                                    </v-select>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar dense :color="themeColor" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.languages}}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in languages"
                                                :key="item.id"
                                            >
                                                <v-list-item-action>
                                                    <v-checkbox :color="themeColor" v-model="companyLanguages" :value="item.id" v-on:change="updateCompanyLanguages(item.id)"></v-checkbox>
                                                </v-list-item-action>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar dense :color="themeColor" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.countries}}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader style="max-height: 15em" class="overflow-y-auto">
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in countries"
                                                :key="item.id"
                                            >
                                                <v-list-item-action>
                                                    <v-checkbox :color="themeColor" v-model="companyCountries" :value="item.id" v-on:change="updateCompanyCountries(item.id)"></v-checkbox>
                                                </v-list-item-action>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="'('+item.iso_3166_2+') '+localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar dense :color="themeColor" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.phone_types}}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in phoneTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="checkRoleByIds([1, 2, 3])">
                                                    <v-icon small @click="showUpdateTypeDialog(item, phoneIcons,'updatePhoneType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="checkRoleByIds([1, 2, 3])">
                                                    <v-icon small @click="deletePhoneType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels multiple v-if="checkRoleByIds([1, 2, 3])">
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
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-text-field :color="themeColor" :item-color="themeColor" v-model="phoneTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-text-field :color="themeColor" :item-color="themeColor" v-model="phoneTypeForm.name_de" :label="langMap.main.name_de" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-2" class="pa-1">
                                                            <v-select :color="themeColor" :item-color="themeColor"
                                                                        v-model="phoneTypeForm.icon" :items="phoneIcons"
                                                                        dense :label="langMap.main.icon">
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn dark fab right bottom small :color="themeColor" @click="submitNewData(phoneTypeForm, 'addPhoneType')">
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

                <v-card class="elevation-12">
                    <v-toolbar dense :color="themeColor" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.social_types}}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in socialTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="checkRoleByIds([1, 2, 3])">
                                                    <v-icon small @click="showUpdateTypeDialog(item, socialIcons, 'updateSocialType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="checkRoleByIds([1, 2, 3])">
                                                    <v-icon small @click="deleteSocialType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels multiple v-if="checkRoleByIds([1, 2, 3])">
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
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-text-field :color="themeColor" :item-color="themeColor" v-model="socialTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-text-field :color="themeColor" :item-color="themeColor" v-model="socialTypeForm.name_de" :label="langMap.main.name_de" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-2" class="pa-1">
                                                            <v-select :color="themeColor" :item-color="themeColor"
                                                                        v-model="socialTypeForm.icon" :items="socialIcons"
                                                                        dense :label="langMap.main.icon">
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn dark fab right bottom small :color="themeColor" @click="submitNewData(socialTypeForm, 'addSocialType')">
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

                <v-card class="elevation-12">
                    <v-toolbar dense :color="themeColor" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.address_types}}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in addressTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="checkRoleByIds([1, 2, 3])">
                                                    <v-icon small @click="showUpdateTypeDialog(item, addressIcons, 'updateAddressType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="checkRoleByIds([1, 2, 3])">
                                                <v-icon small @click="deleteAddressType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels multiple v-if="checkRoleByIds([1, 2, 3])">
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
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-text-field :color="themeColor" :item-color="themeColor" v-model="addressTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-text-field :color="themeColor" :item-color="themeColor" v-model="addressTypeForm.name_de" :label="langMap.main.name_de" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-2" class="pa-1">
                                                            <v-select :color="themeColor" :item-color="themeColor"
                                                                        v-model="addressTypeForm.icon" :items="addressIcons"
                                                                        dense :label="langMap.main.icon">
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn dark fab right bottom small :color="themeColor" @click="submitNewData(addressTypeForm, 'addAddressType')">
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

                <v-card class="elevation-12">
                    <v-toolbar dense :color="themeColor" dark flat>
                        <v-toolbar-title>{{langMap.system_settings.email_types}}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in emailTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="checkRoleByIds([1, 2, 3])">
                                                    <v-icon small @click="showUpdateTypeDialog(item, emailIcons,'updateEmailType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="checkRoleByIds([1, 2, 3])">
                                                    <v-icon small @click="deleteEmailType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels multiple v-if="checkRoleByIds([1, 2, 3])">
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.system_settings.new_email_type}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-text-field :color="themeColor" :item-color="themeColor" v-model="emailTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-5" class="pa-1">
                                                            <v-text-field :color="themeColor" :item-color="themeColor" v-model="emailTypeForm.name_de" :label="langMap.main.name_de" dense></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-2" class="pa-1">
                                                            <v-select :color="themeColor" :item-color="themeColor"
                                                                      v-model="emailTypeForm.icon" :items="emailIcons"
                                                                      dense :label="langMap.main.icon">
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn dark fab right bottom small :color="themeColor" @click="submitNewData(emailTypeForm, 'addEmailType')">
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
                                <v-col cols="md-5" class="pa-1">
                                    <v-text-field :color="themeColor" :item-color="themeColor" v-model="updateTypeForm.name" :label="langMap.main.name" dense></v-text-field>
                                </v-col>
                                <v-col cols="md-5" class="pa-1">
                                    <v-text-field :color="themeColor" :item-color="themeColor" v-model="updateTypeForm.name_de" :label="langMap.main.name_de" dense></v-text-field>
                                </v-col>
                                <v-col cols="md-2" class="pa-1">
                                    <v-select :color="themeColor" :item-color="themeColor"
                                                v-model="updateTypeForm.icon" :items="updateTypeForm.icons"
                                                dense :label="langMap.main.icon">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon small v-text="data.item"></v-icon>
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon small v-text="data.item"></v-icon>
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateTypeDialog=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateTypeDialog=false; submitNewData(updateTypeForm, updateTypeForm.method)">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>

    </v-container>
</template>


<script>
    import EventBus from '../../components/EventBus';
    import moment from 'moment';

    export default {
        data() {
            return {
                enableToEdit: false,
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                tooltip: false,
                updateTypeDialog: false,
                errors: [],
                langMap: this.$store.state.lang.lang_map,
                themeColor: this.$store.state.themeColor,
                company: {
                    name: '',
                    company_number: '',
                    description: ''
                },
                addressTypeForm: {
                    entity_id: '',
                    entity_type: 'App\\Company',
                    name: '',
                    name_de: '',
                    icon: 'mdi-phone'
                },
                addressIcons: [
                    'mdi-home',
                    'mdi-office-building-marker',
                    'mdi-post',
                    'mdi-mailbox',
                    'mdi-map-marker'
                ],
                phoneTypeForm: {
                    entity_id: '',
                    entity_type: 'App\\Company',
                    name: '',
                    name_de: '',
                    icon: 'mdi-phone'
                },
                phoneIcons: [
                    'mdi-phone',
                    'mdi-phone-classic',
                    'mdi-fax',
                    'mdi-deskphone',
                    'mdi-cellphone-basic',
                    'mdi-cellphone-iphone',
                    'mdi-skype',
                    'mdi-whatsapp'
                ],
                socialTypeForm: {
                    entity_id: '',
                    entity_type: 'App\\Company',
                    name: '',
                    name_de: '',
                    icon: 'mdi-web'
                },
                socialIcons: [
                    'mdi-facebook',
                    'mdi-linkedin',
                    'mdi-instagram',
                    'mdi-twitter',
                    'mdi-pinterest',
                    'mdi-web',
                    'mdi-email'
                ],
                emailTypeForm: {
                    entity_id: '',
                    entity_type: 'App\\Company',
                    name: '',
                    name_de: '',
                    icon: 'mdi-email'
                },
                emailIcons: [
                    'mdi-email',
                    'mdi-email-alert',
                    'mdi-email-box',
                    'mdi-email-check',
                    'mdi-email-lock',
                    'mdi-email-mark-as-unread',
                    'mdi-email-multiple',
                    'mdi-email-newsletter',
                    'mdi-email-open',
                    'mdi-email-outline',
                    'mdi-email-variant'
                ],
                updateTypeForm: {
                    entity_id: this.$route.params.id,
                    entity_type: 'App\\Company',
                    name: '',
                    name_de: '',
                    icon: '',
                },
                phoneTypes: [],
                addressTypes: [],
                socialTypes: [],
                emailTypes: [],
                countries: [],
                companyCountries:[],
                languages: [],
                companyLanguages: [],
                companySettings: {
                    navbar_style: '',
                    ticket_number_format: '',
                    timezone: '',
                    theme_color: '',
                    override_user_theme: false
                },
                companyLogo: '',
                companyNewLogo: null,
                timezones:[],
                ticketNumberFormat: {
                    prefix: '',
                    delimiter1: '-',
                    date: 'YYYYMMDD',
                    delimiter2: '-',
                    suffix: '###',
                    delimiters: [
                        '', '.', '-', '_', '|', '!', '/', '\\'
                    ],
                    dates: [
                        'YYYYMMDD',
                        'YYMMDD',
                        'YYYY-MM-DD',
                        'YY-MM-DD',
                        'YYYY/MM/DD',
                        'YY/MM/DD',
                        'YYYY.MM.DD',
                        'YY.MM.DD',
                        'DD-MM-YYYY',
                        'DD-MM-YY',
                        'DD/MM/YYYY',
                        'DD/MM/YY',
                        'DD.MM.YYYY',
                        'DD.MM.YY',
                        'DD/MM/YYYY',
                        'DD/MM/YY',
                        'MM-DD-YYYY',
                        'MM-DD-YY',
                        'MM/DD/YYYY',
                        'MM/DD/YY',
                        'MM.DD.YYYY',
                        'MM.DD.YY'
                    ],
                    suffixes: [
                        '#',
                        '##',
                        '###',
                        '####',
                        '#####',
                        '######'
                    ]
                },
                ticketNumber: ''
            }
        },
        mounted() {
            this.getCompany();
            this.getCompanyLogo();
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
            this.getEmailTypes();
            this.getCountries();
            this.getCompanyCountries();
            this.getLanguages();
            this.getCompanyLanguages();
            this.getCompanySettings();
            this.getTimezones();
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            });
        },
        methods: {
            getCompany() {
                axios.get(`/api/company/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.company = response.data;
                    } else {
                        console.log('error')
                    }

                });
            },
            getCompanyLogo() {
                axios.get('/api/main_company_logo').then(response => {
                    response = response.data
                    if (response.success === true) {
                        if (response.data) {
                            this.companyLogo = response.data;
                        } else {
                            this.companyLogo = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAoMBgDTD2qgAAAAASUVORK5CYII=';
                        }
                    } else {
                        console.log('error')
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
            getLanguages() {
                axios.get('/api/lang/all').then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.languages = response.data;
                    }
                });
            },
            getCompanyLanguages() {
                axios.get('/api/lang/company').then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.companyLanguages = response.data;
                    }
                });
            },
            updateCompanyLanguages(id) {
                if (this.companyLanguages.includes(id)) {
                    axios.post('/api/lang/company', {'language_id': id}).then(response => {
                        response = response.data;
                        if (response.success !== true) {
                            this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                            this.errorType = 'error';
                            this.alert = true;
                            this.getCompanyLanguages();
                        }
                        return true;
                    });
                } else {
                    axios.delete(`/api/lang/${id}/company`).then(response => {
                        response = response.data;
                        if (response.success !== true) {
                            this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                            this.errorType = 'error';
                            this.alert = true;
                            this.getCompanyLanguages();
                        }
                        return true;
                    });
                }
            },
            localized(item, field = 'name') {
                let locale = this.$store.state.lang.locale.replace(/^([^_]+).*$/, '$1');
                return item[field + '_' + locale] ? item[field + '_' + locale] : item[field];
            },
            getCountries() {
                axios.get('/api/countries/all').then(response => {
                    response = response.data
                    if (response.success === true) {
                            this.countries = response.data
                    }
                });
            },
            getCompanyCountries() {
                axios.get('/api/countries/company').then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.companyCountries= response.data;
                    }
                });
            },
            updateCompanyCountries(id) {
                if (this.companyCountries.includes(id)) {
                    axios.post('/api/country/company', {'country_id': id}).then(response => {
                        response = response.data;
                        if (response.success !== true) {
                            this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                            this.errorType = 'error';
                            this.alert = true;
                            this.getCompanyCountries();
                        }
                        return true;
                    });
                } else {
                    axios.delete(`/api/country/${id}/company`,).then(response => {
                        response = response.data;
                        if (response.success !== true) {
                            this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                            this.errorType = 'error';
                            this.alert = true;
                            this.getCompanyCountries();
                        }
                        return true;
                    });
                }
            },
            getPhoneTypes() {
                axios.get(`/api/phone_types`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.phoneTypes = response.data
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
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
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                });
            },
            getEmailTypes() {
                axios.get(`/api/email_types`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.emailTypes = response.data
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            },
            addEmailType(form) {
                axios.post('/api/email_type', form).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getEmailTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.email_type_created}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                    return true
                });
            },
            updateEmailType(form) {
                axios.patch(`/api/email_type/${form.id}`, form).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getEmailTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.email_type_updated}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                    return true
                });
            },
            deleteEmailType(id) {
                axios.delete(`/api/email_type/${id}`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getEmailTypes();
                        this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.emailz_type_deleted}`;
                        this.actionColor = 'success';
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;

                    }
                });
            },
            submitNewData(data, method) {
                this[method](data);
            },
            showUpdateTypeDialog(data, icons, method) {
                this.updateTypeForm.id = data.id;
                this.updateTypeForm.name = data.name;
                this.updateTypeForm.name_de = data.name_de;
                this.updateTypeForm.icon = data.icon;
                this.updateTypeForm.title = '';
                this.updateTypeForm.method = method;
                this.updateTypeForm.icons = icons;
                this.updateTypeForm.entity_id = this.$route.params.id;
                this.updateTypeForm.entity_type = 'App\\Company';

                this.updateTypeDialog = true;
            },
            getCompanySettings() {
                axios.get(`/api/main_company_settings`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.companySettings['navbar_style'] = response.data.hasOwnProperty('navbar_style') ? response.data.navbar_style : 1;
                        this.companySettings['ticket_number_format'] = response.data.hasOwnProperty('ticket_number_format') ? response.data.ticket_number_format : this.company.name.replaceAll(' ', '').substr(0, 6)+'｜-｜YYYYMMDD｜-｜###';
                        this.companySettings['timezone'] = response.data.hasOwnProperty('timezone') ? response.data.timezone : 35;
                        this.companySettings['theme_color'] = response.data.hasOwnProperty('theme_color') ? response.data.theme_color : '#4caf50';
                        this.companySettings['override_user_theme'] = response.data.hasOwnProperty('override_user_theme') ? response.data.override_user_theme : false;

                        let fmt = this.companySettings.ticket_number_format.split('｜');
                        let pos = ['prefix', 'delimiter1', 'date', 'delimiter2', 'suffix'];
                        fmt[0] = fmt[0] ? fmt[0].replaceAll(' ', '').substr(0, 6) : '';
                        for (let i = 0; i < fmt.length; i++) {
                            this.ticketNumberFormat[pos[i]] = fmt[i].toLocaleUpperCase();
                        }

                        this.updateTicketNumber();
                    }
                });
            },
            updateCompanySettings() {
                this.enableToEdit = false;
                if (this.companyNewLogo) {
                    let formData = new FormData();
                    formData.append('logo', this.companyNewLogo);
                    axios.post(
                        '/api/main_company_logo',
                        formData, {
                            headers: {'Content-Type': 'multipart/form-data' }}
                    ).then(response => {
                        this.companyNewLogo = null;

                        response = response.data;
                        if (response.success === true) {
                            this.companyLogo = response.data.logo_url;
                            EventBus.$emit('update-navbar-logo', this.companyLogo);
                        } else {
                            this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                            this.errorType = 'error';
                            this.alert = true;
                        }
                    });
                }

                this.companySettings.ticket_number_format = this.ticketNumberFormat.prefix.toLocaleUpperCase() + '｜' +
                    this.ticketNumberFormat.delimiter1 + '｜' + this.ticketNumberFormat.date + '｜' +
                    this.ticketNumberFormat.delimiter2 + '｜' + this.ticketNumberFormat.suffix;

                axios.post('/api/main_company_settings', this.companySettings).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        window.location.reload()
                } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                        this.getCompanySettings();
                    }
                    return true;
                });
            },
            getTimezones() {
                axios.get(`/api/time_zones`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.timezones = response.data;
                    }
                });
            },
            updateTicketNumber() {
                this.ticketNumber = this.ticketNumberFormat.prefix.toLocaleUpperCase() +
                    this.ticketNumberFormat.delimiter1 + moment().format(this.ticketNumberFormat.date) +
                    this.ticketNumberFormat.delimiter2 + '1'.padStart(this.ticketNumberFormat.suffix.length, '0');
            }
        }
    }
</script>
