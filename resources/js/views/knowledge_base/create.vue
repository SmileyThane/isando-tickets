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
                <div v-if="article.tags && article.tags.length > 0">
                    <v-chip v-for="tag in article.tags" :key="tag.id" label small class="mr-2" v-text="tag.name" :color="tagColor(tag.id)" :text-color="invertColor(tagColor(tag.id))"/>
                    <v-spacer>&nbsp;</v-spacer>
                </div>

                <div v-if="localized(article, 'summary')">
                    <p class="summary">{{ localized(article, 'summary') }}</p>
                    <v-spacer>&nbsp;</v-spacer>
                </div>

                <div class="content" v-html="localized(article, 'content') "/>

                <div v-if="article.attachments && article.attachments.length > 0">
                    <v-chip v-for="attachment in article.attachments" :key="attachment.id" label class="mr-2" v-text="attachment.name" @click="download(attachment.link)"/>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn text :color="themeFgColor" v-text="langMap.kb.back_to_category" @click="openCategory" />
            </v-card-actions>
        </v-card>
    </v-container>
</template>

<style scoped>
>>> .summary {
    border: 1px solid #aaaaaa;
    background: #bbb;
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
        invertColor(color) {
            return Helper.invertColor(color);
        },
        getArticle() {
            this.article = {
                id: 1,
                name: 'Article 1 (en)',
                name_de: 'Article 1 (de)',
                summary: 'Just summary about article (en)',
                summary_de: 'Just summary about article (de)',
                content: '<h1>HTML</h1> content <p>lorem ipsum</p> and other bla-bla-bla (en)',
                content_de: '<h1>HTML</h1> content <p>lorem ipsum</p> and other bla-bla-bla (en)',
                tags: [{id: 1, name: 'zzz'},{id: 3, name: 'something'}, {id:2, name: 'test'}],
                attachments: [{id: 1, name: 'something', link: '/zzz.pdf'},{id: 3, name: 'test', link: '/test.pdf'}, {id:2, name: 'doc', link: 'document.docx'}]
            }
        },
        openCategory() {
            this.$router.push(`/knowledge_base?category=${article.category_id}`);
        },
        download(url) {
            window.open(url, '_blank');
        }
    }
}
</script>
