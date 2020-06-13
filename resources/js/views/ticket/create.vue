<template>

    <div>
        <v-row justify="space-around">
            <v-col cols="12">
                <p class="title text-center">Create new ticket</p>
                <!--                            <v-slider v-model="steps" label="Create new ticket" min="2" max="20"></v-slider>-->
            </v-col>
            <!--                        <v-switch v-model="vertical" label="Vertical"></v-switch>-->
            <!--                        <v-switch v-model="altLabels" label="altLabels"></v-switch>-->
            <!--                        <v-switch v-model="editable" label="Editable"></v-switch>-->
        </v-row>
        <v-stepper
            v-model="e1"
            :vertical="vertical"
            :alt-labels="altLabels"

        >
            <v-stepper-header>
                <template v-for="n in steps">
                    <v-stepper-step
                        :key="`${n}-step`"
                        :complete="e1 > n"
                        :step="n"
                        :editable="editable"
                        :color="'green'"
                    >
                        Step {{ n }}
                    </v-stepper-step>

                    <v-divider
                        v-if="n !== steps"
                        :key="n"
                    ></v-divider>
                </template>
            </v-stepper-header>

            <v-stepper-items

            >
                <v-stepper-content
                    v-for="n in steps"
                    :key="`${n}-content`"
                    :step="n"
                >
                    <v-card
                        class="mb-12"
                        color="grey lighten-1"
                        height="200px"
                    ></v-card>

                    <v-btn
                        :color="n !== steps ?  'primary' : '#4caf50'"
                        @click="n !== steps ? nextStep(n) : submit()"
                        v-text="n !== steps ? 'Continue' : 'Submit'"
                    >
                    </v-btn>
                    <v-btn
                        text
                        @click="previousStep(n)"
                    >
                        Cancel
                    </v-btn>
                </v-stepper-content>
            </v-stepper-items>
            <v-spacer></v-spacer>
        </v-stepper>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                e1: 1,
                steps: 3,
                vertical: false,
                altLabels: true,
                editable: true,
            }
        },

        watch: {
            steps(val) {
                if (this.e1 > val) {
                    this.e1 = val
                }
            },
            vertical() {
                this.e1 = 2
                requestAnimationFrame(() => this.e1 = 1) // Workarounds
            },
        },

        methods: {
            onInput(val) {
                this.steps = parseInt(val)
            },
            nextStep(n) {
                if (n === this.steps) {
                    this.e1 = n
                } else {
                    this.e1 = n + 1
                }
            },
            previousStep(n) {
                if (n === 1) {
                    this.e1 = 1
                } else {
                    this.e1 = n - 1
                }
            },
            submit() {
                alert("Submitted")
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
