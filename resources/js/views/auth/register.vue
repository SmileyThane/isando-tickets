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
                        :value="alert"
                        type="error"
                    >
                        Invalid registration parameters
                    </v-alert>
                    <v-card>
                        <v-toolbar
                            :color="themeBgColor"
                            dark
                            dense
                            flat
                        >
                            <v-toolbar-title :style="`color: white;`">Registration</v-toolbar-title>
                            <v-spacer></v-spacer>
                        </v-toolbar>

                        <v-form>
                            <p class="title text-center">Please fill in the required credentials...</p>
                            <div class="row">
                                <v-col cols="12" md="6">
                                    <v-card-text>
                                        <v-text-field
                                            v-model="registrationFrom.company_name"
                                            :color="themeBgColor"
                                            label="Company name"
                                            name="company_name"
                                            prepend-icon="mdi-account"
                                            required
                                            type="text"
                                        ></v-text-field>
                                    </v-card-text>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-card-text>
                                        <v-text-field
                                            v-model="registrationFrom.company_number"
                                            :color="themeBgColor"
                                            label="Company number"
                                            name="company_number"
                                            prepend-icon="mdi-account"
                                            required
                                            type="text"
                                        ></v-text-field>
                                    </v-card-text>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-card-text>
                                        <v-text-field
                                            v-model="registrationFrom.name"
                                            :color="themeBgColor"
                                            label="Your name"
                                            name="name"
                                            prepend-icon="mdi-account"
                                            required
                                            type="text"
                                        ></v-text-field>
                                    </v-card-text>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-card-text>
                                        <v-text-field
                                            v-model="registrationFrom.email"
                                            :color="themeBgColor"
                                            label="Email"
                                            name="email"
                                            prepend-icon="mdi-account"
                                            required
                                            type="text"
                                        ></v-text-field>
                                    </v-card-text>
                                </v-col>
                                <v-col cols="12" md="4">
                                    <v-card-text>
                                        <v-select
                                            v-model="registrationFrom.plan_price_id"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="plan"
                                            item-value="id"
                                            label="Select plan"
                                        >
                                            <template v-slot:selection="{ attrs, item, parent, selected }">
                                                <v-chip v-bind="attrs" class="ml-2" label small>
                                                    {{ item.plan.name }}
                                                    {{
                                                        item.price && item.currency ? '(' + item.price + ' ' + item.currency.slug + ')' : ''
                                                    }}
                                                </v-chip>
                                            </template>
                                            <template v-slot:item="{ attrs, item, parent, selected }">
                                                <v-chip v-bind="attrs" class="ml-2" label>
                                                    {{ item.plan.name }}
                                                    {{
                                                        item.price && item.currency ? '(' + item.price + ' ' + item.currency.slug + ')' : ''
                                                    }}
                                                </v-chip>
                                            </template>
                                        </v-select>
                                    </v-card-text>
                                </v-col>
                            </div>
                        </v-form>

                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn :color="themeBgColor" style="color: white;" @click="handleSubmit">Register</v-btn>
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
                plan_price_id: '',
                payment_description: 'Free site registration'
            },
            plan: [],
            themeBgColor: this.$store.state.themeBgColor
        }
    },
    mounted() {
        if (localStorage.getItem('auth_token')) {
            this.$router.push('tickets')
        }
        this.getPlans();
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()
            axios.post('/api/register', this.registrationFrom).then(response => {
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
            axios.get('/api/plans').then(response => {
                response = response.data
                if (response.success === true) {
                    this.plan = response.data
                    this.registrationFrom.plan_price_id = this.$route.query.plan_price_id ?? this.plan[0].id
                } else {
                    console.log('error')
                }

            });
        },
    }
}
</script>
