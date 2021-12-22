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
                        <v-icon v-if="!enableToEdit" :color="themeFgColor" @click="setEnableToEdit(null)">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px"
                               @click="setEnableToEdit(null)">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="clientUpdate(null)">
                            {{ langMap.main.update }}
                        </v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                v-model="client.client_name"
                                :color="themeBgColor"
                                :disabled="$helpers.auth.checkPermissionByIds([36, 37])"
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
                        <label>{{langMap.custom_license.connection_links}}</label>
                        <v-text-field
                            v-for="(id, key) in client.connection_links"
                            :key="'connection_links'+key"
                            v-model="client.connection_links[key]"
                            :color="themeBgColor"
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
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-btn
                            v-if="enableToEdit"
                            color="green"
                            outlined
                            @click="addConnectionLink(false)"
                        >
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
<!--                        <v-divider></v-divider>-->
<!--                        <v-spacer>-->
<!--                            &nbsp;-->
<!--                        </v-spacer>-->
<!--                        <label>{{langMap.custom_license.aliases}}</label>-->
<!--                        <v-text-field-->
<!--                            v-for="(id, key) in client.aliases"-->
<!--                            :key="'aliases'+key"-->
<!--                            v-model="client.aliases[key]"-->
<!--                            :color="themeBgColor"-->
<!--                            :readonly="!enableToEdit"-->
<!--                            dense-->
<!--                            prepend-icon="mdi-text"-->
<!--                            required-->
<!--                            type="text"-->
<!--                        >-->
<!--                            <template v-slot:append>-->
<!--                                <v-icon-->
<!--                                    color="red"-->
<!--                                    @click="removeAlias(key)"-->
<!--                                >-->
<!--                                    mdi-cancel-->
<!--                                </v-icon>-->
<!--                            </template>-->
<!--                        </v-text-field>-->
<!--                        <v-spacer>-->
<!--                            &nbsp;-->
<!--                        </v-spacer>-->
<!--                        <v-btn-->
<!--                            v-if="enableToEdit"-->
<!--                            color="green"-->
<!--                            outlined-->
<!--                            @click="addAlias(false)"-->
<!--                        >-->
<!--                            <v-icon>mdi-plus</v-icon>-->
<!--                        </v-btn>-->
                    </v-card-text>
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
                        <v-icon :color="themeFgColor" class="ma-2" @click="historyDialog = true">
                            mdi-history
                        </v-icon>
                        <v-icon v-if="!enableToEditLicense" :color="themeFgColor" @click="setEnableToEditLicense(null)">
                            mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEditLicense" color="white" style="color: black; margin-right: 10px"
                               @click="setEnableToEditLicense(null); getLicense();">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEditLicense" color="white" style="color: black;" @click="updateLicense(null)">
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
                                                <v-list-item-title v-text="langMap.custom_license.total_users"></v-list-item-title>
                                                <v-list-item-subtitle
                                                    v-text="license.usersAllowed"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-account-multiple-minus</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="langMap.custom_license.new_users"></v-list-item-title>
                                                <v-list-item-subtitle v-text="usersAssigned"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-account-multiple-plus</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="langMap.custom_license.users_available"></v-list-item-title>
                                                <v-list-item-subtitle v-text="license.usersLeft"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-license</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="langMap.custom_license.active"></v-list-item-title>
                                                <v-list-item-subtitle v-text="license.active"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon> mdi-calendar-weekend-outline</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="langMap.custom_license.trial_days"></v-list-item-title>
                                                <v-text-field
                                                    v-if="enableToEditLicense"
                                                    v-model="license.trialPeriodDays"
                                                    :color="themeBgColor"
                                                    dense
                                                    name="trial_days"
                                                    style="max-width: 50px"
                                                    type="text"
                                                ></v-text-field>
                                                <v-list-item-subtitle v-if="!enableToEditLicense"
                                                                      v-text="license.trialPeriodDays"></v-list-item-subtitle>
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
                                    {{langMap.custom_license.additional_licenses}}
                                </div>
                                <v-row>
                                    <v-col md="6">
                                        <span v-for="(licenseValue, index) in licenseValues" :key=" '+' + index">
                                            <v-btn
                                                class="ma-2"
                                                color="grey darken-1"
                                                outlined
                                                @click="appendLicenseItems(licenseValue)">
                                                {{ '+' + licenseValue }}
                                            </v-btn>
                                            <br v-if="index === (licenseValues.length/2) - 1">
                                        </span>
                                    </v-col>
                                    <v-col md="6">
                                        <span v-for="(licenseValue, index) in licenseValues" :key="'-' + index">
                                            <v-btn
                                                class="ma-2"
                                                color="grey darken-1"
                                                outlined
                                                @click="spendLicenseItems(licenseValue)">
                                                {{ '-' + licenseValue }}
                                            </v-btn>
                                            <br v-if="index === (licenseValues.length/2) - 1">
                                        </span>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-expand-transition>
                    </v-card-text>
                </v-card>
            </div>
            <div class="col-md-12">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.main.clients }}</v-toolbar-title>
                    </v-toolbar>
                    <div class="card-body">
                        <v-expansion-panels v-model="relatedClientsPanel" v-if="relatedClients" multiple>
                            <v-expansion-panel v-for="(item,i) in relatedClients"
                                               :key="'relatedClients'+i"
                                               @click="processRelatedLicenseUsers(item.id, 'relatedClients'+i)">
                                <v-expansion-panel-header>
                                    <span v-if="item.custom_license && item.custom_license.ixarma_object"
                                          class="text-left"
                                    >
                                        <strong>{{ item.name }}:</strong>
                                        {{ langMap.custom_license.total_users + ': ' + item.custom_license.ixarma_object.limits.usersAllowed }} |
                                        {{ langMap.custom_license.users_available + ' ' + item.custom_license.ixarma_object.limits.usersLeft }} |
                                        {{ langMap.custom_license.active }}
                                                                <v-checkbox
                                                                    style="display: inline-block; margin-top: 0; padding-top: 0;"
                                                                    disabled
                                                                    v-model="item.custom_license.ixarma_object.limits.active"
                                                                    color="success"
                                                                    hide-details
                                                                ></v-checkbox>|
                                        {{ langMap.custom_license.expired_at + ' ' +
                                            item.custom_license.ixarma_object.limits.expiresAt === '01/01/1970' ?
                                            moment.substract(1, 'days') :
                                            item.custom_license.ixarma_object.limits.expiresAt
                                        }} |
                                        {{ langMap.custom_license.trial_days + ' ' + item.custom_license.ixarma_object.limits.trialPeriodDays }} |
                                        {{ langMap.custom_license.auto_assign}}
                                        <v-checkbox
                                            @click.prevent.stop="updateAutoAssignFlag(item)"
                                            style="display: inline-block; margin-top: 0; padding-top: 0;"
                                            v-model="item.custom_license.ixarma_object.limits.autoLicensingEnabled"
                                            color="success"
                                            hide-details
                                        ></v-checkbox>
                                    </span>
                                    <strong v-else>{{ item.name }}</strong>
                                    <template v-slot:actions>
                                        <v-icon
                                            v-if="item.custom_license === null"
                                            @click.prevent.stop="linkClientToIxarmaCompany(item)"
                                        >
                                            mdi-plus
                                        </v-icon>
                                        <v-icon
                                            v-if="item.custom_license && item.custom_license.ixarma_object"
                                            @click.prevent.stop="showChildClientEditorDialog(item)"
                                        >
                                            mdi-pencil
                                        </v-icon>
                                        <v-icon v-else>
                                            mdi-pencil-off
                                        </v-icon>
                                    </template>
                                    <v-spacer>
                                    </v-spacer>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content
                                    v-if="item.custom_license && item.custom_license.ixarma_object"
                                >
                                    <div class="col-md-12">
                                        <v-card class="elevation-12">
                                            <v-toolbar
                                                :color="themeBgColor"
                                                dark
                                                dense
                                                flat
                                            >
                                                <v-toolbar-title :style="`color: ${themeFgColor};`">
                                                    {{ langMap.custom_license.license_users }}
                                                </v-toolbar-title>
                                            </v-toolbar>
                                            <div class="card-body">
                                                <v-data-table
                                                    :footer-props="footerProps"
                                                    :headers="licenseUserHeaders"
                                                    :items="relatedLicenseUsers[item.id]"
                                                    :loading-text="langMap.main.loading"
                                                    :options.sync="options"
                                                    dense
                                                    hide-default-footer
                                                    @click:row="showAssignDialog"
                                                >
                                                    <template v-slot:item.lastActivationChangeString="{ item }">
                                                        <v-icon>
                                                            mdi-calendar
                                                        </v-icon>
                                                        {{item.lastActivationChangeString}}
                                                    </template>
                                                    <template v-slot:item.trialExpirationAtString="{ item }">
                                                        <v-icon
                                                            @click.stop.prevent="selectedUserId = item.id; trialExpirationModal = true"
                                                        >
                                                            mdi-calendar
                                                        </v-icon>
                                                        <span
                                                            @click.stop.prevent="selectedUserId = item.id; trialExpirationModal = true"
                                                        >
                                                            {{item.trialExpirationAtString}}
                                                        </span>
                                                    </template>
                                                    <template v-slot:item.licensed="{ item }">
                                                        <v-checkbox
                                                            style="margin: 0;"
                                                            v-model="item.licensed"
                                                            @click.stop.prevent="manageLicenseUsers(item.id, item.licensed); item.licensed = !item.licensed;"
                                                            color="success"
                                                            hide-details
                                                        ></v-checkbox>
                                                    </template>
                                                    <template v-slot:item.actions="{ item }">
                                                        <v-tooltip top>
                                                            <template v-slot:activator="{ on, attrs }">
                                                                <v-btn @click.stop.prevent="showUnassignDialog(item)" icon v-bind="attrs" v-on="on">
                                                                    <v-icon>
                                                                        mdi-delete
                                                                    </v-icon>
                                                                </v-btn>
                                                            </template>
                                                            <span>{{langMap.custom_license.unassign_contact}}</span>
                                                        </v-tooltip>
                                                    </template>
                                                </v-data-table>
                                            </div>
                                        </v-card>
                                    </div>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </div>
                </v-card>
            </div>
            <div class="col-md-12">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.custom_license.license_users }}</v-toolbar-title>
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
                            dense
                            @click:row="showAssignDialog"
                        >
                            <template v-slot:item.lastActivationChangeString="{ item }">
                                <v-icon>
                                    mdi-calendar
                                </v-icon>
                                {{item.lastActivationChangeString}}
                            </template>
                            <template v-slot:item.trialExpirationAtString="{ item }">
                                <v-icon
                                    @click.stop.prevent="showExpiredAtDialog"
                                >
                                    mdi-calendar
                                </v-icon>
                                <span
                                    @click.stop.prevent="showExpiredAtDialog"
                                >
                                    {{item.trialExpirationAtString}}
                                </span>
                            </template>
                            <template v-slot:item.licensed="{ item }">
                                <v-checkbox
                                    :disabled="client.is_portal === 1"
                                    style="margin: 0;"
                                    v-model="item.licensed"
                                    @click.stop.prevent="manageLicenseUsers(item.id, item.licensed); item.licensed = !item.licensed;"
                                    color="success"
                                    hide-details
                                ></v-checkbox>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn @click.stop.prevent="showUnassignDialog(item)" icon v-bind="attrs" v-on="on">
                                            <v-icon>
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{langMap.company.delete_contact}}</span>
                                </v-tooltip>
                            </template>
                        </v-data-table>
                    </div>
                </v-card>
            </div>
        </div>
        <v-dialog v-model="historyDialog" max-width="30%">
            <v-card class="elevation-12">
                <v-toolbar
                    :color="themeBgColor"
                    dark
                    dense
                    flat
                >
                    <v-toolbar-title :style="`color: ${themeFgColor};`">
                        {{
                            langMap.notification.history
                        }}
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn color="white" style="color: black; margin-right: 10px"
                           @click="historyDialog = false">
                        {{ langMap.main.cancel }}
                    </v-btn>
                </v-toolbar>
                <v-card-text>
                    <v-list
                        dense
                    >
                        <v-list-item
                            v-for="(item, index) in licenseHistory"
                            v-if="item.diff.length > 0"
                            :key="'licenseHistory'+index"
                        >
                            <v-list-item-title>
                                {{ item.diff }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-card-text>
            </v-card>
        </v-dialog>
        <v-dialog v-model="assignCompanyDialog" max-width="480" persistent>
            <v-card>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.main.assign }}
                </v-card-title>
                <v-card-text>
                    <v-select v-model="reassignedUserForm.company_id" :color="themeBgColor"
                              :item-color="themeBgColor" :items="customers"
                              :label="langMap.main.company"
                              dense
                              item-text="name" item-value="id">
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
        <v-dialog
            ref="dialog"
            v-model="trialExpirationModal"
            :return-value.sync="tempExpDate"
            persistent
            width="290px"
        >
            <v-date-picker
                v-model="tempExpDate"
                :color="themeBgColor"
                :locale="calendarLocale"
                scrollable
            >
                <v-spacer></v-spacer>
                <v-btn
                    color="cancel"
                    text
                    @click="trialExpirationModal = false"
                >
                    Cancel
                </v-btn>
                <v-btn
                    :color="themeBgColor"
                    text
                    @click="setUserTrialDateItem"
                >
                    OK
                </v-btn>
            </v-date-picker>
        </v-dialog>
        <v-dialog
            v-model="childClientEditorDialog"
            width="50%"
        >
            <v-card
                outlined
            >
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.company.info
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEditChild" :color="themeFgColor"
                                @click="setEnableToEdit(selectedChildClient)">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEditChild" color="white" style="color: black; margin-right: 10px"
                               @click="setEnableToEdit(selectedChildClient)">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEditChild"
                               color="white"
                               style="color: black;"
                               @click="clientUpdate(selectedChildClient)">
                            {{ langMap.main.update }}
                        </v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                v-model="selectedChildClient.client_name"
                                :color="themeBgColor"
                                :disabled="$helpers.auth.checkPermissionByIds([36, 37])"
                                :label="langMap.company.name"
                                :readonly="!enableToEditChild"
                                dense
                                name="client_name"
                                prepend-icon="mdi-rename-box"
                                required
                                type="text"
                            ></v-text-field>
                            <v-text-field
                                v-model="selectedChildClient.client_description"
                                :color="themeBgColor"
                                :label="langMap.company.description"
                                :readonly="!enableToEditChild"
                                dense
                                name="client_description"
                                prepend-icon="mdi-comment-text"
                                required
                                type="text"
                            ></v-text-field>
                        </v-form>
                        <v-checkbox
                            v-model="selectedChildClient.is_active"
                            :label="this.$store.state.lang.lang_map.customer.active"
                            color="success"
                            hide-details
                        ></v-checkbox>
                        <v-divider></v-divider>
                        <br>
