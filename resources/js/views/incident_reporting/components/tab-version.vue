<template>
    <v-row>
        <v-col
            cols="6" lg="6" md="6" sm="12" xl="4"
        >
            <v-text-field
                v-if="$store.getters['IncidentReporting/getIsEditable']"
                v-model="$store.getters['IncidentReporting/getSelectedIR'].version"
                :color="themeBgColor"
                :label="langMap.main.version"
                prepend-icon="mdi-text"
                required
                type="text"
            ></v-text-field>
            <div v-else>
                {{ $store.getters['IncidentReporting/getSelectedIR'].version }}
            </div>
        </v-col>

        <v-col
            v-if="$store.getters['IncidentReporting/getIsEditable']"
            cols="6" lg="6" md="6" sm="12" xl="8">
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
