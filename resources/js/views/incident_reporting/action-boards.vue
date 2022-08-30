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

        <v-row v-if="$store.getters['IncidentReporting/getSelectedIR']">
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
                <div class="text-h6">
                    {{$store.getters['IncidentReporting/getSelectedIR'].name}}
                    <v-menu v-if="!$store.getters['IncidentReporting/getIsEditable']" bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn v-on="on" icon>
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-item link @click.prevent="setIsEditable">
                                <v-list-item-title >{{ langMap.main.edit }}</v-list-item-title>
                                <v-list-item-action>
                                    <v-icon :color="themeBgColor">mdi-pencil</v-icon>
                                </v-list-item-action>
                            </v-list-item>
                            <v-list-item link @click.prevent="$store.dispatch('IncidentReporting/callDeleteIR', $store.getters['IncidentReporting/getSelectedIR'].id)">
                                <v-list-item-title >{{ langMap.main.delete }}</v-list-item-title>
                                <v-list-item-action>
                                    <v-icon :color="themeBgColor">mdi-delete</v-icon>
                                </v-list-item-action>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </div>

                <v-tabs v-model="tab" :color="themeBgColor">
                    <v-tab>General</v-tab>
                    <v-tab>Actions</v-tab>
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
        setIsEditable() {
            this.$store.dispatch('IncidentReporting/callSetIsEditable', true)
        }
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
