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
                                <v-col class="col-md-12">
                                    <v-list
                                        dense
                                        subheader
                                    >
                                        <v-list-item-group :color="themeColor">
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
                                    <v-expansion-panels
                                        multiple
                                    >
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
                                    <p v-if="item.user_data.email"><strong>{{langMap.main.email}}:</strong> {{
                                        item.user_data.email }}
                                    </p>
                                    <p v-if="item.user_data.phones.length > 0"><strong>{{langMap.main.phone}}:</strong>
                                    </p>
                                    <p v-if="item.user_data.phones.length > 0"
                                       v-for="phoneItem in item.user_data.phones"><v-icon small dense left v-if="phoneItem.type">{{phoneItem.type.icon}}</v-icon> {{ phoneItem.phone }}
                                        <span v-if="phoneItem.type">({{ localized(phoneItem.type) }})</span></p>
                                    <p v-if="item.user_data.addresses.length > 0">
                                        <strong>{{langMap.main.address}}:</strong></p>
                                    <p v-if="item.user_data.addresses.length > 0"
                                       v-for="addressItem in item.user_data.addresses"><v-icon small dense left v-if="addressItem.type">{{addressItem.type.icon}}</v-icon>
                                        {{ addressItem.street }}
                                        {{addressItem.postal_code }} {{ addressItem.city }}
                                        <span v-if="addressItem.country">{{localized(addressItem.country)}}</span>
                                        <span v-if="addressItem.type">({{ localized(addressItem.type) }})</span></p>
                                    <p><strong>{{langMap.main.actions}}:</strong></p>
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
                                <div class="justify-center" v-if="item.user_data">{{ item.user_data.name }} {{
                                    item.user_data.surname }}
                                </div>
                            </template>
                            <template v-slot:item.actions="{ item }">

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
                                                    :color="themeColor"
                                                    :label="langMap.main.name"
                                                    name="name"
                                                    type="text"
                                                    v-model="employeeForm.name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    :color="themeColor"
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
                                                    :color="themeColor"
                                                    :item-color="themeColor"
                                                    item-value="id"
                                                    :items="roles"
                                                    v-model="employeeForm.role_id"
                                                >
                                                    <template slot="selection" slot-scope="data">
                                                        {{ langMap.roles[data.item.name] }}
                                                    </template>
                                                    <template slot="item" slot-scope="data">
                                                        {{ langMap.roles[data.item.name] }}
                                                    </template>
                                                </v-select>
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
        <div class="row">
            <div class="col-md-12">
                <v-spacer></v-spacer>
                <v-card class="elevation-12">
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
                                                <v-text-field
                                                    :color="themeColor"
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
                                                                            <v-textarea
                                                                                no-resize
                                                                                rows="3"
                                                                                :color="themeColor"
                                                                                :item-color="themeColor"
                                                                                v-model="userAddressForm.address.street"
                                                                                :label="langMap.main.street"
                                                                                dense
                                                                            ></v-textarea>
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
                                            <v-list-item-icon><v-icon small left v-text="data.item.icon"></v-icon></v-list-item-icon>
                                            <v-list-item-content v-text="localized(data.item)"></v-list-item-content>
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-list-item-icon><v-icon small left v-text="data.item.icon"></v-icon></v-list-item-icon>
                                            <v-list-item-content v-text="localized(data.item)"></v-list-item-content>
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
                                            <v-list-item-icon><v-icon small left v-text="data.item.icon"></v-icon></v-list-item-icon>
                                            <v-list-item-content v-text="localized(data.item)"></v-list-item-content>
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-list-item-icon><v-icon small left v-text="data.item.icon"></v-icon></v-list-item-icon>
                                            <v-list-item-content v-text="localized(data.item)"></v-list-item-content>
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
                productCategoryForm: {
                    name: '',
                    company_id: '',
                    parent_id: '',
                },
                phoneTypes: [],
                addressTypes: [],
                socialTypes: [],
                countries: [],
                productCategoriesFlat: [],
                productCategoriesTree: [],
                selectedProductCategoryId: null,
                updatePhoneDlg: false,
                updateAddressDlg: false,
                updateSocialDlg: false
            }
        },
        mounted() {
            this.getCompany();
            this.getRoles();
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getSocialTypes();
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
            addEmployee() {
                axios.post(`/api/company/${this.$route.params.id}/employee`, this.employeeForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getCompany()
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
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
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
