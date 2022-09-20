<template>
    <v-card class="my-2">
        <v-row v-if="$store.getters['IncidentReporting/getSelectedIR']">
            <v-col
                v-if="$store.getters['IncidentReporting/getIsEditable']" class="pb-0" cols="6" lg="6" md="6"
                sm="12"
                xl="4">
                <v-text-field
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].name"
                    :color="themeBgColor"
                    :label="langMap.main.name"
                    prepend-icon="mdi-text"
                    required
                    type="text"
                ></v-text-field>
            </v-col>
            <v-col
                v-if="$store.getters['IncidentReporting/getIsEditable']"
                cols="6" lg="6" md="6" sm="12" xl="8">
            </v-col>
            <v-col
                class="pb-0" cols="12" lg="12" md="12" sm="12" xl="12">
                <label>{{ langMap.ir.ab.categories }}:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].categories"
                    :items="$store.getters['IncidentReporting/getIROptions'].categories"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
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
                <div
                    v-for="category in $store.getters['IncidentReporting/getSelectedIR'].categories"
                    v-else
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
                </div>
            </v-col>
            <v-col v-if="$store.getters['IncidentReporting/getSelectedIR'].clients"
                   class="pb-0" cols="12" lg="12" md="12" sm="12" xl="12">
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
                    :items="$store.getters['RiskRepository/getClients']"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
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
                <span
                    v-for="client in $store.getters['IncidentReporting/getSelectedIR'].clients"
                    v-else
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
            </v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <label>
                    {{ langMap.ir.ab.stage_monitoring }}:
                </label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].stage_monitoring_id"
                    :items="$store.getters['IncidentReporting/getIROptions'].stage_monitorings"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    class=""
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    outlined
                    placeholder="Valid from Stage Monitoring"
                    required
                ></v-select>
                <div
                    v-else
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
                        {{
                            $store.getters['IncidentReporting/getSelectedIR'].stage_monitoring ?
                                $store.getters['IncidentReporting/getSelectedIR'].stage_monitoring.name :
                                ''
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8">
            </v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <label>
                    {{ langMap.ir.ab.impact_potentials }}:
                </label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].impact_potential_id"
                    :items="$store.getters['IncidentReporting/getIROptions'].impact_potentials"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
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
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{
                            $store.getters['IncidentReporting/getSelectedIR'].impact_potentials ?
                                $store.getters['IncidentReporting/getSelectedIR'].impact_potentials.name :
                                ''
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8">
            </v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <label>{{ langMap.ir.ab.importance }}:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].priority_id"
                    :items="$store.getters['IncidentReporting/getIROptions'].priorities"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
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
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{
                            $store.getters['IncidentReporting/getSelectedIR'].priority ?
                                $store.getters['IncidentReporting/getSelectedIR'].priority.name : ''
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8">
            </v-col>
            <v-col class="pb-0" cols="8" lg="8" md="8" sm="12" xl="8">
                <v-textarea
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].description"
                    :readonly="!$store.getters['IncidentReporting/getIsEditable']"
                    auto-grow
                    label="Description"
                    name="input-7-1"
                    :color="themeBgColor"
                    outlined
                ></v-textarea>
            </v-col>
            <v-col
                class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <label>{{ langMap.ir.ab.access }}:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].access_id"
                    :items="$store.getters['IncidentReporting/getIROptions'].accesses"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
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
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        class="text-uppercase"
                        label
                        outlined
                        style="margin: 5px;"
                        x-small
                    >
                        {{
                            $store.getters['IncidentReporting/getSelectedIR'].access ?
                                $store.getters['IncidentReporting/getSelectedIR'].access.name : ''
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col
                cols="6" lg="6" md="6" sm="12" xl="8"></v-col>
        </v-row>
        <div v-else>
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
        }
    },
    mounted() {
        this.$store.dispatch('RiskRepository/callGetClients');
        const that = this
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });

    },
    methods: {}
}
</script>

<style scoped>
.v-input, ::v-deep(label) {
    font-size: 14px;
}
</style>
