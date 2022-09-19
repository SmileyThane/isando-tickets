<template>
    <div>
        <div
            class="mx-auto mt-2"
            v-if="$store.getters['IncidentReporting/getIsEditable']"
        >
            <br/>
            <h2>
                {{langMap.ir.ab.select_actions}}:
            </h2>
            <br/>
            <v-select
                v-model="$store.getters['IncidentReporting/getSelectedIR'].actions"
                :items="$store.getters['IncidentReporting/getIRActions']"
                class=""
                dense
                hide-details
                item-text="name"
                item-value="id"
                multiple
                outlined
                :color="themeBgColor"
                :item-color="themeBgColor"
                :placeholder="langMap.main.actions"
                required
            ></v-select>
        </div>
        <v-row v-else>
            <v-col cols="5">{{ langMap.main.name }}</v-col>
            <v-col cols="2">{{ langMap.ir.ab.deadline_time_value}}</v-col>
            <v-col cols="4">{{ langMap.main.customer }}</v-col>
            <v-col cols="1"></v-col>

            <v-col cols="12">
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
                                    <v-btn small text href="/settings/incident">
                                        {{ action.type ? action.type.name : '' }}
                                    </v-btn>
                                </v-col>
                                <v-col cols=2>
                                    {{ action.deadline_time_indicator }} {{ action.deadline_time_value }} {{ action.deadline_time_parameter }}
                                </v-col>
                                <v-col cols="4">
                                    {{ action.assignee ? action.assignee.email : 'not assinged' }}
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
                                                <v-list-item-title>{{langMap.main.delete}}</v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </v-col>
                            </v-row>
                        </v-list-item-content>
                    </v-list-item>
                </v-card>
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
