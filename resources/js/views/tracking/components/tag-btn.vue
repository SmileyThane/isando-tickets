<template>
    <v-menu
        :close-on-content-click="false"
        :nudge-width="200"
        offset-x
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
            style="max-width: 400px"
        >
            <template>
                <v-combobox
                    v-bind="$attrs"
                    v-on="$listeners"
                    v-model="selectedTags"
                    :hide-no-data="!search"
                    :search-input.sync="search"
                    hide-selected
                    return-object
                    label="Choose tags"
                    item-text="name"
                    item-id="name"
                    multiple
                    small-chips
                    solo
                    clearable
                    @input="onUpdate"
                >
                    <template v-slot:no-data>
                        <v-list-item>
                            <span class="subheading">Create&nbsp;</span>
                            <v-chip
                                :color="color"
                                label
                                small
                                outlined
                            >
                                {{ search }}
                            </v-chip>
                        </v-list-item>
                    </template>
                    <template v-slot:selection="{ attrs, item, parent, selected }">
                        <v-chip
                            v-if="item.name === Object(item).name"
                            v-bind="attrs"
                            :color="color"
                            :input-value="selected"
                            label
                            small
                            outlined
                        >
                        <span v-if="item.id">
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
                            :color="color"
                            label
                            small
                            style="color: white"
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
            search: '',
            isLoadingTags: false
        };
    },
    created() {
        this.debounceGetTags = _.debounce(this.__getTags, 2000);
    },
    mounted() {
        // this.debounceGetTags();
    },
    methods: {
        __getTags() {
            if (this.isLoadingTags) return;
            this.isLoadingTags = true;
            return axios.get('/api/tags')
                .then(({ data }) => {
                    if (data.success) {
                        this.tags = data.data;
                        return data.data;
                    }
                    this.isLoadingTags = false;
                    return null;
                })
                .catch(e => (this.debounceGetTags()));
        },
        __createTag(name) {
            if (typeof name !== 'string') return;
            return axios.post('/api/tags', {name})
                .then(({ data }) => {
                    if (data.success) {
                        const createdTag = data.data;
                        this.debounceGetTags();
                        this.selectedTags = this.selectedTags.map(tag => {
                            if (typeof tag === 'string' && tag === createdTag.name) {
                                return createdTag;
                            }
                            return tag;
                        });
                        return createdTag;
                    }
                    return null;
                })
                .catch(e => ( console.log(e) ));
        },
        onUpdate($event) {
            $event.map(item => {
                if (typeof item === 'string') {
                    this.__createTag(item);
                    this.searchTag = '';
                }
            });
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
