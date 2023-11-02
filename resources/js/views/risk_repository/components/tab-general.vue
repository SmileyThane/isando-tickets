<template>
    <v-card class="my-2">
        <v-row>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <v-select
                    :items="$store.getters['RiskRepository/getCategories']"
                    class="small"
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    multiple
                    outlined
                    placeholder="Categories"
                ></v-select>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8"></v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <v-select
                    :items="$store.getters['RiskRepository/getClients']"
                    class=""
                    dense
                    hide-details
                    item-text="name"
                    item-value="id"
                    outlined
                    placeholder="Clients"
                ></v-select>
            </v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="8">
                <v-checkbox class="mt-0"
                            label="Include child organizations"
                ></v-checkbox>
            </v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <v-select
                    :placeholder="langMap.ir.ab.stage_monitoring"
                    class=""
                    dense
                    hide-details
                    outlined
                ></v-select>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8"></v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <v-select
                    :items="$store.getters['RiskRepository/getImportance']"
                    class=""
                    dense
                    hide-details
                    outlined
                    placeholder="Importance"
                ></v-select>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8"></v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <v-select
                    class=""
                    dense
                    hide-details
                    outlined
                    placeholder="Access - all"
                ></v-select>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8"></v-col>
            <v-col class="pb-0" cols="6" lg="6" md="6" sm="12" xl="4">
                <v-textarea
                    :value="$store.getters['RiskRepository/getSelectedArticle'].content"
                    auto-grow
                    label="Description"
                    name="input-7-1"
                    outlined
                ></v-textarea>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8"></v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    name: 'risk-tab-general',
    data() {
        return {}
    },
    mounted() {
        this.$store.dispatch('RiskRepository/callGetClients');
        this.$store.dispatch('RiskRepository/callGetImportance');
    },
    methods: {}
}
</script>

<style scoped>
.v-input, ::v-deep(label) {
    font-size: 14px;
}
</style>
