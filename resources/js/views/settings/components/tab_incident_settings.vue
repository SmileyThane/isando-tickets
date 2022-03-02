<template>
    <v-card style="height: 100%">
        <v-simple-table dense>
            <template v-slot:default>
                <thead>
                <tr>
                    <th style="width: 90%" class="text-left">Name</th>
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
                            placeholder="Create new"
                            dense outlined hide-details
                            class="mt-4"
                            @keydown="pressEnter"
                        />
                    </td>
                    <td>
                        <v-btn :disabled="name.length < 3"
                            class="mt-4"
                            icon @click="addItem()">
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
                <v-card-title class="text-h5 grey lighten-2">
                    Edit item
                </v-card-title>

                <v-card-text>
                    <v-text-field
                        class="mt-4"
                        v-model="selName"
                        dense outlined hide-details
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
    data: function() {
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
            this.$store.dispatch(`SettingsIncident/${this.entity}/callAdd`, { name: this.name });
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
        selName: {
            get: function () {
                return this.$store.getters[`SettingsIncident/${this.entity}/getItem`]?.name ?? '';
            },
            set: function (val) {
                return this.$store.commit(`SettingsIncident/${this.entity}/setName`, val);
            }
        }
    }
}
</script>
