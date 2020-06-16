<template>


    <v-container
    >
        <v-row
        >
            <v-col
                cols="12"
                sm="12"
                md="8"
            >

                <v-card>
                    <v-toolbar
                    >
                        <v-toolbar-title>Ticket: {{ ticket.name }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-container>
                        <v-row align="center"
                               justify="center">
                        </v-row>
                    </v-container>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-label>
                                    <strong>From:</strong>
                                </v-label>
                                <v-textarea
                                    label="Description"
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    v-text="ticket.from.name"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-label>
                                    <strong>To:</strong>
                                </v-label>
                                <v-textarea
                                    label="To"
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    v-text="ticket.to.name"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-label>
                                    <strong>Priority:</strong>
                                </v-label>
                                <v-textarea
                                    label="Priority"
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    v-text="ticket.priority.name"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-label>
                                    <strong>Due date:</strong>
                                </v-label>
                                <v-textarea
                                    label="Priority"
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    v-text="ticket.due_date || 'No due date'"
                                ></v-textarea>
                            </v-col>
                            <div v-if="ticket.contact">
                                <v-col cols="12" md="6">
                                    <v-label>
                                        <strong>Contact name:</strong>
                                    </v-label>
                                    <v-textarea
                                        label="Priority"
                                        auto-grow
                                        rows="3"
                                        row-height="25"
                                        shaped
                                        disabled
                                        v-text="ticket.contact ? ticket.contact.user_data.name : ''"
                                    ></v-textarea>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-label>
                                        <strong>Contact email:</strong>
                                    </v-label>
                                    <v-textarea
                                        label="Priority"
                                        auto-grow
                                        rows="3"
                                        row-height="25"
                                        shaped
                                        disabled
                                        v-text="ticket.contact ? ticket.contact.user_data.email : ''"
                                    ></v-textarea>
                                </v-col>
                            </div>
                            <v-col cols="12">
                                <v-label>
                                    <strong>Description</strong>
                                </v-label>
                                <v-textarea
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    :value="ticket.description"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-label>
                                    <strong>IP address(es) of the servers (for remote access)</strong>
                                </v-label>
                                <v-textarea
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    :value="ticket.connection_details"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-label>
                                    <strong>Access details:</strong>
                                </v-label>
                                <v-textarea
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    :value="ticket.access_details"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-card-text>
                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-header>
                                Create Answer
                                <template v-slot:actions>
                                    <v-icon color="submit">mdi-plus</v-icon>
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
                                                rows="3"
                                                row-height="25"
                                                shaped
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
                                        <v-btn
                                            dark
                                            fab
                                            right
                                            bottom
                                            color="green"
                                            @click=""
                                        >
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>
                                    </div>
                                </v-form>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panels>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-card>
                    <v-toolbar
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
                                        <h1 class="text-right caption mb-2">{{answer.created_at}}</h1>
                                        <v-list-item-title class="mb-2">{{answer.answer}}</v-list-item-title>
                                        <v-list-item-subtitle>{{answer.employee.user_data.email}}</v-list-item-subtitle>
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
                    <v-toolbar>
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
                            <v-btn color="green" style="color: white;" @click="updateUser">Close Ticket</v-btn>
                        </v-row>
                    </v-card-actions>
                    <v-card-text>
                        <v-expansion-panels>
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
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    label="Name"
                                                    name="team_name"
                                                    type="text"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    label="Description"
                                                    name="team_description"
                                                    type="text"

                                                    required
                                                ></v-text-field>
                                            </div>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                color="green"
                                                @click=""
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    Ticket notices
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <div v-for="historyItem in ticket.history"
                                         :key="historyItem.id"
                                    >
                                        <v-card
                                            class="mx-auto"
                                            outlined
                                        >
                                            <v-list-item three-line>
                                                <v-list-item-content>
                                                    <h1 class="text-right caption mb-2">{{historyItem.created_at}}</h1>
                                                    <v-list-item-title class="mb-2">{{historyItem.description}}</v-list-item-title>
                                                    <v-list-item-subtitle>{{historyItem.employee.user_data.email}}</v-list-item-subtitle>
                                                </v-list-item-content>
                                            </v-list-item>

                                        </v-card>
                                        <v-spacer>
                                            &nbsp;
                                        </v-spacer>
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
                                                    <h1 class="text-right caption mb-2">{{history.created_at}}</h1>
                                                    <v-list-item-title class="mb-2">{{history.description}}</v-list-item-title>
                                                    <v-list-item-subtitle>{{history.employee.user_data.email}}</v-list-item-subtitle>
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
                alert: false,
                errorType: '',
                error: [],
                ticket: {
                    answers: []
                }
            }
        },
        mounted() {
            this.getTicket();
            // if (localStorage.getticket('auth_token')) {
            //     this.$router.push('tickets')
            // }
        },
        methods: {
            getTicket() {
                axios.get(`/api/ticket/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.ticket = response.data
                        // console.log(this.userData);
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
