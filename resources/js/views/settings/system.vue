<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>
        <div class="row">
            <div class="col-md-6">
                <v-card class="elevation-12 without-bottom">
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.system_settings.display
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" :color="themeFgColor" @click="enableToEdit = true">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEdit" :color="themeFgColor"
                               :style="`color: ${themeBgColor}; margin-right: 10px;`"
                               @click="cancelUpdateCompanySettings">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" :color="themeFgColor" :style="`color: ${themeBgColor};`"
                               @click="updateCompanySettings">
                            {{ langMap.main.update }}
                        </v-btn>
                    </v-toolbar>

                    <v-card-text>
                        <template v-if="$helpers.auth.checkPermissionByIds([94])">
                            <v-form>
                                <v-row>
                                    <v-col class="col-md-12">
                                        <v-label>{{ langMap.system_settings.navbar_style }}</v-label>
                                        <v-radio-group v-model="companySettings.navbar_style" :readonly="!enableToEdit"
                                                       dense>
                                            <v-radio :color="themeBgColor"
                                                     :label="langMap.system_settings.navbar_no_logo"
                                                     :value="1"></v-radio>
                                            <v-radio :color="themeBgColor" :label="langMap.system_settings.navbar_logo"
                                                     :value="2"></v-radio>
                                            <v-radio :color="themeBgColor"
                                                     :label="langMap.system_settings.navbar_logo_2"
                                                     :value="3"></v-radio>
                                            <v-radio :color="themeBgColor"
                                                     :label="langMap.system_settings.navbar_only_logo"
                                                     :value="4"></v-radio>
                                        </v-radio-group>
                                    </v-col>
                                </v-row>
                            </v-form>
                            <v-spacer v-if="companySettings.navbar_style !== 1">&nbsp;</v-spacer>
                            <v-form>

                                <v-row>
                                    <v-col v-show="companySettings.navbar_style !== 1"
                                           :class="companySettings.navbar_style === 4 ? 'col-md-12' : 'col-md-4'">
                                        <v-img
                                            v-if="companyLogo"
                                            :src="companyLogo"
                                            contain
                                            max-height="15em"
                                            style="z-index: 0"
                                        >
                                            <v-file-input
                                                v-show="companySettings.navbar_style !== 1"
                                                v-model="companyNewLogo"
                                                :color="themeBgColor"
                                                :disabled="!enableToEdit"
                                                accept="image/*"
                                                dense
                                                prepend-icon="mdi-camera"
                                                style="z-index: 2; max-width: 1em;"
                                            >
                                            </v-file-input>
                                        </v-img>
                                        <v-label v-else>{{ langMap.company.logo }}</v-label>
                                    </v-col>
                                    <v-col v-show="companySettings.navbar_style !== 4" class="col-md-4">
                                        <span v-if="companySettings.navbar_style !== 1">
                                            <br>
                                            <br>
                                        </span>
                                        <v-text-field
                                            v-model="company.first_alias"
                                            :color="themeBgColor"
                                            :label="langMap.system_settings.text"
                                            :readonly="!enableToEdit"
                                            counter="25"
                                            dense
                                            maxlength="30"
                                        ></v-text-field>
                                        <v-text-field
                                            v-show="companySettings.navbar_style === 3"
                                            v-model="company.second_alias"
                                            :color="themeBgColor"
                                            :label="langMap.system_settings.text + ' 2'"
                                            :readonly="!enableToEdit"
                                            counter="25"
                                            dense
                                            maxlength="30"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-form>

                            <v-spacer>&nbsp;</v-spacer>

                            <v-form>
                                <v-row>
                                    <v-col cols="6">
                                        <label>{{ langMap.system_settings.company_theme_fg_color }}</label>
                                        <v-color-picker
                                            v-model="companySettings.theme_fg_color"
                                            :disabled="!enableToEdit"
                                            dot-size="25"
                                            mode="hexa"
                                        />
                                        <v-checkbox
                                            v-model="autoFgColor"
                                            :color="themeBgColor"
                                            :label="langMap.system_settings.automate_theme_fg_color"
                                            :readonly="!enableToEdit"
                                            :value="true"
                                            @change="setThemeFgColor"
                                        />
                                        <p>{{ langMap.system_settings.automate_theme_fg_color_hint }}</p>

                                    </v-col>
                                    <v-col cols="6">
                                        <label>{{ langMap.system_settings.company_theme_bg_color }}</label>
                                        <v-color-picker
                                            v-model="companySettings.theme_bg_color"
                                            :disabled="!enableToEdit"
                                            dot-size="25"
                                            mode="hexa"
                                        />

                                        <p>{{ langMap.system_settings.override_user_theme_color_hint }}</p>

                                    </v-col>
                                </v-row>
                            </v-form>

                            <v-spacer>&nbsp;</v-spacer>
                        </template>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-label>{{ langMap.system_settings.ticket_number_format }}</v-label>
                                    <v-row>
                                        <v-col class="col-md-3">
                                            <v-text-field
                                                v-model="ticketNumberFormat.prefix"
                                                :color="themeBgColor"
                                                :label="langMap.system_settings.ticket_number_format_prefix"
                                                :readonly="!enableToEdit"
                                                counter="6"
                                                dense
                                                maxlength="6"
                                                @change="updateTicketNumber()"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col class="col-md-2">
                                            <v-select
                                                v-model="ticketNumberFormat.delimiter1"
                                                :color="themeBgColor"
                                                :items="ticketNumberFormat.delimiters"
                                                :label="langMap.system_settings.ticket_number_format_delimiter"
                                                :readonly="!enableToEdit"
                                                dense
                                                @change="updateTicketNumber()"
                                            >
                                            </v-select>
                                        </v-col>
                                        <v-col class="col-md-3">
                                            <v-select
                                                v-model="ticketNumberFormat.date"
                                                :color="themeBgColor"
                                                :items="ticketNumberFormat.dates"
                                                :label="langMap.system_settings.ticket_number_format_date"
                                                :readonly="!enableToEdit"
                                                dense
                                                @change="updateTicketNumber()"
                                            >
                                            </v-select>
                                        </v-col>
                                        <v-col class="col-md-2">
                                            <v-select
                                                v-model="ticketNumberFormat.delimiter2"
                                                :color="themeBgColor"
                                                :items="ticketNumberFormat.delimiters"
                                                :label="langMap.system_settings.ticket_number_format_delimiter"
                                                :readonly="!enableToEdit"
                                                dense
                                                @change="updateTicketNumber()"
                                            >
                                            </v-select>
                                        </v-col>
                                        <v-col class="col-md-2">
                                            <v-select
                                                v-model="ticketNumberFormat.suffix"
                                                :color="themeBgColor"
                                                :items="ticketNumberFormat.suffixes"
                                                :label="langMap.system_settings.ticket_number_format_sequence"
                                                :readonly="!enableToEdit"
                                                dense
                                                @change="updateTicketNumber()"
                                            >
                                            </v-select>
                                        </v-col>
                                    </v-row>
                                    <p>{{ langMap.system_settings.ticket_number_example }}
                                        <strong>{{ ticketNumber }}</strong></p>
                                    <p>{{ langMap.system_settings.ticket_number_format_help }}</p>
                                </v-col>
                            </v-row>
                        </v-form>

                        <v-spacer>&nbsp;</v-spacer>

                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-label>{{ langMap.system_settings.default_timezone }}</v-label>
                                    <v-select v-model="companySettings.timezone" :color="themeBgColor"
                                              :item-color="themeBgColor"
                                              :items="timezones"
                                              :readonly="!enableToEdit"
                                              claass="mx-4"
                                              dense
                                              item-text="text"
                                              item-value="id"
                                    >
                                    </v-select>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.system_settings.languages
                            }}
                        </v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeBgColor">
                                            <v-list-item
                                                v-for="(item, i) in languages"
                                                :key="item.id"
                                            >
                                                <v-list-item-action>
                                                    <v-checkbox v-model="companyLanguages" :color="themeBgColor"
                                                                :value="item.id"
                                                                v-on:change="updateCompanyLanguages(item.id)"></v-checkbox>
                                                </v-list-item-action>
                                                <v-list-item-content>
                                                    <v-list-item-title
                                                        v-text="$helpers.i18n.localized(item)"></v-list-item-title>
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
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.system_settings.countries
                            }}
                        </v-toolbar-title>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12" style="max-height: 20em">
                                    <perfect-scrollbar style="height: 100%">
                                        <v-list dense subheader>
                                            <v-list-item-group :color="themeBgColor">
                                                <v-list-item
                                                    v-for="(item, i) in countries"
                                                    :key="item.id"
                                                >
                                                    <v-list-item-action>
                                                        <v-checkbox v-model="companyCountries" :color="themeBgColor"
                                                                    :value="item.id"
                                                                    v-on:change="updateCompanyCountries(item.id)"></v-checkbox>
                                                    </v-list-item-action>
                                                    <v-list-item-content>
                                                        <v-list-item-title
                                                            v-text="'('+item.iso_3166_2+') '+$helpers.i18n.localized(item)"></v-list-item-title>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list-item-group>
                                        </v-list>
                                    </perfect-scrollbar>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">
                            {{ langMap.system_settings.employee_number_format }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" :color="themeFgColor" @click="enableToEdit = true">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px"
                               @click="cancelUpdateCompanySettings">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateCompanySettings">
                            {{ langMap.main.update }}
                        </v-btn>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-checkbox
                                        v-model="employeeNumberFormat.auto"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.employee_number_automatic"
                                        :readonly="!enableToEdit"
                                        value="1"
                                        @change="updateEmployeeNumber()"
                                    >
                                    </v-checkbox>
                                    <p>{{ langMap.system_settings.employee_number_automatic_hint }}</p>

                                </v-col>
                                <v-col class="col-md-4">
                                    <v-text-field
                                        v-model.trim="employeeNumberFormat.prefix"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.employee_number_format_prefix"
                                        :readonly="!enableToEdit || employeeNumberFormat.auto !== '1'"
                                        counter="6"
                                        dense
                                        maxlength="6"
                                        @change="updateEmployeeNumber()"
                                    ></v-text-field>
                                </v-col>
                                <v-col class="col-md-4">
                                    <v-text-field
                                        v-model.number="employeeNumberFormat.start"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.employee_number_format_start"
                                        :readonly="!enableToEdit || employeeNumberFormat.auto !== '1'"
                                        dense
                                        @change="updateEmployeeNumber()"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col class="col-md-4">
                                    <v-text-field
                                        v-model.number="employeeNumberFormat.size"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.employee_number_format_size"
                                        :readonly="!enableToEdit || employeeNumberFormat.auto !== '1'"
                                        dense
                                        @change="updateEmployeeNumber()"
                                    >
                                    </v-text-field>
                                </v-col>
                            </v-row>
                            <p>{{ langMap.system_settings.employee_number_example }}
                                <strong>{{ employeeNumber }}</strong></p>
                        </v-form>
                    </v-card-text>
                </v-card>

                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">
                            {{ langMap.system_settings.client_number_format }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" :color="themeFgColor" @click="enableToEdit = true">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px"
                               @click="cancelUpdateCompanySettings">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateCompanySettings">
                            {{ langMap.main.update }}
                        </v-btn>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-checkbox
                                        v-model="companyNumberFormat.auto"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.client_number_automatic"
                                        :readonly="!enableToEdit"
                                        value="1"
                                        @change="updateEmployeeNumber()"
                                    >
                                    </v-checkbox>
                                    <p>{{ langMap.system_settings.client_number_automatic_hint }}</p>

                                </v-col>
                                <v-col class="col-md-4">
                                    <v-text-field
                                        v-model.trim="companyNumberFormat.prefix"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.client_number_format_prefix"
                                        :readonly="!enableToEdit || companyNumberFormat.auto !== '1'"
                                        counter="6"
                                        dense
                                        maxlength="6"
                                        @change="updateEmployeeNumber()"
                                    ></v-text-field>
                                </v-col>
                                <v-col class="col-md-4">
                                    <v-text-field
                                        v-model.number="companyNumberFormat.start"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.client_number_format_start"
                                        :readonly="!enableToEdit || companyNumberFormat.auto !== '1'"
                                        dense
                                        @change="updateEmployeeNumber()"
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col class="col-md-4">
                                    <v-text-field
                                        v-model.number="companyNumberFormat.size"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.client_number_format_size"
                                        :readonly="!enableToEdit || companyNumberFormat.auto !== '1'"
                                        dense
                                        @change="updateEmployeeNumber()"
                                    >
                                    </v-text-field>
                                </v-col>
                            </v-row>
                            <p>{{ langMap.system_settings.client_number_example }}
                                <strong>{{ employeeNumber }}</strong></p>
                        </v-form>
                    </v-card-text>
                </v-card>

                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.tracking.settings.currencies
                            }}
                        </v-toolbar-title>
                    </v-toolbar>

                    <v-card-text>
                        <v-data-table
                            :headers="headers.currencies"
                            :items="$store.getters['Currencies/getCurrencies']"
                            :items-per-page="15"
                            class="elevation-1"
                            dense
                        >
                            <template v-slot:item.name="props">
                                <v-edit-dialog
                                    @cancel="saveCurrency(props.item)"
                                    @close="saveCurrency(props.item)"
                                    @open="saveCurrency(props.item)"
                                    @save="saveCurrency(props.item)"
                                >
                                    {{ props.item.name }}
                                    <template v-slot:input>
                                        <v-text-field
                                            v-model="props.item.name"
                                            :hint="langMap.tracking.settings.name"
                                            :label="langMap.tracking.settings.name"
                                            counter
                                            single-line
                                        ></v-text-field>
                                    </template>
                                </v-edit-dialog>
                            </template>
                            <template v-slot:item.slug="props">
                                <v-edit-dialog
                                    @cancel="saveCurrency(props.item)"
                                    @close="saveCurrency(props.item)"
                                    @open="saveCurrency(props.item)"
                                    @save="saveCurrency(props.item)"
                                >
                                    {{ props.item.slug }}
                                    <template v-slot:input>
                                        <v-text-field
                                            v-model="props.item.slug"
                                            :hint="langMap.tracking.settings.slug"
                                            :label="langMap.tracking.settings.slug"
                                            counter
                                            single-line
                                        ></v-text-field>
                                    </template>
                                </v-edit-dialog>
                            </template>
                            <template v-slot:item.symbol="props">
                                <v-edit-dialog
                                    @cancel="saveCurrency(props.item)"
                                    @close="saveCurrency(props.item)"
                                    @open="saveCurrency(props.item)"
                                    @save="saveCurrency(props.item)"
                                >
                                    {{ props.item.symbol }}
                                    <template v-slot:input>
                                        <v-text-field
                                            v-model="props.item.symbol"
                                            :hint="langMap.tracking.settings.symbol"
                                            :label="langMap.tracking.settings.symbol"
                                            counter
                                            single-line
                                        ></v-text-field>
                                    </template>
                                </v-edit-dialog>
                            </template>
                            <template v-slot:item.actions="props">
                                <v-btn
                                    icon
                                    :color="themeBgColor"
                                    @click="removeCurrency(props.item.id)"
                                >
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                        </v-data-table>
                        <v-expansion-panels v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])" multiple>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.tracking.settings.create_currency_title }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col class="pa-1" cols="md-4">
                                                <v-text-field
                                                    :label="langMap.tracking.settings.name"
                                                    v-model="forms.currency.name"
                                                    required
                                                ></v-text-field>
                                            </v-col>
                                            <v-col class="pa-1" cols="md-4">
                                                <v-text-field
                                                    :label="langMap.tracking.settings.slug"
                                                    v-model="forms.currency.slug"
                                                    required
                                                ></v-text-field>
                                            </v-col>
                                            <v-col class="pa-1" cols="md-4">
                                                <v-text-field
                                                    :label="langMap.tracking.settings.symbol"
                                                    v-model="forms.currency.symbol"
                                                    required
                                                ></v-text-field>
                                            </v-col>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom dark fab right small
                                                @click="createCurrency(); dialogCurrencies = false"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>

            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.system_settings.phone_types }}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeBgColor">
                                            <v-list-item
                                                v-for="(item, i) in phoneTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="$helpers.i18n.localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small
                                                            @click="showUpdateTypeDialog(item, phoneIcons,'updatePhoneType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small @click="deletePhoneType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])" multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{ langMap.system_settings.new_phone_type }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="phoneTypeForm.name"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="phoneTypeForm.name_de"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name_de"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-2">
                                                            <v-select v-model="phoneTypeForm.icon" :color="themeBgColor"
                                                                      :item-color="themeBgColor" :items="phoneIcons"
                                                                      :label="langMap.main.icon" dense>
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn :color="themeBgColor" bottom dark fab right small
                                                               @click="submitNewData(phoneTypeForm, 'addPhoneType')">
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.system_settings.social_types }}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeBgColor">
                                            <v-list-item
                                                v-for="(item, i) in socialTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="$helpers.i18n.localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small
                                                            @click="showUpdateTypeDialog(item, socialIcons, 'updateSocialType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small @click="deleteSocialType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])" multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{ langMap.system_settings.new_social_type }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="socialTypeForm.name"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="socialTypeForm.name_de"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name_de"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-2">
                                                            <v-select v-model="socialTypeForm.icon" :color="themeBgColor"
                                                                      :item-color="themeBgColor" :items="socialIcons"
                                                                      :label="langMap.main.icon" dense>
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn :color="themeBgColor" bottom dark fab right small
                                                               @click="submitNewData(socialTypeForm, 'addSocialType')">
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.system_settings.address_types }}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeBgColor">
                                            <v-list-item
                                                v-for="(item, i) in addressTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="$helpers.i18n.localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small
                                                            @click="showUpdateTypeDialog(item, addressIcons, 'updateAddressType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small @click="deleteAddressType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])" multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{ langMap.system_settings.new_address_type }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="addressTypeForm.name"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="addressTypeForm.name_de"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name_de"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-2">
                                                            <v-select v-model="addressTypeForm.icon" :color="themeBgColor"
                                                                      :item-color="themeBgColor"
                                                                      :items="addressIcons"
                                                                      :label="langMap.main.icon" dense>
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn :color="themeBgColor" bottom dark fab right small
                                                               @click="submitNewData(addressTypeForm, 'addAddressType')">
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.system_settings.email_types }}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeBgColor">
                                            <v-list-item
                                                v-for="(item, i) in emailTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="$helpers.i18n.localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small
                                                            @click="showUpdateTypeDialog(item, emailIcons,'updateEmailType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="item.id === 1">
                                                    <v-icon small :title="langMap.profile.login_email">
                                                        mdi-lock
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="item.id !== 1 && $helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small @click="deleteEmailType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])" multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{ langMap.system_settings.new_email_type }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="emailTypeForm.name"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="emailTypeForm.name_de"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name_de"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-2">
                                                            <v-select v-model="emailTypeForm.icon" :color="themeBgColor"
                                                                      :item-color="themeBgColor" :items="emailIcons"
                                                                      :label="langMap.main.icon" dense>
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn :color="themeBgColor" bottom dark fab right small
                                                               @click="submitNewData(emailTypeForm, 'addEmailType')">
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.system_settings.notification_types }}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeBgColor">
                                            <v-list-item
                                                v-for="(item, i) in notificationTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="$helpers.i18n.localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small
                                                            @click="showUpdateTypeDialog(item, notificationIcons,'updateNotificationType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small @click="deleteNotificationType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])" multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{ langMap.system_settings.new_notification_type }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="notificationTypeForm.name"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="notificationTypeForm.name_de"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name_de"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-2">
                                                            <v-select v-model="notificationTypeForm.icon"
                                                                      :color="themeBgColor"
                                                                      :item-color="themeBgColor"
                                                                      :items="notificationIcons"
                                                                      :label="langMap.main.icon" dense>
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn :color="themeBgColor" bottom dark fab right small
                                                               @click="submitNewData(notificationTypeForm, 'addNotificationType')">
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.system_settings.ticket_types }}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-list dense subheader>
                                        <v-list-item-group :color="themeBgColor">
                                            <v-list-item
                                                v-for="(item, i) in ticketTypes"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon left v-text="item.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="$helpers.i18n.localized(item)"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small
                                                            @click="showUpdateTypeDialog(item, ticketIcons,'updateTicketType')">
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="item.id === 1">
                                                    <v-icon small :title="langMap.profile.default_ticket_type">
                                                        mdi-lock
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="item.id !== 1 && $helpers.auth.checkPermissionByIds([1, 2, 3])">
                                                    <v-icon small @click="deleteTicketType(item.id)">
                                                        mdi-delete
                                                    </v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])" multiple>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{ langMap.system_settings.new_ticket_type }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="ticketTypeForm.name"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-5">
                                                            <v-text-field v-model="ticketTypeForm.name_de"
                                                                          :color="themeBgColor"
                                                                          :item-color="themeBgColor"
                                                                          :label="langMap.main.name_de"
                                                                          dense></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-2">
                                                            <v-select v-model="ticketTypeForm.icon" :color="themeBgColor"
                                                                      :item-color="themeBgColor" :items="ticketIcons"
                                                                      :label="langMap.main.icon" dense>
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon small v-text="data.item"></v-icon>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn :color="themeBgColor" bottom dark fab right small
                                                               @click="submitNewData(ticketTypeForm, 'addTicketType')">
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.tracking.settings.tags }}</v-toolbar-title>

                    </v-toolbar>

                    <v-card-text>
                        <v-data-table
                            dense
                            :headers="headers.tags"
                            :items="$store.getters['Tags/getTags']"
                            :items-per-page="15"
                            class="elevation-1"
                            single-expand
                            show-expand
                        >
                            <template v-slot:item.name="props">
                                <v-edit-dialog
                                    @save="saveTag(props.item)"
                                    @cancel="saveTag(props.item)"
                                    @open="saveTag(props.item)"
                                    @close="saveTag(props.item)"
                                >
                                    {{ props.item.name }}
                                    <template v-slot:input>
                                        <v-text-field
                                            v-model="props.item.name"
                                            :label="langMap.tracking.settings.name"
                                            :hint="langMap.tracking.settings.name"
                                            single-line
                                            counter
                                        ></v-text-field>
                                    </template>
                                </v-edit-dialog>
                            </template>
                            <template v-slot:item.color="props">
                                <v-menu
                                    v-model="colorMenu[props.item.id]"
                                    top
                                    nudge-bottom="105"
                                    nudge-left="16"
                                    :close-on-content-click="false"
                                >
                                    <template v-slot:activator="{ on }">
                                        <div
                                            v-on="on"
                                            :style="{
                                                    backgroundColor: props.item.color,
                                                    cursor: 'pointer',
                                                    height: '30px',
                                                    width: '30px',
                                                    borderRadius: colorMenu[props.item.id] ? '50%' : '4px',
                                                    transition: 'border-radius 200ms ease-in-out'
                                                }"
                                        />
                                    </template>
                                    <v-card>
                                        <v-card-text class="pa-0">
                                            <v-color-picker
                                                v-model="props.item.color"
                                                flat
                                                @input="saveTag(props.item)"
                                            />
                                        </v-card-text>
                                    </v-card>
                                </v-menu>

                            </template>
                            <template v-slot:item.actions="props">
                                <v-btn
                                    icon
                                    :color="themeBgColor"
                                    @click="removeTag(props.item.id)"
                                >
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-list
                                        dense
                                    >
                                        <v-list-item
                                            v-for="translate in item.translates"
                                            :key="translate.id"
                                            class="d-flex flex-row"
                                        >
                                            <div
                                                class="d-inline-flex flex-grow-0 mx-3"
                                                style="min-width: 100px"
                                            >
                                                {{ getLangName(translate.lang) }}
                                            </div>
                                            <div class="d-inline-flex flex-grow-1">
                                                <v-text-field
                                                    class="mt-n1"
                                                    hide-details
                                                    dense
                                                    v-model="translate.name"
                                                    @blur="addOrUpdateTranslate(item, translate.lang, translate.name)"
                                                ></v-text-field>
                                            </div>
                                        </v-list-item>
                                        <v-list-item-action v-if="filterLang(item).length > 0">
                                            <v-dialog
                                                v-model="addTranslationDialog[item.id]"
                                                width="500"
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-btn
                                                        :color="themeBgColor"
                                                        text
                                                        v-bind="attrs"
                                                        v-on="on"
                                                        @click="resetDialog()"
                                                    >
                                                        {{ langMap.tracking.settings.add_translation }}
                                                    </v-btn>
                                                </template>

                                                <v-card>
                                                    <v-card-title class="headline grey lighten-2">
                                                        {{ langMap.tracking.settings.add_translation }}
                                                    </v-card-title>

                                                    <v-card-text>
                                                        <v-select
                                                            :items="filterLang(item)"
                                                            :label="langMap.tracking.settings.language"
                                                            item-text="name"
                                                            item-value="locale"
                                                            v-model="tagTranslateForm.lang"
                                                        ></v-select>
                                                        <v-text-field
                                                            label="Tag name"
                                                            v-model="tagTranslateForm.name"
                                                        ></v-text-field>
                                                    </v-card-text>

                                                    <v-divider></v-divider>

                                                    <v-card-actions>
                                                        <v-spacer></v-spacer>
                                                        <v-btn
                                                            color="error"
                                                            text
                                                            @click="closeDialog(item.id)"
                                                        >
                                                            {{ langMap.tracking.settings.cancel }}
                                                        </v-btn>
                                                        <v-btn
                                                            color="success"
                                                            text
                                                            @click="addOrUpdateTranslate(item); closeDialog(item.id)"
                                                        >
                                                            {{ langMap.tracking.settings.add }}
                                                        </v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-dialog>
                                        </v-list-item-action>
                                    </v-list>
                                </td>
                            </template>
                        </v-data-table>
                        <v-expansion-panels v-if="$helpers.auth.checkPermissionByIds([1, 2, 3])" multiple>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.tracking.settings.create_tag_title }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col class="pa-1" cols="md-6">
                                                <v-text-field
                                                    :label="langMap.tracking.settings.name"
                                                    v-model="forms.tags.name"
                                                    required
                                                ></v-text-field>

                                            </v-col>
                                            <v-col class="pa-1" cols="md-6">
                                                <v-text-field
                                                    v-model="forms.tags.color"
                                                    hide-details
                                                    class="ma-0 pa-0"
                                                    solo
                                                    :label="langMap.tracking.settings.color"
                                                    required
                                                >
                                                    <template v-slot:append>
                                                        <v-menu
                                                            v-model="colorMenuCreate"
                                                            top
                                                            nudge-bottom="105"
                                                            nudge-left="16"
                                                            :close-on-content-click="false"
                                                        >
                                                            <template v-slot:activator="{ on }">
                                                                <div
                                                                    :style="{
                                                                backgroundColor: forms.tags.color,
                                                                cursor: 'pointer',
                                                                height: '30px',
                                                                width: '30px',
                                                                borderRadius: colorMenuCreate ? '50%' : '4px',
                                                                transition: 'border-radius 200ms ease-in-out'
                                                            }"
                                                                    v-on="on"
                                                                />
                                                            </template>
                                                            <v-card>
                                                                <v-card-text
                                                                    class="pa-0"
                                                                >
                                                                    <v-color-picker
                                                                        v-model="forms.tags.color"
                                                                        flat
                                                                    />
                                                                </v-card-text>
                                                            </v-card>
                                                        </v-menu>
                                                    </template>
                                                </v-text-field>
                                            </v-col>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom dark fab right small
                                                @click="createTag"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
            <v-dialog v-model="updateTypeDialog" max-width="600px" persistent>
                <v-card dense outlined>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.system_settings.update_type_info }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="5">
                                    <v-text-field v-model="updateTypeForm.name" :color="themeBgColor"
                                                  :item-color="themeBgColor" :label="langMap.main.name"
                                                  dense/>
                                </v-col>
                                <v-col cols="5">
                                    <v-text-field v-model="updateTypeForm.name_de" :color="themeBgColor"
                                                  :item-color="themeBgColor" :label="langMap.main.name_de"
                                                  dense/>
                                </v-col>
                                <v-col cols="2">
                                    <v-select v-model="updateTypeForm.icon" :color="themeBgColor"
                                              :item-color="themeBgColor" :items="updateTypeForm.icons"
                                              :label="langMap.main.icon" dense>
                                        <template slot="selection" slot-scope="data">
                                            <v-icon small v-text="data.item"></v-icon>
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon small v-text="data.item"></v-icon>
                                        </template>
                                    </v-select>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="red" text @click="updateTypeDialog=false">{{ langMap.main.cancel }}</v-btn>
                        <v-btn :color="themeBgColor" text
                               @click="updateTypeDialog=false; submitNewData(updateTypeForm, updateTypeForm.method)">
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>

    </v-container>
