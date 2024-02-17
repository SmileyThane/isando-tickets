<template>
    <v-container id="pdf-element" fluid>
        <v-snackbar v-model="snackbar"
                    :bottom="true"
                    :color="actionColor"
                    :right="true"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <v-row v-if="isArticleLoading" class="flex-row justify-center align-items-center onCenter">
            <v-progress-circular class="mt-4" indeterminate :value="20" color="#40613e"
            ></v-progress-circular>
        </v-row>
        <v-card v-else ref="card" outlined>
            <v-img v-if="article.featured_image"
                   :src="article.featured_image.link"
                   width="100%"
            />
            <v-card-title>
                {{ $helpers.i18n.localized(article) }}
            </v-card-title>
            <v-card-text>
                <div v-if="article.tags && article.tags.length > 0">
                    <h4 class="mb-2">
                        {{ langMap.kb.tags }}
                    </h4>
                    <v-chip
                        v-for="tag in article.tags"
                        :key="tag.id"
                        :color="tag.color"
                        :text-color="invertColor(tag.color)"
                        class="mr-2"
                        label
                        small
                        v-text="tag.name"
                    />
                    <v-spacer>&nbsp;</v-spacer>
                </div>
                <div v-if="article.summary && $helpers.i18n.localized(article, 'summary')">
                    <p ref="summary"
                       class="summary pa-3"
                    >
                        {{ $helpers.i18n.localized(article, 'summary') }}
                    </p>
                    <v-spacer>&nbsp;</v-spacer>
                </div>
                <div class="content"
                     v-html="$helpers.i18n.localized(article, 'content') === 'content' ? '' : $helpers.i18n.localized(article, 'content')"
                />
                <div v-if="article.attachments && article.attachments.length > 0">
                    <v-spacer>&nbsp;</v-spacer>
                    <hr/>
                    <h4 class="mb-2">{{ langMap.kb.attachments }}</h4>
                    <v-chip-group column>
                        <v-chip v-for="attachment in article.attachments"
                                v-if="attachment.service_info && attachment.service_info.lang === $helpers.i18n.getCurrentLocale()"
                                :key="attachment.id" :color="themeBgColor"
                                class="mr-2"
                                label
                                outlined
                                @click="download(attachment.link)"
                        >
                            <v-icon
                                :color="themeBgColor"
                                left
                                v-text="fileIcon(attachment.name)"
                            />
                            {{ attachment.name }}
                        </v-chip>
                    </v-chip-group>
                </div>
            </v-card-text>
            <v-card-actions
                style="display: flex; flex-direction: column; justify-content: center; align-items: flex-start;">
                <p>{{ langMap.main.useful_links }}</p>
                <v-btn :color="themeBgColor"
                       v-if="article.next.length > 0"
                       v-for="item in article.next"
                       key="id"
                       text
                       @click="next(item.id)"
                >
                    {{ item.name }}
                </v-btn>

                <v-btn :color="themeBgColor"
                       text
                       @click="exportToPdf"
                       style="margin-left: 0;"
                       v-if="$helpers.auth.checkKbPermissionsByType(getRouteAlias, kbPermissionsTypes.edit)"
                >{{ 'export to pdf' }}
                </v-btn>
                <v-btn :color="themeBgColor"
                       text
                       @click="back()"
                       v-text="langMap.kb.back_to_category"
                       style="margin-left: 0;"
                />
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<style scoped>
>>> .summary {
    border: 1px solid #aaaaaa;
    background: #fafafa;
}

>>> .v-card__text {
    font-family: "Roboto", sans-serif;
    font-size: .875rem;
    font-weight: 400;
    line-height: 1.375rem;
    letter-spacing: .0071428571em;
    color: #00000099;
}

.onCenter {
    width: 100%;
    height: 50vh;
    display: flex;
    align-items: flex-end;
}
</style>

<script>
import EventBus from '../../components/EventBus';
import html2pdf from "html2pdf.js";

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
            isArticleLoading: false,
            kbPermissionsTypes: {
                view: 'view',
                create: 'create',
                edit: 'edit',
                delete: 'delete',
            },
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
    computed: {
        getRouteAlias() {
            return this.$route.params.alias;
        },
    },
    methods: {
        exportToPdf() {
            html2pdf(document.getElementById("pdf-element"), {
                margin: 1,
                filename: `${this.$helpers.i18n.localized(this.article)}.pdf`,
                image: {
                    quality: 1
                },
                pagebreak: {
                    mode: ['avoid-all']
                }
            });
        },
        back() {
            window.history.back()
        },
        next(id) {
            let url = `/${this.$route.params.alias}/${id}`
            window.location.href = url;
        },
        invertColor(hex) {
            return this.$helpers.color.invertColor(hex);
        },
        getArticle() {
            this.isArticleLoading = true;
            axios.get(`/api/kb/article/${this.$route.params.id}?type=${this.$route.params.alias}`).then(response => {
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

                    this.isArticleLoading = false;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                    this.isArticleLoading = false;
                }
            }).catch(() => {
                this.isArticleLoading = false;
            });
        },
        openCategory() {
            if (parseInt(localStorage.getItem('kb_category'))) {
                this.$router.push(`/${this.$route.params.alias}?category=${localStorage.getItem('kb_category')}`);
            } else {
                this.$router.push(`/${this.$route.params.alias}`);
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
