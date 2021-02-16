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
                                            v-model="options.itemsPerPage"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:item.type="{item}">
                                <v-icon v-if="item.type" left :title="localized(item.type)" v-text="item.type.icon"></v-icon>
                                <span v-if="item.type">{{localized(item.type)}}</span>
                            </template>
                            <template v-slot:item.sender="{item}">
                                {{item.sender.full_name}}
                            </template>
                            <template v-slot:item.created_at="{item}">
                                {{moment(item.created_at).format('YYYY-MM-DD hh:mm') }}
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
                sortDesc: [true],
                sortBy: ['created_at'],
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            headers: [
                {text: 'ID', align: 'end', value: 'id'},
                {text: this.$store.state.lang.lang_map.notification.sent_at, value: 'created_at'},
                {text: this.$store.state.lang.lang_map.main.type, value: 'type'},
                {text: this.$store.state.lang.lang_map.notification.subject, value: 'subject'},
                {text: this.$store.state.lang.lang_map.notification.sender, value: 'sender'},
                {text: this.$store.state.lang.lang_map.main.action, value: 'action', sortable: false}
            ],
            notifications: []
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
            axios.get('/api/notifications/history', {
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
        showNotification(item) {
            this.$router.push(`/notify_history/${item.id}`);
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value
            localStorage.itemsPerPage = value;
            this.options.page = 1
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
