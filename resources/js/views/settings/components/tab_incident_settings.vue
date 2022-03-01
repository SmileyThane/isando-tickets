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
                    <td>name</td>
                    <td>
                        <v-btn icon @click="editItem(item.id)">
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
                    <td>
                        <v-text-field v-model="name" placeholder="Create new" />
                    </td>
                    <td>
                        <v-btn icon @click="addItem()">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tfoot>
            </template>
        </v-simple-table>
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
        }
    },
    mounted() {
        this.$store.dispatch(`SettingsIncident/${this.entity}/callList`);
    },
    methods: {
        addItem() {
            this.$store.dispatch(`SettingsIncident/${this.entity}/callAdd`, { name: this.name });
        },
        editItem() {
            this.$store.dispatch(`SettingsIncident/${this.entity}/callEdit`);
        },
        deleteItem(id) {
            this.$store.dispatch(`SettingsIncident/${this.entity}/callDelete`, id);
        }
    },
}
</script>
