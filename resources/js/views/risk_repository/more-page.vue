<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>

        <v-row>
            <v-col cols="12">
                <riskSearch />
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="4" lg="4" xl="3">
                <riskCategoryItem
                    v-for="article in $store.getters['RiskRepository/getArticles']"
                    :key="article.id"
                    :item="article"
                    :selected="$store.getters['RiskRepository/getSelectedArticle'] && $store.getters['RiskRepository/getSelectedArticle'].id === article.id"
                    :extended="true"
                />
            </v-col>
            <v-col cols="8" v-if="$store.getters['RiskRepository/getSelectedArticle']">
                <div class="text-h6">Action board: {{ $store.getters['RiskRepository/getSelectedArticle'].name }}</div>
                <v-tabs v-model="tab" :color="themeBgColor">
                    <v-tab>General</v-tab>
                    <v-tab>Action boards</v-tab>
                    <v-tab>Attachments</v-tab>
                    <v-tab>Version</v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab">
                    <v-tab-item>
                        <riskTabGeneral />
                    </v-tab-item>
                    <v-tab-item>
                        <riskTabActionBoards />
                    </v-tab-item>
                    <v-tab-item>
                        <riskTabAttachments />
                    </v-tab-item>
                    <v-tab-item>
                        <riskTabVersion />
                    </v-tab-item>
                </v-tabs-items>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import * as _ from "lodash";
import riskSearch from "./components/search";
import riskCategory from "./components/category";
import riskCategoryDescription from "./components/category-description";
import riskCategoryItem from "./components/category-item";
import riskTabGeneral from "./components/tab-general";
import riskTabActionBoards from "./components/tab-action-boards";
import riskTabAttachments from "./components/tab-attachments";
import riskTabVersion from "./components/tab-version";

export default {
    components: {
        riskSearch,
        riskCategory,
        riskCategoryItem,
        riskCategoryDescription,
        riskTabGeneral,
        riskTabActionBoards,
        riskTabAttachments,
        riskTabVersion
    },
    name: 'risk-repository',
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            tab: 0,
        }
    },
    created() {
        this.dGetTags = _.debounce(this.getTags, 1000);
    },
    mounted() {
        const that = this
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        if (this.$route.params.categoryId) {
            this.$store.dispatch('RiskRepository/callGetCategories', this.$route.params.categoryId)
                .then(() => this.$store.commit('RiskRepository/selectCategoryById', this.$route.params.categoryId))
            this.$store.dispatch('RiskRepository/callGetArticles', this.$route.params.categoryId)
                .then(() => {
                    if (this.$route.params.articleId) {
                        this.$store.commit('RiskRepository/selectArticleById', this.$route.params.articleId)
                    }
                })
        }
    },
    methods: {
        getTags() {
            this.$store.dispatch('Tags/getTagList')
        },
    },
}
</script>

<style scoped>
*,
>>> .v-input,
>>> label,
>>> .v-btn__content {
    font-size: 12px;
}
.heading {
    font-size: 16px;
}
.clearfix {
    clear: both;
}
</style>
