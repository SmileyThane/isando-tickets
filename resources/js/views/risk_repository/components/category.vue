<template>
    <v-card :style="`${selected ? `border: 2px solid ${this.themeBgColor}`: ''}`" class="mb-2" outlined
            style="cursor: pointer"
            @click="$store.getters['RiskRepository/getSelectedCategory'] && $store.getters['RiskRepository/getSelectedCategory'].id === item.id ? openCategory(item.parent_id) : openCategory(item.id)"
    >
        <v-card-title class="py-2">
            {{ item.name }}
            <v-spacer></v-spacer>
            <v-menu bottom class="float-right" offset-y>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        icon
                        rounded
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item link>
                        <v-list-item-title>{{ langMap.kb.edit }}</v-list-item-title>
                        <v-list-item-action>
                            <v-icon :color="themeBgColor">mdi-file-edit-outline</v-icon>
                        </v-list-item-action>
                    </v-list-item>
                    <v-list-item link>
                        <v-list-item-title>{{ langMap.kb.delete }}</v-list-item-title>
                        <v-list-item-action>
                            <v-icon :color="themeBgColor">mdi-file-remove-outline</v-icon>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-card-title>
        <v-list-item class="py-0">
            <v-list-item-content class="py-0">
                <div class="text-overline">
                    Risks: {{ item.articles_count }}
                </div>
                <v-list-item-subtitle>{{ item.description }}</v-list-item-subtitle>
            </v-list-item-content>
        </v-list-item>

        <v-card-actions class="py-0 pb-2">
            <v-btn
                v-if="$store.getters['RiskRepository/getSelectedCategory'] && $store.getters['RiskRepository/getSelectedCategory'].id === item.id"
                :color="themeBgColor"
                text @click="openCategory(item.id)"
            >
                Return to parent
            </v-btn>
            <v-btn
                v-else
                :color="themeBgColor"
                text @click="openCategory(item.parent_id)"
            >
                Open category
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'risk-category',
    props: {
        selected: {
            type: Boolean,
            default: false,
        },
        item: {
            type: Object,
            required: true,
        }
    },
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
    methods: {
        openCategory(categoryId) {
            if (categoryId) {
                this.$store.commit('RiskRepository/unselectArticle')
                this.$store.dispatch('RiskRepository/callGetCategories', categoryId)
                this.$store.commit('RiskRepository/selectCategoryById', categoryId)
                this.$store.dispatch('RiskRepository/callGetArticles')
                this.$router.push(`/risk_repository/${categoryId}`)
            } else {
                this.$store.commit('RiskRepository/unselectCategory')
                this.$store.dispatch('RiskRepository/callGetCategories')
                this.$store.dispatch('RiskRepository/callGetArticles')
                this.$router.push(`/risk_repository`)
            }
        }
    }
}
</script>
