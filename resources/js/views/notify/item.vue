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
            <v-col cols="6">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeColor"
                        dense
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.notification.template}}</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <h2 v-if="templateId === 'create'">{{langMap.notification.new_template}}</h2>
                            <div v-else>
                                <h2>{{template.name}}</h2>
                                <p>{{template.description}}</p>
                            </div>

                            <v-btn
                                :color="themeColor"
                                @click="cancel()"
                            >{{langMap.notification.select_template}}</v-btn>
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
                        <v-toolbar-title>{{langMap.notification.recipients}}</v-toolbar-title>
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
                                                v-for="(item, i) in recipients"
                                                :key="item.type+item.id"
                                            >
                                                <v-list-item-icon>
                                                    <v-icon v-if="item.type === 'e'" left small>mdi-email</v-icon>
                                                    <v-icon v-if="item.type === 'c'" left small>mdi-factory</v-icon>
                                                    <v-icon v-if="item.type === 'i'" left small>mdi-account</v-icon>
                                                </v-list-item-icon>
                                                <v-list-item-content>
                                                    <v-list-item-title v-text="item.name"></v-list-item-title>
                                                    <v-list-item-subtitle v-text="item.email"></v-list-item-subtitle>
                                                </v-list-item-content>
                                                <v-list-item-action>
                                                    <v-btn
                                                        dark
                                                        fab
                                                        right
                                                        bottom
                                                        small
                                                        :color="themeColor"
                                                        @click="deleteRecipient(item)"
                                                    >
                                                        <v-icon>mdi-delete</v-icon>
                                                    </v-btn>
                                                </v-list-item-action>
                                            </v-list-item>
                                        </v-list-item-group>
                                    </v-list>
                                    <v-expansion-panels>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.notification.new_email}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <v-row>
                                                        <v-col cols="12" class="pa-1">
                                                            <v-text-field
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                v-model="email"
                                                                :label="langMap.main.email"
                                                                dense
                                                            ></v-text-field>
                                                        </v-col>
                                                        <v-col cols="12" class="pa-1">
                                                            <v-btn
                                                            dark
                                                            fab
                                                            right
                                                            bottom
                                                            small
                                                            :color="themeColor"
                                                            @click="addRecipient('e', Date.now(), email, email)"
                                                        >
                                                            <v-icon>mdi-plus</v-icon>
                                                        </v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.notification.new_customer}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <v-row>
                                                        <v-col cols="12" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-text="name"
                                                                item-value="id"
                                                                v-model="selectedCustomer"
                                                                :items="customers"
                                                                :label="langMap.notification.select_customer"
                                                                dense
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-list-item-title v-text="item.name"></v-list-item-title>
                                                                    <v-list-item-subtitle v-text="item.contact_email"></v-list-item-subtitle>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-list-item-title v-text="item.name"></v-list-item-title>
                                                                    <v-list-item-subtitle v-text="item.contact_email"></v-list-item-subtitle>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-col cols="12" class="pa-1">
                                                                <v-btn
                                                            dark
                                                            fab
                                                            right
                                                            bottom
                                                            small
                                                            :color="themeColor"
                                                            @click="addRecipient('c', selectedCustomer.id, selectedCustomer.name, selectedCustomer.contact_email)"
                                                        >
                                                            <v-icon>mdi-plus</v-icon>
                                                        </v-btn>
                                                        </v-col>
                                                    </v-row>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{langMap.main.new_employee}}
                                                <template v-slot:actions>
                                                    <v-icon color="submit">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <v-row>
                                                        <v-col cols="12" class="pa-1">
                                                            <v-select
                                                                :color="themeColor"
                                                                :item-color="themeColor"
                                                                item-value="id"
                                                                v-model="selectedEmployee"
                                                                :items="employees"
                                                                :label="langMap.notification.select_employee"
                                                                dense
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-list-item-title v-text="item.full_name"></v-list-item-title>
                                                                    <v-list-item-subtitle v-text="item.contact_email"></v-list-item-subtitle>
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-list-item-title v-text="item.full_name"></v-list-item-title>
                                                                    <v-list-item-subtitle v-text="item.contact_email"></v-list-item-subtitle>
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-col cols="12" class="pa-1">
                                                            <v-btn
                                                                dark
                                                                fab
                                                                right
                                                                bottom
                                                                small
                                                                :color="themeColor"
                                                                @click="addRecipient('i', selectedEmployee.id, selectedEmployee.name, selectedEmployee.contact_email)"
                                                            >
                                                                <v-icon>mdi-plus</v-icon>
                                                            </v-btn>
                                                        </v-col>
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
                        <v-toolbar-title>{{langMap.notification.attributes}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12" class="pa-1">
                                    <v-date-picker
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="dueDate"
                                        :label="langMap.notification.due_date"
                                        dense
                                    >
                                    </v-date-picker>
                                </v-col>
                                <v-col cols="12" class="pa-1">
                                    <v-select
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="template.priority"
                                        :items="priorities"
                                        :label="langMap.notification.priority"
                                        item-value="id"
                                        item-text="name"
                                        dense
                                    >
                                    </v-select>
                                </v-col>
                                <v-col cols="12" class="pa-1">
                                    <v-select
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="template.notification_type_id"
                                        :items="notificationTypes"
                                        :label="langMap.notification.notification_type"
                                        item-value="id"
                                        dense
                                    >
                                        <template slot="selection" slot-scope="data">
                                            {{localized(item)}}
                                        </template>
                                        <template slot="item" slot-scope="data">
                                            {{localized(item)}}
                                        </template>
                                    </v-select>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="6">
                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.notification.details}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12" class="pa-1">
                                    <v-date-picker
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="template.name"
                                        :label="langMap.notification.subject"
                                        dense
                                    >
                                    </v-date-picker>
                                </v-col>
                                <v-col cols="12" class="pa-1">
                                    <tiptap-vuetify
                                        :color="themeColor"
                                        v-model="template.text"
                                        :extensions="extensions"
                                        :placeholder="langMap.notification.text"
                                    ></tiptap-vuetify>
                                </v-col>
                                <v-col cols="12" class="pa-1">
                                    <v-select
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        v-model="selectedSignature"
                                        :items="signatures"
                                        :label="langMap.notification.signature"
                                        item-value="id"
                                        item-text="name"
                                        dense
                                    ></v-select>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>

                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.notification.details}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn
                            :color="themeColor"
                            fab
                            @click="saveAs()"
                        >
                            {{langMap.main.save_as}}
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                            :color="themeColor"
                            fab
                            @click="send()"
                        >
                            {{langMap.main.send}}
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                            fab
                            @click="cancel()"
                        >
                            {{langMap.main.cancel}}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                    </v-card-text>
                </v-card>
            </v-col>
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
        components: {TiptapVuetify},

        data() {

            return {
                themeColor: this.$store.state.themeColor,
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
                    priority: ''
                },
                email: '',
                selectedCustomer: null,
                selectedEmployee: null,
                selectedSignature: null,
                recipients: [],
                notificationTypes: [],
                signatures: [],
                customers: [],
                employees: [],
                priorities: [
                    {id: 1, name: this.$store.state.lang.lang_map.notification.priority_hight},
                    {id: 2, name: this.$store.state.lang.lang_map.notification.priority_medium},
                    {id: 3, name: this.$store.state.lang.lang_map.notification.priority_low}
                ],
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
                ],
            }
        },
        mounted() {
            this.getTemplate();
            this.getNotificationTypes();
            this.getSignatures();
            this.getCustomers();
            this.getEmployees();

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
            getTemplate() {
                if (this.$route.params.id === 'create') {
                    return;
                }
                axios.get(`/api/notification/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.notification = response.data;
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
            getCustomers() {
                axios.get('api/client', {
                    params: {
                        search: '',
                        sort_by: 'name',
                        sort_val: false,
                        per_page: -1,
                        page: 1
                    }
                }).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.customers = response.data.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            getEmployees() {
                axios.get('api/employee', {
                    params: {
                        search: '',
                        sort_by: 'name',
                        sort_val: false,
                        per_page: -1,
                        page: 1
                    }
                }).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.employees = response.data.data
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            addRecipient(type, id, name, email) {
                this.recipients.push({
                    'type': type,
                    'id': id,
                    'name': name,
                    'email': email
                });
            },
            deleteRecipient(item) {
                this.recipients.splice(this.recipients.indexOf(item), 1);
            },
            saveAs() {

            },
            send() {

            },
            cancel() {
                this.$router.push('/notify');
            }}
    }
</script>
