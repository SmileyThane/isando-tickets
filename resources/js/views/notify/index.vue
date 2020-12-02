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
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header @click.native.stop="addNotification()">
                                    {{langMap.company.new_notification_template}}
                                    <template v-slot:actions>
                                        <v-icon color="submit" @click="addNotification()">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-data-table
                            :headers="headers"
                            :items="notifications"
                            :options.sync="options"
                            :server-items-length="totalNotifications"
                            :loading="loading"
                            :footer-props="footerProps"
                            class="elevation-1"
                            hide-default-footer
                            :loading-text="langMap.main.loading"
                            @click:row="showNotification"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col sm="12" md="10">
                                       &nbsp;
                                    </v-col>
                                    <v-col sm="12" md="2">
                                        <v-select
                                            class="mx-4"
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:item.type="{item}">
                                <v-icon v-if="item.type" left :title="localized(item.type)" v-text="item.type.icon"></v-icon>
                                <span v-if="item.type">{{localized(item.type)}}</span>
                            </template>
                            <template v-slot:item.action="{item}">
                                <v-icon small :title="langMap.main.delete" @click.native.stop="removeNotification(item)">mdi-delete</v-icon>
                            </template>
                            <template v-slot:footer>
                                <v-pagination :color="themeColor"
                                              v-model="options.page"
                                              :length="lastPage"
                                              circle
                                              :page="options.page"
                                              :total-visible="5"
                                >
                                </v-pagination>
                            </template>
                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
        <template>
            <v-dialog v-model="removeNotificationDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.delete_selected}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeNotificationDialog = false">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="removeNotificationDialog = false; deleteNotification(selectedNotificationId)">
                            {{langMap.main.delete}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
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
            totalNotifications: 0,
            lastPage: 0,
            loading: this.themeColor,
            langMap: this.$store.state.lang.lang_map,
            options: {
                page: 1,
                sortDesc: [false],
                sortBy: ['name']
            },
            footerProps: {
                itemsPerPage: 10,
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            headers: [
                {text: 'ID', align: 'end', value: 'id'},
                {text: this.$store.state.lang.lang_map.main.type, value: 'type'},
                {text: this.$store.state.lang.lang_map.main.name, value: 'name'},
                {text: this.$store.state.lang.lang_map.main.description, value: 'description'},
                {text: this.$store.state.lang.lang_map.main.action, value: 'action', sortable: false}
            ],
            notifications: [],
            removeNotificationDialog: false,
            selectedNotificationId: null
        }
    },
    mounted() {
        this.getNotifications();

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
        getNotifications() {
            this.loading = this.themeColor
            // console.log(this.options);
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id'
                this.options.sortDesc[0] = false
            }
            if (this.totalNotifications < this.options.itemsPerPage) {
                this.options.page = 1
            }
            axios.get('api/notifications', {
                params: {
                    sort_by: this.options.sortBy[0],
                    sort_val: this.options.sortDesc[0],
                    per_page: this.options.itemsPerPage,
                    page: this.options.page
                }
            }).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.notifications = response.data.data
                        this.totalNotifications = response.data.total
                        this.lastPage = response.data.last_page
                        this.loading = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        deleteNotification(id) {
            axios.delete(`/api/notification/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getClients()
                    this.snackbarMessage = this.langMap.company.notification_template_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.selectedNotificationId = null
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        removeNotification(item) {
            this.selectedNotificationId = item.id;
            this.removeNotificationDialog = true;
            this.getNotifications();
        },
        showNotification(item) {
            this.$router.push(`/notify/${item.id}`);
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value
            this.options.page = 1
        },
        addNotification() {
            this.$router.push('/notify/create');
        }
    },
    watch: {
        options: {
            handler() {
                this.getNotifications()
            },
            deep: true,
        }
    }
}
</script>
