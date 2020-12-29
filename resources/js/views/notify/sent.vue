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
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.main.details}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-label :color="themeColor">{{langMap.notification.subject}}:</v-label> <span>{{template.subject}}</span>
                        <v-spacer>&nbsp;</v-spacer>
                        <v-label :color="themeColor">{{langMap.notification.sender}}:</v-label> <span>{{template.sender.full_name}}</span>
                        <v-spacer>&nbsp;</v-spacer>
                        <v-label :color="themeColor">{{langMap.notification.sent_at}}:</v-label> <span>{{moment(template.created_at).format('YYYY-DD-MM hh:mm')}}</span>
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
                        <v-label :color="themeColor">{{langMap.notification.notification_type}}:</v-label> <span><v-icon :color="themeColor" small left v-text="template.type.icon"></v-icon> {{ localized(template.type) }}</span>
                        <v-spacer>&nbsp;</v-spacer>
                        <v-label :color="themeColor">{{langMap.notification.priority}}:</v-label> <span>{{priorities[template.priority]}}</span>
                    </v-card-text>
                </v-card>

                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.notification.attachments}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-chip class="ma-1" v-for="item in template.attachments" :key="item" outlined :color="themeColor">
                            <v-icon dense x-small left :color="themeColor">mdi-file</v-icon>{{item}}
                        </v-chip>
                    </v-card-text>
                </v-card>

            </v-col>
            <v-col cols="8">
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
                        <v-chip class="ma-1" v-for="item in template.recipients" :key="item.address" outlined :color="themeColor">
                            <v-icon dense x-small left :color="themeColor">mdi-email</v-icon>{{item.address}}
                        </v-chip>
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
                        <v-toolbar-title>{{langMap.notification.text}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <div v-html="template.text"></div>
                    </v-card-text>
                </v-card>
            </v-col>
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
            templateId: this.$route.params.id,
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            template: {
                id: '',
                subject: '',
                text: '',
                notification_type_id: '',
                priority: '',
                recipients: [],
                attachments: [],
                type: {
                    icon: '',
                    name: '',
                    name_de: ''
                },
                sender: {
                    full_name: ''
                },
                created_at: ''
            },
            priorities: {
                1: this.$store.state.lang.lang_map.notification.priority_high,
                2: this.$store.state.lang.lang_map.notification.priority_medium,
                3: this.$store.state.lang.lang_map.notification.priority_low
            }
        }
    },
    mounted() {
        this.getTemplate();

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
            axios.get(`/api/notification/history/${this.$route.params.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.template = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        }
    }
}
</script>
