<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>

        <v-row>
            <v-col cols="12">
                <IncidentSearch />
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="2">
                <IncidentCategoryItem
                    v-for="item in $store.getters['IncidentReporting/getIR']"
                    :key="item.id"
                    :extended="true"
                    :item="item"
                    :selected="item.id === $store.getters['IncidentReporting/getSelectedIR'].id"
                />
            </v-col>
            <v-col cols="10">
                <div class="text-h6">Action board: Assessment of damage to physical facilities</div>
                <v-tabs v-model="tab" :color="themeBgColor">
                    <v-tab>General</v-tab>
                    <v-tab>Action boards</v-tab>
                    <v-tab>Version</v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab">
                    <v-tab-item>
                        <IncidentTabGeneral />
                    </v-tab-item>
                    <v-tab-item>
                        <IncidentTabActionBoards />
                    </v-tab-item>
                    <v-tab-item>
                        <IncidentTabVersion />
                    </v-tab-item>
                </v-tabs-items>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import IncidentCategoryItem from './components/category-item'
import IncidentSearch from './components/search'
import IncidentTabGeneral from './components/tab-general'
import IncidentTabActionBoards from './components/tab-action-boards'
import IncidentTabVersion from './components/tab-version'
import EventBus from "../../components/EventBus";

export default {
    name: 'incident_reporting_action_boards',
    components: {
        IncidentCategoryItem,
        IncidentSearch,
        IncidentTabGeneral,
        IncidentTabActionBoards,
        IncidentTabVersion,
    },
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            tab: 0,
        }
    },
    mounted() {
        const that = this
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.$store.dispatch('IncidentReporting/callGetIR');
    },
    methods: {
    }
}
</script>

<style scoped>
*,
>>> .v-input,
>>> label,
>>> .v-btn__content {
    font-size: 12px;
}
.heading {
    font-size: 16px;
}
.clearfix {
    clear: both;
}
</style>
