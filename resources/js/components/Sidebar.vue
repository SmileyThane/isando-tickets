<template>
    <v-navigation-drawer
        v-model="drawer"
        :mini-variant.sync="localDrawer"
        app
    >
        <perfect-scrollbar style="height: 100%;">
            <v-list-item v-if="this.navbarStyle === 2 && this.companyLogo">
                <v-list-item-icon style="margin-right: 16px!important;">
                    <v-img :src="companyLogo" contain max-height="3em" max-width="30"></v-img>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title class="title">
                        {{ firstAlias }}
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-list-item v-else-if="this.navbarStyle === 3 && this.companyLogo">
                <v-list-item-icon style="margin-right: 16px!important;">
                    <v-img :src="companyLogo" contain max-height="3em" max-width="30"></v-img>
                </v-list-item-icon>
                <v-list-item-content>
                    <v-list-item-title class="title">
                        {{ firstAlias }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                        {{ secondAlias }}
                    </v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>
            <v-list-item v-else-if="this.navbarStyle === 4 && this.companyLogo">
                <v-list-item-content>
                    <v-list-item-icon>
                        <v-img :src="companyLogo" contain></v-img>
                    </v-list-item-icon>
                </v-list-item-content>
            </v-list-item>
            <v-list-item v-else>
                <v-list-item-content>
                    <v-list-item-title class="title">
                        {{ firstAlias }}
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>
            <v-divider>&nbsp;</v-divider>
            <v-list dense>
                <v-list-item
                    v-show="localDrawer"
                    style="background-color: white;"
                    @click.stop="localDrawer = !localDrawer"
                >
                    <v-list-item-action>
                        <v-icon> mdi-menu</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title></v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    style="background-color: white;"
                    dense
                    link to="/home">
                    <v-list-item-action>
                        <v-icon>mdi-home</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>{{ langMap.sidebar.home }}</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
            <v-divider></v-divider>
            <v-list dense>
                <v-list-item
                    style="background-color: white;"
                    dense
                    link
                    to="/knowledge_base">
                    <v-list-item-action>
                        <v-icon>mdi-book-open-page-variant</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{ langMap.sidebar.knowledge_base }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
            <v-divider></v-divider>
            <v-list dense>
                <v-list-item
                    v-if="$helpers.auth.checkPermissionByIds([25])"
                    style="background-color: white;"
                    dense
                    link
                    to="/custom_license">
                    <v-list-item-action>
                        <v-icon>mdi-license</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{ langMap.sidebar.custom_licenses }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
                <v-list-item
                    v-if="$helpers.auth.checkPermissionByIds([25])"
                    style="background-color: white;"
                    dense
                    link
                    to="/custom_license_unassigned">
                    <v-list-item-action>
                        <v-icon>mdi-cellphone-erase</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>
                            {{ langMap.sidebar.custom_licenses }}
                        </v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
            <v-divider></v-divider>
            <v-list dense>
                <v-list-group
                    v-if="$helpers.auth.checkPermissionByIds([7])"
                    :style="'background-color: ' + 'white' + ';'"
                    :value="sidebarGroups"
                    color="#757575"
                    multiple
                    prepend-icon="mdi-badge-account-horizontal-outline"
                >
                    <template
                        v-slot:activator
                    >
                        <v-list-item-content>
                            <v-list-item-title>{{ customers }} - CRM</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([7, 10])"
                        color="#757575"
                        link
                        style="background-color:white;"
                        to="/all"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-contacts-outline</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.all }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([7])"
                        color="#757575"
                        link
                        style="background-color:white;"
                        to="/customer"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-factory</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.customers }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([10])"
                        color="#757575"
                        link
                        style="background-color:white;"
                        to="/employee"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-account</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.individuals }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([19])"
                        color="#757575"
                        link
                        style="background-color:white;"
                        to="/product"
                    >
                        <v-list-item-action>
                            <v-icon> mdi-monitor-clean</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.products }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>
            </v-list>
            <v-divider
                v-if="$helpers.auth.checkPermissionByIds([1])"
            >&nbsp;
            </v-divider>
            <v-list dense>
                <v-list-group
                    v-if="$helpers.auth.checkPermissionByIds([1])"
                    :style="'background-color: ' + 'white' + ';'"
                    :value="sidebarGroups"
                    color="#757575"
                    multiple
                    prepend-icon="mdi-ticket-account"
                >
                    <template
                        v-slot:activator
                    >
                        <v-list-item-content>
                            <v-list-item-title>{{ ticket }}</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([1])"
                        color="#757575" link
                        style="background-color:white;"
                        to="/tickets"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-format-list-numbered</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.ticket_list }}
                                {{ langMap.sidebar.list }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([2])"
                        color="#757575" link
                        style="background-color:white;"
                        to="/ticket_create"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-shape-rectangle-plus</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.create_ticket }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>
            </v-list>
            <v-divider></v-divider>
            <v-list dense v-if="hasLicense && $helpers.auth.checkPermissionByIds([40,41,42,51,52,54,55,61,66])">
                <v-list-group
                    v-if="hasLicense"
                    :style="'background-color: ' + 'white' + ';'"
                    :value="sidebarGroups"
                    color="#757575"
                    multiple
                    prepend-icon="mdi-alarm"
                >
                    <template
                        v-slot:activator
                    >
                        <v-list-item-content>
                            <v-list-item-title>{{ timeTracking }}</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                        v-if="hasLicense && $helpers.auth.checkPermissionByIds([51])"
                        link
                        style="background-color:white;"
                        to="/tracking/dashboard"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-view-dashboard-outline</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.tracking_dashboard }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="hasLicense && $helpers.auth.checkPermissionByIds([40,41,42])"
                        link
                        style="background-color:white;"
                        to="/tracking/tracker"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-alarm</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.tracking_tracker }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="hasLicense && enableTimesheet"
                        link
                        style="background-color:white;"
                        to="/tracking/timesheet"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-file-clock-outline</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.tracking_timesheet }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="hasLicense && $helpers.auth.checkPermissionByIds([52])"
                        link
                        style="background-color:white;"
                        to="/tracking/calendar"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-calendar</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.tracking_calendar }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="hasLicense && $helpers.auth.checkPermissionByIds([54,55])"
                        link
                        style="background-color:white;"
                        to="/tracking/projects"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-folder-account-outline</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.tracking_projects }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="hasLicense && $helpers.auth.checkPermissionByIds([61,66])"
                        link
                        style="background-color:white;"
                        to="/tracking/reports"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-chart-areaspline</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.tracking_reports }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="hasLicense && $helpers.auth.checkPermissionByIds([75,79])"
                        link
                        style="background-color:white;"
                        to="/tracking/settings"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-tune-vertical</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.tracking_settings }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>
            </v-list>
            <v-divider></v-divider>
            <v-list dense>
                <v-list-group
                    :style="'background-color: ' + 'white' + ';'"
                    :value="sidebarGroups"
                    color="#757575"
                    multiple
                    prepend-icon="mdi-email-alert-outline"
                >
                    <template
                        v-slot:activator
                    >
                        <v-list-item-content>
                            <v-list-item-title>{{ notifications }}</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                        v-if="!$helpers.auth.checkPermissionByIds([36])"
                        link
                        style="background-color:white;"
                        to="/notify"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-email-send-outline</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.notify_customers }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        link
                        style="background-color:white;"
                        to="/notify_history"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-history</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.notifications_history }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>
            </v-list>
            <v-divider>&nbsp;</v-divider>
            <v-list dense>
                <v-list-group
                    :style="'background-color: ' + 'white' + ';'"
                    :value="sidebarGroups"
                    color="#757575"
                    multiple
                    prepend-icon="mdi-cog"

                >
                    <template
                        v-slot:activator
                    >
                        <v-list-item-content
                            :color="'white'"
                        >
                            <v-list-item-title>{{ settings }}</v-list-item-title>
                        </v-list-item-content>
                    </template>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([7, 13])"
                        link
                        style="background-color:white;"
                        to="/company">
                        <v-list-item-action>
                            <v-icon>mdi-office-building</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.companies }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([31])"
                        link
                        style="background-color:white;"
                        to="/team"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-account-box-multiple-outline</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.teams }}</v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                        v-if="$helpers.auth.checkPermissionByIds([13])"
                        link
                        style="background-color:white;"
                        to="/settings/system"
                    >
                        <v-list-item-action>
                            <v-icon>mdi-folder-cog-outline</v-icon>
                        </v-list-item-action>
                        <v-list-item-content>
                            <v-list-item-title>{{ langMap.sidebar.system_settings }}
                            </v-list-item-title>
                        </v-list-item-content>
                    </v-list-item>
                </v-list-group>
            </v-list>
        </perfect-scrollbar>
    </v-navigation-drawer>
