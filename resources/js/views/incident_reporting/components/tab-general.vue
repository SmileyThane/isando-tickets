<template>
    <v-card class="my-2">
        <v-row v-if="$store.getters['IncidentReporting/getSelectedIR']">
            <v-col
                cols="6" xl="4" lg="6" md="6" sm="12"
                v-if="$store.getters['IncidentReporting/getIsEditable']"
                class="pb-0">
                <v-text-field
                    :color="themeBgColor"
                    :label="langMap.main.name"
                    type="text"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].name"
                    prepend-icon="mdi-text"
                    required
                ></v-text-field>
            </v-col>
            <v-col
                v-if="$store.getters['IncidentReporting/getIsEditable']"
                cols="6" xl="8" lg="6" md="6" sm="12">
            </v-col>
            <v-col
                cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label>Categories:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    class="small"
                    placeholder="Categories"
                    :items="$store.getters['IncidentReporting/getIROptions'].categories"
                    item-value="id"
                    item-text="name"
                    multiple
                    dense
                    outlined
                    hide-details
                    required
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].categories"
                ></v-select>
                <div
                    v-else
                    v-for="category in $store.getters['IncidentReporting/getSelectedIR'].categories"
                >
                    <v-chip
                            class="float-right text-uppercase"
                            label
                            style="margin: 5px;"
                            :color="themeBgColor"
                            :textColor="themeBgColor"
                            x-small
                            outlined
                    >
                        {{category.name}}
                    </v-chip>
                </div>
            </v-col>
            <v-col
                cols="6" xl="8" lg="6" md="6" sm="12">
            </v-col>
            <v-col v-if="$store.getters['IncidentReporting/getSelectedIR'].clients"
                   cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label>Clients:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    class=""
                    placeholder="Clients"
                    :items="$store.getters['RiskRepository/getClients']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].clients"
                    item-value="id"
                    item-text="name"
                    multiple
                    dense
                    outlined
                    hide-details
                    required
                ></v-select>
                <div
                    v-else
                    v-for="client in $store.getters['IncidentReporting/getSelectedIR'].clients"
                >
                    <v-chip
                        class="float-right text-uppercase"
                        label
                        style="margin: 5px;"
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        x-small
                        outlined
                    >
                        {{client.name}}
                    </v-chip>
                </div>
            </v-col>
            <v-col
                cols="6" xl="8" lg="6" md="6" sm="12" class="pb-0">
                <v-checkbox
                    v-if="$store.getters['IncidentReporting/getSelectedIR'].clients.length > 0"
                    :disabled="!$store.getters['IncidentReporting/getIsEditable']" class="mt-0"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].with_child_clients"
                    label="Include child organizations"
                ></v-checkbox>
            </v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label>
                    Valid from Stage Monitoring:
                </label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    class=""
                    placeholder="Valid from Stage Monitoring"
                    :items="$store.getters['IncidentReporting/getIROptions'].stage_monitorings"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].stage_monitoring_id"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    hide-details
                    required
                ></v-select>
                <div
                    v-else
                >
                    <v-chip
                        class="float-right text-uppercase"
                        label
                        style="margin: 5px;"
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        x-small
                        outlined
                    >
                        {{  $store.getters['IncidentReporting/getSelectedIR'].stage_monitoring ?
                            $store.getters['IncidentReporting/getSelectedIR'].stage_monitoring.name :
                            ''
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12">
            </v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label >Importance:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    class=""
                    placeholder="Importance"
                    :items="$store.getters['IncidentReporting/getIROptions'].priorities"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].priority_id"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    hide-details
                    required
                ></v-select>
                <div
                    v-else
                >
                    <v-chip
                        class="float-right text-uppercase"
                        label
                        style="margin: 5px;"
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        x-small
                        outlined
                    >
                        {{
                                $store.getters['IncidentReporting/getSelectedIR'].priority.name
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12">
            </v-col>
            <v-col
                cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label>Access:</label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getIsEditable']"
                    class=""
                    placeholder="Access"
                    :items="$store.getters['IncidentReporting/getIROptions'].accesses"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].access_id"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    hide-details
                    required
                ></v-select>
                <div
                    v-else
                >
                    <v-chip
                        class="float-right text-uppercase"
                        label
                        style="margin: 5px;"
                        :color="themeBgColor"
                        :textColor="themeBgColor"
                        x-small
                        outlined
                    >
                        {{
                                $store.getters['IncidentReporting/getSelectedIR'].access.name
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col
                cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <v-textarea
                    :readonly="!$store.getters['IncidentReporting/getIsEditable']"
                    name="input-7-1"
                    label="Description"
                    auto-grow
                    outlined
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].description"
                ></v-textarea>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
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
    methods: {
    }
}
</script>

<style scoped>
.v-input, ::v-deep(label) {
    font-size: 14px;
}
</style>
