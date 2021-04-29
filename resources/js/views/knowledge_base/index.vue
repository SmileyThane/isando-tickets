<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>

        <v-row>
            <v-col cols="12">
                <v-card outlined>
                    <v-card-title>
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
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="6">
                                <v-text-field v-model="search" hide-details append-icon="mdi-magnify" :label="langMap.main.search" :color="themeBgColor" v-on:keyup="openCategory($route.query.category)" />
                            </v-col>
                            <v-col cols="6">
                                <v-select multiple v-model="searchWhere" hide-details :items="searchOptions" item-value="id" item-text="name" label="Search in" :color="themeBgColor" v-on:change="openCategory($route.query.category)" />
                            </v-col>
                            <v-col cols="12">
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
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="4">
                <v-row>
                    <v-col v-for="category in categories" :key="'c'+category.id"  cols="12">
                        <v-card outlined :class="category.id == $route.query.category ? 'parent' : ''">
                        <v-card-title>
                            <v-icon large left :color="category.icon_color" v-text="category.icon ? category.icon : 'mdi-help'" />
                            {{ $helpers.i18n.localized(category) }}
                            <v-spacer></v-spacer>
                            <v-menu bottom>
                                <template v-slot:activator="{ on }">
                                    <v-btn v-on="on" icon>
                                        <v-icon>mdi-dots-vertical</v-icon>
                                    </v-btn>
                                </template>

                                <v-list>
                                    <v-list-item link>
                                        <v-list-item-title @click="editCategory(category)">{{ langMap.kb.edit }}</v-list-item-title>
                                        <v-list-item-action>
                                            <v-icon :color="themeBgColor">mdi-folder-edit-outline</v-icon>
                                        </v-list-item-action>
                                    </v-list-item>
                                    <v-list-item link>
                                        <v-list-item-title @click="deleteCategory(category)">{{ langMap.kb.delete }}</v-list-item-title>
                                        <v-list-item-action>
                                            <v-icon :color="themeBgColor">mdi-folder-remove-outline</v-icon>
                                        </v-list-item-action>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </v-card-title>
                        <v-card-text style="height: 6em;">
                            <p v-if="$helpers.i18n.localized(category, 'description')" :tooltip="$helpers.i18n.localized(category, 'description')" class="lim">{{ $helpers.i18n.localized(category, 'description') }}</p>
                            <p>
                                {{ langMap.kb.articles }}: {{ category.articles_count }} <br/>
                                {{ langMap.kb.categories }}: {{ category.categories_count }}
                            </p>

                        </v-card-text>
                        <v-card-actions>
                            <v-btn v-if="category.id == $route.query.category" text :color="themeBgColor" v-text="langMap.kb.return_to_parent" @click="openCategory(category.parent_id)" />
                            <v-btn v-else text :color="themeBgColor" v-text="langMap.kb.open_category" @click="openCategory(category.id)" />
                        </v-card-actions>
                    </v-card>
                    </v-col>
                </v-row>
            </v-col>
            <v-col cols="8">
                <v-row>
                    <v-col v-for="article in articles" :key="'a'+article.id" cols="6">
                        <v-card outlined :style="`background-color: ${article.featured_color};`">
                            <v-img v-if="article.featured_image" height="200px" :src="article.featured_image.link"/>
                            <v-card-title>
                                {{ $helpers.i18n.localized(article) }}
                                <v-spacer></v-spacer>
                                <v-menu bottom>
                                    <template v-slot:activator="{ on }">
                                        <v-btn v-on="on" icon>
                                            <v-icon>mdi-dots-vertical</v-icon>
                                        </v-btn>
                                    </template>

                                    <v-list>
                                        <v-list-item link>
                                            <v-list-item-title @click="editArticle(article.id)">{{ langMap.kb.edit }}</v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-file-edit-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                        <v-list-item link>
                                            <v-list-item-title @click="deleteArticle(article)">{{ langMap.kb.delete }}</v-list-item-title>
                                            <v-list-item-action>
                                                <v-icon :color="themeBgColor">mdi-file-remove-outline</v-icon>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list>
                                </v-menu>
                            </v-card-title>
                            <v-card-text style="height: 10em;">
                                <v-chip-group v-if="article.tags">
                                    <v-chip v-for="tag in article.tags" :key="tag.id" label small class="mr-2" v-text="tag.name" :color="tag.color" :text-color="invertColor(tag.color)"/>
                                </v-chip-group>
                                <p v-else>{{ langMap.kb.no_tags}}</p>
                                <v-spacer>&nbsp;</v-spacer>
                                <p>{{ $helpers.i18n.localized(article, 'summary') }}</p>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn text :color="themeBgColor" v-text="langMap.kb.read_article" @click="readArticle(article.id)" />
                            </v-card-actions>
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
                                    <v-treeview activatable open-all :active="categoryForm._active" :items="categoriesTree" item-key="id" :color="themeBgColor" @update:active="refreshCategoryForm">
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
                                <v-combobox v-model="categoryForm.icon" :items="categoryIcons" :prepend-icon="categoryForm.icon" hide-selected :label="langMap.main.icon" :color="themeBgColor" />

                                <label>{{ langMap.kb.icon_color }}</label>
                                <v-color-picker dot-size="25" mode="hexa" :model="categoryForm.icon_color" @update:color="updateCategoryColor"/>

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
                        <v-btn text v-text="categoryForm.id ? langMap.main.update : langMap.main.create" @click="updateCategory" :color="themeBgColor" />
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
                        <v-btn text left v-text="langMap.main.cancel" @click="deleteCategoryDlg=false; clearCategoryForm();" />
                        <v-btn text v-text="langMap.main.delete" @click="removeCategory" :color="themeBgColor" />
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
                        <v-btn text left v-text="langMap.main.cancel" @click="deleteArticleDlg=false; clearSelectedArticle();" />
                        <v-btn text v-text="langMap.main.delete" @click="removeArticle" :color="themeBgColor" />
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
>>>.ps {
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
                id: null,
                parent_id: this.$route.query.category ? this.$route.query.category: null,
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
    },
    created() {
        this.dGetTags = _.debounce(this.getTags, 1000);
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
        limitTo (str, count = 50) {
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
            axios.get('/api/kb/categories', {
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
        getCategoriesTree() {
            axios.get('/api/kb/categories/tree').then(response => {
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
            axios.get('/api/kb/articles', {
                params: {
                    search: this.searchWhere.includes(2) || this.searchWhere.includes(3) ? this.search : '',
                    search_in_text: this.searchWhere.includes(3),
                    category_id: this.$route.query.category ? this.$route.query.category: null,
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
                    this.$router.push(`/knowledge_base?category=${id}`, () => {})
                    // query.category = id;
                } else if(this.$router.app.$route.fullPath !== this.$router.app.$route.path) {
                    this.$router.push(`/knowledge_base`, () => {})
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
                parent_id: this.$route.query.category ? this.$route.query.category: null,
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
                parent_id: category.parent_id,
                name: category.name,
                name_de: category.name_de,
                description: category.description,
                description_de: category.description_de,
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
            this.$router.push(`/knowledge_base/${id}`);
        },
        createArticle() {
            this.$router.push('/knowledge_base/create');
        },
        editArticle(id) {
            this.$router.push(`/knowledge_base/${id}/edit`);
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
