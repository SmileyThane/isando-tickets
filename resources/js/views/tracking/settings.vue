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

        <template>
            <v-tabs v-model="tab">
                <v-tab :key="0">{{ langMap.tracking.settings.tags }}</v-tab>
                <v-tab :key="1">{{ langMap.tracking.settings.services }}</v-tab>
                <v-tab :key="2">{{ langMap.tracking.settings.company }}</v-tab>
                <v-tab :key="3">{{ langMap.tracking.settings.currencies }}</v-tab>
            </v-tabs>

            <v-tabs-items v-model="tab">
                <v-tab-item :key="0">
                    <v-card flat>
                        <v-toolbar flat>
                            <v-dialog
                                v-model="dialogTags"
                                width="500"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        :color="themeBgColor"
                                        style="color: white"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        {{ langMap.tracking.settings.create_tag }}
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                                        {{ langMap.tracking.settings.create_tag_title }}
                                    </v-card-title>

                                    <v-card-text>
                                        <v-text-field
                                            :label="langMap.tracking.settings.name"
                                            v-model="forms.tags.name"
                                            required
                                        ></v-text-field>
                                        <v-text-field
                                            v-model="forms.tags.color"
                                            hide-details
                                            class="ma-0 pa-0"
                                            solo
                                            :label="langMap.tracking.settings.color"
                                            required
                                        >
                                            <template v-slot:append>
                                                <v-menu
                                                    v-model="colorMenuCreate"
                                                    top
                                                    nudge-bottom="105"
                                                    nudge-left="16"
                                                    :close-on-content-click="false"
                                                >
                                                    <template v-slot:activator="{ on }">
                                                        <div
                                                            :style="{
                                                                backgroundColor: forms.tags.color,
                                                                cursor: 'pointer',
                                                                height: '30px',
                                                                width: '30px',
                                                                borderRadius: colorMenuCreate ? '50%' : '4px',
                                                                transition: 'border-radius 200ms ease-in-out'
                                                            }"
                                                            v-on="on"
                                                        />
                                                    </template>
                                                    <v-card>
                                                        <v-card-text
                                                            class="pa-0"
                                                        >
                                                            <v-color-picker
                                                                v-model="forms.tags.color"
                                                                flat
                                                            />
                                                        </v-card-text>
                                                    </v-card>
                                                </v-menu>
                                            </template>
                                        </v-text-field>
                                    </v-card-text>

                                    <v-divider></v-divider>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            color="error"
                                            text
                                            @click="resetForm(); dialogTags = false"
                                        >
                                            {{ langMap.tracking.settings.cancel }}
                                        </v-btn>
                                        <v-btn
                                            color="success"
                                            text
                                            @click="createTag(); dialogTags = false"
                                        >
                                            {{ langMap.tracking.settings.create }}
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </v-toolbar>
                        <v-card-text>
                            <v-data-table
                                dense
                                :headers="headers.tags"
                                :items="$store.getters['Tags/getTags']"
                                :items-per-page="15"
                                class="elevation-1"
                                single-expand
                                show-expand
                            >
                                <template v-slot:item.name="props">
                                    <v-edit-dialog
                                        @save="saveTag(props.item)"
                                        @cancel="saveTag(props.item)"
                                        @open="saveTag(props.item)"
                                        @close="saveTag(props.item)"
                                    >
                                        {{ props.item.name }}
                                        <template v-slot:input>
                                            <v-text-field
                                                v-model="props.item.name"
                                                :label="langMap.tracking.settings.name"
                                                :hint="langMap.tracking.settings.name"
                                                single-line
                                                counter
                                            ></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </template>
                                <template v-slot:item.color="props">
                                    <v-menu
                                        v-model="colorMenu[props.item.id]"
                                        top
                                        nudge-bottom="105"
                                        nudge-left="16"
                                        :close-on-content-click="false"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <div
                                                v-on="on"
                                                :style="{
                                                    backgroundColor: props.item.color,
                                                    cursor: 'pointer',
                                                    height: '30px',
                                                    width: '30px',
                                                    borderRadius: colorMenu[props.item.id] ? '50%' : '4px',
                                                    transition: 'border-radius 200ms ease-in-out'
                                                }"
                                            />
                                        </template>
                                        <v-card>
                                            <v-card-text class="pa-0">
                                                <v-color-picker
                                                    v-model="props.item.color"
                                                    flat
                                                    @input="saveTag(props.item)"
                                                />
                                            </v-card-text>
                                        </v-card>
                                    </v-menu>

                                </template>
                                <template v-slot:item.actions="props">
                                    <v-btn
                                        icon
                                        :color="themeBgColor"
                                        @click="removeTag(props.item.id)"
                                    >
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </template>
                                <template v-slot:expanded-item="{ headers, item }">
                                    <td :colspan="headers.length">
                                        <v-list
                                            dense
                                        >
                                            <v-list-item
                                                v-for="translate in item.translates"
                                                :key="translate.id"
                                                class="d-flex flex-row"
                                            >
                                                <div
                                                    class="d-inline-flex flex-grow-0 mx-3"
                                                    style="min-width: 100px"
                                                >
                                                    {{ getLangName(translate.lang) }}
                                                </div>
                                                <div class="d-inline-flex flex-grow-1">
                                                    <v-text-field
                                                        class="mt-n1"
                                                        hide-details
                                                        dense
                                                        v-model="translate.name"
                                                        @blur="addOrUpdateTranslate(item, translate.lang, translate.name)"
                                                    ></v-text-field>
                                                </div>
                                            </v-list-item>
                                            <v-list-item-action v-if="filterLang(item).length > 0">
                                                <v-dialog
                                                    v-model="addTranslationDialog[item.id]"
                                                    width="500"
                                                >
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-btn
                                                            :color="themeBgColor"
                                                            text
                                                            v-bind="attrs"
                                                            v-on="on"
                                                            @click="resetDialog()"
                                                        >
                                                            Add translation
                                                        </v-btn>
                                                    </template>

                                                    <v-card>
                                                        <v-card-title class="headline grey lighten-2">
                                                            Add translation
                                                        </v-card-title>

                                                        <v-card-text>
                                                            <v-select
                                                                :items="filterLang(item)"
                                                                label="Language"
                                                                item-text="name"
                                                                item-value="locale"
                                                                v-model="tagTranslateForm.lang"
                                                            ></v-select>
                                                            <v-text-field
                                                                label="Tag name"
                                                                v-model="tagTranslateForm.name"
                                                            ></v-text-field>
                                                        </v-card-text>

                                                        <v-divider></v-divider>

                                                        <v-card-actions>
                                                            <v-spacer></v-spacer>
                                                            <v-btn
                                                                color="error"
                                                                text
                                                                @click="closeDialog(item.id)"
                                                            >
                                                                Cancel
                                                            </v-btn>
                                                            <v-btn
                                                                color="success"
                                                                text
                                                                @click="addOrUpdateTranslate(item); closeDialog(item.id)"
                                                            >
                                                                Add
                                                            </v-btn>
                                                        </v-card-actions>
                                                    </v-card>
                                                </v-dialog>
                                            </v-list-item-action>
                                        </v-list>
                                    </td>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item :key="1">
                    <v-card flat>
                        <v-toolbar flat>
                            <v-dialog
                                v-model="dialogServices"
                                width="500"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        :color="themeBgColor"
                                        style="color: white"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        {{ langMap.tracking.settings.create_service }}
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                                        {{ langMap.tracking.settings.create_service_title }}
                                    </v-card-title>

                                    <v-card-text>
                                        <v-text-field
                                            :label="langMap.tracking.settings.name"
                                            v-model="forms.services.name"
                                            required
                                        ></v-text-field>
                                    </v-card-text>

                                    <v-divider></v-divider>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            color="error"
                                            text
                                            @click="resetForm(); dialogServices = false"
                                        >
                                            {{ langMap.tracking.settings.cancel }}
                                        </v-btn>
                                        <v-btn
                                            color="success"
                                            text
                                            @click="createService(); dialogServices = false"
                                        >
                                            {{ langMap.tracking.settings.create }}
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </v-toolbar>
                        <v-card-text>
                            <v-data-table
                                dense
                                :headers="headers.services"
                                :items="$store.getters['Services/getServices']"
                                :items-per-page="15"
                                class="elevation-1"
                            >
                                <template v-slot:item.name="props">
                                    <v-edit-dialog
                                        @save="saveService(props.item)"
                                        @cancel="saveService(props.item)"
                                        @open="saveService(props.item)"
                                        @close="saveService(props.item)"
                                    >
                                        {{ props.item.name }}
                                        <template v-slot:input>
                                            <v-text-field
                                                v-model="props.item.name"
                                                :label="langMap.tracking.settings.name"
                                                :hint="langMap.tracking.settings.name"
                                                single-line
                                                counter
                                            ></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </template>
                                <template v-slot:item.actions="props">
                                    <v-btn
                                        icon
                                        :color="themeBgColor"
                                        @click="removeService(props.item.id)"
                                    >
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item :key="2">
                    <v-card>
                        <v-card-text>
                            <div class="d-flex flex-row">
                                <div class="d-flex-inline">
