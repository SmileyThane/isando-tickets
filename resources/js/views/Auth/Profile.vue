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
                    sm="12"
                    md="8"
                >
                    <v-alert
                        :type="errorType"
                        :value="alert"
                        v-for="(item) in error"
                    >
                        {{item}}
                    </v-alert>
                    <v-card class="elevation-12">
                        <v-toolbar
                            color="green"
                            dark
                            flat
                        >
                            <v-toolbar-title>Profile info</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>
                        <v-container>
                            <v-row align="center"
                                   justify="center">
                                <v-col cols="auto">
                                    <v-img
                                        height="200"
                                        width="200"
                                        src="https://cdn.vuetifyjs.com/images/cards/store.jpg"
                                    ></v-img>
                                </v-col>

                                <v-col
                                    cols="auto"
                                    class="text-center pl-0"
                                >
                                    <v-row
                                        class="flex-column ma-0 fill-height"
                                        justify="center"
                                    >
                                        <v-col class="px-0">
                                            <v-btn icon>
                                                <v-icon>mdi-upload</v-icon>
                                            </v-btn>
                                        </v-col>

                                        <v-col class="px-0">
                                            <v-btn icon>
                                                <v-icon>mdi-delete</v-icon>
                                            </v-btn>
                                        </v-col>

                                    </v-row>
                                </v-col>
                            </v-row>
                        </v-container>

                        <v-card-text>
                            <v-form>
                                <v-text-field
                                    color="green"
                                    label="Name"
                                    name="name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.name"
                                    required
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Login"
                                    name="email"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.email"
                                    required
                                ></v-text-field>

                                <v-text-field
                                    color="green"
                                    id="password"
                                    label="Password"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    type="password"
                                    v-model="userData.password"
                                    required
                                ></v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="green" style="color: white;" @click="updateUser">Update</v-btn>
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
                errorType: '',
                error: [],
                userData: {
                    name: "",
                    email: "",
                    password: ""
                }
            }
        },
        mounted() {
            this.getUser();
            // if (localStorage.getItem('auth_token')) {
            //     this.$router.push('tickets')
            // }
        },
        methods: {
            getUser() {
                axios.get('api/user').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.userData = response.data
                        // console.log(this.userData);
                    }
                });
            },

            updateUser(e) {
                e.preventDefault()
                this.alert = false;
                this.error = []
                axios.post('api/user', this.userData).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.userData.password = ''
                        this.getUser()
                        this.error.push('Update successful')
                        this.errorType = 'success'
                        this.alert = true;
                    } else {
                        this.parseErrors(response.error)
                        this.errorType = 'error'
                        this.alert = true;

                    }
                });
            },
            parseErrors(errorTypes) {

                for (let typeIndex in errorTypes) {
                    let errorType = errorTypes[typeIndex]
                    for (let errorIndex in errorType) {
                        this.error.push(errorType[errorIndex])
                    }
                }
                // console.log(this.error)
            }
        }
    }
</script>