<!--                        <label>{{langMap.custom_license.connection_links}}</label>-->
<!--                        <v-text-field-->
<!--                            v-for="(id, key) in selectedChildClient.connection_links"-->
<!--                            :key="'child_connection_links'+key"-->
<!--                            v-model="selectedChildClient.connection_links[key]"-->
<!--                            :color="themeBgColor"-->
<!--                            :readonly="!enableToEditChild"-->
<!--                            dense-->
<!--                            prepend-icon="mdi-link"-->
<!--                            required-->
<!--                            type="text"-->
<!--                        >-->
<!--                            <template v-slot:append>-->
<!--                                <v-icon-->
<!--                                    color="red"-->
<!--                                    @click="removeConnectionLink(key, true)"-->
<!--                                >-->
<!--                                    mdi-cancel-->
<!--                                </v-icon>-->
<!--                            </template>-->
<!--                        </v-text-field>-->
<!--                        <v-spacer>-->
<!--                            &nbsp;-->
<!--                        </v-spacer>-->
<!--                        <v-btn-->
<!--                            v-if="enableToEditChild"-->
<!--                            color="green"-->
<!--                            outlined-->
<!--                            @click="addConnectionLink(true)"-->
<!--                        >-->
<!--                            <v-icon>mdi-plus</v-icon>-->
<!--                        </v-btn>-->
<!--                        <v-spacer>-->
<!--                            &nbsp;-->
<!--                        </v-spacer>-->
<!--                        <v-divider></v-divider>-->
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <label>{{langMap.custom_license.aliases}}</label>
                        <v-text-field
                            v-for="(id, key) in selectedChildClient.aliases"
                            :key="'aliases'+key"
                            v-model="selectedChildClient.aliases[key]"
                            :color="themeBgColor"
                            :readonly="!enableToEditChild"
                            dense
                            prepend-icon="mdi-text"
                            required
                            type="text"
                        >
                            <template v-slot:append>
                                <v-icon
                                    color="red"
                                    @click="removeAlias(key, true)"
                                >
                                    mdi-cancel
                                </v-icon>
                            </template>
                        </v-text-field>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-btn
                            v-if="enableToEditChild"
                            color="green"
                            outlined
                            @click="addAlias(true)"
                        >
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </v-card-text>
                </v-card>
                <br>
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
                        <!--                        <v-icon :color="themeFgColor" class="ma-2" @click="historyDialog = true">-->
                        <!--                            mdi-history-->
                        <!--                        </v-icon>-->
                        <v-icon v-if="!enableToEditChildLicense" :color="themeFgColor"
                                @click="setEnableToEditLicense(selectedChildClient)">
                            mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEditChildLicense" color="white" style="color: black; margin-right: 10px"
                               @click="setEnableToEditLicense(selectedChildClient); getLicense(selectedChildClient.id);">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEditChildLicense" color="white" style="color: black;"
                               @click="updateLicense(selectedChildClient.id)">
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
                                                    v-text="selectedChildClientLicense.usersAllowed"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-account-multiple-minus</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="langMap.custom_license.new_users"></v-list-item-title>
                                                <v-list-item-subtitle
                                                    v-text="childUsersAssigned"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-account-multiple-plus</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="langMap.custom_license.users_available"></v-list-item-title>
                                                <v-list-item-subtitle
                                                    v-text="selectedChildClientLicense.usersLeft"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon>mdi-license</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="langMap.custom_license.active"></v-list-item-title>
                                                <v-list-item-subtitle
                                                    v-text="selectedChildClientLicense.active"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                        <v-list-item>
                                            <v-list-item-icon>
                                                <v-icon> mdi-calendar-weekend-outline</v-icon>
                                            </v-list-item-icon>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="langMap.custom_license.trial_days"></v-list-item-title>
                                                <v-text-field
                                                    v-if="enableToEditChildLicense"
                                                    v-model="selectedChildClientLicense.trialPeriodDays"
                                                    :color="themeBgColor"
                                                    dense
                                                    name="trial_days"
                                                    style="max-width: 50px"
                                                    type="text"
                                                ></v-text-field>
                                                <v-list-item-subtitle v-if="!enableToEditChildLicense"
                                                                      v-text="selectedChildClientLicense.trialPeriodDays"></v-list-item-subtitle>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </v-list-item-group>
                                </v-list>
                            </v-col>
                            <v-col md="6">
                                <v-date-picker
                                    v-model="selectedChildClientLicense.expiresAt"
                                    :color="themeBgColor"
                                    :first-day-of-week="1"
                                    :locale="calendarLocale"
                                    :readonly="!enableToEditChildLicense"
                                ></v-date-picker>
                            </v-col>
                        </v-row>
                        <v-expand-transition>
                            <v-card
                                v-if="enableToEditChildLicense"
                                class="mx-auto"
                                outlined
                            >
                                <div class="overline mx-2">

                                </div>
                                <v-btn
                                    v-if="enableToEditChildLicense"
                                    :color="selectedChildClientLicense.active ? 'red' :'green'"
                                    class="ma-2"
                                    dark
                                    @click="selectedChildClientLicense.active = !selectedChildClientLicense.active"
                                >
                                    {{ selectedChildClientLicense.active ? 'suspend' : 'renew' }}
                                </v-btn>
                                <div class="overline mx-2">
                                    {{langMap.custom_license.additional_licenses}}
                                </div>
                                <v-row>
                                    <v-col md="6">
                                        <span v-for="(licenseValue, index) in licenseValues" :key=" '+' + index">
                                            <v-btn
                                                class="ma-2"
                                                color="grey darken-1"
                                                outlined
                                                @click="appendLicenseItems(licenseValue, true)">
                                                {{ '+' + licenseValue }}
                                            </v-btn>
                                            <br v-if="index === (licenseValues.length/2) - 1">
                                        </span>
                                    </v-col>
                                    <v-col md="6">
                                        <span v-for="(licenseValue, index) in licenseValues" :key="'-' + index">
                                            <v-btn
                                                class="ma-2"
                                                color="grey darken-1"
                                                outlined
                                                @click="spendLicenseItems(licenseValue, true)">
                                                {{ '-' + licenseValue }}
                                            </v-btn>
                                            <br v-if="index === (licenseValues.length/2) - 1">
                                        </span>
                                    </v-col>
                                </v-row>
                            </v-card>
                        </v-expand-transition>
                    </v-card-text>
                </v-card>
            </v-card>
        </v-dialog>
        <v-dialog v-model="unassignDialog" max-width="480" persistent>
            <v-card>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.main.unassign }}
                </v-card-title>
                <v-card-text>
                    {{ unassignedUserForm.name }}
                    {{ unassignedUserForm.phone }}
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="unassignDialog = false">
                        {{ langMap.main.cancel }}
                    </v-btn>
                    <v-btn color="red darken-1" text @click="unassignFromIxarmaCompany">
                        {{ langMap.main.unassign }}
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
                {text: `${this.$store.state.lang.lang_map.custom_license.username}`, value: 'username'},
                {text: `${this.$store.state.lang.lang_map.main.phone}`, value: 'phoneNumber', sortable: false},
                {text: `${this.$store.state.lang.lang_map.custom_license.platform}`, value: 'platform', sortable: false},
                {text: `${this.$store.state.lang.lang_map.ticket.ip_address}`, value: 'serverIp'},
                {text: `${this.$store.state.lang.lang_map.custom_license.organisation}`, value: 'orgPath'},
                {text: `${this.$store.state.lang.lang_map.custom_license.licensed}`, value: 'licensed', sortable: false},
                {text: `${this.$store.state.lang.lang_map.custom_license.active}`, value: 'active', sortable: false},
                {text: `${this.$store.state.lang.lang_map.custom_license.expired_at}`, value: 'trialExpirationAtString', sortable: false},
                {text: `${this.$store.state.lang.lang_map.custom_license.last_activation}`, value: 'lastActivationChangeString', sortable: false},
                {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions'},
            ],
            menu2: false,
            tempExpDate: null,
            selectedUserId: null,
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
            enableToEditChild: false,
            enableToEditChildLicense: false,
            rolesDialog: false,
            historyDialog: false,
            licenseHistory: [],
            licenseUsers: [],
            relatedLicenseUsers: [],
            singleUserForm: {
                user: '',
                role_ids: [],
                company_user_id: 0
            },
            trialExpirationModal: false,
            clientIsLoaded: false,
            employees: [],
            licenseValues: [10, 20, 50, 100, 500, 1000],
            usersAssigned: 0,
            childUsersAssigned: 0,
            selectedDate: new Date().toISOString().substr(0, 10),
            selectedChildClient: {
                is_portal: 1,
                client_name: '',
                client_description: '',
                connection_links: [""],
                aliases: [""],
            },
            selectedChildClientLicense: {},
            selectedChildClientLicenseHistory: [],
            relatedClientsPanel: [],
            unassignedUserForm: {
                user_id: '',
                company_id: '',
                name: '',
                phone: ''
            },
            client: {
                is_portal: 1,
                client_name: '',
                client_description: '',
                connection_links: [""],
                aliases: [""],
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
            unassignDialog: false,
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
            relatedClients: [],
            updatePhoneDlg: false,
            updateAddressDlg: false,
            updateSocialDlg: false,
            updateEmailDlg: false,
            contactInfoForm: null,
            deleteProductDlg: false,
            childClientEditorDialog: false,
            selectedProductId: null
        }
    },
    mounted() {
        this.getClient();
        this.getLicenseUsers();
        this.getRoles();
        this.getLanguages();
        this.getCountries();
        this.getRelatedClients();
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        getClient(id = null) {
            let isRelated = true
            if (id === null) {
                id = this.$route.params.id
                isRelated = false
            }
            axios.get(`/api/client/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    let client;
                    client = response.data
                    client.client_name = response.data.name
                    client.client_description = response.data.description
                    if (isRelated) {
                        this.selectedChildClient = client
                        this.getLicense(client.id)
                    } else {
                        this.$store.state.pageName = this.client.client_name
                        this.client = client
                        this.getLicense()

                    }
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        getLicense(id = null) {
            let isRelated = true
            if (id === null) {
                id = this.$route.params.id
                isRelated = false
            }
            axios.get(`/api/custom_license/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    if (isRelated === true) {
                        this.selectedChildClientLicense = response.data.limits
                        this.selectedChildClient.connection_links = response.data.info !== null ?
                            response.data.info.serverUrls : [""]
                        this.selectedChildClient.aliases = response.data.info !== null ?
                            response.data.info.aliases : [""]
                        this.selectedChildClientLicense.expiresAt = this.moment(response.data.limits.expiresAt, 'DD/MM/YYYY')
                            .format('YYYY-MM-DD')
                        this.childUsersAssigned =
                            this.selectedChildClientLicense.usersAllowed - this.selectedChildClientLicense.usersLeft;
                        this.getLicenseHistory(id);
                        this.$forceUpdate()
                    } else {
                        this.license = response.data.limits
                        this.client.connection_links = response.data.info !== null ? response.data.info.serverUrls : [""]
                        this.client.aliases = response.data.info !== null ? response.data.info.aliases : [""]
                        this.license.expiresAt = this.moment(response.data.limits.expiresAt, 'DD/MM/YYYY').format('YYYY-MM-DD')
                        this.usersAssigned = this.license.usersAllowed - this.license.usersLeft;
                        this.getLicenseHistory();

                    }

                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        showUnassignDialog(item) {
            this.unassignedUserForm.user_id = item.id
            this.unassignedUserForm.name = item.username
            this.unassignedUserForm.phone = item.phoneNumber
            this.unassignDialog = true
        },
        updateAutoAssignFlag(item) {
            this.selectedChildClient = item;
            this.selectedChildClientLicense = item.custom_license.ixarma_object.limits;
            this.updateLicense(item.id, false);
        },
        unassignFromIxarmaCompany() {
            axios.post('/api/custom_license_user/unassign', this.unassignedUserForm, {
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.getLicenseUsers();
                    this.getRelatedClients();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
            this.unassignDialog = false
        },
        getLicenseHistory(id = null) {
            let isRelated = true
            if (id === null) {
                id = this.$route.params.id
                isRelated = false
            }
            axios.get(`/api/custom_license/${id}/history`).then(response => {
                response = response.data
                if (response.success === true) {
                    if (isRelated) {
                        this.selectedChildClientLicenseHistory = response.data
                    } else {
                        this.licenseHistory = response.data
                    }

                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        processRelatedLicenseUsers(id, index) {
            this.getLicenseUsers(id)
        },
        getLicenseUsers(id = null) {
            let isRelated = true;
            if (id === null) {
                id = this.$route.params.id
                isRelated = false
            }
            axios.get(`/api/custom_license/${id}/users`).then(response => {
                response = response.data
                if (response.success === true) {
                    let users = []
                    for (let key in response.data.entities) {

                        if (response.data.entities[key].lastActivationChangeString !== "01/01/1970") {
                            // response.data.entities[key].lastActivationChangeString =
                            //     this.moment(response.data.entities[key].lastActivationChangeString).format('DD/MM/YYYY')
                        } else {
                            response.data.entities[key].lastActivationChangeString = ''
                        }

                        if (response.data.entities[key].trialExpirationAtString !== "01/01/1970") {
                            // response.data.entities[key].trialExpirationAtString =
                            //     this.moment(response.data.entities[key].trialExpirationAtString).format('DD/MM/YYYY')
                        } else {
                            response.data.entities[key].trialExpirationAtString = ''
                        }
                        users.push(response.data.entities[key])
                    }
                    if (isRelated) {
                        this.relatedLicenseUsers[id] = users;
                    } else {
                        this.licenseUsers = users;
                    }
                    this.$forceUpdate();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        manageLicenseUsers(remoteId, isLicensed) {
            axios.get(`/api/custom_license/${this.$route.params.id}/user/${remoteId}/${isLicensed}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getLicense();
                    this.getLicenseUsers();
                    this.getRelatedClients()
                    this.$forceUpdate();
                    // this.licenseUsers = response.data.entities
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        appendLicenseItems(count = 0, isRelated = false) {
            if (isRelated) {
                this.selectedChildClientLicense.usersAllowed += count
            } else {
                this.license.usersAllowed += count
            }
        },
        spendLicenseItems(count = 0, isRelated = false) {
            if (isRelated) {
                this.selectedChildClientLicense.usersAllowed += count
                if (this.selectedChildClientLicense.usersAllowed - count > 0) {
                    this.selectedChildClientLicense.usersAllowed -= count
                }
            } else {
                if (this.license.usersAllowed - count > 0) {
                    this.license.usersAllowed -= count
                }
            }
        },
        updateLicense(id = null, renewable = true) {
            let license, isRelated;
            if (id === null) {
                id = this.$route.params.id
                license = this.license
                isRelated = 0
            } else {
                id = this.selectedChildClient.id
                license = this.selectedChildClientLicense
                isRelated = 1
            }
            license.renewable = renewable
            license.expiresAt = this.moment(license.expiresAt, 'YYYY-MM-DD').format('DD/MM/YYYY')
            axios.put(`/api/custom_license/${id}/limits`, license).then(response => {
                response = response.data
                if (response.success === true) {
                    if (isRelated === 0) {
                        this.license = response.data
                        this.license.expiresAt = this.moment(response.data.expiresAt, 'DD/MM/YYYY').format('YYYY-MM-DD')
                    } else {
                        this.selectedChildClientLicense = response.data
                        this.selectedChildClientLicense.expiresAt = this.moment(response.data.expiresAt, 'DD/MM/YYYY').format('YYYY-MM-DD')
                    }
                    this.snackbarMessage = this.license = this.langMap.main.update_successful;
                    this.actionColor = 'success';
                    this.snackbar = true;
                    this.setEnableToEditLicense(isRelated === 1 ? license : null)
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
        clientUpdate(id = null) {
            let client, isRelated;
            if (id === null) {
                id = this.$route.params.id
                client = this.client
                isRelated = 0
            } else {
                id = this.selectedChildClient.id
                client = this.selectedChildClient
                isRelated = 1
            }
            axios.put(`/api/custom_license/${id}`, client).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient();
                    this.setEnableToEdit(isRelated === 1 ? client : null)
                    this.snackbarMessage = this.license = this.langMap.main.update_successful;
                    this.actionColor = 'success';
                    this.snackbar = true;

                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        addConnectionLink(isRelated = false) {
            if (isRelated) {
                this.selectedChildClient.connection_links.push(" ");
            } else {
                this.client.connection_links.push(" ");
            }
            this.$forceUpdate();
        },
        removeConnectionLink(id, isRelated = false) {
            if (isRelated) {
                this.selectedChildClient.connection_links.splice(id, 1);
            } else {
                this.client.connection_links.splice(id, 1);
            }
            this.$forceUpdate();
        },
        addAlias(isRelated = false) {
            if (isRelated) {
                this.selectedChildClient.aliases.push(" ");
            } else {
                this.client.aliases.push(" ");
            }
            this.$forceUpdate();
        },
        removeAlias(id, isRelated = false) {
            if (isRelated) {
                this.selectedChildClient.aliases.splice(id, 1);
            } else {
                this.client.aliases.splice(id, 1);
            }
            this.$forceUpdate();
        },
        getClients() {
            this.loading = this.themeBgColor
            axios.get('/api/client', {}).then(response => {
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
        getRelatedClients() {
            this.loading = this.themeBgColor
            axios.get(`/api/client/${this.$route.params.id}/related`, {}).then(response => {
                this.loading = false
                response = response.data
                if (response.success === true) {
                    this.relatedClients = response.data

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
        showChildClientEditorDialog(item) {
            this.childClientEditorDialog = true
            this.selectedChildClient = item
            this.getClient(this.selectedChildClient.id);
        },
        linkClientToIxarmaCompany(item) {
            axios.get(`/api/custom_license/${item.id}/link`)
                .then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.snackbarMessage = this.langMap.main.update_successful;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.getLicenseUsers();
                        this.getRelatedClients();
                        this.$forceUpdate();
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            this.assignCompanyDialog = false
        },
        assignToIxarmaCompany() {
            axios.post('/api/custom_license_unassigned/assign', this.reassignedUserForm, {})
                .then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.snackbarMessage = this.langMap.main.update_successful;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.getLicenseUsers();
                        this.getRelatedClients();
                        this.$forceUpdate();
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            this.assignCompanyDialog = false
        },
        setUserTrialDateItem() {
            axios.put(`/api/custom_license_user/${this.selectedUserId}/trial`, {'trialExpirationDate': this.tempExpDate}, {})
                .then(response => {
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
            this.trialExpirationModal = false
        },
        setEnableToEdit(selectedClient = null) {
            let isRelated = true
            if (selectedClient === null) {
                selectedClient = this.client
                isRelated = false
            }
            // if (selectedClient.is_portal === 0) {
                if (isRelated) {
                    this.enableToEditChild = !this.enableToEditChild
                } else {
                    this.enableToEdit = !this.enableToEdit
                }
            // }
            this.$forceUpdate();

        },
        setEnableToEditLicense(selectedClient = null) {
            let isRelated = true
            if (selectedClient === null) {
                selectedClient = this.client
                isRelated = false
            }
            // if (selectedClient.is_portal === 0) {
                if (isRelated) {
                    this.enableToEditChildLicense = !this.enableToEditChildLicense
                } else {
                    this.enableToEditLicense = !this.enableToEditLicense
                }
            // }
        },
        showExpiredAtDialog() {
            if (this.client.is_portal === 0) {
                this.selectedUserId = item.id;
                this.trialExpirationModal = true;
            }

        }
    }
}
</script>
