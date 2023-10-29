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
            <v-col cols="4">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.main.details }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-label :color="themeBgColor">{{ langMap.notification.subject }}:</v-label>
                        <span>{{ template.subject }}</span>
                        <v-spacer>&nbsp;</v-spacer>
                        <v-label :color="themeBgColor">{{ langMap.notification.sender }}:</v-label>
                        <span>{{ template.sender.full_name }}</span>
                        <v-spacer>&nbsp;</v-spacer>
                        <v-label :color="themeBgColor">{{ langMap.notification.sent_at }}:</v-label>
                        <span>{{ moment(template.created_at).format('YYYY-DD-MM hh:mm') }}</span>
                    </v-card-text>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.notification.attributes }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-label :color="themeBgColor">{{ langMap.notification.notification_type }}:</v-label>
                        <span><v-icon :color="themeBgColor" left small
                                      v-text="template.type.icon"></v-icon> {{ $helpers.i18n.localized(template.type) }}</span>
                        <v-spacer>&nbsp;</v-spacer>
                        <v-label :color="themeBgColor">{{ langMap.notification.priority }}:</v-label>
                        <span>{{ priorities[template.priority] }}</span>
                    </v-card-text>
                </v-card>

                <v-spacer>&nbsp;</v-spacer>

                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.notification.attachments}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-chip class="ma-1" v-for="item in template.attachments" :key="item" outlined :color="themeBgColor">
                            <v-icon dense x-small left :color="themeBgColor">mdi-file</v-icon>{{item}}
                        </v-chip>
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
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.notification.recipients}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-chip class="ma-1" v-for="item in template.recipients" :key="item.address" outlined :color="themeBgColor">
                            <v-icon dense x-small left :color="themeBgColor">mdi-email</v-icon>{{item.address}}
                        </v-chip>
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
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{langMap.notification.text}}</v-toolbar-title>
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
            themeFgColor: this.$store.state.themeFgColor,
themeBgColor: this.$store.state.themeBgColor,
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
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
       EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
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
