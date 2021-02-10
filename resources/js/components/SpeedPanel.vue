<template>
    <v-dialog
        v-model="dialog"
        content-class="dialog-right"
        hide-overlay
        max-width="350px"
        persistent
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                v-show="displayDlg()"
                v-bind="attrs"
                v-on="on"
                :color="themeColor"
                class="mx-2"
                dark
                fab
                fixed
                right
                bottom
                style="z-index: 99;"
            >
                <v-icon>
                    mdi-cog
                </v-icon>
            </v-btn>
        </template>
        <v-expand-x-transition>
            <v-card
                v-show="dialog"
            >
                <v-toolbar
                    :color="themeColor"
                    dark
                >
                    <v-btn
                        dark
                        icon
                        @click="dialog = false"
                    >
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{ this.$store.state.lang.lang_map.system_settings.theme_color }}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn
                            text
                            @click="updateUserSettings"
                        >
                            {{ this.$store.state.lang.lang_map.main.update }}
                        </v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-color-picker
                                    v-model="themeColor"
                                    dot-size="25"
                                    mode="hexa"
                                ></v-color-picker>
                            </v-col>
                            <v-col cols="12">
                                <v-checkbox
                                    v-model="themeColorDlg"
                                    :label="this.$store.state.lang.lang_map.main.do_not_show_this_again"
                                    :value="1"></v-checkbox>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
            </v-card>
        </v-expand-x-transition>
    </v-dialog>
</template>
<style scoped>
>>> .dialog-right {
    position: absolute;
    right: 0;
}
</style>
<script>
import EventBus from "./EventBus";

export default {
    name: "SpeedPanel",
    props: {value: {type: Boolean}},
    data() {
        return {
            themeColor: this.$store.state.themeColor,
            themeColorDlg: localStorage.themeColorDlg,
            dialog: false
        }
    },
    mounted() {
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
    },
    methods: {
        displayDlg() {
            if (this.themeColorDlg != 1) {
                return !this.dialog
            } else {
                return false;
            }
        },
        updateUserSettings() {
            this.dialog = false;
            axios.post('/api/user/settings', {theme_color: this.themeColor}).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.$store.state.themeColor = this.themeColor;
                    localStorage.themeColor = this.themeColor;
                    EventBus.$emit('update-theme-color', this.themeColor);
                    localStorage.themeColorDlg = this.themeColorDlg;
                } else {
                    this.snackbarMessage = this.$store.state.lang.lang_map.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
                return true;
            });
        }
    }
}
</script>
