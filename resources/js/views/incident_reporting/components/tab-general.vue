<template>
    <v-card class="my-2">
        <v-row>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12">
                <v-select
                    class="small"
                    placeholder="Natural disasters"
                    multiple
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12">
                <v-select
                    class=""
                    placeholder="Brauerei Luzern"
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12">
                <v-checkbox class="mt-0"
                    label="Include child organizations"
                ></v-checkbox>
            </v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12">
                <v-select
                    class=""
                    placeholder="Valid from Stage Monitoring"
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12">
                <v-select
                    class=""
                    placeholder="Valid from Potential impact: Low"
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12">
                <v-select
                    class=""
                    placeholder="Access - all"
                    dense
                    outlined
                    hide-details
                ></v-select>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12">
                <v-textarea
                    name="input-7-1"
                    label="Description"
                    auto-grow
                    outlined
                    value=""
                ></v-textarea>
            </v-col>
            <v-col cols="6" xl="8" lg="6" md="6" sm="12"></v-col>
            <v-col cols="6" xl="4" lg="6" md="6" sm="12">
                <v-file-input
                    placeholder="Add attachments"
                    truncate-length="15"
                ></v-file-input>
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
    }
}
</script>
