<template>
    <v-container>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <div>
            <v-row justify="space-around">
                <v-col cols="12">
                    <p class="title text-center">Create new ticket</p>
                    <!--                            <v-slider v-model="steps" label="Create new ticket" min="2" max="20"></v-slider>-->
                </v-col>
            </v-row>
            <v-stepper
                v-model="e1"
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
                    <v-form>
                        <v-stepper-content step="1">
                            <v-card-text>
                                <div class="row">
                                    <div class="col-md-6">
                                        <v-select
                                            label="From"
                                            color="green"
                                            item-color="green"
                                            item-text="name"
                                            item-value="item"
                                            :items="suppliers"
                                            v-model="ticketFrom.from"
                                            @input="getContacts"
                                        />
                                    </div>
                                    <v-col cols="md-6">
                                        <v-autocomplete
                                            color="green"
                                            item-color="green"
                                            item-text="name"
                                            item-value="item"
                                            v-model="ticketFrom.to"
                                            :items="suppliers"
                                            label="To"
                                        ></v-autocomplete>
                                    </v-col>
                                    <div class="col-md-6">
                                        <v-select
                                            label="Product"
                                            color="green"
                                            item-color="green"
                                            item-text="product_data.name"
                                            item-value="product_data.id"
                                            :items="products"
                                            v-model="ticketFrom.to_product_id"
                                        />
                                    </div>
                                    <v-col cols="md-6">
                                        <v-autocomplete
                                            color="green"
                                            item-color="green"
                                            item-text="user_data.email"
                                            item-value="id"
                                            v-model="ticketFrom.contact_company_user_id"
                                            :items="employees"
                                            label="Client contact"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-tooltip v-model="availabilityTooltip" bottom>
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-textarea
                                                    label="Client's availability"
                                                    color="green"
                                                    item-color="green"
                                                    auto-grow
                                                    outlined
                                                    rows="3"
                                                    row-height="25"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-textarea>
                                            </template>
                                            <span>Please provide working hours when we can contact you on the submitted issue</span>
                                        </v-tooltip>
                                    </v-col>
                                </div>
                            </v-card-text>
                        </v-stepper-content>

                        <v-stepper-content step="2">
                            <v-card-text>
                                <div class="row">
                                    <v-col cols="md-6">
                                        <v-text-field
                                            color="green"
                                            item-color="green"
                                            label="Title"
                                            v-model="ticketFrom.name"
                                        ></v-text-field>
                                    </v-col>
                                    <div class="col-md-6">
                                        <v-select
                                            label="Priority"
                                            color="green"
                                            item-color="green"
                                            item-text="name"
                                            item-value="id"
                                            :items="priorities"
                                            v-model="ticketFrom.priority_id"
                                        />
                                    </div>
                                    <v-col cols="12">
                                        <v-textarea
                                            label="Description"
                                            color="green"
                                            item-color="green"
                                            auto-grow
                                            outlined
                                            rows="3"
                                            row-height="25"
                                            v-model="ticketFrom.description"
                                        ></v-textarea>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-label>Server access details:</v-label>
                                    </v-col>
                                    <v-col cols="md-6">
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-textarea
                                                    label="IP address"
                                                    color="green"
                                                    item-color="green"
                                                    auto-grow
                                                    outlined
                                                    rows="3"
                                                    row-height="25"
                                                    v-model="ticketFrom.connection_details"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-textarea>
                                            </template>
                                            <span>IP addresses of your server for remote access if applicable</span>
                                        </v-tooltip>
                                    </v-col>
                                    <v-col cols="md-6">
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-textarea
                                                    label="Access details"
                                                    color="green"
                                                    item-color="green"
                                                    auto-grow
                                                    outlined
                                                    rows="3"
                                                    row-height="25"
                                                    v-model="ticketFrom.access_details"
                                                    v-bind="attrs"
                                                    v-on="on"
                                                ></v-textarea>
                                            </template>
                                            <span>Please provide login details to your server for Teamviewer access, if applicable</span>
                                        </v-tooltip>
                                    </v-col>
                                </div>
                            </v-card-text>
                        </v-stepper-content>
                        <v-stepper-content step="3">
                            <div>
                                <v-file-input
                                    chips
                                    chips-color="green"
                                    multiple
                                    label="Attach a Document(s)"
                                    color="green"
                                    item-color="green"
                                    prepend-icon="mdi-paperclip"
                                    :show-size="1000"
                                    v-on:change="onFileChange('ticketFrom')"
                                >
                                    <template v-slot:selection="{ index, text }">
                                        <v-chip
                                            color="green"
                                        >
                                            {{ text }}
                                        </v-chip>
                                    </template>
                                </v-file-input>
                            </div>
                        </v-stepper-content>
                    </v-form>

                    <v-stepper-content
                        v-for="n in steps"
                        :key="`${n}-content`"
                        :step="n"
                    >
                        <v-btn
                            style="color: white;"
                            color="#4caf50"
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
    </v-container>
