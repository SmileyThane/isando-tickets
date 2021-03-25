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
                        Invalid Username or Password
                    </v-alert>
                    <v-card class="elevation-12">
                        <v-toolbar
                            :color="themeBgColor"
                            dark
                            dense
                            flat
                        >
                            <v-toolbar-title :style="`color: ${themeFgColor};`">Authorization</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-card-text>
                            <v-form>
                                <v-text-field
                                    v-model="email"
                                    :color="themeBgColor"
                                    autocomplete="new-email"
                                    label="Login"
                                    name="email"
                                    prepend-icon="mdi-account"
                                    required
                                    type="text"
                                ></v-text-field>

                                <v-text-field
                                    id="password"
                                    v-model="password"
                                    :color="themeBgColor"
                                    autocomplete="new-password"
                                    label="Password"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    required
                                    type="password"
                                ></v-text-field>
                                <br>

                                <v-tooltip
                                    bottom
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            :color="themeBgColor"
                                            outlined
                                            style="max-width: 36px; min-width: 36px;"

                                        >
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on"
                                                :color="themeBgColor"
                                  na.              dark
                                            >
                                                mdi-two-factor-authentication
                                            </v-icon>
                                        </v-btn>

                                    </template>
                                    <span>Coming soon...</span>
                                </v-tooltip>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-list>
                                <v-list-item to="/reset-password">Forgot your password? </v-list-item>
                            </v-list>
                            <v-spacer></v-spacer>
                            <v-btn :color="themeBgColor" style="color: white;" @click="handleSubmit">Login</v-btn>
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
            email: "",
            password: "",
            themeBgColor: this.$store.state.themeBgColor,
            themeFgColor: this.$store.state.themeFgColor
        }
    },
    mounted() {
        if (localStorage.getItem('auth_token')) {
            this.$router.push('tickets')
        }
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
       EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()
            if (this.password.length > 0) {
                let email = this.email
                let password = this.password

                axios.post('/api/login', {email, password}).then(response => {
                    response = response.data
                    if (response.success === true) {
                        localStorage.setItem('auth_token', response.data.token)
                        window.open('tickets', '_self')
                    } else {
                        console.log('error')
                        this.alert = true;
                    }

                });
            }
        }
    }
}
</script>
