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
                <v-card class="elevation-12 ">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.company.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" :color="themeFgColor" @click="enableToEdit = true">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEdit" :color="themeFgColor"
                               :style="`color: ${themeBgColor}; margin-right: 10px;`"
                               @click="cancelUpdateCompany">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" :color="themeFgColor" :style="`color: ${themeBgColor};`"
                               @click="updateCompany">
                            {{ langMap.main.update }}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-img v-if="$helpers.auth.checkPermissionByIds([95]) && companyLogo"
                                   :src="companyLogo"
                                   contain
                                   max-height="7em"
                                   max-width="15em"
                            />

                            <v-text-field
                                v-model="company.name"
                                :color="themeBgColor"
                                :disabled="$helpers.auth.checkPermissionByIds([36, 37])"
                                :label="langMap.company.name"
                                :readonly="!enableToEdit"
                                dense
                                name="name"
                                prepend-icon="mdi-rename-box"
                                required
                                type="text"
                            ></v-text-field>

                            <v-text-field
                                v-model="company.description"
                                :color="themeBgColor"
                                :label="langMap.company.description"
                                :readonly="!enableToEdit"
                                dense
                                name="description"
                                prepend-icon="mdi-comment-text"
                                required
                                type="text"
                            ></v-text-field>

                            <v-text-field
                                v-model="company.company_number"
                                :color="themeBgColor"
                                :label="langMap.company.company_number"
                                :readonly="!enableToEdit"
                                dense
                                name="company_number"
                                prepend-icon="mdi-numeric"
                                required
                                type="text"
                            ></v-text-field>
                            <v-menu
                                :close-on-content-click="false"
                                :disabled="!enableToEdit"
                                :nudge-right="40"
                                min-width="290px"
                                offset-y
                                transition="scale-transition"

                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="company.registration_date"
                                        v-bind="attrs"
                                        v-on="on"
                                        :color="themeBgColor"
                                        :label="langMap.company.registration_date"
                                        name="registration_date"
                                        prepend-icon="mdi-calendar"
                                        readonly
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    first-day-of-week="1"
                                    v-model="company.registration_date"
                                    :color="themeBgColor"
                                ></v-date-picker>
                            </v-menu>
                            <v-select
                                v-model="company.currency_id"
                                :color="themeBgColor"
                                :items="$store.getters['Currencies/getCurrencies']"
                                :label="langMap.tracking.settings.currency"
                                :placeholder="langMap.tracking.settings.currency"
                                :readonly="!enableToEdit"
                                dense
                                item-text="name"
                                item-value="id"
                                prepend-icon="mdi-currency-usd"
                                required
                            >
                                <template v-slot:item="props">
                                    <div class="d-flex flex-row" style="width: 100%">
                                        <div class="d-flex-inline flex-grow-1" style="width: 100%">{{ props.item.name }}
                                            ({{ props.item.slug }}) {{ props.item.symbol }}
                                        </div>
                                        <div class="d-flex-inline text-right"></div>
                                    </div>
                                </template>
                                <template v-slot:selection="props">
                                    <div class="d-flex flex-row" style="width: 100%">
                                        <div class="d-flex-inline flex-grow-1" style="width: 100%">{{ props.item.name }}
                                            ({{ props.item.slug }}) {{ props.item.symbol }}
                                        </div>
                                        <div class="d-flex-inline text-right"></div>
                                    </div>
                                </template>
                            </v-select>
                        </v-form>
                    </v-card-text>
                </v-card>
                <br>
                <v-card class="elevation-12 ">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.company.additional_info
                            }}
                        </v-toolbar-title>
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
                                        <v-list-item-group :color="themeBgColor">
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
                                                                          v-text="$helpers.i18n.localized(item.type)"></v-list-item-subtitle>
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
                                                                          v-text="$helpers.i18n.localized(item.type)"></v-list-item-subtitle>
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
                                                        <span v-if="item.street">{{ item.street }}, </span>
                                                        <span v-if="item.street2">{{ item.street2 }}, </span>
                                                        <span v-if="item.street3">{{ item.street3 }}</span>
                                                        <br>{{ item.postal_code }}&nbsp;&nbsp;{{ item.city }},
                                                        <span v-if="item.country">{{
                                                                $helpers.i18n.localized(item.country)
                                                            }}</span>
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                                          v-text="$helpers.i18n.localized(item.type)"></v-list-item-subtitle>
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
                                        <v-expansion-panel @click="resetEmail">
                                            <v-expansion-panel-header>
                                                {{ this.$store.state.lang.lang_map.main.new_email }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                        mdi-plus
                                                    </v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="emailForm.email"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.email"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="6">
                                                            <v-select
                                                                v-model="emailForm.email_type"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :items="emailTypes"
                                                                :label="langMap.main.type"
                                                                dense
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ $helpers.i18n.localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ $helpers.i18n.localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeBgColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
                                                            @click="submitNewData(company.id, emailForm, 'addEmail')"
                                                        >
                                                            <v-icon :color="themeBgColor"
                                                                    :style="`color: ${themeFgColor};`">mdi-plus
                                                            </v-icon>
                                                        </v-btn>
                                                    </div>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                        <v-expansion-panel @click="resetPhone">
                                            <v-expansion-panel-header>
                                                {{ langMap.main.new_phone }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                        mdi-plus
                                                    </v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="phoneForm.phone"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.phone"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="6">
                                                            <v-select
                                                                v-model="phoneForm.phone_type"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :items="phoneTypes"
                                                                :label="langMap.main.type"
                                                                dense
                                                                item-text="name"
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-list-item-icon>
                                                                        <v-icon small v-text="data.item.icon"></v-icon>
                                                                    </v-list-item-icon>
                                                                    <v-list-item-content>
                                                                        {{ $helpers.i18n.localized(data.item) }}
                                                                    </v-list-item-content>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-list-item-icon>
                                                                        <v-icon small v-text="data.item.icon"></v-icon>
                                                                    </v-list-item-icon>
                                                                    <v-list-item-content>
                                                                        {{ $helpers.i18n.localized(data.item) }}
                                                                    </v-list-item-content>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeBgColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
                                                            @click="submitNewData(company.id, phoneForm, 'addPhone')"
                                                        >
                                                            <v-icon :color="themeBgColor"
                                                                    :style="`color: ${themeFgColor};`">mdi-plus
                                                            </v-icon>
                                                        </v-btn>
                                                    </div>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                        <v-expansion-panel @click="resetAddress">
                                            <v-expansion-panel-header>
                                                {{ langMap.main.new_address }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                        mdi-plus
                                                    </v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-12">
                                                            <v-text-field
                                                                v-model="addressForm.address.street"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.address_line1"
                                                                dense
                                                                no-resize
                                                                rows="3"
                                                            ></v-text-field>
                                                            <v-text-field
                                                                v-model="addressForm.address.street2"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.address_line2"
                                                                dense
                                                                no-resize
                                                                rows="3"
                                                            ></v-text-field>
                                                            <v-text-field
                                                                v-model="addressForm.address.street3"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.address_line3"
                                                                dense
                                                                no-resize
                                                                rows="3"
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="addressForm.address.postal_code"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.postal_code"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="addressForm.address.city"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.city"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-select
                                                                v-model="addressForm.address.country_id"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :items="countries"
                                                                :label="langMap.main.country"
                                                                :rules="['Required']"
                                                                dense
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    ({{ data.item.iso_3166_2 }}) {{
                                                                        $helpers.i18n.localized(data.item)
                                                                    }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    ({{ data.item.iso_3166_2 }}) {{
                                                                        $helpers.i18n.localized(data.item)
                                                                    }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="6">
                                                            <v-select
                                                                v-model="addressForm.address_type"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :items="addressTypes"
                                                                :label="langMap.main.type"
                                                                dense
                                                                item-text="name"
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ $helpers.i18n.localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ $helpers.i18n.localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeBgColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
                                                            @click="submitNewData(company.id, addressForm, 'addAddress')"
                                                        >
                                                            <v-icon :color="themeBgColor"
                                                                    :style="`color: ${themeFgColor};`">mdi-plus
                                                            </v-icon>
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
                <br>
                <v-card v-if="$helpers.auth.checkPermissionByIds([88])" class="elevation-12 ">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.profile.internal_billing
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-list-item v-for="(item, i) in company.billing" :key="item.id">
                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.name"></v-list-item-title>
                                        <v-list-item-subtitle
                                            v-text="item.cost + ' ' + currency.symbol"></v-list-item-subtitle>
                                    </v-list-item-content>
                                    <v-list-item-action>
                                        <v-icon small @click="editInternalBilling(item)">
                                            mdi-pencil
                                        </v-icon>
                                    </v-list-item-action>
                                    <v-list-item-action>
                                        <v-icon small @click="deleteInternalBilling(item.id)">
                                            mdi-delete
                                        </v-icon>
                                    </v-list-item-action>
                                </v-list-item>
                            </v-col>
                            <v-col cols="12">
                                <v-expansion-panels v-model="internalBillingEditor" accordion multiple>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>
                                            {{ langMap.main.add }}
                                            <template v-slot:actions>
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                    mdi-plus
                                                </v-icon>
                                            </template>
                                        </v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-form>
                                                <v-row>
                                                    <v-col cols="8">
                                                        <v-text-field
                                                            v-model="internalBillingForm.name"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :label="langMap.main.name"
                                                            dense
                                                        />
                                                    </v-col>
                                                    <v-col cols="3">
                                                        <v-text-field
                                                            v-model="internalBillingForm.cost"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :label="langMap.main.cost"
                                                            dense
                                                        />
                                                    </v-col>
                                                    <v-col cols="1">
                                                        <v-text-field
                                                            v-model="currency.symbol"
                                                            readonly
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :label="langMap.tracking.settings.currency"
                                                            dense
                                                        />
                                                    </v-col>
                                                    <v-btn
                                                        v-if="!internalBillingForm.id"
                                                        :color="themeBgColor"
                                                        bottom
                                                        dark
                                                        fab
                                                        right
                                                        small
                                                        @click="createInternalBilling"
                                                    >
                                                        <v-icon :color="themeBgColor"
                                                                :style="`color: ${themeFgColor};`">mdi-plus
                                                        </v-icon>
                                                    </v-btn>
                                                    <v-btn
                                                        v-if="internalBillingForm.id"
                                                        :color="themeBgColor"
                                                        bottom
                                                        dark
                                                        fab
                                                        right
                                                        small
                                                        @click="updateInternalBilling(internalBillingForm.id)"
                                                    >
                                                        <v-icon :color="themeBgColor"
                                                                :style="`color: ${themeFgColor};`">mdi-update
                                                        </v-icon>
                                                    </v-btn>
                                                </v-row>
                                            </v-form>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
                <br v-if="$helpers.auth.checkPermissionByIds([88])">
                <v-card v-if="$helpers.auth.checkPermissionByIds([87])" class="elevation-12">
                    <v-spacer></v-spacer>
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.company.product_categories
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-treeview
                                        :items="productCategoriesTree"
                                        activatable
                                        item-key="id"
                                        open-on-click
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
                                                {{ langMap.company.new_product_category }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                        mdi-plus
                                                    </v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="productCategoryForm.name"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.name"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="6">
                                                            <v-select
                                                                v-model="productCategoryForm.parent_id"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :items="productCategoriesFlat"
                                                                :label="langMap.company.parent_product_category"
                                                                dense
                                                                item-text="full_name"
                                                                item-value="id"
                                                            >
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeBgColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
                                                            @click="submitNewData(company.id, productCategoryForm, 'addProductCategory')"
                                                        >
                                                            <v-icon :color="themeBgColor"
                                                                    :style="`color: ${themeFgColor};`">mdi-plus
                                                            </v-icon>
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
                <br v-if="$helpers.auth.checkPermissionByIds([87])">
                <v-card v-if="$helpers.auth.checkPermissionByIds([86])" class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.product.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="productHeaders"
                            :items="company.products"
                            :loading="loading"
                            :options.sync="options"
                            class="elevation-1"
                            dense
                            item-key="id"
                            @update:options="updateItemsPerPage"
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
                        <br>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.product.add_new }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content style="padding-bottom: 0">
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <v-select
                                                    v-model="productForm.category_id"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :items="productCategoriesFlat"
                                                    :label="langMap.main.category"
                                                    item-text="full_name"
                                                    item-value="id"
                                                    name="category_id"
                                                    prepend-icon="mdi-rename-box"
                                                />
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    v-model="productForm.product_name"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.name"
                                                    name="product_name"
                                                    required
                                                    type="text"
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-textarea
                                                    v-model="productForm.product_description"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.description"
                                                    auto-grow
                                                    name="product_description"
                                                    required
                                                    rows="1"
                                                    type="text"
                                                ></v-textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    v-model="productForm.product_code"
                                                    :color="themeBgColor"
                                                    :label="langMap.product.code"
                                                    name="product_code"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-file-input
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :label="langMap.ticket.add_attachments"
                                                    :show-size="1000"
                                                    chips
                                                    multiple
                                                    prepend-icon="mdi-paperclip"
                                                    v-on:change="onFileChange('productForm')"
                                                >
                                                    <template v-slot:selection="{ index, text }">
                                                        <v-chip
                                                            :color="themeBgColor"
                                                            :text-color="themeFgColor"
                                                            class="ma-2">
                                                            {{ text }}
                                                        </v-chip>
                                                    </template>
                                                </v-file-input>
                                            </div>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                @click="addProduct"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                    mdi-plus
                                                </v-icon>
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
                <v-card v-if="$helpers.auth.checkPermissionByIds([85])" class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.company.company_contacts
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="company.employees"
                            :options.sync="options"
                            class="elevation-1"
                            dense
                            item-key="id"
                            show-expand
                            @click:row="showRolesModal"
                            @update:options="updateItemsPerPage"
                        >
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <p></p>
                                    <p v-if="item.user_data.emails && item.user_data.emails.length > 0">
                                        <strong>{{ langMap.main.email }}:</strong>
                                    </p>
                                    <p v-for="emailItem in item.user_data.emails"
                                       v-if="item.user_data.emails && item.user_data.emails.length > 0">
                                        <v-icon v-if="emailItem.type" dense left small>{{ emailItem.type.icon }}
                                        </v-icon>
                                        {{ emailItem.email }}
                                        <span v-if="emailItem.type">({{
                                                $helpers.i18n.localized(emailItem.type)
                                            }})</span></p>
                                    <p v-if="item.user_data.phones && item.user_data.phones.length > 0">
                                        <strong>{{ langMap.main.phone }}:</strong>
                                    </p>
                                    <p v-for="phoneItem in item.user_data.phones"
                                       v-if="item.user_data.phones && item.user_data.phones.length > 0">
                                        <v-icon v-if="phoneItem.type" dense left small>{{ phoneItem.type.icon }}
                                        </v-icon>
                                        {{ phoneItem.phone }}
                                        <span v-if="phoneItem.type">({{
                                                $helpers.i18n.localized(phoneItem.type)
                                            }})</span></p>
                                    <p v-if="item.user_data.addresses && item.user_data.addresses.length > 0">
                                        <strong>{{ langMap.main.address }}:</strong></p>
                                    <p v-for="addressItem in item.user_data.addresses"
                                       v-if="item.user_data.addresses && item.user_data.addresses.length > 0">
                                        <v-icon v-if="addressItem.type" dense left small>{{ addressItem.type.icon }}
                                        </v-icon>
                                        <span v-if="addressItem.street">{{ addressItem.street }}, </span>
                                        <span v-if="addressItem.street2">{{ addressItem.street2 }}, </span>
                                        <span v-if="addressItem.street3">{{ addressItem.street3 }}</span>
                                        <br>{{ addressItem.postal_code }}&nbsp;&nbsp;{{ addressItem.city }},
                                        <span v-if="addressItem.country">{{
                                                $helpers.i18n.localized(addressItem.country)
                                            }}</span>
                                        <span v-if="addressItem.type">({{
                                                $helpers.i18n.localized(addressItem.type)
                                            }})</span></p>
                                </td>
                            </template>
                            <template v-slot:item.user_data="{ item }">
                                <div v-if="item.user_data">
                                    <v-avatar
                                        v-if="item.user_data.avatar_url || item.user_data.full_name"
                                        class="mr-2"
                                        color="grey darken-1"
                                        size="2em"
                                    >
                                        <v-img v-if="item.user_data.avatar_url" :src="item.user_data.avatar_url"/>
                                        <span v-else-if="item.user_data.full_name" class="white--text">
                                            {{
                                                item.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                            }}
                                        </span>

                                    </v-avatar>
                                    <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>
                                    {{ item.user_data.full_name }}
                                </div>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs"
                                               v-on="on" :disabled="!item.user_data.is_active"
                                               icon @click.native.stop="sendInvite(item)">
                                            <v-icon
                                                small
                                            >
                                                mdi-email-alert
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.company.resend_invite }}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon @click.native.stop="showRolesModal(item)">
                                            <v-icon
                                                small
                                            >
                                                mdi-pencil
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.company.edit_contact }}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs"
                                               v-on="on" icon
                                               @click.native.stop="showRemoveEmployeeDlg(item)">
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
                                    {{ langMap.company.new_contact }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-text-field
                                                v-model="employeeForm.name"
                                                :color="themeBgColor"
                                                :label="langMap.main.name"
                                                class="col-md-4"
                                                dense
                                                hide-details
                                                name="name"
                                                required
                                                type="text"
                                            ></v-text-field>
                                            <v-text-field
                                                v-model="employeeForm.middle_name"
                                                :color="themeBgColor"
                                                :error-messages="errors.middle_name"
                                                :label="this.$store.state.lang.lang_map.main.middle_name"
                                                class="col-md-4"
                                                dense
                                                hide-details
                                                lazy-validation
                                                name="middle_name"
                                                type="text"
                                            ></v-text-field>
                                            <v-text-field
                                                v-model="employeeForm.surname"
                                                :color="themeBgColor"
                                                :error-messages="errors.surname"
                                                :label="this.$store.state.lang.lang_map.main.last_name"
                                                class="col-md-4"
                                                dense
                                                hide-details
                                                lazy-validation
                                                name="surname"
                                                type="text"
                                            ></v-text-field>
                                            <v-text-field
                                                v-model="employeeForm.email"
                                                :color="themeBgColor"
                                                :label="langMap.main.email"
                                                class="col-md-4"
                                                dense
                                                hide-details
                                                name="email"
                                                required
                                                type="text"
                                            ></v-text-field>
                                            <v-select
                                                v-model="employeeForm.role_id"
                                                :color="themeBgColor"
                                                :item-color="themeBgColor"
                                                :items="roles"
                                                :label="langMap.main.roles"
                                                class="col-md-4"
                                                dense
                                                hide-details
                                                item-value="id"
                                            >
                                                <template slot="selection" slot-scope="data">
                                                    {{
                                                        langMap.roles[data.item.name] ?
                                                            langMap.roles[data.item.name] :
                                                            data.item.name
                                                    }}
                                                </template>
                                                <template slot="item" slot-scope="data">
                                                    {{
                                                        langMap.roles[data.item.name] ?
                                                            langMap.roles[data.item.name] :
                                                            data.item.name
                                                    }}
                                                </template>
                                            </v-select>
                                            <v-select
                                                v-model="employeeForm.language_id"
                                                :color="themeBgColor"
                                                :error-messages="errors.language_id"
                                                :item-color="themeBgColor"
                                                :items="languages"
                                                :label="this.$store.state.lang.lang_map.main.language"
                                                class="col-md-4"
                                                dense
                                                item-text="name"
                                                item-value="id"
                                                lazy-validation
                                                name="language"
                                                prepend-icon="mdi-web"
                                            />
                                            <v-checkbox
                                                v-model="employeeForm.is_active"
                                                :disabled="$helpers.auth.checkPermissionByIds([36, 37])"
                                                :label="langMap.main.give_access + '?'"
                                                class="col-md-6"
                                                color="success"
                                                hide-details
                                                style="margin-top: 0!important;"
                                            ></v-checkbox>
                                        </div>
                                        <v-btn
                                            :color="themeBgColor"
                                            bottom
                                            dark
                                            fab
                                            right
                                            @click="addEmployee"
                                        >
                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                            </v-icon>
                                        </v-btn>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>
                <br v-if="$helpers.auth.checkPermissionByIds([85])">
                <v-card class="elevation-12">

                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.company.social_info
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-list
                            dense
                            subheader
                        >
                            <v-list-item-group :color="themeBgColor">
                                <v-list-item
                                    v-for="(item) in company.socials"
                                    :key="item.id"
                                >
                                    <v-list-item-icon v-if="item.type">
                                        <v-icon left v-text="item.type.icon"></v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        <v-list-item-title
                                            v-text="item.social_link"></v-list-item-title>
                                        <v-list-item-subtitle v-if="item.type"
                                                              v-text="$helpers.i18n.localized(item.type)"></v-list-item-subtitle>
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
                            <v-expansion-panel @click="resetSocial">
                                <v-expansion-panel-header>
                                    {{ langMap.company.new_social_item }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col class="pa-1" cols="md-6">
                                                <v-text-field
                                                    v-model="socialForm.social_link"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :label="langMap.main.link"
                                                    dense
                                                ></v-text-field>
                                            </v-col>
                                            <v-col class="pa-1" cols="6">
                                                <v-select
                                                    v-model="socialForm.social_type"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :items="socialTypes"
                                                    :label="langMap.main.type"
                                                    dense
                                                    item-value="id"
                                                >
                                                    <template slot="selection" slot-scope="data">
                                                        <v-icon left small v-text="data.item.icon"></v-icon>
                                                        {{ $helpers.i18n.localized(data.item) }}
                                                    </template>
                                                    <template slot="item" slot-scope="data">
                                                        <v-icon left small v-text="data.item.icon"></v-icon>
                                                        {{ $helpers.i18n.localized(data.item) }}
                                                    </template>
                                                </v-select>
                                            </v-col>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                small
                                                @click="submitNewData(company.id, socialForm, 'addSocial')"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                    mdi-plus
                                                </v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>
                <br>
                <v-card v-if="$helpers.auth.checkPermissionByIds([89])" class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.limitation_group.info
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="limitationGroupHeaders"
                            :items="company.limitation_groups"
                            :options.sync="options"
                            class="elevation-1"
                            dense
                            item-key="id"
                            @update:options="updateItemsPerPage"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon @click="showLimitationGroup(item)">
                                            <v-icon
                                                small
                                            >
                                                mdi-eye
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.main.show }}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon
                                               @click="showDeleteLimitationGroupDlg(item)">
                                            <v-icon
                                                small
                                            >
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.main.delete }}</span>
                                </v-tooltip>
                            </template>
                        </v-data-table>
                        <br>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.limitation_group.add }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content style="padding-bottom: 0">
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <v-text-field
                                                    v-model="limitationGroupForm.name"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.name"
                                                    name="product_name"
                                                    required
                                                    type="text"
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-3">
                                                <v-select
                                                    v-model="limitationGroupForm.limitation_type_id"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :items="limitationTypes"
                                                    :label="langMap.main.type"
                                                    item-value="id"
                                                    item-text="short_code"
                                                />
                                            </div>
                                            <div class="col-md-3">
                                                <v-checkbox
                                                    v-model="limitationGroupForm.auto_assign"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :label="'Auto assign'"
                                                    item-value="id"
                                                />
                                            </div>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                @click="addLimitationGroup"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                    mdi-plus
                                                </v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>

                    </v-card-text>
                </v-card>

                <br>
                <v-card class="elevation-12">
                    <v-spacer></v-spacer>
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.main.client_groups
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-12">
                                    <v-treeview
                                        :items="clientFilterGroups"
                                        activatable
                                        item-key="id"
                                        open-on-click
                                    >
                                        <template v-slot:prepend="{ item }">
                                            <v-icon v-if="item.children && item.children.length > 0">mdi-folder</v-icon>
                                            <v-icon v-else>mdi-group</v-icon>
                                        </template>
                                        <template v-slot:append="{ item }">
                                            <v-btn
                                                icon
                                                small
                                                @click="selectedClientGroup = item; editClientFilterGroupsDialog = true;"
                                            >
                                                <v-icon>mdi-pencil</v-icon>
                                            </v-btn>
                                            <v-btn
                                                icon
                                                small
                                                @click="selectedClientGroup = item; removeClientFilterGroupsDialog = true; "
                                            >
                                                <v-icon>mdi-trash-can</v-icon>
                                            </v-btn>
                                        </template>

                                    </v-treeview>

                                    <v-expansion-panels>
                                        <v-expansion-panel class="mt-5">
                                            <v-expansion-panel-header>
                                                {{ langMap.main.add_client_group }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                        mdi-plus
                                                    </v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="clientFilterGroupForm.name"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.name"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-6">
                                                            <v-text-field
                                                                v-model="clientFilterGroupForm.number"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.number"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="md-12">
                                                            <v-textarea
                                                                v-model="clientFilterGroupForm.description"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.description"
                                                                dense
                                                            ></v-textarea>
                                                        </v-col>
                                                        <v-col class="pa-1" cols="12">
                                                            <v-select
                                                                v-model="clientFilterGroupForm.parent_id"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :items="clientFilterGroupsRaw"
                                                                :label="langMap.company.parent_product_category"
                                                                item-text="name"
                                                                item-value="id"
                                                                dense
                                                            >
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeBgColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
                                                            @click="addClientFilterGroup()"
                                                        >
                                                            <v-icon :color="themeBgColor"
                                                                    :style="`color: ${themeFgColor};`">mdi-plus
                                                            </v-icon>
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
            <v-dialog v-model="rolesDialog" max-width="600px" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.company.update_info }}: {{ singleUserForm.user.name }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-select
                                v-model="singleUserForm.role_ids"
                                :color="themeBgColor"
                                :disabled="$helpers.auth.checkPermissionByIds([36, 37])"
                                :item-color="themeBgColor"
                                :items="roles"
                                :label="langMap.main.role"
                                item-value="id"
                                multiple
                            >
                                <template slot="selection" slot-scope="data">
                                    {{
                                        langMap.roles[data.item.name] ?
                                            langMap.roles[data.item.name] :
                                            data.item.name
                                    }}
                                </template>
                                <template slot="item" slot-scope="data">
                                    {{
                                        langMap.roles[data.item.name] ?
                                            langMap.roles[data.item.name] :
                                            data.item.name
                                    }}
                                </template>
                            </v-select>
                            <v-expansion-panels
                                :disabled="$helpers.auth.checkPermissionByIds([36, 37])"
                            >
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        {{ langMap.company.user_info }}
                                        <template v-slot:actions>
                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                            </v-icon>
                                        </template>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-form>
                                            <v-row>
                                                <v-text-field
                                                    v-model="singleUserForm.user.title_before_name"
                                                    :color="themeBgColor"
                                                    :error-messages="errors.title_before_name"
                                                    :label="langMap.main.title_before_name"
                                                    class="col-md-6"
                                                    dense
                                                    lazy-validation
                                                    name="title_before_name"
                                                    prepend-icon="mdi-book-account-outline"
                                                    type="text"
                                                ></v-text-field>
                                                <v-text-field
                                                    v-model="singleUserForm.user.title"
                                                    :color="themeBgColor"
                                                    :error-messages="errors.title"
                                                    :label="langMap.main.title"
                                                    class="col-md-6"
                                                    dense
                                                    lazy-validation
                                                    name="title"
                                                    prepend-icon="mdi-book-account-outline"
                                                    type="text"
                                                ></v-text-field>
                                                <v-text-field
                                                    v-model="singleUserForm.user.name"
                                                    :color="themeBgColor"
                                                    :error-messages="errors.name"
                                                    :label="langMap.main.name"
                                                    class="col-md-6"
                                                    dense
                                                    lazy-validation
                                                    name="name"
                                                    prepend-icon="mdi-book-account-outline"
                                                    required
                                                    type="text"
                                                ></v-text-field>
                                                <v-text-field
                                                    v-model="singleUserForm.user.surname"
                                                    :color="themeBgColor"
                                                    :error-messages="errors.surname"
                                                    :label="langMap.main.last_name"
                                                    class="col-md-6"
                                                    dense
                                                    lazy-validation
                                                    name="surname"
                                                    prepend-icon="mdi-book-account-outline"
                                                    type="text"
                                                ></v-text-field>
                                                <v-btn
                                                    :color="themeBgColor"
                                                    bottom
                                                    dark
                                                    fab
                                                    right
                                                    small
                                                    @click="updateUser"
                                                >
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                        mdi-plus
                                                    </v-icon>
                                                </v-btn>
                                            </v-row>
                                        </v-form>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        {{ langMap.company.additional_info }}
                                        <template v-slot:actions>
                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                            </v-icon>
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
                                                        <v-list-item-group :color="themeBgColor">
                                                            <v-list-item
                                                                v-for="(item, i) in singleUserForm.user.emails"
                                                                :key="item.id"
                                                            >
                                                                <v-list-item-icon v-if="item.type">
                                                                    <v-icon v-text="item.type.icon"></v-icon>
                                                                </v-list-item-icon>
                                                                <v-list-item-content>
                                                                    <v-list-item-title
                                                                        v-text="item.email"></v-list-item-title>
                                                                    <v-list-item-subtitle v-if="item.type"
                                                                                          v-text="$helpers.i18n.localized(item.type)"></v-list-item-subtitle>
                                                                </v-list-item-content>
                                                                <v-list-item-action>
                                                                    <v-icon
                                                                        small
                                                                        @click="editUserEmail(singleUserForm.user.id, item)"
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
                                                                v-for="(item, i) in singleUserForm.user.phones"
                                                                :key="item.id"
                                                            >
                                                                <v-list-item-icon v-if="item.type">
                                                                    <v-icon left v-text="item.type.icon"></v-icon>
                                                                </v-list-item-icon>
                                                                <v-list-item-content>
                                                                    <v-list-item-title
                                                                        v-text="item.phone"></v-list-item-title>
                                                                    <v-list-item-subtitle v-if="item.type"
                                                                                          v-text="$helpers.i18n.localized(item.type)"></v-list-item-subtitle>
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
                                                                <v-list-item-icon v-if="item.type">
                                                                    <v-icon left v-text="item.type.icon"></v-icon>
                                                                </v-list-item-icon>
                                                                <v-list-item-content>
                                                                    <v-list-item-title v-text="">
                                                                        <span
                                                                            v-if="item.street">{{
                                                                                item.street
                                                                            }}<br></span>
                                                                        <span v-if="item.street2">{{ item.street2 }}<br></span>
                                                                        <span v-if="item.street3">{{ item.street3 }}<br></span>
                                                                        {{ item.postal_code }} {{ item.city }}
                                                                        <span
                                                                            v-if="item.country">{{
                                                                                $helpers.i18n.localized(item.country)
                                                                            }}</span>
                                                                    </v-list-item-title>
                                                                    <v-list-item-subtitle v-if="item.type"
                                                                                          v-text="$helpers.i18n.localized(item.type)"></v-list-item-subtitle>
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
                                                                {{ this.$store.state.lang.lang_map.main.new_email }}
                                                                <template v-slot:actions>
                                                                    <v-icon :color="themeBgColor"
                                                                            :style="`color: ${themeFgColor};`">mdi-plus
                                                                    </v-icon>
                                                                </template>
                                                            </v-expansion-panel-header>
                                                            <v-expansion-panel-content>
                                                                <v-form>
                                                                    <div class="row">
                                                                        <v-col class="pa-1" cols="md-6">
                                                                            <v-text-field
                                                                                v-model="userEmailForm.email"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :label="langMap.main.email"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col class="pa-1" cols="6">
                                                                            <v-select
                                                                                v-model="userEmailForm.email_type"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :items="emailTypes"
                                                                                :label="langMap.main.type"
                                                                                dense
                                                                                item-value="id"
                                                                            >
                                                                                <template slot="selection"
                                                                                          slot-scope="data">
                                                                                    <v-icon left small
                                                                                            v-text="data.item.icon"></v-icon>
                                                                                    {{
                                                                                        $helpers.i18n.localized(data.item)
                                                                                    }}
                                                                                </template>
                                                                                <template slot="item" slot-scope="data">
                                                                                    <v-icon left small
                                                                                            v-text="data.item.icon"></v-icon>
                                                                                    {{
                                                                                        $helpers.i18n.localized(data.item)
                                                                                    }}
                                                                                </template>
                                                                            </v-select>
                                                                        </v-col>
                                                                        <v-btn
                                                                            :color="themeBgColor"
                                                                            bottom
                                                                            dark
                                                                            fab
                                                                            right
                                                                            small
                                                                            @click="submitNewData(singleUserForm.user.id, userEmailForm, 'addEmail')"
                                                                        >
                                                                            <v-icon :color="themeBgColor"
                                                                                    :style="`color: ${themeFgColor};`">
                                                                                mdi-plus
                                                                            </v-icon>
                                                                        </v-btn>
                                                                    </div>
                                                                </v-form>
                                                            </v-expansion-panel-content>
                                                        </v-expansion-panel>

                                                        <v-expansion-panel>
                                                            <v-expansion-panel-header>
                                                                {{ langMap.main.new_phone }}
                                                                <template v-slot:actions>
                                                                    <v-icon :color="themeBgColor"
                                                                            :style="`color: ${themeFgColor};`">mdi-plus
                                                                    </v-icon>
                                                                </template>
                                                            </v-expansion-panel-header>
                                                            <v-expansion-panel-content>
                                                                <v-form>
                                                                    <div class="row">
                                                                        <v-col class="pa-1" cols="md-6">
                                                                            <v-text-field
                                                                                v-model="userPhoneForm.phone"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :label="langMap.main.phone"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col class="pa-1" cols="6">
                                                                            <v-select
                                                                                v-model="userPhoneForm.phone_type"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :items="phoneTypes"
                                                                                :label="langMap.main.type"
                                                                                dense
                                                                                item-value="id"
                                                                            >
                                                                                <template slot="selection"
                                                                                          slot-scope="data">
                                                                                    <v-icon left small
                                                                                            v-text="data.item.icon"></v-icon>
                                                                                    {{
                                                                                        $helpers.i18n.localized(data.item)
                                                                                    }}
                                                                                </template>
                                                                                <template slot="item" slot-scope="data">
                                                                                    <v-icon left small
                                                                                            v-text="data.item.icon"></v-icon>
                                                                                    {{
                                                                                        $helpers.i18n.localized(data.item)
                                                                                    }}
                                                                                </template>
                                                                            </v-select>
                                                                        </v-col>
                                                                        <v-btn
                                                                            :color="themeBgColor"
                                                                            bottom
                                                                            dark
                                                                            fab
                                                                            right
                                                                            small
                                                                            @click="submitNewData(singleUserForm.user.id, userPhoneForm, 'addPhone')"
                                                                        >
                                                                            <v-icon :color="themeBgColor"
                                                                                    :style="`color: ${themeFgColor};`">
                                                                                mdi-plus
                                                                            </v-icon>
                                                                        </v-btn>
                                                                    </div>
                                                                </v-form>
                                                            </v-expansion-panel-content>
                                                        </v-expansion-panel>
                                                        <v-expansion-panel>
                                                            <v-expansion-panel-header>
                                                                {{ langMap.main.new_address }}
                                                                <template v-slot:actions>
                                                                    <v-icon :color="themeBgColor"
                                                                            :style="`color: ${themeFgColor};`">mdi-plus
                                                                    </v-icon>
                                                                </template>
                                                            </v-expansion-panel-header>
                                                            <v-expansion-panel-content>
                                                                <v-form>
                                                                    <div class="row">
                                                                        <v-col class="pa-1" cols="md-12">
                                                                            <v-text-field
                                                                                v-model="userAddressForm.address.street"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :label="langMap.main.address_line1"
                                                                                dense
                                                                                no-resize
                                                                                rows="3"
                                                                            ></v-text-field>
                                                                            <v-text-field
                                                                                v-model="userAddressForm.address.street2"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :label="langMap.main.address_line2"
                                                                                dense
                                                                                no-resize
                                                                                rows="3"
                                                                            ></v-text-field>
                                                                            <v-text-field
                                                                                v-model="userAddressForm.address.street3"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :label="langMap.main.address_line3"
                                                                                dense
                                                                                no-resize
                                                                                rows="3"
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col class="pa-1" cols="md-6">
                                                                            <v-text-field
                                                                                v-model="userAddressForm.address.postal_code"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :label="langMap.main.postal_code"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col class="pa-1" cols="md-6">
                                                                            <v-text-field
                                                                                v-model="userAddressForm.address.city"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :label="langMap.main.city"
                                                                                dense
                                                                            ></v-text-field>
                                                                        </v-col>
                                                                        <v-col class="pa-1" cols="md-6">
                                                                            <v-select
                                                                                v-model="userAddressForm.address.country_id"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :items="countries"
                                                                                :label="langMap.main.country"
                                                                                :rules="['Required']"
                                                                                dense
                                                                                item-value="id"
                                                                            >
                                                                                <template slot="selection"
                                                                                          slot-scope="data">
                                                                                    ({{ data.item.iso_3166_2 }})
                                                                                    {{
                                                                                        $helpers.i18n.localized(data.item)
                                                                                    }}
                                                                                </template>
                                                                                <template slot="item" slot-scope="data">
                                                                                    ({{ data.item.iso_3166_2 }})
                                                                                    {{
                                                                                        $helpers.i18n.localized(data.item)
                                                                                    }}
                                                                                </template>
                                                                            </v-select>
                                                                        </v-col>
                                                                        <v-col class="pa-1" cols="6">
                                                                            <v-select
                                                                                v-model="userAddressForm.address_type"
                                                                                :color="themeBgColor"
                                                                                :item-color="themeBgColor"
                                                                                :items="addressTypes"
                                                                                :label="langMap.main.type"
                                                                                dense
                                                                                item-value="id"
                                                                            >
                                                                                <template slot="selection"
                                                                                          slot-scope="data">
                                                                                    <v-icon left small
                                                                                            v-text="data.item.icon"></v-icon>
                                                                                    {{
                                                                                        $helpers.i18n.localized(data.item)
                                                                                    }}
                                                                                </template>
                                                                                <template slot="item" slot-scope="data">
                                                                                    <v-icon left small
                                                                                            v-text="data.item.icon"></v-icon>
                                                                                    {{
                                                                                        $helpers.i18n.localized(data.item)
                                                                                    }}
                                                                                </template>
                                                                            </v-select>
                                                                        </v-col>
                                                                        <v-btn
                                                                            :color="themeBgColor"
                                                                            bottom
                                                                            dark
                                                                            fab
                                                                            right
                                                                            small
                                                                            @click="submitNewData(singleUserForm.user.id, userAddressForm, 'addAddress')"
                                                                        >
                                                                            <v-icon :color="themeBgColor"
                                                                                    :style="`color: ${themeFgColor};`">
                                                                                mdi-plus
                                                                            </v-icon>
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
                                v-model="singleUserForm.user.is_active"
                                :color="themeBgColor"
                                :disabled="$helpers.auth.checkPermissionByIds([36, 37])"
                                :label="langMap.main.give_access"
                                hide-details
                                @change="changeIsActive(singleUserForm.user)"
                            ></v-checkbox>
                        </v-container>
                        <!--                        <small>*indicates required field</small>-->
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text @click="rolesDialog = false">{{ langMap.main.cancel }}</v-btn>
                        <v-btn :color="themeBgColor" text @click="updateRole">{{ langMap.main.save }}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updatePhoneDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.company.update_phone }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field v-model="phoneForm.phone" :color="themeBgColor"
                                                  :item-color="themeBgColor"
                                                  :label="langMap.main.phone" dense></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select v-model="phoneForm.phone_type" :color="themeBgColor"
                                              :item-color="themeBgColor" :items="phoneTypes" :label="langMap.main.type"
                                              dense item-value="id">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updatePhoneDlg=false; resetPhone()">{{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" text @click="updatePhoneDlg=false; updatePhone()">
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateSocialDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.company.update_social }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field v-model="socialForm.social_link" :color="themeBgColor"
                                                  :item-color="themeBgColor" :label="langMap.main.link"
                                                  dense></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select v-model="socialForm.social_type" :color="themeBgColor"
                                              :item-color="themeBgColor" :items="socialTypes" :label="langMap.main.type"
                                              dense item-value="id">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateSocialDlg=false; resetSocial()">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" text @click="updateSocialDlg=false; updateSocial()">
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateAddressDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.company.update_address }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-12">
                                    <v-text-field
                                        v-model="addressForm.address.street"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :label="langMap.main.address_line1"
                                        dense
                                        no-resize
                                        rows="3"
                                    ></v-text-field>
                                    <v-text-field
                                        v-model="addressForm.address.street2"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :label="langMap.main.address_line2"
                                        dense
                                        no-resize
                                        rows="3"
                                    ></v-text-field>
                                    <v-text-field
                                        v-model="addressForm.address.street3"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :label="langMap.main.address_line3"
                                        dense
                                        no-resize
                                        rows="3"
                                    ></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field
                                        v-model="addressForm.address.postal_code"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :label="langMap.main.postal_code"
                                        dense
                                    ></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field
                                        v-model="addressForm.address.city"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :label="langMap.main.city"
                                        dense
                                    ></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select
                                        v-model="addressForm.address.country_id"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :items="countries"
                                        :label="langMap.main.country"
                                        :rules="['Required']"
                                        dense
                                        item-value="id"
                                    >
                                        <template slot="selection" slot-scope="data">
                                            ({{ data.item.iso_3166_2 }}) {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            ({{ data.item.iso_3166_2 }}) {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                                <v-col class="pa-1" cols="6">
                                    <v-select
                                        v-model="addressForm.address_type"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :items="addressTypes"
                                        :label="langMap.main.type"
                                        dense
                                        item-value="id"
                                    >
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateAddressDlg=false; resetAddress()">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" text @click="updateAddressDlg=false; updateAddress()">
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateEmailDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.company.update_email }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field v-model="emailForm.email" :color="themeBgColor"
                                                  :item-color="themeBgColor"
                                                  :label="langMap.main.email" dense></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select v-if="emailForm.email_type == 1" v-model="emailForm.email_type"
                                              :color="themeBgColor"
                                              :item-color="themeBgColor"
                                              :items="emailTypes" :label="langMap.main.type" dense
                                              item-value="id" readonly>
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                    </v-select>
                                    <v-select v-else v-model="emailForm.email_type" :color="themeBgColor"
                                              :item-color="themeBgColor" :items="emailTypes" :label="langMap.main.type"
                                              dense item-value="id">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon left small v-text="data.item.icon"></v-icon>
                                            {{ $helpers.i18n.localized(data.item) }}
                                        </template>
                                    </v-select>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateEmailDlg=false; resetEmail()">{{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" text @click="updateEmailDlg=false; updateEmail()">
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="deleteProductDlg" max-width="480" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.customer.unlink_product }}?
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="deleteProductDlg = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text
                               @click="deleteProductDlg = false; deleteProduct(selectedProductId)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="deleteLimitationGroupDlg" max-width="480" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.limitation_group.delete_msg }}?
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="deleteLimitationGroupDlg = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text
                               @click="deleteLimitationGroup(selectedLimitationGroupId)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="showLimitationGroupDlg" max-width="60%" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.limitation_group.single }}
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col md="6">
                                <v-card outlined class="pb-4 pr-4">
                                    <v-subheader>{{ langMap.limitation_group.limitation_items }}</v-subheader>
                                    <perfect-scrollbar style="max-height: 30em;">
                                        <v-list dense subheader>
                                            <v-list-item-group :color="themeBgColor">
                                                <v-list-item v-for="(item, i) in groupModelItems" :key="item.id">
                                                    <v-list-item-action>
                                                        <v-checkbox v-model="selectedGroupModelItems"
                                                                    :color="themeBgColor" :value="item.id"/>
                                                    </v-list-item-action>
                                                    <v-list-item-content>
                                                        <v-list-item-title v-text="item.name"></v-list-item-title>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list-item-group>
                                        </v-list>
                                    </perfect-scrollbar>
                                </v-card>
                                <!--
                                <v-select
                                    v-model="selectedGroupModelItems"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :items="groupModelItems"
                                    :label="langMap.limitation_group.limitation_items"
                                    chips
                                    class="mx-4"
                                    item-text="name"
                                    item-value="id"
                                    multiple
                                    outlined
                                >
                                    <template #selection="{ item }">
                                        <v-chip label>{{ item.name }}</v-chip>
                                    </template>
                                </v-select>
                                -->
                            </v-col>
                            <v-col md="6">
                                <v-card outlined class="pb-4 pr-4">
                                    <v-subheader>{{ langMap.customer.customer }}</v-subheader>
                                    <perfect-scrollbar style="max-height: 30em;">
                                        <v-list dense subheader>
                                            <v-list-item-group :color="themeBgColor">
                                                <v-list-item v-for="(item, i) in company.employees" :key="item.id">
                                                    <v-list-item-action>
                                                        <v-checkbox v-model="selectedGroupEmployees"
                                                                    :color="themeBgColor" :value="item.id"/>
                                                    </v-list-item-action>
                                                    <v-list-item-content>
                                                        <v-list-item-title
                                                            v-text="item.user_data.full_name"></v-list-item-title>
                                                    </v-list-item-content>
                                                </v-list-item>
                                            </v-list-item-group>
                                        </v-list>
                                    </perfect-scrollbar>
                                </v-card>
                                <!--
                                <v-select
                                    v-model="selectedGroupEmployees"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :items="company.employees"
                                    :label="langMap.customer.customer"
                                    chips
                                    class="mx-4"
                                    item-text="user_data.full_name"
                                    item-value="id"
                                    multiple
                                    outlined
                                >
                                    <template #selection="{ item }">
                                        <v-chip label>{{ item.user_data.full_name }}</v-chip>
                                    </template>
                                </v-select>
                                -->
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="showLimitationGroupDlg = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="green darken-1" text
                               @click="updateLimitationGroup()">
                            {{ langMap.main.add }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="removeEmployeeDlg" max-width="480" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.company.delete_employee_msg }}
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeEmployeeDlg = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text
                               @click="removeEmployeeDlg = false; removeEmployee(selectedEmployee)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

        </v-row>

        <template>
            <v-dialog v-model="emailTrashed" max-width="480" persistent>
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.company.email_exist }} ? ({{ employeeForm.email }})
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="green darken-1" text @click="createOrRestoreEmployee('create')">
                            {{ langMap.main.create }}
                        </v-btn>
                        <v-btn color="blue darken-1" text @click="createOrRestoreEmployee('restore')">
                            {{ langMap.individuals.restore }}
                        </v-btn>
                        <v-btn color="grey darken-1" text @click="emailTrashed = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>

        <template>
            <v-dialog v-model="editClientFilterGroupsDialog" max-width="440" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.main.edit_client_group }}
                    </v-card-title>
                    <v-card-text>
                        <div class="col-md-12">
                            <v-text-field
                                v-model="selectedClientGroup.name"
                                :color="themeBgColor"
                                :label="langMap.main.name"
                                type="text"
                            ></v-text-field>
                        </div>
                        <div class="col-md-6">
                            <v-text-field
                                v-model="selectedClientGroup.number"
                                :color="themeBgColor"
                                :label="langMap.main.number"
                                type="text"
                            ></v-text-field>
                        </div>
                        <div class="col-md-6">
                            <v-text-field
                                v-model="selectedClientGroup.description"
                                :color="themeBgColor"
                                :label="langMap.main.description"
                                type="text"
                            ></v-text-field>
                        </div>
                        <div class="col-md-6">
                            <v-select v-model="selectedClientGroup.parent_id" :color="themeBgColor"
                                      :items="clientFilterGroupsRaw"
                                      :label="langMap.kb.parent_category"
                                      item-text="name" item-value="id"
                            />
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="editClientFilterGroupsDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text
                               @click="editClientFilterGroupsDialog = false; editClientFilterGroup(selectedClientGroup)">
                            {{ langMap.main.edit }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>

        <template>
            <v-dialog v-model="removeClientFilterGroupsDialog" max-width="440" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.main.remove_client_group }}
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeClientFilterGroupsDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteClientFilterGroup(selectedClientGroup.id)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>

    </v-container>