</template>

<style scoped>
>>> .v-navigation-drawer__content {
    overflow-y: hidden !important;
}
</style>


<script>
import EventBus from './EventBus.vue';

export default {
    name: "Sidebar",
    props: {value: {type: Boolean}},
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            firstAlias: '',
            secondAlias: '',
            companyLogo: '',
            navbarStyle: 1,
            localDrawer: null,
            drawer: true,
            show: true,
            ticket: '',
            customers: '',
            settings: '',
            notifications: '',
            timeTracking: '',
            sidebarGroups: []
        }
    },
    watch: {
        value: function () {
            this.localDrawer = this.value
        },
        localDrawer: function () {
            this.$emit('input', this.localDrawer)
        },
        $route(to, from) {
            this.changeAppTitle();
        }
    },
    mounted() {
        this.getCompanyName();
        this.getCompanyLogo();
        this.getCompanySettings();
        this.getTrackingSettings();

        this.ticket = this.langMap.sidebar.ticket;
        this.customers = this.langMap.sidebar.customers
        this.notifications = this.langMap.sidebar.notifications
        this.settings = this.langMap.sidebar.settings
        this.timeTracking = this.langMap.sidebar.time_tracking

        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        EventBus.$on('update-navbar-style', function (style) {
            that.navbarStyle = style;
        });
        EventBus.$on('update-navbar-logo', function (logo) {
            that.companyLogo = logo;
        });
    },
    methods: {
        getCompanyName() {
            axios.get(`/api/main_company/name`)
                .then(
                    response => {
                        this.firstAlias = response.data.data.first_alias
                        this.secondAlias = response.data.data.second_alias
                        this.changeAppTitle();
                    });
        },
        getCompanyLogo() {
            axios.get(`/api/main_company/logo`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.companyLogo = response.data;
                }
            });
        },
        getCompanySettings() {
            axios.get(`/api/main_company/settings`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.navbarStyle = response.data.hasOwnProperty('navbar_style') ? response.data.navbar_style : 1;
                }
            });
        },
        changeAppTitle() {
            this.$store.state.pageName = this.langMap.sidebar[this.$route.name]
            document.title = this.firstAlias + ' | ' + this.langMap.sidebar[this.$route.name]
        },
        getTrackingSettings() {
            return this.$store.dispatch('Tracking/getSettings');
        }
    },
    computed: {
        hasLicense() {
            const company = this.$store.getters['getMainCompany'];
            return company ? company.license : null;
        },
        enableTimesheet() {
            return this.$store.getters['Tracking/getSettings'].settings && this.$store.getters['Tracking/getSettings'].settings.enableTimesheet;
        }
    }
}
</script>
