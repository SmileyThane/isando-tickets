<template>


    <v-container>
        <v-row>
            <v-col
                cols="12"
                sm="12"
                md="8"
            >
                <v-card>
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title class="text-truncate" style="max-width: 60%">
                            #{{ ticket.id }} {{ ticket.name }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-chip
                            :color="ticket.status.color" outlined label class="float-md-right">
                            <strong>{{ ticket.status.name }}</strong>
                        </v-chip>
                        &nbsp;
                        <v-chip
                            color="white" light label class="float-md-right">
                            <v-badge
                                :color="ticket.priority.color"
                                inline
                                dot
                            >
                                <strong>{{ ticket.priority.name }}</strong>
                            </v-badge>
                        </v-chip>
                        &nbsp;
                        <v-btn v-show="ticket.can_be_edited && submitEdit" class="float-md-right"
                               small color="white" style="color: black;" @click="updateTicket">Save
                        </v-btn>
                    </v-toolbar>
                    <v-container>
                        <v-row align="center"
                               justify="center">
                        </v-row>
                    </v-container>
                    <v-card-text>
                        <v-row dense>
                            <v-col cols="6">
                                <v-select
                                    dense
                                    label="From"
                                    color="green"
                                    item-color="green"
                                    item-text="name"
                                    item-value="item"
                                    :items="suppliers"
                                    :filled="fromEdit"
                                    v-model="from"
                                    :readonly="!fromEdit"
                                    @input="getContacts"
                                >
                                    <template slot="append">
                                        <v-btn
                                            text
                                            small
                                            v-show="ticket.can_be_edited"
                                            :disabled="fromEdit"
                                            @click="toggleEdit('fromEdit')"
                                        >
                                            <v-icon>{{!fromEdit ? 'mdi-pencil' : '$expand'}}</v-icon>
                                        </v-btn>
                                    </template>
                                </v-select>
                                <v-autocomplete
                                    label="Contact email"
                                    dense
                                    color="green"
                                    item-color="green"
                                    item-text="user_data.email"
                                    item-value="id"
                                    v-model="ticket.contact_company_user_id"
                                    :items="contacts"
                                    :filled="contactEdit"
                                    :readonly="!contactEdit"
                                >
                                    <template slot="append">
                                        <v-btn
                                            text
                                            small
                                            v-show="ticket.can_be_edited"
                                            :disabled="contactEdit"
                                            @click="toggleEdit('contactEdit')"
                                        >
                                            <v-icon>{{contactEdit ? '$expand' : 'mdi-pencil'}}</v-icon>
                                        </v-btn>
                                    </template>
                                </v-autocomplete>
                                <p v-if="ticket.contact ">
                                    {{ticket.contact.user_data.name}} {{ticket.contact.user_data.surname}}
                                </p>
                            </v-col>
                            <v-col cols="6">
                                <v-select
                                    dense
                                    label="Product"
                                    color="green"
                                    item-color="green"
                                    item-text="product_data.name"
                                    item-value="product_data.id"
                                    :items="products"
                                    v-model="ticket.to_product_id"
                                    :disabled="!fromEdit"
                                    @input="getProducts"
                                />
                            </v-col>
                            <v-col cols="12">
                                <v-label v-if="ticket.description">
                                    <strong>Description</strong>
                                </v-label>
                                <strong>{{ ticket.name }}</strong>
                                <div v-html="ticket.description"></div>
                                <v-spacer>
                                    &nbsp;

                                </v-spacer>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-textarea
                                    label="IP address(es) of the servers (for remote access)"
                                    dense
                                    auto-grow
                                    rows="2"
                                    row-height="25"
                                    shaped
                                    color="green"
                                    :filled="ipEdit"
                                    :readonly="!ipEdit"
                                    v-model="ticket.connection_details"
                                >
                                    <template slot="append">
                                        <v-btn
                                            text
                                            small
                                            v-show="ticket.can_be_edited"
                                            :disabled="ipEdit"
                                            @click="toggleEdit('ipEdit')"
                                        >
                                            <v-icon>{{ipEdit ? '' : 'mdi-pencil'}}</v-icon>
                                        </v-btn>
                                    </template>
                                </v-textarea>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-textarea
                                    dense
                                    auto-grow
                                    rows="2"
                                    row-height="25"
                                    shaped
                                    color="green"
                                    :filled="detailsEdit"
                                    :readonly="!detailsEdit"
                                    v-model="ticket.access_details"
                                >
                                    <template slot="append">
                                        <v-btn
                                            text
                                            small
                                            v-show="ticket.can_be_edited"
                                            :disabled="detailsEdit"
                                            @click="toggleEdit('detailsEdit')"
                                        >
                                            <v-icon>{{detailsEdit ? '' : 'mdi-pencil'}}</v-icon>
                                        </v-btn>
                                    </template>
                                </v-textarea>
                            </v-col>
                        </v-row>

                        <v-expansion-panels focusable dense multiple inset>
                            <v-expansion-panel v-if="ticket.attachments.length > 0 ">
                                <v-expansion-panel-header>
                                    <template v-slot:actions>
                                        <v-icon>$expand</v-icon>
                                    </template>
                                    <strong>Attachments: {{ticket.attachments.length}}
                                        <v-icon>mdi-paperclip</v-icon>
                                    </strong>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <div
                                        v-for="attachment in ticket.attachments"
                                    >
                                        <v-chip
                                            class="ma-2"
                                            color="green"
                                            text-color="white"
                                            :href="attachment.link"
                                        >
                                            {{attachment.name}}
                                        </v-chip>
                                    </div>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                            <v-expansion-panel


                            >
                                <v-expansion-panel-header>
                                    <h3>Create Answer</h3>
                                    <template v-slot:actions>
                                        <v-icon color="success">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <v-textarea
                                                    label="Description"
                                                    prepend-icon="mdi-text"
                                                    color="green"
                                                    item-color="green"
                                                    auto-grow
                                                    rows="1"
                                                    row-height="25"
                                                    shaped
                                                    v-model="ticketAnswer.answer"
                                                    dense
                                                ></v-textarea>
                                            </div>
                                            <div class="col-md-12">
                                                <v-file-input
                                                    chips
                                                    chips-color="green"
                                                    multiple
                                                    label="Attach a Document(s)"
                                                    color="green"
                                                    item-color="green"
                                                    prepend-icon="mdi-paperclip"
                                                    :show-size="1000"
                                                    dense
                                                    v-on:change="onFileChange('ticketAnswer')"
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
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                color="green"
                                                small
                                                @click="addTicketAnswer"
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>
                <br>
                <v-card>
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Ticket Answers</v-toolbar-title>
                    </v-toolbar>
                    <v-container>
                        <v-row align="center"
                               justify="center">
                        </v-row>
                    </v-container>
                    <v-card-text>
                        <div v-for="answer in ticket.answers"
                             :key="answer.id"
                        >
                            <v-card
                                class="mx-auto"
                                outlined
                            >
                                <v-list-item three-line>
                                    <v-list-item-content>
                                        <v-row>
                                            <v-col md="6">
                                                <p class="text-left mb-3">{{answer.employee.user_data.name}}
                                                    {{answer.employee.user_data.surname}} responded
                                                    {{answer.created_at_time}}:</p>
                                            </v-col>
                                            <v-col md="6">
                                                <p class="text-right caption mb-2">{{answer.created_at}}</p>
                                            </v-col>
                                        </v-row>
                                        <!--                                        <v-list-item-subtitle class="mb-3">{{answer.employee.user_data.email}}-->
                                        <!--                                        </v-list-item-subtitle>-->
                                        <v-list class="mb-2" v-html="answer.answer"></v-list>

                                        <v-col cols="12" v-if="answer.attachments.length > 0 ">
                                            <h4>Attachments</h4>
                                            <div
                                                v-for="attachment in answer.attachments"
                                            >
                                                <v-chip
                                                    class="ma-2"
                                                    color="green"
                                                    text-color="white"
                                                    :href="attachment.link"
                                                >
                                                    {{attachment.name}}
                                                </v-chip>
                                            </div>

                                        </v-col>
                                    </v-list-item-content>

                                </v-list-item>

                            </v-card>
                            <v-spacer>
                                &nbsp;
                            </v-spacer>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col
                cols="12"
                sm="12"
                md="4"
            >
                <v-card>
                    <v-toolbar
                        dense
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Ticket Actions</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-container>
                        <v-row align="center"
                               justify="center">
                        </v-row>
                    </v-container>
                    <v-card-actions>
                        <v-row align="center"
                               justify="center">
                            <v-btn v-if="ticket.status.id !== 5" color="green" style="color: white;"
                                   @click="closeTicket">Close Ticket
                            </v-btn>
                        </v-row>
                    </v-card-actions>
                    <v-card-text>
                        <v-expansion-panels
                            multiple
                            v-model="assignPanel"
                        >
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    Assign to:
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="md-12">
                                                <v-autocomplete
                                                    color="green"
                                                    item-color="green"
                                                    item-text="name"
                                                    item-value="id"
                                                    v-model="ticket.to_team_id"
                                                    :items="ticket.to.teams"
                                                    label="Team"
                                                    :disabled="selectionDisabled"
                                                    @change="selectTeam"
                                                ></v-autocomplete>
                                            </v-col>
                                            <v-col cols="12">
                                                <v-autocomplete
                                                    :disabled="selectionDisabled"
                                                    color="green"
                                                    item-color="green"
                                                    item-text="employee.user_data.email"
                                                    item-value="employee.id"
                                                    v-model="ticket.to_company_user_id"
                                                    :items="employees"
                                                    label="Person"
                                                ></v-autocomplete>
                                            </v-col>
                                            <v-btn v-if="selectionDisabled === false"
                                                   dark
                                                   fab
                                                   right
                                                   bottom
                                                   color="green"
                                                   @click="updateTicket"
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                            <v-expansion-panel v-show="!this.$store.state.roles.includes(6)">
                                <v-expansion-panel-header>
                                    Ticket notices
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="12">
                                                <v-textarea
                                                    color="green"
                                                    item-color="green"
                                                    v-model="ticketNotice.notice"
                                                    label="Notice"
                                                ></v-textarea>
                                            </v-col>
                                            <v-btn v-if="selectionDisabled === false"
                                                   dark
                                                   fab
                                                   right
                                                   bottom
                                                   color="green"
                                                   @click="addTicketNotice"
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <div v-for="noticeItem in ticket.notices"
                                         :key="noticeItem.id"
                                    >
                                        <v-card
                                            class="mx-auto"
                                            outlined
                                        >
                                            <v-list-item three-line>
                                                <v-list-item-content>
                                                    <v-row>
                                                        <v-col md="6">
                                                            <p class="text-left mb-3">
                                                                {{noticeItem.employee.user_data.name}}
                                                                {{noticeItem.employee.user_data.surname}} responded </p>
                                                        </v-col>
                                                        <v-col md="6">
                                                            <p class="text-right caption mb-2">
                                                                {{noticeItem.created_at}}</p>
                                                        </v-col>
                                                    </v-row>
                                                    <v-list class="mb-2" v-html="noticeItem.notice"></v-list>
                                                </v-list-item-content>
                                            </v-list-item>
                                        </v-card>
                                    </div>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    Ticket history
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <div v-for="history in ticket.histories"
                                         :key="history.id"
                                    >
                                        <v-card
                                            class="mx-auto"
                                            outlined
                                        >
                                            <v-list-item three-line>
                                                <v-list-item-content>
                                                    <p class="text-right caption mb-2">{{history.created_at}}</p>
                                                    <v-list-item-title class="mb-2">{{history.description}}
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle>{{history.employee.user_data.email}}
                                                    </v-list-item-subtitle>
                                                </v-list-item-content>
                                            </v-list-item>

                                        </v-card>
                                        <v-spacer>
                                            &nbsp;
                                        </v-spacer>
                                    </div>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
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
                selectionDisabled: false,
                assignPanel: [],
                alert: false,
                fromEdit: false,
                contactEdit: false,
                ipEdit: false,
                detailsEdit: false,
                priorityEdit: false,
                submitEdit: false,
                errorType: '',
                error: [],
                suppliers: [],
                products: [],
                priorities: [],
                employees: [],
                contacts: [],
                from: [],
                ticket: {
                    status_id: '',
                    to_company_user_id: '',
                    attachments: [{
                        name: '',
                        link: ''
                    }
                    ],
                    from: {
                        name: ''
                    },
                    from_entity_type: '',
                    from_entity_id: '',
                    to: {
                        name: ''
                    },
                    priority: {
                        name: ''
                    },
                    can_be_edited: '',
                    contact: {
                        user_data: {
                            name: '',
                            surname: '',
                            email: ''
                        }
                    },
                    status: {
                        name: ''
                    },
                    to_entity_type: '',
                    to_entity_id: '',
                    to_team_id: '',
                    contact_company_user_id: '',
                    to_product_id: '',
                    priority_id: '',
                    name: '',
                    description: '',
                    connection_details: '',
                    access_details: '',
                    answers: [
                        {
                            created_at: '',
                            files: [],
                            attachments: [{
                                name: '',
                                link: ''
                            }],
                            employee: {
                                user_data: {
                                    name: '',
                                    email: '',
                                    surname: ''
                                }
                            },
                            answer: ''
                        }
                    ],
                    histories: [
                        {
                            created_at: '',
                            files: [],
                            attachments: [{
                                name: '',
                                link: ''
                            }],
                            employee: {
                                user_data: {
                                    name: '',
                                    email: ''
                                }
                            },
                            description: ''
                        }
                    ],
                    notices: [
                        {
                            created_at: '',
                            files: [],
                            attachments: [{
                                name: '',
                                link: ''
                            }],
                            employee: {
                                user_data: {
                                    name: '',
                                    email: ''
                                }
                            },
                            description: ''
                        }
                    ],
                },
                ticketAnswer: {
                    files: [],
                    answer: ''
                },
                ticketNotice: {
                    notice: '',
                },
                onFileChange(form) {
                    this[form].files = null;
                    console.log(event.target.files);
                    this[form].files = event.target.files;
                },
            }
        },
        mounted() {
            this.getSuppliers()
            this.getTicket();
            this.getProducts()
            this.getPriorities()
            // if (localStorage.getticket('auth_token')) {
            //     this.$router.push('tickets')
            // }
        },
        methods: {
            toggleEdit(edit) {
                this[edit] = !this[edit]
                this.submitEdit = this.fromEdit ||
                    this.contactEdit ||
                    this.priorityEdit ||
                    this.ipEdit ||
                    this.detailsEdit
            },
            getTicket() {
                axios.get(`/api/ticket/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.ticket = response.data
                        this.from = {[this.ticket.from_entity_type]: this.ticket.from_entity_id}
                        this.selectTeam();
                        this.getContacts(this.from)
                        if (this.ticket.notices.length > 0) {
                            this.assignPanel.push(1);
                        }
                    }
                });
            },
            getSuppliers() {
                axios.get('/api/supplier').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.suppliers = response.data
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
                    } else {
                        console.log('error')
                    }

                });
            },
            getContacts(entityItem) {
                this.contacts = []
                // this.ticket.contact_company_user_id = null;
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
                            response.employees.forEach(employeeItem => this.contacts.push(employeeItem.employee))
                            // console.log('client');
                        } else {
                            this.contacts = response.employees
                            // console.log('company');
                        }
                        // this.ticketForm.contact_company_user_id = this.employees[0].id
                    } else {
                        console.log('error')
                    }

                });
            },
            selectTeam() {
                if (this.ticket.can_be_edited === false) {
                    this.selectionDisabled = true
                }
                if (this.ticket.to_team_id !== null) {
                    // if (this.ticket.to_company_user_id) {
                    //     this.selectionDisabled = true
                    // }
                    this.assignPanel = [0];
                    axios.get(`/api/team/${this.ticket.to_team_id}`).then(response => {
                        response = response.data
                        if (response.success === true) {
                            this.employees = response.data.employees
                            // console.log(this.employees);
                        }
                    });

                }
            },
            updateTicket() {
                this.ticket.from_entity_id = Object.values(this.from)[0]
                this.ticket.from_entity_type = Object.keys(this.from)[0]
                axios.patch(`/api/ticket/${this.$route.params.id}`, this.ticket).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getTicket()
                    } else {
                        console.log('error')
                    }
                    this.submitEdit = this.fromEdit = this.contactEdit = this.priorityEdit = this.ipEdit = this.detailsEdit = false
                });
            },
            closeTicket() {
                this.ticket.status_id = 5;
                this.updateTicket();
            },
            addTicketAnswer() {
                const config = {
                    headers: {'content-type': 'multipart/form-data'}
                }
                console.log(this.ticketAnswer);
                let formData = new FormData();
                for (let key in this.ticketAnswer) {
                    if (key !== 'files') {
                        formData.append(key, this.ticketAnswer[key]);
                    }
                }
                Array.from(this.ticketAnswer.files).forEach(file => formData.append('files[]', file));
                axios.post(`/api/ticket/${this.$route.params.id}/answer`, formData, config).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.ticketAnswer.answer = ''
                        this.ticketAnswer.files = []
                        this.getTicket()
                    } else {
                        console.log('error')
                    }
                });
            },
            addTicketNotice() {
                axios.post(`/api/ticket/${this.$route.params.id}/notice`, this.ticketNotice).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.ticketNotice.notice = ''
                        this.getTicket()
                    } else {
                        console.log('error')
                    }
                });
            }
        }
    }
</script>
