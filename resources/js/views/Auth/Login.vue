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
                    sm="8"
                    md="3"
                >
                    <v-alert
                        type="error"
                        :value="alert"
                    >
                            Invalid Username or Password
                        </v-alert>
                        <v-card class="elevation-12" >
                            <v-toolbar
                                dense
                                :color="themeColor"
                                dark
                                flat
                            >
                                <v-toolbar-title>Authorization</v-toolbar-title>
                                <v-spacer></v-spacer>
                            </v-toolbar>
                            <v-card-text>
                                <v-form>
                                    <v-text-field
                                        :color="themeColor"
                                        label="Login"
                                        name="email"
                                        prepend-icon="mdi-account"
                                        type="text"
                                        v-model="email"
                                        required
                                        autocomplete="new-email"
                                    ></v-text-field>

                                    <v-text-field
                                        autocomplete="new-password"
                                        :color="themeColor"
                                        id="password"
                                        label="Password"
                                        name="password"
                                        prepend-icon="mdi-lock"
                                        type="password"
                                        v-model="password"
                                        required
                                    ></v-text-field>

                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                color="grey"
                                                dark
                                                v-bind="attrs"
                                                v-on="on"
                                            >
                                                mdi-two-factor-authentication
                                            </v-icon>
                                        </template>
                                        <span>Coming soon...</span>
                                    </v-tooltip>
                                </v-form>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn :color="themeColor" style="color: white;" @click="handleSubmit">Login</v-btn>
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
