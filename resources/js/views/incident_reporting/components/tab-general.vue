<template>
    <v-card class="my-2">
        <v-row>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <v-select
                    class="small"
                    placeholder="Categories"
                    :items="$store.getters['RiskRepository/getCategories']"
                    item-value="id"
                    item-text="name"
                    multiple
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <v-select
                    class=""
                    placeholder="Clients"
                    :items="$store.getters['RiskRepository/getClients']"
                    item-value="id"
                    item-text="name"
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12" class="pb-0">
                <v-checkbox class="mt-0"
                    label="Include child organizations"
                ></v-checkbox>
            </v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <v-select
                    class=""
                    placeholder="Valid from Stage Monitoring"
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <v-select
                    class=""
                    placeholder="Importance"
                    :items="$store.getters['RiskRepository/getImportance']"
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <v-select
                    class=""
                    placeholder="Access - all"
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12" class="pb-0">
                <v-textarea
                    name="input-7-1"
                    label="Description"
                    auto-grow
                    outlined
                    value=""
                ></v-textarea>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    name: 'incident-tab-general',
    data() {
        return {

        }
    },
    mounted() {
        this.$store.dispatch('RiskRepository/callGetCategories');
        this.$store.dispatch('RiskRepository/callGetArticles');
        this.$store.dispatch('RiskRepository/callGetClients');
        this.$store.dispatch('RiskRepository/callGetImportance');
    },
    methods: {

    }
}
</script>

<style scoped>
.v-input, ::v-deep(label) {
    font-size: 14px;
}
</style>
