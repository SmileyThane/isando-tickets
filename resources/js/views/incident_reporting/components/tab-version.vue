<template>
    <v-row>
        <v-col
            cols="6" xl="4" lg="6" md="6" sm="12"
        >
            <v-text-field
                v-if="$store.getters['IncidentReporting/getIsEditable']"
                :color="themeBgColor"
                :label="langMap.main.version"
                type="text"
                v-model="$store.getters['IncidentReporting/getSelectedIR'].version"
                prepend-icon="mdi-text"
                required
            ></v-text-field>
            <div v-else>
                {{ $store.getters['IncidentReporting/getSelectedIR'].version }}
            </div>
        </v-col>

        <v-col
            v-if="$store.getters['IncidentReporting/getIsEditable']"
            cols="6" xl="8" lg="6" md="6" sm="12">
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'incident-tab-version',
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
        }
    }
}
</script>
