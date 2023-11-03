<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>

        <v-row>
            <v-col cols="12">
                <riskSearch/>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="4">
                <riskCategory
                    v-for="category in $store.getters['RiskRepository/getCategories']"
                    :key="category.id"
                    :item="category"
                    :selected="$store.getters['RiskRepository/getSelectedCategory'] && $store.getters['RiskRepository/getSelectedCategory'].id === category.id"
                />
            </v-col>
            <v-col v-if="$store.getters['RiskRepository/getSelectedCategory']" cols="4">
                <riskCategoryItem
                    v-for="article in $store.getters['RiskRepository/getArticles']"
                    :key="article.id"
                    :item="article"
                    :selected="$store.getters['RiskRepository/getSelectedArticle'] && $store.getters['RiskRepository/getSelectedArticle'].id === article.id"
                />
            </v-col>
            <v-col
                v-if="$store.getters['RiskRepository/getSelectedCategory'] && $store.getters['RiskRepository/getSelectedArticle']"
                cols="4">
                <riskCategoryDescription/>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import * as _ from "lodash";
import riskSearch from "./components/search";
import riskCategory from "./components/category";
import riskCategoryItem from "./components/category-item";
import riskCategoryDescription from "./components/category-description";

export default {
    components: {
        riskSearch,
        riskCategory,
        riskCategoryItem,
        riskCategoryDescription
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
        this.$store.dispatch('RiskRepository/callGetCategories')
            .catch(err => {
                this.snackbarMessage = this.langMap.main.generic_error;
                this.errorType = 'error';
                this.alert = true;
            })
        this.$store.dispatch('RiskRepository/callGetArticles')
            .catch(err => {
                this.snackbarMessage = this.langMap.main.generic_error;
                this.errorType = 'error';
                this.alert = true;
            })
        if (this.$route.params.categoryId) {
            this.$store.dispatch('RiskRepository/callGetCategories', this.$route.params.categoryId)
                .then(() => this.$store.commit('RiskRepository/selectCategoryById', this.$route.params.categoryId))
            this.$store.dispatch('RiskRepository/callGetArticles', this.$route.params.categoryId)
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
