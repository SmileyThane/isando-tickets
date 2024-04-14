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
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.main.profile }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn v-if="!enableToEdit" :color="themeBgColor" icon @click="enableToEdit = true">
                            <v-icon :color="themeFgColor" dense small>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn v-if="enableToEdit" :color="themeBgColor" icon @click="cancelUserData">
                            <v-icon :color="themeFgColor" dense small>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar>

                    <v-card-text v-if="!enableToEdit">
                        <v-row>
                            <v-col cols="2">
                                <v-avatar
                                    v-if="userData.avatar_url || userData.full_name"
                                    color="grey darken-1"
                                    size="80px"
                                >
                                    <v-img v-if="userData.avatar_url " :src="userData.avatar_url" size="100px"/>
                                    <div v-else-if="userData.full_name" class="white--text" size="100px">
                                        {{
                                            userData.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                        }}
                                    </div>
                                </v-avatar>
                                <v-icon v-else size="100px">mdi-account-circle</v-icon>
                            </v-col>
                            <v-col cols="4">
                                <p v-if="userData.number" class="mb-3 font-weight-bold">{{ userData.number }}</p>

                                <h3 class="mb-3">{{ userData.title }} {{ userData.title_before_name }}
                                    {{ userData.full_name }}</h3>

                                <hr/>

                                <div v-if="userData.emails && userData.emails.length > 0" class="mb-3">
                                    <p v-for="(item, i) in userData.emails" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                class="mr-2" dense small v-text="item.type.icon"/>
                                        {{ item.email }}
                                    </p>
                                </div>

                                <div v-if="userData.phones && userData.phones.length > 0">
                                    <hr/>
                                    <p v-for="(item, i) in userData.phones" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                class="mr-2" dense small v-text="item.type.icon"/>
                                        {{ item.phone }}
                                    </p>
                                </div>
                            </v-col>
                            <v-col cols="6">
                                <div v-if="userData.addresses && userData.addresses.length > 0" class="mb-3">
                                    <p v-for="(item, i) in userData.addresses" :key="item.id" class="mb-1">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                class="mr-2 mb-2" dense small v-text="item.type.icon"/>

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
                                    <hr/>
                                    <p v-for="(item, i) in userData.socials" :key="item.id" class="mb-0">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                class="mr-2" dense small v-text="item.type.icon"/>
                                        {{ item.social_link }}
                                    </p>
                                </div>
                            </v-col>
                        </v-row>
                        <v-row>
                            <v-col cols="6">
                                <hr/>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(101)" color="success" dense left small>
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.profile.new_assigned_to_me }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(201)" color="success" dense left small>
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.profile.new_assigned_to_team }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(301)" color="success" dense left small>
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.profile.new_assigned_to_company }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(103)" color="success" dense left small>
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.profile.client_response_assigned_to_me }}
                                </p>
                            </v-col>
                            <v-col cols="6">
                                <hr/>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(102)" color="success" dense left small>
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.profile.update_assigned_to_me }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(202)" color="success" dense left small>
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.profile.update_assigned_to_team }}
                                </p>

                                <p class="mb-0">
                                    <v-icon v-if="notificationStatuses.includes(302)" color="success" dense left small>
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.profile.update_assigned_to_company }}
                                </p>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="12">
                                <hr/>

                                <p v-if="langs && langs[userData.language_id]" class="mb-0">
                                    <v-icon :color="themeBgColor" dense left small>mdi-web</v-icon>
                                    {{ langs[userData.language_id].name }}
                                </p>
                            </v-col>
                            <!--                            <v-col cols="6">-->
                            <!--                                <hr/>-->

                            <!--                                <p class="mb-0">-->
                            <!--                                    <v-icon v-if="userData.status" small dense left color="success">mdi-check-circle</v-icon>-->
                            <!--                                    <v-icon v-else small dense left>mdi-cancel</v-icon>-->
                            <!--                                    {{ langMap.individuals.active }}-->
                            <!--                                </p>-->

                            <!--                                <p class="mb-0">-->
                            <!--                                    <v-icon v-if="userData.is_active" small dense left color="success">mdi-check-circle</v-icon>-->
                            <!--                                    <v-icon v-else small dense left>mdi-cancel</v-icon>-->
                            <!--                                    {{ langMap.main.give_access }}-->
                            <!--                                </p>-->
                            <!--                            </v-col>-->
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
                                                        accept="image/*"
                                                        class="mt-7 ml-7"
                                                        color="white"
                                                        dense
                                                        icon
                                                        prepend-icon="mdi-camera"
                                                        style="z-index: 2; max-width: 1em;"
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
                                                        prepend-icon="mdi-picture-in-picture-top-right-outline"
                                                        type="text"
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
                                                <v-col cols="4">
                                                    <v-text-field
                                                        v-model="userData.password"
                                                        :append-icon="isPasswordVisible ? 'mdi-eye' : 'mdi-eye-off'"
                                                        :color="themeBgColor"
                                                        :error-messages="errors.password"
                                                        :label="langMap.main.password"
                                                        :type="isPasswordVisible ? 'text' : 'password'"
                                                        class="input-group--focused"
                                                        dense
                                                        name="password"
                                                        prepend-icon="mdi-lock"
                                                        @click:append="isPasswordVisible = !isPasswordVisible"
                                                    />

                                                </v-col>
                                                <v-col cols="4">
                                                    <v-text-field
                                                        v-model="userData.password_confirmation"
                                                        :append-icon="isPasswordConfirmationVisible ? 'mdi-eye' : 'mdi-eye-off'"
                                                        :color="themeBgColor"
                                                        :error-messages="errors.password"
                                                        :label="langMap.main.password_confirmation"
                                                        :type="isPasswordConfirmationVisible ? 'text' : 'password'"
                                                        class="input-group--focused"
                                                        dense
                                                        lazy-validation
                                                        name="password_confirmation"
                                                        prepend-icon="mdi-lock"
                                                        @click:append="isPasswordConfirmationVisible = !isPasswordConfirmationVisible"
                                                    />

                                                </v-col>
                                            </v-row>
                                        </v-col>
                                    </v-row>

                                    <v-spacer>&nbsp;</v-spacer>
                                    <hr/>
                                    <v-spacer>&nbsp;</v-spacer>

                                    <h3>{{ langMap.individuals.contact_info }}</h3>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-list-item v-for="(item, i) in userData.emails" :key="item.id">
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
                                            <v-list-item v-for="(item, i) in userData.phones" :key="item.id">
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
                                            <v-list-item v-for="(item, i) in userData.addresses" :key="item.id">
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
                                            <v-list-item v-for="(item, i) in userData.socials" :key="item.id">
                                                <v-list-item-icon v-if="item.type">
                                                    <v-icon dense small v-text="item.type.icon"/>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.social_link"/>
                                                    <v-list-item-subtitle v-if="item.type"
                                                                          v-text="$helpers.i18n.localized(item.type)"/>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon small @click="editSocial(item)">mdi-pencil</v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                    <v-icon small @click="deleteSocial(item.id)">mdi-delete</v-icon>
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
                                                        <v-form ref="addressFormNew" lazy-validation>
                                                            <v-row>
                                                                <v-col cols="12">
                                                                    <v-text-field
                                                                        v-model="addressForm.address.street"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :label="langMap.main.address_line1"
                                                                        :rules="[v => !!v || $helpers.i18n.required(langMap.main.address_line1)]"
                                                                        dense
                                                                        required
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
                                                                    />
                                                                    <v-select
                                                                        v-model="addressForm.address.country_id"
                                                                        :color="themeBgColor"
                                                                        :item-color="themeBgColor"
                                                                        :items="countries"
                                                                        :label="langMap.main.country"
                                                                        :rules="[v => !!v || $helpers.i18n.required(langMap.main.country)]"
                                                                        dense
                                                                        item-value="id"
                                                                        required
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
                                                                        :rules="[v => !!v || $helpers.i18n.required(langMap.main.type)]"
                                                                        dense
                                                                        item-value="id"
                                                                        required
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
                                    <hr/>
                                    <v-spacer>&nbsp;</v-spacer>

                                    <h3>{{ langMap.profile.notifications_settings }}</h3>

                                    <v-row>
                                        <v-col cols="6">
                                            <v-checkbox
                                                v-model="notificationStatuses"
                                                :color="themeBgColor"
                                                :label="langMap.profile.new_assigned_to_me"
                                                :value="101"
                                                dense
                                            />
                                            <v-checkbox
                                                v-model="notificationStatuses"
                                                :color="themeBgColor"
                                                :label="langMap.profile.new_assigned_to_team"
                                                :value="201"
                                                dense
                                            />
                                            <v-checkbox
                                                v-model="notificationStatuses"
                                                :color="themeBgColor"
                                                :label="langMap.profile.new_assigned_to_company"
                                                :value="301"
                                                dense
                                            />
                                            <v-checkbox
                                                v-model="notificationStatuses"
                                                :color="themeBgColor"
                                                :label="langMap.profile.client_response_assigned_to_me"
                                                :value="103"
                                                dense
                                            />
                                        </v-col>
                                        <v-col cols="6">
                                            <v-checkbox
                                                v-model="notificationStatuses"
                                                :color="themeBgColor"
                                                :label="langMap.profile.update_assigned_to_me"
                                                :value="102"
                                                dense
                                            />
                                            <v-checkbox
                                                v-model="notificationStatuses"
                                                :color="themeBgColor"
                                                :label="langMap.profile.update_assigned_to_team"
                                                :value="202"
                                                dense
                                            />
                                            <v-checkbox
                                                v-model="notificationStatuses"
                                                :color="themeBgColor"
                                                :label="langMap.profile.update_assigned_to_company"
                                                :value="302"
                                                dense
                                            />
                                        </v-col>
                                    </v-row>

                                    <v-spacer>&nbsp;</v-spacer>
                                    <hr/>
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
                                        <v-col cols="9">
                                            &nbsp;
                                        </v-col>
                                        <!--                                        <v-col cols="3">-->
                                        <!--                                            <v-checkbox-->
                                        <!--                                                v-model="userData.status"-->
                                        <!--                                                :label="langMap.individuals.active"-->
                                        <!--                                                :color="themeBgColor"-->
                                        <!--                                                dense-->
                                        <!--                                                hide-details-->
                                        <!--                                                @change="updateStatus"-->
                                        <!--                                            />-->
                                        <!--                                        </v-col>-->
                                        <!--                                        <v-col cols="12">-->
                                        <!--                                            <v-checkbox-->
                                        <!--                                                v-model="userData.is_active"-->
                                        <!--                                                :label="langMap.main.give_access"-->
                                        <!--                                                :color="themeBgColor"-->
                                        <!--                                                dense-->
                                        <!--                                                hide-details-->
                                        <!--                                                @change="showIsAccessedModal(userData)"-->
                                        <!--                                            />-->
                                        <!--                                        </v-col>-->
                                    </v-row>
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    color="grey darken"
                                    text
                                    @click="cancelUserData"
                                >
                                    {{ langMap.main.cancel }}
                                </v-btn>
                                <v-btn
                                    :color="themeBgColor"
                                    text
                                    @click="updateUser"
                                >
                                    {{ langMap.main.save }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-expand-transition>
                </v-card>
                <br>
                <v-card v-if="$helpers.auth.checkPermissionByIds([88])">
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
                                <v-list-item v-for="(item, i) in userData.billing" :key="item.id">
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
                <v-card v-if="userData.ixarma_link">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.profile.link_to_ixarma
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn v-if="!enableToEditIxarma" :color="themeBgColor" icon @click="enableToEditIxarma = true">
                            <v-icon :color="themeFgColor" dense small>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn v-if="enableToEditIxarma" :color="themeBgColor" icon @click="cancelUserData">
                            <v-icon :color="themeFgColor" dense small>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar>

                    <v-card-text v-if="!enableToEditIxarma">
                        <v-row>
                            <v-col cols="6">
                                <p class="mb-0">
                                    <v-icon v-if="userData.ixarma_link && userData.ixarma_link.login && userData.ixarma_link.password" color="success" dense left small>
                                        mdi-check-circle
                                    </v-icon>
                                    <v-icon v-else dense left small>mdi-cancel</v-icon>
                                    {{ langMap.profile.ixarma_connected }}
                                </p>
                            </v-col>
                            <v-col cols="6">
                                <p class="mb-0">
                                    {{ langMap.profile.ixarma_login }}
                                    {{ userData.ixarma_link ? userData.ixarma_link.login : '' }}
                                </p>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-expand-transition>
                        <v-card v-if="enableToEditIxarma" class="transition-fast-in-fast-out">
                            <v-card-text>
                                <v-form>
                                    <v-row>
                                        <v-col cols="6">
                                            <v-text-field
                                                v-model="userData.ixarma_link.login"
                                                :color="themeBgColor"
                                                :error-messages="errors.ixarma_login"
                                                :label="langMap.profile.ixarma_login"
                                                dense
                                                name="ixarma_login"
                                                prepend-icon="mdi-book-account-outline"
                                                type="text"
                                            />
                                        </v-col>
                                        <v-col cols="6">
                                            <v-text-field
                                                v-model="userData.ixarma_link.password"
                                                :append-icon="isPasswordVisible ? 'mdi-eye' : 'mdi-eye-off'"
                                                :color="themeBgColor"
                                                :error-messages="errors.ixarma_password"
                                                :label="langMap.profile.ixarma_password"
                                                :type="isPasswordVisible ? 'text' : 'password'"
                                                class="input-group--focused"
                                                dense
                                                name="ixarma_password"
                                                prepend-icon="mdi-lock"
                                                @click:append="isPasswordVisible = !isPasswordVisible"
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
                                    @click="cancelUserData"
                                >
                                    {{ langMap.main.cancel }}
                                </v-btn>
                                <v-btn
                                    :color="themeBgColor"
                                    text
                                    @click="updateUserIxarma"
                                >
                                    {{ langMap.profile.check_and_save_ixarma_link }}
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-expand-transition>
                </v-card>

            </v-col>
            <v-col cols="6">
                <v-card v-if="$helpers.auth.checkPermissionByIds([2,23])">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.profile.email_signatures
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12">
                                    <v-list-item v-for="(item, i) in userData.email_signatures" :key="item.id">
                                        <v-list-item-content>
                                            <v-list-item-title v-text="item.name"></v-list-item-title>
                                        </v-list-item-content>
                                        <v-list-item-action>
                                            <v-icon small @click="editEmailSignature(item)">
                                                mdi-pencil
                                            </v-icon>
                                        </v-list-item-action>
                                        <v-list-item-action>
                                            <v-icon small @click="deleteEmailSignature(item.id)">
                                                mdi-delete
                                            </v-icon>
                                        </v-list-item-action>
                                    </v-list-item>
                                </v-col>
                                <v-col cols="12">
                                    <v-expansion-panels v-model="emailSignatureForm.opened" accordion>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{ langMap.profile.new_email_signature }}
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
                                                                v-model="emailSignatureForm.name"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.name"
                                                                dense
                                                            />
                                                        </v-col>
                                                        <v-col cols="12">
                                                            <Tinymce
                                                                v-model="emailSignatureForm.signature"
                                                                :color="themeBgColor"
                                                                :label="langMap.profile.signature"
                                                                :placeholder="langMap.profile.signature"
                                                                aria-rowcount="7"
                                                            ></Tinymce>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeBgColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
                                                            @click="addEmailSignature"
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
                        </v-form>
                    </v-card-text>
                </v-card>

                <v-spacer>&nbsp;</v-spacer>

                <v-card>
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                                langMap.profile.user_theme_colors
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn v-if="!enableToEditColor" :color="themeBgColor" icon @click="enableToEditColor = true">
                            <v-icon :color="themeFgColor" dense small>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn v-if="enableToEditColor" :color="themeBgColor" icon @click="cancelUserDataSettings">
                            <v-icon :color="themeFgColor" dense small>mdi-close</v-icon>
                        </v-btn>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="6">
                                    <label>{{ langMap.profile.user_theme_fg_color }}</label>
                                    <v-color-picker
                                        v-model="themeFgColorNew"
                                        :disabled="!enableToEditColor"
                                        dot-size="25"
                                        mode="hexa"
                                    />
                                    <v-checkbox
                                        v-model="autoFgColor"
                                        :color="themeBgColor"
                                        :label="langMap.system_settings.automate_theme_fg_color"
                                        :readonly="!enableToEditColor"
                                        :value="true"
                                        @change="setThemeFgColor"
                                    />
                                    <p>{{ langMap.system_settings.automate_theme_fg_color_hint }}</p>
                                </v-col>
                                <v-col cols="6">
                                    <label>{{ langMap.profile.user_theme_bg_color }}</label>
                                    <v-color-picker
                                        v-model="themeBgColorNew"
                                        :disabled="!enableToEditColor"
                                        dot-size="25"
                                        mode="hexa"
                                    />
                                    <v-checkbox
                                        v-model="resetThemeBgColorFlag"
                                        :color="themeBgColor"
                                        :label="langMap.profile.revert_to_company_theme_colors"
                                        :readonly="!enableToEditColor"
                                        :value="1"
                                        @change="resetThemeBgColor()"
                                    />
                                    <v-btn :color="companySettings.theme_bg_color"
                                           :style="`color: ${companySettings.theme_fg_color};`">
                                        {{ langMap.main.example }}
                                    </v-btn>
                                    <v-spacer>&nbsp;</v-spacer>

                                    <v-checkbox
                                        v-model="themeBgColorDlg"
                                        :color="themeBgColor"
                                        :label="langMap.profile.show_speed_panel"
                                        :readonly="!enableToEditColor"
                                        :value="1"></v-checkbox>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions v-if="enableToEditColor">
                        <v-spacer></v-spacer>
                        <v-btn
                            color="grey darken"
                            text
                            @click="cancelUserDataSettings"
                        >
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn
                            :color="themeFgColor"
                            :style="`color: ${themeBgColor};`"
                            text
                            @click="updateUserDataSettings"
                        >
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-card-actions>
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
                                                  :item-color="themeBgColor" :label="langMap.main.phone" dense/>
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

            <v-dialog v-model="updateAddressDlg" max-width="600px" persistent>
                <v-card>
                    <v-form ref="addressFormUpd" lazy-validation>
                        <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                                      class="mb-5">
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
                                            :rules="[v => !!v || $helpers.i18n.required(langMap.main.address_line1)]"
                                            dense
                                            no-resize
                                            placeholder=""
                                            required
                                            rows="3"
                                        />
                                        <v-text-field
                                            v-model="addressForm.address.street2"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :label="langMap.main.address_line2"
                                            dense
                                            no-resize
                                            placeholder=""
                                            rows="3"
                                        />
                                        <v-text-field
                                            v-model="addressForm.address.street3"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :label="langMap.main.address_line3"
                                            dense
                                            no-resize
                                            placeholder=""
                                            rows="3"
                                        />
                                    </v-col>
                                    <v-col class="pa-1" cols="md-6">
                                        <v-text-field
                                            v-model="addressForm.address.postal_code"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :label="langMap.main.postal_code"
                                            dense
                                        />
                                    </v-col>
                                    <v-col class="pa-1" cols="md-6">
                                        <v-text-field
                                            v-model="addressForm.address.city"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :label="langMap.main.city"
                                            dense
                                        />
                                    </v-col>
                                    <v-col class="pa-1" cols="md-6">
                                        <v-select
                                            v-model="addressForm.address.country_id"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="countries"
                                            :label="langMap.main.country"
                                            :rules="[v => !!v || $helpers.i18n.required(langMap.main.country)]"
                                            dense
                                            item-value="id"
                                            required
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
                                            :rules="[v => !!v || $helpers.i18n.required(langMap.main.type)]"
                                            dense
                                            item-value="id"
                                            required
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
                            <v-btn :color="themeBgColor" text @click="updateAddress()">{{ langMap.main.save }}</v-btn>
                        </v-card-actions>
                    </v-form>
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
                                                  :item-color="themeBgColor" :label="langMap.main.email" dense/>
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

            <v-dialog v-model="updateEmailSignatureDlg" :eager="true" :retain-focus="false" max-width="600px"
                      persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.profile.update_email_signature }}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col class="pa-1" cols="12">
                                    <v-text-field
                                        v-model="emailSignatureForm.name"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :label="langMap.main.name"
                                        dense
                                    />
                                </v-col>
                                <v-col class="pa-1" cols="12">
                                    <Tinymce
                                        v-model="emailSignatureForm.signature"
                                        :placeholder="langMap.profile.signature"
                                        aria-rowcount="7"
                                    />
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="red" text @click="updateEmailSignatureDlg=false; resetEmailSignature()">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" text
                               @click="updateEmailSignatureDlg=false; updateEmailSignature()">{{ langMap.main.save }}
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
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            errors: [],
            enableToEdit: false,
            enableToEditIxarma: false,
            enableToEditColor: false,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
            userData: {
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
                language_id: '',
                timezone_id: '',
                notification_statuses: [],
                number: '',
                avatar_url: '',
                ixarma_link: {
                    login: '',
                    password: ''
                }
            },
            notificationStatuses: [],
            phoneForm: {
                entity_id: '',
                entity_type: 'App\\User',
                phone: '',
                phone_type: '',
                opened: null
            },
            addressForm: {
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
                address_type: '',
                opened: null
            },
            emailForm: {
                entity_id: '',
                entity_type: 'App\\User',
                email: '',
                email_type: '',
                opened: null
            },
            emailSignatureForm: {
                entity_id: '',
                entity_type: 'App\\User',
                name: '',
                signature: '',
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
            phoneTypes: [],
            addressTypes: [],
            emailTypes: [],
            socialTypes: [],
            languages: [],
            langs: [],
            timezones: [],
            countries: [],
            themeFgColorNew: this.$store.state.themeFgColor,
            themeBgColorNew: this.$store.state.themeBgColor,
            themeBgColorDlg: localStorage.themeBgColorDlg == 1 ? 0 : 1,
            internalBillings: [],
            internalBillingEditor: null,
            internalBillingForm: {},
            autoFgColor: false,
            companySettings: {
                theme_bg_color: '#6AA75D',
                theme_fg_color: '#FFFFFF',
                override_user_theme: false
            },
            resetThemeBgColorFlag: 0,
            isPasswordVisible: false,
            isPasswordConfirmationVisible: false,
            updatePhoneDlg: false,
            updateAddressDlg: false,
            updateEmailDlg: false,
            updateEmailSignatureDlg: false,
            updateSocialDlg: false,
            avatar: '',
            newAvatar: null,
            currency: {
                symbol: ''
            }
        }
    },
    mounted() {
        this.getUser();
        this.getPhoneTypes();
        this.getAddressTypes();
        this.getEmailTypes();
        this.getSocialTypes();
        this.getLanguages();
        this.getTimeZones();
        this.getCountries();
        this.getCompanySettings();
        // this.getInternalBilling();
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
        },
        mainCompany() {
            if (this.$store.state.mainCompany.currency) {
                this.currency = this.$store.state.mainCompany.currency;
            }
        },
    },
    methods: {
        getUser() {
            axios.get('/api/user').then(response => {
                response = response.data
                if (response.success === true) {
                    this.userData = response.data;
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
        getTimeZones() {
            axios.get('/api/time_zones').then(response => {
                response = response.data
                if (response.success === true) {
                    this.timezones = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getInternalBilling() {
            axios.get('/api/billing/internal').then(response => {
                response = response.data
                if (response.success === true) {
                    this.internalBillings = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        editInternalBilling(item) {

            console.log(this.internalBillingForm);
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
                    this.getUser()
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
                    this.getUser()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        createInternalBilling() {
            axios.post(`/api/billing/internal`, this.internalBillingForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.internalBillingEditor = null
                    this.internalBillingForm = {}
                    this.getUser()
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

                    this.snackbarMessage = this.langMap.main.update_successful;
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
        updateUserIxarma() {
            this.snackbar = false;
            axios.post('/api/user/ixarma/'+this.userData.id, this.userData.ixarma_link).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful;
                    this.actionColor = 'success'
                    this.snackbar = true
                    this.enableToEdit = false
                    this.enableToEditIxarma = false
                    this.getUser()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                    this.errors = response.error;
                }
            });
        },
        cancelUserData() {
            this.enableToEdit = false;
            this.enableToEditIxarma = false;
            this.getUser();
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

                    window.location.reload()
                } else {
                    this.getUser();
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
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
                    this.resetPhone();
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
                    this.resetPhone();

                    this.getUser();
                    this.snackbarMessage = this.langMap.company.phone_updated;
                    this.actionColor = 'success';
                    this.snackbar = true;
                    this.resetPhone();
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
                    this.getUser()
                    this.snackbarMessage = this.langMap.company.phone_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.resetPhone();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        resetPhone() {
            this.phoneForm = {
                entity_id: '',
                entity_type: 'App\\User',
                phone: '',
                phone_type: '',
                opened: null
            }
        },
        addAddress() {
            if (!this.$refs.addressFormNew.validate()) {
                return;
            }
            this.addressForm.entity_id = this.userData.id

            axios.post('/api/address', this.addressForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.resetAddress();

                    this.getUser()
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
            if (!this.$refs.addressFormUpd.validate()) {
                return;
            }
            axios.patch(`/api/address/${this.addressForm.id}`, this.addressForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.updateAddressDlg = false;

                    this.resetAddress();

                    this.getUser()
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
                    this.getUser()
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
                address_type: '',
                opened: null
            }
        },
        updateUserDataSettings() {
            this.snackbar = false;

            axios.post('/api/user/settings', {
                theme_bg_color: this.themeBgColorNew,
                theme_fg_color: this.themeFgColorNew
            }).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.themeBgColor = this.themeBgColorNew;
                    this.$store.state.themeBgColor = this.themeBgColorNew;
                    EventBus.$emit('update-theme-bg-color', this.themeBgColorNew);

                    this.themeFgColor = this.themeFgColorNew;
                    this.$store.state.themeFgColor = this.themeFgColorNew;
                    EventBus.$emit('update-theme-fg-color', this.themeFgColorNew);

                    localStorage.themeBgColorDlg = this.themeBgColorDlg == 1 ? 0 : 1;
                    this.enableToEditColor = false;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
                return true;
            });
        },
        cancelUserDataSettings() {
            this.enableToEditColor = false;
            this.getUser();
            this.getCompanySettings();
        },
        getCompanySettings() {
            this.snackbar = false;

            axios.get(`/api/main_company/settings`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.companySettings['theme_fg_color'] = response.data.hasOwnProperty('theme_fg_color') ? response.data.theme_fg_color : '#FFFFFF';

                    if (response.data.hasOwnProperty('theme_bg_color')) {
                        this.companySettings['theme_bg_color'] = response.data.theme_bg_color;
                    } else if (response.data.hasOwnProperty('theme_color')) {
                        this.companySettings['theme_bg_color'] = response.data.theme_color;
                    } else {
                        this.companySettings['theme_bg_color'] = '#6AA75D';
                    }

                    this.companySettings['override_user_theme'] = response.data.hasOwnProperty('override_user_theme') ? response.data.override_user_theme : false;

                    axios.get('/api/user/settings').then(response => {
                        response = response.data;
                        if (response.success === true) {
                            if (response.data.hasOwnProperty('theme_bg_color')) {
                                this.themeBgColorNew = response.data.theme_bg_color;
                            } else if (response.data.hasOwnProperty('theme_color')) {
                                this.themeBgColorNew = response.data.theme_color;
                            } else if (this.companySettings.override_user_theme) {
                                this.themeBgColorNew = this.companySettings.theme_bg_color
                            } else {
                                this.themeBgColorNew = '#6AA75D';
                            }
                            this.themeBgColor = this.themeBgColorNew;

                            if (response.data.hasOwnProperty('theme_fg_color')) {
                                this.themeFgColorNew = response.data.theme_fg_color;
                            } else if (this.companySettings.override_user_theme) {
                                this.themeFgColorNew = this.companySettings.theme_fg_color
                            } else {
                                this.themeFgColorNew = '#FFFFFF';
                            }

                            this.themeFgColor = this.themeFgColorNew;

                            this.enableToEdit = false;
                        } else {
                            this.snackbarMessage = this.langMap.main.user_permissions_warning;
                            this.actionColor = 'warinng';
                            this.snackbar = true;
                        }
                    });
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        resetThemeBgColor() {
            if (this.resetThemeBgColorFlag === 1) {
                this.themeFgColorNew = this.companySettings.theme_fg_color;
                this.themeBgColorNew = this.companySettings.theme_bg_color;
            } else {
                this.themeBgColorNew = this.themeBgColor;
                this.themeFgColorNew = this.themeFgColor;
            }
        },
        setThemeFgColor() {
            if (this.autoFgColor) {
                this.themeFgColorNew = this.$helpers.color.invertColor(this.themeBgColorNew);
            } else {
                this.themeFgColorNew = this.themeFgColor;
            }
        },
        editPhone(item) {
            this.updatePhoneDlg = true;

            this.phoneForm.id = item.id;
            this.phoneForm.phone = item.phone;
            this.phoneForm.phone_type = item.type ? item.type.id : 0;
        },
        editAddress(item) {
            if (this.$refs.addressFormUpd) {
                this.$refs.addressFormUpd.resetValidation();
            }
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
        editSocial(item) {
            this.updateSocialDlg = true;

            this.socialForm.id = item.id;
            this.socialForm.social_link = item.social_link;
            this.socialForm.social_type = item.type ? item.type.id : 0;
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
                    this.resetEmail();

                    this.getUser();
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
                    this.getUser()
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
        },
        resetEmail() {
            this.emailForm = {
                entity_id: '',
                entity_type: 'App\\User',
                email: '',
                email_type: '',
                opened: null
            };
        },
        addEmailSignature() {
            this.emailSignatureForm.entity_id = this.userData.id
            axios.post('/api/email_signature', this.emailSignatureForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.snackbarMessage = this.langMap.profile.email_signature_created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.resetEmailSignature();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        updateEmailSignature() {
            axios.patch(`/api/email_signature/${this.emailSignatureForm.id}`, this.emailSignatureForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.emailSignatureForm.id = '';
                    this.getUser();
                    this.snackbarMessage = this.langMap.profile.email_signature_updated;
                    this.actionColor = 'success';
                    this.snackbar = true;
                    this.resetEmailSignature();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
                return true
            });
        },
        deleteEmailSignature(id) {
            axios.delete(`/api/email_signature/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUser()
                    this.emailSignatureForm.name = ''
                    this.emailSignatureForm.signature = ''
                    this.snackbarMessage = this.langMap.profile.email_signature_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.resetEmailSignature();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        editEmailSignature(item) {
            this.updateEmailSignatureDlg = true;

            this.emailSignatureForm.id = item.id;
            this.emailSignatureForm.name = item.name;
            this.emailSignatureForm.signature = item.signature;
        },
        resetEmailSignature() {
            this.emailSignatureForm = {
                entity_id: '',
                entity_type: 'App\\User',
                name: '',
                signature: '',
                opened: null
            };
        }
    },
    computed: {
        mainCompany: function () {
            return this.$store.state.mainCompany;
        }
    }
}
</script>
