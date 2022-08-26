<template>
    <v-card class="my-2">
        <v-row>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label>Categories:</label>
                <v-select
                    v-if="isEditable"
                    class="small"
                    placeholder="Categories"
                    :items="$store.getters['RiskRepository/getCategories']"
                    item-value="id"
                    item-text="name"
                    multiple
                    dense
                    outlined
                    hide-details
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
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label v-if="$store.getters['IncidentReporting/getSelectedIR'].clients.length > 0">Clients:</label>
                <v-select
                    v-if="isEditable"
                    class=""
                    placeholder="Clients"
                    :items="$store.getters['RiskRepository/getClients']"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    hide-details
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
            <v-col cols="6" xl="8" lg="6" md="6" sm="12" class="pb-0">
                <v-checkbox v-if="isEditable" class="mt-0"
                    label="Include child organizations"
                ></v-checkbox>
            </v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label v-if="$store.getters['IncidentReporting/getSelectedIR'].stage_monitoring !== null">Valid from Stage Monitoring:</label>
                <v-select
                    v-if="isEditable"
                    class=""
                    placeholder="Valid from Stage Monitoring"
                    :items="$store.getters['RiskRepository/getClients']"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    hide-details
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
                            $store.getters['IncidentReporting/getSelectedIR'].stage_monitoring ?
                                $store.getters['IncidentReporting/getSelectedIR'].stage_monitoring.name :
                                ''
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label v-if="$store.getters['IncidentReporting/getSelectedIR'].priority">Importance:</label>
                <v-select
                    v-if="isEditable"
                    class=""
                    placeholder="Importance"
                    :items="$store.getters['RiskRepository/getImportance']"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    hide-details
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
                            $store.getters['IncidentReporting/getSelectedIR'].priority ?
                                $store.getters['IncidentReporting/getSelectedIR'].priority.name :
                                ''
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <label v-if="$store.getters['IncidentReporting/getSelectedIR'].access">Access:</label>
                <v-select
                    v-if="isEditable"
                    class=""
                    placeholder="Access"
                    :items="$store.getters['RiskRepository/getImportance']"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    hide-details
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
                            $store.getters['IncidentReporting/getSelectedIR'].access ?
                                $store.getters['IncidentReporting/getSelectedIR'].access.name :
                                ''
                        }}
                    </v-chip>
                </div>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <v-textarea
                    name="input-7-1"
                    label="Description"
                    auto-grow
                    outlined
                    :value="$store.getters['IncidentReporting/getSelectedIR'].description"
                ></v-textarea>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
        </v-row>
    </v-card>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'incident-tab-general',
    data() {
        return {
            isEditable: false,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,

        }
    },
    mounted() {
        this.$store.dispatch('RiskRepository/callGetCategories');
        this.$store.dispatch('RiskRepository/callGetArticles');
        this.$store.dispatch('RiskRepository/callGetClients');
        this.$store.dispatch('RiskRepository/callGetImportance');
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
