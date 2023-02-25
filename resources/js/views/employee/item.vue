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

            <v-col cols="6">
                <v-card>
                    <v-toolbar
                        dark
                        dense
                        flat
                        :color="themeBgColor"
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.individuals.info
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn :color="themeBgColor" icon @click="enableToEdit = true" v-if="!enableToEdit">
                            <v-icon :color="themeFgColor" small dense>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn :color="themeBgColor" icon @click="cancelUpdateUser" v-if="enableToEdit">
                            <v-icon :color="themeFgColor" small dense>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar>

                    <v-card-text v-if="!enableToEdit">
                        <v-row>
                            <v-col cols="2">
                                <v-avatar
                                    color="grey darken-1"
                                    v-if="userData.avatar_url || userData.full_name"
                                    size="80px"
                                >
                                    <v-img v-if="userData.avatar_url" :src="userData.avatar_url" size="80px"/>
                                    <div v-else-if="userData.full_name" class="white--text" size="80px">
                                        {{
                                            userData.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                        }}
                                    </div>
                                </v-avatar>
                                <v-icon v-else size="80px">mdi-account-circle</v-icon>
                            </v-col>
                            <v-col cols="5">
                                <p v-if="userData.number" class="mb-3 font-weight-bold">{{ userData.number }}</p>

                                <h3 class="mb-3">{{ userData .title }} {{ userData.title_before_name}} {{ userData.full_name }}</h3>

                                <div v-if="userData.emails && userData.emails.length > 0" class="mb-3">
                                    <hr class="lighten"/>
                                    <p v-for="(item, i) in userData.emails" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                v-text="item.type.icon" dense small class="mr-2"/>
                                        {{ item.email }}
                                    </p>
                                </div>

                                <div v-if="userData.phones && userData.phones.length > 0">
                                    <hr class="lighten"/>
                                    <p v-for="(item, i) in userData.phones" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                v-text="item.type.icon" dense small class="mr-2"/>
                                        {{ item.phone }}
                                    </p>
                                </div>
                            </v-col>
                            <v-col cols="5">
                                <div v-if="userData.addresses && userData.addresses.length > 0" class="mb-3">
                                    <p v-for="(item, i) in userData.addresses" :key="item.id" class="mb-1">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                v-text="item.type.icon" dense small class="mr-2 mb-2"/>

                                        <span v-if="item.street">{{ item.street }}</span>
                                        <span v-if="item.street2">, {{ item.street2 }}</span>
                                        <span v-if="item.street3">, {{ item.street3 }}</span>
                                        <br/>{{ item.postal_code }} {{ item.city }}
                                        <br/><span v-if="item.country">{{
                                            $helpers.i18n.localized(item.country)
                                        }}</span>
                                    </p>
                                </div>

                                <div v-if="userData.socials && userData.socials.length > 0">
                                    <hr class="lighten"/>
                                    <p v-for="(item, i) in userData.socials" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                v-text="item.type.icon" dense small class="mr-2"/>
                                        {{ item.social_link }}
                                    </p>
                                </div>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <hr class="lighten"/>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(101)" small dense left color="success">
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else small dense left>mdi-cancel</v-icon>
                                    {{ langMap.profile.new_assigned_to_me }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(201)" small dense left color="success">mdi-check-circle</v-icon>
                                    <v-icon v-else small dense left>mdi-cancel</v-icon>
                                    {{ langMap.profile.new_assigned_to_team }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(301)" small dense left color="success">mdi-check-circle</v-icon>
                                    <v-icon v-else small dense left>mdi-cancel</v-icon>
                                    {{ langMap.profile.new_assigned_to_company }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(103)" small dense left color="success">mdi-check-circle</v-icon>
                                    <v-icon v-else small dense left>mdi-cancel</v-icon>
                                    {{ langMap.profile.client_response_assigned_to_me }}
                                </p>
                            </v-col>
                            <v-col cols="6">
                                <hr class="lighten" />

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(102)" small dense left color="success">mdi-check-circle</v-icon>
                                    <v-icon v-else small dense left>mdi-cancel</v-icon>
                                    {{ langMap.profile.update_assigned_to_me }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(202)" small dense left color="success">mdi-check-circle</v-icon>
                                    <v-icon v-else small dense left>mdi-cancel</v-icon>
                                    {{ langMap.profile.update_assigned_to_team }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(302)" small dense left color="success">mdi-check-circle</v-icon>
                                    <v-icon v-else small dense left>mdi-cancel</v-icon>
                                    {{ langMap.profile.update_assigned_to_company }}
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
                                            <label>{{ langMap.profile.avatar }}</label>
                                            <v-avatar size="80px">
                                                <v-img :src="avatar" style="z-index: 1;">
                                                    <v-file-input
                                                        v-model="newAvatar"
                                                        color="white"
                                                        accept="image/*"
                                                        dense
                                                        prepend-icon="mdi-camera"
                                                        icon
                                                        style="z-index: 2; max-width: 1em;"
                                                        class="mt-7 ml-7"
                                                    />
                                                </v-img>
                                            </v-avatar>
                                        </v-col>
                                        <v-col cols="10">
                                            <v-row>
                                                <v-col cols="4">
                                                    <v-text-field
                                                        v-model="userData.number"
                                                        :color="themeBgColor"
                                                        :error-messages="errors.number"
                                                        :label="langMap.profile.personal_id"
                                                        dense
                                                        name="number"
                                                        type="text"
                                                        prepend-icon="mdi-picture-in-picture-top-right-outline"
                                                    />
                                                </v-col>
                                                <v-col cols="4">
                                                    <v-text-field
                                                        v-model="userData.title_before_name"
                                                        :color="themeBgColor"
                                                        :error-messages="errors.title_before_name"
                                                        :label="langMap.main.title_before_name"
                                                        dense
                                                        lazy-validation
                                                        name="title_before_name"
                                                        prepend-icon="mdi-book-account-outline"
                                                        type="text"
                                                    />
                                                </v-col>
                                                <v-col cols="4">
                                                    <v-text-field
                                                        v-model="userData.title"
                                                        :color="themeBgColor"
                                                        :error-messages="errors.title"
                                                        :label="langMap.main.title"
                                                        dense
                                                        lazy-validation
                                                        name="title"
                                                        prepend-icon="mdi-book-account-outline"
                                                        type="text"
                                                    />
                                                </v-col>
                                                <v-col cols="4">
                                                    <v-text-field
                                                        v-model="userData.name"
                                                        :color="themeBgColor"
                                                        :error-messages="errors.name"
                                                        :label="langMap.main.first_name"
                                                        dense
                                                        lazy-validation
                                                        name="name"
                                                        prepend-icon="mdi-book-account-outline"
                                                        required
                                                        type="text"
                                                    />
                                                </v-col>
                                                <v-col cols="4">
                                                    <v-text-field
                                                        v-model="userData.middle_name"
                                                        :color="themeBgColor"
                                                        :error-messages="errors.middle_name"
                                                        :label="langMap.main.middle_name"
                                                        dense
                                                        lazy-validation
                                                        name="middle_name"
                                                        prepend-icon="mdi-book-account-outline"
                                                        type="text"
                                                    />
                                                </v-col>
                                                <v-col cols="4">
                                                    <v-text-field
                                                        v-model="userData.surname"
                                                        :color="themeBgColor"
                                                        :error-messages="errors.surname"
                                                        :label="langMap.main.last_name"
                                                        dense
                                                        lazy-validation
                                                        name="surname"
                                                        prepend-icon="mdi-book-account-outline"
                                                        type="text"
                                                    />
                                                </v-col>
                                            </v-row>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col cols="6">
                                            <hr class="lighten" />

                                            <p class="mb-0" v-if="langs && langs.length > 0 && userData">
                                                <v-icon left small dense :color="themeBgColor" :title="langMap.main.language">mdi-web</v-icon>
                                                {{ langs[userData.language_id].name }}
                                            </p>
                                        </v-col>
                                        <v-col cols="6">
                                            <hr class="lighten" />

                                            <p class="mb-0" v-if="userData.deleted_at">
                                                <v-icon v-if="userData.deleted_at" small dense left color="red">mdi-cancel</v-icon>
                                                {{ langMap.individuals.deleted }}
                                            </p>

                                            <p class="mb-0" v-if="!userData.deleted_at">
                                                <v-icon v-if="userData.status" small dense left color="success">mdi-check-circle</v-icon>
                                                <v-icon v-else small dense left>mdi-cancel</v-icon>
                                                {{ langMap.individuals.active }}
                                            </p>

                                            <p class="mb-0">
                                                <v-icon v-if="userData.is_active" small dense left color="success">mdi-check-circle</v-icon>
                                                <v-icon v-else small dense left>mdi-cancel</v-icon>
                                                {{ langMap.main.give_access }}
                                            </p>
                                        </v-col>
                                        <v-col cols="12">
                                            <v-btn text @click="resetPassword" :color="themeBgColor" v-text="langMap.company.reset_password" />
                                        </v-col>
                                    </v-row>

                                    <v-spacer>&nbsp;</v-spacer>
                                    <hr class="lighten" />
                                    <v-spacer>&nbsp;</v-spacer>

                                    <h3>{{ langMap.individuals.contact_info }}</h3>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-list-item v-for="(item, i) in userData.emails" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon v-text="item.type.icon" small dense />
                                                </v-list-item-icon>
                                                <v-list-item-content class="mr-2">
                                                    <v-list-item-title v-text="item.email"></v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type" v-text="$helpers.i18n.localized(item.type)" />
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
                                            <v-list-item v-for="(item, i) in userData.phones" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon v-text="item.type.icon" small dense />
                                                </v-list-item-icon>
                                                <v-list-item-content class="mr-2">
                                                    <v-list-item-title v-text="item.phone"></v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type" v-text="$helpers.i18n.localized(item.type)" />
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
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                                                                            <v-icon left small v-text="data.item.icon"></v-icon> {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            <v-icon left small v-text="data.item.icon"></v-icon> {{ $helpers.i18n.localized(data.item) }}
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
                                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                                                                            <v-icon left small v-text="data.item.icon"></v-icon> {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            <v-icon left small v-text="data.item.icon"></v-icon> {{ $helpers.i18n.localized(data.item) }}
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
                                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                                            <v-list-item v-for="(item, i) in userData.addresses" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon v-text="item.type.icon" small dense />
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="">
                                                        <span v-if="item.street">{{ item.street }}</span>
                                                        <span v-if="item.street2">, {{ item.street2 }}</span>
                                                        <span v-if="item.street3">, {{ item.street3 }}</span>
                                                        <br/>{{ item.postal_code }}&nbsp;&nbsp;{{ item.city }}
                                                        <br/><span v-if="item.country">{{ $helpers.i18n.localized(item.country) }}</span>
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle v-if="item.type" v-text="$helpers.i18n.localized(item.type)" />
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
                                            <v-list-item v-for="(item, i) in userData.socials" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon v-text="item.type.icon" small dense />
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.social_link" />
                                                    <v-list-item-subtitle v-if="item.type" v-text="$helpers.i18n.localized(item.type)" />
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
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                                                                            ({{ data.item.iso_3166_2 }}) {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            ({{ data.item.iso_3166_2 }}) {{ $helpers.i18n.localized(data.item) }}
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
                                                                            <v-icon left small v-text="data.item.icon"></v-icon> {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            <v-icon left small v-text="data.item.icon"></v-icon> {{ $helpers.i18n.localized(data.item) }}
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
                                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
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
                                                                            <v-icon left small v-text="data.item.icon"></v-icon> {{ $helpers.i18n.localized(data.item) }}
                                                                        </template>
                                                                        <template slot="item" slot-scope="data">
                                                                            <v-icon left small v-text="data.item.icon"></v-icon> {{ $helpers.i18n.localized(data.item) }}
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
                                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                                                </v-btn>
                                                            </v-row>
                                                        </v-form>
                                                    </v-expansion-panel-content>
                                                </v-expansion-panel>
                                            </v-expansion-panels>
                                        </v-col>
                                    </v-row>

                                    <v-spacer>&nbsp;</v-spacer>
                                    <hr class="lighten" />
                                    <v-spacer>&nbsp;</v-spacer>

                                    <h3>{{ langMap.profile.notifications_settings }}</h3>

                                    <v-row>
                                        <v-col cols="6">
                                            <v-checkbox
                                                :color="themeBgColor"
                                                v-model="notificationStatuses"
                                                :value="101"
                                                :label="langMap.profile.new_assigned_to_me"
                                                dense
                                            />
                                            <v-checkbox
                                                :color="themeBgColor"
                                                v-model="notificationStatuses"
                                                :value="201"
                                                :label="langMap.profile.new_assigned_to_team"
                                                dense
                                            />
                                            <v-checkbox
                                                :color="themeBgColor"
                                                v-model="notificationStatuses"
                                                :value="301"
                                                :label="langMap.profile.new_assigned_to_company"
                                                dense
                                            />
                                            <v-checkbox
                                                :color="themeBgColor"
                                                v-model="notificationStatuses"
                                                :value="103"
                                                :label="langMap.profile.client_response_assigned_to_me"
                                                dense
                                            />
                                        </v-col>
                                        <v-col cols="6">
                                            <v-checkbox
                                                :color="themeBgColor"
                                                v-model="notificationStatuses"
                                                :value="102"
                                                :label="langMap.profile.update_assigned_to_me"
                                                dense
                                            />
                                            <v-checkbox
                                                :color="themeBgColor"
                                                v-model="notificationStatuses"
                                                :value="202"
                                                :label="langMap.profile.update_assigned_to_team"
                                                dense
                                            />
                                            <v-checkbox
                                                :color="themeBgColor"
                                                v-model="notificationStatuses"
                                                :value="302"
                                                :label="langMap.profile.update_assigned_to_company"
                                                dense
                                            />
                                        </v-col>
                                    </v-row>

                                    <v-spacer>&nbsp;</v-spacer>
                                    <hr class="lighten" />
                                    <v-spacer>&nbsp;</v-spacer>

                                    <v-row>
                                        <v-col cols="3">
                                            <v-autocomplete
                                                v-model="userData.language_id"
                                                :color="themeBgColor"
                                                :item-color="themeBgColor"
                                                :items="languages"
                                                :label="langMap.main.language"
                                                dense
                                                item-text="name"
                                                item-value="id"
                                                lazy-validation
                                                name="language"
                                                prepend-icon="mdi-web"
                                            />
                                        </v-col>
                                        <v-col cols="3">
                                            &nbsp;
                                        </v-col>
                                        <v-col cols="3">
                                            <v-checkbox
                                                v-model="userData.status"
                                                :label="langMap.individuals.active"
                                                :color="themeBgColor"
                                                dense
                                                hide-details
                                                @change="updateStatus"
                                            />
                                        </v-col>
                                        <v-col cols="3">
                                            <v-checkbox
                                                v-model="userData.is_active"
                                                :label="langMap.main.give_access"
                                                :color="themeBgColor"
                                                dense
                                                hide-details
                                                @change="showIsAccessedModal(userData)"
                                            />
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn
                                    v-if="!userData.deleted_at"
                                    text
                                    color="red"
                                    @click="deleteUser"
                                >
                                    {{ langMap.individuals.delete}}
                                </v-btn>
                                <v-btn
                                    v-if="userData.deleted_at"
                                    text
                                    color="green darken"
                                    @click="restoreUser"
                                >
                                    {{ langMap.individuals.restore}}
                                </v-btn>
                                <v-spacer></v-spacer>
                                <v-btn
                                    text
                                    color="grey darken"
                                    @click="cancelUpdateUser"
                                >
                                    {{ langMap.main.cancel}}
                                </v-btn>
                                <v-btn
                                    text
                                    :color="themeBgColor"
                                    @click="updateUser"
                                >
                                    {{ langMap.main.save}}
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
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.main.activities
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-card-title>
                            <v-text-field
                                v-model="activitySearch"
                                append-icon="mdi-magnify"
                                :color="themeBgColor"
                                :label="langMap.main.search"
                                single-line
                                hide-details
                            ></v-text-field>
                        </v-card-title>
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="activityHeaders"
                            :items="userData.employee.activities"
                            :options.sync="options"
                            class="elevation-1"
                            dense
                            :expanded.sync="activityExpanded"
                            single-expand
                            show-expand
                            item-key="id"
                            :search="activitySearch"
                            @update:options="updateItemsPerPage"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon @click="selectActivity(item)">
                                            <v-icon small>mdi-pencil</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.main.update_activity }}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon @click="deleteActivity(item.id)">
                                            <v-icon small>mdi-trash-can</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.main.delete_activity }}</span>
                                </v-tooltip>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <span class="ml-2">
                                    {{ item.content }}
                                </span>
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                </td>
                            </template>
                        </v-data-table>

                        <v-spacer>&nbsp;</v-spacer>

                        <v-expansion-panels v-model="activityFormPanel">
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.main.add_activity }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="md-12">
                                                <v-text-field
                                                    v-model="activityForm.title"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.subject"
                                                    dense
                                                    prepend-icon="mdi-book-account-outline"
                                                    required
                                                    type="text"
                                                />
                                            </v-col>
                                            <v-col cols="md-12">
                                                <v-textarea
                                                    v-model="activityForm.content"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.description"
                                                    dense
                                                    prepend-icon="mdi-book-account-outline"
                                                    required
                                                    type="text"
                                                />
                                            </v-col>
                                            <v-col cols="md-6">
                                                <v-select v-model="activityForm.type_id"
                                                          :color="themeBgColor"
                                                          :item-color="themeBgColor"
                                                          :items="activityTypes"
                                                          :label="langMap.main.type"
                                                          dense
                                                          item-text="name" item-value="id"
                                                >
                                                </v-select>
                                            </v-col>
                                            <v-col cols="md-6">
                                                <v-autocomplete
                                                    v-model="activityForm.client_id"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :items="companies"
                                                    :label="langMap.main.activity_company"
                                                    prepend-icon="mdi-account-outline"
                                                    dense
                                                    item-value="clients.id"
                                                >
                                                    <template v-slot:selection="data">
                                                        {{ data.item.clients.name }}
                                                        <!--                                        ({{ data.item.employee.user_data.email }})-->
                                                    </template>
                                                    <template v-slot:item="data">
                                                        {{ data.item.clients.name }}
                                                        <!--                                        ({{ data.item.employee.user_data.email }})-->
                                                    </template>
                                                </v-autocomplete>
                                            </v-col>
                                            <v-col cols="md-6">
                                                <v-menu
                                                    ref="menuActivityDateRef"
                                                    v-model="menuActivityDate"
                                                    :close-on-content-click="false"
                                                    :return-value.sync="activityForm.date"
                                                    transition="scale-transition"
                                                    offset-y
                                                    min-width="auto"
                                                >
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field
                                                            v-model="activityForm.date"
                                                            label="Date"
                                                            :color="themeBgColor"
                                                            prepend-icon="mdi-calendar"
                                                            readonly
                                                            v-bind="attrs"
                                                            v-on="on"
                                                        ></v-text-field>
                                                    </template>
                                                    <v-date-picker
                                                        v-model="activityForm.date"
                                                        no-title
                                                        :color="themeBgColor"
                                                        scrollable
                                                    >
                                                        <v-spacer></v-spacer>
                                                        <v-btn
                                                            text
                                                            color="primary"
                                                            @click="menuActivityDate = false"
                                                        >
                                                            Cancel
                                                        </v-btn>
                                                        <v-btn
                                                            text
                                                            color="primary"
                                                            @click="$refs.menuActivityDateRef.save(activityForm.date)"
                                                        >
                                                            OK
                                                        </v-btn>
                                                    </v-date-picker>
                                                </v-menu>
                                            </v-col>
                                            <v-col cols="md-6">
                                                <v-menu
                                                    ref="menuActivityTimeRef"
                                                    v-model="menuActivityTime"
                                                    :close-on-content-click="false"
                                                    :nudge-right="40"
                                                    :return-value.sync="activityForm.time"
                                                    transition="scale-transition"
                                                    offset-y
                                                    max-width="290px"
                                                    min-width="290px"
                                                >
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-text-field
                                                            v-model="activityForm.time"
                                                            label="Time"
                                                            :color="themeBgColor"
                                                            prepend-icon="mdi-clock-time-four-outline"
                                                            readonly
                                                            v-bind="attrs"
                                                            v-on="on"
                                                        ></v-text-field>
                                                    </template>
                                                    <v-time-picker
                                                        v-if="menuActivityTime"
                                                        v-model="activityForm.time"
                                                        :color="themeBgColor"
                                                        full-width
                                                        @click:minute="$refs.menuActivityTimeRef.save(activityForm.time)"
                                                    ></v-time-picker>
                                                </v-menu>
                                            </v-col>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                small
                                                @click="addActivity"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                    mdi-plus
                                                </v-icon>
                                            </v-btn>
                                            &nbsp;
                                            <v-btn
                                                color="#f1f1f1"
                                                bottom
                                                dark
                                                fab
                                                right
                                                small
                                                @click="resetActivity"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: red;`">
                                                    mdi-cancel
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
                        dark
                        dense
                        flat
                        :color="themeBgColor"
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.individuals.assigned_companies }}
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
                            :options.sync="options"
                            @click:row="showCompany"
                            @update:options="updateItemsPerPage"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            v-bind="attrs"
                                            v-on="on"
                                            icon
                                            @click.native.stop="removeEmployeeProcess(item)">
                                            <v-icon small>mdi-delete</v-icon>
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
                                    {{ langMap.individuals.new_customer }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <v-row>
                                            <v-col cols="12">
                                                <v-autocomplete
                                                    v-model="employeeForm.client_id"
                                                    :color="themeBgColor"
                                                    :error-messages="employeeForm.id"
                                                    :items="customers"
                                                    :label="langMap.main.company"
                                                    :placeholder="this.$store.state.lang.lang_map.main.search"
                                                    hide-no-data
                                                    hide-selected
                                                    item-text="name"
                                                    item-value="id"
                                                />
                                                <v-text-field
                                                    v-model="employeeForm.description"
                                                    :color="themeBgColor"
                                                    :label="langMap.main.description"
                                                    class="pa-1"
                                                    dense
                                                    type="text"
                                                />
                                            </v-col>
                                        </v-row>
                                        <v-btn
                                            :color="themeBgColor"
                                            bottom
                                            dark
                                            fab
                                            right
                                            small
                                            @click="addEmployee"
                                        >
                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                        </v-btn>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>

            </v-col>
        </v-row>

        <v-row justify="center">
            <v-dialog v-model="removeEmployeeDialog" max-width="520" persistent>
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.individuals.unlink }}
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
            <v-dialog v-model="rolesDialog" max-width="600px" persistent>
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.company.update_info }}: {{ singleUserForm.user.name }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-select
                                v-model="singleUserForm.role_ids"
                                :color="themeBgColor"
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
            <v-dialog v-model="isAccessedDialog" max-width="600px" persistent>
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.company.update_info }}: {{ userData.full_name }}
                    </v-card-title>
                    <v-card-text>
                        {{ userData.is_active === true ? langMap.individuals.give_access : langMap.individuals.remove_access }}
                        <v-select
                            v-model="primaryEmailId"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            :items="userData.emails"
                            :label="langMap.main.email"
                            :readonly="userData.is_active === false"
                            prepend-icon="mdi-mail"
                            item-text="email"
                            item-value="id"
                        />
                        <v-autocomplete
                            v-model="userData.language_id"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            :items="languages"
                            :label="langMap.main.language"
                            item-text="name"
                            item-value="id"
                            lazy-validation
                            name="language"
                            prepend-icon="mdi-web"
                        />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red" text
                               @click="isAccessedDialog = false; userData.is_active = !userData.is_active">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" text @click="changeIsAccessed">{{ langMap.main.save }}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updatePhoneDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.company.update_phone }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field v-model="phoneForm.phone" :color="themeBgColor" :item-color="themeBgColor"
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
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
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
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
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
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{ langMap.company.update_email }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="md-6">
                                    <v-text-field v-model="emailForm.email" :color="themeBgColor" :item-color="themeBgColor"
                                                  :label="langMap.main.email" dense></v-text-field>
                                </v-col>
                                <v-col class="pa-1" cols="md-6">
                                    <v-select v-if="emailForm.email_type === 1" v-model="emailForm.email_type" :color="themeBgColor"
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
            headers: [
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'clients.name'},
                {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'description'},
                {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
            ],
            options: {
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10,
            },
            footerProps: {
                    showFirstLastPage: true,
                    itemsPerPageOptions: [10, 25, 50, 100],
            },
            langMap: this.$store.state.lang.lang_map,
            companies: [],
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            errors: [],
            enableToEdit: false,
            languages: [],
            langs: [],
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
                phones: [],
                addresses: [],
                emails: [],
                socials: [],
                status: '',
                notification_statuses: [],
                number: '',
                avatar_url: '',
                deleted_at: null
            },
            notificationStatuses: [],
            singleUserForm: {
                user: '',
                role_ids: [],
                client_id: ''
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
                phone_type: '',
                opened: null
            },
            addressForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                address: {
                    street: '',
                    street2: '',
                    street3: '',
                    city: '',
                    country_id: ''
                },
                address_type: '',
                opened: null
            },
            socialForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                social_link: '',
                social_type: '',
                opened: null
            },
            emailForm: {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                email: '',
                email_type: '',
                opened: null
            },
            phoneTypes: [],
            addressTypes: [],
            socialTypes: [],
            emailTypes: [],
            isCustomersLoading: false,
            isPasswordVisible: false,
            isPasswordConfirmationVisible: false,
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
            primaryEmailId: null,
            avatar: '',
            newAvatar: null,
            activityForm: {
                model_id: null,
                model_type: 'App\\CompanyUser',
                date: null,
                time: null,
            },
            activityTypes: [],
            activityExpanded: [],
            activityHeaders: [
                {
                    text: 'ID',
                    align: 'start',
                    sortable: false,
                    value: 'id',
                },
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'title'},
                {text: `${this.$store.state.lang.lang_map.main.activity_company}`, value: 'client.name'},
                {text: `${this.$store.state.lang.lang_map.tracking.tracker.date}`, value: 'datetime'},
                {text: `${this.$store.state.lang.lang_map.main.type}`, value: 'type.name'},
                {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
            ],
            activitySearch: '',
            activityFormPanel:[],
            menuActivityDate: false,
            menuActivityTime: false,
            employees: []

        }
    },
    mounted() {
        this.getCountries()
        this.getUser()
        this.getLanguages()
        this.getPhoneTypes()
        this.getAddressTypes()
        this.getSocialTypes()
        this.getEmailTypes()
        this.getRoles()
        this.getClients()
        this.getEmployees();
        this.getActivityTypes();
        // if (localStorage.getItem('auth_token')) {
        //     this.$router.push('tickets')
        // }
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
       EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    watch: {
        newAvatar(val) {
            if (this.newAvatar !== null) {
                this.avatar = URL.createObjectURL(this.newAvatar)
            }
        }
    },
    methods: {
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
        getLanguages() {
            let that = this;
            axios.get('/api/lang').then(response => {
                response = response.data
                if (response.success === true) {
                    this.languages = response.data;
                    response.data.forEach(function (item) {
                        that.langs[item.id] = item;
                    });
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
                    this.activityForm.model_id = this.userData.employee.id

                    if (this.userData.notification_statuses.length) {
                        let that = this;
                        this.userData.notification_statuses.forEach(function (item) {
                            if (item.status !== 0) {
                                that.notificationStatuses.push(item.status);
                            }
                        });
                    } else {
                        this.notificationStatuses = [101, 102, 103, 201, 202, 301, 302];
                    }

                    if (this.userData.avatar_url) {
                        this.avatar = this.userData.avatar_url;
                    } else {
                        this.avatar = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAoMBgDTD2qgAAAAASUVORK5CYII=';
                    }
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        updateUser() {
            this.snackbar = false;

            if (this.newAvatar) {
                let formData = new FormData();
                formData.append('user_id', this.userData.id);
                formData.append('avatar', this.newAvatar);
                axios.post(
                    '/api/user/avatar',
                    formData, {
                        headers: {'content-type': 'multipart/form-data'}
                    }
                ).then(response => {
                    this.newAvatar = null;

                    response = response.data;
                    if (response.success === true) {
                        this.avatar = response.data.avatar_url;
                        this.userData.avatar_url = response.data.avatar_url;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            }

            axios.post('/api/user', this.userData).then(response => {
                response = response.data
                if (response.success === true) {
                    this.updateNotificationsSettings();

                    this.snackbarMessage = this.langMap.company.user_updated;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.enableToEdit = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                    this.errors = response.error;

                }
            });
        },
        cancelUpdateUser() {
            this.getUser();
            this.enableToEdit = false;
        },
        updateNotificationsSettings() {
            this.enableToEdit = false;

            if (this.notificationStatuses.length === 0) {
                this.notificationStatuses = [0];
            }
            axios.post('/api/user/notifications', {
                user_id: this.userData.id,
                notification_statuses: this.notificationStatuses
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.profile.notifications_settings_updated;
                    this.actionColor = 'success';
                    this.snackbar = true;
                } else {
                    this.getUser();
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.snackbar = true;
                }
            });
        },
        updateStatus() {
            this.snackbar = false;
            axios.post('/api/user', this.userData).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.userData.status ? this.langMap.company.employee_activated : this.langMap.company.employee_deactivated;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.enableToEdit = false
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
                    this.resetPhone();

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
                    this.resetPhone();

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
        deleteUser() {
            axios.delete(`/api/user/delete/${this.userData.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.langMap.individuals.deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        restoreUser() {
                axios.post('/api/user/restore', {id: this.userData.id}).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
                        this.snackbarMessage = this.langMap.individuals.restored;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
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
                    this.resetAddress();

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
                    this.resetAddress();

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
        addSocial() {
            this.socialForm.entity_id = this.userData.id

            axios.post('/api/social', this.socialForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetSocial();

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
                    this.resetSocial();

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
            request.language_id = this.userData.language_id
            console.log(this.primaryEmailId);
            request.is_active = this.selectedIsAccessedItem.is_active
            this.singleUserForm.role_ids = this.selectedIsAccessedItem.is_active == true ? [6] : [];
            this.singleUserForm.user = this.userData
            this.singleUserForm.company_user_id = this.userData.employee ? this.userData.employee.id : null;
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
        showIsAccessedModal(item) {
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
            axios.patch(`/api/user/roles`, this.singleUserForm).then(response => {
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
                    this.snackbarMessage = this.langMap.company.employee_added;
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
            this.emailForm.entity_id = this.userData.id
            axios.post('/api/email', this.emailForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetEmail();

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
                    this.resetEmail();

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
            this.emailForm.entity_type = 'App\\User';
            this.emailForm.entity_id = this.userData.id;
        },
        resetEmail() {
            this.emailForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                email: '',
                email_type: '',
                opened: null
            }
        },
        resetPhone() {
            this.phoneForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                phone: '',
                phone_type: '',
                opened: null
            }
        },
        resetAddress() {
            this.addressForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                address: {
                    street: '',
                    street2: '',
                    street3: '',
                    city: '',
                    country_id: ''
                },
                address_type: '',
                opened: null
            }
        },
        resetSocial() {
            this.socialForm = {
                id: '',
                entity_id: '',
                entity_type: 'App\\User',
                social_link: '',
                social_type: '',
                opened: null
            }
        },
        updateItemsPerPage(options) {
            localStorage.itemsPerPage = options.itemsPerPage;
        },
        resetPassword() {
            axios.post('/api/reset_password', {
                email: this.userData.email
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
        addActivity() {
            if (this.activityForm.id) {
                this.updateActivity()
            } else {
            // console.log(this.activityForm);
                axios.post(`/api/activities`, this.activityForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser();
                    this.resetActivity();
                } else {
                    console.log('error')
                }
            });
            }
        },
        updateActivity() {
            console.log(this.activityForm);
            axios.put(`/api/activities/${this.activityForm.id}`, this.activityForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser();
                    this.resetActivity();
                    this.activityFormPanel = []
                } else {
                    console.log('error')
                }
            });
        },
        selectActivity(item) {
            this.activityForm = item
            this.activityFormPanel = 0
        },
        deleteActivity(id) {
            axios.delete(`/api/activities/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.selectedProductId = null;
                    this.snackbarMessage = ''
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        resetActivity() {
            this.activityForm = {
                model_id: this.userData.employee.id,
                model_type: 'App\\CompanyUser',
                date: null,
                time: null,
            }
        },
        getActivityTypes() {
            axios.get('/api/activities/types').then(response => {
                response = response.data
                if (response.success === true) {
                    this.activityTypes = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
    }
}
</script>
