<template>
    <v-container>
        <v-overlay
            :value="!isLoaded"
            light
            opacity="0.45"
        >
            <v-progress-circular
                :rotate="360"
                :size="150"
                :width="15"
                :value="progressBuffer"
                :color="themeColor"
            >
                <span class="white--text">{{ progressBuffer }} %</span>

            </v-progress-circular>

        </v-overlay>
        <v-snackbar
            :bottom="true"
            :right="true"
            v-model="snackbar"
            :color="actionColor"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <template>
            <v-dialog v-model="serverAccessDialog" max-width="480">
                <v-card outlined dense>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">Server access</v-card-title>
                    <v-card-text>
                        <v-label v-if="ticket.connection_details">
                            {{langMap.ticket.ip_address}}:
                        </v-label>
                        <span v-if="ticket.connection_details" v-text="ticket.connection_details">
                            </span>
                        <br>
                        <v-label v-if="ticket.access_details">
                            {{langMap.ticket.access_details}}:
                        </v-label>
                        <span v-if="ticket.access_details" v-text="ticket.access_details">
                            </span>
                        <br>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="serverAccessDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="updateDialog" max-width="60%">
                <v-card outlined dense>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">
                        <span class="text-capitalize">{{langMap.main.edit}}</span>
                        <v-spacer></v-spacer>
                        <v-btn :color="themeColor" dark @click="updateTicket">{{langMap.main.update}}
                        </v-btn>
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-textarea
                                    :label="langMap.ticket.availability_description"
                                    prepend-icon="mdi-text"
                                    :color="themeColor"
                                    :item-color="themeColor"
                                    auto-grow
                                    rows="1"
                                    row-height="25"
                                    shaped
                                    v-model="ticket.availability"
                                    dense
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    :label="langMap.ticket.ip_address"
                                    prepend-icon="mdi-text"
                                    :color="themeColor"
                                    :item-color="themeColor"
                                    auto-grow
                                    rows="1"
                                    row-height="25"
                                    shaped
                                    v-model="ticket.connection_details"
                                    dense
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    :label="langMap.ticket.access_details"
                                    prepend-icon="mdi-text"
                                    :color="themeColor"
                                    :item-color="themeColor"
                                    auto-grow
                                    rows="1"
                                    row-height="25"
                                    shaped
                                    v-model="ticket.access_details"
                                    dense
                                ></v-textarea>
                             </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="answerDialog" max-width="50%">
                <v-card outlined dense>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">{{langMap.ticket.create_answer}}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-label>
                                        <strong>{{langMap.ticket.answer_description}}</strong>
                                    </v-label>
                                    <tiptap-vuetify
                                        v-model="ticketAnswer.answer"
                                        :extensions="extensions"
                                    />
                                </div>
                                <div class="col-md-12">
                                    <v-file-input
                                        chips
                                        multiple
                                        :label="langMap.ticket.add_attachments"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        prepend-icon="mdi-paperclip"
                                        :show-size="1000"
                                        dense
                                        v-on:change="onFileChange('ticketAnswer')"
                                    >
                                        <template v-slot:selection="{ index, text }">
                                            <v-chip
                                                :color="themeColor"
                                            >
                                                {{ text }}
                                            </v-chip>
                                        </template>
                                    </v-file-input>
                                </div>
                            </div>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="grey darken-1" text @click="answerDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn :color="themeColor" dark @click="addTicketAnswer">{{langMap.main.create}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="noteDialog" max-width="480">
                <v-card outlined dense>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">
                        {{langMap.ticket.add_internal_note}}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-textarea
                                        :label="langMap.ticket.add_internal_note"
                                        prepend-icon="mdi-text"
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        auto-grow
                                        rows="1"
                                        row-height="25"
                                        shaped
                                        v-model="ticketNotice.notice"
                                        dense
                                    ></v-textarea>
                                </div>
                            </div>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="grey darken-1" text @click="noteDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn :color="themeColor" dark @click="addTicketNotice">{{langMap.main.create}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="ticketLinkDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.link}}</v-card-title>
                    <v-card-text>
                        <v-autocomplete
                            :label="langMap.ticket.subject"
                            dense
                            :color="themeColor"
                            :item-color="themeColor"
                            item-text="name"
                            item-value="id"
                            v-model="linkTicketForm.parent_ticket_id"
                            :items="tickets"
                        />
                        <v-autocomplete
                            :label="langMap.ticket.subject"
                            dense
                            :color="themeColor"
                            :item-color="themeColor"
                            item-text="name"
                            item-value="id"
                            v-model="linkTicketForm.child_ticket_id"
                            :items="tickets"
                            multiple
                        />
                        <v-textarea
                            :label="langMap.main.description"
                            v-model="linkTicketForm.merge_comment"
                            dense
                            auto-grow
                            rows="2"
                            row-height="25"
                            shaped
                            :color="themeColor"
                        />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn :color="themeColor" darken-1 text @click="ticketLinkDialog = false">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn :color="themeColor" darken-1 text @click="linkTicket()">
                            {{langMap.main.link}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="removeTicketDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.delete_selected}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeTicketDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteTicket()">
                            {{langMap.main.delete}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <v-row v-if="isLoaded">
            <v-col
                cols="12"
                sm="12"
                :md="firstColumnSize + secondColumnSize + thirdColumnSize"
            >
                <v-toolbar dense color="#F0F0F0" class="rounded-lg elevation-2">
                    <v-btn small class="ma-2" :color="themeColor" style="color: white;"
                           @click="answerDialog = true"
                    >{{langMap.ticket.create_answer}}
                    </v-btn>
                    <v-btn small class="ma-2 d-sm-none d-md-flex" color="#f2f2f2"
                           @click="ticket.merged_parent.length > 0 || ticket.merged_child.length > 0 ? manageThirdColumn() : linkTicketProcess()"
                    >{{langMap.main.link}}
                    </v-btn>
                    <v-btn v-if="ticket.parent_id !== null || ticket.child_tickets !== null" small
                           class="ma-2 d-sm-none d-md-flex" color="#f2f2f2"
                           @click="manageThirdColumn"
                    >
                        Merge
                    </v-btn>
                    <v-btn
                        v-show="!thirdColumn"
                        small class="ma-2" color="#f2f2f2"
                        @click="manageThirdColumn"
                    >
                        {{langMap.ticket.ticket_history}}
                    </v-btn>
                    <v-btn small class="ma-2 d-sm-none d-md-flex" :color="this.ticket.is_spam ? 'red' : '#f2f2f2'"
                           @click="markAsSpam"
                    >
                        Spam
                    </v-btn>
                    <v-btn small class="ma-2 d-sm-none d-md-flex" color="#f2f2f2"
                           @click="ticketDeleteProcess"
                    >
                        Delete
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-menu
                        rounded
                        offset-y
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                small
                                class="ma-2 float-md-right"
                                v-bind="attrs"
                                v-on="{...menu}"
                            >
                                <v-badge
                                    :color="ticket.status.color"
                                    inline
                                    dot
                                >
                                    <strong>{{ langMap.ticket_statuses[ticket.status.name] }}</strong>
                                </v-badge>
                            </v-btn>
                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                link
                                @click="closeTicket"
                            >
                                <v-list-item-title>
                                    <!--                                    {{langMap.ticket_statuses['closed']}}-->
                                    Close ticket
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-menu
                        rounded
                        offset-y
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                small
                                class="ma-2 float-md-right"
                                v-bind="attrs"
                                v-on="{...menu}"
                            >
                                <v-badge
                                    :color="ticket.priority.color"
                                    inline
                                    dot
                                >
                                    <strong>{{ langMap.ticket_priorities[ticket.priority.name] }}</strong>
                                </v-badge>
                            </v-btn>
                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                link
                                v-for="priority in priorities"
                                :key="priority.id"
                                @click="changePriority(priority.id)"
                            >
                                <v-list-item-title>
                                    <v-badge
                                        :color="priority.color"
                                        inline
                                        dot
                                    >
                                        <strong>{{ langMap.ticket_priorities[priority.name] }}</strong>
                                    </v-badge>
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-menu
                        rounded
                        offset-y
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                small
                                class="ma-2 float-md-right"
                                v-bind="attrs"
                                v-on="{...menu}"
                            >
                                <strong>Question</strong>
                            </v-btn>

                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                link
                            >
                                <v-list-item-title>
                                    Question
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                link
                            >
                                <v-list-item-title>
                                    Issue
                                </v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                link
                            >
                                <v-list-item-title>
                                    Quote request
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-menu
                        rounded
                        offset-y
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                small
                                class="ma-2 d-sm-flex d-md-none"
                                color="#f2f2f2"
                                v-bind="attrs"
                                v-on="{...menu }"
                            >
                                <v-icon small>mdi-dots-vertical</v-icon>
                            </v-btn>
                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                @click="ticket.merged_parent.length > 0 || ticket.merged_child.length > 0 ? manageThirdColumn() : linkTicketProcess()"
                            >
                                <v-list-item-title>{{langMap.main.link}}</v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                v-if="ticket.parent_id !== null || ticket.child_tickets !== null"
                                @click="manageThirdColumn"
                            >
                                <v-list-item-title>Merge</v-list-item-title>
                            </v-list-item>
                            <v-list-item
                            >
                                <v-list-item-title
                                    :color="this.ticket.is_spam ? 'red' : '#f2f2f2'"
                                    @click="markAsSpam"
                                >Spam
                                </v-list-item-title>
                            </v-list-item>

                            <v-list-item
                                link
                                active-class="red--text"
                                color="red"
                                @click="ticketDeleteProcess"
                            >
                                <v-list-item-title>Delete</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-toolbar>
            </v-col>
            <v-col
                cols="12"
                sm="12"
                :md="firstColumnSize"
            >
                <v-expansion-panels
                    class="d-sm-flex d-md-none"
                    v-model="assignPanel"
                >
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                                        <span>
                                            <strong>Reported by: </strong>
                                            <span v-if="ticket.contact !== null">
                                                {{ ticket.contact.user_data.name}}
                                                {{ ticket.contact.user_data.surname}}
                                                <br>
                                            </span>
                                            {{ ticket.from.name }}
                                            <!--                                    <v-btn-->
                                            <!--                                        text-->
                                            <!--                                        small-->
                                            <!--                                        :to="makeCompanyLink(ticket)"-->
                                            <!--                                        style="text-transform: none;"-->
                                            <!--                                    >-->
                                            <!--                                        {{ ticket.from.name }}-->
                                            <!--                                    </v-btn>-->
                                            <!--                                    <v-btn-->
                                            <!--                                        text-->
                                            <!--                                        small-->
                                            <!--                                        :to="'/individuals/'+ ticket.contact.id"-->
                                            <!--                                        style="text-transform: none;"-->
                                            <!--                                    >-->
                                            <!--                                        {{ ticket.contact.user_data.name }}-->
                                            <!--                                        {{ ticket.contact.user_data.surname }}-->
                                            <!--                                    </v-btn>-->
                            </span>


                            <template v-slot:actions>
                                <v-icon>$expand</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br>
                            <v-btn class="float-md-right"
                                   small color="white"
                                   style="color: black;"
                                   @click="updateDialog = true"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                                {{langMap.main.edit}}
                            </v-btn>
                            <span>
                                    <v-label>
                                        {{langMap.ticket.contact_email}}:
                                    </v-label>
                                <span v-if="ticket.contact !== null">
                                    {{ ticket.contact.user_data.email }}
                                </span>
                                </span>
                            <br>
                            <span>
                                <v-label v-if="ticket.product">
                                    {{langMap.ticket.product_name}}:
                                </v-label>
                                {{ ticket.product.name }}
                            </span>
                            <br>
                            <v-label v-if="ticket.availability">
                                {{langMap.ticket.availability}}:
                            </v-label>
                            <span v-if="ticket.availability" v-html="ticket.availability">
                            </span>
                            <br>
                            <v-btn class="float-md-right"
                                   small color="#F0"
                                   style="color: black;"
                                   @click="serverAccessDialog = true"
                            >
                                <v-icon small>mdi-eye</v-icon>
                                Server access
                            </v-btn>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br class="d-sm-flex d-md-none">
                <v-card
                    outlined
                >
                    <v-card-text>
                        <div v-for="answer in ticket.answers"
                             :key="answer.id"
                        >
                            <v-card
                                class="mx-auto"
                                outlined
                                dense
                            >
                                <v-list-item three-line>
                                    <v-list-item-content>
                                        <strong class="text-left">
                                            {{answer.employee.user_data.name}}
                                            {{answer.employee.user_data.surname}}
                                            {{answer.created_at_time !== '' ? answer.created_at_time :
                                            answer.created_at}}:
                                        </strong>
                                        <div v-html="answer.answer"></div>
                                        <v-col cols="12" v-if="answer.attachments.length > 0 ">
                                            <h4>{{langMap.main.attachments}}</h4>
                                            <div
                                                v-for="attachment in answer.attachments"
                                            >
                                                <v-chip
                                                    class="ma-2"
                                                    :color="themeColor"
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
                        <v-card
                            class="mx-auto"
                            outlined
                            dense
                            v-if="ticket.description"
                            color="#f2f2f2"

                        >
                            <v-list-item three-line>
                                <v-list-item-content>
                                    <strong>{{ ticket.name }}</strong>
                                    <div v-html="ticket.description"></div>
                                </v-list-item-content>
                            </v-list-item>
                        </v-card>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <div v-if="ticket.child_tickets"
                             v-for="child_ticket in ticket.child_tickets"
                             :key="child_ticket.id"
                        >
                            <div v-if="child_ticket.answers.length > 0"
                                 v-for="answer in child_ticket.answers"
                                 :key="answer.id"
                            >
                                <v-card
                                    class="mx-auto"
                                    outlined
                                    dense
                                >
                                    <v-list-item three-line>
                                        <v-list-item-content>
                                            <strong class="text-left">
                                                {{answer.employee.user_data.name}}
                                                {{answer.employee.user_data.surname}}
                                                {{answer.created_at_time !== '' ? answer.created_at_time :
                                                answer.created_at}}:
                                            </strong>
                                            <div v-html="answer.answer"></div>
                                            <v-col cols="12" v-if="answer.attachments.length > 0 ">
                                                <h4>{{langMap.main.attachments}}</h4>
                                                <div
                                                    v-if="answer.attachments.length > 0"
                                                    v-for="attachment in answer.attachments"
                                                >
                                                    <v-chip
                                                        class="ma-2"
                                                        :color="themeColor"
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
                            <v-card
                                class="mx-auto"
                                outlined
                                dense


                                color="#f2f2f2"
                            >
                                <v-list-item three-line>
                                    <v-list-item-content>
                                        <strong>{{ child_ticket.name }}</strong>
                                        <div v-html="child_ticket.description"></div>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-card>
                            <v-spacer>
                                &nbsp;
                            </v-spacer>
                        </div>
                    </v-card-text>
                </v-card>

                <br>

            </v-col>
            <v-col
                cols="12"
                sm="12"
                :md="secondColumnSize"
            >
                <v-expansion-panels
                    v-model="assignPanel"
                    class="d-sm-none d-md-flex"
                >
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                 <strong>Reported by: </strong>
                                 <span v-if="ticket.contact !== null" class="float-md-right">
                                     {{ ticket.contact.user_data.name}}
                                     {{ ticket.contact.user_data.surname}}
                                  </span>
                                <br v-if="ticket.contact !== null">
                                  <span class="float-md-right">
                                     {{ ticket.from.name }}
                                  </span>
                            </span>
                            <v-spacer></v-spacer>
                            <template v-slot:actions>
                                <v-icon>$expand</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br>
                            <v-btn class="float-md-right"
                                   small color="white"
                                   style="color: black;"
                                   @click="updateDialog = true"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                                {{langMap.main.edit}}
                            </v-btn>
                            <span>
                                    <v-label>
                                        {{langMap.ticket.contact_email}}:
                                    </v-label>
                                <span v-if="ticket.contact !== null">
                                    {{ ticket.contact.user_data.email }}
                                </span>
                                </span>
                            <br>
                            <span>
                                <v-label v-if="ticket.product">
                                    {{langMap.ticket.product_name}}:
                                </v-label>
                                {{ ticket.product.name }}
                                <!--                                <v-btn-->
                                <!--                                    text-->
                                <!--                                    x-small-->
                                <!--                                    :to="'/product/'+ticket.product.id"-->
                                <!--                                    style="text-transform: none;"-->
                                <!--                                >-->
                                <!--                                    {{ ticket.product.name }}-->
                                <!--                                </v-btn>-->
                            </span>
                            <br>
                            <v-label v-if="ticket.availability">
                                {{langMap.ticket.availability}}:
                            </v-label>
                            <span v-if="ticket.availability" v-html="ticket.availability">
                            </span>
                            <br>
                            <v-btn class="float-md-right"
                                   small color="#F0"
                                   style="color: black;"
                                   @click="serverAccessDialog = true"
                            >
                                <v-icon small>mdi-eye</v-icon>
                                Server access
                            </v-btn>
                            <br>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br>
                <v-expansion-panels
                    class="d-sm-none d-md-flex"
                >
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                 <strong>Product: </strong>
                                 <span v-if="ticket.product !== null" class="float-md-right">
                                     {{ ticket.product.name}}
                                  </span>
                            </span>
                            <v-spacer></v-spacer>
                            <template v-slot:actions>
                                <v-icon>$expand</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br>
                                <v-select
                                    dense
                                    label="Product"
                                    color="green"
                                    item-color="green"
                                    item-text="name"
                                    item-value="id"
                                    :items="products"
                                    v-model="ticket.to_product_id"
                                    @input="getProducts"
                                />
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br>
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <span v-if="ticket.team !== null">
                                <strong>{{langMap.sidebar.team}}: </strong>
                                    <span class="float-md-right">{{ ticket.team.name }} </span>
                                    <br>
                                </span>
                                 <span v-else>
                                    <strong>{{ langMap.ticket.no_assigned }}</strong>
                                </span>
                                <span v-if="ticket.assigned_person !== null">
                                <strong>{{langMap.team.members}}: </strong>
                                    <span class="float-md-right">
                                        {{ ticket.assigned_person.user_data.name}}
                                        {{ ticket.assigned_person.user_data.surname}}
                                    </span>
                                </span>
                            </span>
                            <v-spacer></v-spacer>
                            <template v-slot:actions>
                                <v-btn class="float-md-right"
                                       small color="white"
                                       style="color: black;"
                                       @click="true"
                                >
                                    {{langMap.main.edit}}
                                </v-btn>
                                <!--                                <v-icon>$expand</v-icon>-->
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br>
                            <v-form>
                                <v-autocomplete
                                    dense
                                    :color="themeColor"
                                    :item-color="themeColor"
                                    item-text="name"
                                    item-value="id"
                                    v-model="ticket.to_team_id"
                                    :items="teams"
                                    :label="langMap.sidebar.team"
                                    :disabled="selectionDisabled"
                                    @change="selectTeam"
                                ></v-autocomplete>
                                <v-autocomplete
                                    dense
                                    :disabled="selectionDisabled"
                                    :color="themeColor"
                                    :item-color="themeColor"
                                    item-text="employee.user_data.email"
                                    item-value="employee.id"
                                    v-model="ticket.to_company_user_id"
                                    :items="employees"
                                    :label="langMap.team.members"
                                ></v-autocomplete>
                                <v-btn class="ma-2"
                                       small
                                       :color="themeColor"
                                       style="color: white;"
                                       @click.native.stop="updateTicket"
                                >
                                    {{langMap.ticket.assign_to}}
                                </v-btn>
                                <v-btn class="ma-2"
                                       small color="white"
                                       style="color: black;"
                                >
                                    Cancel
                                </v-btn>
                            </v-form>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br>
                <v-expansion-panels v-model="notesPanel" multiple>
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>{{langMap.ticket.internal_notes}}</strong>
                            </span>
                            <template v-slot:actions>
                                <v-btn
                                    small color="white"
                                    style="color: black;"
                                    @click.native.stop="noteDialog = true"
                                >
                                    {{langMap.ticket.add_internal_note}}
                                </v-btn>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br>
                            <div v-for="noticeItem in ticket.notices"
                                 :key="noticeItem.id"
                            >
                                <v-card
                                    class="mx-auto"
                                    outlined
                                    dense
                                >
                                    <v-list-item three-line>
                                        <v-list-item-content class="custom-small-text">
                                            <strong>{{noticeItem.employee.user_data.name}}
                                                {{noticeItem.employee.user_data.surname}}
                                                {{noticeItem.created_at}}:</strong>
                                            <div v-html="noticeItem.notice"></div>
                                        </v-list-item-content>

                                    </v-list-item>
                                </v-card>
                                <br>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>

            </v-col>
            <v-col
                v-show="thirdColumn"
                cols="12"
                sm="12"
                :md="thirdColumnSize"
            >
                <v-expansion-panels
                    v-model="thirdColumnPanels"
                    multiple
                >
                    <v-expansion-panel v-if="ticket.merged_parent.length > 0 || ticket.merged_child.length > 0">
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>Linked tickets</strong>
                            </span>

                            <template v-slot:actions>
                                <v-icon @click.native.stop="manageThirdColumn">mdi-close</v-icon>
                                <!--                                <v-icon>$expand</v-icon>-->
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-list
                                dense
                            >
                                <v-list-item-group :color="themeColor">
                                    <!--                                <v-subheader>Parent</v-subheader>-->
                                    <v-list-item
                                        v-for="(item, i) in ticket.merged_parent"
                                        :key="item.id"
                                        link
                                        :color="themeColor"
                                        @click="showTicket(item.parent_ticket_data.id)"
                                    >
                                        <v-list-item-title>
                                            <span class="text-left"> {{item.parent_ticket_data.name}}</span>
                                        </v-list-item-title>
                                    </v-list-item>
                                    <!--                                <v-subheader>Child</v-subheader>-->
                                    <v-list-item
                                        v-for="(item, i) in ticket.merged_child"
                                        :key="item.id"
                                        link
                                        @click="showTicket(item.child_ticket_data.id)"
                                    >
                                        <v-list-item-title>
                                            {{item.child_ticket_data.name}}
                                        </v-list-item-title>
                                    </v-list-item>
                                </v-list-item-group>
                            </v-list>
                            <v-btn
                                small
                                color="#f2f2f2"
                                @click="linkTicketProcess()"
                            >
                                New {{langMap.main.link}}
                            </v-btn>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br v-if="ticket.merged_parent.length > 0 || ticket.merged_child.length > 0">
                <v-expansion-panels
                    multiple
                >
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>Merged tickets</strong>
                            </span>

                            <template v-slot:actions>
                                <v-icon @click.native.stop="manageThirdColumn">mdi-close</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-text-field @input="getTickets" v-model="ticketsSearch" :color="themeColor"
                                          :label="langMap.main.search">
                                <template slot="append">
                                    <v-menu
                                        rounded
                                        transition="slide-y-transition"
                                        bottom
                                    >
                                        <template v-slot:activator="{ on: menu, attrs }">
                                            <v-btn
                                                text
                                                v-bind="attrs"
                                                v-on="{...menu}"
                                            >
                                                <v-icon>$expand</v-icon>
                                            </v-btn>
                                        </template>
                                        <v-list
                                            dense
                                        >
                                            <v-list-item
                                                link
                                                v-for="item in searchCategories"
                                                :key="item.id"
                                                @click="selectSearchCategory(item)"
                                            >
                                                <v-list-item-title>
                                                    {{ item.name }}
                                                </v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </template>
                            </v-text-field>
                            <div style="max-height: 200px; overflow-y: auto;">
                                <v-checkbox v-for="item in tickets"
                                            dense
                                            :key="item.id"
                                            v-model="mergeTicketForm.child_ticket_id"
                                            :value="item.id"
                                            :color="themeColor"
                                            :item-color="themeColor"
                                            :disabled="mergeTicketForm.parent_ticket_id === item.id"
                                            :label="item.name"
                                            hide-details
                                >
                                    <template slot="append">
                                        <v-checkbox
                                            dense
                                            :key="item.id"
                                            v-model="mergeTicketForm.parent_ticket_id"
                                            :value="item.id"
                                            color="red"
                                            item-color="red"
                                            label="Make primary"
                                            hide-details
                                        />
                                    </template>
                                </v-checkbox>
                            </div>
                            <v-textarea
                                :label="langMap.main.description"
                                v-model="mergeTicketForm.merge_comment"
                                dense
                                auto-grow
                                rows="2"
                                row-height="25"
                                shaped
                                :color="themeColor"
                            />
                            <v-spacer></v-spacer>
                            <v-btn color="red" text>{{langMap.main.cancel}}
                            </v-btn>
                            <v-btn color="green" text @click="mergeTicket()">
                                {{langMap.ticket.merge}}
                            </v-btn>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br>
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>{{langMap.ticket.ticket_history}}</strong>
                            </span>
                            <template v-slot:actions>
                                <v-icon @click.native.stop="manageThirdColumn">mdi-close</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-list dense>
                                <v-list-item-group>
                                    <v-list-item
                                        v-for="(history, i) in ticket.histories"
                                        :key="history.id"
                                    >
                                        <v-list-item-title>
                                            <strong class="text-left">
                                                {{history.employee.user_data.name}}
                                                {{history.employee.user_data.surname}}
                                                {{history.created_at}}:
                                            </strong>
                                            <span>{{history.description}}</span>
                                        </v-list-item-title>
                                    </v-list-item>
                                </v-list-item-group>
                            </v-list>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>

            </v-col>
            <v-col
                :class="rightSidebarClass"
                md="1"
                v-show="!thirdColumn"
            >
                <v-navigation-drawer
                    absolute
                    permanent
                    mini-variant
                    right
                >
                    <v-divider></v-divider>
                    <v-list
                        nav
                        dense
                    >
                        <v-list-item link>
                            <v-list-item-icon>
                                <v-icon>mdi-folder</v-icon>
                            </v-list-item-icon>
                        </v-list-item>
                        <v-list-item link>
                            <v-list-item-icon>
                                <v-icon>mdi-account-multiple</v-icon>
                            </v-list-item-icon>
                        </v-list-item>
                        <v-list-item link>
                            <v-list-item-icon>
                                <v-icon>mdi-star</v-icon>
                            </v-list-item-icon>
                        </v-list-item>
                    </v-list>
                </v-navigation-drawer>
            </v-col>
        </v-row>
    </v-container>

</template>

<script>
    import {
        Blockquote,
        Bold,
        BulletList,
        Code,
        HardBreak,
        Heading,
        History,
        HorizontalRule,
        Image,
        Italic,
        Link,
        ListItem,
        OrderedList,
        Paragraph,
        Strike,
        TiptapVuetify,
        Underline
    } from 'tiptap-vuetify'

    export default {
        components: {TiptapVuetify},
        data() {
            return {
                extensions: [
                    History,
                    Blockquote,
                    Link,
                    Underline,
                    Strike,
                    Italic,
                    ListItem,
                    BulletList,
                    OrderedList,
                    [Heading, {
                        options: {
                            levels: [1, 2, 3]
                        }
                    }],
                    Bold,
                    Code,
                    HorizontalRule,
                    Paragraph,
                    Image,
                    HardBreak
                ],
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                themeColor: this.$store.state.themeColor,
                selectionDisabled: false,
                assignPanel: [],
                notesPanel: [],
                thirdColumn: false,
                thirdColumnPanels: [0],
                firstColumnSize: 7,
                secondColumnSize: 4,
                thirdColumnSize: 0,
                rightSidebarClass: 'd-sm-none d-md-flex',
                ticketLinkDialog: false,
                serverAccessDialog: false,
                updateDialog: false,
                answerDialog: false,
                noteDialog: false,
                removeTicketDialog: false,
                alert: false,
                fromEdit: false,
                contactEdit: false,
                ipEdit: false,
                detailsEdit: false,
                priorityEdit: false,
                submitEdit: false,
                isLoaded: false,
                progressBuffer: 0,
                errorType: '',
                error: [],
                suppliers: [],
                products: [],
                priorities: [],
                employees: [],
                contacts: [],
                teams: [],
                from: [],
                langMap: this.$store.state.lang.lang_map,
                tickets: [],
                mergeParentTickets: [],
                linkParentTickets: [],
                ticketsSearch: '',
                searchCategories: [
                    {
                        id: 1,
                        name: 'ID'
                    },
                    {
                        id: 2,
                        name: 'Subject'
                    },
                    {
                        id: 3,
                        name: 'Contact'
                    }
                ],
                mergeTicketForm: {
                    parent_ticket_id: null,
                    child_ticket_id: [],
                    merge_comment: null
                },
                linkTicketForm: {
                    parent_ticket_id: null,
                    child_ticket_id: null,
                    merge_comment: null
                },
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
                    availability: '',
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
                    // console.log(event.target.files);
                    this[form].files = event.target.files;
                }
            }
        },
        watch: {
            checkProgress(value) {
                if (value === 100) {
                    this.isLoaded = true;
                }
                console.log(`val ${value}`);
            }
        },
        mounted() {
            this.getTicket();
            this.getSuppliers()
            this.getProducts()
            this.getPriorities()
            this.getTeams()
            this.getTickets()
            // if (localStorage.getticket('auth_token')) {
            //     this.$router.push('tickets')
            // }
        },
        methods: {
            selectSearchCategory(item) {
                this.searchLabel = item.name
            },
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
                        this.progressBuffer = this.progressBuffer + 40;
                        this.$store.state.pageName = '#' + this.ticket.id + ' ' + this.ticket.name
                        this.spamButtonColor = this.ticket.is_spam ? 'red' : '#f2f2f2'
                        this.mergeTicketForm.parent_ticket_id = this.ticket.id
                        this.selectTeam();
                        this.getContacts(this.from)
                        if (this.ticket.notices.length > 0) {
                            this.notesPanel.push(0);
                        }

                    }
                });
            },
            getTickets() {
                axios.get(`/api/ticket?search=${this.ticketsSearch}&minified=1`)
                    .then(response => {
                        response = response.data
                        if (this.ticketsSearch === '') {
                            this.mergeParentTickets = response.data.data
                            this.linkParentTickets = response.data.data
                        }
                        this.tickets = response.data.data
                    });
            },
            getSuppliers() {
                axios.get('/api/supplier').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.suppliers = response.data
                        this.progressBuffer = this.progressBuffer + 20;
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
                        this.progressBuffer = this.progressBuffer + 10;
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
                        this.progressBuffer = this.progressBuffer + 20;
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
            getTeams() {
                axios.get(`/api/team`
                ).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.teams = response.data.data
                        this.progressBuffer = this.progressBuffer + 10;
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
                        this.updateDialog = false
                    } else {
                        console.log('error')
                    }
                    // this.submitEdit = this.fromEdit = this.contactEdit = this.priorityEdit = this.ipEdit = this.detailsEdit = false
                });
            },
            closeTicket() {
                this.ticket.status_id = 5;
                this.updateTicket();
            },
            showTicket(item) {
                // this.$router.push(`/ticket/${item.id}`)
                window.location.href = `/ticket/${item}`
            },
            addTicketAnswer() {
                const config = {
                    headers: {'content-type': 'multipart/form-data'}
                }
                // console.log(this.ticketAnswer);
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
                        this.answerDialog = false
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
                        this.noteDialog = false
                    } else {
                        console.log('error')
                    }
                });
            },
            makeCompanyLink(ticket) {
                let redirectToEntity = ticket.from_entity_type.substring(ticket.from_entity_type.lastIndexOf("\\") + 1).toLowerCase()
                return `/${redirectToEntity}/${ticket.from_entity_id}`
            },
            manageThirdColumn() {
                if (this.thirdColumn === false) {
                    this.firstColumnSize = 6
                    this.secondColumnSize = 3
                    this.thirdColumnSize = 3
                    this.thirdColumn = true
                    this.rightSidebarClass = ''
                } else {
                    this.firstColumnSize = 7
                    this.secondColumnSize = 4
                    this.thirdColumnSize = 0
                    this.thirdColumn = false
                    this.rightSidebarClass = 'd-sm-none d-md-flex'
                }
            },
            mergeTicket() {
                axios.post('/api/merge/ticket', this.mergeTicketForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getTickets()
                        this.getTicket()
                    } else {
                        console.log('error')
                    }
                });
            },
            markAsSpam() {
                axios.post('/api/spam/ticket', {'id': this.ticket.id}).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.snackbarMessage = 'Ticket marked as spam'
                        this.actionColor = 'alert'
                        this.snackbar = true;
                        this.getTicket();
                    } else {
                        console.log('error')
                    }
                });
            },
            changePriority(id) {
                this.ticket.priority_id = id
                this.updateTicket()
            },
            linkTicketProcess() {
                this.linkTicketForm.parent_ticket_id = this.ticket.id
                this.ticketLinkDialog = true
            },
            linkTicket() {
                axios.post('/api/link/ticket', this.mergeTicketForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.linkTicketForm.merge_comment = null
                        this.linkTicketForm.parent_ticket_id = null
                        this.linkTicketForm.child_ticket_id = null
                        this.ticketLinkDialog = false
                    } else {
                        console.log('error')
                    }
                });
            },
            ticketDeleteProcess() {
                this.removeTicketDialog = true
            },
            deleteTicket() {
                axios.delete(`/api/ticket/${this.ticket.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        window.location.href = `/tickets`
                    } else {
                        this.snackbarMessage = 'Ticket delete error'
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            },
        },
        computed: {
            checkProgress: function () {
                return this.progressBuffer
            },
        }
    }
</script>
<style>
    .custom-small-text {
        font-size: .875rem;
    }
</style>
