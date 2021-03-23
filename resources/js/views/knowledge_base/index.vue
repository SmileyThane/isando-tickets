<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>
        <v-toolbar floating class="mb-2 mr-2">
            <v-text-field v-model="search" hide-details append-icon="mdi-magnify" single-line :label="langMap.main.search" :color="themeBgColor" />
            <span v-if="tags.length < 1" class="ml-2">{{ langMap.kb.no_tags }}</span>
            <span v-else class="ml-2">{{ langMap.kb.tags }}:</span>
            <v-chip v-for="tag in tags" :key="tag.id" label small class="ml-2" :color="tagColor(tag.id)" :text-color="invertColor(tagColor(tag.id))" @click="tag.on = !tag.on">
                <v-icon v-if="tag.on" small left :color="invertColor(tagColor(tag.id))">mdi-check</v-icon>
                {{ tag.name }}
            </v-chip>
            <v-btn class="ml-2" text :color="themeBgColor" v-text="langMap.kb.find" />

            <v-spacer></v-spacer>
            <v-menu bottom>
                <template v-slot:activator="{ on }">
                    <v-btn v-on="on" icon>
                        <v-icon>mdi-dots-vertical</v-icon>
                    </v-btn>
                </template>

                <v-list>
                    <v-list-item link>
                        <v-list-item-title @click="updateCategoryDlg = true">{{ langMap.kb.create_category }}</v-list-item-title>
                        <v-list-item-action>
                            <v-icon :color="themeBgColor">mdi-folder-plus-outline</v-icon>
                        </v-list-item-action>
                    </v-list-item>
                    <v-list-item link>
                        <v-list-item-title @click="createArticle">{{ langMap.kb.create_article }}</v-list-item-title>
                        <v-list-item-action>
                            <v-icon :color="themeBgColor">mdi-file-plus-outline</v-icon>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-toolbar>
        <v-row>
            <v-col v-for="category in categories" :key="'c'+category.id" cols="4">
                <v-card outlined>
                    <v-card-title>
                        <v-icon large left :color="themeBgColor" v-text="category.icon ? category.icon : 'mdi-help'" />
                        {{ localized(category) }}
                    </v-card-title>
                    <v-card-text style="height: 6em;">
                        <v-tooltip v-if="localized(category, 'description')" :color="themeBgColor" bottom :style="`color: ${themeFgColor}l`">
                            <template v-slot:activator="{ on, attrs }">
                                <p class="lim" v-bind="attrs" v-on="on">{{ localized(category, 'description') }}</p>
                            </template>
                            <span>{{ localized(category, 'description') }}</span>
                        </v-tooltip>
                        <p>{{ langMap.kb.articles }}: 12</p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn text :color="themeBgColor" v-text="langMap.kb.open_category" @click="openCategory(category.id)" />
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col v-for="article in articles" :key="'a'+article.id" cols="4">
                <v-card outlined>
                    <v-card-title>{{ localized(article) }}</v-card-title>
                    <v-card-text style="height: 10em;">
                        <v-chip v-for="tag in article.tags" :key="tag.id" label small class="mr-2" v-text="tag.name" :color="tagColor(tag.id)" :text-color="invertColor(tagColor(tag.id))"/>

                        <v-spacer>&nbsp;</v-spacer>
                        <v-tooltip v-if="localized(article, 'summary')" :color="themeBgColor" bottom :style="`color: ${themeFgColor}l`">
                            <template v-slot:activator="{ on, attrs }">
                                <p v-bind="attrs" v-on="on">{{ localized(article, 'summary') }}</p>
                            </template>
                            <span>{{ stripHtml(localized(article, 'content')) }}</span>
                        </v-tooltip>

                    </v-card-text>
                    <v-card-actions>
                        <v-btn text :color="themeBgColor" v-text="langMap.kb.read_article" @click="readArticle(article.id)" />
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-dialog v-model="updateCategoryDlg" max-width="600px" persistent>
                <v-card outlined>
                    <v-card-title>{{ langMap.kb.category_details }}</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="4">
                                <label>{{ langMap.kb.parent_category }}</label>
                                <perfect-scrollbar>
                                <v-treeview activatable :items="categories" v-model="categoryForm.parent_id" :color="themeBgColor">
                                    <template v-slot:prepend="{ item }">
                                        <v-icon small>mdi-folder</v-icon>
                                    </template>
                                    <template v-slot:label="{ item }">
                                        {{ localized(item) }}
                                    </template>
                                </v-treeview>
                                </perfect-scrollbar>
                            </v-col>
                            <v-col cols="8">
                                <v-combobox v-model="categoryForm.icon" :items="categoryIcons" :prepend-icon="categoryForm.icon" hide-selected :label="langMap.main.icon" :color="themeBgColor" />

                                <v-expansion-panels>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>English</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-text-field v-model="categoryForm.name" :label="langMap.main.name" hide-details single-line :color="themeBgColor"/>
                                            <v-text-field v-model="categoryForm.description" :label="langMap.main.description" hide-details single-line :color="themeBgColor"/>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Deutsch</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-text-field v-model="categoryForm.name_en" :label="langMap.main.name" hide-details single-line :color="themeBgColor"/>
                                            <v-text-field v-model="categoryForm.description_en" :label="langMap.main.description" hide-details single-line :color="themeBgColor"/>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn text left v-text="langMap.main.cancel" @click="updateCategoryDlg=false; clearCategoryForm();" />
                        <v-btn text v-text="langMap.main.create" @click="createCategory" :color="themeBgColor" />
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </v-container>
</template>

