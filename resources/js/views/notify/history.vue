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
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="notifications"
                            :loading="loading"
                            :loading-text="langMap.main.loading"
                            :options.sync="options"
                            :server-items-length="totalNotifications"
                            class="elevation-1"
                            hide-default-footer
                            @click:row="showNotification"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col md="10" sm="12">
                                        &nbsp;
                                    </v-col>
                                    <v-col md="2" sm="12">
                                        <v-select
                                            v-model="options.itemsPerPage"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            class="mx-4"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:item.type="{item}">
                                <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)" left
                                        v-text="item.type.icon"></v-icon>
                                <span v-if="item.type">{{ $helpers.i18n.localized(item.type) }}</span>
                            </template>
                            <template v-slot:item.sender="{item}">
                                {{item.sender.full_name}}
                            </template>
                            <template v-slot:item.created_at="{item}">
                                {{moment(item.created_at).format('YYYY-MM-DD hh:mm') }}
                            </template>
                            <template v-slot:footer>
                                <v-pagination :color="themeBgColor"
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
            themeFgColor: this.$store.state.themeFgColor,
themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            totalNotifications: 0,
            lastPage: 0,
            loading: this.themeBgColor,
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
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
       EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        getNotifications() {
            this.loading = this.themeBgColor
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
