<template>
    <v-card outlined>
        <v-list-item three-line>
            <v-list-item-content>
                <div class="text-overline mb-4">
                    100.00 Natural disasters
                </div>
                <v-list-item-subtitle><strong>Risk description:</strong> Greyhound divisely hello coldly fonwderfully</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Risk owner:</strong> Jan Stolz</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Approved on:</strong> 15.07.2020</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Applies to organizations:</strong> All</v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>
        <v-card-actions>
            <v-btn text :color="themeBgColor" @click="$router.push('/incident_reporting/1/1')">More info</v-btn>
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
