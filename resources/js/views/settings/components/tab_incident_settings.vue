<template>
    <v-card style="height: 100%">
        <v-simple-table dense>
            <template v-slot:default>
                <thead>
                <tr>
                    <th class="text-left" style="width: 90%">Name</th>
                    <th class="text-left">Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in $store.getters[`SettingsIncident/${entity}/getItems`]">
                    <td>{{ item.name }}</td>
                    <td>
                        <v-btn icon @click="open(item.id)">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn icon @click="deleteItem(item.id)">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td class="mt-4">
                        <v-text-field
                            v-model="name"
                            class="mt-4"
                            dense
                            hide-details
                            outlined
                            placeholder="Create new"
                            @keydown="pressEnter"
                        />
                    </td>
                    <td>
                        <v-btn :disabled="name.length < 3"
                               class="mt-4"
                               icon
                               @click="addItem()">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tfoot>
            </template>
        </v-simple-table>
        <v-dialog
            v-model="editDialog"
            width="500"
        >
            <v-card>
                <v-card-title>
                    Edit item
                </v-card-title>

                <v-card-text>
                    <v-text-field
                        v-model="selName"
                        class="mt-4"
                        dense
                        hide-details
                        outlined
                    />
                </v-card-text>

                <v-divider></v-divider>
                <v-card-title v-if="issetColor">
                    Edit color
                </v-card-title>

                <v-card-text v-if="issetColor">
                    <v-color-picker
                        v-model="selColor"
                        dot-size="25"
                        mode="hexa"
                    />
                </v-card-text>
                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="error"
                        text
                        @click="cancel"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="success"
                        text
                        @click="editItem"
                    >
                        Apply
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
</template>

<script>
export default {
    name: 'tab-incident-settings',
    props: {
        entity: {
            type: String,
            required: true,
        }
    },
    data: function () {
        return {
            name: '',
            editDialog: false,
        }
    },
    mounted() {
        this.$store.dispatch(`SettingsIncident/${this.entity}/callList`);
    },
    methods: {
        pressEnter(event) {
            if (event.code === 'Enter') {
                this.addItem()
            }
        },
        open(id) {
            this.$store.commit(`SettingsIncident/${this.entity}/selectItem`, id);
            this.editDialog = true;
        },
        cancel() {
            this.$store.commit(`SettingsIncident/${this.entity}/unSelectItem`);
            this.$store.dispatch(`SettingsIncident/${this.entity}/callList`);
            this.editDialog = false;
        },
        addItem() {
            this.$store.dispatch(`SettingsIncident/${this.entity}/callAdd`, {name: this.name});
            this.name = '';
        },
        editItem() {
            this.$store.dispatch(`SettingsIncident/${this.entity}/callEdit`);
            this.editDialog = false;
        },
        deleteItem(id) {
            this.$store.dispatch(`SettingsIncident/${this.entity}/callDelete`, id);
        }
    },
    computed: {
        issetColor() {
            return this.$store.getters[`SettingsIncident/${this.entity}/getItem`]?.color !== undefined
        },
        selName: {
            get: function () {
                return this.$store.getters[`SettingsIncident/${this.entity}/getItem`]?.name ?? '';
            },
            set: function (val) {
                return this.$store.commit(`SettingsIncident/${this.entity}/setName`, val);
            }
        },
        selColor: {
            get: function () {
                return this.$store.getters[`SettingsIncident/${this.entity}/getItem`]?.color ?? '';
            },
            set: function (val) {
                return this.$store.commit(`SettingsIncident/${this.entity}/setColor`, val);
            }
        }
    }
}
</script>

<style scoped>
>>> .v-input *,
>>> .v-card__title,
>>> .v-btn__content {
    font-size: 14px !important;
}
</style>
