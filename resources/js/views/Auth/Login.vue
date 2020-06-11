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
                    md="4"
                >
                    <v-alert
                        type="error"
                        :value="alert"
                    >
                        Invalid Username or Password
                    </v-alert>
                    <v-card class="elevation-12">
                        <v-toolbar
                            color="green"
                            dark
                            flat
                        >
                            <v-toolbar-title>Authorization</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-card-text>
                            <v-form>
                                <v-text-field
                                    color="green"
                                    label="Login"
                                    name="email"
                                    prepend-icon="mdi-account"
                                    type="text"
                                    v-model="email"
                                    required
                                ></v-text-field>

                                <v-text-field
                                    color="green"
                                    id="password"
                                    label="Password"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    type="password"
                                    v-model="password"
                                    required
                                ></v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="green" style="color: white;" @click="handleSubmit">Login</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </v-content>

</template>

<script>
    export default {
        data() {
            return {
                alert: false,
                email: "",
                password: ""
            }
        },
        mounted() {
            if (localStorage.getItem('auth_token')) {
                this.$router.push('tickets')
            }
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
