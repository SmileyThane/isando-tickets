<template>
    <div>
        <div
            v-if="$store.getters['IncidentReporting/getIsEditable']"
            class="mx-auto mt-2"
        >
            <br/>
            <h2 v-if="$store.getters['IncidentReporting/getIRType'] === 1">
                {{ langMap.ir.ab.select_actions }}:
            </h2>
            <h2 v-if="$store.getters['IncidentReporting/getIRType'] === 2">
                {{ langMap.sidebar.action_boards }}:
            </h2>
            <br/>
            <v-select
                v-model="$store.getters['IncidentReporting/getSelectedIR'].actions"
                :color="themeBgColor"
                :item-color="themeBgColor"
                :items="$store.getters['IncidentReporting/getIRActions']"
                :placeholder="langMap.main.actions"
                class=""
                dense
                hide-details
                item-text="name"
                item-value="id"
                multiple
                outlined
                required
            ></v-select>
        </div>
        <v-row v-else>
            <v-col cols="5">{{ langMap.main.name }}</v-col>
            <v-col v-if="$store.getters['IncidentReporting/getIRType'] === 1" cols="2">
                {{
                    langMap.ir.ab.deadline_time_value
                }}
            </v-col>
            <v-col v-if="$store.getters['IncidentReporting/getIRType'] === 2" cols="4">
                {{
                    langMap.ir.ab.impact_potentials
                }}
            </v-col>

            <v-col v-if="$store.getters['IncidentReporting/getIRType'] === 1" cols="4">
                {{ langMap.main.customer }}
            </v-col>
            <v-col v-if="$store.getters['IncidentReporting/getIRType'] === 2" cols="2">
                {{ langMap.main.version }}
            </v-col>
            <v-col cols="1"></v-col>

            <v-col cols="12">
                <div v-if="$store.getters['IncidentReporting/getIRType'] === 1">
                    <v-card
                        v-for="action in $store.getters['IncidentReporting/getSelectedIR'].actions"
                        :key="action.id"
                        class="mx-auto mb-2"
                        outlined
                    >
                        <v-list-item three-line>
                            <v-list-item-content>
                                <v-row>
                                    <v-col cols="5">
                                        <span class="subtitle-1 mb-4 pl-3">{{ action.name }}</span>
                                        <br>
                                        <v-list-item-title class="pl-3">{{ action.description }}</v-list-item-title>
                                        <br>
                                        <v-btn href="/settings/incident" small text>
                                            {{ action.type ? action.type.name : '' }}
                                        </v-btn>
                                    </v-col>
                                    <v-col cols=2>
                                        {{ action.deadline_time_indicator }} {{ action.deadline_time_value }}
                                        {{ action.deadline_time_parameter }}
                                    </v-col>
                                    <v-col cols="4">
                                        {{
                                            action.assignee && action.assignee.user_data ? action.assignee.user_data.email : 'not assinged'
                                        }}
                                    </v-col>
                                    <v-col class="text-right" cols="1">
                                        <v-menu
                                            bottom
                                            left
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-btn
                                                    icon
                                                    v-bind="attrs"
                                                    v-on="on"
                                                >
                                                    <v-icon>mdi-dots-vertical</v-icon>
                                                </v-btn>
                                            </template>
                                            <v-list>
                                                <v-list-item>
                                                    <v-list-item-title>{{ langMap.main.delete }}</v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </v-col>
                                </v-row>
                            </v-list-item-content>
                        </v-list-item>
                    </v-card>
                </div>
                <div v-if="$store.getters['IncidentReporting/getIRType'] === 2">
                    <v-card
                        v-for="action in $store.getters['IncidentReporting/getSelectedIR'].action_boards"
                        :key="action.id"
                        class="mx-auto mb-2"
                        outlined
                    >
                        <v-list-item three-line>
                            <v-list-item-content>
                                <v-row>
                                    <v-col cols="5">
                                        <span class="subtitle-1 mb-4 pl-3">{{ action.name }}</span>
                                        <br>
                                        <v-list-item-title class="pl-3">{{ action.description }}</v-list-item-title>
                                        <br>
                                        <v-btn href="/settings/incident" small text>
                                            {{ action.type ? action.type.name : '' }}
                                        </v-btn>
                                    </v-col>
                                    <v-col cols=4>
                                        <v-chip
                                            :color="action.impact_potentials ?
                                                    action.impact_potentials.color :
                                                    themeBgColor
                                            "
                                            :textColor="action.impact_potentials ?
                                                        action.impact_potentials.color :
                                                        themeBgColor
                                            "
                                            class="text-uppercase"
                                            label
                                            outlined
                                            style="margin: 5px;"
                                            x-small
                                        >
                                            {{
                                                action.impact_potentials ? action.impact_potentials.name : ''
                                            }}
                                        </v-chip>
                                    </v-col>
                                    <v-col cols="2">
                                        {{
                                            action.version
                                        }}
                                    </v-col>
                                    <v-col class="text-right" cols="1">
                                        <v-menu
                                            bottom
                                            left
                                        >
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-btn
                                                    icon
                                                    v-bind="attrs"
                                                    v-on="on"
                                                >
                                                    <v-icon>mdi-dots-vertical</v-icon>
                                                </v-btn>
                                            </template>
                                            <v-list>
                                                <v-list-item>
                                                    <v-list-item-title>{{ langMap.main.delete }}</v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </v-col>
                                </v-row>
                            </v-list-item-content>
                        </v-list-item>
                    </v-card>
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'incident-tab-action-boards',
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,

        }
    }
}
</script>
