<template>
    <v-card class="my-2">
        <v-row v-if="$store.getters['IncidentReporting/getSelectedIR']">
            <v-col
                v-if="$store.getters['IncidentReporting/getIsEditable']"
                class="pb-0"
                cols="12"
            >
                <label>
                    {{ langMap.main.name }}:
                </label>
                <v-text-field
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].name"
                    :color="themeBgColor"
                    outlined
                    hide-details
                    dense
                    required
                    type="text"
                ></v-text-field>
            </v-col>
            <v-col
                class="pb-0"
                cols="12"
            >
                <label>{{ langMap.ir.ab.categories }}:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].categories"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="$store.getters['IncidentReporting/getIROptions'].categories"
                    class="small"
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    multiple
                    outlined
                    placeholder="Categories"
                    required
                ></v-select>
                <template v-else>
                <span
                    v-for="category in $store.getters['IncidentReporting/getSelectedIR'].categories"
                >
                    <v-chip
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{ category.name }}
                    </v-chip>
                </span>
                    <div v-if="$store.getters['IncidentReporting/getSelectedIR'].categories.length === 0">
                        <v-chip
                            :color="colors.notSelected"
                            :textColor="colors.notSelected"
                            class="text-uppercase"
                            label
                            outlined
                            style="margin: 5px;"
                            x-small
                        >
                            {{ langMap.ir.ab.not_selected }}
                        </v-chip>
                    </div>
                </template>
            </v-col>
            <v-col v-if="$store.getters['IncidentReporting/getSelectedIR'].clients"
                   class="pb-0" cols="12">
                <label>
                    {{ langMap.ir.ab.clients }}:
                </label>
                <v-checkbox
                    v-if="$store.getters['IncidentReporting/getSelectedIR'].clients.length > 0"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].with_child_clients"
                    :disabled="!$store.getters['IncidentReporting/getIsEditable']"
                    class="mt-0"
                    label="Include child organizations"
                >
                </v-checkbox>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].clients"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="$store.getters['RiskRepository/getClients']"
                    class=""
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    multiple
                    outlined
                    placeholder="Clients"
                    required
                ></v-select>
                <template v-else>
                <span
                    v-for="client in $store.getters['IncidentReporting/getSelectedIR'].clients"
                >
                    <v-chip
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{ client.name }}
                    </v-chip>
                </span>
                    <div v-if="$store.getters['IncidentReporting/getSelectedIR'].clients.length === 0">
                        <v-chip
                            :color="colors.notSelected"
                            :textColor="colors.notSelected"
                            class="text-uppercase"
                            label
                            outlined
                            style="margin: 5px;"
                            x-small
                        >
                            {{ langMap.ir.ab.not_selected }}
                        </v-chip>
                    </div>
                </template>
            </v-col>
            <v-col class="pb-0" cols="12">
                <label>
                    {{ langMap.ir.ab.stage_monitoring }}:
                </label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].state_id"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="$store.getters['SettingsIncident/ProcessStates/getItems']"
                    class=""
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    outlined
                    :placeholder="langMap.ir.ab.stage_monitoring"
                    required
                ></v-select>
                <div
                    v-else
                >
                    <v-chip
                        :color="getChipColor($store.getters['IncidentReporting/getSelectedIR'].stage_monitoring)"
                        :textColor="getChipColor($store.getters['IncidentReporting/getSelectedIR'].stage_monitoring)"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{
                            $store.getters['IncidentReporting/getSelectedIR'].stage_monitoring ?
                                $store.getters['IncidentReporting/getSelectedIR'].stage_monitoring.name :
                                langMap.ir.ab.not_selected
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col class="pb-0"
                   cols="12"
            >
                <label>
                    {{ langMap.ir.ab.impact_potentials }}:
                </label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].impact_potential_id"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="$store.getters['IncidentReporting/getIROptions'].impact_potentials"
                    class=""
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    outlined
                    required
                ></v-select>
                <div
                    v-else
                >
                    <v-chip
                        :color="getChipColor($store.getters['IncidentReporting/getSelectedIR'].impact_potentials)"
                        :textColor="getChipColor($store.getters['IncidentReporting/getSelectedIR'].impact_potentials)"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{
                            $store.getters['IncidentReporting/getSelectedIR'].impact_potentials ?
                                $store.getters['IncidentReporting/getSelectedIR'].impact_potentials.name :
                                langMap.ir.ab.not_selected
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col class="pb-0" cols="12">
                <label>{{ langMap.ir.ab.importance }}:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].priority_id"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="$store.getters['IncidentReporting/getIROptions'].priorities"
                    class=""
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    outlined
                    placeholder="Importance"
                    required
                ></v-select>
                <div
                    v-else
                >
                    <v-chip
                        :color="getChipColor($store.getters['IncidentReporting/getSelectedIR'].priority)"
                        :textColor="getChipColor($store.getters['IncidentReporting/getSelectedIR'].priority)"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{
                            $store.getters['IncidentReporting/getSelectedIR'].priority ?
                                $store.getters['IncidentReporting/getSelectedIR'].priority.name :
                                langMap.ir.ab.not_selected
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col class="pb-0" cols="8" lg="8" md="8" sm="12" xl="6"
                   v-if="$store.getters['IncidentReporting/getSelectedIR'].type_id === 3"
            >
                <label>{{ langMap.ir.ab.scenarios }}:</label>
                <div>
                <span
                    v-for="item in scenarios"
                >
                    <v-chip
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{ item.name }}
                    </v-chip>
                </span>
                </div>
                <div v-if="scenarios.length === 0">
                    <v-chip
                        :color="colors.notSelected"
                        :textColor="colors.notSelected"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{ langMap.ir.ab.not_selected }}
                    </v-chip>
                </div>
            </v-col>
            <v-col class="pb-0" cols="8" lg="8" md="8" sm="12" xl="6">
                <label>{{ langMap.main.description }}:</label>
                <v-textarea
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].description"
                    :color="themeBgColor"
                    :readonly="!$store.getters['IncidentReporting/getIsEditable']"
                    auto-grow
                    hide-details
                    name="input-7-1"
                    outlined
                ></v-textarea>
            </v-col>
            <v-col
                class="pb-0" cols="12">
                <label>{{ langMap.ir.ab.access }}:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].access_id"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="$store.getters['IncidentReporting/getIROptions'].accesses"
                    class=""
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    outlined
                    placeholder="Access"
                    required
                ></v-select>
                <div
                    v-else
                >
                    <v-chip
                        :color="getChipColor($store.getters['IncidentReporting/getSelectedIR'].access)"
                        :textColor="getChipColor($store.getters['IncidentReporting/getSelectedIR'].access)"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{
                            $store.getters['IncidentReporting/getSelectedIR'].access ?
                                $store.getters['IncidentReporting/getSelectedIR'].access.name :
                                langMap.ir.ab.not_selected
                        }}
                    </v-chip>
                </div>
            </v-col>
        </v-row>
        <div v-else>@
            <h3>No data</h3>
        </div>
    </v-card>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'incident-tab-general',
    data() {
        return {
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
            colors: {
                notSelected: '#c1c4c9',
            },
        }
    },
    mounted() {
        this.$store.dispatch('RiskRepository/callGetClients');
        this.$store.dispatch('SettingsIncident/ProcessStates/callList');
        const that = this
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });

    },
    methods: {
        getChipColor(data) {
            return data ? (data.color ? data.color : this.themeBgColor) : this.colors.notSelected;
        }
    },
    computed: {
        scenarios: function () {
            return this.$store.getters['IncidentReporting/getSelectedIR'].action_boards.map(item => {
                if (item.type_id === 2) {
                    return item;
                }
            });
        }
    }
}
</script>

<style scoped>
.v-input, ::v-deep(label) {
    font-size: 14px;
}
</style>
