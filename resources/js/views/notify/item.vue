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
        <v-row>
            <v-col cols="4">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dense
                        dark
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.notification.template}}</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <h3 v-if="templateId === 'create'">{{langMap.notification.new_template}}</h3>
                            <div v-else>
                                <h3>{{template.name}}</h3>
                                <p>{{template.description}}</p>
                            </div>

                            <a href="#" @click="cancel">{{langMap.notification.select_template}}</a>
                        </v-form>
                    </v-card-text>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        :color="themeBgColor"
                        dark
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.notification.recipients}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn color="white" style="color: black;" @click="addRecipient(); updateRecipientsDlg = true">
                            {{langMap.main.update}}
                        </v-btn>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12">
                                    <perfect-scrollbar>
                                        <v-treeview
                                            ref="tree1"
                                            :items="template.recipients"
                                            dense
                                            :color="themeBgColor"
                                        >
                                            <template v-slot:prepend="{ item }">
                                                <v-icon small v-if="item.entity_type === 'App\\Client'">
                                                    mdi-factory
                                                </v-icon>
                                                <v-icon small v-if="item.entity_type === 'App\\User'">
                                                    mdi-account
                                                </v-icon>
                                                <v-icon small v-if="item.entity_type === 'App\\Email' && item.type">
                                                    {{ item.type.icon }}
                                                </v-icon>
                                                <v-icon small v-else-if="item.entity_type === 'App\\Email' && !item.type">
                                                    mdi-email
                                                </v-icon>
                                            </template>
                                            <template v-slot:label="{ item }">
                                                {{ item.name }}
                                                <small v-if="item.type">
                                                    ({{ localized(item.type) }})
                                                </small>
                                            </template>
                                        </v-treeview>
                                    </perfect-scrollbar>
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
                        :color="themeBgColor"
                        dark
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.notification.attributes}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12" class="pa-3">

                                    <v-menu
                                        ref="menu1"
                                        v-model="datepickerMenu"
                                        :close-on-content-click="false"
                                        transition="scale-transition"
                                        offset-y
                                        max-width="290px"
                                        min-width="290px"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                                :color="themeBgColor"
                                                dense
                                                v-model="dueDate"
                                                :label="langMap.notification.due_date"
                                                persistent-hint
                                                append-icon="mdi-calendar"
                                                v-bind="attrs"
                                                v-on="on"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker
                                            :color="themeBgColor"
                                            v-model="dueDate"
                                            no-title
                                            @input="datepickerMenu = false"
                                        ></v-date-picker>
                                    </v-menu>
                                </v-col>
                                <v-col cols="12" class="pa-3">
                                    <v-select
                                        ref="priority"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        v-model="template.priority"
                                        :items="priorities"
                                        :label="langMap.notification.priority"
                                        item-value="id"
                                        item-text="name"
                                        dense
                                        required
                                    >
                                    </v-select>
                                </v-col>
                                <v-col cols="12" class="pa-3">
                                    <v-select
                                        ref="type"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        v-model="template.notification_type_id"
                                        :items="notificationTypes"
                                        :label="langMap.notification.notification_type"
                                        item-value="id"
                                        dense
                                        required
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
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="8">
                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        :color="themeBgColor"
                        dark
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.notification.details}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12" class="pa-3">

                                    <v-text-field
                                        ref="subject"
                                        :color="themeBgColor"
                                        v-model="template.name"
                                        :label="langMap.notification.subject"
                                        dense
                                        required
                                    >
                                    </v-text-field>
                                </v-col>
                                <v-col cols="12" class="pa-3">
                                    <Tinymce
                                        ref="body"
                                        aria-rowcount="20"
                                        v-model="template.text"
                                        :placeholder="langMap.notification.text"
                                        required
                                    />
                                    <v-spacer>&nbsp;</v-spacer>
                                </v-col>
                                <v-col cols="12" class="pa-3">
                                    <v-select
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        v-model="selectedSignatureId"
                                        :items="signatures"
                                        :label="langMap.notification.signature"
                                        item-value="id"
                                        item-text="name"
                                        dense
                                    >
                                        <template slot="selection" slot-scope="data">
                                            <div class="text--black mt-3" v-html="data.item.signature"></div>
                                        </template>
                                    </v-select>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>

                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        :color="themeBgColor"
                        dark
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.notification.attachments}}</v-toolbar-title>
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
                                        <v-list-item-group :color="themeBgColor">
                                            <v-list-item
                                                v-for="(item, i) in attachments"
                                                :key="item.id"
                                            >
                                                <v-list-item-content>
                                                    <v-file-input
                                                        dense
                                                        :color="themeBgColor"
                                                        v-model="attachments[i]"
                                                        :label="langMap.notification.select_file"
                                                        show-size
                                                    >
                                                    </v-file-input>

                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-btn
                                                        dark
                                                        fab
                                                        right
                                                        bottom
                                                        small
                                                        :color="themeBgColor"
                                                        @click="attachments.splice(i, 1)"
                                                    >
                                                        <v-icon x-small>mdi-delete</v-icon>
                                                    </v-btn>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header @click.native.stop="attachments.push({})">
                                                {{langMap.notification.new_attachment}}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`" @click="attachments.push({})">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                        </v-expansion-panel>
                                    </v-expansion-panels>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>

                <v-spacer>&nbsp;</v-spacer>


                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        :color="themeBgColor"
                        dark
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.main.actions}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" class="pa-3">
                                <v-btn
                                    :color="themeBgColor"
                                    dark
                                    @click="saveAsDlg = true"
                                    class="mr-3"
                                >
                                    {{langMap.main.save_as}}
                                </v-btn>

                                <v-btn
                                    :color="themeBgColor"
                                    dark
                                    @click="send()"
                                    class="mr-3"

                                >
                                    {{langMap.main.send}}
                                </v-btn>

                                <v-btn
                                    @click="cancel()"
                                    class="mr-3"
                                    color="grey"
                                >
                                    {{langMap.main.cancel}}
                                </v-btn>

                                <v-btn
                                    color="red lighten-4"
                                    @click="deleteDlg = true"
                                >
                                    {{langMap.main.delete}}
                                </v-btn>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row justify="center">
            <v-dialog v-model="saveAsDlg" persistent max-width="600px">
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{langMap.main.save_as}}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="6" class="pa-3">
                                    <v-text-field :color="themeBgColor" :item-color="themeBgColor" v-model="template.name" :label="langMap.main.name" dense></v-text-field>
                                </v-col>
                                <v-col cols="6" class="pa-3">
                                    <v-text-field :color="themeBgColor" :item-color="themeBgColor" v-model="template.description" :label="langMap.main.description" dense></v-text-field>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="saveAsDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeBgColor" text @click="saveAsDlg=false; saveAsTemplate()">{{langMap.main.save}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="deleteDlg" persistent max-width="480">
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{langMap.notification.delete_template}}
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="deleteDlg = false">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteTemplate()">
                            {{langMap.main.delete}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="updateRecipientsDlg" persistent max-width="800px">
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{langMap.notification.recipients}}
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <div class="row">
                                <v-col cols="5" class="pa-3">
                                    <h3>{{langMap.notification.select_recipient}}</h3>
                                    <v-spacer>&nbsp;</v-spacer>

                                    <v-text-field
                                        v-model="srcSearch"
                                        :label="langMap.main.search"
                                        dense
                                        hide-details
                                        clearable
                                        clear-icon="mdi-close-circle-outline"
                                        :color="themeBgColor"
                                        class="pa-3"
                                    ></v-text-field>
                                    <perfect-scrollbar>
                                        <v-treeview
                                            :items="recipients"
                                            v-model="toAdd"
                                            :search="srcSearch"
                                            hoverable
                                            selectable
                                            open-on-click
                                            item-disabled="disabled"
                                            dense
                                            :color="themeBgColor"
                                            :selected-color="themeBgColor"
                                        >
                                            <template v-slot:prepend="{ item }">
                                                <v-icon small v-if="item.entity_type === 'App\\Client'">
                                                    mdi-factory
                                                </v-icon>
                                                <v-icon small v-if="item.entity_type === 'App\\User'">
                                                    mdi-account
                                                </v-icon>
                                                <v-icon small v-if="item.entity_type === 'App\\Email' && item.type">
                                                    {{ item.type.icon }}
                                                </v-icon>
                                                <v-icon small v-else-if="item.entity_type === 'App\\Email' && !item.type">
                                                    mdi-email
                                                </v-icon>
                                            </template>
                                            <template v-slot:label="{ item }">
                                                {{ item.name }}
                                                <small v-if="item.type">
                                                    ({{ localized(item.type) }})
                                                </small>
                                            </template>
                                        </v-treeview>
                                    </perfect-scrollbar>
                                </v-col>
                                <v-col cols="2">
                                    <v-spacer>&nbsp;</v-spacer>

                                    <v-btn
                                        style="color: white;"
                                        :color="themeBgColor"
                                        :title="langMap.notification.add_selected"
                                        class="pa-3 mt-10"
                                        @click="addRecipient"
                                    >
                                        &gt;&gt;
                                    </v-btn>
                                    <v-spacer>&nbsp;</v-spacer>
                                    <v-btn
                                        style="color: white;"
                                        :color="themeBgColor"
                                        :title="langMap.notification.delete_selected"
                                        class="pa-3"
                                        @click="deleteRecipient"
                                    >
                                        &lt;&lt;
                                    </v-btn>
                                </v-col>
                                <v-col cols="5" class="pa-3">
                                    <h3>{{langMap.notification.selected_recipients}}</h3>
                                    <v-spacer>&nbsp;</v-spacer>
                                    <v-text-field
                                        v-model="dstSearch"
                                        :label="langMap.main.search"
                                        dense
                                        hide-details
                                        clearable
                                        clear-icon="mdi-close-circle-outline"
                                        :color="themeBgColor"
                                        class="pa-3"
                                    ></v-text-field>
                                    <perfect-scrollbar>
                                        <v-treeview
                                            ref="tree2"
                                            :items="newRecipients"
                                            v-model="toDelete"
                                            selectable
                                            open-all
                                            :search="dstSearch"
                                            item-disabled="locked"
                                            dense
                                            :color="themeBgColor"
                                            :selected-color="themeBgColor"
                                        >
                                            <template v-slot:prepend="{ item }">
                                                <v-icon small v-if="item.entity_type === 'App\\Client'">
                                                    mdi-factory
                                                </v-icon>
                                                <v-icon small v-if="item.entity_type === 'App\\User'">
                                                    mdi-account
                                                </v-icon>
                                                <v-icon small v-if="item.entity_type === 'App\\Email' && item.type">
                                                    {{ item.type.icon }}
                                                </v-icon>
                                                <v-icon small v-else-if="item.entity_type === 'App\\Email' && !item.type">
                                                    mdi-email
                                                </v-icon>
                                            </template>
                                            <template v-slot:label="{ item }">
                                                {{ item.name }}
                                                <small v-if="item.type">
                                                    ({{ localized(item.type) }})
                                                </small>
                                            </template>
                                        </v-treeview>
                                    </perfect-scrollbar>
                                </v-col>
                            </div>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="red" text @click="updateRecipientsDlg=false">{{langMap.main.cancel}}</v-btn>
                        <v-btn :color="themeBgColor" text @click="updateRecipientsDlg=false; updateRecipients()">{{langMap.main.update}}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </v-container>
</template>
<style scoped>
>>>.ps {
    max-height: 300px;
}
>>>.v-treeview--dense .v-treeview-node__root {
    min-height: 1.1em;
}
>>>.v-treeview-node__root .v-icon {
    font-size: 20px;
}
>>>.v-treeview-node__checkbox {
    margin-left: 0px !important;
}
</style>

<style src="vue2-perfect-scrollbar/dist/vue2-perfect-scrollbar.css"/>

<script>
import EventBus from "../../components/EventBus";

export default {
    data() {
        return {
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
            templateId: this.$route.params.id,
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            template: {
                id: '',
                name: '',
                description: '',
                text: '',
                notification_type_id: '',
                priority: '',
                recipients: []
            },
            datepickerMenu: false,
            selectedSignatureId: null,
            saveAsDlg: false,
            deleteDlg: false,
            updateRecipientsDlg: false,
            dueDate: '',
            attachments: [],
            notificationTypes: [],
            signatures: [],
            recipients: [],
            newRecipients: [],
            priorities: [
                {id: 1, name: this.$store.state.lang.lang_map.notification.priority_high},
                {id: 2, name: this.$store.state.lang.lang_map.notification.priority_medium},
                {id: 3, name: this.$store.state.lang.lang_map.notification.priority_low}
            ],
            srcSearch: '',
            dstSearch: '',
            toAdd: [],
            toDelete: []
        }
    },
    mounted() {
        this.getTemplate();
        this.getNotificationTypes();
        this.getSignatures();
        this.getRecipients();

        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
       EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        localized(item, field = 'name') {
            let locale = this.$store.state.lang.locale.replace(/^([^_]+).*$/, '$1');
            return item[field + '_' + locale] ? item[field + '_' + locale] : item[field];
        },
        getTemplate() {
            if (this.$route.params.id === 'create') {
                return;
            }
            axios.get(`/api/notification/${this.$route.params.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.template = response.data;
                    let that = this;
                    let check = function(item) {
                        if (item.entity_type === 'App\\Email') {
                            that.toAdd.push(item.id);
                        }
                        if (item.children) {
                            item.children.forEach(check);
                        }
                    }
                    this.template.recipients.forEach(check);
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        getSignatures() {
            axios.get('/api/email_signatures').then(response => {
                response = response.data
                if (response.success === true) {
                    this.signatures = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getNotificationTypes() {
            axios.get(`/api/notification_types`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.notificationTypes = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getRecipients() {
            axios.get('/api/recipients').then(response => {
                response = response.data
                if (response.success === true) {
                    this.recipients = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        updateRecipients() {
            this.template.recipients = this.newRecipients;
        },
        addRecipient() {
            let that = this;
            let disable = function(items) {
                items.forEach(function (item) {
                    if (that.toAdd.includes(item.id)) {
                        item.disabled = true;
                    }
                    if (item.children) {
                        disable(item.children);
                    }
                });
            }
            disable(this.recipients);

            let clear = function (items) {
                for (let i = 0; i < items.length; i++) {
                    if (items[i].children) {
                        clear(items[i].children)
                    }
                    if (items[i].entity_type === 'App\\Email') {
                        if (items[i].disabled !== true) {
                            items.splice(i, 1);
                            i--;
                        }
                    } else {
                        if (!items[i].children || items[i].children.length === 0) {
                            items.splice(i, 1);
                            i--;
                        }
                    }

                }
            }

            this.newRecipients = JSON.parse(JSON.stringify(this.recipients));
            clear(this.newRecipients);
            this.toAdd = [];
        },
        deleteRecipient() {
            let that = this;

            let enable = function(items) {
                items.forEach(function (item) {
                    if (that.toDelete.includes(item.id)) {
                        item.disabled = false;
                    }
                    if (item.children) {
                        enable(item.children);
                    }
                });
            }
            enable(this.recipients);

            let clear = function (items) {
                for (let i = 0; i < items.length; i++) {
                    if (items[i].children) {
                        clear(items[i].children)
                    }
                    if (items[i].entity_type === 'App\\Email') {
                        if (that.toDelete.includes(items[i].id)) {
                            items.splice(i, 1);
                            i--;
                        }
                    } else {
                        if (!items[i].children || items[i].children.length === 0) {
                            items.splice(i, 1);
                            i--;
                        }
                    }
                }
            }

            clear(this.newRecipients);
            this.toDelete = [];
        },
        saveAsTemplate() {
            if (!this.template.notification_type_id) {
                this.snackbarMessage = this.langMap.notification.type_required;
                this.actionColor = 'error';
                this.snackbar = true;
                this.$refs.type.focus();
                return false;
            }
            if (!this.template.priority) {
                this.snackbarMessage = this.langMap.notification.priority_required;
                this.actionColor = 'error';
                this.snackbar = true;
                this.$refs.priority.focus();
                return false;
            }
            if (!this.template.text) {
                this.snackbarMessage = this.langMap.notification.text_required;
                this.actionColor = 'error';
                this.snackbar = true;
                this.$refs.body.focus();
                return false;
            }
            if (!this.template.name) {
                this.snackbarMessage = this.langMap.notification.name_required;
                this.actionColor = 'error';
                this.snackbar = true;
                this.$refs.subject.focus();
                return false;
            }
            if (!this.template.description) {
                this.snackbarMessage = this.langMap.notification.description_required;
                this.actionColor = 'error';
                this.snackbar = true;
                this.$refs.subject.focus();
                return false;
            }
            axios.post('/api/notification', this.template).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.notification.template_saved;
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
        deleteTemplate() {
            axios.delete(`/api/notification/${this.template.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getTemplate();
                    this.snackbarMessage = this.langMap.notification.template_deleted;
                    this.actionColor = 'success';
                    this.snackbar = true;
                    this.cancel();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
                return true
            });
        },
        send() {
            let that = this;

            let recipients = [];
            let extract = function(item) {
                if (item.entity_type === 'App\\Email') {
                    recipients.push(item.name);
                }
                if (item.children) {
                    item.children.forEach(extract);
                }
            }
            this.template.recipients.forEach(extract);

            let signature  = this.signatures.find(function (item) {
                return item.id === that.selectedSignatureId;
            });

            let formData = new FormData();
            formData.append('subject', this.template.name);
            formData.append('body', this.template.text + (signature ? '\n' + signature.signature : ''));
            formData.append('recipients', recipients.join(','));
            formData.append('notification_type', this.template.notification_type_id);
            formData.append('priority', this.template.priority);

            this.attachments.forEach(function (item, i) {
                formData.append('attachment_'+i, item);
            });
            axios.post('/api/notification/send', formData,{
                headers:{
                    'content-type': 'multipart/form-data'
                }
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.notification.notification_sent;
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
        cancel() {
            this.$router.push('/notify');
        }
    }
}
</script>
