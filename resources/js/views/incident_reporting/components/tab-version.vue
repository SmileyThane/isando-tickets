<template>
    <v-row>
        <v-col
            cols="6" lg="6" md="6" sm="12" xl="4"
        >
            <div v-if="$store.getters['IncidentReporting/getIsEditable']" class="pt-4">
                <v-label>
                    {{ 'Status' }}
                </v-label>
                <v-select
                    v-if="$store.getters['IncidentReporting/getSelectedIR']"
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].status_id"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :items="$store.getters['SettingsIncident/ActionBoardStatuses/getItems']"
                    class=""
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    outlined
                    required
                ></v-select>
                <br>
                <v-text-field
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].version"
                    :color="themeBgColor"
                    :item-color="themeBgColor"
                    :label="langMap.main.version"
                    prepend-icon="mdi-text"
                    required
                    type="text"
                ></v-text-field>
                <br>
                <v-label>
                    {{ langMap.ir.ab.valid_till }}:
                </v-label>
                <br>
                <v-date-picker
                    v-model="$store.getters['IncidentReporting/getSelectedIR'].valid_till"
                    :color="themeBgColor"
                    :first-day-of-week="1"
                ></v-date-picker>

            </div>
            <div v-else>
                <p> {{ langMap.main.status }}: {{
                        $store.getters['IncidentReporting/getSelectedIR'].status ?
                            $store.getters['IncidentReporting/getSelectedIR'].status.name :
                            ''
                    }}</p>
                <p> {{ langMap.main.version }}: {{ $store.getters['IncidentReporting/getSelectedIR'].version }}</p>
                <p> {{ 'Updated at' }}: {{
                        moment($store.getters['IncidentReporting/getSelectedIR'].updated_at).format('Y-M-D H:m:s')

                    }}</p>
                <p> {{ langMap.ir.ab.valid_till }}: {{
                        moment($store.getters['IncidentReporting/getSelectedIR'].valid_till).format('Y-M-D H:m:s')
                    }}</p>
                <p> {{ langMap.ir.ab.updated_by }}:
                    <span v-if="$store.getters['IncidentReporting/getSelectedIR'].updated_by">
                        {{ $store.getters['IncidentReporting/getSelectedIR'].updated_by.name }}
                        {{ $store.getters['IncidentReporting/getSelectedIR'].updated_by.surname }}
                    </span>
                </p>
            </div>
        </v-col>
        <v-col
            v-if="$store.getters['IncidentReporting/getIsEditable']"
            cols="6" lg="6" md="6" sm="12" xl="8">
        </v-col>
        <v-col cols="12">
            <IncidentCategoryItem
                v-for="item in $store.getters['IncidentReporting/getSelectedIR'].child_versions"
                :key="item.id"
                :extended="true"
                :item="item"
                :with-version="true"
            />
        </v-col>
    </v-row>
</template>

<script>
import IncidentCategoryItem from "./category-item";

export default {
    name: 'incident-tab-version',
    components: {
        IncidentCategoryItem,
    },
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,

        }
    }
}
</script>