</template>


<script>
import EventBus from '../../components/EventBus';
import _ from 'lodash';

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
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            company: {
                id: '',
                name: '',
                company_number: '',
                description: '',
                first_alias: '',
                second_alias: ''
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
                'mdi-email-mark-as-unread',
                'mdi-email-multiple',
                'mdi-email-newsletter',
                'mdi-email-open',
                'mdi-email-outline',
                'mdi-email-variant'
            ],
            notificationTypeForm: {
                entity_id: '',
                entity_type: 'App\\Company',
                name: '',
                name_de: '',
                icon: 'mdi-alert'
            },
            notificationIcons: [
                'mdi-alert',
                'mdi-alert-decagram',
                'mdi-information',
                'mdi-information-variant',
                'mdi-email-newsletter',
                'mdi-email-outline',
                'mdi-message',
                'mdi-bell',
                'mdi-calendar'
            ],
            ticketTypeForm: {
                entity_id: '',
                entity_type: 'App\\Company',
                name: '',
                name_de: '',
                icon: 'mdi-file-question'
            },
            ticketIcons: [
                'mdi-alert',
                'mdi-alert-box',
                'mdi-alert-circle',
                'mdi-file-question',
                'mdi-file-question-outline',
                'mdi-cash-usd',
                'mdi-cash-usd-outline',
                'mdi-information',
                'mdi-information-outline',
                'mdi-information-variant',
                'mdi-ticket',
                'mdi-ticket-account',
                'mdi-ticket-confirmation',
                'mdi-ticket-confirmation-outline',
                'mdi-ticket-percent-outline',
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
            notificationTypes: [],
            ticketTypes: [],
            countries: [],
            companyCountries: [],
            languages: [],
            companyLanguages: [],
            companySettings: {
                navbar_style: '',
                ticket_number_format: '',
                timezone: '',
                theme_fg_color: '',
                theme_bg_color: '',
                override_user_theme: true,
                employee_number_format: ''
            },
            autoFgColor: false,
            companyLogo: '',
            companyNewLogo: null,
            timezones: [],
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
            ticketNumber: '',
            employeeNumberFormat: {
                auto: false,
                prefix: '',
                size: 8,
                start: 50000
            },
            companyNumberFormat: {
                auto: false,
                prefix: '',
                size: 8,
                start: 50000
            },
            employeeNumber: '',
            headers: {
                tags: [
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.tag_name,
                        align: 'start',
                        sortable: true,
                        value: 'name',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.color,
                        sortable: false,
                        value: 'color',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.actions,
                        sortable: false,
                        value: 'actions',
                    }
                ],
                services: [
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.service_name,
                        align: 'start',
                        sortable: true,
                        value: 'name',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.actions,
                        sortable: false,
                        value: 'actions',
                    }
                ],
                currencies: [
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.currency_name,
                        align: 'start',
                        sortable: true,
                        value: 'name',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.currency_slug,
                        align: 'start',
                        sortable: true,
                        value: 'slug',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.currency_symbol,
                        align: 'start',
                        sortable: true,
                        value: 'symbol',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.actions,
                        sortable: false,
                        value: 'actions',
                    }
                ]
            },
            forms: {
                tags: {
                    name: '',
                    color: this.$helpers.color.genRandomColor()
                },
                services: {
                    name: ''
                },
                currency: {
                    name: '',
                    slug: '',
                    symbol: ''
                }
            },
            colorMenuCreate: false,
            colorMenu: {},
            searchService: null,
            searchCurrency: null,
            tagTranslateForm: {
                lang: null,
                name: ''
            },
            addTranslationDialog: {},
            searchTag: null,
            dialogTags: false,
            dialogServices: false,
            dialogCurrencies: false,
        }
    },
    created () {
        this.debounceGetTags = _.debounce(this.__getTags, 1000);
        this.debounceGetServices = _.debounce(this.__getServices, 1000);
        this.debounceGetCurrencies = _.debounce(this.__getCurrencies, 1000);
        this.debounceGetLanguages = _.debounce(this.__getLanguages, 1000);
    },
    mounted() {
        this.debounceGetLanguages();
        this.debounceGetTags();
        this.debounceGetServices();
        this.debounceGetCurrencies();
        this.getCompany();
        this.getCompanyLogo();
        this.getPhoneTypes();
        this.getAddressTypes();
        this.getSocialTypes();
        this.getEmailTypes();
        this.getNotificationTypes();
        this.getTicketTypes();
        this.getCountries();
        this.getCompanyCountries();
        this.getLanguages();
        this.getCompanyLanguages();
        this.getCompanySettings();
        this.getTimezones();
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },

    watch: {
        companyNewLogo(val) {
            if (this.companyNewLogo !== null) {
                this.companyLogo = URL.createObjectURL(this.companyNewLogo)
            }
        }
    },
    methods: {
        __getLanguages() {
            this.$store.dispatch('Languages/getLanguageList');
        },
        __getTags() {
            this.$store.dispatch('Tags/getTagList', { search: this.searchTag });
            this.$store.getters['Tags/getTags'].map(i => {
                this.addTranslationDialog[i.id] = false;
            });
        },
        __getServices() {
            this.$store.dispatch('Services/getServicesList', { search: this.searchService });
        },
        __getCurrencies() {
            this.$store.dispatch('Currencies/getCurrencyList', { search: this.searchCurrency });
        },
        getCompany() {
            axios.get(`/api/main_company/name`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.company = response.data;
                } else {
                    console.log('error')
                }

            });
        },
        updateCompany() {
            axios.post(`/api/company/${this.company.id}`, this.company).then(response => {
                response = response.data
                if (response.success === true) {
                    // this.getCompany();
                    // this.snackbarMessage = 'Update successful'
                    // this.actionColor = 'success'
                    // this.snackbar = true;
                    // this.enableToEdit = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getCompanyLogo() {
            axios.get('/api/main_company/logo').then(response => {
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
                    this.companyCountries = response.data;
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
                    this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.email_type_deleted}`;
                    this.actionColor = 'success';
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;

                }
            });
        },
        getNotificationTypes() {
            axios.get(`/api/notification_types`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.notificationTypes = response.data
                } else {
                    this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        addNotificationType(form) {
            axios.post('/api/notification_type', form).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getNotificationTypes();
                    this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.notification_type_created}`;
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
        updateNotificationType(form) {
            axios.patch(`/api/notification_type/${form.id}`, form).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getNotificationTypes();
                    this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.notification_type_updated}`;
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
        deleteNotificationType(id) {
            axios.delete(`/api/notification_type/${id}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getNotificationTypes();
                    this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.notification_type_deleted}`;
                    this.actionColor = 'success';
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;

                }
            });
        },
        getTicketTypes() {
            axios.get(`/api/ticket_types/all`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.ticketTypes = response.data
                } else {
                    this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        addTicketType(form) {
            axios.post('/api/ticket_type', form).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getTicketTypes();
                    this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.ticket_type_created}`;
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
        updateTicketType(form) {
            axios.patch(`/api/ticket_type/${form.id}`, form).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getTicketTypes();
                    this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.ticket_type_updated}`;
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
        deleteTicketType(id) {
            axios.delete(`/api/ticket_type/${id}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getTicketTypes();
                    this.snackbarMessage = `${this.$store.state.lang.lang_map.system_settings.ticket_type_deleted}`;
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
            axios.get(`/api/main_company/settings`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.companySettings['navbar_style'] = response.data.hasOwnProperty('navbar_style') ? response.data.navbar_style : 1;
                    this.companySettings['ticket_number_format'] = response.data.hasOwnProperty('ticket_number_format') ? response.data.ticket_number_format : this.company.name.replaceAll(' ', '').substr(0, 6) + '｜-｜YYYYMMDD｜-｜###';
                    this.companySettings['timezone'] = response.data.hasOwnProperty('timezone') ? response.data.timezone : 35;
                    this.companySettings['theme_fg_color'] = response.data.hasOwnProperty('theme_fg_color') ? response.data.theme_fg_color : '#FFFFFF';
                    this.companySettings['theme_bg_color'] = response.data.hasOwnProperty('theme_bg_color') ? response.data.theme_bg_color : response.data.hasOwnProperty('theme_color') ? response.data.theme_color : '#6AA75D';
                    this.companySettings['override_user_theme'] = true;
                    this.companySettings['employee_number_format'] = response.data.hasOwnProperty('employee_number_format') ? response.data.employee_number_format : '0||50000|8';

                    let fmt = this.companySettings.ticket_number_format.split('｜');
                    let pos = ['prefix', 'delimiter1', 'date', 'delimiter2', 'suffix'];
                    fmt[0] = fmt[0] ? fmt[0].replaceAll(' ', '').substr(0, 6) : '';
                    for (let i = 0; i < fmt.length; i++) {
                        if (typeof fmt[i] === 'string') {
                            this.ticketNumberFormat[pos[i]] = fmt[i].toLocaleUpperCase();
                        } else {
                            this.ticketNumberFormat[pos[i]] = fmt[i];
                        }
                    }

                    fmt = this.companySettings.employee_number_format.split('｜');
                    fmt[0] = fmt[0] ? fmt[0] : 0;
                    fmt[1] = fmt[1] ? fmt[1].replaceAll(' ', '').substr(0, 6) : '';

                    pos = ['auto', 'prefix', 'start', 'size'];
                    for (let i = 0; i < fmt.length; i++) {
                        if (typeof fmt[i] === 'string') {
                            this.employeeNumberFormat[pos[i]] = fmt[i].toLocaleUpperCase();
                        } else {
                            this.employeeNumberFormat[pos[i]] = fmt[i];
                        }
                    }

                    for (let i = 0; i < fmt.length; i++) {
                        if (typeof fmt[i] === 'string') {
                            this.companyNumberFormat[pos[i]] = fmt[i].toLocaleUpperCase();
                        } else {
                            this.companyNumberFormat[pos[i]] = fmt[i];
                        }
                    }
                    this.updateTicketNumber();
                    this.updateEmployeeNumber();
                }
            });
        },
        updateCompanySettings() {
            this.enableToEdit = false;
            if (this.companyNewLogo) {
                let formData = new FormData();
                formData.append('logo', this.companyNewLogo);
                axios.post(
                    '/api/main_company/logo',
                    formData, {
                        headers: {'content-type': 'multipart/form-data'}
                    }
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

                        return;
                    }
                });
            }

            this.companySettings.ticket_number_format = this.ticketNumberFormat.prefix.toLocaleUpperCase() + '｜' +
                this.ticketNumberFormat.delimiter1 + '｜' + this.ticketNumberFormat.date + '｜' +
                this.ticketNumberFormat.delimiter2 + '｜' + this.ticketNumberFormat.suffix;

            this.companySettings.employee_number_format = this.employeeNumberFormat.auto + '｜' + this.employeeNumberFormat.prefix.toLocaleUpperCase() + '｜' +
                this.employeeNumberFormat.start + '｜' + this.employeeNumberFormat.size;

            this.companySettings.company_number_format = this.companyNumberFormat.auto + '｜' + this.companyNumberFormat.prefix.toLocaleUpperCase() + '｜' +
                this.companyNumberFormat.start + '｜' + this.companyNumberFormat.size;

            this.updateCompany();

            axios.post('/api/main_company/settings', this.companySettings).then(response => {
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
        cancelUpdateCompanySettings() {
            this.getCompany();
            this.getCompanySettings();
            this.enableToEdit = false;
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
                this.ticketNumberFormat.delimiter1 + this.moment().format(this.ticketNumberFormat.date) +
                this.ticketNumberFormat.delimiter2 + '1'.padStart(this.ticketNumberFormat.suffix.length, '0');
        },
        updateEmployeeNumber() {
            if (this.employeeNumberFormat.start < 1) {
                this.employeeNumberFormat.start = 1;
            }

            if (this.employeeNumberFormat.size < 1) {
                this.employeeNumberFormat.size = 1;
            }

            this.employeeNumber = this.employeeNumberFormat.prefix.toLocaleUpperCase() + (this.employeeNumberFormat.start + 1).toString().padStart(this.employeeNumberFormat.size, '0');
        },
        updateCompanyNumber() {
            if (this.companyNumberFormat.start < 1) {
                this.companyNumberFormat.start = 1;
            }

            if (this.companyNumberFormat.size < 1) {
                this.companyNumberFormat.size = 1;
            }

            this.employeeNumber = this.companyNumberFormat.prefix.toLocaleUpperCase() + (this.companyNumberFormat.start + 1).toString().padStart(this.companyNumberFormat.size, '0');
        },
        setThemeFgColor() {
            if (this.autoFgColor) {
                this.companySettings.theme_fg_color = this.invertColor(this.themeBgColor);
            } else {
                this.companySettings.theme_fg_color = this.themeFgColor;
            }
        },
        invertColor(hex) {
            if (hex.indexOf('#') === 0) {
                hex = hex.slice(1);
            }
            if (hex.length === 3) {
                hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
            }
            if (hex.length !== 6) {
                this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                this.errorType = 'error';
                this.alert = true;

                return hex;
            }
            let r = (255 - parseInt(hex.slice(0, 2), 16)).toString(16),
                g = (255 - parseInt(hex.slice(2, 4), 16)).toString(16),
                b = (255 - parseInt(hex.slice(4, 6), 16)).toString(16);
            return '#' + this.padZero(r) + this.padZero(g) + this.padZero(b);
        },
        padZero(str, len) {
            len = len || 2;
            let zeros = new Array(len).join('0');
            return (zeros + str).slice(-len);
        },
        createTag() {
            this.$store.dispatch('Tags/createTag', this.forms.tags)
                .then(tag => {
                    if (tag) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.tag_created_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.resetForm();
                    }
                });
        },
        createService() {
            this.$store.dispatch('Services/createService', this.forms.services)
                .then(service => {
                    if (service) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_created_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.resetForm();
                    }
                });
        },
        createCurrency() {
            this.$store.dispatch('Currencies/createCurrency', this.forms.currency)
                .then(currency => {
                    if (currency) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.currency_created_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.resetForm();
                    }
                });
        },
        getLangName(code) {
            const languages = this.$store.getters['Languages/getLanguages'];
            const foundLang = languages.find(i => i.locale === code);
            return foundLang ? foundLang.name : code;
        },
        saveTag (item) {
            this.$store.dispatch('Tags/updateTag', item);
        },
        saveService (item) {
            this.$store.dispatch('Services/updateService', item);
        },
        saveCurrency (item) {
            this.$store.dispatch('Currencies/updateCurrency', item);
        },
        removeTag(tagId) {
            this.$store.dispatch('Tags/deleteTag', tagId)
                .then(result => {
                    if (result) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.tag_deleted_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.tag_removal_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        removeService(serviceId) {
            this.$store.dispatch('Services/deleteService', serviceId)
                .then(result => {
                    if (result) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_deleted_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_removal_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        removeCurrency(currencyId) {
            this.$store.dispatch('Currencies/removeCurrency', currencyId)
                .then(result => {
                    console.log(result);
                    if (result) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.currency_deleted_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.currency_removal_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        filterLang(item) {
            const languages = this.$store.getters['Languages/getLanguages'];
            const langs = item.translates.map(t => t.lang);
            return languages.filter(i => langs && langs.indexOf(i.locale) == -1);
        },
        resetForm() {
            this.forms.tags = {
                name: '',
                color: this.$helpers.color.genRandomColor()
            };
            this.forms.services = {
                name: ''
            };
            this.forms.currency = {
                name: '',
                slug: '',
                symbol: ''
            }
        },
        closeDialog(id) {
            console.log(id);
            this.addTranslationDialog[id] = false;
        },
        resetDialog() {
            this.tagTranslateForm = {
                lang: null,
                name: ''
            };
        },
        addOrUpdateTranslate(item, lang = null, name = null) {
            this.$store.dispatch('Tags/addOrUpdateTranslate', {
                id: item.id,
                lang: lang ?? this.tagTranslateForm.lang,
                name: name ?? this.tagTranslateForm.name
            });
        },
    }
}
</script>
