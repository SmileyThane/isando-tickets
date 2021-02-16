<template>
    <v-menu
        :close-on-content-click="false"
        :nudge-width="200"
        offset-y
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                :icon="!selectedTags.length"
                x-small
                fab
                :color="color"
                v-bind="attrs"
                v-on="on"
            >
                <v-icon
                    v-if="selectedTags.length"
                    style="color: white"
                >mdi-tag</v-icon>
                <v-icon v-else center>
                    mdi-tag-outline
                </v-icon>
            </v-btn>
        </template>
        <v-card
            max-width="400"
            class="d-flex pa-2"
        >
            <template>
                <v-combobox
                    v-bind="$attrs"
                    v-on="$listeners"
                    v-model="selectedTags"
                    :hide-no-data="!form.name"
                    :search-input.sync="form.name"
                    hide-selected
                    return-object
                    label="Tags"
                    placeholder="Choose tags"
                    item-text="name"
                    item-id="id"
                    :items="$store.getters['Tags/getTags']"
                    multiple
                    small-chips
                    clearable
                    @input="onUpdate"
                >
                    <template v-slot:no-data>
                        <v-list-item>
                            <span class="subheading d-flex justify-start mr-auto">
                                Create&nbsp;
                                <v-chip
                                    label
                                    small
                                    :color="form.color"
                                    :text-color="invertColor(form.color)"
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
                            :text-color="item.color ? invertColor(item.color) : '#ffffff'"
                        >
                        <span
                            v-if="item.id"
                        >
                            {{ item.name }}
                            <v-icon
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
                            :text-color="invertColor(item.color)"
                        >
                            {{ item.name }}
                        </v-chip>
                    </template>
                </v-combobox>
            </template>
        </v-card>
    </v-menu>
</template>

<script>

import _ from 'lodash';
import * as Helper from '../helper';

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
        }
    },
    data: () => {
        return {
            isLoadingTags: false,
            form: {
                name: '',
                color: Helper.genRandomColor()
            },
            colorMenu: false
        };
    },
    created() {
        this.debounceGetTags = _.debounce(this.__getTags, 1000);
    },
    mounted() {
        // this.debounceGetTags();
    },
    methods: {
        __getTags() {
            this.$store.dispatch('Tags/getTagList');
        },
        __createTag(name) {
            if (typeof name !== 'string') return;
            this.$store.dispatch('Tags/createTag', this.form)
                .then(createdTag => {
                    this.selectedTags = this.selectedTags.map(tag => {
                        if (typeof tag === 'string' && tag === createdTag.name) {
                            return createdTag;
                        }
                        return tag;
                    });
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
        genRandomColor() {
            return Helper.genRandomColor();
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
        invertColor(hex, bw = true) {
            return Helper.invertColor(hex, bw)
        }
    },
    computed: {
        selectedTags: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('input', val);
            }
        }
    }
};
</script>
