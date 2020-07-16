<template>


    <v-container fluid>
        <v-row>
            <v-col class="col-md-6">
                <!--                    <v-alert-->
                <!--                        :type="errorType"-->
                <!--                        :value="alert"-->
                <!--                        v-for="(item, key) in error"-->
                <!--                        v-bind:key="key"-->
                <!--                    >-->
                <!--                        {{item}}-->
                <!--                    </v-alert>-->
                <v-card class="elevation-6">
                    <v-toolbar
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Profile info</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <!--                        <v-container>-->
                    <!--                            <v-row align="center"-->
                    <!--                                   justify="center">-->
                    <!--                                <v-col cols="auto">-->
                    <!--                                    <v-img-->
                    <!--                                        height="200"-->
                    <!--                                        width="200"-->
                    <!--                                        src="https://cdn.vuetifyjs.com/images/cards/store.jpg"-->
                    <!--                                    ></v-img>-->
                    <!--                                </v-col>-->

                    <!--                                <v-col-->
                    <!--                                    cols="auto"-->
                    <!--                                    class="text-center pl-0"-->
                    <!--                                >-->
                    <!--                                    <v-row-->
                    <!--                                        class="flex-column ma-0 fill-height"-->
                    <!--                                        justify="center"-->
                    <!--                                    >-->
                    <!--                                        <v-col class="px-0">-->
                    <!--                                            <v-btn icon>-->
                    <!--                                                <v-icon>mdi-upload</v-icon>-->
                    <!--                                            </v-btn>-->
                    <!--                                        </v-col>-->

                    <!--                                        <v-col class="px-0">-->
                    <!--                                            <v-btn icon>-->
                    <!--                                                <v-icon>mdi-delete</v-icon>-->
                    <!--                                            </v-btn>-->
                    <!--                                        </v-col>-->

                    <!--                                    </v-row>-->
                    <!--                                </v-col>-->
                    <!--                            </v-row>-->
                    <!--                        </v-container>-->

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-text-field
                                    color="green"
                                    label="Title before name"
                                    name="title_before_name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.title_before_name"
                                    class="col-md-6"
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Title"
                                    name="title"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.title"
                                    class="col-md-6"
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Name"
                                    name="name"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.name"
                                    required
                                    class="col-md-6"
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Surname"
                                    name="surname"
                                    prepend-icon="mdi-book-account-outline"
                                    type="text"
                                    v-model="userData.surname"
                                    class="col-md-6"

                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Email"
                                    name="email"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.email"
                                    required
                                    class="col-md-6"
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    id="password"
                                    label="Password"
                                    name="password"
                                    prepend-icon="mdi-lock"
                                    type="password"
                                    v-model="userData.password"
                                    class="col-md-6"
                                    required
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Country"
                                    name="country"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.country"
                                    class="col-md-4"
                                    required
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Anredeform"
                                    name="anredeform"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.anredeform"
                                    class="col-md-4"
                                    required
                                ></v-text-field>
                                <v-text-field
                                    color="green"
                                    label="Language"
                                    name="lang"
                                    prepend-icon="mdi-mail"
                                    type="text"
                                    v-model="userData.lang"
                                    class="col-md-4"
                                    required
                                ></v-text-field>

                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="green" style="color: white;" @click="updateUser">Update</v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col class="col-md-6">
                <v-card class="elevation-6">
                    <v-toolbar
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Additional info</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>

                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col class="col-md-6">
                                    <v-data-table
                                        :headers="phoneHeaders"
                                        :items="userData.phones"
                                        hide-default-footer
                                        class="elevation-1"
                                        dense
                                    ></v-data-table>
                                </v-col>
                                <v-col class="col-md-6">
                                    <v-data-table
                                        :headers="addressHeaders"
                                        :items="userData.addresses"
                                        hide-default-footer
                                        class="elevation-1"
                                        dense
                                    ></v-data-table>
                                </v-col>

                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>

</template>

<script>
    export default {
        data() {
            return {
                phoneHeaders: [
                    {text: 'Phone', sortable: false, value: 'phone'},
                    {text: 'Type',  value: 'type.name'},
                ],
                addressHeaders: [
                    {text: 'Address', value: 'address'},
                    {text: 'Type', value: 'type.name'},
                ],
                alert: false,
                errorType: '',
                error: [],
                userData: {
                    title: '',
                    title_before_name: '',
                    surname: '',
                    country: '',
                    anredeform: '',
                    lang: '',
                    name: "",
                    email: "",
                    password: "",
                    phones: [],
                    addresses: []
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
