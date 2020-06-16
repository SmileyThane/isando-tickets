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
                        dark
                        flat
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
                                <p><strong>From:</strong> {{ ticket.from.name }}</p>
                            </v-col>
                            <v-col cols="12" md="6">
                                <p><strong>To:</strong> {{ ticket.to.name }}</p>
                            </v-col>
                            <v-col cols="12" md="6">
                                <p><strong>Priority:</strong> {{ ticket.priority.name }}</p>
                            </v-col>
                            <v-col  cols="12" md="6">
                                <p><strong>Due date:</strong> {{ ticket.due_date || 'No due date' }}</p>
                            </v-col>
                            <div v-if="ticket.contact">
                                <v-col cols="12" md="6">
                                    <p><strong>Contact name:</strong> {{ ticket.contact ? ticket.contact.user_data.name
                                        : '' }}</p>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <p><strong>Contact email:</strong> {{ ticket.contact ?
                                        ticket.contact.user_data.email : '' }}</p>
                                </v-col>
                            </div>
                            <v-col cols="12">
                                <v-label>
                                    <strong>Description</strong>
                                </v-label>
                                <v-textarea
                                    label="Description"
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    v-text="ticket.description"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-label>
                                    <strong>IP address(es) of the servers (for remote access)</strong>
                                </v-label>
                                <v-textarea
                                    label="IP address(es) of the servers (for remote access)"
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    v-text="ticket.connection_details"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-label>
                                    <strong>Access details:</strong>
                                </v-label>
                                <v-textarea
                                    label="Access details:"
                                    auto-grow
                                    rows="3"
                                    row-height="25"
                                    shaped
                                    disabled
                                    v-text="ticket.access_details"
                                ></v-textarea>
                            </v-col>
                        </v-row>
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
                                    Ticket history
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
                    name: "",
                    email: "",
                    password: ""
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
