<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>
        <v-form>
            <v-card outlined>
                <v-card-title>
                    {{ langMap.kb.article_details }}
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="6">
                            <label>{{ langMap.kb.featured_image }}</label>
                            <v-file-input v-model="newFeatured" :color="themeBgColor" accept="image/*" class="mb-2"
                                          dense prepend-icon="mdi-camera"/>
                            <div :style="`background-color: ${article.featured_color};`" class="pa-2">
                                <v-img :src="featured" contain max-height="300"/>
                            </div>
                        </v-col>
                        <v-col cols="6">
                            <label>{{ langMap.kb.featured_color }}</label>
                            <v-color-picker v-model="article.featured_color" dot-size="25" mode="hexa"/>
                        </v-col>
                        <v-col cols="6">
                            <label>{{ langMap.kb.article_category }}</label>
                            <perfect-scrollbar>
                                <v-treeview v-model="categories" :color="themeBgColor" :items="categoriesTree"
                                            :selected-color="themeBgColor"
                                            item-key="id" open-all selectable>
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
                            <label>{{ langMap.kb.tags }}</label>
                            <v-combobox v-model="article.tags" :color="themeBgColor"
                                        :items="$store.getters['Tags/getTags']"
                                        :label="langMap.tracking.tag_btn.choose_tags" chips hide-selected
                                        item-text="name"
                                        item-value="id" multiple>
                                <template v-slot:selection="{ attrs, item, parent, selected }">
                                    <v-chip v-if="item.id" :color="item.color" :text-color="invertColor(item.color)"
                                            class="ml-2" close label v-bind="attrs"
                                            @click:close="removeTag(item)">
                                        {{ item.name }}
                                    </v-chip>
                                    <v-chip v-else class="ml-2" close label @click:close="removeTag(item)">
                                        {{ item }}
                                    </v-chip>
                                </template>
                            </v-combobox>

                            <v-spacer>&nbsp;</v-spacer>
                            <v-switch v-model="article.is_internal" :color="themeBgColor"
                                      :label="langMap.kb.is_internal" :value="1"/>

                        </v-col>
                        <v-col cols="12">
                            <v-expansion-panels>
                                <v-expansion-panel>
                                    <v-expansion-panel-header>English</v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-text-field v-model="article.name" :color="themeBgColor"
                                                      :label="langMap.main.name" :placeholder="langMap.main.name"
                                                      hide-details
                                                      single-line/>
                                        <v-textarea v-model="article.summary" :color="themeBgColor"
                                                    :label="langMap.kb.summary"
                                                    hide-details rows="4" single-line/>
                                        <v-spacer>&nbsp;</v-spacer>
                                        <tinymce ref="content" v-model="article.content"
                                                 :placeholder="langMap.kb.article_content"
                                                 aria-rowcount="40"/>
                                        <v-spacer>&nbsp;</v-spacer>
                                        <hr/>

                                        <v-row>
                                            <v-col cols="6">
                                                <v-textarea v-model="article.keywords" :color="themeBgColor"
                                                            :placeholder="langMap.kb.keywords" rows="4"/>
                                            </v-col>
                                            <v-col cols="6">
                                                <v-spacer>&nbsp;</v-spacer>
                                                <label>{{ langMap.kb.attachments }}</label>

                                                <v-chip-group column>
                                                    <v-chip v-for="attachment in article.attachments"
                                                            v-if="attachment.service_info && attachment.service_info.lang == 'en'"
                                                            :key="attachment.id" :color="themeBgColor" class="mr-2"
                                                            close
                                                            label outlined
                                                            @click:close="deleteAttachment(attachment)">
                                        <span @click="download(attachment.link)">
                                            <v-icon :color="themeBgColor" left v-text="fileIcon(attachment.name)"/>
                                            {{ attachment.name }}
                                        </span>
                                                    </v-chip>
                                                </v-chip-group>

                                                <v-spacer>&nbsp;</v-spacer>
                                                <v-file-input
                                                    ref="fileupload"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :label="langMap.kb.create_attachment"
                                                    :show-size="1024"
                                                    chips
                                                    counter
                                                    multiple
                                                    prepend-icon="mdi-paperclip"
                                                    v-on:change="onFileChange"
                                                >
                                                    <template v-slot:selection="{ file, index, text }">
                                                        <v-chip :color="themeBgColor" label outlined>
                                                            <v-icon :color="themeBgColor" left
                                                                    v-text="fileIcon(file.name)"/>
                                                            {{ text }}
                                                        </v-chip>
                                                    </template>
                                                </v-file-input>
                                            </v-col>
                                        </v-row>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                                <v-expansion-panel>
                                    <v-expansion-panel-header>Deutsch</v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-text-field v-model="article.name_de" :color="themeBgColor"
                                                      :label="langMap.main.name"
                                                      hide-details single-line/>
                                        <v-textarea v-model="article.summary_de" :color="themeBgColor"
                                                    :label="langMap.kb.summary"
                                                    hide-details rows="4" single-line/>
                                        <v-spacer>&nbsp;</v-spacer>
                                        <tinymce ref="content_de" v-model="article.content_de"
                                                 :placeholder="langMap.kb.article_content"
                                                 aria-rowcount="40"/>
                                        <v-spacer>&nbsp;</v-spacer>
                                        <hr/>

                                        <v-row>
                                            <v-col cols="6">
                                                <v-textarea v-model="article.keywords_de" :color="themeBgColor"
                                                            :placeholder="langMap.kb.keywords" rows="4"/>
                                            </v-col>
                                            <v-col cols="6">
                                                <v-spacer>&nbsp;</v-spacer>
                                                <label>{{ langMap.kb.attachments }}</label>

                                                <v-chip-group column>
                                                    <v-chip v-for="attachment in article.attachments"
                                                            v-if="attachment.service_info && attachment.service_info.lang == 'de'"
                                                            :key="attachment.id" :color="themeBgColor" class="mr-2" close
                                                            label outlined
                                                            @click:close="deleteAttachment(attachment)">
                                        <span @click="download(attachment.link)">
                                            <v-icon :color="themeBgColor" left v-text="fileIcon(attachment.name)"/>
                                            {{ attachment.name }}
                                        </span>
                                                    </v-chip>
                                                </v-chip-group>

                                                <v-spacer>&nbsp;</v-spacer>
                                                <v-file-input
                                                    ref="fileupload_de"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :label="langMap.kb.create_attachment"
                                                    :show-size="1024"
                                                    chips
                                                    counter
                                                    multiple
                                                    prepend-icon="mdi-paperclip"
                                                    v-on:change="onFileChangeDe"
                                                >
                                                    <template v-slot:selection="{ file, index, text }">
                                                        <v-chip :color="themeBgColor" label outlined>
                                                            <v-icon :color="themeBgColor" left
                                                                    v-text="fileIcon(file.name)"/>
                                                            {{ text }}
                                                        </v-chip>
                                                    </template>
                                                </v-file-input>
                                            </v-col>
                                        </v-row>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-btn text @click="openCategory" v-text="langMap.main.cancel"/>
                    <v-btn :color="themeBgColor" text @click="saveArticle(false)" v-text="langMap.main.save"/>
                    <v-btn :color="themeBgColor" text @click="saveArticle(true)" v-text="langMap.kb.save_and_close"/>
                </v-card-actions>
            </v-card>

            <v-spacer>&nbsp;</v-spacer>

            <v-card outlined>
                <v-card-title>
                    {{ langMap.kb.article_steps }}
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="6">
                            <v-radio-group v-model="stepType" dense>
                                <v-radio v-for="type in stepTypes" v-bind:key="type.id" :color="themeBgColor"
                                         :label="type.name" :value="type.id"/>
                            </v-radio-group>
                        </v-col>
                        <v-col cols="6">
                            <v-list dense outlined>
                                <v-list-item v-for="(step, index) in article.next" v-bind:key="step.id" dense>
                                    <v-list-item-content>
                                        <v-select v-model="article.next[index]" :color="themeBgColor" :item-color="themeBgColor"
                                                  :items="articles" :label="langMap.kb.next_step" dense
                                                  item-value="id">
                                            <template v-slot:item="{ item }">{{
                                                    $helpers.i18n.localized(item)
                                                }}
                                            </template>
                                            <template v-slot:selection="{ item }">{{
                                                    $helpers.i18n.localized(item)
                                                }}
                                            </template>
                                        </v-select>
                                    </v-list-item-content>
                                    <v-list-item-action>
                                        <v-btn :color="themeBgColor" class="mr-2" icon @click="unlinkStep(step)">
                                            <v-icon>mdi-link-off</v-icon>
                                        </v-btn>
                                    </v-list-item-action>
                                </v-list-item>
                                <v-list-item>
                                    <v-list-item-content v-text="langMap.kb.add_step"/>
                                    <v-list-item-action>
                                        <v-btn :color="themeBgColor" icon @click="addStep">
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>
                                    </v-list-item-action>
                                </v-list-item>
                            </v-list>
                        </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-btn text @click="openCategory" v-text="langMap.main.cancel"/>
                    <v-btn :color="themeBgColor" text @click="saveArticle(false)" v-text="langMap.main.save"/>
                    <v-btn :color="themeBgColor" text @click="saveArticle(true)" v-text="langMap.kb.save_and_close"/>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-container>
