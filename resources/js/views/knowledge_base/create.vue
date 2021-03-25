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
                            <v-col cols="12">
                                <v-expansion-panels>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>English</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-text-field v-model="article.name" :label="langMap.main.name" :placeholder="langMap.main.name" hide-details single-line :color="themeBgColor"/>
                                            <v-text-field v-model="article.summary" :label="langMap.kb.summary" hide-details single-line :color="themeBgColor"/>
                                            <v-spacer>&nbsp;</v-spacer>
                                            <tinymce ref="content" aria-rowcount="40" v-model="article.content" :placeholder="langMap.kb.article_content" />
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header>Deutsch</v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-text-field v-model="article.name_de" :label="langMap.main.name" hide-details single-line :color="themeBgColor"/>
                                            <v-text-field v-model="article.summary_de" :label="langMap.main.summary" hide-details single-line :color="themeBgColor"/>
                                            <v-spacer>&nbsp;</v-spacer>
                                            <tinymce ref="content_de" aria-rowcount="40" v-model="article.content_de" :placeholder="langMap.kb.article_content" />
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-col>
                            <v-col cols="6">
                                <label>{{ langMap.kb.article_category }}</label>
                                <perfect-scrollbar>
                                <v-treeview activatable open-all :active="article._active" :items="categoriesTree" item-key="id" :color="themeBgColor" @update:active="refreshArticle">
                                    <template v-slot:prepend="{ item }">
                                        <v-icon>mdi-folder</v-icon>
                                    </template>
                                    <template v-slot:label="{ item }">
                                        {{ localized(item) }}
                                    </template>
                                </v-treeview>
                                </perfect-scrollbar>
                            </v-col>
                            <v-col cols="6">
                                <label>{{ langMap.kb.tags }}</label>
                                <v-combobox v-model="article.tags" :items="tags" item-value="id" item-text="name" hide-selected multiple chips :label="langMap.tracking.tag_btn.choose_tags" :color="themeBgColor">
                                    <template v-slot:selection="{ attrs, item, parent, selected }">
                                        <v-chip v-if="item.id" v-bind="attrs" :color="item.color" :text-color="invertColor(item.color)" label class="ml-2" close @click:close="removeTag(item)">
                                            {{ item.name }}
                                        </v-chip>
                                        <v-chip v-else label class="ml-2" close @click:close="removeTag(item)">
                                            {{ item }}
                                        </v-chip>
                                    </template>
                                </v-combobox>

                                <v-spacer>&nbsp;</v-spacer>
                                <label>{{ langMap.kb.attachments }}</label>

                                <v-chip-group column>
                                    <v-chip v-for="attachment in article.attachments" :key="attachment.id" label outlined :color="themeBgColor" class="mr-2" close @click:close="deleteAttachment(attachment)">
                                        <span @click="download(attachment.link)">
                                            <v-icon :color="themeBgColor" left v-text="fileIcon(attachment.name)" />
                                            {{ attachment.name}}
                                        </span>
                                    </v-chip>
                                </v-chip-group>

                                <v-spacer>&nbsp;</v-spacer>
                                <v-file-input
                                    ref="fileupload"
                                    chips
                                    multiple
                                    :label="langMap.kb.create_attachment"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    prepend-icon="mdi-paperclip"
                                    :show-size="1024"
                                    counter
                                    v-on:change="onFileChange"
                                >
                                    <template v-slot:selection="{ file, index, text }">
                                        <v-chip :color="themeBgColor" label outlined>
                                            <v-icon :color="themeBgColor" left v-text="fileIcon(file.name)" />
                                            {{ text }}
                                        </v-chip>
                                    </template>
                                </v-file-input>

                            </v-col>
                        </v-row>
                </v-card-text>
                <v-card-actions>
                    <v-btn text v-text="langMap.main.cancel" @click="openCategory" />
                    <v-btn text :color="themeBgColor" v-text="langMap.main.save" @click="saveArticle(false)" />
                    <v-btn text :color="themeBgColor" v-text="langMap.kb.save_and_close" @click="saveArticle(true)" />
                </v-card-actions>
            </v-card>
        </v-form>
    </v-container>
</template>

<style scoped>
>>>.ps {
    max-height: 20em;
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
            article: {
                id: null,
                category_id: null,
                _active: [],
                name: '',
                name_de: '',
                summary: '',
                summary_de: '',
                content: '',
                content_de: '',
                tags: [],
                attachments: [],
                files: []
            },
            categoriesTree: [],
            tags: [],
            deleteAttachDlg: false,
            selectedAttachment: null
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
        this.getCategoriesTree();
        this.getArticle();

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
        invertColor(hex) {
            return Helper.invertColor(hex);
        },
        getTags() {
            axios.get('/api/tags').then(response => {
                response = response.data;
                if (response.success === true) {
                    this.tags = response.data;
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
        getArticle() {
            if (this.$route.params.id) {
                axios.get(`/api/kb/article/${this.$route.params.id}`).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.article = response.data;
                        this.article._active = [this.article.category_id],
                        this.article.files = [];
                        this.$refs.fileupload.reset();
                        this.$forceUpdate();
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.errorType = 'error';
                        this.alert = true;
                    }
                });
            }
        },
        refreshArticle(category) {
            this.article.category_id = category.length > 0 ? category[0] : null;
            this.$forceUpdate();
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
                if (key !== 'files' && key !== 'tags') {
                    if (this.article[key] != null) {
                        formData.append(key, this.article[key]);
                    }
                }
            }
            formData.append('tags', JSON.stringify(this.article.tags));
            if (this.article.files && this.article.files.length > 0) {
                Array.from(this.article.files).forEach(file => formData.append('files[]', file));
            }

            if (this.article.id) {
                formData.append('_method', 'PUT');

                axios.post(`/api/kb/article/${this.article.id}`, formData, {'content-type': 'multipart/form-data'}).then(response => {
                    response = response.data;
                    if (response.success === true) {
                        this.getArticle();

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

                        this.snackbarMessage = this.langMap.kb.article_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
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
            if (this.article.category_id) {
                this.$router.push(`/knowledge_base?category=${this.article.category_id}`);
            } else {
                this.$router.push('/knowledge_base');
            }
        },
        download(url) {
            window.open(url, '_blank');
        },
        onFileChange(files) {
            this.article.files = files;
        }

    }
}
</script>
