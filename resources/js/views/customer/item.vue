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
                        dense
                        dark
                        flat
                    >
                        <v-toolbar-title>Basic info</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateClient">Save
                        </v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                color="green"
                                label="Name"
                                name="client_name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="client.client_name"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                            <v-text-field
                                color="green"
                                label="Description"
                                name="client_description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="client.client_description"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <!--                    <v-card-actions>-->
                    <!--                        <v-spacer></v-spacer>-->
                    <!--                        <v-btn color="green" style="color: white;" @click="updateClient">Save</v-btn>-->
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
                        <v-toolbar-title>Additional info</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col md="12">
                                    <v-list
                                        dense
                                        subheader
                                    >
                                        <v-list-item-group color="green">
                                            <v-list-item
                                                v-for="(item, i) in client.phones"
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
                                                v-for="(item, i) in client.addresses"
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
                                                            @click="submitNewData(client.id, phoneForm, 'addPhone')"
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
                                                            @click="submitNewData(client.id, addressForm, 'addAddress')"
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
                        <v-toolbar-title>Customer contacts</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="client.employees"
                            :items-per-page="25"
                            class="elevation-1"
                            item-key="id"
                            show-expand
                            dense
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn :disabled="!item.employee.user_data.is_active" @click="sendInvite(item.employee)"
                                               icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                            >
                                                mdi-email-alert
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Resend invite</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn @click="showRolesModal(item.employee)" icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                            >
                                                mdi-pencil
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Edit contact</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn @click="removeEmployee(item)" icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                                hint="Delete contact"
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Delete contact</span>
                                </v-tooltip>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <p></p>
                                    <p v-if="item.employee.user_data.email"><strong>E-mail:</strong></p>
                                    <p v-if="item.employee.user_data.email">{{ item.employee.user_data.email }}</p>
                                    <p v-if="item.employee.user_data.phones.length > 0"><strong>Phone(s):</strong></p>
                                    <p v-for="phoneItem in item.employee.user_data.phones">{{ phoneItem.phone }} ({{
                                        phoneItem.type.name }})</p>
                                    <!--                                    <p><strong>Lang:</strong></p>-->
                                    <!--                                    <p>{{ item.employee.user_data.lang }}</p>-->
                                    <p v-if="item.employee.user_data.addresses.length > 0"><strong>Address(es):</strong>
                                    </p>
                                    <p v-for="addressItem in item.employee.user_data.addresses">{{ addressItem.address
                                        }} {{ addressItem.address_line_2 }} {{ addressItem.address_line_3 }} ({{
                                        addressItem.type.name }})</p>
                                </td>
                            </template>
                            <template v-slot:item.user_data="{ item }">
                                <div class="justify-center" v-if="item.employee.user_data">{{
                                    item.employee.user_data.name }} {{
                                    item.employee.user_data.surname }}
                                </div>
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
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    label="Name"
                                                    name="name"
                                                    type="text"
                                                    v-model="employeeForm.name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    label="Email"
                                                    name="email"
                                                    type="text"
                                                    v-model="employeeForm.email"
                                                    required
                                                ></v-text-field>
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
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Social info</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-list
                            dense
                            subheader
                        >
                            <v-list-item-group color="green">
                                <v-list-item
                                    v-for="(item) in client.socials"
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
                                                @click="submitNewData(client.id, socialForm, 'addSocial')"
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
                        <span class="headline">Update info for {{singleUserForm.user.name}}</span>
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
                                v-model="singleUserForm.role_ids"
                                multiple
                            />
                            <v-expansion-panels
                                :disabled="!checkRoleByIds([1,2,3])"
                            >
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        User information
                                        <template v-slot:actions>
                                            <v-icon color="submit">mdi-plus</v-icon>
                                        </template>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-form>
                                            <v-row>
                                                <v-text-field
                                                    color="green"
                                                    label="Title before name"
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
                                                    label="Title"
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
                                                    label="Name"
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
                                                    label="Surname"
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
                                                    label="Email"
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
                                        Contact phones and addresses
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
                                                                                v-model="userPhoneForm.phone"
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
                                                                                v-model="userPhoneForm.phone_type"
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
                                                                                v-model="userAddressForm.address.address"
                                                                                label="Address"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-6" class="pa-1">
                                                                            <v-text-field
                                                                                color="green"
                                                                                item-color="green"
                                                                                v-model="userAddressForm.address.address_line_2"
                                                                                label="Address line 2"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-3" class="pa-1">
                                                                            <v-text-field
                                                                                color="green"
                                                                                item-color="green"
                                                                                v-model="userAddressForm.address.city"
                                                                                label="City"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col cols="md-3" class="pa-1">
                                                                            <v-text-field
                                                                                color="green"
                                                                                item-color="green"
                                                                                v-model="userAddressForm.address.country"
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
                                                                                v-model="userAddressForm.address_type"
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
                                label="Give access to the system"
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
                    {text: 'roles', value: 'employee.role_names'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                errors:[],
                enableToEdit: false,
                rolesDialog: false,
                singleUserForm: {
                    user: '',
                    role_ids: [],
                    company_user_id: ''
                },
                clientIsLoaded: false,
                client: {
                    client_name: '',
                    client_description: '',
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
                    name: '',
                    email: '',
                    client_id: '',
                    is_active: false
                },
                roles: [
                    {
                        id: '',
                        name: ''
                    }
                ],
                phoneForm: {
                    entity_id: '',
                    entity_type: 'App\\Client',
                    phone: '',
                    phone_type: ''
                },
                addressForm: {
                    entity_id: '',
                    entity_type: 'App\\Client',
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
                    entity_type: 'App\\Client',
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
                socialTypes: []

            }
        },
        mounted() {
            this.getClient()
            this.getRoles()
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
            this.employeeForm.client_id = this.$route.params.id
        },
        methods: {
            getClient() {
                axios.get(`/api/client/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.client = response.data
                        this.client.client_name = response.data.name
                        this.client.client_description = response.data.description
                    } else {
                        console.log('error')
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
                        console.log('error')
                    }

                });
            },
            addEmployee() {
                axios.post(`/api/client/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.snackbarMessage = 'Contact was added successfully'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        console.log('error')
                    }

                });
            },
            updateClient() {
                axios.patch(`/api/client/${this.$route.params.id}`, this.client).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.enableToEdit = false
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
                    }
                });
            },
            getSocialTypes() {
                axios.get(`/api/social_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.socialTypes = response.data
                    } else {
                    }
                });
            },
            getAddressTypes() {
                axios.get(`/api/address_types`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.addressTypes = response.data
                    } else {
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
                        this.getClient()
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
                        this.getClient()
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
                        this.getClient()
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
                        this.getClient()
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
                        this.getClient()
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
                        this.getClient()
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
            removeEmployee(item) {
                axios.delete(`/api/client/employee/${item.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.rolesDialog = false
                        this.snackbarMessage = 'Contact was removed'
                        this.actionColor = 'success'
                        this.snackbar = true;
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
                        this.getClient()
                        this.rolesDialog = false
                        this.snackbarMessage = 'Update successful'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        console.log('error')
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
                        this.getClient()
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
                        this.getClient()
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
        },
        watch: {
            clientUpdates(value) {
                this.clientIsLoaded = true;
                // console.log(this.singleUserForm.user);
                if (this.singleUserForm.user){
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
