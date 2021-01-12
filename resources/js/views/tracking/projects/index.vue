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
        <v-alert
            outlined
            type="info"
        >
            Coming soon
        </v-alert>
    </v-container>
</template>

<script>
import EventBus from "../../../components/EventBus";

export default {
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeColor: this.$store.state.themeColor,
            snackbarMessage: '',
            snackbar: false,
            actionColor: '',
        }
    },
    mounted() {
        // this.getTickets()
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
    },
    methods: {

    },
    watch: {

    },
}
</script>
