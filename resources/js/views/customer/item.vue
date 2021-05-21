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
        <v-row>
            <v-col cols="6">
                <v-card>
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.company.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn v-if="!enableToEdit" :color="themeBgColor" icon @click="enableToEdit = true">
                            <v-icon :color="themeFgColor" dense small>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn v-if="enableToEdit" :color="themeBgColor" icon @click="cancelUpdateClient">
                            <v-icon :color="themeFgColor" dense small>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar>

                    <v-card-text v-if="!enableToEdit">
                        <v-row>
                            <v-col v-if="client.logo_url" cols="2">
                                <v-img v-if="client.logo_url" :src="client.logo_url"
                                       style="max-width: 80px; max-height: 80px"/>
                            </v-col>
                            <v-col :cols="client.logo_url ? 4 : 6">
                                <h3 class="mb-3">{{ client.client_name }}</h3>
                                <h4 class="mb-3">{{ client.number }}</h4>
                                <p v-if="client.client_description">| {{ client.client_description }}</p>

                                <div v-if="client.emails && client.emails.length > 0" class="mb-3">
                                    <hr class="lighten"/>
                                    <p v-for="(item, i) in client.emails" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)" class="mr-2"
                                                dense small v-text="item.type.icon"/>
                                        {{ item.email }}
                                    </p>
                                </div>

                                <div v-if="client.phones && client.phones.length > 0">
                                    <hr class="lighten"/>
                                    <p v-for="(item, i) in client.phones" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)" class="mr-2"
                                                dense small v-text="item.type.icon"/>
                                        {{ item.phone }}
                                    </p>
                                </div>
                            </v-col>
                            <v-col cols="6">
                                <div v-if="client.addresses && client.addresses.length > 0" class="mb-3">
                                    <p v-for="(item, i) in client.addresses" :key="item.id" class="mb-1">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)" class="mr-2 mb-2"
                                                dense small v-text="item.type.icon"/>

                                        <span v-if="item.street">{{ item.street }}</span>
                                        <span v-if="item.street2">, {{ item.street2 }}</span>
                                        <span v-if="item.street3">, {{ item.street3 }}</span>
                                        <br/>{{ item.postal_code }} {{ item.city }}
                                        <br/><span v-if="item.country">{{ $helpers.i18n.localized(item.country) }}</span>
                                    </p>
                                </div>

                                <div v-if="client.socials && client.socials.length > 0">
                                    <hr class="lighten"/>
                                    <p v-for="(item, i) in client.socials" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)" class="mr-2"
                                                dense small v-text="item.type.icon"/>
                                        {{ item.social_link }}
                                    </p>
                                </div>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <hr class="lighten"/>

                                <p class="mb-0">
                                    <v-icon v-if="client.is_active" color="success" dense left small>mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.customer.active }}
                                </p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-expand-transition>
                        <v-card v-if="enableToEdit" class="transition-fast-in-fast-out">
                            <v-card-text>
                                <v-form>
                                    <v-row>
                                        <v-col cols="2">
                                            <label>{{ langMap.company.logo }}</label>
                                            <v-img :src="logo" style="z-index: 1; max-height: 80px; max-width: 80px">
                                                <v-file-input
                                                    v-model="newLogo"
                                                    accept="image/*"
                                                    class="mt-7 ml-7"
                                                    color="white"
                                                    dense
                                                    icon
                                                    prepend-icon="mdi-camera"
                                                    style="z-index: 2; max-width: 1em;"
                                                />
                                            </v-img>
                                        </v-col>
                                        <v-col cols="10">
                                            <v-row>
                                                <v-col cols="6">
                                                    <v-text-field
                                                        v-model="client.client_name"
                                                        :color="themeBgColor"
                                                        :label="langMap.company.name"
                                                        dense
                                                        prepend-icon="mdi-book-account-outline"
                                                        required
                                                        type="text"
                                                    />
                                                </v-col>
                                                <v-col cols="3">
                                                    <v-text-field
                                                        v-model="client.short_name"
                                                        :color="themeBgColor"
                                                        :label="langMap.company.short_name"
                                                        dense
                                                        prepend-icon="mdi-book-account-outline"
                                                        type="text"
                                                    />
                                                </v-col>
                                                <v-col cols="3">
                                                    <v-text-field
                                                        v-model="client.number"
                                                        :color="themeBgColor"
                                                        :label="langMap.company.company_number"
                                                        dense
                                                        prepend-icon="mdi-numeric"
                                                        type="text"
                                                        :rules="[false, 0, ]"
                                                    />
                                                </v-col>
                                                <v-col cols="12">
                                                    <v-textarea
                                                        v-model="client.client_description"
                                                        :color="themeBgColor"
                                                        :placeholder="langMap.company.description"
                                                        dense
                                                        prepend-icon="mdi-book-account-outline"
                                                        rows="3"
                                                        type="text"
                                                    />
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                    </v-row>

                                    <v-spacer>&nbsp;</v-spacer>
                                    <hr class="lighten"/>
                                    <v-spacer>&nbsp;</v-spacer>

                                    <h3>{{ langMap.company.additional_info }}</h3>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-list-item v-for="(item, i) in client.emails" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon dense small v-text="item.type.icon"/>
                                                </v-list-item-icon>
                                                <v-list-item-content class="mr-2">
                                                    <v-list-item-title v-text="item.email"></v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                                          v-text="$helpers.i18n.localized(item.type)"/>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon small @click="editEmail(item)">mdi-pencil</v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-if="item.email_type === 1">
                                                    <v-icon :title="langMap.profile.login_email" small>mdi-lock</v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action v-else>
                                                    <v-icon small @click="deleteEmail(item.id)">mdi-delete</v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-col>
                                        <v-col cols="6">
                                            <v-list-item v-for="(item, i) in client.phones" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon dense small v-text="item.type.icon"/>
                                                </v-list-item-icon>
                                                <v-list-item-content class="mr-2">
                                                    <v-list-item-title v-text="item.phone"></v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                                          v-text="$helpers.i18n.localized(item.type)"/>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon small @click="editPhone(item)">mdi-pencil</v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                    <v-icon small @click="deletePhone(item.id)">mdi-delete</v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-expansion-panels v-model="emailForm.opened" accordion>
                                                <v-expansion-panel>
                                                    <v-expansion-panel-header>
                                                        {{ langMap.main.new_email }}
                                                        <template v-slot:actions>
                                                            <v-icon :color="themeBgColor"
                                                                    :style="`color: ${themeFgColor};`">mdi-plus
                                                            </v-icon>
                                                        </template>
                                                    </v-expansion-panel-header>
                                                    <v-expansion-panel-content>
                                                        <v-form>
                                                            <v-row>
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        v-model="emailForm.email"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.email"
                                                                        dense
                                                                    />
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
                                                                            <v-icon left small
                                                                                    v-text="data.item.icon"></v-icon>
                                                                            {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            <v-icon left small
                                                                                    v-text="data.item.icon"></v-icon>
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
                                                                    @click="addEmail"
                                                                >
                                                                    <v-icon :color="themeBgColor"
                                                                            :style="`color: ${themeFgColor};`">mdi-plus
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-row>
                                                        </v-form>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                            </v-expansion-panels>
                                        </v-col>
                                        <v-col cols="6">
                                            <v-expansion-panels v-model="phoneForm.opened" accordion>
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
                                                            <v-row>
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        v-model="phoneForm.phone"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.phone"
                                                                        dense
                                                                    />
                                                                    <v-select
                                                                        v-model="phoneForm.phone_type"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :items="phoneTypes"
                                                                        :label="langMap.main.type"
                                                                        dense
                                                                        item-value="id"
                                                                    >
                                                                        <template slot="selection" slot-scope="data">
                                                                            <v-icon left small
                                                                                    v-text="data.item.icon"></v-icon>
                                                                            {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            <v-icon left small
                                                                                    v-text="data.item.icon"></v-icon>
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
                                                                    @click="addPhone"
                                                                >
                                                                    <v-icon :color="themeBgColor"
                                                                            :style="`color: ${themeFgColor};`">mdi-plus
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-row>
                                                        </v-form>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                            </v-expansion-panels>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-list-item v-for="(item, i) in client.addresses" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon dense small v-text="item.type.icon"/>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="">
                                                        <span v-if="item.street">{{ item.street }}</span>
                                                        <span v-if="item.street2">, {{ item.street2 }}</span>
                                                        <span v-if="item.street3">, {{ item.street3 }}</span>
                                                        <br/>{{ item.postal_code }}&nbsp;&nbsp;{{ item.city }}
                                                        <br/><span v-if="item.country">{{
                                                            $helpers.i18n.localized(item.country)
                                                        }}</span>
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type"
                                                                          v-text="$helpers.i18n.localized(item.type)"/>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon small @click="editAddress(item)">mdi-pencil</v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                    <v-icon small @click="deleteAddress(item.id)">mdi-delete</v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-col>
                                        <v-col cols="6">
                                            <v-list-item v-for="(item, i) in client.socials" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon dense small v-text="item.type.icon"/>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.social_link"/>
                                                    <v-list-item-subtitle v-if="item.type"
                                                                          v-text="$helpers.i18n.localized(item.type)"/>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon small @click="editAddress(item)">mdi-pencil</v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                    <v-icon small @click="deleteAddress(item.id)">mdi-delete</v-icon>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-expansion-panels v-model="addressForm.opened" accordion>
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
                                                            <v-row>
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        v-model="addressForm.address.street"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.address_line1"
                                                                        dense
                                                                    />
                                                                    <v-text-field
                                                                        v-model="addressForm.address.street2"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.address_line2"
                                                                        dense
                                                                    />
                                                                    <v-text-field
                                                                        v-model="addressForm.address.street3"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.address_line3"
                                                                        dense

                                                                    />
                                                                    <v-text-field
                                                                        v-model="addressForm.address.postal_code"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.postal_code"
                                                                        dense
                                                                    />
                                                                    <v-text-field
                                                                        v-model="addressForm.address.city"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.city"
                                                                        dense
                                                                    />
                                                                    <v-select
                                                                        v-model="addressForm.address.country_id"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :items="countries"
                                                                        :label="langMap.main.country"
                                                                        dense
                                                                        item-value="id"
                                                                    >
                                                                        <template slot="selection" slot-scope="data">
                                                                            ({{ data.item.iso_3166_2 }})
                                                                            {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            ({{ data.item.iso_3166_2 }})
                                                                            {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                    </v-select>
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
                                                                            <v-icon left small
                                                                                    v-text="data.item.icon"></v-icon>
                                                                            {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            <v-icon left small
                                                                                    v-text="data.item.icon"></v-icon>
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
                                                                    @click="addAddress"
                                                                >
                                                                    <v-icon :color="themeBgColor"
                                                                            :style="`color: ${themeFgColor};`">mdi-plus
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-row>
                                                        </v-form>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                            </v-expansion-panels>
                                        </v-col>
                                        <v-col cols="6">
                                            <v-expansion-panels v-model="socialForm.opened" accordion>
                                                <v-expansion-panel>
                                                    <v-expansion-panel-header>
                                                        {{ langMap.company.new_social_item }}
                                                        <template v-slot:actions>
                                                            <v-icon :color="themeBgColor"
                                                                    :style="`color: ${themeFgColor};`">mdi-plus
                                                            </v-icon>
                                                        </template>
                                                    </v-expansion-panel-header>
                                                    <v-expansion-panel-content>
                                                        <v-form>
                                                            <v-row>
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        v-model="socialForm.social_link"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.link"
                                                                        dense
                                                                    />
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
                                                                            <v-icon left small
                                                                                    v-text="data.item.icon"></v-icon>
                                                                            {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            <v-icon left small
                                                                                    v-text="data.item.icon"></v-icon>
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
                                                                    @click="addSocial"
                                                                >
                                                                    <v-icon :color="themeBgColor"
                                                                            :style="`color: ${themeFgColor};`">mdi-plus
                                                                    </v-icon>
                                                                </v-btn>
                                                            </v-row>
                                                        </v-form>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                            </v-expansion-panels>
                                        </v-col>
                                    </v-row>

                                    <v-spacer>&nbsp;</v-spacer>
                                    <hr class="lighten"/>
                                    <v-spacer>&nbsp;</v-spacer>

                                    <v-row>
                                        <v-col cols="6">
                                            <v-checkbox
                                                v-model="client.is_active"
                                                :color="themeBgColor"
                                                :label="langMap.customer.active"
                                                dense
                                                hide-details
                                                @change="changeIsActiveClient(client)"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    color="grey darken"
                                    text
                                    @click="cancelUpdateClient"
                                >
                                    {{ langMap.main.cancel }}
                                </v-btn>
                                <v-btn
                                    :color="themeBgColor"
                                    text
                                    @click="updateClient"
                                >
                                    {{ langMap.main.save }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-expand-transition>
                </v-card>

                <v-spacer>
                    &nbsp;
                </v-spacer>

                <v-card>
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
                            :items="client.products"
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
                                            <v-icon small>mdi-eye</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.customer.show_product }}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon @click="showDeleteProductDlg(item)">
                                            <v-icon small>mdi-link-off</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.product.unlink_product }}</span>
                                </v-tooltip>
                            </template>
                        </v-data-table>

                        <v-spacer>&nbsp;</v-spacer>

                        <v-expansion-panels>
                            <v-expansion-panel @click="resetProduct">
                                <v-expansion-panel-header>
                                    {{ langMap.product.add_new }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="md-12">
                                                <v-autocomplete
                                                    v-model="supplierForm.product_id"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :items="products"
                                                    :label="langMap.main.products"
                                                    item-text="name"
                                                    item-value="id"
                                                />
                                            </v-col>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                @click="addProductClient"
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
            </v-col>
            <v-col cols="6">
                <v-card>
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
                        <v-container fluid>
                            <v-row dense>
                                <v-col
                                    v-for="clientEmployee in client.employees"
                                    :key="clientEmployee.id"
                                    :cols="clientEmployee.flex"
                                    class="ma-2"
                                    color="lightgrey"

                                >
                                    <v-card
                                        height="100"
                                        max-width="200"
                                        min-width="200"
                                        @click="showUser(clientEmployee)"
                                    >
                                        <v-card-text style="padding: 5px 10px;">
                                            <v-tooltip :color="themeBgColor"
                                                       top
                                            >
                                                <template v-slot:activator="{ on, attrs }">
                                                    <span v-on="on">
                                                        <v-avatar
                                                            v-if="clientEmployee.employee.user_data.avatar_url || clientEmployee.employee.user_data.full_name"
                                                            class="mr-2"
                                                            color="grey darken-1"
                                                            size="2em"
                                                        >
                                                            <v-img v-if="clientEmployee.employee.user_data.avatar_url"
                                                                   :src="clientEmployee.employee.user_data.avatar_url"/>
                                                            <span
                                                                v-else-if="clientEmployee.employee.user_data.full_name"
                                                                class="white--text">
                                                                {{
                                                                    clientEmployee.employee.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                                                }}
                                                            </span>
                                                        </v-avatar>
                                                        <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>
                                                        {{ clientEmployee.employee.user_data.full_name }}
                                                        <p class="caption mt-2"
                                                           style="
                                                            color: darkgrey;
                                                            margin: 0;
                                                            width: 180px;
                                                            text-overflow: ellipsis;
                                                            overflow: hidden;
                                                            white-space: nowrap;">{{
                                                                clientEmployee.description ? clientEmployee.description : '&nbsp;'
                                                            }}</p>
                                                    </span>
                                                </template>
                                                <span>
                                                    {{ langMap.company.user }}:
                                                    {{ clientEmployee.employee.user_data.full_name }}
                                                    <p v-if="clientEmployee.description" class="caption">
                                                        {{ clientEmployee.description }}
                                                    </p>
                                                    <br v-else/>
                                                    {{ langMap.main.roles }}:
                                                    {{ clientEmployee.employee.role_names }}

                                                </span>
                                            </v-tooltip>
                                            <v-tooltip top>
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-btn
                                                        v-bind="attrs"
                                                        v-on="on"
                                                        :disabled="!clientEmployee.employee.user_data.is_active" icon
                                                        @click.native.stop="resetPassword(clientEmployee.employee)">
                                                        <v-icon small>mdi-email-alert</v-icon>
                                                    </v-btn>
                                                </template>
                                                <span>{{ langMap.company.resend_password }}</span>
                                            </v-tooltip>
                                            <v-tooltip top>
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-btn
                                                        v-bind="attrs"
                                                        v-on="on"
                                                        icon
                                                        @click.native.stop="removeEmployeeProcess(clientEmployee.employee)">
                                                        <v-icon small>mdi-delete</v-icon>
                                                    </v-btn>
                                                </template>
                                                <span>{{ langMap.company.delete_contact }}</span>
                                            </v-tooltip>
                                            <v-tooltip top>
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-btn
                                                        v-bind="attrs"
                                                        v-on="on"
                                                        icon
                                                        @click.native.stop="showContactInfo(clientEmployee)">
                                                        <v-icon small>mdi-account-switch-outline</v-icon>
                                                    </v-btn>
                                                </template>
                                                <span>{{ langMap.individuals.contact_info }}</span>
                                            </v-tooltip>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-container>

                        <template>
                            <v-dialog v-model="removeEmployeeDialog" max-width="480" persistent>
                                <v-card>
                                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                                                  class="mb-5">
                                        {{ langMap.company.delete_employee_msg }}
                                    </v-card-title>
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
                        </template>
                        <template>
                            <v-dialog v-model="unlinkEmployeeDialog" max-width="520" persistent>
                                <v-card>
                                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                                                  class="mb-5">
                                        {{ langMap.individuals.unlink }}
                                    </v-card-title>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="grey darken-1" text @click="unlinkEmployeeDialog = false">
                                            {{ langMap.main.cancel }}
                                        </v-btn>
                                        <v-btn color="red darken-1" text @click="unlinkEmployee(selectedEmployeeId)">
                                            {{ langMap.main.delete }}
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </template>
                        <v-dialog v-if="contactInfoForm !== null" v-model="contactInfoModal" max-width="600">
                            <v-card>
                                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                                              class="mb-5">
                                    {{ langMap.individuals.contact_info }}
                                </v-card-title>
                                <v-card-text>
                                    <v-row>
                                            <span
                                                class="col-md-6 pa-4 text-left"
                                            >

                                                <v-icon x-large>
                                                    mdi-factory
                                                </v-icon>
                                                <br>
                                                <strong>
                                                    {{ client.client_name }}
                                                </strong>
                                                <br>
                                                <br>
                                                <br>
                                                <br>
                                                <span>
                                                    {{ langMap.main.email }}
                                                </span>
                                                <br>
                                                <strong v-for="email in client.emails">
                                                    <span>
                                                        {{ email.email }}
                                                        <v-icon
                                                            v-if="contactInfoEditBtn == true && email.email_type !== 1"
                                                            small
                                                            @click="deleteEmail(email.id)"
                                                        >
                                                            mdi-backspace-outline
                                                        </v-icon>
                                                    </span>
                                                </strong>
                                                <v-divider>&nbsp;</v-divider>
                                                <span>
                                                    {{ langMap.main.phone }}
                                                </span>
                                                <br>
                                                <strong v-for="phone in client.phones">
                                                    <span>
                                                        {{ phone.phone }}
                                                        <v-icon
                                                            v-if="contactInfoEditBtn == true"
                                                            small
                                                            @click="deletePhone(phone.id)"
                                                        >
                                                            mdi-backspace-outline
                                                        </v-icon>
                                                    </span>
                                                </strong>
                                            </span>
                                        <v-spacer></v-spacer>
                                        <span
                                            class="col-md-6 pa-4  text-right"
                                        >
                                            <v-icon x-large>
                                                mdi-card-account-details-outline
                                            </v-icon>
                                                <br/>
                                            <span>
                                                <br>
                                                <v-text-field
                                                    v-model="contactInfoForm.description"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.description"
                                                    :readonly="contactInfoEditBtn === false"
                                                    dense
                                                    reverse
                                                    size="9"
                                                    style="text-align: right;"
                                                >
                                                </v-text-field>
                                            </span>

                                                <br>
                                                <span>
                                                    {{ langMap.main.email }}
                                                </span>
                                                <br>
                                                <strong v-for="email in contactInfoForm.employee.user_data.emails">
                                                    <span>
                                                        {{ email.email }}
                                                        <v-icon
                                                            v-if="contactInfoEditBtn == true && email.email_type !== 1"
                                                            small
                                                            @click="deleteEmail(email.id)"
                                                        >
                                                            mdi-backspace-outline
                                                        </v-icon>
                                                    </span>
                                                </strong>
                                                <v-spacer>&nbsp;</v-spacer>
                                                <span>
                                                    {{ langMap.main.phone }}
                                                </span>
                                                <br/>
                                                <strong v-for="phone in contactInfoForm.employee.user_data.phones">
                                                    <span>
                                                        {{ phone.phone }}
                                                        <v-icon
                                                            v-if="contactInfoEditBtn == true"
                                                            small
                                                            @click="deletePhone(phone.id)"
                                                        >
                                                            mdi-backspace-outline
                                                        </v-icon>
                                                    </span>
                                                </strong>
                                            </span>
                                    </v-row>
                                </v-card-text>
                                <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        color="grey darken-1" text
                                        @click="contactInfoEditBtn = false; contactInfoModal = false"
                                    >
                                        {{ langMap.main.cancel }}
                                    </v-btn>
                                    <v-btn
                                        color="red darken-1" text
                                        @click="unlinkEmployeeProcess(contactInfoForm)"
                                    >
                                        <v-icon small>
                                            mdi-delete
                                        </v-icon>
                                        {{ langMap.main.delete }}
                                    </v-btn>

                                    <v-btn color="green darken-1" text @click="editContactInfo">
                                        <v-icon small>{{ contactInfoEditBtn === false ? 'mdi-pencil' : '' }}</v-icon>

                                        {{
                                            contactInfoEditBtn === true ? langMap.main.save : langMap.individuals.update_link
                                        }}
                                    </v-btn>
                                    <v-spacer></v-spacer>
                                </v-card-actions>
                            </v-card>
                        </v-dialog>
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
                                            <div class="col-md-4">
                                                <v-text-field
                                                    v-model="employeeForm.name"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.name"
                                                    name="name"
                                                    required
                                                    type="text"
                                                />
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    v-model="employeeForm.middle_name"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.middle_name"
                                                    name="name"
                                                    required
                                                    type="text"
                                                />
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    v-model="employeeForm.surname"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.last_name"
                                                    name="name"
                                                    required
                                                    type="text"
                                                />
                                            </div>
                                            <div class="col-md-6">
                                                <v-autocomplete
                                                    v-model="employeeForm.language_id"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :items="languages"
                                                    :label="this.$store.state.lang.lang_map.main.language"
                                                    item-text="name"
                                                    item-value="id"
                                                    name="language"
                                                    prepend-icon="mdi-web"
                                                />
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    v-model="employeeForm.email"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.email"
                                                    name="email"
                                                    prepend-icon="mdi-mail"
                                                    required
                                                    type="text"
                                                />
                                                <v-checkbox
                                                    v-model="employeeForm.is_active"
                                                    :disabled="$helpers.auth.checkPermissionByIds(['36,37'])"
                                                    :label="langMap.main.give_access"
                                                    color="success"
                                                    hide-details
                                                />
                                            </div>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                @click="addEmployee"
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
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ this.$store.state.lang.lang_map.individuals.new_employee }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col class="pa-1" cols="md-12">
                                                <v-autocomplete
                                                    v-model="employeeForm.company_user_id"
                                                    :color="themeBgColor"
                                                    :error-messages="employeeForm.id"
                                                    :items="employees"
                                                    :placeholder="this.$store.state.lang.lang_map.main.search"
                                                    hide-no-data
                                                    hide-selected
                                                    item-text="user_data.full_name"
                                                    item-value="id"
                                                />
                                            </v-col>
                                            <v-text-field
                                                v-model="employeeForm.description"
                                                :color="themeBgColor"
                                                :label="this.$store.state.lang.lang_map.main.description"
                                                class="pa-1"
                                                dense
                                                type="text"
                                            />
                                        </div>
                                        <v-btn
                                            :color="themeBgColor"
                                            bottom
                                            dark
                                            fab
                                            right
                                            small
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
                <br>
                <v-card>
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
                                <v-list-item v-for="(item, i) in client.billing" :key="item.id">
                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.name"></v-list-item-title>
                                        <v-list-item-subtitle v-text="item.cost"></v-list-item-subtitle>
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
                                                    <v-col cols="12">
                                                        <v-text-field
                                                            v-model="internalBillingForm.name"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :label="langMap.main.name"
                                                            dense
                                                        />
                                                    </v-col>
                                                    <v-col cols="12">
                                                        <v-text-field
                                                            v-model="internalBillingForm.cost"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :label="langMap.main.cost"
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
            </v-col>
        </v-row>

        <v-row justify="center">
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

                        <v-btn color="red" text @click="updatePhoneDlg=false; resetPhone()">{{
                                langMap.main.cancel
                            }}
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

                        <v-btn color="red" text @click="updateSocialDlg=false; resetSocial()">{{
                                langMap.main.cancel
                            }}
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
                            <v-row>
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
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateAddressDlg=false; resetAddress()">{{
                                langMap.main.cancel
                            }}
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
                                    <v-select v-model="emailForm.email_type" :color="themeBgColor"
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

                        <v-btn color="red" text @click="updateEmailDlg=false; resetEmail()">{{
                                langMap.main.cancel
                            }}
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
                        {{ langMap.product.unlink_product }}?
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

        </v-row>

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
            headers: [
                {text: `${this.$store.state.lang.lang_map.company.user}`, value: 'user_data'},
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
            languages: [],
            options: {
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10,
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            enableToEdit: false,
            rolesDialog: false,
            singleUserForm: {
                user: '',
                role_ids: [],
                company_user_id: 0
            },
            clientIsLoaded: false,
            employees: [],
            client: {
                client_name: '',
                short_name: '',
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
                ],
                logo_url: '',
                is_active: false
            },
            employeeForm: {
                company_user_id: 0,
                description: '',
                name: '',
                email: '',
                client_id: 0,
                is_active: false
            },
            selectedEmployeeId: null,
            removeEmployeeDialog: false,
            unlinkEmployeeDialog: false,
            contactInfoModal: false,
            contactInfoEditBtn: false,
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
                phone_type: '',
                opened: null
            },
            addressForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\Client',
                address: {
                    street: '',
                    street2: '',
                    street3: '',
                    postal_code: '',
                    city: '',
                    country_id: ''
                },
                address_type: '',
                opened: null
            },
            socialForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\Client',
                social_link: '',
                social_type: '',
                opened: null,
            },
            emailForm: {
                entity_id: '',
                entity_type: 'App\\Client',
                email: '',
                email_type: '',
                opened: null,
            },
            supplierForm: {
                client_id: null,
                product_id: null
            },
            productsSearch: '',
            products: [],
            phoneTypes: [],
            addressTypes: [],
            socialTypes: [],
            emailTypes: [],
            countries: [],
            updatePhoneDlg: false,
            updateAddressDlg: false,
            updateSocialDlg: false,
            updateEmailDlg: false,
            contactInfoForm: null,
            deleteProductDlg: false,
            selectedProductId: null,
            logo: '',
            newLogo: '',
            internalBillingEditor: null,
            internalBillingForm: {}
        }
    },
    mounted() {
        this.getClient();
        this.getRoles();
        this.getLanguages();
        this.getPhoneTypes();
        this.getAddressTypes();
        this.getSocialTypes();
        this.getEmailTypes();
        this.getCountries();
        this.getProducts();
        this.getEmployees();
        this.employeeForm.client_id = parseInt(this.$route.params.id);
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        getClient() {
            axios.get(`/api/client/${this.$route.params.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.client = response.data
                    this.client.client_name = response.data.name
                    this.client.client_description = response.data.description
                    this.$store.state.pageName = this.client.client_name
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
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
        showContactInfo(item) {
            this.contactInfoForm = item
            this.contactInfoModal = true

        },
        getEmployees() {
            axios.get('/api/employee?sort_by=user_data.name&sort_val=false').then(
                response => {
                    this.loading = false
                    response = response.data
                    if (response.success === true) {
                        this.employees = response.data.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.snackbar = true;
                    }
                });
        },
        addEmployee(update = false) {
            axios.post(`/api/client/employee`, this.employeeForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient()
                    this.snackbarMessage = update ? this.langMap.company.employee_updated : this.langMap.company.employee_added;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        editContactInfo() {
            if (this.contactInfoEditBtn === true) {
                this.employeeForm.client_id = this.contactInfoForm.client_id
                this.employeeForm.company_user_id = this.contactInfoForm.company_user_id
                this.employeeForm.description = this.contactInfoForm.description
                this.addEmployee(true);
                this.contactInfoModal = false
                this.contactInfoEditBtn = false
                this.getClient()

            } else {
                this.contactInfoEditBtn = true
            }
        },
        updateClient() {
            this.snackbar = false;

            if (this.newLogo) {
                let formData = new FormData();
                formData.append('logo', this.newLogo);
                axios.post(
                    `/api/client/${this.$route.params.id}/logo`,
                    formData, {
                        headers: {'content-type': 'multipart/form-data'}
                    }
                ).then(response => {
                    this.newLogo = null;

                    response = response.data;
                    if (response.success === true) {
                        this.logo = response.data.logo_url;
                        this.client.logo_url = response.data.logo_url;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;

                        return;
                    }
                });
            }
            axios.patch(`/api/client/${this.$route.params.id}`, this.client).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient()
                    this.snackbarMessage = this.langMap.company.customer_updated;
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
        cancelUpdateClient() {
            this.getClient();
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
                    this.getClient()
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
                    this.getClient()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        createInternalBilling() {
            this.internalBillingForm.entity_id = this.client.id
            this.internalBillingForm.entity_type = 'App\\Client'
            axios.post(`/api/billing/internal`, this.internalBillingForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.internalBillingEditor = null
                    this.internalBillingForm = {}
                    this.getClient()
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
                    this.snackbarMessage = this.langMap.company.user_updated;
                    this.actionColor = 'success'
                    this.snackbar = true
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        addPhone() {
            this.phoneForm.entity_id = this.client.id;

            axios.post('/api/phone', this.phoneForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetPhone();
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
            this.phoneForm.entity_id = this.client.id;

            axios.patch(`/api/phone/${this.phoneForm.id}`, this.phoneForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetPhone();
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
                    this.contactInfoModal = false;
                    this.resetPhone();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        addSocial() {
            this.socialForm.entity_id = this.client.id;

            axios.post('/api/social', this.socialForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetSocial();
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
            this.socialForm.entity_id = this.client.id;

            axios.patch(`/api/social/${this.socialForm.id}`, this.socialForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetSocial();
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
                    this.resetSocial();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        addAddress() {
            this.addressForm.entity_id = this.client.id;

            axios.post('/api/address', this.addressForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetAddress();
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
            this.addressForm.entity_id = this.client.id;

            axios.patch(`/api/address/${this.addressForm.id}`, this.addressForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetAddress();
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
                    this.resetAddress();
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
                    this.snackbarMessage = item.is_active === true ? this.langMap.company.company_activated : this.langMap.company.company_deactivated;
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
            console.log(item);
            this.selectedEmployeeId = item.id
            this.removeEmployeeDialog = true
        },
        removeEmployee(id) {
            axios.delete(`/api/company/employee/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient()
                    this.rolesDialog = false
                    this.snackbarMessage = this.langMap.company.employee_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.removeEmployeeDialog = false
                    this.contactInfoModal = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        unlinkEmployeeProcess(item) {
            console.log(item);
            this.selectedEmployeeId = item.id
            this.unlinkEmployeeDialog = true
        },
        unlinkEmployee(id) {
            axios.delete(`/api/client/employee/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient()
                    this.rolesDialog = false
                    this.snackbarMessage = this.langMap.company.employee_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.unlinkEmployeeDialog = false
                    this.contactInfoModal = false
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
                    this.snackbarMessage = item.is_active ? this.langMap.company.employee_activated : this.langMap.company.employee_deactivated;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        resetPassword(item) {
            axios.post('/api/reset_password', {
                email: item.user_data.email
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.company.reset_password_email_sent;
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
        addEmail() {
            this.emailForm.entity_id = this.client.id;

            axios.post('/api/email', this.emailForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient()
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
            this.emailForm.entity_id = this.client.id;

            axios.patch(`/api/email/${this.emailForm.id}`, this.emailForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.emailForm.id = '';
                    this.getClient();
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
                    this.getClient()
                    this.emailForm.email = ''
                    this.snackbarMessage = this.langMap.company.email_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.contactInfoModal = false;
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
            this.emailForm.entity_id = this.client.id;
        },
        showDeleteProductDlg(item) {
            this.selectedProductId = item.id;
            this.deleteProductDlg = true;
        },
        getProducts() {
            axios.get(`/api/product?
                    search=${this.productsSearch}&
                    sort_by=name&
                    sort_val=false&
                    `).then(response => {
                response = response.data
                if (response.success === true) {
                    this.products = response.data.data
                    this.totalProducts = response.data.total
                    this.lastPage = response.data.last_page
                    this.loading = false
                } else {
                    console.log('error')
                }

            });
        },
        addProductClient() {
            console.log(this.client.id);
            this.supplierForm.client_id = this.client.id
            axios.post(`/api/product/client`, this.supplierForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient();
                    this.resetProduct();
                } else {
                    console.log('error')
                }

            });
        },
        deleteProduct(productId) {
            axios.delete(`/api/product/client/${productId}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClient()
                    this.selectedProductId = null;
                    this.snackbarMessage = this.langMap.customer.product_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.resetProduct();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        resetEmail() {
            this.emailForm = {
                entity_id: '',
                entity_type: 'App\\Client',
                email: '',
                email_type: '',
                opened: null,
            };
        },
        resetPhone() {
            this.phoneForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\Client',
                phone: '',
                phone_type: '',
                opened: null
            };
        },
        resetAddress() {
            this.addressForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\Client',
                address: {
                    street: '',
                    street2: '',
                    street3: '',
                    postal_code: '',
                    city: '',
                    country_id: ''
                },
                address_type: '',
                opened: null
            };
        },
        resetProduct() {
            this.supplierForm = {
                client_id: null,
                product_id: null
            }
        },
        resetSocial() {
            this.socialForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\Client',
                social_link: '',
                social_type: '',
                opened: null
            }
        },
        updateItemsPerPage(options) {
            localStorage.itemsPerPage = options.itemsPerPage;
        }
    },
    watch: {
        clientUpdates(value) {
            this.clientIsLoaded = true;
            // console.log(this.singleUserForm.user);
            if (this.singleUserForm.user) {
                this.singleUserForm.user = this.client.employees.find(x => x.employee.user_id === this.singleUserForm.user.id).employee.user_data;
            }
        },
        newLogo(val) {
            if (this.newLogo !== null) {
                this.logo = URL.createObjectURL(this.newLogo)
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