</template>

<style scoped>
>>> .ps {
    max-height: 20em;
}

</style>

<script>
import EventBus from '../../components/EventBus';
import * as Helper from '../tracking/helper';
import * as _ from "lodash";

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
            article: {
                id: null,
                type: this.$route.params.alias,
                categories: [],
                name: '',
                name_de: '',
                summary: '',
                summary_de: '',
                content: '',
                content_de: '',
                tags: [],
                attachments: [],
                featured_image: '',
                featured_color: 'transparent',
                keywords: '',
                keywords_de: '',
                is_internal: 0,
                next: []
            },
            categories: [],
            featured: '',
            newFeatured: null,
            categoriesTree: [],
            deleteAttachDlg: false,
            selectedAttachment: null,
            files: [],
            files_de: [],
            stepTypes: [
                {id: 1, name: this.$store.state.lang.lang_map.kb.buttons},
                {id: 2, name: this.$store.state.lang.lang_map.kb.links},
                {id: 3, name: this.$store.state.lang.lang_map.kb.select},
                {id: 4, name: this.$store.state.lang.lang_map.kb.radios},
            ],
            stepType: 1,
            articles: []
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

        this.getCategoriesTree();
        this.getArticle();
        this.getArticles();


    },
    watch: {
        newFeatured(val) {
            if (this.newFeatured !== null) {
                this.featured = URL.createObjectURL(this.newFeatured);
            }
        }
    },
    created() {
        this.dGetTags = _.debounce(this.getTags, 1500);
    },
    methods: {
        invertColor(hex) {
            return Helper.invertColor(hex);
        },
        getTags() {
            this.$store.dispatch('Tags/getTagList');
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
        getArticle() {
            if (this.$route.params.id) {
                axios.get(`/api/kb/article/${this.$route.params.id}`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        let that = this;
                        this.article = response.data;
                        Array.from(this.article.categories).forEach(function (category) {
                            that.categories.push(category.id);
                        });
                        this.featured = this.article.featured_image ? this.article.featured_image.link : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAoMBgDTD2qgAAAAASUVORK5CYII=';
                        if (this.$refs.fileupload) {
                            this.$refs.fileupload.reset();
                        }
                        if (this.$refs.fileupload_de) {
                            this.$refs.fileupload_de.reset();
                        }
                        this.$forceUpdate();
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            }
        },
        fileIcon(filename) {
            let icons = {
                avi: 'mdi-file-video',
                doc: 'mdi-file-word',
                docx: 'mdi-file-word',
                gif: 'mdi-file-image',
                jpg: 'mdi-file-image',
                jpeg: 'mdi-file-image',
                mov: 'mdi-file-video',
                mp3: 'mdi-file-music',
                mp4: 'mdi-file-video',
                mpeg: 'mdi-file-video',
                pdf: 'mdi-file-pdf',
                png: 'mdi-file-image',
                ppt: 'mdi-file-powerpoint',
                pptx: 'mdi-file-powerpoint',
                xls: 'mdi-file-excel',
                xlsx: 'mdi-file-excel',
                zip: 'mdi-archive',

            }
            let ext = filename.split('.').pop();
            if (icons[ext]) {
                return icons[ext];
            }

            return 'mdi-file';
        },
        saveArticle(redirect = false) {
            let formData = new FormData();
            for (let key in this.article) {
                if (key !== 'files' && key !== 'tags' && key !== 'categories' && key !== 'attachments' && key !== 'next') {
                    if (this.article[key] != null) {
                        formData.append(key, this.article[key]);
                    }
                }
            }
            formData.append('tags', JSON.stringify(this.article.tags));
            formData.append('categories', JSON.stringify(this.categories));
            formData.append('next', JSON.stringify(this.article.next));
            formData.append('step_type', JSON.stringify(this.stepType));

            if (this.newFeatured) {
                formData.append('files[]', this.newFeatured);
                formData.append('file_infos[]', JSON.stringify({type: 'kb_featured', order: 0}));
            }
            if (this.files && this.files.length > 0) {
                Array.from(this.files).forEach(function (file) {
                    formData.append('files[]', file);
                    formData.append('file_infos[]', JSON.stringify({type: 'kb_document', lang: 'en'}));
                });
            }

            if (this.files_de && this.files_de.length > 0) {
                Array.from(this.files_de).forEach(function (file) {
                    formData.append('files[]', file);
                    formData.append('file_infos[]', JSON.stringify({type: 'kb_document', lang: 'de'}));
                });
            }

            if (this.article.id) {
                formData.append('_method', 'PUT');

                axios.post(`/api/kb/article/${this.article.id}`, formData, {'content-type': 'multipart/form-data'}).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getArticle();
                        this.getArticles();

                        this.snackbarMessage = this.langMap.kb.article_updated;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            } else {
                axios.post('/api/kb/article', formData, {'content-type': 'multipart/form-data'}).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getArticle();
                        this.getArticles();

                        this.snackbarMessage = this.langMap.kb.article_created;
                        this.actionColor = 'success'
                        this.snackbar = true;

                        this.$router.push(`/${this.$route.params.alias}`);
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            }

            if (redirect) {
                this.openCategory();
            }
        },
        removeTag(item) {
            this.article.tags.splice(this.article.tags.indexOf(item), 1)
        },
        deleteAttachment(attachment) {
            this.selectedAttachment = attachment;
            this.deleteAttachDlg = true;
        },
        removeAttachment() {
            this.deleteAttachDlg = false;
            axios.delete(`/api/file/${this.selectedAttachment.id}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.article.attachments.splice(this.article.attachments.indexOf(this.selectedAttachment), 1);
                    this.selectedAttachment = null;

                    this.snackbarMessage = this.langMap.kb.attachment_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        openCategory() {
            if (parseInt(localStorage.getItem('kb_category'))) {
                this.$router.push(`/${this.$route.params.alias}?category=` + localStorage.getItem('kb_category'));
            } else {
                this.$router.push(`/${this.$route.params.alias}`);
            }
        },
        download(url) {
            window.open(url, '_blank');
        },
        onFileChange(files) {
            this.files = files;
        },
        onFileChangeDe(files) {
            this.files_de = files;
        },
        unlinkStep(item) {
            this.article.next.splice(this.article.next.indexOf(item), 1);
            this.$forceUpdate();
        },
        addStep() {
            this.article.next.push({id: 0, name: ''});
            this.$forceUpdate();
        },
        getArticles() {
            if (this.$route.params.id) {
                axios.get(`/api/kb/articles/all`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.articles = Array.from(response.data);
                        this.articles.unshift({
                            id: '--',
                            name: this.langMap.kb.link_existing_articles,
                            header: this.langMap.kb.link_existing_articles
                        });
                        this.articles.unshift({id: 0, name: this.langMap.kb.create_new_step});
                        this.$forceUpdate();
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            }
        },
    }
}
</script>