</template>
<script>
    export default {
        data() {
            return {
                clientId: 6,
                overlay: false,
                availabilityTooltip: false,
                e1: 1,
                steps: 3,
                vertical: false,
                altLabels: true,
                editable: true,
                ticketFrom: {
                    from: '',
                    from_entity_type: '',
                    from_entity_id: '',
                    to: '',
                    to_entity_type: '',
                    to_entity_id: '',
                    contact_company_user_id: '',
                    to_product_id: '',
                    priority_id: '',
                    name: '',
                    description: '',
                    connection_details: '',
                    access_details: '',
                    files: []
                },
                suppliers: [],
                products: [],
                priorities: [],
                employees: [],
                onFileChange(form) {
                    this[form].files = null;
                    console.log(event.target.files);
                    this[form].files = event.target.files;
                },
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
        mounted() {
            this.getSuppliers()
            this.getProducts()
            this.getPriorities()
            // this.getCompany()
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
            getSuppliers() {
                axios.get('/api/supplier').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.suppliers = response.data
                        this.ticketFrom.from = this.$store.state.roles.includes(this.clientId) ? this.suppliers[1] : this.suppliers[0];
                        this.ticketFrom.to = this.suppliers[0]
                        this.getContacts(this.ticketFrom.from.item)
                    } else {
                        console.log('error')
                    }
                });
            },
            getProducts() {
                axios.get('/api/product').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.products = response.data.data
                        this.ticketFrom.to_product_id = this.products[0].product_data.id
                    } else {
                        console.log('error')
                    }

                });
            },
            getPriorities() {
                axios.get('/api/ticket_priorities').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.priorities = response.data
                        this.ticketFrom.priority_id = this.priorities[1].id
                    } else {
                        console.log('error')
                    }

                });
            },
            getContacts(entityItem) {
                this.employees = []
                let route = '';
                if (Object.keys(entityItem)[0] === 'App\\Company') {
                    route = `/api/company/${Object.values(entityItem)[0]}`
                } else {
                    route = `/api/client/${Object.values(entityItem)[0]}`
                }
                // console.log(entityItem);
                axios.get(route).then(response => {
                    response = response.data
                    if (response.success === true) {
                        response = response.data
                        // console.log(response);
                        if (!response.hasOwnProperty('company_number')) {
                            response.employees.forEach(employeeItem => this.employees.push(employeeItem.employee))
                            // console.log('client');
                        } else {
                            this.employees = response.employees
                            // console.log('company');
                        }
                        // this.ticketFrom.contact_company_user_id = this.employees[0].id
                    } else {
                        console.log('error')
                    }

                });
            },
            submit() {
                this.overlay = true;
                this.ticketFrom.from_entity_type = Object.keys(this.ticketFrom.from.item)[0]
                this.ticketFrom.from_entity_id = Object.values(this.ticketFrom.from.item)[0]
                this.ticketFrom.to_entity_type = Object.keys(this.ticketFrom.to.item)[0]
                this.ticketFrom.to_entity_id = Object.values(this.ticketFrom.to.item)[0]
                this.addTicket()
            },
            addTicket() {
                const config = {
                    headers: {'content-type': 'multipart/form-data'}
                }
                let formData = new FormData();
                for (let key in this.ticketFrom) {
                    if (key !== 'files') {
                        formData.append(key, this.ticketFrom[key]);
                    }
                }
                Array.from(this.ticketFrom.files).forEach(file => formData.append('files[]', file));
                axios.post('/api/ticket', formData, config).then(response => {
                    response = response.data
                    if (response.success === true) {
                        window.open('/tickets', '_self')
                    } else {
                        console.log('error')
                    }
                });
            },
        }
    }
</script>
