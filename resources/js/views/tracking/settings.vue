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
                        <v-card-text>
                            <v-data-table
                                dense
                                :headers="headers"
                                :items="$store.getters['Tags/getTags']"
                                item-key="name"
                                class="elevation-1"
                            >
                                <template v-slot:item.name="props">
                                    <v-edit-dialog
                                        :return-value.sync="props.item.name"
                                        @save="save"
                                        @cancel="cancel"
                                        @open="open"
                                        @close="close"
                                    >
                                        {{ props.item.name }}
                                        <template v-slot:input>
                                            <v-text-field
                                                v-model="props.item.name"
                                                label="Edit"
                                                single-line
                                                counter
                                            ></v-text-field>
                                        </template>
                                    </v-edit-dialog>
                                </template>
                                <template v-slot:item.actions="props">
                                    actions
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

export default {
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
                    text: 'Actions',
                    align: 'start',
                    sortable: false,
                    value: 'actions',
                }
            ],
            searchTag: null
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
        removeTag(tagId) {
            this.$store.dispatch('Tags/deleteTag', tagId);
        },
        save () {
            this.snack = true
            this.snackColor = 'success'
            this.snackText = 'Data saved'
        },
        cancel () {
            this.snack = true
            this.snackColor = 'error'
            this.snackText = 'Canceled'
        },
        open () {
            this.snack = true
            this.snackColor = 'info'
            this.snackText = 'Dialog opened'
        },
        close () {
            console.log('Dialog closed')
        }
    },
    watch: {

    },
}
</script>
