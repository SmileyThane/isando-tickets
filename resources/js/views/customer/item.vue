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
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeColor"
                        dense
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.company.info}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateClient">
                            {{langMap.main.update}}
                        </v-btn>

                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                :color="themeColor"
                                :label="langMap.company.name"
                                name="client_name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="client.client_name"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                            <v-text-field
                                :color="themeColor"
                                :label="langMap.company.description"
                                name="client_description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="client.client_description"
                                required
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                        </v-form>
                        <v-checkbox
                            :label="this.$store.state.lang.lang_map.customer.active"
                            color="success"
                            v-model="client.is_active"
                            @change="changeIsActiveClient(client)"
                            hide-details
                        ></v-checkbox>
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
                        <v-toolbar-title>{{langMap.company.additional_info}}</v-toolbar-title>
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
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in client.emails"
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
                                                v-for="(item, i) in client.phones"
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
                                                v-for="(item, i) in client.addresses"
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
                                                        v-text="localized(item.type.name)"></v-list-item-subtitle>
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
                                                            @click="submitNewData(client.id, emailForm, 'addEmail')"
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
                                                {{langMap.main.new_address}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-12" class="pa-1">
                                                            <v-textarea
                                                                no-resize
                                                                rows="3"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.street"
                                                                :label="langMap.main.street"
                                                                dense
                                                            ></v-textarea>
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
                        <v-toolbar-title>{{langMap.product.info}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="productHeaders"
                            :items="client.products"
                            class="elevation-1"
                            item-key="id"
                            :footer-props="footerProps"
                            dense
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn @click="showProduct(item.product_data)" icon v-bind="attrs" v-on="on">
                                            <v-icon
                                                small
                                            >
                                                mdi-eye
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{langMap.customer.show_product}}</span>
                                </v-tooltip>
                            </template>
                        </v-data-table>
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
                            :items="client.employees"
                            class="elevation-1"
                            item-key="id"
                            :footer-props="footerProps"
                            show-expand
                            dense
                            @click:row="showUser"
                        >
                            <template v-slot:item.actions="{ item }">

                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <p></p>
                                    <p v-if="item.employee.user_data.contact_email && item.employee.user_data.contact_email.email">
                                        <strong>{{langMap.main.email}}:</strong>
                                    </p>
                                    <p v-if="item.employee.user_data.contact_email && item.employee.user_data.contact_email.email">
                                        <v-icon v-if="item.employee.user_data.contact_email.type" small dense :title="localized(item.employee.user_data.contact_email.type)">{{item.employee.user_data.contact_email.type.icon}}</v-icon>
                                        {{item.employee.user_data.contact_email.email}}
                                    </p>
                                    <p v-if="item.employee.user_data.phones.length > 0">
                                        <strong>{{langMap.main.phone}}:</strong></p>
                                    <p v-for="phoneItem in item.employee.user_data.phones"><v-icon small dense left v-if="phoneItem.type">{{phoneItem.type.icon}}</v-icon> {{ phoneItem.phone }}
                                        <span v-if="phoneItem.type">({{ localized(phoneItem.type) }})</span></p>
                                    <!--                                    <p><strong>Lang:</strong></p>-->
                                    <!--                                    <p>{{ item.employee.user_data.lang }}</p>-->
                                    <p v-if="item.employee.user_data.addresses.length > 0"><strong>{{langMap.main.address}}:</strong>
                                    </p>
                                    <p v-for="addressItem in item.employee.user_data.addresses"><v-icon small dense left v-if="addressItem.type">{{addressItem.type.icon}}</v-icon>
                                        {{addressItem.street}}
                                        {{addressItem.postal_code}} {{addressItem.city}}
                                        <span v-if="addressItem.country">{{localized(addressItem.country)}}</span>
                                        <span v-if="addressItem.type">({{ localized(addressItem.type) }})</span></p>
                                    <p><strong>{{langMap.main.actions}}:</strong></p>
                                    <v-tooltip top>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn :disabled="!item.employee.user_data.is_active"
                                                   @click="sendInvite(item.employee)"
                                                   icon v-bind="attrs" v-on="on">
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
                                            <v-btn @click="showUser(item)" icon v-bind="attrs" v-on="on">
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
                                            <v-btn
                                                @click="removeEmployeeProcess(item)"
                                                icon
                                                v-bind="attrs"
                                                v-on="on">
                                                <v-icon
                                                    small

                                                >
                                                    mdi-delete
                                                </v-icon>
                                            </v-btn>
                                        </template>
                                        <span>{{langMap.company.delete_contact}}</span>
                                    </v-tooltip>
                                </td>

                            </template>
                            <template v-slot:item.user_data="{ item }">
                                <div class="justify-center" v-if="item.employee.user_data">
                                    {{item.employee.user_data.full_name }}
                                </div>
                            </template>
                        </v-data-table>
                        <template>
                            <v-dialog v-model="removeEmployeeDialog" persistent max-width="480">
                                <v-card>
                                    <v-card-title class="headline">{{langMap.main.delete_selected}}?</v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="grey darken-1" text @click="removeEmployeeDialog = false">
                                            {{langMap.main.cancel}}
                                        </v-btn>
                                        <v-btn color="red darken-1" text @click="removeEmployee(selectedEmployeeId)">
                                            {{langMap.main.delete}}
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </template>
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
                                            <div class="col-md-6">
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.name"
                                                    name="name"
                                                    type="text"
                                                    v-model="employeeForm.name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    :color="themeColor"
                                                    :label="langMap.main.email"
                                                    name="email"
                                                    type="text"
                                                    v-model="employeeForm.email"
                                                    required
                                                ></v-text-field>
                                                <v-checkbox
                                                    :label="langMap.main.give_access"
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
                                    v-for="(item) in client.socials"
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
                                            <v-col cols="md-12" class="pa-1">
                                                <v-text-field
                                                    :color="themeColor"
                                                    :item-color="themeColor"
                                                    v-model="socialForm.social_link"
                                                    :label="langMap.main.link"
                                                    dense
                                                ></v-text-field>
                                            </v-col>
                                            <v-col cols="12" class="pa-1">
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
                            <v-row>
                                <v-col cols="md-12" class="pa-1">
                                    <v-textarea
                                        no-resize
                                        rows="3"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.street"
                                        :label="langMap.main.street"
                                        dense
                                    ></v-textarea>
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
                            </v-row>
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
                                    <v-select :color="themeColor" :item-color="themeColor"
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

        </v-row>

    </v-container>
</template>


<script>
    import EventBus from "../../components/EventBus";

    export default {

        data() {

            return {
                themeColor: this.$store.state.themeColor,
                langMap: this.$store.state.lang.lang_map,
                headers: [
                    {text: '', value: 'data-table-expand'},
                    // {
                    //     text: 'ID',
                    //     align: 'start',
                    //     sortable: false,
                    //     value: 'id',
                    // },
                    {text: `${this.$store.state.lang.lang_map.company.user}`, value: 'user_data'},
                    // {text: 'email', value: 'user_data.email'},
                    {text: `${this.$store.state.lang.lang_map.main.roles}`, value: 'employee.role_names'},
                    {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
                ],
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
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                errors: [],
                footerProps: {
                    itemsPerPage: 10,
                    disableItemsPerPage: true,
                    itemsPerPageText: this.$store.state.lang.lang_map.main.items_per_page
                },
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
                    name: '',
                    email: '',
                    client_id: '',
                    is_active: false
                },
                selectedEmployeeId: null,
                removeEmployeeDialog: false,
                roles: [
                    {
                        id: '',
                        name: ''
                    }
                ],
                phoneForm: {
                    id: '',
                    entity_id: '',
                    entity_type: 'App\\Client',
                    phone: '',
                    phone_type: ''
                },
                addressForm: {
                    id: '',
                    entity_id: '',
                    entity_type: 'App\\Client',
                    address: {
                        street: '',
                        postal_code: '',
                        city: '',
                        country_id: ''
                    },
                    address_type: ''
                },
                socialForm: {
                    id: '',
                    entity_id: '',
                    entity_type: 'App\\Client',
                    social_link: '',
                    social_type: ''
                },
                emailForm: {
                    entity_id: '',
                    entity_type: 'App\\Client',
                    email: '',
                    email_type: ''
                },
                phoneTypes: [],
                addressTypes: [],
                socialTypes: [],
                emailTypes: [],
                countries: [],
                updatePhoneDlg: false,
                updateAddressDlg: false,
                updateSocialDlg: false,
                updateEmailDlg: false
            }
        },
        mounted() {
            this.getClient()
            this.getRoles()
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
            this.getEmailTypes();
            this.getCountries();
            this.employeeForm.client_id = this.$route.params.id;
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
            addEmployee() {
                axios.post(`/api/client/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.snackbarMessage = 'Contact was added successfully'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
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
                        this.getClient()
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
                        this.getClient();
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
                        this.getClient()
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
                        this.getClient()
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
                        this.getClient()
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
                        this.getClient()
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
                        this.getClient()
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
                        this.addressForm.address.postal_code = '';
                        this.addressForm.address.city = '';
                        this.addressForm.address.country_id = '';
                        this.getClient()
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
                        this.getClient()
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
            changeIsActiveClient(item) {
                let request = {}
                request.id = item.id
                request.is_active = item.is_active
                axios.post(`/api/client/is_active`, request).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.snackbarMessage = item.is_active ? 'Contact activated' : 'Contact deactivated'
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            removeEmployeeProcess(item) {
                this.selectedEmployeeId = item.id
                this.removeEmployeeDialog = true
            },
            removeEmployee(id) {
                axios.delete(`/api/client/employee/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
                        this.rolesDialog = false
                        this.snackbarMessage = 'Contact was removed'
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
            showUser(item) {
                this.$router.push(`/employee/${item.employee.user_id}`)
            },
            showProduct(item) {
                this.$router.push(`/product/${item.id}`)
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
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
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
            addEmail(form) {
                axios.post('/api/email', form).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getClient()
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
                        this.getClient();
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
                        this.getClient()
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
            clientUpdates(value) {
                this.clientIsLoaded = true;
                // console.log(this.singleUserForm.user);
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
