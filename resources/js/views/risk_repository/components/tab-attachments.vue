<template>
    <div>
        <v-row>
            <v-col cols="6" lg="6" md="6" sm="12" xl="4">
                <v-file-input
                    placeholder="Add attachments"
                    truncate-length="15"
                ></v-file-input>
            </v-col>
            <v-col cols="6" lg="6" md="6" sm="12" xl="8"></v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: 'risk-tab-attachments',
    data() {
        return {}
    }
}
</script>
