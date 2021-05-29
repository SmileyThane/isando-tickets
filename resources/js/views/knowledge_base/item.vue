<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>
        <v-card outlined ref="card">
            <v-img v-if="article.featured_image" width="100%" :src="article.featured_image.link"/>
            <v-card-title>
                {{ $helpers.i18n.localized(article) }}
            </v-card-title>
            <v-card-text>
                <div v-if="article.tags">
                    <h4 class="mb-2">{{ langMap.kb.tags}}</h4>
                    <v-chip v-for="tag in article.tags" :key="tag.id" label small class="mr-2" v-text="tag.name" :color="tag.color" :text-color="invertColor(tag.color)"/>
                    <v-spacer>&nbsp;</v-spacer>
                </div>

                <div v-if="$helpers.i18n.localized(article, 'summary')">
                    <p ref="summary" class="summary pa-3">{{ $helpers.i18n.localized(article, 'summary') }}</p>
                    <v-spacer>&nbsp;</v-spacer>
                </div>

                <div class="content" v-html="$helpers.i18n.localized(article, 'content') "/>

                <div v-if="article.attachments && article.attachments.length > 0">
                    <v-spacer>&nbsp;</v-spacer>
                    <hr/>
                    <h4 class="mb-2">{{ langMap.kb.attachments}}</h4>

                    <v-chip-group column>
                        <v-chip v-for="attachment in article.attachments" v-if="attachment.service_info && attachment.service_info.lang == $helpers.i18n.getCurrentLocale()" :key="attachment.id" label outlined :color="themeBgColor" class="mr-2" @click="download(attachment.link)">
                            <v-icon :color="themeBgColor" left v-text="fileIcon(attachment.name)" />
                            {{ attachment.name}}
                        </v-chip>
                    </v-chip-group>
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
            article: []
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
        invertColor(hex) {
            return this.$helpers.color.invertColor(hex);
        },
        getArticle() {
            axios.get(`/api/kb/article/${this.$route.params.id}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.article = response.data;

                    if (this.article.featured_color && this.article.featured_color !== 'transparent' && this.article.featured_color !== '#00000000') {
                        if (this.$refs.card) {
                            this.$refs.card.$el.style.borderColor = this.article.featured_color;
                            this.$refs.card.$el.style.borderSize = '2px';
                        }
                        if (this.$refs.summary) {
                            this.$refs.summary.style.backgroundColor = this.article.featured_color;
                            this.$refs.summary.style.borderColor = this.article.featured_color;
                        }
                    }
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },
        openCategory() {
            if (parseInt(localStorage.getItem('kb_category'))) {
                this.$router.push('/knowledge_base?category='+localStorage.getItem('kb_category'));
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
