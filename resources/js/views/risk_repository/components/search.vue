<template>
    <v-card outlined>
        <v-card-text style="padding: 5px 15px;">
            <v-row>
                <v-col cols="4">
                    <v-text-field
                        v-model="search"
                        :color="themeBgColor" :label="langMap.main.search"
                        append-icon="mdi-magnify"
                        class="pb-0 mr-4"
                        hide-details
                        style="height: 50px;"
                        v-on:keyup="openCategory($route.query.category)"
                    />
                </v-col>
                <v-col cols="4">
                    <v-select
                        v-model="searchWhere"
                        :color="themeBgColor"
                        :items="searchOptions" class="pb-0 mr-4"
                        hide-details item-text="name"
                        item-value="id" label="Search in"
                        multiple
                        style="height: 50px;"
                        v-on:change="openCategory($route.query.category)"
                    />
                </v-col>
                <v-col cols="3">
                    <v-select
                        v-model="activeTags"
                        :color="themeBgColor"
                        :items="$store.getters['Tags/getTags']" :label="langMap.kb.tags"
                        append-icon="mdi-tag-multiple-outline"
                        class="pb-0" hide-selected item-text="name"
                        item-value="id"
                        multiple
                        small-chips
                        style="height: 50px;"
                        v-on:change="getArticles();"
                    >
                        <template v-slot:selection="{ attrs, item, parent, selected }">
                            <v-chip :color="item.color" :text-color="$helpers.color.invertColor(item.color)"
                                    class="ml-2"
                                    close label small v-bind="attrs"
                                    @click:close="syncTags(item)">
                                {{ item.name }}
                            </v-chip>
                        </template>
                        <template v-slot:item="{ attrs, item, parent, selected }">
                            <v-chip :color="item.color" :text-color="$helpers.color.invertColor(item.color)"
                                    class="ml-2" label v-bind="attrs">
                                {{ item.name }}
                            </v-chip>
                        </template>
                    </v-select>
                </v-col>
                <v-col class="text-right" cols="1">
                    <v-menu bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn icon v-on="on">
                                <v-icon>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>

                        <v-list>
                            <v-list-item link @click.prevent="updateCategoryDlg = true">
                                <v-list-item-title>{{ langMap.kb.create_category }}</v-list-item-title>
                                <v-list-item-action>
                                    <v-icon :color="themeBgColor">mdi-folder-plus-outline</v-icon>
                                </v-list-item-action>
                            </v-list-item>
                            <v-list-item link @click.prevent="createArticle">
                                <v-list-item-title>{{ langMap.kb.create_article }}</v-list-item-title>
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
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'risk-search',
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            searchOptions: [
                {id: 1, name: this.$store.state.lang.lang_map.kb.search_in_category_names},
                {id: 2, name: this.$store.state.lang.lang_map.kb.search_in_article_names},
                {id: 3, name: this.$store.state.lang.lang_map.kb.search_in_article_names_and_contents}
            ],
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
    computed: {
        search: {
            get() {
                return this.$store.getters['RiskRepository/getSearch']
            },
            set(val) {
                this.$store.commit('RiskRepository/setSearch', val)
            }
        },
        searchWhere: {
            get() {
                return this.$store.getters['RiskRepository/getSearchWhere']
            },
            set(val) {
                this.$store.commit('RiskRepository/setSearchWhere', val)
            }
        },
        activeTags: {
            get() {
                return this.$store.getters['RiskRepository/getActiveTags']
            },
            set(val) {
                this.$store.commit('RiskRepository/setActiveTags', val)
            }
        }
    }
}
</script>