<!--                                    column 1-->
                                    <v-select
                                        :items="$store.getters['Currencies/getCurrencies']"
                                        item-value="id"
                                        item-text="name"
                                        :label="langMap.tracking.settings.currency"
                                        :placeholder="langMap.tracking.settings.currency"
                                        v-model="currentCurrency"
                                        dense
                                    >
                                        <template v-slot:item="props">
                                            <div class="d-flex flex-row" style="width: 100%">
                                                <div class="d-flex-inline flex-grow-1" style="width: 100%">{{props.item.name}} ({{props.item.slug}})</div>
                                                <div class="d-flex-inline text-right">{{props.item.symbol}}</div>
                                            </div>
                                        </template>
                                    </v-select>
                                </div>
                                <div class="d-flex-inline">
<!--                                    column 2-->
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
                <v-tab-item :key="3">
                    <v-card flat>
                        <v-toolbar flat>
                            <v-dialog
                                v-model="dialogCurrencies"
                                width="500"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        :color="themeBgColor"
                                        style="color: white"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        {{ langMap.tracking.settings.create_currency }}
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                                        {{ langMap.tracking.settings.create_currency_title }}
                                    </v-card-title>

                                    <v-card-text>
                                        <v-text-field
                                            :label="langMap.tracking.settings.name"
                                            v-model="forms.currency.name"
                                            required
                                        ></v-text-field>
                                        <v-text-field
                                            :label="langMap.tracking.settings.slug"
                                            v-model="forms.currency.slug"
                                            required
                                        ></v-text-field>
                                        <v-text-field
                                            :label="langMap.tracking.settings.symbol"
                                            v-model="forms.currency.symbol"
                                            required
                                        ></v-text-field>
                                    </v-card-text>

                                    <v-divider></v-divider>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            color="error"
                                            text
                                            @click="resetForm(); dialogCurrencies = false"
                                        >
                                            {{ langMap.tracking.settings.cancel }}
                                        </v-btn>
                                        <v-btn
                                            color="success"
                                            text
                                            @click="createCurrency(); dialogCurrencies = false"
                                        >
                                            {{ langMap.tracking.settings.create }}
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </v-toolbar>
                        <v-card-text>
                            <v-data-table
                                dense
                                :headers="headers.currencies"
                                :items="$store.getters['Currencies/getCurrencies']"
                                :items-per-page="15"
                                class="elevation-1"
                            >
                                <template v-slot:item.name="props">
                                    <v-edit-dialog
                                        @save="saveCurrency(props.item)"
                                        @cancel="saveCurrency(props.item)"
                                        @open="saveCurrency(props.item)"
                                        @close="saveCurrency(props.item)"
                                    >
                                        {{ props.item.name }}
                                        <template v-slot:input>
                                            <v-text-field
                                                v-model="props.item.name"
                                                :label="langMap.tracking.settings.name"
                                                :hint="langMap.tracking.settings.name"
                                                single-line
                                                counter
                                            ></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </template>
                                <template v-slot:item.slug="props">
                                    <v-edit-dialog
                                        @save="saveCurrency(props.item)"
                                        @cancel="saveCurrency(props.item)"
                                        @open="saveCurrency(props.item)"
                                        @close="saveCurrency(props.item)"
                                    >
                                        {{ props.item.slug }}
                                        <template v-slot:input>
                                            <v-text-field
                                                v-model="props.item.slug"
                                                :label="langMap.tracking.settings.slug"
                                                :hint="langMap.tracking.settings.slug"
                                                single-line
                                                counter
                                            ></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </template>
                                <template v-slot:item.symbol="props">
                                    <v-edit-dialog
                                        @save="saveCurrency(props.item)"
                                        @cancel="saveCurrency(props.item)"
                                        @open="saveCurrency(props.item)"
                                        @close="saveCurrency(props.item)"
                                    >
                                        {{ props.item.symbol }}
                                        <template v-slot:input>
                                            <v-text-field
                                                v-model="props.item.symbol"
                                                :label="langMap.tracking.settings.symbol"
                                                :hint="langMap.tracking.settings.symbol"
                                                single-line
                                                counter
                                            ></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </template>
                                <template v-slot:item.actions="props">
                                    <v-btn
                                        icon
                                        :color="themeBgColor"
                                        @click="removeCurrency(props.item.id)"
                                    >
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs-items>
        </template>

    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import _ from 'lodash';
