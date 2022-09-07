<template>
    <v-card outlined>
        <v-list-item three-line>
            <v-list-item-content>
                <div class="text-overline mb-4">
                    {{ $store.getters['RiskRepository/getSelectedArticle'].name }}
                </div>
                <v-list-item-subtitle><strong>Risk description:</strong>
                    {{ $store.getters['RiskRepository/getSelectedArticle'].summary }}
                </v-list-item-subtitle>
                <v-list-item-subtitle><strong>Risk owner:</strong>
                    {{ $store.getters['RiskRepository/getSelectedArticle'].owner_id }}
                </v-list-item-subtitle>
                <v-list-item-subtitle><strong>Approved on:</strong> <span
                    v-if="$store.getters['RiskRepository/getSelectedArticle'].approved_at">{{
                        $store.getters['RiskRepository/getSelectedArticle'].approved_at
                    }}</span></v-list-item-subtitle>
                <v-list-item-subtitle><strong>Applies to organizations:</strong> All</v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>
        <v-card-actions>
            <v-btn
                :color="themeBgColor" text
                @click="$router.push(`/risk_repository/${$store.getters['RiskRepository/getSelectedCategory'].id}/${$store.getters['RiskRepository/getSelectedArticle'].id}`)"
            >More info
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'risk-category-description',
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
