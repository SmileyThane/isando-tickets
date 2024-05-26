<template>
    <v-footer
        style="z-index: 5;"
        :color="themeBgColor"
        app
    >
        <span :style="`color: ${themeFgColor};`">&copy; ISANDO {{moment(moment.now()).format('YYYY')}}</span>
        <span :style="`color: ${themeFgColor}; margin-left: auto;`" v-if="$store.state.lang && $store.state.lang.lang_map">{{ $store.state.lang.lang_map.main.version }} {{ $store.state.appVersion }}</span>
    </v-footer>
</template>

<script>
    import EventBus from "./EventBus";

    export default {
        name: "AppFooter",
        data() {
            return {
                themeFgColor: this.$store.state.themeFgColor,
                themeBgColor: this.$store.state.themeBgColor
            }
        },
        mounted() {
            let that = this;
            EventBus.$on('update-theme-fg-color', function (color) {
                that.themeFgColor = color;
            });
            EventBus.$on('update-theme-bg-color', function (color) {
                that.themeBgColor = color;
            });
        }
    }
</script>
