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
                :color="themeBgColor"
                class="mx-2"
                dark
                fab
                fixed
                right
                bottom
                style="z-index: 99;"
            >
                <v-icon :color="themeFgColor">mdi-cog</v-icon>
            </v-btn>
        </template>
        <v-expand-x-transition>
            <v-card v-show="dialog">
                <v-toolbar :color="themeBgColor" dark>
                    <v-btn dark icon @click="dialog = false">
                        <v-icon :color="themeFgColor">mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title :style="`color: ${themeFgColor};`">{{
                            langMap.profile.user_theme_colors
                        }}
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn :color="themeFgColor" :style="`color: ${themeBgColor};`" @click="updateUserSettings">
                        {{ langMap.main.update }}
                    </v-btn>
                </v-toolbar>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-color-picker
                                    v-model="themeFgColor"
                                    dot-size="25"
                                    mode="hexa"
                                />
                            </v-col>
                            <v-col cols="12">
                                <v-color-picker
                                    v-model="themeBgColor"
                                    dot-size="25"
                                    mode="hexa"
                                />
                            </v-col>
                            <v-col cols="12">
                                <v-checkbox
                                    v-model="themeColorDlg"
                                    :label="langMap.main.do_not_show_this_again"
                                    :value="1"/>
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
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
	        themeBgColor: this.$store.state.themeBgColor,
            themeColorDlg: localStorage.themeColorDlg,
            dialog: false
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
            axios.post('/api/user/settings', {
		        theme_fg_color: this.themeFgColor,
		        theme_bg_color: this.themeBgColor
	        }).then(response => {
                response = response.data;

                if (response.success === true) {
                    this.$store.state.themeFgColor = this.themeFgColor;
                    localStorage.themeFgColor = this.themeFgColor;
                    EventBus.$emit('update-theme-fg-color', this.themeFgColor);

                    this.$store.state.themeBgColor = this.themeBgColor;
                    localStorage.themeBgColor = this.themeBgColor;
                    EventBus.$emit('update-theme-bg-color', this.themeBgColor);

                    localStorage.themeColorDlg = this.themeColorDlg;
                } else {
                    this.snackbarMessage = langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
                return true;
            });
        }
    }
}
</script>
