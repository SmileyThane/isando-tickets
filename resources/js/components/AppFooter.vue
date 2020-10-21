<template>
    <v-footer
        :color="themeColor"
        app
    >
        <span class="white--text">&copy;ISANDO 2020</span>
    </v-footer>
</template>

<script>
    import EventBus from "./EventBus";

    export default {
        name: "AppFooter",
        data() {
            return {
                themeColor: this.$store.state.themeColor
            }
        },
        mounted() {
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            });
        },
    }
</script>
