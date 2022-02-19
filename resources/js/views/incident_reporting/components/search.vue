<template>
    <v-card outlined>
        <v-card-text style="padding: 5px 15px;">
            <v-row>
                <v-col cols="4">
                    <v-text-field
                        v-model="search"
                        hide-details append-icon="mdi-magnify"
                        :label="langMap.main.search"
                        :color="themeBgColor"
                        v-on:keyup="openCategory($route.query.category)"
                    />
                </v-col>
                <v-col cols="4">
                    <v-select
                        multiple
                        v-model="searchWhere"
                        hide-details :items="searchOptions"
                        item-value="id" item-text="name"
                        label="Search in" :color="themeBgColor"
                        v-on:change="openCategory($route.query.category)"
                    />
                </v-col>
                <v-col cols="3">
                    <v-select v-model="activeTags"  :items="$store.getters['Tags/getTags']" item-value="id" item-text="name" :label="langMap.kb.tags" hide-selected multiple small-chips append-icon="mdi-tag-multiple-outline" :color="themeBgColor" v-on:change="getArticles();">
                        <template v-slot:selection="{ attrs, item, parent, selected }">
                            <v-chip small v-bind="attrs" :color="item.color" :text-color="$helpers.color.invertColor(item.color)" label class="ml-2" close @click:close="syncTags(item)">
                                {{ item.name }}
                            </v-chip>
                        </template>
                        <template v-slot:item="{ attrs, item, parent, selected }">
                            <v-chip v-bind="attrs" :color="item.color" :text-color="$helpers.color.invertColor(item.color)" label class="ml-2">
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
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    name: 'incident-search',
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
                return this.$store.getters['IncidentReporting/getSearch']
            },
            set(val) {
                this.$store.commit('IncidentReporting/setSearch', val)
            }
        },
        searchWhere: {
            get() {
                return this.$store.getters['IncidentReporting/getSearchWhere']
            },
            set(val) {
                this.$store.commit('IncidentReporting/setSearchWhere', val)
            }
        },
        activeTags: {
            get() {
                return this.$store.getters['IncidentReporting/getActiveTags']
            },
            set(val) {
                this.$store.commit('IncidentReporting/setActiveTags', val)
            }
        }
    }
}
</script>
