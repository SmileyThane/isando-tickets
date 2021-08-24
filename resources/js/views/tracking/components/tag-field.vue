<template>
    <v-combobox
        v-bind="$attrs"
        v-on="$listeners"
        v-model="selectedTags"
        :hide-no-data="!form.name"
        :search-input.sync="form.name"
        return-object
        :label="langMap.tracking.tag_btn.tags"
        :placeholder="langMap.tracking.tag_btn.choose_tags"
        item-text="name"
        item-id="id"
        :items="$store.getters['Tags/getTags']"
        multiple
        small-chips
        dense
        @input="onUpdate"
        @focus="isFocused = true"
        @focusout="isFocused = false"
        ref="tagSelector"
        :style="{ width: `${width}px` }"
        :clearable="isFocused"
        class="mt-3 tag-field"
    >
        <template v-slot:no-data>
            <v-list-item>
                <span class="subheading d-flex justify-start mr-auto">
                    {{ langMap.tracking.tag_btn.create }}&nbsp;
                    <v-chip
                        label
                        small
                        :color="form.color"
                        :text-color="$helpers.color.invertColor(form.color)"
                        @click.stop.prevent="onClickNewTag"
                    >
                        {{ form.name }}
                    </v-chip>
                </span>

                <v-card
                    :style="{ background: form.color, width: '30px', height: '30px' }"
                    class="ma-0 pa-0 d-flex justify-end ml-auto"
                    solo
                    @click="colorMenu = !colorMenu"
                >
                    <template v-slot:default>
                        <v-menu v-model="colorMenu" top nudge-bottom="105" nudge-left="16" :close-on-content-click="false">
                            <template v-slot:activator="{ on }">
                                <div :style="switchColor" v-on="on" />
                            </template>
                            <v-card>
                                <v-card-text class="pa-0">
                                    <v-color-picker v-model="form.color" flat />
                                </v-card-text>
                            </v-card>
                        </v-menu>
                    </template>
                </v-card>
            </v-list-item>
        </template>
        <template v-slot:selection="{ attrs, item, parent, selected }">
            <v-chip
                v-if="item.name === Object(item).name"
                v-bind="attrs"
                :input-value="selected"
                label
                small
                :color="item.color"
                :text-color="item.color ? $helpers.color.invertColor(item.color) : '#ffffff'"
            >
                <span
                    v-if="item.id"
                >
                    {{ item.name }}
                    <v-icon
                        v-if="false"
                        small
                        @click="parent.selectItem(item)"
                    >
                        mdi-close-circle-outline
                    </v-icon>
                </span>
                <v-progress-circular
                    v-if="!item.id"
                    indeterminate
                    :size="20"
                    :color="color"
                ></v-progress-circular>
            </v-chip>
        </template>
        <template v-slot:item="{ parent, item, on, attrs }">
            <v-chip
                :color="item.color"
                label
                small
                :text-color="$helpers.color.invertColor(item.color)"
            >
                {{ item.name }}
            </v-chip>
        </template>
    </v-combobox>
</template>

<style scoped>
>>>.v-select__slot {
    max-width: 100%;
    overflow: hidden;
}
>>>.v-select__selections {
    flex-wrap: nowrap;
    flex-grow: 1;
    overflow: hidden;
}
>>>.v-chip__content {
    overflow: hidden;
}
>>>.v-chip--label {
    min-width: 35px;
}
>>>.v-input--is-disabled .v-input__append-inner {
    display: none;
}
>>> .tag-field * {
    border: none !important;
}
>>> .v-input--is-disabled.v-text-field>.v-input__control>.v-input__slot:after,
.v-input--is-disabled.v-text-field>.v-input__control>.v-input__slot:before {
    border: none !important;
}
>>> *:not(.v-icon) {
 font-size: 12px !important;
}
</style>

<script>

import _ from 'lodash';

export default {
    props: {
        value: {
            required: true
        },
        color: {
            type: String,
            default: '#ffffff'
        },
        onChoosable: {
            type: Function
        },
        width: {
            required: false,
            default: 150
        }
    },
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            isLoadingTags: false,
            form: {
                name: '',
                color: this.$helpers.color.genRandomColor(),
                langs: []
            },
            colorMenu: false,
            isFocused: false
        };
    },
    created() {
        this.debounceGetTags = _.debounce(this.__getTags, 1000);
        this.debounceGetLanguages = _.debounce(this.__getLanguages, 1000);
    },
    mounted() {
        // this.debounceGetTags();
        // this.debounceGetLanguages();
    },
    methods: {
        __getTags() {
            this.$store.dispatch('Tags/getTagList');
        },
        __getLanguages() {
            this.$store.dispatch('Languages/getLanguageList');
        },
        __createTag(name) {
            if (typeof name !== 'string') return;
            return this.$store.dispatch('Tags/createTag', this.form)
                .then(createdTag => {
                    this.selectedTags = this.selectedTags.map(tag => {
                        if (typeof tag === 'string' && tag === createdTag.name) {
                            return createdTag;
                        }
                        return tag;
                    });
                    return createdTag;
                });
        },
        onUpdate($event) {
            $event.map(item => {
                if (typeof item === 'string') {
                    this.__createTag(item);
                    this.searchTag = '';
                }
            });
        },
        switchColor() {
            const { form: { color }, colorMenu } = this
            return {
                backgroundColor: color,
                cursor: 'pointer',
                height: '30px',
                width: '30px',
                borderRadius: colorMenu ? '50%' : '4px',
                transition: 'border-radius 200ms ease-in-out'
            }
        },
        onClickNewTag() {
            this.__createTag(this.form.name)
                .then(tag => {
                    this.form = {
                        name: '',
                        color: this.$helpers.color.genRandomColor()
                    };
                    this.debounceGetTags();
                    this.selectedTags.push(tag);
                });
        }
    },
    computed: {
        selectedTags: {
            get() {
                return this.value.map(item => {
                    if (typeof item !== 'string') {
                        if (item.translates) {
                            const foundTranslation = item.translates.find(i => i.lang === this.currentLang);
                            item.name = foundTranslation ? foundTranslation.name : item.name;
                        }
                    }
                    return item;
                });
            },
            set(val) {
                this.$emit('input', val);
            }
        },
        currentLang: function () {
            return this.$store.getters['getLang'].locale;
        },
        languages: function () {
            const languages = this.$store.getters['Languages/getLanguages'];
            const currentLang = this.$store.getters['getLang'];
            const filteredLangs = languages.filter(i => i.id !== currentLang.id);
            // this.form.langs = filteredLangs.map(lang => ({ id: lang.id, name: lang.name, text: '' }));
            return filteredLangs;
        }
    }
};
</script>
