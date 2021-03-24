<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>
        <v-card outlined>
            <v-card-title>
                {{ localized(article) }}
            </v-card-title>
            <v-card-text>
                <div v-if="article.category">
                    <h3>{{ localized(article.category) }}</h3>
                    <v-spacer>&nbsp;</v-spacer>
                </div>

                <div v-if="article.tags && article.tags.length > 0">
                    <h4 class="mb-2">{{ langMap.kb.tags}}</h4>
                    <v-chip v-for="tag in article.tags" :key="tag.id" label small class="mr-2" v-text="tag.name" :color="tagColor(tag.id)" :text-color="invertColor(tagColor(tag.id))"/>
                    <v-spacer>&nbsp;</v-spacer>
                </div>

                <div v-if="localized(article, 'summary')">
                    <p class="summary pa-3 ">{{ localized(article, 'summary') }}</p>
                    <v-spacer>&nbsp;</v-spacer>
                </div>

                <div class="content" v-html="localized(article, 'content') "/>

                <div v-if="article.attachments && article.attachments.length > 0">
                    <v-spacer>&nbsp;</v-spacer>
                    <h4 class="mb-2">{{ langMap.kb.attachments}}</h4>

                    <v-chip v-for="attachment in article.attachments" :key="attachment.id" outlined label :color="themeBgColor" class="mr-2" @click="download(attachment.link)">
                        <v-icon left v-text="fileIcon(attachment.filepath)" :color="themeBgColor" />
                        <v-subheader v-text="attachment.name" /> <br/>
                        <small>{{ attachment.filepath }}</small>
                    </v-chip>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn text :color="themeBgColor" v-text="langMap.kb.back_to_category" @click="openCategory" />
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<style scoped>
>>> .summary {
    border: 1px solid #aaaaaa;
    background: #fafafa;
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
            article: [],
            tagColors: [],
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
        getArticle() {
            axios.get(`/api/kb/article/${this.$route.params.id}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.article = response.data;
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
        }
    }
}
</script>
