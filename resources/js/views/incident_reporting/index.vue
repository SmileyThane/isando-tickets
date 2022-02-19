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
            <v-col cols="4">
                <incidentCategory
                    v-for="category in $store.getters['IncidentReporting/getCategories']"
                    :key="category.id"
                    :item="category"
                    :selected="$store.getters['IncidentReporting/getSelectedCategory'] && $store.getters['IncidentReporting/getSelectedCategory'].id === category.id"
                />
            </v-col>
            <v-col cols="4" v-if="$store.getters['IncidentReporting/getSelectedCategory']">
                <incidentCategoryItem
                    v-for="article in $store.getters['IncidentReporting/getArticles']"
                    :key="article.id"
                    :item="article"
                    :selected="$store.getters['IncidentReporting/getSelectedArticle'] && $store.getters['IncidentReporting/getSelectedArticle'].id === article.id"
                />
            </v-col>
            <v-col cols="4" v-if="$store.getters['IncidentReporting/getSelectedCategory'] && $store.getters['IncidentReporting/getSelectedArticle']">
                <incidentCategoryDescription />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import * as _ from "lodash";
import incidentSearch from "./components/search";
import incidentCategory from "./components/category";
import incidentCategoryItem from "./components/category-item";
import incidentCategoryDescription from "./components/category-description";

export default {
    components: {
        incidentSearch,
        incidentCategory,
        incidentCategoryItem,
        incidentCategoryDescription
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
        this.$store.dispatch('IncidentReporting/callGetCategories')
            .catch(err => {
                this.snackbarMessage = this.langMap.main.generic_error;
                this.errorType = 'error';
                this.alert = true;
            })
        this.$store.dispatch('IncidentReporting/callGetArticles')
            .catch(err => {
                this.snackbarMessage = this.langMap.main.generic_error;
                this.errorType = 'error';
                this.alert = true;
            })
        if (this.$route.params.categoryId) {
            this.$store.dispatch('IncidentReporting/callGetCategories', this.$route.params.categoryId)
            .then(() => this.$store.commit('IncidentReporting/selectCategoryById', this.$route.params.categoryId))
            this.$store.dispatch('IncidentReporting/callGetArticles', this.$route.params.categoryId)
        }
    },
    methods: {
        getTags() {
            this.$store.dispatch('Tags/getTagList')
        },
    },
}
</script>
