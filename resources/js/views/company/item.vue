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
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{langMap.company.info}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateCompany">
                            {{langMap.main.update}}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                color="green"
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
                                color="green"
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
                                color="green"
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
                                        color="green"
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
                                    color="green"
                                    v-model="company.registration_date"
                                ></v-date-picker>
                            </v-menu>
                        </v-form>
                    </v-card-text>
                    <!--                    <v-card-actions>-->
                    <!--                        <v-spacer></v-spacer>-->
                    <!--                        <v-btn color="green" style="color: white;" @click="updateCompany">Save</v-btn>-->
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
                                        <v-list-item-group color="green">
                                            <v-list-item
                                                v-for="(item, i) in company.phones"
                                                :key="item.id"
                                            >
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.phone"></v-list-item-title>
                                                    <v-list-item-subtitle
                                                        v-text="item.type.name"></v-list-item-subtitle>
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
                                                v-for="(item, i) in company.addresses"
                                                :key="item.id"
                                            >
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="">{{item.address}}
                                                        {{item.address_line_2}} {{item.address_line_3}}
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle
                                                        v-text="item.type.name"></v-list-item-subtitle>
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
                                    <v-expansion-panels
                                        multiple
                                    >
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.company.new_phone}}
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
                                                                :label="langMap.main.phone"
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
                                                                :label="langMap.main.type"
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
                                                {{langMap.company.new_address}}
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
                                                                v-model="addressForm.address.address"
                                                                :label="langMap.main.address_line + ' 1'"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="addressForm.address.address_line_2"
                                                                :label="langMap.main.address_line + ' 2'"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-6" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="addressForm.address.postal_code"
                                                                :label="langMap.main.postal_code"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-3" class="pa-1">
                                                            <v-text-field
                                                                color="green"
                                                                item-color="green"
                                                                v-model="addressForm.address.city"
                                                                :label="langMap.main.city"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="md-3" class="pa-1">
                                                            <v-select
                                                                color="green"
                                                                item-color="green"
                                                                item-text="name"
                                                                item-value="name"
                                                                v-model="addressForm.address.country"
                                                                :items="countries"
                                                                :label="langMap.main.country"
                                                                dense
                                                            ></v-select>
                                                        </v-col>
                                                        <v-col cols="6" class="pa-1">
                                                            <v-select
                                                                color="green"
                                                                item-color="green"
                                                                item-text="name"
                                                                item-value="id"
                                                                v-model="addressForm.address_type"
                                                                :items="addressTypes"
                                                                :label="langMap.main.type"
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
                        color="green"
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
                        >
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <p></p>
                                    <p v-if="item.user_data.email"><strong>{{langMap.main.email}}:</strong> {{
                                        item.user_data.email }}
                                    </p>
                                    <p v-if="item.user_data.phones.length > 0"><strong>{{langMap.main.phone}}:</strong>
                                    </p>
                                    <p v-if="item.user_data.phones.length > 0"
                                       v-for="phoneItem in item.user_data.phones">{{ phoneItem.phone }} ({{
                                        phoneItem.type.name }})</p>
                                    <p v-if="item.user_data.addresses.length > 0">
                                        <strong>{{langMap.main.address}}:</strong></p>
                                    <p v-if="item.user_data.addresses.length > 0"
                                       v-for="addressItem in item.user_data.addresses">{{ addressItem.address }} {{
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
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn :disabled="!item.user_data.is_active" @click="sendInvite(item)" icon
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
                                        <v-btn @click="showRolesModal(item)" icon v-bind="attrs" v-on="on">
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
                                               @click="removeEmployee(item)" icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                                :hint="langMap.main.delete+ ' ' + langMap.main.contact"
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
                                            <div class="col-md-4">
                                                <v-text-field
                                                    color="green"
                                                    :label="langMap.main.name"
                                                    name="name"
                                                    type="text"
                                                    v-model="employeeForm.name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    color="green"
                                                    :label="langMap.main.email"
                                                    name="email"
                                                    type="text"
                                                    v-model="employeeForm.email"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-select
                                                    :label="langMap.main.roles"
                                                    color="green"
                                                    item-color="green"
                                                    item-text="name"
                                                    item-value="id"
                                                    :items="roles"
                                                    v-model="employeeForm.role_id"
                                                />
                                                <v-checkbox
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
                        <v-toolbar-title>{{langMap.company.social_info}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-list
                            dense
                            subheader
                        >
                            <v-list-item-group color="green">
                                <v-list-item
                                    v-for="(item) in company.socials"
                                    :key="item.id"
                                >
                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.social_link"></v-list-item-title>
                                        <v-list-item-subtitle v-text="item.type.name"></v-list-item-subtitle>
                                    </v-list-item-content>
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
                                            <v-col cols="md-12" class="pa-1">
                                                <v-text-field
                                                    color="green"
                                                    item-color="green"
                                                    v-model="socialForm.social_link"
                                                    :label="langMap.main.social + ' ' + langMap.main.link"
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
                                                    :label="langMap.main.type"
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
                                color="green"
                                item-color="green"
                                item-text="name"
                                item-value="id"
                                :items="roles"
                                :disabled="!checkRoleByIds([1,2,3])"
                                v-model="singleUserForm.role_ids"
                                multiple
                            />
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
                                                    color="green"
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
                                                    color="green"
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
                                                    color="green"
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
                                                    color="green"
                                                    :label="langMap.main.surname"
                                                    name="surname"
                                                    prepend-icon="mdi-book-account-outline"
                                                    type="text"
                                                    v-model="singleUserForm.user.surname"
                                                    :error-messages="errors.surname"
                                                    lazy-validation
                                                    class="col-md-6"
                                                    dense
                                                ></v-text-field>
                                                <v-text-field
                                                    color="green"
                                                    :label="langMap.main.email"
                                                    name="email"
                                                    prepend-icon="mdi-mail"
                                                    type="text"
                                                    v-model="singleUserForm.user.email"
                                                    required
                                                    :error-messages="errors.email"
                                                    lazy-validation
                                                    class="col-md-12"
                                                    dense
                                                ></v-text-field>
                                                <v-btn
                                                    dark
                                                    fab
                                                    right
                                                    bottom
                                                    small
                                                    color="green"
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
                                                        <v-list-item-group color="green">
                                                            <v-list-item
                                                                v-for="(item, i) in singleUserForm.user.phones"
                                                                :key="item.id"
                                                            >
                                                                <v-list-item-content>
                                                                    <v-list-item-title
                                                                        v-text="item.phone"></v-list-item-title>
                                                                    <v-list-item-subtitle
                                                                        v-text="item.type.name"></v-list-item-subtitle>
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
                                                                v-for="(item, i) in singleUserForm.user.addresses"
                                                                :key="item.id"
                                                            >
                                                                <v-list-item-content>
                                                                    <v-list-item-title v-text="">{{item.address}}
                                                                        {{item.address_line_2}} {{item.address_line_3}}
                                                                    </v-list-item-title>
                                                                    <v-list-item-subtitle
                                                                        v-text="item.type.name"></v-list-item-subtitle>
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
                                                                {{langMap.company.new_phone}}
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
                                                                                v-model="userPhoneForm.phone"
                                                                                :label="langMap.main.phone"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="6" class="pa-1">
                                                                            <v-select
                                                                                color="green"
                                                                                item-color="green"
                                                                                item-text="name"
                                                                                item-value="id"
                                                                                v-model="userPhoneForm.phone_type"
                                                                                :items="phoneTypes"
                                                                                :label="langMap.main.type"
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
                                                                {{langMap.company.new_address}}
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
                                                                                v-model="userAddressForm.address.address"
                                                                                :label="langMap.main.address_line + ' 1'"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-6" class="pa-1">
                                                                            <v-text-field
                                                                                color="green"
                                                                                item-color="green"
                                                                                v-model="userAddressForm.address.address_line_2"
                                                                                :label="langMap.main.address_line + ' 2'"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-6" class="pa-1">
                                                                            <v-text-field
                                                                                color="green"
                                                                                item-color="green"
                                                                                v-model="userAddressForm.address.postal_code"
                                                                                :label="langMap.main.postal_code"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-3" class="pa-1">
                                                                            <v-text-field
                                                                                color="green"
                                                                                item-color="green"
                                                                                v-model="userAddressForm.address.city"
                                                                                :label="langMap.main.city"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-3" class="pa-1">
                                                                            <v-select
                                                                                color="green"
                                                                                item-color="green"
                                                                                item-text="name"
                                                                                item-value="name"
                                                                                v-model="userAddressForm.address.country"
                                                                                :items="countries"
                                                                                :label="langMap.main.country"
                                                                                dense
                                                                            ></v-select>
                                                                        </v-col>
                                                                        <v-col cols="6" class="pa-1">
                                                                            <v-select
                                                                                color="green"
                                                                                item-color="green"
                                                                                item-text="name"
                                                                                item-value="id"
                                                                                v-model="userAddressForm.address_type"
                                                                                :items="addressTypes"
                                                                                :label="langMap.main.type"
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
                                :label="langMap.main.give_access + '?'"
                                color="success"
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
                        <v-btn color="green" text @click="updateRole">{{langMap.main.save}}</v-btn>
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
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'user_data'},
                    {text: `${this.$store.state.lang.lang_map.main.roles}`, value: 'role_names'},
                    {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
                ],
                companyIsLoaded: false,
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
                        postal_code: '',
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
                        address: '',
                        postal_code: '',
                        address_line_2: '',
                        address_line_3: '',
                        city: '',
                        country: ''
                    },
                    address_type: ''
                },
                userSocialForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    social_link: '',
                    social_type: ''
                },
                phoneTypes: [],
                addressTypes: [],
                socialTypes: [],
                countries: [],
            }
        },
        mounted() {
            this.getCompany();
            this.getRoles();
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
            this.getCountries();
            this.employeeForm.company_id = this.$route.params.id
        },
        methods: {
            getCompany() {
                this.companyIsLoaded = false
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
            getCountries() {
                axios.get('/api/countries').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.countries = response.data
                    }
                });
            },
            addEmployee() {
                axios.post(`/api/company/${this.$route.params.id}/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = 'Contact was added successfully'
                        this.actionColor = 'success'
                        this.snackbar = true;
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
                        this.getCompany()
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.enableToEdit = false
                    } else {
                        console.log('error')
                    }

                });
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
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
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
                        this.snackbarMessage = 'Contact was removed'
                        this.actionColor = 'success'
                        this.snackbar = true;
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
                        this.errors = response.error
                    }
                });
            },
            addPhone(form) {
                axios.post('/api/phone', form).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.errorType = 'error'
                        this.alert = true;

                    }
                    return true
                });
            },
            deletePhone(id) {
                axios.delete(`/api/phone/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = 'Delete successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            addSocial(form) {
                axios.post('/api/social', form).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
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
                        this.snackbarMessage = 'Delete successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            addAddress(form) {
                if (form.address.city !== '' && form.address.country !== '') {
                    form.address.address_line_3 = `${form.address.city}, ${form.address.country}`
                } else {
                    form.address_line_3 = `${form.address.city}${form.address.country}`
                }
                axios.post('/api/address', form).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
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
                        this.snackbarMessage = 'Delete successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

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
                        this.snackbarMessage = item.is_active ? 'Contact activated' : 'Contact deactivated'
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
                        this.snackbarMessage = 'Invite sent'
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
