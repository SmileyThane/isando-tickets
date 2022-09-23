<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>

        <v-row>
            <v-col cols="12">
                <IncidentSearch/>
            </v-col>
        </v-row>

        <v-row v-if="$store.getters['IncidentReporting/getSelectedIR']">
            <v-col cols="4">
                <IncidentCategoryItem
                    v-for="item in $store.getters['IncidentReporting/getIR']"
                    :key="item.id"
                    :extended="true"
                    :item="item"
                    :selected="item.id === $store.getters['IncidentReporting/getSelectedIR'].id"
                />
            </v-col>
            <v-col cols="8">
                <div class="text-h6">
                    {{ $store.getters['IncidentReporting/getSelectedIR'].name }}
                    <v-menu v-if="!$store.getters['IncidentReporting/getIsEditable']" bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn icon v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-item link @click.prevent="setIsEditable">
                                <v-list-item-title>{{ langMap.main.edit }}</v-list-item-title>
                                <v-list-item-action>
                                    <v-icon :color="themeBgColor">mdi-pencil</v-icon>
                                </v-list-item-action>
                            </v-list-item>
                            <v-list-item link
                                         @click.prevent="$store.dispatch('IncidentReporting/callDeleteIR', $store.getters['IncidentReporting/getSelectedIR'].id)">
                                <v-list-item-title>{{ langMap.main.delete }}</v-list-item-title>
                                <v-list-item-action>
                                    <v-icon :color="themeBgColor">mdi-delete</v-icon>
                                </v-list-item-action>
                            </v-list-item>
                            <v-list-item link @click.prevent="createIRAction">
                                <v-list-item-title>{{ langMap.main.action }}</v-list-item-title>
                                <v-list-item-action>
                                    <v-icon :color="themeBgColor">mdi-plus-outline</v-icon>
                                </v-list-item-action>
                            </v-list-item>

                        </v-list>
                    </v-menu>
                    <div v-if="$store.getters['IncidentReporting/getIsEditable']">
                        <v-btn
                            text
                            @click="cancelEditing"
                        >
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn
                            :color="themeBgColor"
                            text
                            @click="saveIR(false)"
                        >
                            {{ langMap.main.save }}
                        </v-btn>
                        <v-btn
                            :color="themeBgColor"
                            text
                            @click="saveIR(true)"
                        >
                            {{ langMap.ir.ab.clone }}
                        </v-btn>
                    </div>
                </div>

                <v-tabs v-model="tab" :color="themeBgColor">
                    <v-tab>{{langMap.ir.ab.general}}</v-tab>
                    <v-tab>{{langMap.ir.ab.actions}}</v-tab>
                    <v-tab>{{langMap.ir.ab.version}}</v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab">
                    <v-tab-item>
                        <IncidentTabGeneral/>
                    </v-tab-item>
                    <v-tab-item>
                        <IncidentTabActionBoards/>
                    </v-tab-item>
                    <v-tab-item>
                        <IncidentTabVersion/>
                    </v-tab-item>
                </v-tabs-items>
            </v-col>
        </v-row>
        <v-dialog v-model="$store.getters['IncidentReporting/getManageActionDlg']" max-width="480">
            <v-card dense outlined>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.ir.ab.action }}
                </v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="$store.getters['IncidentReporting/getSelectedIRAction'].name"
                        :color="themeBgColor"
                        :label="langMap.main.name"
                        prepend-icon="mdi-text"
                        required
                        type="text"
                    ></v-text-field>
                    <br/>
                    <v-textarea
                        v-model="$store.getters['IncidentReporting/getSelectedIRAction'].description"
                        :color="themeBgColor"
                        :label="langMap.main.description"
                        prepend-icon="mdi-text"
                        required
                    ></v-textarea>
                    <br/>
                    <v-label>
                        {{langMap.ir.ab.deadline_time_value}}:
                    </v-label>
                    <br/>
                    <div class="row mt-3">
                        <v-select
                            v-model="$store.getters['IncidentReporting/getSelectedIRAction'].deadline_time_indicator"
                            :items="$store.getters['IncidentReporting/getIROptions'].actions.deadline_time_indicators"
                            class="small col-4"
                            prepend-icon="mdi-clock"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            dense
                            hide-details
                        ></v-select>
                        <v-text-field
                            v-model="$store.getters['IncidentReporting/getSelectedIRAction'].deadline_time_value"
                            :color="themeBgColor"
                            class="col-4 pt-0"
                            required
                            :label="langMap.ir.ab.select"
                            type="text"
                        ></v-text-field>
                        <v-select
                            v-model="$store.getters['IncidentReporting/getSelectedIRAction'].deadline_time_parameter"
                            :items="$store.getters['IncidentReporting/getIROptions'].actions.deadline_time_parameters"
                            class="small col-4"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            :label="langMap.ir.ab.unit"
                            dense
                            hide-details
                        ></v-select>
                    </div>
                    <br/>
                    <v-label>
                        {{langMap.ir.ab.action_type}}:
                    </v-label>
                    <v-select
                        v-model="$store.getters['IncidentReporting/getSelectedIRAction'].type_id"
                        :items="$store.getters['IncidentReporting/getIROptions'].actions.types"
                        item-text="name"
                        item-value="id"
                        class="small"
                        prepend-icon="mdi-list-status"
                        :color="themeBgColor"
                        :item-color="themeBgColor"
                        dense
                        hide-details
                    ></v-select>
                    <br/>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="cancelActionEditing">{{ langMap.main.cancel }}
                    </v-btn>
                    <v-btn
                        :color="themeBgColor"
                        text
                        @click="saveIRAction()"
                    >
                        {{ langMap.main.save }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
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
        this.$store.dispatch('IncidentReporting/callGetIROptions');
        this.$store.dispatch('IncidentReporting/callGetIRActions');
    },
    methods: {
        setIsEditable() {
            this.$store.dispatch('IncidentReporting/callSetIsEditable', true)
        },
        cancelEditing() {
            this.$store.dispatch('IncidentReporting/callSetIsEditable', false).then(() => {
                this.$store.dispatch('IncidentReporting/callGetIR')
            })
        },
        saveIR(incrementVersion) {
            this.$store.dispatch('IncidentReporting/callStoreIR', incrementVersion)

        },
        createIRAction() {
            this.$store.dispatch('IncidentReporting/callSetManageActionDlg', true)
        },
        saveIRAction() {
            this.$store.dispatch('IncidentReporting/callStoreIRAction')
        },
        cancelActionEditing() {
            this.$store.dispatch('IncidentReporting/callSetManageActionDlg', false)
        },


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

>>> .v-text-field,
>>> input {
    padding: 0 5px 10px 5px !important;
}
</style>
