<template>
    <v-container fluid>
        <v-snackbar
            :bottom="true"
            :right="true"
            v-model="snackbar"
            :color="actionColor"
        >
            {{ snackbarMessage }}
        </v-snackbar>

        <template>
            <v-tabs>
                <v-tab>Tags</v-tab>
            </v-tabs>

            <v-tabs-items v-model="tab">
                <v-tab-item :key="0">
                    <v-card flat>
                        <v-toolbar flat>
                            <v-dialog
                                v-model="dialog"
                                width="500"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        :color="themeColor"
                                        style="color: white"
                                        v-bind="attrs"
                                        v-on="on"
                                    >
                                        Create tag
                                    </v-btn>
                                </template>

                                <v-card>
                                    <v-card-title class="headline">
                                        Create new tag
                                    </v-card-title>

                                    <v-card-text>
                                        <v-text-field
                                            label="Name"
                                            v-model="form.name"
                                            required
                                        ></v-text-field>
                                        <v-text-field
                                            v-model="form.color"
                                            hide-details
                                            class="ma-0 pa-0"
                                            solo
                                            label="Color"
                                            required
                                        >
                                            <template v-slot:append>
                                                <v-menu
                                                    v-model="colorMenuCreate"
                                                    top
                                                    nudge-bottom="105"
                                                    nudge-left="16"
                                                    :close-on-content-click="false"
                                                >
                                                    <template v-slot:activator="{ on }">
                                                        <div
                                                            :style="{
                                                                backgroundColor: form.color,
                                                                cursor: 'pointer',
                                                                height: '30px',
                                                                width: '30px',
                                                                borderRadius: colorMenuCreate ? '50%' : '4px',
                                                                transition: 'border-radius 200ms ease-in-out'
                                                            }"
                                                            v-on="on"
                                                        />
                                                    </template>
                                                    <v-card>
                                                        <v-card-text
                                                            class="pa-0"
                                                        >
                                                            <v-color-picker
                                                                v-model="form.color"
                                                                flat
                                                            />
                                                        </v-card-text>
                                                    </v-card>
                                                </v-menu>
                                            </template>
                                        </v-text-field>
                                    </v-card-text>

                                    <v-divider></v-divider>

                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn
                                            color="error"
                                            text
                                            @click="resetForm(); dialog = false"
                                        >
                                            Cancel
                                        </v-btn>
                                        <v-btn
                                            color="success"
                                            text
                                            @click="createTag(); dialog = false"
                                        >
                                            Create
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-dialog>
                        </v-toolbar>
                        <v-card-text>
                            <v-data-table
                                dense
                                :headers="headers"
                                :items="$store.getters['Tags/getTags']"
                                :items-per-page="15"
                                class="elevation-1"
                            >
                                <template v-slot:item.name="props">
                                    <v-edit-dialog
                                        @save="save(props.item)"
                                        @cancel="save(props.item)"
                                        @open="save(props.item)"
                                        @close="save(props.item)"
                                    >
                                        {{ props.item.name }}
                                        <template v-slot:input>
                                            <v-text-field
                                                v-model="props.item.name"
                                                label="Name"
                                                hint="Name"
                                                single-line
                                                counter
                                            ></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </template>
                                <template v-slot:item.color="props">
                                    <v-menu
                                        v-model="colorMenu[props.item.id]"
                                        top
                                        nudge-bottom="105"
                                        nudge-left="16"
                                        :close-on-content-click="false"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <div
                                                v-on="on"
                                                :style="{
                                                    backgroundColor: props.item.color,
                                                    cursor: 'pointer',
                                                    height: '30px',
                                                    width: '30px',
                                                    borderRadius: colorMenu[props.item.id] ? '50%' : '4px',
                                                    transition: 'border-radius 200ms ease-in-out'
                                                }"
                                            />
                                        </template>
                                        <v-card>
                                            <v-card-text class="pa-0">
                                                <v-color-picker
                                                    v-model="props.item.color"
                                                    flat
                                                    @input="save(props.item)"
                                                />
                                            </v-card-text>
                                        </v-card>
                                    </v-menu>

                                </template>
                                <template v-slot:item.actions="props">
                                    <v-btn
                                        icon
                                        :color="themeColor"
                                        @click="removeTag(props.item.id)"
                                    >
                                        <v-icon>mdi-delete</v-icon>
                                    </v-btn>
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-tab-item>
            </v-tabs-items>
        </template>

    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";
import _ from 'lodash';
import TagBtn from './components/tag-btn';

export default {
    components: {TagBtn},
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeColor: this.$store.state.themeColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
            tab: 0,
            headers: [
                {
                    text: 'Tag name',
                    align: 'start',
                    sortable: true,
                    value: 'name',
                },
                {
                    text: 'Color',
                    sortable: false,
                    value: 'color',
                },
                {
                    text: 'Actions',
                    sortable: false,
                    value: 'actions',
                }
            ],
            searchTag: null,
            dialog: false,
            form: {
                name: '',
                color: '#' + Math.floor(Math.random()*16777215).toString(16).substr(0, 6)
            },
            colorMenuCreate: false,
            colorMenu: {}
        }
    },
    created() {
        this.debounceGetTags = _.debounce(this.__getTags, 1000);
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
        this.debounceGetTags();
    },
    methods: {
        __getTags() {
            this.$store.dispatch('Tags/getTagList', { search: this.searchTag });
        },
        createTag() {
            this.$store.dispatch('Tags/createTag', this.form)
                .then(tag => {
                    if (tag) {
                        this.snackbarMessage = 'Tag created successfully';
                        this.actionColor = 'success'
                        this.snackbar = true;
                    }
                });
        },
        resetForm() {
            this.form = {
                name: '',
                color: '#' + Math.floor(Math.random()*16777215).toString(16).substr(0, 6)
            };
        },
        removeTag(tagId) {
            this.$store.dispatch('Tags/deleteTag', tagId)
                .then(result => {
                    console.log(result);
                    if (result) {
                        this.snackbarMessage = 'Tag deleted successfully';
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = 'Tag removal error';
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
        },
        save (item) {
            this.$store.dispatch('Tags/updateTag', item);
        },
        invertColor(hex, bw = true) {
            if (hex.indexOf('#') === 0) {
                hex = hex.slice(1);
            }
            // convert 3-digit hex to 6-digits.
            if (hex.length === 3) {
                hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
            }
            if (hex.length !== 6) {
                throw new Error('Invalid HEX color.');
            }
            var r = parseInt(hex.slice(0, 2), 16),
                g = parseInt(hex.slice(2, 4), 16),
                b = parseInt(hex.slice(4, 6), 16);
            if (bw) {
                // http://stackoverflow.com/a/3943023/112731
                return (r * 0.299 + g * 0.587 + b * 0.114) > 186
                    ? '#000000'
                    : '#FFFFFF';
            }
            // invert color components
            r = (255 - r).toString(16);
            g = (255 - g).toString(16);
            b = (255 - b).toString(16);
            // pad each with zeros and return
            return "#" + this.padZero(r) + this.padZero(g) + this.padZero(b);
        },
        padZero(str, len) {
            len = len || 2;
            let zeros = new Array(len).join('0');
            return (zeros + str).slice(-len);
        }
    },
    computed: {
        switchColorCreate() {
            const { form: { color }, colorMenuCreate } = this
            return
        }
    },
}
</script>
