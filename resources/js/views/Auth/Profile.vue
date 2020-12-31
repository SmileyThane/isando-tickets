<template>
    <v-container fluid>
        <v-row>
            <v-snackbar
                :bottom="true"
                :right="true"
                v-model="snackbar"
                :color="actionColor"
            >
                {{ snackbarMessage }}
            </v-snackbar>
            <v-col class="col-md-6">
                <v-card class="elevation-6">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{this.$store.state.lang.lang_map.main.profile}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 4px" @click="cancelUpdateUser">
                            {{this.$store.state.lang.lang_map.main.cancel}}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateUser">
                            {{this.$store.state.lang.lang_map.main.update}}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-text-field
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.title_before_name"
                                    name="title_before_name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.title_before_name"
                                    :error-messages="errors.title_before_name"
                                    lazy-validation
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.title"
                                    name="title"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.title"
                                    :error-messages="errors.title"
                                    lazy-validation
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.first_name"
                                    name="name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.name"
                                    :error-messages="errors.name"
                                    lazy-validation
                                    required
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.middle_name"
                                    name="middle_name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.middle_name"
                                    :error-messages="errors.middle_name"
                                    lazy-validation
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    :color="themeColor"
                                    :label="this.$store.state.lang.lang_map.main.last_name"
                                    name="surname"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.surname"
                                    :error-messages="errors.surname"
                                    lazy-validation
                                    class="col-md-6"
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-text-field
                                    :color="themeColor"
                                    id="password"
                                    :label="this.$store.state.lang.lang_map.main.password"
                                    placeholder="********"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    type="password"
                                    v-model="userData.password"
                                    :error-messages="errors.password"
                                    lazy-validation
                                    class="col-md-6"
                                    required
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-select
                                    :label="this.$store.state.lang.lang_map.main.country"
                                    :color="themeColor"
                                    class="col-md-6"
                                    :item-color="themeColor"
                                    name="country"
                                    prepend-icon="mdi-map"
                                    item-value="id"
                                    :items="countries"
                                    v-model="userData.country_id"
                                    :error-messages="errors.country_id"
                                    lazy-validation
                                    :readonly="!enableToEdit"
                                    required
                                    dense>
                                <template slot="selection" slot-scope="data">
                                    ({{ data.item.iso_3166_2 }}) {{ localized(data.item) }}
                                </template>
                                <template slot="item" slot-scope="data">
                                    ({{ data.item.iso_3166_2 }}) {{ localized(data.item) }}
                                </template>
                                </v-select>
                                <v-text-field
                                    :color="themeColor"
                                    label="Anredeform"
                                    name="anredeform"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.anredeform"
                                    :error-messages="errors.anredeform"
                                    lazy-validation
                                    class="col-md-6"
                                    required
                                    :readonly="!enableToEdit"
                                    dense
                                ></v-text-field>
                                <v-select
                                    :label="this.$store.state.lang.lang_map.main.language"
                                    :color="themeColor"
                                    class="col-md-6"
                                    :item-color="themeColor"
                                    name="language"
                                    prepend-icon="mdi-mail"
                                    item-text="name"
                                    item-value="id"
                                    :items="languages"
                                    v-model="userData.language_id"
                                    :error-messages="errors.language_id"
                                    lazy-validation
                                    :readonly="!enableToEdit"
                                    dense
                                />
                                <v-select
                                    :label="this.$store.state.lang.lang_map.main.timezone"
                                    :color="themeColor"
                                    class="col-md-6"
                                    :item-color="themeColor"
                                    name="timezone"
                                    prepend-icon="mdi-timetable"
                                    item-text="text"
                                    item-value="id"
                                    :items="timezones"
                                    v-model="userData.timezone_id"
                                    :error-messages="errors.timezone_id"
                                    lazy-validation
                                    :readonly="!enableToEdit"
                                    dense
                                />
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>

                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-6">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>
                            {{langMap.profile.email_signatures}}
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
                                        <v-list-item-group :color="themeColor">
                                            <v-list-item
                                                v-for="(item, i) in userData.email_signatures"
                                                :key="item.id"
                                            >
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.name"></v-list-item-title>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-icon
                                                        small
                                                        @click="editEmailSignature(item)"
                                                    >
                                                        mdi-pencil
                                                    </v-icon>
                                                </v-list-item-action>
                                                <v-list-item-action>
                                                    <v-icon small @click="deleteEmailSignature(item.id)">
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
                                                {{langMap.profile.new_email_signature}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="12" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="emailSignatureForm.name"
                                                                :label="langMap.main.name"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="12" class="pa-1">
                                                            <tiptap-vuetify
                                                                ref="body"
                                                                aria-rowcount="7"
                                                                :color="themeColor"
                                                                v-model="emailSignatureForm.signature"
                                                                :extensions="extensions"
                                                                :placeholder="langMap.profile.signature"
                                                                :label="langMap.profile.signature"
                                                            ></tiptap-vuetify>
                                                        </v-col>
                                                        <v-btn
                                                            dark
                                                            fab
                                                            right
                                                            bottom
                                                            small
                                                            :color="themeColor"
                                                            @click="addEmailSignature"
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
            <v-col class="col-md-6">
                <v-card class="elevation-6">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>
                            {{this.$store.state.lang.lang_map.individuals.contact_info}}
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
                                                    <v-icon small :title="langMap.profile.login_email">
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
                                                <v-list-item-icon v-if="item.type"><v-icon left v-text="item.type.icon"></v-icon></v-list-item-icon>
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
                                                <v-list-item-icon v-if="item.type"><v-icon left v-text="item.type.icon"></v-icon></v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="">
                                                        <span v-if="item.street">{{item.street}}, </span>
                                                        <span v-if="item.street2">{{item.street2}}, </span>
                                                        <span v-if="item.street3">{{item.street3}}</span>
                                                        <br>{{item.postal_code}}&nbsp;&nbsp;{{item.city}},
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
                                                {{this.$store.state.lang.lang_map.main.phone}}
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
                                                                :label="this.$store.state.lang.lang_map.main.phone"
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
                                                                :label="this.$store.state.lang.lang_map.main.type"
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
                                                {{this.$store.state.lang.lang_map.main.address}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <div class="row">
                                                        <v-col cols="md-12" class="pa-1">
                                                            <v-text-field
                                                                no-resize
                                                                rows="3"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.street"
                                                                :label="langMap.main.address_line1"
                                                                dense
                                                            ></v-text-field>
                                                            <v-text-field
                                                                no-resize
                                                                rows="3"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.street2"
                                                                :label="langMap.main.address_line2"
                                                                dense
                                                            ></v-text-field>
                                                            <v-text-field
                                                                no-resize
                                                                rows="3"
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="addressForm.address.street3"
                                                                :label="langMap.main.address_line3"
                                                                dense
                                                            ></v-text-field>
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

                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-6">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.profile.user_theme_color}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 4px" @click="cancelUpdateUserSettings">
                            {{this.$store.state.lang.lang_map.main.cancel}}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateUserSettings">
                            {{this.langMap.main.update}}
                        </v-btn>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="6">
                                    <v-color-picker
                                        dot-size="25"
                                        mode="hexa"
                                        v-model="themeColorNew"
                                        :disabled="!enableToEdit"
                                    ></v-color-picker>
                                </v-col>
                                <v-col cols="6">
                                    <v-checkbox
                                        :color="themeColor"
                                        :readonly="!enableToEdit"
                                        v-model="resetThemeColorFlag"
                                        :value="1"
                                        @change="resetThemeColor()"
                                    >
                                        <template v-slot:label>
                                            {{ langMap.profile.revert_to_company_theme_color }}
                                            <v-icon x-large right :color="companySettings.theme_color">mdi-checkbox-blank</v-icon>
                                        </template>
                                    </v-checkbox>

                                    <v-spacer>&nbsp;</v-spacer>

                                    <v-checkbox
                                        :color="themeColor"
                                        :readonly="!enableToEdit"
                                        v-model="themeColorDlg"
                                        :value="1"
                                        :label="langMap.profile.show_speed_panel"></v-checkbox>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

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

            <v-dialog v-model="updateAddressDlg" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.company.update_address}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="md-12" class="pa-1">
                                    <v-text-field
                                        no-resize
                                        rows="3"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.street"
                                        :label="langMap.main.address_line1"
                                        placeholder=""
                                        dense
                                    ></v-text-field>
                                    <v-text-field
                                        no-resize
                                        rows="3"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.street2"
                                        :label="langMap.main.address_line2"
                                        placeholder=""
                                        dense
                                    ></v-text-field>
                                    <v-text-field
                                        no-resize
                                        rows="3"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="addressForm.address.street3"
                                        :label="langMap.main.address_line3"
                                        placeholder=""
                                        dense
                                    ></v-text-field>
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
                                    <v-select v-if="emailForm.email_type === 1" readonly :color="themeColor" :item-color="themeColor"
                                              v-model="emailForm.email_type" :items="emailTypes" item-value="id"
                                              dense :label="langMap.main.type">
                                        <template slot="selection" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon small left v-text="data.item.icon"></v-icon> {{ localized(data.item) }}
                                        </template>
                                    </v-select>
                                    <v-select v-else :color="themeColor" :item-color="themeColor"
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

            <v-dialog v-model="updateEmailSignatureDlg" persistent max-width="600px">
                <v-card>
                    <v-card-title>
                        <span class="headline">{{langMap.profile.update_email_signature}}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="12" class="pa-1">
                                    <v-text-field
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="emailSignatureForm.name"
                                        :label="langMap.main.name"
                                        dense
                                    ></v-text-field>                                </v-col>
                                <v-col cols="12" class="pa-1">
                                    <tiptap-vuetify
                                        ref="body"
                                        aria-rowcount="7"
                                        :color="themeColor"
                                        v-model="emailSignatureForm.signature"
                                        :extensions="extensions"
                                        :placeholder="langMap.profile.signature"
                                        :label="langMap.profile.signature"
                                    ></tiptap-vuetify>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateEmailSignatureDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeColor" text @click="updateEmailSignatureDlg=false; updateEmailSignature()">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

        </v-row>
    </v-container>

</template>

<script>

    import EventBus from "../../components/EventBus";
    import {
        Blockquote,
        Bold,
        BulletList,
        Code,
        HardBreak,
        Heading,
        History,
        HorizontalRule,
        Image,
        Italic,
        Link,
        ListItem,
        OrderedList,
        Paragraph,
        Strike,
        TiptapVuetify,
        Underline
    } from 'tiptap-vuetify';

    export default {
        components: {
            TiptapVuetify
        },
        data() {
            return {
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                errors: [],
                enableToEdit: false,
                themeColor: this.$store.state.themeColor,
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
                    timezone_id: ''
                },
                phoneForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    phone: '',
                    phone_type: ''
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
                    address_type: ''
                },
                emailForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    email: '',
                    email_type: ''
                },
                emailSignatureForm: {
                    entity_id: '',
                    entity_type: 'App\\User',
                    name: '',
                    signature: ''
                },
                phoneTypes: [],
                addressTypes: [],
                emailTypes: [],
                languages: [],
                timezones: [],
                countries: [],
                themeColorNew: this.$store.state.themeColor,
                themeColorDlg: localStorage.themeColorDlg == 1 ? 0 : 1,
                companySettings: {
                    theme_color: '',
                    override_user_theme: false
                },
                resetThemeColorFlag: 0,
                updatePhoneDlg: false,
                updateAddressDlg: false,
                updateEmailDlg: false,
                updateEmailSignatureDlg: false,
                extensions: [
                    History,
                    Blockquote,
                    Link,
                    Underline,
                    Strike,
                    Italic,
                    ListItem,
                    BulletList,
                    OrderedList,
                    [Heading, {
                        options: {
                            levels: [1, 2, 3]
                        }
                    }],
                    Bold,
                    Code,
                    HorizontalRule,
                    Paragraph,
                    Image,
                    HardBreak
                ]
            }
        },
        mounted() {
            this.getUser();
            this.getPhoneTypes();
            this.getAddressTypes();
            this.getEmailTypes();
            this.getLanguages();
            this.getTimeZones();
            this.getCountries();
            this.getCompanySettings();
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
            getUser() {
                axios.get('/api/user').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.userData = response.data
                        // console.log(this.userData);
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
            updateUser(e) {
                e.preventDefault()
                this.snackbar = false;
                axios.post('/api/user', this.userData).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.userData.password = ''
                        this.getUser()
                        this.snackbarMessage = this.langMap.main.update_successful;
                        this.actionColor = 'success'
                        this.snackbar = true
                        this.enableToEdit = false
                        window.location.reload()
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            cancelUpdateUser() {
                this.enableToEdit = false;
                this.getUser();
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
            deletePhone(id) {
                axios.delete(`/api/phone/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getUser()
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
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            getUserSettings() {
                this.snackbar = false;

                axios.get('/api/user/settings').then(response => {
                    response = response.data;
                    if (response.success === true) {
                        if (this.companySettings.override_user_theme !== true) {
                            this.themeColor = response.data.theme_color;
                            this.$store.state.themeColor = response.data.theme_color;
                            localStorage.themeColor = response.data.theme_color;
                            EventBus.$emit('update-theme-color', response.data.theme_color);
                        }
                        this.enableToEdit = false;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                    return true;
                });
            },
            updateUserSettings() {
                this.snackbar = false;

                axios.post('/api/user/settings', {theme_color: this.themeColorNew}).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        if (this.companySettings.override_user_theme !== true) {
                            this.themeColor = this.themeColorNew;
                            this.$store.state.themeColor = this.themeColorNew;
                            localStorage.themeColor = this.themeColorNew;
                            EventBus.$emit('update-theme-color', this.themeColorNew);
                        }
                        localStorage.themeColorDlg = this.themeColorDlg == 1 ? 0 : 1;
                        this.enableToEdit = false;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                    return true;
                });
            },
            cancelUpdateUserSettings() {
                this.enableToEdit = false;
                this.getUser();
                this.getCompanySettings();
                if (!this.companySettings.override_user_theme) {
                    this.getUserSettings();
                }
            },
            getCompanySettings() {
                axios.get(`/api/main_company_settings`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.companySettings['theme_color'] = response.data.hasOwnProperty('theme_color') ? response.data.theme_color : '#4caf50';
                        this.companySettings['override_user_theme'] = response.data.hasOwnProperty('override_user_theme') ? response.data.override_user_theme : false;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            resetThemeColor() {
                const resetThemeColorFlag = this.resetThemeColorFlag;
                if (resetThemeColorFlag === 1) {
                    this.themeColorNew = this.companySettings.theme_color;
                } else {
                    this.themeColorNew = this.$store.state.themeColor;
                }
            },
            editPhone(item) {
                this.updatePhoneDlg = true;

                this.phoneForm.id = item.id;
                this.phoneForm.phone = item.phone;
                this.phoneForm.phone_type = item.type ? item.type.id : 0;
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
            }
        }
    }
</script>
