<template>
    <v-card>
        <v-simple-table dense>
            <template v-slot:default>
                <thead>
                <tr>
                    <th class="text-left">Name</th>
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
                        <v-btn icon @click="deleteItem(ite.id)">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
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

        }
    },
    mounted() {
        this.$store.dispatch(`SettingsIncident/${this.entity}/callList`);
    },
    methods: {
        addItem() {

        },
        editItem(id) {

        },
        deleteItem(id) {

        }
    },
}
</script>
