<template>
    <v-content>
        <v-container
            class="fill-height"
            fluid
        >
            <v-row
                align="center"
                justify="center"
            >
                <v-col
                    cols="12"
                    md="3"
                    sm="8"
                >
                    <v-alert
                        :value="alert"
                        type="error"
                    >
                        Invalid Email
                    </v-alert>
                    <v-alert
                        :value="message"
                        type="success"
                    >
                        The new password sent to the email address you specified. Please, check your mail box.
                    </v-alert>
                    <v-card class="elevation-12">
                        <v-toolbar
                            :color="themeColor"
                            dark
                            dense
                            flat
                        >
                            <v-toolbar-title>Reset password</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-card-text>
                            <v-form>
                                <v-text-field
                                    v-model="email"
                                    :color="themeColor"
                                    autocomplete="new-email"
                                    label="Email"
                                    name="email"
                                    prepend-icon="mdi-account"
                                    required
                                    type="text"
                                ></v-text-field>

                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <a href="/login">Back to login</a>
                            <v-spacer></v-spacer>
                            <v-btn :color="themeColor" style="color: white;" @click="handleSubmit">Reset</v-btn>
                        </v-card-actions>
                    </v-card>


                </v-col>
            </v-row>
        </v-container>
    </v-content>
</template>

<script>
import EventBus from "../../components/EventBus";

export default {
    data() {
        return {
            alert: false,
            message: false,
            email: "",
            themeColor: this.$store.state.themeColor
        }
    },
    mounted() {
        if (localStorage.getItem('auth_token')) {
            this.$router.push('tickets')
        }
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeColor = color;
        });
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault();
            this.message = false;
            this.alert = false;
            if (this.email.length > 0) {
                axios.post('/api/reset_password', {
                    email: this.email
                }).then(response => {
                    response = response.data
                    if (response.success === true) {
                       this.message = true;
                    } else {
                        this.alert = true;
                    }
                });
            }
        }
    }
}
</script>
