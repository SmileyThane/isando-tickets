<template>
    <v-footer
        :color="themeColor"
        app
    >
        <span class="white--text">&copy; ISANDO 2021</span>
        <span class="white--text" style="margin-left: auto;" v-if="$store.state.lang && $store.state.lang.lang_map">{{ $store.state.lang.lang_map.main.version }} {{ $store.state.appVersion }}</span>
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
