<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>

        <v-row>
            <v-col cols="12">
                <v-card outlined>
                    <v-card-text style="padding: 5px 15px;">
                        <v-row>
                            <v-col cols="4">
                                <v-text-field v-model="search" hide-details append-icon="mdi-magnify" :label="langMap.main.search" :color="themeBgColor" v-on:keyup="openCategory($route.query.category)" />
                            </v-col>
                            <v-col cols="4">
                                <v-select multiple v-model="searchWhere" hide-details :items="searchOptions" item-value="id" item-text="name" label="Search in" :color="themeBgColor" v-on:change="openCategory($route.query.category)" />
                            </v-col>
                            <v-col cols="3">
                                <v-select v-model="activeTags"  :items="$store.getters['Tags/getTags']" item-value="id" item-text="name" :label="langMap.kb.tags" hide-selected multiple small-chips append-icon="mdi-tag-multiple-outline" :color="themeBgColor" v-on:change="getArticles();">
                                    <template v-slot:selection="{ attrs, item, parent, selected }">
                                        <v-chip small v-bind="attrs" :color="item.color" :text-color="invertColor(item.color)" label class="ml-2" close @click:close="syncTags(item)">
                                            {{ item.name }}
                                        </v-chip>
                                    </template>
                                    <template v-slot:item="{ attrs, item, parent, selected }">
                                        <v-chip v-bind="attrs" :color="item.color" :text-color="invertColor(item.color)" label class="ml-2">
                                            {{ item.name }}
                                        </v-chip>
                                    </template>
                                </v-select>
                            </v-col>
                            <v-col cols="1" class="text-right">
                                <v-menu bottom>
                                    <template v-slot:activator="{ on }">
                                        <v-btn v-on="on" icon>
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>

                                    <v-list>
                                        <v-list-item link @click.prevent="updateCategoryDlg = true">
                                            <v-list-item-title >{{ langMap.kb.create_category }}</v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-folder-plus-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                        <v-list-item link @click.prevent="createArticle">
                                            <v-list-item-title >{{ langMap.kb.create_article }}</v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-file-plus-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="4">
                <incidentCategory v-for="category in categories" :selected="false" />
            </v-col>
            <v-col cols="4">
                <incidentCategoryItem :selected="false" />
                <incidentCategoryItem :selected="true" />
                <incidentCategoryItem :selected="false" />
                <incidentCategoryItem :selected="false" />
                <incidentCategoryItem :selected="false" />
                <incidentCategoryItem :selected="false" />
            </v-col>
            <v-col cols="4">
                <incidentCategoryDescription />
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import * as _ from "lodash";
import incidentCategory from "./components/category";
import incidentCategoryItem from "./components/category-item";
import incidentCategoryDescription from "./components/category-description";

export default {
    components: {
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
            activeTags: [],
            search: '',
            searchWhere: [1, 2, 3],
            searchOptions: [
                {id: 1, name: this.$store.state.lang.lang_map.kb.search_in_category_names},
                {id: 2, name: this.$store.state.lang.lang_map.kb.search_in_article_names},
                {id: 3, name: this.$store.state.lang.lang_map.kb.search_in_article_names_and_contents}
            ],
            categories: []
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
        this.getCategories()
    },
    methods: {
        getTags() {
            this.$store.dispatch('Tags/getTagList')
        },
        getCategories() {
            axios.get(`/api/kb/categories?type=incident_reporting`, {
                params: {
                    search: this.searchWhere.includes(1) ? this.search : '',
                    category_id: this.$route.query && this.$route.query.category ? this.$route.query.category: null
                }
            }).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.categories = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
    },
}
</script>