</template>


<script>
import EventBus from "../../components/EventBus";
import _ from 'lodash';

export default {

    data() {
        return {
            snackbar: false,
            actionColor: '',
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            tooltip: false,
            enableToEdit: false,
            loading: this.themeBgColor,
            languages: [],
            errors: [],
            options: {
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10,
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [
                    {
                        'text': 10,
                        'value': 10,
                    },
                    {
                        'text': 20,
                        'value': 20,
                    },
                    {
                        'text': 50,
                        'value': 50,
                    },
                    {
                        'text': 100,
                        'value': 100,
                    },
                    {
                        'text': this.$store.state.lang.lang_map.sidebar.all,
                        'value': 10000,
                    }
                ],
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
                product_code: '',
                category_id: null,
                files: []
            },
            limitationGroupForm: {
                name: '',
                company_id: null,
                limitation_type_id: null,
                auto_assign: 0
            },
            productHeaders: [
                {
                    text: 'ID',
                    align: 'start',
                    sortable: false,
                    value: 'product_data.id',
                },
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'product_data.full_name'},
                {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'product_data.description'},
                {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
            ],
            limitationGroupHeaders: [
                {
                    text: 'ID',
                    align: 'start',
                    sortable: false,
                    value: 'id',
                },
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name'},
                {text: `${this.$store.state.lang.lang_map.main.type}`, value: 'type.short_code'},
                {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
            ],
            deleteProductDlg: false,
            selectedProductId: null,
            selectedLimitationGroupId: null,
            deleteLimitationGroupDlg: false,
            showLimitationGroupDlg: false,
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
                            full_name: '',
                            phones: [],
                            addresses: [],
                        }
                    }
                ]
            },
            companyLogo: '',
            employeeForm: {
                name: '',
                email: '',
                language_id: null,
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
            clientFilterGroupForm: {
                name: '',
                parent_id: '',
            },
            customers: [],
            groupModelItems: [],
            selectedGroupModelItems: [],
            selectedGroupEmployees: [],
            selectedGroupModel: [],
            phoneTypes: [],
            addressTypes: [],
            socialTypes: [],
            emailTypes: [],
            limitationTypes: [],
            countries: [],
            productCategoriesFlat: [],
            productCategoriesTree: [],
            selectedProductCategoryId: null,
            updatePhoneDlg: false,
            updateAddressDlg: false,
            updateSocialDlg: false,
            updateEmailDlg: false,
            removeEmployeeDlg: false,
            selectedEmployee: null,
            internalBillingEditor: null,
            internalBillingForm: {},
            currency: {
                symbol: ''
            },
            emailTrashed: false,
            clientFilterGroups: [],
            clientFilterGroupsRaw: [],
            removeClientFilterGroupsDialog: false,
            editClientFilterGroupsDialog: false,
            selectedClientGroup: {
                id: '',
                name: '',
                number: null,
                description: '',
                parent_id: null
            }
        }
    },
    created() {
        this.debounceGetCurrencies = _.debounce(this.__getCurrencies, 1000);
    },
    mounted() {
        this.debounceGetCurrencies();
        this.getCompany();
        this.getCompanyLogo();
        this.getLimitationTypes();
        this.getLanguages();
        this.getRoles();
        this.getPhoneTypes();
        this.getAddressTypes();
        this.getSocialTypes();
        this.getEmailTypes();
        this.getCountries();
        this.getProductCategoriesTree();
        this.getProductCategoriesFlat();
        this.getFilterGroups()
        this.getFilterGroupsRaw()
        this.productCategoryForm.company_id = this.$route.params.id;
        this.employeeForm.company_id = this.$route.params.id;
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        __getCurrencies() {
            this.$store.dispatch('Currencies/getCurrencyList', {search: null});
        },
        localized(item, field = 'name') {
            let locale = this.$store.state.lang.locale.replace(/^([^_]+).*$/, '$1');
            return item[field + '_' + locale] ? item[field + '_' + locale] : item[field];
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
        getFilterGroups() {
            axios.get('/api/filter_groups/client?view_as_tree=1').then(response => {
                response = response.data
                if (response.success === true) {
                    this.clientFilterGroups = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        getFilterGroupsRaw() {
            axios.get('/api/filter_groups/client').then(response => {
                response = response.data
                if (response.success === true) {
                    this.clientFilterGroupsRaw = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
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
                    this.snackbar = true;
                }

            });
        },
        // getClients() {
        //     axios.get('/api/client').then(response => {
        //         this.loading = false
        //         response = response.data
        //         if (response.success === true) {
        //             this.customers = response.data.data
        //         } else {
        //             this.snackbarMessage = this.langMap.main.generic_error;
        //             this.actionColor = 'error'
        //             this.snackbar = true;
        //         }
        //     });
        // },
        getLimitationTypes() {
            axios.get('/api/limit_group/types').then(response => {
                this.loading = false
                response = response.data
                if (response.success === true) {
                    this.limitationTypes = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        getCompanyLogo() {
            axios.get(`/api/main_company/logo`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.companyLogo = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
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
        getProductCategoriesTree() {
            axios.get(`/api/main_company/product_categories/tree`).then(response => {
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
                        name: this.langMap.main.none,
                        full_name: this.langMap.main.none,
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
                    this.snackbarMessage = this.langMap.company.product_category_created;
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
        onFileChange(form) {
            this[form].files = null;
            this[form].files = event.target.files;
        },
        addProduct() {
            const config = {
                headers: {'content-type': 'multipart/form-data'}
            }
            let formData = new FormData();
            for (let key in this.productForm) {
                if (key !== 'files') {
                    formData.append(key, this.productForm[key]);
                }
            }
            Array.from(this.productForm.files).forEach(file => formData.append('files[]', file));
            axios.post('/api/product', formData, config).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getCompany();
                    this.snackbarMessage = this.langMap.product.product_created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        addLimitationGroup() {
            this.limitationGroupForm.company_id = this.company.id
            axios.post('/api/limit_group', this.limitationGroupForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getCompany();
                    this.snackbarMessage = this.langMap.limitation_group.created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.limitationGroupForm.name = ''
                    this.limitationGroupForm.auto_assign = 0
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        showProduct(item) {
            this.$router.push(`/product/${item.id}`)
        },
        showDeleteProductDlg(item) {
            this.selectedProductId = item.id;
            this.deleteProductDlg = true;
        },
        deleteProduct(productId) {
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
        showLimitationGroup(item) {
            this.showLimitationGroupDlg = true;
            this.groupModelItems = []
            if (item.type.model === "App\\Client") {
                this.groupModelItems = this.company.clients
            }
            if (item.type.model === "App\\Product") {
                this.groupModelItems = this.company.products.map((i) => i.product_data);
            }

            this.groupModelItems.sort((a, b) => (a.name.toLocaleUpperCase() > b.name.toLocaleUpperCase()) ? 1 : ((b.name.toLocaleUpperCase() > a.name.toLocaleUpperCase()) ? -1 : 0));
            this.company.employees.sort((a, b) => (a.user_data.full_name.toLocaleUpperCase() > b.user_data.full_name.toLocaleUpperCase()) ? 1 : ((b.user_data.full_name.toLocaleUpperCase() > a.user_data.full_name.toLocaleUpperCase()) ? -1 : 0));

            if (item.limitation_models !== null) {
                this.selectedGroupModelItems = item.limitation_models.map((i) => i.model_id);
            }
            if (item.employees !== null) {
                this.selectedGroupEmployees = item.employees.map((i) => i.company_user_id);
            }
            this.selectedLimitationGroupId = item.id
        },
        updateLimitationGroup() {
            axios.post(`/api/limit_group/client`,
                {
                    'model_ids': this.selectedGroupModelItems,
                    'limitation_group_id': this.selectedLimitationGroupId
                }).then(response => {
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
            axios.post(`/api/limit_group/employee`,
                {
                    'company_user_ids': this.selectedGroupEmployees,
                    'limitation_group_id': this.selectedLimitationGroupId
                }).then(response => {
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
                this.showLimitationGroupDlg = false
            });

        },
        showDeleteLimitationGroupDlg(item) {
            this.selectedLimitationGroupId = item.id;
            this.deleteLimitationGroupDlg = true;
        },
        deleteLimitationGroup(id) {
            axios.delete(`/api/limit_group/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getCompany()
                    this.selectedLimitationGroupId = null;
                    this.deleteLimitationGroupDlg = false;
                    this.snackbarMessage = this.langMap.limitation_group.deleted;
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
                    if (response.error.email_trashed) {
                        this.emailTrashed = true;
                    } else {
                        let error = Object.keys(response.error) ? response.error[Object.keys(response.error)[0]].shift() : this.langMap.main.generic_error;
                        this.snackbarMessage = error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                }

            });
        },
        createOrRestoreEmployee(action) {
            this.emailTrashed = false;
            this.employeeForm.action = action;
            this.addEmployee();
        },
        updateCompany() {
            this.company.phones = null;
            this.company.addresses = null;
            this.company.socials = null;
            this.company.employees = null;
            this.company.clients = null;
            this.company.teams = null;
            this.company.currency = null;

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
        cancelUpdateCompany() {
            this.getCompany();
            this.enableToEdit = false;
        },
        editInternalBilling(item) {
            if (this.internalBillingEditor === null) {

                this.internalBillingEditor = [0]
                this.internalBillingForm.id = item.id
                this.internalBillingForm.name = item.name
                this.internalBillingForm.cost = item.cost
            } else {
                this.internalBillingEditor = null
                this.internalBillingForm = {}
            }
        },
        deleteInternalBilling(id) {
            axios.delete(`/api/billing/internal/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.getCompany()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        updateInternalBilling(id) {
            axios.put(`/api/billing/internal/${id}`, this.internalBillingForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.internalBillingEditor = null
                    this.internalBillingForm = {}
                    this.getCompany()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        createInternalBilling() {
            this.internalBillingForm.entity_id = this.company.id
            this.internalBillingForm.entity_type = 'App\\Company'
            axios.post(`/api/billing/internal`, this.internalBillingForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.internalBillingEditor = null
                    this.internalBillingForm = {}
                    this.getCompany()
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
            axios.patch(`/api/user/roles`, this.singleUserForm).then(response => {
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
        showRemoveEmployeeDlg(item) {
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
        resetPhone() {
            this.phoneForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\Company',
                phone: '',
                phone_type: ''
            }
        },
        addSocial(form) {
            axios.post('/api/social', form).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getCompany()
                    this.snackbarMessage = this.langMap.company.social_created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.resetSocial();
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
                    this.resetSocial();
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
                    this.resetSocial();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        resetSocial() {
            this.socialForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\Company',
                social_link: '',
                social_type: ''
            }
        },
        addAddress(form) {
            axios.post('/api/address', form).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getCompany()
                    this.snackbarMessage = this.langMap.company.address_created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.resetAddress();
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
                    this.resetAddress();
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
                    this.resetAddress();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        resetAddress() {
            this.addressForm = {
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
            }
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
                    this.resetEmail();
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
                    this.resetEmail();
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
                    this.resetEmail();
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
            this.emailForm.entity_type = 'App\\Company';
            this.emailForm.entity_id = this.company.id;
        },
        editUserEmail(userId, item) {
            this.updateEmailDlg = true;

            this.emailForm.id = item.id;
            this.emailForm.email = item.email;
            this.emailForm.email_type = item.type ? item.type.id : 0;
            this.emailForm.entity_type = 'App\\User';
            this.emailForm.entity_id = userId;
        },
        resetEmail() {
            this.emailForm = {
                entity_id: '',
                entity_type: 'App\\Company',
                email: '',
                email_type: ''
            }
        },
        updateItemsPerPage(options) {
            localStorage.itemsPerPage = options.itemsPerPage;
        },
        addClientFilterGroup() {
            axios.post('/api/filter_groups/client', this.clientFilterGroupForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getFilterGroups()
                    this.getFilterGroupsRaw()
                    this.snackbarMessage = this.langMap.main.add_client_group_success;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.add_client_group_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
                return true
            });
        },
        editClientFilterGroup() {
            axios.patch(`/api/filter_groups/client/${this.selectedClientGroup.id}`, {
                name: this.selectedClientGroup.name,
                number: this.selectedClientGroup.number,
                description: this.selectedClientGroup.description,
                parent_id: this.selectedClientGroup.parent_id
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getFilterGroups()
                    this.getFilterGroupsRaw()
                    this.snackbarMessage = this.langMap.main.edit_client_group_success;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.selectedClientGroup = {
                        id: '',
                        name: '',
                        number: null,
                        description: '',
                        parent_id: null
                    }
                } else {
                    this.snackbarMessage = this.langMap.main.edit_client_group_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            })
        },
        deleteClientFilterGroup(id) {
            axios.delete(`/api/filter_groups/client/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getFilterGroups()
                    this.getFilterGroupsRaw()
                    this.snackbarMessage = this.langMap.main.remove_client_group_success;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.selectedClientGroup = {
                        id: '',
                        name: '',
                        number: null,
                        description: '',
                        parent_id: null
                    }
                    this.removeClientFilterGroupsDialog = false;
                } else {
                    this.snackbarMessage = this.langMap.main.remove_client_group_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
                return true
            });
        },
    },
    watch: {
        companyUpdates(value) {
            this.companyIsLoaded = true;
            // console.log(this.singleUserForm.user);
            if (this.singleUserForm.user) {
                this.singleUserForm.user = this.company.employees.find(x => x.user_id === this.singleUserForm.user.id).user_data;
            }
            this.currency = this.$store.getters['Currencies/getCurrencies'].find(obj => {
                return obj.id === this.company.currency_id;
            }) ?? ''
        }
    },
    computed: {
        companyUpdates: function () {
            return this.company
        }
    }
}
</script>
