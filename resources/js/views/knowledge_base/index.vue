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
                                <v-text-field v-model="search" :color="themeBgColor" :label="langMap.main.search"
                                              append-icon="mdi-magnify" hide-details
                                              v-on:keyup="openCategory($route.query.category)"/>
                            </v-col>
                            <v-col cols="4">
                                <v-select v-model="searchWhere" :color="themeBgColor" :items="searchOptions"
                                          hide-details
                                          item-text="name" item-value="id" label="Search in" multiple
                                          v-on:change="openCategory($route.query.category)"/>
                            </v-col>
                            <v-col cols="3">
                                <v-select v-model="activeTags" :color="themeBgColor"
                                          :items="$store.getters['Tags/getTags']"
                                          :label="langMap.kb.tags" append-icon="mdi-tag-multiple-outline" hide-selected
                                          item-text="name" item-value="id"
                                          multiple small-chips
                                          v-on:change="getArticles();">
                                    <template v-slot:selection="{ attrs, item, parent, selected }">
                                        <v-chip :color="item.color" :text-color="invertColor(item.color)" class="ml-2"
                                                close label small v-bind="attrs"
                                                @click:close="syncTags(item)">
                                            {{ item.name }}
                                        </v-chip>
                                    </template>
                                    <template v-slot:item="{ attrs, item, parent, selected }">
                                        <v-chip :color="item.color" :text-color="invertColor(item.color)" class="ml-2"
                                                label v-bind="attrs">
                                            {{ item.name }}
                                        </v-chip>
                                    </template>
                                </v-select>
                            </v-col>
                            <v-col class="text-right" cols="1">
                                <v-menu
                                    v-if="$helpers.auth.checkPermissionByIds([103])"
                                    bottom
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-btn icon v-on="on">
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>

                                    <v-list>
                                        <v-list-item link @click.prevent="updateCategoryDlg = true">
                                            <v-list-item-title>
                                                {{ langMap.kb.create_category }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-folder-plus-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                        <v-list-item link @click.prevent="createArticle">
                                            <v-list-item-title>
                                                {{ langMap.main.create }}
                                                {{ langMap.sidebar[$route.params.alias] }}
                                                {{ langMap.kb.create_article }}
                                            </v-list-item-title>
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
                <v-row>
                    <v-col v-for="category in categories" :key="'c'+category.id" cols="12">
                        <v-card :class="category.id == $route.query.category ? 'parent' : ''" outlined>
                            <v-card-title>
                                <v-icon :color="category.icon_color" large left
                                        v-text="category.icon ? category.icon : 'mdi-help'"/>
                                {{ $helpers.i18n.localized(category) }}
                                <v-spacer></v-spacer>
                                <v-menu
                                    v-if="$helpers.auth.checkPermissionByIds([98])"
                                    bottom
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-btn icon v-on="on">
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>

                                    <v-list>
                                        <v-list-item link @click="editCategory(category)">
                                            <v-list-item-title>
                                                {{
                                                    langMap.kb.edit
                                                }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">
                                                    mdi-folder-edit-outline
                                                </v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                        <v-list-item
                                            v-if="$helpers.auth.checkPermissionByIds([99])"
                                            link
                                            @click="deleteCategory(category)"
                                        >
                                            <v-list-item-title>
                                                {{
                                                    langMap.kb.delete
                                                }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-folder-remove-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </v-card-title>
                            <v-card-text style="height: 6em;">
                                <p v-if="$helpers.i18n.localized(category, 'description')"
                                   :tooltip="$helpers.i18n.localized(category, 'description')" class="lim">
                                    {{ $helpers.i18n.localized(category, 'description') }}</p>
                            </v-card-text>
<!--                            <v-card-actions>-->
<!--                                <v-btn v-if="category.id == $route.query.category" :color="themeBgColor" text-->
<!--                                       @click="openCategory(category.parent_id)" v-text="langMap.kb.return_to_parent"/>-->
<!--                                <v-btn v-else :color="themeBgColor" text @click="openCategory(category.id)"-->
<!--                                       v-text="langMap.kb.open_category"/>-->
<!--                            </v-card-actions>-->
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="8">
                <v-row>
                    <v-col v-for="article in articles" :key="'a'+article.id" cols="6">
                        <v-card :style="`background-color: ${article.featured_color};`" outlined>
                            <v-card-title style="cursor: pointer" @click="readArticle(article.id)">
                                {{ $helpers.i18n.localized(article) }}
                                <v-spacer></v-spacer>
                                <v-menu
                                    v-if="$helpers.auth.checkPermissionByIds([98])"
                                    bottom
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-btn icon v-on="on">
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>

                                    <v-list>
                                        <v-list-item link @click="editArticle(article.id)">
                                            <v-list-item-title >
                                                {{
                                                    langMap.kb.edit
                                                }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-file-edit-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                        <v-list-item
                                            v-if="$helpers.auth.checkPermissionByIds([99])"
                                            link
                                            @click="deleteArticle(article)"
                                        >
                                            <v-list-item-title>
                                                {{
                                                    langMap.kb.delete
                                                }}
                                            </v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-file-remove-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </v-card-title>
                            <!--                            <v-card-text style="height: 6em;">-->
                            <!--                                <v-chip-group v-if="article.tags">-->
                            <!--                                    <v-chip v-for="tag in article.tags" :key="tag.id" label small class="mr-2" v-text="tag.name" :color="tag.color" :text-color="invertColor(tag.color)"/>-->
                            <!--                                </v-chip-group>-->
                            <!--                                <p v-else>{{ langMap.kb.no_tags}}</p>-->
                            <!--                                <v-spacer>&nbsp;</v-spacer>-->
                            <!--                                <p>{{ $helpers.i18n.localized(article, 'summary') }}</p>-->
                            <!--                            </v-card-text>-->
                            <!--                            <v-card-actions>-->
                            <!--                                <v-btn text :color="themeBgColor" v-text="langMap.kb.read_article" @click="readArticle(article.id)" />-->
                            <!--                            </v-card-actions>-->
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
        <v-row justify="center">
            <v-dialog v-model="updateCategoryDlg" max-width="600px" persistent>
                <v-card outlined>
                    <v-card-title>{{ langMap.kb.category_details }}</v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="6">
                                <label>{{ langMap.kb.parent_category }}</label>
                                <perfect-scrollbar>
                                    <v-treeview :active="categoryForm._active" :color="themeBgColor"
                                                :items="categoriesTree"
                                                activatable item-key="id" open-all
                                                @update:active="refreshCategoryForm">
                                        <template v-slot:prepend="{ item }">
                                            <v-icon>mdi-folder</v-icon>
                                        </template>
                                        <template v-slot:label="{ item }">
                                            {{ $helpers.i18n.localized(item) }}
                                        </template>
                                    </v-treeview>
                                </perfect-scrollbar>
                            </v-col>
                            <v-col cols="6">
                                <label>{{ langMap.main.icon }}</label>
                                <v-select v-model="categoryForm.icon"
                                          :color="themeBgColor"
                                          :items="categoryIcons"
                                          :label="langMap.main.icon"
                                          append-icon="mdi-tag-multiple-outline"
                                          hide-selected
                                          item-text="name"
                                          item-value="id"
                                          v-on:change="getArticles();">
                                    <template v-slot:selection="{ attrs, item, parent, selected }">
                                        <i class="mdi" :class="item"></i>
                                    </template>
                                    <template v-slot:item="{ attrs, item, parent, selected }">
                                        <i class="mdi" :class="item"></i>
                                    </template>
                                </v-select>

                                <label>{{ langMap.kb.icon_color }}</label>
                                <v-color-picker :model="categoryForm.icon_color" dot-size="25" mode="hexa"
                                                @update:color="updateCategoryColor"/>

                                <v-expansion-panels>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>English</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-text-field v-model="categoryForm.name" :color="themeBgColor"
                                                          :label="langMap.main.name" hide-details single-line/>
                                            <v-text-field v-model="categoryForm.description"
                                                          :color="themeBgColor" :label="langMap.main.description"
                                                          hide-details
                                                          single-line/>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Deutsch</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-text-field v-model="categoryForm.name_de" :color="themeBgColor"
                                                          :label="langMap.main.name" hide-details single-line/>
                                            <v-text-field v-model="categoryForm.description_de"
                                                          :color="themeBgColor" :label="langMap.main.description"
                                                          hide-details
                                                          single-line/>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn left text @click="updateCategoryDlg=false; clearCategoryForm();"
                               v-text="langMap.main.cancel"/>
                        <v-btn :color="themeBgColor" text
                               @click="updateCategory"
                               v-text="categoryForm.id ? langMap.main.update : langMap.main.create"/>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="deleteCategoryDlg" max-width="600px" persistent>
                <v-card outlined>
                    <v-card-title>{{ langMap.main.delete }}</v-card-title>
                    <v-card-text>
                        <p>
                            {{ langMap.kb.delete_category }}<br/>
                            <b>{{ $helpers.i18n.localized(categoryForm) }}</b>
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn left text @click="deleteCategoryDlg=false; clearCategoryForm();"
                               v-text="langMap.main.cancel"/>
                        <v-btn :color="themeBgColor" text @click="removeCategory" v-text="langMap.main.delete"/>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <v-dialog v-model="deleteArticleDlg" max-width="600px" persistent>
                <v-card outlined>
                    <v-card-title>{{ langMap.main.delete }}</v-card-title>
                    <v-card-text>
                        <p>
                            {{ langMap.kb.delete_article }}<br/>
                            <b>{{ $helpers.i18n.localized(selectedArticle) }}</b>
                        </p>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn left text @click="deleteArticleDlg=false; clearSelectedArticle();"
                               v-text="langMap.main.cancel"/>
                        <v-btn :color="themeBgColor" text @click="removeArticle" v-text="langMap.main.delete"/>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>
    </v-container>
</template>

<style scoped>
>>> .parent {
    background: #fafafa !important;
}

>>> .lim {
    max-height: 1.5em;
    overflow: hidden;
}

>>> .ps {
    max-height: 20em;
}
</style>

<script>
import EventBus from '../../components/EventBus';
import * as _ from 'lodash';

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
            searchWhere: [1, 2, 3],
            searchOptions: [
                {id: 1, name: this.$store.state.lang.lang_map.kb.search_in_category_names},
                {id: 2, name: this.$store.state.lang.lang_map.kb.search_in_article_names},
                {id: 3, name: this.$store.state.lang.lang_map.kb.search_in_article_names_and_contents}
            ],
            categories: [],
            categoriesTree: [],
            articles: [],
            activeTags: [],
            updateCategoryDlg: false,
            deleteCategoryDlg: false,
            categoryForm: {
                type: this.$route.params.alias,
                id: null,
                parent_id: this.$route.query.category ? this.$route.query.category : null,
                name: '',
                name_de: '',
                description: '',
                description_de: '',
                icon: '',
                icon_color: this.$store.state.themeBgColor,
                _active: []
            },
            categoryIcons: [
                'mdi-help',
                'mdi-help-circle',
                'mdi-rocket',
            ],
            deleteArticleDlg: false,
            selectedArticle: {
                id: null,
                name: '',
                name_de: ''
            }
        }
    },
    watch: {
        $route(to, from) {
            this.getCategories();
            this.getArticles();
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

        this.openCategory(this.$route.query.category);
        this.getCategoriesTree();
        this.getTags()
    },
    created() {

    },
    methods: {
        limitTo(str, count = 50) {
            if (!str) return '';
            if (String(str).length <= count) return str;
            return String(str).substring(0, count) + '...';
        },
        invertColor(hex) {
            return this.$helpers.color.invertColor(hex);
        },
        getTags() {
            this.$store.dispatch('Tags/getTagList')
        },
        getCategories() {
            axios.get(`/api/kb/categories?type=${this.$route.params.alias}`, {
                params: {
                    search: this.searchWhere.includes(1) ? this.search : '',
                    category_id: this.$route.query && this.$route.query.category ? this.$route.query.category : null
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
        getCategoriesTree() {
            axios.get(`/api/kb/categories/tree?type=${this.$route.params.alias}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.categoriesTree = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        getArticles() {
            axios.get(`/api/kb/articles?type=${this.$route.params.alias}`, {
                params: {
                    search: this.searchWhere.includes(2) || this.searchWhere.includes(3) ? this.search : '',
                    search_in_text: this.searchWhere.includes(3),
                    category_id: this.$route.query.category ? this.$route.query.category : null,
                    tags: this.activeTags
                }
            }).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.articles = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        openCategory(id) {
            // console.log(id);
            // localStorage.setItem('kb_category', id ? id : null);

            // let query = Object.assign({}, this.$route.query);
            if (parseInt(id)) {
                this.$router.push(`/${this.$route.params.alias}?category=${id}`, () => {
                })
                // query.category = id;
            } else if (this.$router.app.$route.fullPath !== this.$router.app.$route.path) {
                this.$router.push(`/${this.$route.params.alias}`, () => {
                })
                // delete query.category;
            }
            // if (this.$route.query !== {})
            // {
            //     console.log(query);
            //     this.$router.replace({ query });
            //
            // }

            this.getCategories();
            this.getArticles();
        },
        clearCategoryForm() {
            this.categoryForm = {
                id: null,
                type: this.$route.params.alias,
                parent_id: this.$route.query.category ? this.$route.query.category : null,
                name: '',
                name_de: '',
                description: '',
                description_de: '',
                icon: '',
                icon_color: this.themeBgColor,
                _active: []
            };
        },
        fillCategoryForm(category) {
            this.categoryForm = {
                id: category.id,
                type: this.$route.params.alias,
                parent_id: category.parent_id,
                name: category.name,
                name_de: category.name_de,
                description: category.description,
                description_de: category.description_de,
                icon_color: category.icon_color,
                icon: category.icon,
                _active: [category.parent_id]
            };
            this.$forceUpdate();
        },
        refreshCategoryForm(parent) {
            this.categoryForm.parent_id = parent.length > 0 ? parent[0] : null;
            this.$forceUpdate();
        },
        editCategory(category) {
            this.fillCategoryForm(category);
            this.updateCategoryDlg = true;
        },
        updateCategory() {
            if (this.categoryForm.id) {
                axios.put(`/api/kb/category/${this.categoryForm.id}`, this.categoryForm).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.updateCategoryDlg = false;
                        this.clearCategoryForm();
                        this.getCategories();

                        this.snackbarMessage = this.langMap.kb.category_updated;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            } else {
                axios.post('/api/kb/category', this.categoryForm).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.updateCategoryDlg = false;
                        this.clearCategoryForm();
                        this.getCategories();

                        this.snackbarMessage = this.langMap.kb.category_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            }
        },
        deleteCategory(category) {
            this.fillCategoryForm(category);
            this.deleteCategoryDlg = true;
        },
        removeCategory() {
            this.deleteCategoryDlg = false;

            axios.delete(`/api/kb/category/${this.categoryForm.id}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.clearCategoryForm();
                    this.getCategories();

                    this.snackbarMessage = this.langMap.kb.category_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });

        },
        readArticle(id) {
            this.$router.push(`/${this.$route.params.alias}/${id}`);
        },
        createArticle() {
            location.href = `/${this.$route.params.alias}/create`;
        },
        editArticle(id) {
            this.$router.push(`/${this.$route.params.alias}/${id}/edit`);
        },
        clearSelectedArticle() {
            this.selectedArticle = {
                id: null,
                name: '',
                name_de: ''
            }
        },
        deleteArticle(article) {
            this.selectedArticle = article;
            this.deleteArticleDlg = true;
        },
        removeArticle() {
            this.deleteArticleDlg = false;

            axios.delete(`/api/kb/article/${this.selectedArticle.id}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.clearSelectedArticle();
                    this.getArticles();

                    this.snackbarMessage = this.langMap.kb.article_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        updateCategoryColor(color) {
            this.categoryForm.icon_color = color.hex;
        },
        syncTags(item) {
            let index = this.activeTags.indexOf(item.id);
            if (index !== -1) {
                this.activeTags.splice(index, 1);
                this.getArticles();
            }
        }
    }
}
</script>
