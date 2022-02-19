<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>

        <v-row>
            <v-col cols="12">
                <incidentSearch />
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="4" lg="4" xl="3">
                <incidentCategoryItem
                    v-for="article in $store.getters['IncidentReporting/getArticles']"
                    :key="article.id"
                    :item="article"
                    :selected="$store.getters['IncidentReporting/getSelectedArticle'] && $store.getters['IncidentReporting/getSelectedArticle'].id === article.id"
                    :extended="true"
                />
            </v-col>
            <v-col cols="8" v-if="$store.getters['IncidentReporting/getSelectedArticle']">
                <div class="text-h6">Action board: {{ $store.getters['IncidentReporting/getSelectedArticle'].name }}</div>
                <v-tabs v-model="tab" :color="themeBgColor">
                    <v-tab>General</v-tab>
                    <v-tab>Action boards</v-tab>
                    <v-tab>Attachments</v-tab>
                    <v-tab>Version</v-tab>
                </v-tabs>
                <v-tabs-items v-model="tab">
                    <v-tab-item>
                        <incidentTabGeneral />
                    </v-tab-item>
                    <v-tab-item>
                        <incidentTabActionBoards />
                    </v-tab-item>
                    <v-tab-item>
                        <incidentTabAttachments />
                    </v-tab-item>
                    <v-tab-item>
                        <incidentTabVersion />
                    </v-tab-item>
                </v-tabs-items>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import * as _ from "lodash";
import incidentSearch from "./components/search";
import incidentCategory from "./components/category";
import incidentCategoryDescription from "./components/category-description";
import incidentCategoryItem from "./components/category-item";
import incidentTabGeneral from "./components/tab-general";
import incidentTabActionBoards from "./components/tab-action-boards";
import incidentTabAttachments from "./components/tab-attachments";
import incidentTabVersion from "./components/tab-version";

export default {
    components: {
        incidentSearch,
        incidentCategory,
        incidentCategoryItem,
        incidentCategoryDescription,
        incidentTabGeneral,
        incidentTabActionBoards,
        incidentTabAttachments,
        incidentTabVersion
    },
    name: 'incident-reporting',
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            activeTags: [],
            search: '',
            searchWhere: [1, 2, 3],
            searchOptions: [
                {id: 1, name: this.$store.state.lang.lang_map.kb.search_in_category_names},
                {id: 2, name: this.$store.state.lang.lang_map.kb.search_in_article_names},
                {id: 3, name: this.$store.state.lang.lang_map.kb.search_in_article_names_and_contents}
            ],
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
            this.$store.dispatch('IncidentReporting/callGetCategories', this.$route.params.categoryId)
                .then(() => this.$store.commit('IncidentReporting/selectCategoryById', this.$route.params.categoryId))
            this.$store.dispatch('IncidentReporting/callGetArticles', this.$route.params.categoryId)
                .then(() => {
                    if (this.$route.params.articleId) {
                        this.$store.commit('IncidentReporting/selectArticleById', this.$route.params.articleId)
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
