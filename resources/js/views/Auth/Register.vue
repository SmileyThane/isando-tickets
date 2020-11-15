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
                    md="8"
                >
                    <v-alert
                        type="error"
                        :value="alert"
                    >
                        Invalid Username or Password
                    </v-alert>
                    <v-card>
                        <v-toolbar
                            dense
                            :color="themeColor"
                            dark
                            flat
                        >
                            <v-toolbar-title>Registration</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>

                        <v-form>
                            <p class="title text-center">Please fill in the required credentials...</p>
                            <div class="row">
                                <v-col cols="12" md="6">
                                    <v-card-text>
                                        <v-text-field
                                            :color="themeColor"
                                            label="Company name"
                                            name="company_name"
                                            prepend-icon="mdi-account"
                                            type="text"
                                            v-model="registrationFrom.company_name"
                                            required
                                        ></v-text-field>
                                    </v-card-text>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-card-text>
                                        <v-text-field
                                            :color="themeColor"
                                            label="Company number"
                                            name="company_number"
                                            prepend-icon="mdi-account"
                                            type="text"
                                            v-model="registrationFrom.company_number"
                                            required
                                        ></v-text-field>
                                    </v-card-text>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-card-text>
                                        <v-text-field
                                            :color="themeColor"
                                            label="Your name"
                                            name="name"
                                            prepend-icon="mdi-account"
                                            type="text"
                                            v-model="registrationFrom.name"
                                            required
                                        ></v-text-field>
                                    </v-card-text>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-card-text>
                                        <v-text-field
                                            :color="themeColor"
                                            label="Email"
                                            name="email"
                                            prepend-icon="mdi-account"
                                            type="text"
                                            v-model="registrationFrom.email"
                                            required
                                        ></v-text-field>
                                    </v-card-text>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-card-text>
                                        <v-select
                                            label="Select plan"
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            item-text="name"
                                            item-value="id"
                                            :items="plan"
                                            v-model="registrationFrom.plan_id"
                                        />
                                    </v-card-text>
                                </v-col>
                            </div>
                        </v-form>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn :color="themeColor" style="color: white;" @click="handleSubmit">Register</v-btn>
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
                registrationFrom: {
                    company_name: '',
                    company_number: '',
                    name: '',
                    email: '',
                    plan_id: '',
                    payment_description: 'Free site registration'
                },
                plan: [],
                themeColor: this.$store.state.themeColor
            }
        },
        mounted() {
            if (localStorage.getItem('auth_token')) {
                this.$router.push('tickets')
            }
            this.getPlans();
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            });
        },
        methods: {
            handleSubmit(e) {
                e.preventDefault()
                axios.post('api/register', this.registrationFrom).then(response => {
                    response = response.data
                    if (response.success === true) {
                        window.open('login', '_self')
                    } else {
                        console.log('error')
                        this.alert = true;
                    }

                });
            },
            getPlans() {
                axios.get('api/plans').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.plan = response.data
                        this.registrationFrom.plan_id = this.plan[0].id
                    } else {
                        console.log('error')
                    }

                });
            },
        }
    }
</script>
