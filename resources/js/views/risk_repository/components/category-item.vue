<template>
    <v-card :style="`${selected ? `border: 2px solid ${this.themeBgColor}`: ''}`" class="mb-2" outlined
            style="cursor: pointer"
            @click="openArticle()"
    >
        <v-list-item>
            <v-list-item-content>
                <v-list-item-title class="text-h6 mb-1">
                    <div class="float-left">{{ item.name }}</div>
                    <v-spacer v-if="extended"></v-spacer>
                    <v-chip v-if="extended"
                            :color="themeBgColor"
                            :textColor="$helpers.color.invertColor(themeBgColor)"
                            class="float-right text-uppercase"
                            label
                            x-small
                    >
                        approved
                    </v-chip>
                </v-list-item-title>
                <v-list-item-title v-if="extended">
                    Natural disaster
                    <br>
                    Brauerei Luzern including all child organizations
                    <br>
                    Risk incident potential: From Low
                </v-list-item-title>
                <v-list-item-title v-if="extended">
                    <v-chip v-for="(tag, index) in item.tags"
                            :key="index"
                            :color="themeBgColor"
                            class="text-uppercase"
                            label
                            outlined
                            x-small
                    >
                        {{ tag }}
                    </v-chip>
                </v-list-item-title>
            </v-list-item-content>
        </v-list-item>
    </v-card>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'risk-category-item',
    props: {
        selected: {
            type: Boolean,
            default: false,
        },
        extended: {
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
        openArticle() {
            console.log(this.item.id);
            this.$store.commit('RiskRepository/selectArticleById', this.item.id)
            // this.$router.push(`/risk_repository/${this.$route.params.categoryId}/${this.item.id}`)
        }
    }
}
</script>
