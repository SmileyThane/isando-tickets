<template>
    <v-container fluid>
        <v-snackbar v-model="snackbar" :bottom="true" :color="actionColor" :right="true">
            {{ snackbarMessage }}
        </v-snackbar>
        <v-row>
            <v-col v-for="card in cards" :key="card.id" cols="6">
            </v-col>
            <v-col v-if="!checkRoleByIds([6, 101])" cols="6">
                <v-card dense outlined>
                    <v-toolbar :color="themeBgColor" dark dense flat>
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.kb.create_knowledge }}</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                        <v-text-field :color="themeBgColor" v-model="cardForm.icon" :items="cardIcons" :label="langMap.main.icon" :append-outer-icon="cardForm.icon" />
                        <v-expansion-panels v-model="createCardPanels" multiple>
                            <v-expansion-panel v-for="language in languages" :key="language.id">
                                <v-expansion-panel-header>{{ language.name }}</v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-text-field :color="themeBgColor" v-model="cardForm.name[language.locale]" :label="langMap.kb.knowledge_name" />
                                    <tiptap-vuetify v-model="cardForm.details[language.locale]" :extensions="extensions" :placeholder="langMap.kb.knowledge_details" />
                                    
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="grey darken-1" text @click="cancelCreateCard">{{ langMap.main.cancel }}</v-btn>
                        <v-btn :color="themeBgColor" :style="`color: ${themeFgColor};`" dark @click="createCard">{{ langMap.main.create }}</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>


<script>
import EventBus from '../../components/EventBus';
import {
    Blockquote,
    Bold,
    BulletList,
    Code,
    HardBreak,
    Heading,
    History,
    HorizontalRule,
    Image,
    Italic,
    Link,
    ListItem,
    OrderedList,
    Paragraph,
    Strike,
    TiptapVuetify,
    Underline
} from 'tiptap-vuetify';

export default {
    components: {
        TiptapVuetify
    },
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            errors: [],
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            extensions: [
                History,
                Blockquote,
                Link,
                Underline,
                Strike,
                Italic,
                ListItem,
                BulletList,
                OrderedList,
                [Heading, {
                    options: {
                        levels: [1, 2, 3]
                    }
                }],
                Bold,
                Code,
                HorizontalRule,
                Paragraph,
                Image,
                HardBreak
            ],
            languages: [],
            cardIcons: [],
            cards: [],
            cardForm: {
                id: '',
                icon: '',
                name: [],
                details: []
            },
            createCardPanels: []
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

        this.getLanguages();
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
        getLanguages() {
            axios.get('/api/lang/all').then(response => {
                response = response.data;
                if (response.success === true) {
                    this.languages = response.data;

                    //this.cancelCreateCard();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });

        },
        cancelCreateCard() {
            this.cardForm = {
                id: '',
                icon: '',
                name: [],
                details: []
            };
            this.createCardPanels = [];
        },
        createCard() {

        },
    }
}
</script>
