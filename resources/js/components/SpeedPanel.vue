<template>
    <v-dialog
        v-model="dialog"
        hide-overlay
        persistent
        transition="dialog-right-transition"
        max-width="350px"
        content-class="dialog-right"
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                v-bind="attrs"
                v-on="on"
                :color="themeColor"
                dark
                fixed
                right
                class="mx-2"
                style="z-index: 1;"
                fab
                v-show="displayDlg()"
            >
              <v-icon>
                  mdi-cog
              </v-icon>
            </v-btn>
        </template>
        <v-card>
            <v-toolbar
                dark
                :color="themeColor"
            >
                <v-btn
                    icon
                    dark
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
                                    dot-size="25"
                                    mode="hexa"
                                    v-model="themeColor"
                                ></v-color-picker>
                            </v-col>
                            <v-col cols="12">
                                <v-checkbox
                                    v-model="themeColorDlg"
                                    :value="1"
                                    :label="this.$store.state.lang.lang_map.main.do_not_show_this_again"></v-checkbox>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
        </v-card>
    </v-dialog>
</template>
<style scoped>
>>>.dialog-right {
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