import TagBtn from './components/tag-btn';

export default {
    components: {TagBtn},
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            tab: 0,
            headers: {
                tags: [
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.tag_name,
                        align: 'start',
                        sortable: true,
                        value: 'name',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.color,
                        sortable: false,
                        value: 'color',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.actions,
                        sortable: false,
                        value: 'actions',
                    }
                ],
                services: [
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.service_name,
                        align: 'start',
                        sortable: true,
                        value: 'name',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.actions,
                        sortable: false,
                        value: 'actions',
                    }
                ],
                currencies: [
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.currency_name,
                        align: 'start',
                        sortable: true,
                        value: 'name',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.currency_slug,
                        align: 'start',
                        sortable: true,
                        value: 'slug',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.currency_symbol,
                        align: 'start',
                        sortable: true,
                        value: 'symbol',
                    },
                    {
                        text: this.$store.state.lang.lang_map.tracking.settings.actions,
                        sortable: false,
                        value: 'actions',
                    }
                ]
            },
            searchTag: null,
            dialogTags: false,
            dialogServices: false,
            dialogCurrencies: false,
            forms: {
                tags: {
                    name: '',
                    color: this.$helpers.color.genRandomColor()
                },
                services: {
                    name: ''
                },
                currency: {
                    name: '',
                    slug: '',
                    symbol: ''
                }
            },
            colorMenuCreate: false,
            colorMenu: {},
            searchService: null,
            searchCurrency: null,
            tagTranslateForm: {
                lang: null,
                name: ''
            },
            addTranslationDialog: {}
        }
    },
    created() {
        this.debounceGetTags = _.debounce(this.__getTags, 1000);
        this.debounceGetServices = _.debounce(this.__getServices, 1000);
        this.debounceGetCurrencies = _.debounce(this.__getCurrencies, 1000);
        this.debounceGetSettings = _.debounce(this.__getSettings, 1000);
        this.debounceGetLanguages = _.debounce(this.__getLanguages, 1000);
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.debounceGetLanguages();
        this.debounceGetTags();
        this.debounceGetServices();
        this.debounceGetCurrencies();
        this.debounceGetSettings();
    },
    methods: {
        __getLanguages() {
            this.$store.dispatch('Languages/getLanguageList');
        },
        __getTags() {
            this.$store.dispatch('Tags/getTagList', { search: this.searchTag });
            this.$store.getters['Tags/getTags'].map(i => {
                this.addTranslationDialog[i.id] = false;
            });
        },
        __getServices() {
            this.$store.dispatch('Services/getServicesList', { search: this.searchService });
        },
        __getCurrencies() {
            this.$store.dispatch('Currencies/getCurrencyList', { search: this.searchCurrency });
        },
        __getSettings() {
            this.$store.dispatch('Tracking/getSettings');
        },
        createTag() {
            this.$store.dispatch('Tags/createTag', this.forms.tags)
                .then(tag => {
                    if (tag) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.tag_created_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.resetForm();
                    }
                });
        },
        createService() {
            this.$store.dispatch('Services/createService', this.forms.services)
                .then(service => {
                    if (service) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_created_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.resetForm();
                    }
                });
        },
        createCurrency() {
            this.$store.dispatch('Currencies/createCurrency', this.forms.currency)
                .then(currency => {
                    if (currency) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.currency_created_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.resetForm();
                    }
                });
        },
        resetForm() {
            this.forms.tags = {
                name: '',
                color: this.$helpers.color.genRandomColor()
            };
            this.forms.services = {
                name: ''
            };
            this.forms.currency = {
                name: '',
                slug: '',
                symbol: ''
            }
        },
        removeTag(tagId) {
            this.$store.dispatch('Tags/deleteTag', tagId)
                .then(result => {
                    if (result) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.tag_deleted_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.tag_removal_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        removeService(serviceId) {
            this.$store.dispatch('Services/deleteService', serviceId)
                .then(result => {
                    if (result) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_deleted_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.service_removal_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        removeCurrency(currencyId) {
            this.$store.dispatch('Currencies/removeCurrency', currencyId)
                .then(result => {
                    console.log(result);
                    if (result) {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.currency_deleted_successfully;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.$store.state.lang.lang_map.tracking.settings.currency_removal_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        saveTag (item) {
            this.$store.dispatch('Tags/updateTag', item);
        },
        saveService (item) {
            this.$store.dispatch('Services/updateService', item);
        },
        saveCurrency (item) {
            this.$store.dispatch('Currencies/updateCurrency', item);
        },
        getLangName(code) {
            const languages = this.$store.getters['Languages/getLanguages'];
            const foundLang = languages.find(i => i.locale === code);
            return foundLang ? foundLang.name : code;
        },
        filterLang(item) {
            const languages = this.$store.getters['Languages/getLanguages'];
            const langs = item.translates.map(t => t.lang);
            return languages.filter(i => langs && langs.indexOf(i.locale) == -1);
        },
        closeDialog(id) {
            console.log(id);
            this.addTranslationDialog[id] = false;
        },
        resetDialog() {
            this.tagTranslateForm = {
                lang: null,
                name: ''
            };
        },
        addOrUpdateTranslate(item, lang = null, name = null) {
            this.$store.dispatch('Tags/addOrUpdateTranslate', {
                id: item.id,
                lang: lang ?? this.tagTranslateForm.lang,
                name: name ?? this.tagTranslateForm.name
            });
        }
    },
    computed: {
        currentCurrency: {
            get: function () {
                const settings = this.$store.getters['Tracking/getSettings'];
                return settings.currency ?? null;
            },
            set: function (currency) {
                this.$store.dispatch('Tracking/updateSettings', {currency});
            }
        }
    }
}
</script>
