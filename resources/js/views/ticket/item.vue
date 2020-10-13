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
                color="green"
            >
                <span class="white--text">{{ progressBuffer }} %</span>

            </v-progress-circular>

        </v-overlay>
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
            <v-dialog v-model="answerDialog" max-width="480">
                <v-card outlined dense>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">{{langMap.ticket.create_answer}}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-textarea
                                        :label="langMap.ticket.answer_description"
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
                                        :label="langMap.ticket.add_attachments"
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
                            </div>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="grey darken-1" text @click="answerDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="green" dark @click="addTicketAnswer" >{{langMap.main.create}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="noteDialog" max-width="480">
                <v-card outlined dense>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">{{langMap.ticket.add_internal_note}}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-textarea
                                        :label="langMap.ticket.add_internal_note"
                                        prepend-icon="mdi-text"
                                        color="green"
                                        item-color="green"
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
                        <v-btn color="green" dark @click="addTicketNotice" >{{langMap.main.create}}
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
                            color="green"
                            item-color="green"
                            item-text="name"
                            item-value="id"
                            v-model="linkTicketForm.parent_ticket_id"
                            :items="tickets"
                        />
                        <v-autocomplete
                            :label="langMap.ticket.subject"
                            dense
                            color="green"
                            item-color="green"
                            item-text="name"
                            item-value="id"
                            v-model="linkTicketForm.child_ticket_id"
                            :items="tickets"

                        />
                        <v-textarea
                            :label="langMap.main.description"
                            v-model="linkTicketForm.merge_comment"
                            dense
                            auto-grow
                            rows="2"
                            row-height="25"
                            shaped
                            color="green"
                        />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="ticketLinkDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="green darken-1" text @click="linkTicket()">
                            {{langMap.main.link}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="mergeTicketDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.link}}</v-card-title>
                    <v-card-text>
                        <v-autocomplete
                            :label="langMap.ticket.subject"
                            dense
                            color="green"
                            item-color="green"
                            item-text="name"
                            item-value="id"
                            v-model="mergeTicketForm.parent_ticket_id"
                            :items="tickets"
                        />
                        <v-autocomplete
                            :label="langMap.ticket.subject"
                            dense
                            color="green"
                            item-color="green"
                            item-text="name"
                            item-value="id"
                            v-model="mergeTicketForm.child_ticket_id"
                            :items="tickets"

                        />
                        <v-textarea
                            :label="langMap.main.description"
                            v-model="mergeTicketForm.merge_comment"
                            dense
                            auto-grow
                            rows="2"
                            row-height="25"
                            shaped
                            color="green"
                        />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="mergeTicketDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="green darken-1" text @click="mergeTicket()">
                            {{langMap.main.link}}
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
                    <v-btn small class="ma-2" color="green" style="color: white;"
                           @click="answerDialog = true"
                    >{{langMap.ticket.create_answer}}
                    </v-btn>
                    <v-btn small class="ma-2 d-sm-none d-md-flex" color="#f2f2f2"
                           @click="ticket.merged_parent.length > 0 || ticket.merged_child.length > 0 ? manageThirdColumn() : ticketLinkDialog = true"
                    >{{langMap.main.link}}
                    </v-btn>
                    <v-btn small class="ma-2 d-sm-none d-md-flex" color="#f2f2f2"
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
                            >
                                <v-list-item-title>Spam</v-list-item-title>
                            </v-list-item>

                            <v-list-item
                                link
                                active-class="red--text"
                                color="red"
                            >
                                <v-list-item-title>Delete</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-btn small class="ma-2 d-sm-none d-md-flex" color="#f2f2f2"
                    >
                        Spam
                    </v-btn>
                    <v-btn small class="ma-2 d-sm-none d-md-flex" color="#f2f2f2"
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
                                class="ma-2 d-sm-none d-md-flex float-md-right"
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
                            >
                                <v-list-item-title>
                                    {{langMap.ticket_statuses[langMap.main.close]}}
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
                                class="ma-2 d-sm-none d-md-flex float-md-right"
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
                                onclick="console.log(priority.id);"
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
                                class="ma-2 d-sm-none d-md-flex float-md-right"
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

                                <!--                                    <v-btn-->
                                <!--                                        text-->
                                <!--                                        x-small-->
                                <!--                                        :to="'/individuals/'+ticket.contact.id"-->
                                <!--                                        style="text-transform: none;"-->
                                <!--                                    >-->
                                <!--                                        {{ ticket.contact.user_data.email }}-->
                                <!--                                    </v-btn>-->
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


                        <!--                                <div v-if="ticket.attachments.length > 0 ">-->
                        <!--                                    <strong>{{langMap.main.attachments}}: {{ticket.attachments.length}}-->
                        <!--                                        <v-icon>mdi-paperclip</v-icon>-->
                        <!--                                    </strong>-->
                        <!--                                    <template>-->
                        <!--                                        <div class="text-center"-->
                        <!--                                             v-for="attachment in ticket.attachments"-->
                        <!--                                        >-->
                        <!--                                            <v-chip-->
                        <!--                                                class="ma-2"-->
                        <!--                                                label-->
                        <!--                                                outlined-->
                        <!--                                                color="grey"-->
                        <!--                                                :href="attachment.link"-->
                        <!--                                            >-->
                        <!--                                                {{attachment.name}}-->
                        <!--                                            </v-chip>-->
                        <!--                                        </div>-->
                        <!--                                    </template>-->

                        <!--                                </div>-->
                    </v-card-text>
                </v-card>
                <!--                    </v-col>-->
                <!--                </v-row>-->

                <!--                <v-expansion-panels focusable dense multiple inset>-->
                <!--                    <v-expansion-panel-->


                <!--                    >-->
                <!--                        <v-expansion-panel-header>-->
                <!--                            <h3>{{langMap.ticket.create_answer}}</h3>-->
                <!--                            <template v-slot:actions>-->
                <!--                                <v-icon color="success">mdi-plus</v-icon>-->
                <!--                            </template>-->
                <!--                        </v-expansion-panel-header>-->
                <!--                        <v-expansion-panel-content>-->
                <!--                            <v-form>-->
                <!--                                <div class="row">-->
                <!--                                    <div class="col-md-12">-->
                <!--                                        <v-textarea-->
                <!--                                            :label="langMap.ticket.answer_description"-->
                <!--                                            prepend-icon="mdi-text"-->
                <!--                                            color="green"-->
                <!--                                            item-color="green"-->
                <!--                                            auto-grow-->
                <!--                                            rows="1"-->
                <!--                                            row-height="25"-->
                <!--                                            shaped-->
                <!--                                            v-model="ticketAnswer.answer"-->
                <!--                                            dense-->
                <!--                                        ></v-textarea>-->
                <!--                                    </div>-->
                <!--                                    <div class="col-md-12">-->
                <!--                                        <v-file-input-->
                <!--                                            chips-->
                <!--                                            chips-color="green"-->
                <!--                                            multiple-->
                <!--                                            :label="langMap.ticket.add_attachments"-->
                <!--                                            color="green"-->
                <!--                                            item-color="green"-->
                <!--                                            prepend-icon="mdi-paperclip"-->
                <!--                                            :show-size="1000"-->
                <!--                                            dense-->
                <!--                                            v-on:change="onFileChange('ticketAnswer')"-->
                <!--                                        >-->
                <!--                                            <template v-slot:selection="{ index, text }">-->
                <!--                                                <v-chip-->
                <!--                                                    color="green"-->
                <!--                                                >-->
                <!--                                                    {{ text }}-->
                <!--                                                </v-chip>-->
                <!--                                            </template>-->
                <!--                                        </v-file-input>-->
                <!--                                    </div>-->
                <!--                                    <v-btn-->
                <!--                                        dark-->
                <!--                                        fab-->
                <!--                                        right-->
                <!--                                        bottom-->
                <!--                                        color="green"-->
                <!--                                        small-->
                <!--                                        @click="addTicketAnswer"-->
                <!--                                    >-->
                <!--                                        <v-icon>mdi-plus</v-icon>-->
                <!--                                    </v-btn>-->
                <!--                                </div>-->
                <!--                            </v-form>-->
                <!--                        </v-expansion-panel-content>-->
                <!--                    </v-expansion-panel>-->
                <!--                </v-expansion-panels>-->

                <br>
                <!--                <v-card>-->
                <!--                    <v-toolbar-->
                <!--                        dense-->
                <!--                        color="green"-->
                <!--                        dark-->
                <!--                        flat-->
                <!--                    >-->
                <!--                        <v-toolbar-title>{{langMap.ticket.ticket_actions}}</v-toolbar-title>-->
                <!--                        <v-spacer></v-spacer>-->
                <!--                    </v-toolbar>-->
                <!--                    <v-container>-->
                <!--                        <v-row align="center"-->
                <!--                               justify="center">-->
                <!--                        </v-row>-->
                <!--                    </v-container>-->
                <!--                    <v-card-actions>-->
                <!--                        <v-row align="center"-->
                <!--                               justify="center">-->
                <!--                            <v-btn v-if="ticket.status.id !== 5" color="green" style="color: white;"-->
                <!--                                   @click="closeTicket">{{langMap.main.close}}-->
                <!--                            </v-btn>-->
                <!--                        </v-row>-->
                <!--                    </v-card-actions>-->
                <!--                    <v-card-text>-->
                <!--                        <v-expansion-panels-->
                <!--                            multiple-->
                <!--                            v-model="assignPanel"-->
                <!--                        >-->
                <!--                            <v-expansion-panel>-->
                <!--                                <v-expansion-panel-header>-->
                <!--                                    {{langMap.ticket.assign_to}}:-->
                <!--                                    <template v-slot:actions>-->
                <!--                                        <v-icon color="submit">mdi-plus</v-icon>-->
                <!--                                    </template>-->
                <!--                                </v-expansion-panel-header>-->
                <!--                                <v-expansion-panel-content>-->
                <!--                                    <v-form>-->
                <!--                                        <div class="row">-->
                <!--                                            <v-col cols="md-12">-->
                <!--                                                <v-autocomplete-->
                <!--                                                    color="green"-->
                <!--                                                    item-color="green"-->
                <!--                                                    item-text="name"-->
                <!--                                                    item-value="id"-->
                <!--                                                    v-model="ticket.to_team_id"-->
                <!--                                                    :items="teams"-->
                <!--                                                    :label="langMap.sidebar.team"-->
                <!--                                                    :disabled="selectionDisabled"-->
                <!--                                                    @change="selectTeam"-->
                <!--                                                ></v-autocomplete>-->
                <!--                                            </v-col>-->
                <!--                                            <v-col cols="12">-->
                <!--                                                <v-autocomplete-->
                <!--                                                    :disabled="selectionDisabled"-->
                <!--                                                    color="green"-->
                <!--                                                    item-color="green"-->
                <!--                                                    item-text="employee.user_data.email"-->
                <!--                                                    item-value="employee.id"-->
                <!--                                                    v-model="ticket.to_company_user_id"-->
                <!--                                                    :items="employees"-->
                <!--                                                    :label="langMap.team.members"-->
                <!--                                                ></v-autocomplete>-->
                <!--                                            </v-col>-->
                <!--                                            <v-btn v-if="selectionDisabled === false"-->
                <!--                                                   dark-->
                <!--                                                   fab-->
                <!--                                                   right-->
                <!--                                                   bottom-->
                <!--                                                   color="green"-->
                <!--                                                   @click="updateTicket"-->
                <!--                                            >-->
                <!--                                                <v-icon>mdi-plus</v-icon>-->
                <!--                                            </v-btn>-->
                <!--                                        </div>-->
                <!--                                    </v-form>-->
                <!--                                </v-expansion-panel-content>-->
                <!--                            </v-expansion-panel>-->
                <!--                            <v-expansion-panel v-show="!this.$store.state.roles.includes(6)">-->
                <!--                                <v-expansion-panel-header>-->
                <!--                                    {{langMap.ticket.add_internal_note}}-->
                <!--                                    <template v-slot:actions>-->
                <!--                                        <v-icon color="submit">mdi-plus</v-icon>-->
                <!--                                    </template>-->
                <!--                                </v-expansion-panel-header>-->
                <!--                                <v-expansion-panel-content>-->
                <!--                                    <v-form>-->
                <!--                                        <div class="row">-->
                <!--                                            <v-col cols="12">-->
                <!--                                                <v-textarea-->
                <!--                                                    color="green"-->
                <!--                                                    item-color="green"-->
                <!--                                                    auto-grow-->
                <!--                                                    rows="1"-->
                <!--                                                    v-model="ticketNotice.notice"-->
                <!--                                                ></v-textarea>-->
                <!--                                            </v-col>-->
                <!--                                            <v-btn v-if="selectionDisabled === false"-->
                <!--                                                   dark-->
                <!--                                                   fab-->
                <!--                                                   right-->
                <!--                                                   bottom-->
                <!--                                                   color="green"-->
                <!--                                                   small-->
                <!--                                                   @click="addTicketNotice"-->
                <!--                                            >-->
                <!--                                                <v-icon>mdi-plus</v-icon>-->
                <!--                                            </v-btn>-->
                <!--                                        </div>-->
                <!--                                    </v-form>-->
                <!--                                </v-expansion-panel-content>-->
                <!--                            </v-expansion-panel>-->
                <!--                            <v-expansion-panel-->
                <!--                                v-show="!this.$store.state.roles.includes(6) && ticket.notices.length > 0">-->
                <!--                                <v-expansion-panel-header>-->
                <!--                                    {{langMap.ticket.internal_notes}}:-->
                <!--                                    <template v-slot:actions>-->
                <!--                                        <v-icon color="submit">mdi-plus</v-icon>-->
                <!--                                    </template>-->
                <!--                                </v-expansion-panel-header>-->
                <!--                                <v-expansion-panel-content>-->
                <!--                                    <div v-for="noticeItem in ticket.notices"-->
                <!--                                         :key="noticeItem.id"-->
                <!--                                    >-->
                <!--                                        <v-card-->
                <!--                                            class="mx-auto"-->
                <!--                                            outlined-->
                <!--                                        >-->
                <!--                                            <v-list-item three-line>-->
                <!--                                                <v-list-item-content>-->
                <!--                                                    <v-row>-->
                <!--                                                        <v-col md="6">-->
                <!--                                                            <p class="text-left mb-3">-->
                <!--                                                                {{noticeItem.employee.user_data.name}}-->
                <!--                                                                {{noticeItem.employee.user_data.surname}}-->
                <!--                                                            </p>-->
                <!--                                                        </v-col>-->
                <!--                                                        <v-col md="6">-->
                <!--                                                            <p class="text-right caption mb-2">-->
                <!--                                                                {{noticeItem.created_at}}</p>-->
                <!--                                                        </v-col>-->
                <!--                                                    </v-row>-->
                <!--                                                    <v-list class="mb-2"-->
                <!--                                                            v-html="noticeItem.notice"></v-list>-->
                <!--                                                </v-list-item-content>-->
                <!--                                            </v-list-item>-->
                <!--                                        </v-card>-->
                <!--                                    </div>-->
                <!--                                </v-expansion-panel-content>-->
                <!--                            </v-expansion-panel>-->
                <!--                            <v-expansion-panel>-->
                <!--                                <v-expansion-panel-header>-->
                <!--                                    {{langMap.ticket.ticket_history}}-->
                <!--                                    <template v-slot:actions>-->
                <!--                                        <v-icon color="submit">mdi-plus</v-icon>-->
                <!--                                    </template>-->
                <!--                                </v-expansion-panel-header>-->
                <!--                                <v-expansion-panel-content>-->
                <!--                                    <div v-for="history in ticket.histories"-->
                <!--                                         :key="history.id"-->
                <!--                                    >-->
                <!--                                        <v-card-->
                <!--                                            class="mx-auto"-->
                <!--                                            outlined-->
                <!--                                        >-->
                <!--                                            <v-list-item three-line>-->
                <!--                                                <v-list-item-content>-->
                <!--                                                    <v-row>-->
                <!--                                                        <v-col md="6">-->
                <!--                                                            <p class="text-left mb-3">-->
                <!--                                                                {{history.employee.user_data.name}}-->
                <!--                                                                {{history.employee.user_data.surname}}-->
                <!--                                                            </p>-->
                <!--                                                        </v-col>-->
                <!--                                                        <v-col md="6">-->
                <!--                                                            <p class="text-right caption mb-2">-->
                <!--                                                                {{history.created_at}}</p>-->
                <!--                                                        </v-col>-->
                <!--                                                    </v-row>-->
                <!--                                                    <v-list class="mb-2"-->
                <!--                                                            v-html="history.description"></v-list>-->
                <!--                                                </v-list-item-content>-->
                <!--                                            </v-list-item>-->

                <!--                                        </v-card>-->
                <!--                                        <v-spacer>-->
                <!--                                            &nbsp;-->
                <!--                                        </v-spacer>-->
                <!--                                    </div>-->
                <!--                                </v-expansion-panel-content>-->
                <!--                            </v-expansion-panel>-->
                <!--                            <v-expansion-panel-->
                <!--                                v-if="ticket.merged_parent.length > 0 ||-->
                <!--                                ticket.merged_child.length > 0"-->
                <!--                            >-->
                <!--                                <v-expansion-panel-header>-->
                <!--                                    {{langMap.ticket.merged_tickets}}-->
                <!--                                    <template v-slot:actions>-->
                <!--                                        <v-icon color="submit">mdi-plus</v-icon>-->
                <!--                                    </template>-->
                <!--                                </v-expansion-panel-header>-->
                <!--                                <v-expansion-panel-content>-->

                <!--                                </v-expansion-panel-content>-->
                <!--                            </v-expansion-panel>-->
                <!--                        </v-expansion-panels>-->
                <!--                    </v-card-text>-->
                <!--                </v-card>-->

            </v-col>
            <v-col
                cols="12"
                sm="12"
                :md="secondColumnSize"
            >
                <!--                <v-row>-->
                <!--                    <v-col md="11">-->
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
                                <!--                                    <v-btn-->
                                <!--                                        text-->
                                <!--                                        x-small-->
                                <!--                                        :to="'/individuals/'+ticket.contact.id"-->
                                <!--                                        style="text-transform: none;"-->
                                <!--                                    >-->
                                <!--                                        {{ ticket.contact.user_data.email }}-->
                                <!--                                    </v-btn>-->
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
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>{{langMap.ticket.assign_to}}ments: </strong>
                                <span v-if="ticket.contact !== null">
                                    {{ ticket.assigned_person.user_data.name}}
                                    {{ ticket.assigned_person.user_data.surname}}
                                    <br>
                                </span>
                                    {{ ticket.team.name}}
                            </span>
                            <v-spacer></v-spacer>
                            <template v-slot:actions>
                                <v-btn class="float-md-right"
                                       small color="white"
                                       style="color: black;"
                                >
                                    {{langMap.ticket.assign_to}}
                                </v-btn>
                                <!--                                <v-icon>$expand</v-icon>-->
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br>
                            <v-form>
                                <v-autocomplete
                                    dense
                                    color="green"
                                    item-color="green"
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
                                    color="green"
                                    item-color="green"
                                    item-text="employee.user_data.email"
                                    item-value="employee.id"
                                    v-model="ticket.to_company_user_id"
                                    :items="employees"
                                    :label="langMap.team.members"
                                ></v-autocomplete>
                                <v-btn class="ma-2"
                                       small
                                       color="green"
                                       style="color: white;"
                                       @click.native.stop="updateTicket"
                                >
                                    {{langMap.ticket.assign_to}}
                                </v-btn>
                                <v-btn class="ma-2"
                                       small color="white"
                                       style="color: black;"
                                       @click.native.stop="closeTicket"
                                >
                                    Cancel
                                </v-btn>
                            </v-form>
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
                                <strong>{{langMap.ticket.internal_notes}}</strong>
                            </span>
                            <template v-slot:actions>
                                <v-btn class="ma-2"
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
                                <v-list-item-group color="green">
                                    <!--                                <v-subheader>Parent</v-subheader>-->
                                    <v-list-item
                                        v-for="(item, i) in ticket.merged_parent"
                                        :key="item.id"
                                        link
                                        color="green"
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
                                @click="ticketLinkDialog = true"
                            >
                                New {{langMap.main.link}}
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

    export default {
        data() {
            return {
                selectionDisabled: false,
                assignPanel: [],
                thirdColumn: false,
                thirdColumnPanels: [0],
                firstColumnSize: 7,
                secondColumnSize: 4,
                thirdColumnSize: 0,
                rightSidebarClass: 'd-sm-none d-md-flex',
                ticketLinkDialog: false,
                serverAccessDialog: false,
                answerDialog: false,
                noteDialog: false,
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
                mergeTicketDialog: false,
                tickets: [],
                ticketsSearch: '',
                mergeTicketForm: {
                    parent_ticket_id: null,
                    child_ticket_id: null,
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
                },
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
                        this.selectTeam();
                        this.getContacts(this.from)
                        if (this.ticket.notices.length > 0) {
                            this.assignPanel.push(1);
                        }
                    }
                });
            },
            getTickets() {
                axios.get(`/api/ticket?search=${this.ticketsSearch}&minified=1`)
                    .then(response => {
                        response = response.data
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
            mergeTicketProcess(id) {
                this.mergeTicketForm.parent_ticket_id = id
                this.mergeTicketDialog = true
                this.minifiedTickets = true
            },
            mergeTicket() {
                axios.post('/api/link/ticket', this.mergeTicketForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.minifiedTickets = false
                        this.getTickets()
                        this.mergeTicketForm.merge_comment = null
                        this.mergeTicketForm.parent_ticket_id = null
                        this.mergeTicketForm.child_ticket_id = null
                        this.mergeTicketDialog = false
                    } else {
                        console.log('error')
                    }
                });
            },
            linkTicketProcess(id) {
                this.mergeTicketForm.parent_ticket_id = id
                this.mergeTicketDialog = true
                this.minifiedTickets = true
            },
            linkTicket() {
                axios.post('/api/link/ticket', this.mergeTicketForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        // this.minifiedTickets = false
                        // this.getTickets()
                        this.linkTicketForm.merge_comment = null
                        this.linkTicketForm.parent_ticket_id = null
                        this.linkTicketForm.child_ticket_id = null
                        this.ticketLinkDialog = false
                    } else {
                        console.log('error')
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