<style scoped>
>>> .lim{
    max-height: 2em;
    overflow: hidden;
}
>>>.ps {
     max-height: 20em;
}
>>>.v-treeview--dense .v-treeview-node__root {
    min-height: 1.1em;
}
</style>

<script>
import EventBus from '../../components/EventBus';
import * as Helper from '../tracking/helper';

export default {
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            errors: [],
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            search: '',
            categories: [],
            articles: [],
            tags: [],
            tagColors: [],
            updateCategoryDlg: false,
            categoryForm: {
                id: null,
                parent_id: this.$route.query.category,
                name: '',
                name_de: '',
                description: '',
                description_de: '',
                icon: '',
            },
            categoryIcons: [
                'mdi-help',
                'mdi-help-circle',
                'mdi-rocket',
            ]
        }
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });

        this.getTags();
        this.getCategoties();
        this.getArticles();
    },
    methods: {
        checkRoleByIds(ids) {
            let roleExists = false;
            ids.forEach(id => {
                if (roleExists === false) {
                    roleExists = this.$store.state.roles.includes(id)
                }
            });
            return roleExists
        },
        localized(item, field = 'name') {
            let locale = this.$store.state.lang.locale.replace(/^([^_]+).*$/, '$1');
            return item[field + '_' + locale] ? item[field + '_' + locale] : item[field];
        },
        limitTo (str, count = 50) {
            if (!str) return '';
            if (String(str).length <= count) return str;
            return String(str).substring(0, count) + '...';
        },
        tagColor(i) {
            if (!this.tagColors[i]) {
                this.tagColors[i] = Helper.genRandomColor();
            }
            return this.tagColors[i];
        },
        invertColor(hex) {
            if (hex.indexOf('#') === 0) {
                hex = hex.slice(1);
            }
            if (hex.length === 3) {
                hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
            }
            if (hex.length !== 6) {
                this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                this.errorType = 'error';
                this.alert = true;

                return hex;
            }
            let r = (255 - parseInt(hex.slice(0, 2), 16)).toString(16),
                g = (255 - parseInt(hex.slice(2, 4), 16)).toString(16),
                b = (255 - parseInt(hex.slice(4, 6), 16)).toString(16);
            return '#' + this.padZero(r) + this.padZero(g) + this.padZero(b);
        },
        padZero(str, len) {
            len = len || 2;
            let zeros = new Array(len).join('0');
            return (zeros + str).slice(-len);
        },
        getTags() {
            this.tags = [{id: 1, name: 'zzz', on:true},{id: 3, name: 'something', on: false}, {id:2, name: 'test', on: true}];
        },
        getCategoties() {
            this.categories = [
                {id: 1, name: 'category1', description: 'category1 description', icon: 'mdi-help'},
                {id: 2, name: 'category2', description: 'category2 long description, lorem, ispum vse dela long description, lorem, ispum vse dela long description, lorem, ispum vse dela', icon: 'mdi-help-circle'},
                {id: 3, name: 'category3', description: 'category3 description', icon: 'mdi-account'},
                {id: 4, name: 'category4', description: 'category4 description', icon: 'mdi-rocket'}
            ];
        },
        getArticles() {
            this.articles = [
                {id: 1, name: 'article 1', content: 'article 1 content long long long text placed here and lorem ipsum as well long long long text placed here and lorem ipsum as well', tags: [{id: 1, name: 'zzz'}, {id:2, name: 'test'}]},
                {id: 2, name: 'article 2', content: 'article 2 content long long long text placed here and lorem ipsum as well long long long text placed here and lorem ipsum as well', tags: [{id:2, name: 'test'}]},
                {id: 3, name: 'article 3', content: 'article 3 content long long long text placed here and lorem ipsum as well long long long text placed here and lorem ipsum as well', tags: [{id: 1, name: 'zzz'}, {id:2, name: 'test'}]},
                {id: 4, name: 'article 4', content: 'article 4 content long long long text placed here and lorem ipsum as well long long long text placed here and lorem ipsum as well', tags: [{id: 1, name: 'zzz'},{id: 3, name: 'something'}, {id:2, name: 'test'}]}
            ];
        },
        openCategory(id) {
            this.$router.push(`?category=${id}`);
        },
        readArticle(id) {
            this.$router.push(`/knowledge_base/${id}`);
        },
        createArticle() {
            this.$router.push('/knowledge_base/create');
        },
        clearCategoryForm() {
            this.categoryForm = {
                id: null,
                parent_id: this.$route.query.category,
                name: '',
                name_de: '',
                description: '',
                description_de: '',
                icon: '',
            };
        },
        createCategory() {
            this.updateCategoryDlg = false;
            this.categoryForm.id = Math.floor(Math.random()*1000);
            this.categories.push(this.categoryForm);
            this.clearCategoryForm();
        },
    }
}
</script>
