<template>
    <v-app light>
        <v-content>
            <v-parallax  height="1000" src="/login_bg.jpg">
                <v-layout style="margin-top: 10%;" align-center class="white--text" column justify-center>
                    <h1 class="white--text mb-2 display-1 text-xs-center"
                        style="font-weight: 900; text-shadow: 3px 2px #000000">
                        Isando Ticketing System
                    </h1>
                    <div class="white--text subheading mb-3 text-xs-center"
                         style="font-weight: 900; text-shadow: 2px 2px #000000">
                        Unlesh your creativity without limitations
                    </div>
                    <v-btn class="btn-lg green" dark href="/register?plan_price_id=1" large>
                        Try it free
                    </v-btn>
                    <br/>
                    <h1 class="white--text mb-2 display-1 text-xs-center"
                        style="font-weight: 900; text-shadow: 3px 2px #000000">
                        or
                    </h1>
                </v-layout>
                <section>
                    <v-card>
                        <v-tabs
                            centered
                            color="green"
                        >
                            <v-tab
                                v-for="(item) in currency"
                                :key="'tab_'+item.id"
                                v-if="item.plan_price.length > 0"
                            >{{ item.name }}</v-tab>
                            <v-tab-item v-for="(item) in currency" :key="'tab_item_'+item.id">
                                <v-container fluid>
                                    <v-row>
                                        <v-col
                                            v-for="plan_price in item.plan_price"
                                            :key="'plan_price'+plan_price.id"
                                            cols="12"
                                            md="4"
                                        >
                                            <v-card
                                                class="mx-auto"
                                                :color="color[plan_price.id]"
                                                dark
                                                max-width="400"
                                            >
                                                <v-card-title>
                                                    <v-icon
                                                        large
                                                        left
                                                    >
                                                        mdi-credit-card-check
                                                    </v-icon>
                                                    <span class="title font-weight-light">
                                                        {{plan_price.plan.name }} ({{ item.symbol }}{{ plan_price.price }})</span>
                                                    <v-spacer></v-spacer>
                                                    <v-btn icon :href="'/register?plan_price_id='+plan_price.id">
                                                        <v-icon>
                                                            mdi-arrow-right-bold-box
                                                        </v-icon>
                                                    </v-btn>
                                                </v-card-title>
                                                <v-card-text class="headline font-weight-bold">
                                                    {{ plan_price.plan.description }}
                                                </v-card-text>
                                                <v-card-actions>
                                                    <v-list-item class="grow">
                                                        <v-row
                                                            align="center"
                                                        >

                                                        </v-row>
                                                    </v-list-item>
                                                </v-card-actions>
                                            </v-card>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-tab-item>
                            </span>
                        </v-tabs>
                        <v-footer
                            color="green"
                            padless
                        >
                            <v-row
                                justify="center"
                                no-gutters
                            >
                                <v-col
                                    class="green lighten-2 py-4 text-center white--text"
                                    cols="12"
                                >
                                    {{ new Date().getFullYear() }} — <strong>Isando</strong>
                                </v-col>
                            </v-row>
                        </v-footer>

                    </v-card>
                </section>
            </v-parallax>


        </v-content>
    </v-app>
</template>

<script>
export default {
    name: "App",
    data: function () {
        return {
            title: "Isando",
            currency: [],
            color: ['green', 'green', 'grey', 'grey', 'blue', 'blue']
        };
    },
    mounted: function () {
        if (localStorage.getItem('auth_token')) {
            this.$router.push('tickets')
        }
        this.getPlans()
    },
    methods: {
        getPlans() {
            axios.get('/api/plans/currency').then(response => {
                response = response.data
                if (response.success === true) {
                    this.currency = response.data
                } else {
                    console.log('error')
                }
            });
        }
    },
};
</script>

<style scoped>
.finedTitle {
    font-weight: 900;
    text-shadow: 2px 2px #000000;
}

.social-icon {
    font-size: 21px;
    color: white;
}
</style>
