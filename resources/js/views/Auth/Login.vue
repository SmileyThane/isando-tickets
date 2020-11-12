<template>
    <v-main>
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
                            :color="themeColor"
                            dark
                            dense
                            flat
                        >
                            <v-toolbar-title>Authorization</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-card-text>
                            <v-form>
                                <v-text-field
                                    :color="themeColor"
                                    autocomplete="new-email"
                                    label="Login"
                                    name="email"
                                    prepend-icon="mdi-account"
                                    required
                                    type="text"
                                    v-model="email"
                                ></v-text-field>

                                <v-text-field
                                    :color="themeColor"
                                    autocomplete="new-password"
                                    id="password"
                                    label="Password"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    required
                                    type="password"
                                    v-model="password"
                                ></v-text-field>
                                <br>
                                <v-tooltip :key="i"
                                           bottom
                                           v-for="i in 3"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            :color="themeColor"
                                            class="ma-2"
                                            outlined
                                            style="max-width: 36px; min-width: 36px;"

                                        >
                                            <v-icon
                                                :color="themeColor"
                                                dark
                                                v-bind="attrs"
                                                v-on="on"
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
                            <v-btn :color="themeColor" @click="handleSubmit" style="color: white;">Login</v-btn>
                        </v-card-actions>
                    </v-card>


                </v-col>
            </v-row>
        </v-container>
    </v-main>
</template>

<script>
    import EventBus from "../../components/EventBus";

    export default {
        data() {
            return {
                alert: false,
                email: "",
                password: "",
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
                e.preventDefault()
                if (this.password.length > 0) {
                    let email = this.email
                    let password = this.password

                    axios.post('api/login', {email, password}).then(response => {
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
