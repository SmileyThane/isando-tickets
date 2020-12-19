<template>


    <v-container fluid>
        <v-row>
            <v-snackbar
                v-model="snackbar"
                :bottom="true"
                :color="actionColor"
                :right="true"
            >
                {{ snackbarMessage }}
            </v-snackbar>
            <v-col class="col-md-6">
                <v-card class="elevation-6">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{this.$store.state.lang.lang_map.individuals.info}}</v-toolbar-title>
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
                                    v-model="userData.title_before_name"
                                    :color="themeColor"
                                    :error-messages="errors.title_before_name"
                                    :label="this.$store.state.lang.lang_map.main.title_before_name"
                                    :readonly="!enableToEdit"
                                    class="col-md-6"
                                    dense
                                    lazy-validation
                                    name="title_before_name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                ></v-text-field>
                                <v-text-field
                                    v-model="userData.title"
                                    :color="themeColor"
                                    :error-messages="errors.title"
                                    :label="this.$store.state.lang.lang_map.main.title"
                                    :readonly="!enableToEdit"
                                    class="col-md-6"
                                    dense
                                    lazy-validation
                                    name="title"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                ></v-text-field>
                                <v-text-field
                                    v-model="userData.name"
                                    :color="themeColor"
                                    :error-messages="errors.name"
                                    :label="this.$store.state.lang.lang_map.main.first_name"
                                    :readonly="!enableToEdit"
                                    class="col-md-6"
                                    dense
                                    lazy-validation
                                    name="name"
                                    prepend-icon="mdi-book-account-outline"
                                    required
                                    type="text"
                                ></v-text-field>
                                <v-text-field
                                    v-model="userData.middle_name"
                                    :color="themeColor"
                                    :error-messages="errors.middle_name"
                                    :label="this.$store.state.lang.lang_map.main.middle_name"
                                    :readonly="!enableToEdit"
                                    class="col-md-6"
                                    dense
                                    lazy-validation
                                    name="middle_name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                ></v-text-field>
                                <v-text-field
                                    v-model="userData.surname"
                                    :color="themeColor"
                                    :error-messages="errors.surname"
                                    :label="this.$store.state.lang.lang_map.main.last_name"
                                    :readonly="!enableToEdit"
                                    class="col-md-6"
                                    dense
                                    lazy-validation
                                    name="surname"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                ></v-text-field>
                                <v-col md="6">
                                    <v-spacer></v-spacer>
                                </v-col>
                                <v-checkbox
                                    v-model="userData.status"
                                    :label="this.$store.state.lang.lang_map.individuals.active"
                                    class="col-md-6"
                                    color="success"
                                    hide-details
                                    @change="updateStatus"
                                />
                                <v-checkbox
                                    v-model="userData.is_active"
                                    :label="this.$store.state.lang.lang_map.main.give_access"
                                    class="col-md-6"
                                    color="success"
                                    hide-details
                                    @change="showIsAccessedModal(userData)"
                                />
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col md="6">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{this.$store.state.lang.lang_map.individuals.assigned_companies}}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="companies"
                            class="elevation-1"
                            dense
                            item-key="id"
                            @click:row="showCompany"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            v-bind="attrs"
                                            v-on="on"
                                            icon
                                            @click.native.stop="removeEmployeeProcess(item)">
                                            <v-icon
                                                small

                                            >
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.company.delete_contact }}</span>
                                </v-tooltip>
                            </template>
                        </v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{this.$store.state.lang.lang_map.individuals.new_customer}}
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col class="pa-1" cols="md-12">
                                                <v-autocomplete
                                                    v-model="employeeForm.client_id"
                                                    :color="themeColor"
                                                    :error-messages="employeeForm.id"
                                                    :items="customers"
                                                    :label="this.$store.state.lang.lang_map.main.company"
                                                    :placeholder="this.$store.state.lang.lang_map.main.search"
                                                    hide-no-data
                                                    hide-selected
                                                    item-text="name"
                                                    item-value="id"
                                                ></v-autocomplete>
                                            </v-col>
                                            <v-text-field
                                                v-model="employeeForm.description"
                                                :color="themeColor"
                                                :label="this.$store.state.lang.lang_map.main.description"
                                                class="pa-1"
                                                dense
                                                type="text"
                                            ></v-text-field>
                                        </div>
                                        <v-btn
                                            :color="themeColor"
                                            bottom
                                            dark
                                            fab
                                            right
                                            small
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
            </v-col>
            <v-col md="6">
                <v-card class="elevation-6">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{this.$store.state.lang.lang_map.individuals.contact_info}}</v-toolbar-title>
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
                                                v-for="(item, i) in userData.emails"
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
                                                    <v-icon :title="langMap.profile.login_email" small>
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
                                                v-for="(item, i) in userData.phones"
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
                                                v-for="(item, i) in userData.addresses"
                                                :key="item.id"
                                            >
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon v-text="item.type.icon"></v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="">
                                                        {{item.street}}
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
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="emailForm.email"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :label="langMap.main.email"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="6">
                                                            <v-select
                                                                v-model="emailForm.email_type"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :items="emailTypes"
                                                                :label="langMap.main.type"
                                                                dense
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
                                                            @click="addEmail"
                                                        >
                                                            <v-icon>mdi-plus</v-icon>
                                                        </v-btn>
                                                    </div>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{this.$store.state.lang.lang_map.main.new_phone}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="phoneForm.phone"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :label="langMap.main.phone"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="6">
                                                            <v-select
                                                                v-model="phoneForm.phone_type"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :items="phoneTypes"
                                                                :label="langMap.main.type"
                                                                dense
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
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
                                                {{this.$store.state.lang.lang_map.main.new_address}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-12">
                                                            <v-textarea
                                                                v-model="addressForm.address.street"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :label="langMap.main.street"
                                                                dense
                                                                no-resize
                                                                rows="3"
                                                            ></v-textarea>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="addressForm.address.postal_code"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :label="langMap.main.postal_code"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="addressForm.address.city"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :label="langMap.main.city"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-select
                                                                v-model="addressForm.address.country_id"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :items="countries"
                                                                :label="langMap.main.country"
                                                                dense
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    ({{ data.item.iso_3166_2 }}) {{ localized(data.item)
                                                                    }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    ({{ data.item.iso_3166_2 }}) {{ localized(data.item)
                                                                    }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="6">
                                                            <v-select
                                                                v-model="addressForm.address_type"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                :items="addressTypes"
                                                                :label="langMap.main.type"
                                                                dense
                                                                item-text="name"
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
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
            </v-col>
            <v-col md="6">
                <v-card class="elevation-6">
                    <v-toolbar
                        :color="themeColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title>{{this.$store.state.lang.lang_map.individuals.social_info}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-list
                            dense
                            subheader
                        >
                            <v-list-item-group :color="themeColor">
                                <v-list-item
                                    v-for="(item) in userData.socials"
                                    :key="item.id"
                                >
                                    <v-list-item-icon v-if="item.type">
                                        <v-icon v-text="item.type.icon"></v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.social_link"></v-list-item-title>
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
                                            <v-col class="pa-1" cols="md-6">
                                                <v-text-field
                                                    v-model="socialForm.social_link"
                                                    :color="themeColor"
                                                    :item-color="themeColor"
                                                    :label="langMap.main.link"
                                                    dense
                                                ></v-text-field>
                                            </v-col>
                                            <v-col class="pa-1" cols="6">
                                                <v-select
                                                    v-model="socialForm.social_type"
                                                    :color="themeColor"
                                                    :item-color="themeColor"
                                                    :items="socialTypes"
                                                    :label="langMap.main.type"
                                                    dense
                                                    item-text="name"
                                                    item-value="id"
                                                >
                                                    <template slot="selection" slot-scope="data">
                                                        <v-icon left small v-text="data.item.icon"></v-icon>
                                                        {{ localized(data.item) }}
                                                    </template>
                                                    <template slot="item" slot-scope="data">
                                                        <v-icon left small v-text="data.item.icon"></v-icon>
                                                        {{ localized(data.item) }}
                                                    </template>
                                                </v-select>
                                            </v-col>
                                            <v-btn
                                                :color="themeColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                small
                                                @click="submitNewData(userData.id, socialForm, 'addSocial')"
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
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-dialog v-model="removeEmployeeDialog" max-width="480" persistent>
                <v-card>
                    <v-card-title>{{ langMap.main.delete_selected }}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeEmployeeDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="removeEmployee(selectedEmployeeId)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="rolesDialog" max-width="600px" persistent>
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_info}}: {{singleUserForm.user.name}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-select
                                v-model="singleUserForm.role_ids"
                                :color="themeColor"
                                :item-color="themeColor"
                                :items="roles"
                                :label="langMap.main.role"
                                item-value="id"
                                multiple
                            >
                                <template slot="selection" slot-scope="data">
                                    {{ langMap.roles[data.item.name] }}
                                </template>
                                <template slot="item" slot-scope="data">
                                    {{ langMap.roles[data.item.name] }}
                                </template>
                            </v-select>
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
            <v-dialog v-model="isAccessedDialog" max-width="600px" persistent>
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_info}}: {{userData.full_name}}</span>
                    </v-card-title>
                    <v-card-text>
                        {{userData.is_active === true ?
                        this.$store.state.lang.lang_map.main.give_access :
                        this.$store.state.lang.lang_map.main.remove_access
                        }}
                        <v-select
                            :disabled="userData.is_active === true"
                            class="mx-4"
                            v-model="primaryEmailId"
                            :color="themeColor"
                            item-value="id"
                            item-text="email"
                            :item-color="themeColor"
                            :items="userData.emails"
                            :label="langMap.main.email"
                        ></v-select>

                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text @click="isAccessedDialog = false; userData.is_active = !userData.is_active">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="changeIsAccessed">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updatePhoneDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_phone}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field v-model="phoneForm.phone" :color="themeColor" :item-color="themeColor"
                                                  :label="langMap.main.phone" dense></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select v-model="phoneForm.phone_type" :color="themeColor"
                                              :item-color="themeColor" :items="phoneTypes" :label="langMap.main.type"
                                              dense item-value="id">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updatePhoneDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updatePhoneDlg=false; updatePhone()">
                            {{langMap.main.save}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateSocialDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_social}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field v-model="socialForm.social_link" :color="themeColor"
                                                  :item-color="themeColor" :label="langMap.main.link"
                                                  dense></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select v-model="socialForm.social_type" :color="themeColor"
                                              :item-color="themeColor" :items="socialTypes" :label="langMap.main.type"
                                              dense item-value="id">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateSocialDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateSocialDlg=false; updateSocial()">
                            {{langMap.main.save}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateAddressDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_address}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-12">
                                    <v-textarea
                                        v-model="addressForm.address.street"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        :label="langMap.main.street"
                                        dense
                                        no-resize
                                        rows="3"
                                    ></v-textarea>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field
                                        v-model="addressForm.address.postal_code"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        :label="langMap.main.postal_code"
                                        dense
                                    ></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field
                                        v-model="addressForm.address.city"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        :label="langMap.main.city"
                                        dense
                                    ></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select
                                        v-model="addressForm.address.country_id"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        :items="countries"
                                        :label="langMap.main.country"
                                        :rules="['Required']"
                                        dense
                                        item-value="id"
                                    >
                                        <template slot="selection" slot-scope="data">
                                            ({{ data.item.iso_3166_2 }}) {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            ({{ data.item.iso_3166_2 }}) {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                                <v-col class="pa-1" cols="6">
                                    <v-select
                                        v-model="addressForm.address_type"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        :items="addressTypes"
                                        :label="langMap.main.type"
                                        dense
                                        item-value="id"
                                    >
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateAddressDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateAddressDlg=false; updateAddress()">
                            {{langMap.main.save}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateEmailDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_email}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field v-model="emailForm.email" :color="themeColor" :item-color="themeColor"
                                                  :label="langMap.main.email" dense></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select v-if="emailForm.email_type === 1" readonly v-model="emailForm.email_type" :color="themeColor"
                                              :item-color="themeColor" :items="emailTypes" :label="langMap.main.type"
                                              dense item-value="id">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                    <v-select v-else v-model="emailForm.email_type" :color="themeColor"
                                              :item-color="themeColor" :items="emailTypes" :label="langMap.main.type"
                                              dense item-value="id">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateEmailDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateEmailDlg=false; updateEmail()">
                            {{langMap.main.save}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

        </v-row>
    </v-container>

</template>

<script>
import EventBus from "../../components/EventBus";

export default {
    data() {
        return {
            themeColor: this.$store.state.themeColor,
            headers: [
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'clients.name'},
                {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'description'},
                {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
            ],
            footerProps: {
                itemsPerPage: 10,
                disableItemsPerPage: true,
                itemsPerPageText: this.$store.state.lang.lang_map.main.items_per_page
            },
            langMap: this.$store.state.lang.lang_map,
            companies: [],
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            errors: [],
            enableToEdit: false,
            userData: {
                id: '',
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
                emails: [],
                status: ''
            },
            singleUserForm: {
                user: '',
                role_ids: [],
                company_user_id: ''
            },
            roles: [],
            rolesDialog: false,
            employeeForm: {
                client_id: null,
                company_user_id: null,
                description: ''
            },
            phoneForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                phone: '',
                phone_type: ''
            },
            addressForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                address: {
                    street: '',
                    city: '',
                    country_id: ''
                },
                address_type: ''
            },
            socialForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                social_link: '',
                social_type: ''
            },
            emailForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                email: '',
                email_type: ''
            },
            phoneTypes: [],
            addressTypes: [],
            socialTypes: [],
            emailTypes: [],
            isCustomersLoading: false,
            customers: [],
            countries: [],
            updatePhoneDlg: false,
            updateAddressDlg: false,
            updateSocialDlg: false,
            updateEmailDlg: false,
            removeEmployeeDialog: false,
            selectedEmployeeId: null,
            isAccessedDialog: false,
            selectedIsAccessedItem: null,
            primaryEmailId: null
        }
    },
    mounted() {
        this.getCountries()
        this.getUser()
        this.getPhoneTypes()
        this.getAddressTypes()
        this.getSocialTypes()
        this.getEmailTypes()
        this.getRoles()
        this.getClients()
        // if (localStorage.getItem('auth_token')) {
        //     this.$router.push('tickets')
        // }
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
        getUser() {
            axios.get(`/api/user/find/${this.$route.params.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.userData = response.data
                    this.primaryEmailId = this.userData.email_id
                    this.companies = this.userData.employee.assigned_to_clients.length > 0 ? this.userData.employee.assigned_to_clients : this.userData.employee.companies
                    // console.log(this.companies);
                    this.employeeForm.company_user_id = this.userData.employee.id
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        updateUser() {
            this.snackbar = false;
            axios.post('/api/user', this.userData).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.langMap.company.user_updated;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.enableToEdit = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;                }
            });
        },
        updateStatus() {
            this.snackbar = false;
            axios.post('/api/user', this.userData).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.userData.status ? this.langMap.company.employee_activated : this.langMap.company.employee_deactivated;                    this.actionColor = 'success'
                    this.snackbar = true
                    this.enableToEdit = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;                }
            });
        },
        submitNewData(id, data, method) {
            data.entity_id = id
            this[method](data)
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
        addPhone() {
            this.phoneForm.entity_id = this.userData.id
            axios.post('/api/phone', this.phoneForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.langMap.company.phone_created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        updatePhone() {
            axios.patch(`/api/phone/${this.phoneForm.id}`, this.phoneForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.phoneForm.id = '';
                    this.getUser();
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
        removeEmployeeProcess(item) {
            console.log(item);
            this.selectedEmployeeId = item.id
            this.removeEmployeeDialog = true
        },
        removeEmployee(id) {
            axios.delete(`/api/client/employee/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.rolesDialog = false
                    this.snackbarMessage = this.langMap.company.employee_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.removeEmployeeDialog = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
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
                    this.snackbarMessage = this.langMap.company.phone_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        addAddress() {
            this.addressForm.entity_id = this.userData.id
            axios.post('/api/address', this.addressForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.langMap.company.address_created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
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
                    this.addressForm.address.postal_code = '';
                    this.addressForm.address.city = '';
                    this.addressForm.address.country_id = '';
                    this.getUser()
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
                    this.getUser()
                    this.snackbarMessage = this.langMap.company.address_deleted;
                    this.errorType = 'success'
                    this.alert = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;

                }
            });
        },
        addSocial(form) {
            axios.post('/api/social', form).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
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
                    this.getUser()
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
                    this.getUser()
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
        showCompany(item) {
            this.$router.push(`/customer/${item.clients.id}`)
        },
        changeIsAccessed() {
            let request = {}
            request.user_id = this.selectedIsAccessedItem.id
            request.email_id = this.primaryEmailId
            console.log(this.primaryEmailId);
            request.is_active = this.selectedIsAccessedItem.is_active
            this.singleUserForm.role_ids = this.selectedIsAccessedItem.is_active == true ? [6] : [];
            this.singleUserForm.user = this.userData
            this.singleUserForm.company_user_id = this.userData.employee.id
            axios.post(`/api/user/is_active`, request).then(response => {
                response = response.data
                this.isAccessedDialog = false
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.selectedIsAccessedItem.is_active ? this.langMap.history_actions.access_details_updated_msg : this.langMap.history_actions.access_details_updated_msg;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
            this.updateRole()
        },
        showIsAccessedModal(item)
        {
            this.selectedIsAccessedItem = item
            this.isAccessedDialog = true
        },
        showRolesModal() {
            this.rolesDialog = true
            this.singleUserForm.user = this.userData
            this.singleUserForm.role_ids = []
            this.singleUserForm.company_user_id = this.userData.employee.id
            this.userData.employee.roles.forEach(role => {
                this.singleUserForm.role_ids.push(role.id)
            })
            // console.log(item);
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
        updateRole() {
            axios.patch(`/api/roles`, this.singleUserForm).then(response => {
                response = response.data
                if (response.success === true) {
                    // this.getClient()
                    // this.rolesDialog = false
                    // this.snackbarMessage = this.langMap.company.role_updated;
                    // this.actionColor = 'success'
                    // this.snackbar = true;
                } else {
                    // this.snackbarMessage = this.langMap.main.generic_error;
                    // this.actionColor = 'error';
                    // this.snackbar = true;
                }
            });
        },
        getClients() {
            this.isCustomersLoading = true
            axios.get(`/api/client`)
                .then(response => {
                    response = response.data
                    this.isCustomersLoading = false

                    if (response.success === true) {
                        this.customers = response.data.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
        },
        addEmployee() {
            axios.post(`/api/client/employee`, this.employeeForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.langMap.company.employee_created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
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
        addEmail() {
            this.emailForm.entity_id = this.userData.id
            axios.post('/api/email', this.emailForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
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
                    this.getUser();
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
                    this.getUser()
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
    }
}
</script>
