<template>
    <v-card outlined>
        <v-list-item three-line>
            <v-list-item-content>
                <div class="text-overline mb-4">
                    {{ $store.getters['IncidentReporting/getSelectedArticle'].name }}
                </div>
                <v-list-item-subtitle><strong>Risk description:</strong> {{ $store.getters['IncidentReporting/getSelectedArticle'].summary }}</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Risk owner:</strong> {{ $store.getters['IncidentReporting/getSelectedArticle'].owner_id }}</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Approved on:</strong> <span v-if="$store.getters['IncidentReporting/getSelectedArticle'].approved_at">{{ $store.getters['IncidentReporting/getSelectedArticle'].approved_at }}</span></v-list-item-subtitle>
                <v-list-item-subtitle><strong>Applies to organizations:</strong> All</v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>
        <v-card-actions>
            <v-btn
                text :color="themeBgColor"
                @click="$router.push(`/incident_reporting/${$store.getters['IncidentReporting/getSelectedCategory'].id}/${$store.getters['IncidentReporting/getSelectedArticle'].id}`)"
            >More info</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'incident-category-description',
    data() {
        return {
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            langMap: this.$store.state.lang.lang_map,
        }
    },
    mounted() {
        const that = this
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
}
</script>
