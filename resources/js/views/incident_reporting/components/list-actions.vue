<template>
    <v-row
        v-if="$store.getters['IncidentReporting/getSelectedIR'] &&
        $store.getters['IncidentReporting/getSelectedIR'].actions.length > 0"
    >
        <v-col cols="12">
            <h4 class="heading">{{ langMap.ir.ab.selected_actions }}</h4>
            <v-list dense subheader>
                <v-list-item
                    class="mb-1"
                    style="border: 1px solid #c1c4c9; border-radius: 5px"
                    v-for="(item, index) in actions"
                    :key="index"
                >
                    <v-list-item-content>
                        <v-list-item-title v-text="$helpers.i18n.localized(item)"></v-list-item-title>
                    </v-list-item-content>
                    <v-list-item-action>
                        <v-icon
                            @click="deleteActionByIndex(index)">
                            mdi-delete
                        </v-icon>
                    </v-list-item-action>
                </v-list-item>
            </v-list>
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: 'incident-list-actions',
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
        }
    },
    methods: {
        deleteActionByIndex(index) {
            this.actions.splice(index, 1);
        },
    },
    computed: {
        actions: function () {
            return this.$store.getters['IncidentReporting/getSelectedIR'].actions;
        }
    }
}
</script>

<style scoped>

.heading {
    font-weight: 400;
    letter-spacing: normal;
    font-size: 20px;
    text-align: center;
}

</style>
