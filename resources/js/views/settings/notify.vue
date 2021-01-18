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
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-data-table
                            :expanded.sync="expanded"
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="users"
                            :loading="loading"
                            :loading-text="langMap.main.loading"
                            :options.sync="options"
                            :server-items-length="totalUsers"
                            class="elevation-1"
                            hide-default-footer
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col md="10" sm="12">
                                        <v-text-field v-model="usersSearch" :color="themeColor"
                                                      :label="langMap.main.search"
                                                      class="mx-4" @input="getUsers"></v-text-field>
                                    </v-col>
                                    <v-col md="2" sm="12">
                                        <v-select
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            class="mx-4"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:footer>
                                <v-pagination v-model="options.page"
                                              :color="themeColor"
                                              :length="lastPage"
                                              :page="options.page"
                                              :total-visible="5"
                                              circle
                                >
                                </v-pagination>
                            </template>
                            <template v-slot:item.id="{ item }">
                                <div v-if="item" class="justify-center">
                                    {{ item.id }}
                                </div>
                            </template>
                            <template v-slot:item.contact_email="{item}">
                                <span v-if="item.contact_email">
                                    <v-icon v-if="item.contact_email.type"
                                        :title="localized(item.contact_email.type)" dense
                                        x-small
                                        v-text="item.contact_email.type.icon">
                                    </v-icon>
                                    {{ item.contact_email.email }}
                                </span>
                            </template>
                            <template v-slot:item.notifications_status="{ item }">
                                <v-icon v-if="item.notifications_status && item.notifications_status.length > 0 && item.notifications_status.includes(9)" @click="updateStatus(item)"
                                        class="justify-center"
                                        :title="langMap.notifications_settings.status_all"
                                        dense
                                >
                                    mdi-checkbox-multiple-marked-circle-outline
                                </v-icon>
                                <v-icon v-else-if="item.notifications_status && item.notifications_status.length > 0" @click="updateStatus(item)"
                                        class="justify-center"
                                        :title="langMap.notifications_settings.status_some"
                                        dense
                                >
                                    mdi-checkbox-multiple-blank-circle-outline
                                </v-icon>
                                <v-icon v-else @click="updateStatus(item)"
                                        class="justify-center"
                                        :title="langMap.notifications_settings.status_none"
                                        dense
                                >
                                    mdi-cancel
                                </v-icon>
                            </template>
                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>

        <v-row justify="center">
            <v-dialog v-model="updateNotificationsDlg" max-width="800px" persistent>
                <v-card>
                    <v-card-title>
                        <span class="headline">{{ updateUser.full_name }}: {{ langMap.notifications_settings.update_notifications_settings }}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="6">
                                    <v-checkbox
                                        :color="themeColor"
                                        v-model="updateUser.notifications_status"
                                        :value="101"
                                        :label="langMap.notifications_settings.new_assigned_to_me"
                                        dense
                                    >
                                    </v-checkbox>

                                    <v-checkbox
                                        :color="themeColor"
                                        v-model="updateUser.notifications_status"
                                        :value="201"
                                        :label="langMap.notifications_settings.new_assigned_to_team"
                                        dense
                                    >
                                    </v-checkbox>

                                    <v-checkbox
                                        :color="themeColor"
                                        v-model="updateUser.notifications_status"
                                        :value="301"
                                        :label="langMap.notifications_settings.new_assigned_to_company"
                                        dense
                                    >
                                    </v-checkbox>
                                </v-col>
                                <v-col cols="6">
                                    <v-checkbox
                                        :color="themeColor"
                                        v-model="updateUser.notification_status"
                                        :value="102"
                                        :label="langMap.notifications_settings.update_assigned_to_me"
                                        dense
                                    >
                                    </v-checkbox>

                                    <v-checkbox
                                        :color="themeColor"
                                        v-model="updateUser.notifications_status"
                                        :value="202"
                                        :label="langMap.notifications_settings.update_assigned_to_team"
                                        dense
                                    >
                                    </v-checkbox>

                                    <v-checkbox
                                        :color="themeColor"
                                        v-model="updateUser.notifications_status"
                                        :value="302"
                                        :label="langMap.notifications_settings.update_assigned_to_company"
                                        dense
                                    >
                                    </v-checkbox>
                                </v-col>
                            </v-row>
                            <v-row>
                                <v-col cols="12">
                                    <v-checkbox
                                        :color="themeColor"
                                        v-model="updateUser.notifications_status"
                                        :value="103"
                                        :label="langMap.notifications_settings.client_response_assigned_to_me"
                                        dense
                                    >
                                    </v-checkbox>

                                    <v-checkbox
                                        :color="themeColor"
                                        v-model="updateUser.notifications_status"
                                        :value="9"
                                        :label="langMap.notifications_settings.all"
                                        dense
                                    >
                                    </v-checkbox>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>

                        <v-btn color="red" text @click="updateNotificationsDlg=false">{{ langMap.main.cancel }}</v-btn>
                        <v-btn :color="themeColor" text
                               @click="updateNotificationsDlg=false; updateNotifications()">
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
            snackbar: false,
            actionColor: '',
            themeColor: this.$store.state.themeColor,
            snackbarMessage: '',
            totalUsers: 0,
            lastPage: 0,
            loading: this.themeColor,
            expanded: [],
            isLoading: false,
            options: {
                page: 1,
                sortDesc: [false],
                sortBy: ['id']
            },
            footerProps: {
                itemsPerPage: 10,
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            langMap: this.$store.state.lang.lang_map,
            headers: [
                {text: '', value: 'data-table-expand'},
                {
                    text: 'ID',
                    align: 'start',
                    value: 'id',
                },
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name'},
                {text: `${this.$store.state.lang.lang_map.main.last_name}`, value: 'surname'},
                {text: `${this.$store.state.lang.lang_map.main.email}`, value: 'contact_email'},
                {text: `${this.$store.state.lang.lang_map.main.status}`, value: 'notifications_status'}
            ],
            usersSearch: '',
            users: [],
            updateUser: {
                id: '',
                notifications_status: []
            },
            updateNotificationsDlg: false
        }
    },
    mounted() {
        this.getUsers();
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
        getUsers() {
            this.loading = this.themeColor
            // console.log(this.options);
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id'
                this.options.sortDesc[0] = false
            }
            if (this.totalUsers < this.options.itemsPerPage) {
                this.options.page = 1
            }
            axios.get('/api/main_company_settings/notify', {
                params: {
                    search: this.usersSearch,
                    sort_by: this.options.sortBy[0],
                    sort_val: this.options.sortDesc[0],
                    per_page: this.options.itemsPerPage,
                    page: this.options.page
                }
            }).then(
                response => {
                    this.loading = false
                    response = response.data
                    if (response.success === true) {
                        this.users = response.data.data
                        this.totalUsers = response.data.total
                        this.lastPage = response.data.last_page
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.snackbar = true;
                    }
                });
        },
        updateStatus(user) {
            this.updateUser = user;
            this.updateForm = user.notifications_status;
            this.updateNotificationsDlg = true;
        },
        updateNotifications() {
            axios.post('/api/main_company_settings/notify', {
                user_id: this.updateUser.id,
                notifications_status: this.updateUser.notifications_status
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getUsers()
                    this.snackbarMessage = this.langMap.notifications_settings.updated
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.getUsers()
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.snackbar = true;
                }

            });
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value
            this.options.page = 1
        }
    },
    watch: {
        options: {
            handler() {
                this.getUsers()
            },
            deep: true
        },
    },
}
</script>
