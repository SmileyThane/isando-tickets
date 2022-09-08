<template>
    <div>
        <v-row
            v-if="$store.getters['IncidentReporting/getIsEditable']"
        >
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
                :placeholder="langMap.main.actions"
                required
            ></v-select>
        </v-row>
        <v-row v-else>
            <v-col cols="5">{{ langMap.main.name }}</v-col>
            <v-col cols="3">Deadline</v-col>
            <v-col cols="3">Assigned to</v-col>
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
                                    <span class="text-overline mb-4">{{ action.name }}</span>
                                    <br>
                                    <small>{{ action.description }}</small>
                                </v-col>
                                <v-col cols="3">
                                    {{ action.deadline_time_indicator }} {{ action.deadline_time_value }} {{ action.deadline_time_parameter }}
                                </v-col>
                                <v-col cols="3">
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
                                                <v-list-item-title>item 1</v-list-item-title>
                                            </v-list-item>
                                            <v-list-item>
                                                <v-list-item-title>item 2</v-list-item-title>
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
        }
    }
}
</script>
