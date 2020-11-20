<template>
    <v-container fluid>
        <v-overlay
            :value="!isLoaded"
            light
            opacity="0.45"
        >
            <v-progress-circular
                :color="themeColor"
                :rotate="360"
                :size="150"
                :value="progressBuffer"
                :width="15"
            >
                <span class="white--text">{{ progressBuffer }} %</span>

            </v-progress-circular>

        </v-overlay>
        <v-snackbar
            :bottom="true"
            :color="actionColor"
            :right="true"
            v-model="snackbar"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <template>
            <v-dialog max-width="480" v-model="serverAccessDialog">
                <v-card dense outlined>
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
                        <v-btn @click="serverAccessDialog = false" color="grey darken-1" text>{{langMap.main.cancel}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog max-width="60%" v-model="updateDialog">
                <v-card dense outlined>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">
                        <span class="text-capitalize">{{langMap.main.edit}}</span>
                        <v-spacer></v-spacer>
                        <v-btn :color="themeColor" @click="updateTicket" dark>{{langMap.main.update}}
                        </v-btn>
                    </v-card-title>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12">
                                <v-textarea
                                    :color="themeColor"
                                    :item-color="themeColor"
                                    :label="langMap.ticket.availability_description"
                                    auto-grow
                                    dense
                                    prepend-icon="mdi-text"
                                    row-height="25"
                                    rows="1"
                                    v-model="ticket.availability"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    :color="themeColor"
                                    :item-color="themeColor"
                                    :label="langMap.ticket.ip_address"
                                    auto-grow
                                    dense
                                    prepend-icon="mdi-text"
                                    row-height="25"
                                    rows="1"
                                    v-model="ticket.connection_details"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    :color="themeColor"
                                    :item-color="themeColor"
                                    :label="langMap.ticket.access_details"
                                    auto-grow
                                    dense
                                    prepend-icon="mdi-text"
                                    row-height="25"
                                    rows="1"
                                    v-model="ticket.access_details"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog max-width="50%" v-model="answerDialog">
                <v-card dense outlined>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">{{langMap.ticket.create_answer}}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <div class="row">
                                <div class="col-md-12">
                                    <tiptap-vuetify
                                        :extensions="extensions"
                                        v-model="ticketAnswer.answer"
                                        :placeholder="langMap.ticket.answer_description"
                                    />
                                </div>
                                <div class="col-md-12">
                                    <v-file-input
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        :label="langMap.ticket.add_attachments"
                                        :show-size="1000"
                                        chips
                                        dense
                                        multiple
                                        prepend-icon="mdi-paperclip"
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
                        <v-btn @click="answerDialog = false" color="grey darken-1" text>{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn :color="themeColor" @click="addTicketAnswer" dark>{{langMap.main.create}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog max-width="480" v-model="noteDialog">
                <v-card dense outlined>
                    <v-card-title class="headline" style="background-color: #F0F0F0;">
                        {{langMap.ticket.add_internal_note}}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <div class="row">
                                <div class="col-md-12">
                                    <v-textarea
                                        :color="themeColor"
                                        :item-color="themeColor"
                                        :label="langMap.ticket.add_internal_note"
                                        auto-grow
                                        dense
                                        prepend-icon="mdi-text"
                                        row-height="25"
                                        rows="1"
                                        v-model="ticketNotice.notice"
                                    ></v-textarea>
                                </div>
                            </div>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="noteDialog = false" color="grey darken-1" text>{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn :color="themeColor" @click="addTicketNotice" dark>{{langMap.main.create}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog max-width="480" persistent v-model="ticketLinkDialog">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.link}}</v-card-title>
                    <v-card-text>
                        <v-autocomplete
                            :color="themeColor"
                            :item-color="themeColor"
                            :items="tickets"
                            :label="langMap.ticket.subject"
                            dense
                            item-text="name"
                            item-value="id"
                            v-model="linkTicketForm.parent_ticket_id"
                        />
                        <v-autocomplete
                            :color="themeColor"
                            :item-color="themeColor"
                            :items="tickets"
                            :label="langMap.ticket.subject"
                            dense
                            item-text="name"
                            item-value="id"
                            multiple
                            v-model="linkTicketForm.child_ticket_id"
                        />
                        <v-textarea
                            :color="themeColor"
                            :label="langMap.main.description"
                            auto-grow
                            dense
                            row-height="25"
                            rows="2"
                            v-model="linkTicketForm.merge_comment"
                        />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn :color="themeColor" @click="ticketLinkDialog = false" darken-1 text>
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn :color="themeColor" @click="linkTicket()" darken-1 text>
                            {{langMap.main.link}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog max-width="480" persistent v-model="removeTicketDialog">
                <v-card>
                    <v-card-title class="headline">{{langMap.main.delete_selected}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn @click="removeTicketDialog = false" color="grey darken-1" text>{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn @click="deleteTicket()" color="red darken-1" text>
                            {{langMap.main.delete}}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <v-row v-if="isLoaded">
            <v-col
                :md="firstColumnSize + secondColumnSize + thirdColumnSize"
                cols="12"
                sm="12"
            >
                <v-toolbar class="rounded-lg elevation-2" color="#F0F0F0" dense>
                    <v-btn :color="themeColor" @click="answerDialog = true" class="ma-2" small
                           style="color: white;"
                    >{{langMap.ticket.create_answer}}
                    </v-btn>
                    <v-btn :color="!linkBlock ? '#f2f2f2' : themeColor"
                           :outlined="linkBlock"
                           @click="showLinkBlock"
                           class="ma-2 d-sm-none d-md-flex"
                           small
                    >
                        {{langMap.main.link}}
                    </v-btn>
                    <v-btn :color="!mergeBlock ? '#f2f2f2' : themeColor" :outlined="mergeBlock"
                           @click="showMergeBlock"
                           class="ma-2 d-sm-none d-md-flex"
                           small
                           v-if="ticket.parent_id !== null || ticket.child_tickets !== null"
                    >
                        Merge
                    </v-btn>
                    <v-btn
                        @click="manageThirdColumn"
                        class="ma-2" color="#f2f2f2" small
                        v-show="!thirdColumn"
                    >
                        {{langMap.ticket.ticket_history}}
                    </v-btn>
                    <v-btn :color="this.ticket.is_spam ? 'red' : '#f2f2f2'" @click="markAsSpam"
                           class="ma-2 d-sm-none d-md-flex"
                           small
                    >
                        Spam
                    </v-btn>
                    <v-btn @click="ticketDeleteProcess" class="ma-2 d-sm-none d-md-flex" color="#f2f2f2"
                           small
                    >
                        Delete
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-menu
                        offset-y
                        rounded
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                class="ma-2 float-md-right"
                                small
                                v-bind="attrs"
                                v-on="{...menu}"
                            >
                                <v-badge
                                    :color="ticket.status.color"
                                    dot
                                    inline
                                >
                                    <strong>{{ langMap.ticket_statuses[ticket.status.name] }}</strong>
                                </v-badge>
                            </v-btn>
                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                @click="closeTicket"
                                link
                            >
                                <v-list-item-title>
                                    <!--                                    {{langMap.ticket_statuses['closed']}}-->
                                    Close ticket
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-menu
                        offset-y
                        rounded
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                class="ma-2 float-md-right"
                                small
                                v-bind="attrs"
                                v-on="{...menu}"
                            >
                                <v-badge
                                    :color="ticket.priority.color"
                                    dot
                                    inline
                                >
                                    <strong>{{ langMap.ticket_priorities[ticket.priority.name] }}</strong>
                                </v-badge>
                            </v-btn>
                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                :key="priority.id"
                                @click="changePriority(priority.id)"
                                link
                                v-for="priority in priorities"
                            >
                                <v-list-item-title>
                                    <v-badge
                                        :color="priority.color"
                                        dot
                                        inline
                                    >
                                        <strong>{{ langMap.ticket_priorities[priority.name] }}</strong>
                                    </v-badge>
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-menu
                        offset-y
                        rounded
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                class="ma-2 float-md-right"
                                small
                                v-bind="attrs"
                                v-on="{...menu}"
                            >
                                <strong>{{ langMap.ticket_types[ticket.ticket_type.name] }}</strong>
                            </v-btn>
                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                :key="type.id"
                                @click="changeType(type.id)"
                                link
                                v-for="type in types"
                            >
                                <v-list-item-title>
                                        <strong>{{ langMap.ticket_types[type.name] }}</strong>
                                </v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                    <v-menu
                        offset-y
                        rounded
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                class="ma-2 d-sm-flex d-md-none"
                                color="#f2f2f2"
                                small
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
                                @click="showLinkBlock"
                            >
                                <v-list-item-title>{{langMap.main.link}}</v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                @click="showMergeBlock"
                                v-if="ticket.parent_id !== null || ticket.child_tickets !== null"
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
                                @click="ticketDeleteProcess"
                                active-class="red--text"
                                color="red"
                                link
                            >
                                <v-list-item-title>Delete</v-list-item-title>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </v-toolbar>
            </v-col>
            <v-col
                :md="firstColumnSize"
                cols="12"
                sm="12"
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
                            <v-btn @click="updateDialog = true"
                                   class="float-md-right" color="white"
                                   small
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
                                </span>
                            <br>
                            <span v-if="ticket.product !== null">
                                <v-label>
                                    {{langMap.ticket.product_name}}:
                                </v-label>
                                {{ ticket.product.name }}
                            </span>
                            <br>
                            <v-label v-if="ticket.availability">
                                {{langMap.ticket.availability}}:
                            </v-label>
                            <span v-html="ticket.availability" v-if="ticket.availability">
                            </span>
                            <br>
                            <v-btn @click="serverAccessDialog = true"
                                   class="float-md-right" color="#F0"
                                   small
                                   style="color: black;"
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
                        <div :key="answer.id"
                             v-for="answer in ticket.answers"
                        >
                            <v-card
                                class="mx-auto"
                                dense
                                outlined
                            >
                                <v-list-item>
                                    <v-list-item-content>
                                        <span class="text-left" style="font-weight: bold;">
                                            {{answer.employee.user_data.full_name}}
                                            {{answer.created_at_time !== '' ? answer.created_at_time :
                                            answer.created_at}}:
                                        </span>
                                        <div v-html="answer.answer"></div>
                                        <v-col cols="12" v-if="answer.attachments.length > 0 ">
                                            <h4>{{langMap.main.attachments}}</h4>
                                            <div
                                                v-for="attachment in answer.attachments"
                                            >
                                                <v-chip
                                                    :color="themeColor"
                                                    :href="attachment.link"
                                                    class="ma-2"
                                                    text-color="white"
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
                            color="#f2f2f2"
                            dense
                            outlined
                            v-if="ticket.description"

                        >
                            <v-list-item>
                                <v-list-item-content>
                                    <span v-text="ticket.merge_comment"></span>
                                    <span style="font-weight: bold;">{{ ticket.name }}</span>
                                    <div v-html="ticket.description"></div>
                                </v-list-item-content>
                            </v-list-item>
                        </v-card>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <div :key="child_ticket.id"
                             v-for="child_ticket in ticket.child_tickets"
                             v-if="ticket.child_tickets"
                        >
                            <div :key="answer.id"
                                 v-for="answer in child_ticket.answers"
                                 v-if="child_ticket.answers.length > 0"
                            >
                                <v-card
                                    class="mx-auto"
                                    dense
                                    outlined
                                >
                                    <v-list-item>
                                        <v-list-item-content>
                                            <span class="text-left" style="font-weight: bold;">
                                                {{answer.employee.user_data.full_name}}
                                                {{answer.created_at_time !== '' ? answer.created_at_time :
                                                answer.created_at}} - {{ticket.name}}:
                                            </span>
                                            <div v-html="answer.answer"></div>
                                            <v-col cols="12" v-if="answer.attachments.length > 0 ">
                                                <h4>{{langMap.main.attachments}}</h4>
                                                <div
                                                    v-for="attachment in answer.attachments"
                                                    v-if="answer.attachments.length > 0"
                                                >
                                                    <v-chip
                                                        :color="themeColor"
                                                        :href="attachment.link"
                                                        class="ma-2"
                                                        text-color="white"
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
                                color="#f2f2f2"
                                dense
                                outlined
                            >
                                <v-list-item>
                                    <v-list-item-content>
                                        <span style="font-weight: bold;">{{ child_ticket.name }}</span>
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
                :md="secondColumnSize"
                cols="12"
                sm="12"
            >
                <v-expansion-panels
                    class="d-sm-none d-md-flex"
                    v-model="assignPanel"
                >
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                 <strong>Reported by: </strong>
                                 <span class="float-md-right" v-if="ticket.contact !== null">
                                     {{ ticket.contact.user_data.full_name}}
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
                            <v-btn @click="updateDialog = true"
                                   class="float-md-right" color="white"
                                   small
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
                                </span>
                            <br>
                            <span v-if="ticket.product !== null">
                                <v-label >
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
                            <span v-html="ticket.availability" v-if="ticket.availability">
                            </span>
                            <br>
                            <v-btn @click="serverAccessDialog = true"
                                   class="float-md-right" color="#F0"
                                   small
                                   style="color: black;"
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
                    <v-expansion-panel v-if="ticket.product !== null">
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                 <strong>Product: </strong>
                                 <span class="float-md-right">
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
                                :items="products"
                                @input="getProducts"
                                color="green"
                                dense
                                item-color="green"
                                item-text="name"
                                item-value="id"
                                label="Product"
                                v-model="ticket.to_product_id"
                            />
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br v-if="ticket.product !== null">
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
                                        {{ ticket.assigned_person.user_data.full_name}}
                                    </span>
                                </span>
                            </span>
                            <v-spacer></v-spacer>
                            <template v-slot:actions>
                                <v-btn @click="true"
                                       class="float-md-right" color="white"
                                       small
                                       style="color: black;"
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
                                    :color="themeColor"
                                    :disabled="selectionDisabled"
                                    :item-color="themeColor"
                                    :items="teams"
                                    :label="langMap.sidebar.team"
                                    @change="selectTeam"
                                    dense
                                    item-text="name"
                                    item-value="id"
                                    v-model="ticket.to_team_id"
                                ></v-autocomplete>
                                <v-autocomplete
                                    :color="themeColor"
                                    :disabled="selectionDisabled"
                                    :item-color="themeColor"
                                    :items="employees"
                                    :label="langMap.team.members"
                                    dense
                                    item-value="employee.id"
                                    v-model="ticket.to_company_user_id"
                                >
                                    <template v-slot:selection="data">
                                        {{data.item.employee.user_data.full_name}} ({{data.item.employee.user_data.email}})
                                    </template>
                                    <template v-slot:item="data">
                                        {{data.item.employee.user_data.full_name}} ({{data.item.employee.user_data.email}})
                                    </template>
                                </v-autocomplete>
                                <v-btn :color="themeColor"
                                       @click.native.stop="updateTicket"
                                       class="ma-2"
                                       small
                                       style="color: white;"
                                >
                                    {{langMap.ticket.assign_to}}
                                </v-btn>
                                <v-btn class="ma-2"
                                       color="white" small
                                       style="color: black;"
                                >
                                    Cancel
                                </v-btn>
                            </v-form>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br>
                <v-expansion-panels multiple v-model="notesPanel">
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>{{langMap.ticket.internal_notes}}</strong>
                            </span>
                            <template v-slot:actions>
                                <v-btn
                                    @click.native.stop="noteDialog = true" color="white"
                                    small
                                    style="color: black;"
                                >
                                    {{langMap.ticket.add_internal_note}}
                                </v-btn>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br>
                            <div :key="noticeItem.id"
                                 v-for="noticeItem in ticket.notices"
                            >
                                <v-card
                                    class="mx-auto"
                                    dense
                                    outlined
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
                :md="thirdColumnSize"
                cols="12"
                sm="12"
                v-show="thirdColumn"
            >
                <v-expansion-panels
                    multiple
                    v-model="thirdColumnPanels"
                >
                    <v-expansion-panel
                        v-if="linkBlock">
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>Linked tickets</strong>
                                <v-badge
                                    v-if="ticket.merged_parent.length > 0 || ticket.merged_child.length > 0"
                                    color="orange"
                                    :content="ticket.merged_parent.length+ticket.merged_child.length"
                                    inline
                                >
                                </v-badge>
                            </span>
                            <template v-slot:actions>
                                <v-icon @click.native.stop="hideLinkBlock">mdi-close</v-icon>
                                <!--                                <v-icon>$expand</v-icon>-->
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-text-field :color="themeColor" :label="langMap.main.search" @input="getTickets"
                                          v-model="ticketsSearch">
                                <template slot="append">
                                    <v-menu
                                        bottom
                                        rounded
                                        transition="slide-y-transition"
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
                                                :key="item.id"
                                                @click="selectSearchCategory(item)"
                                                link
                                                v-for="item in searchCategories"
                                            >
                                                <v-list-item-title>
                                                    {{ item.name }}
                                                </v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </template>
                            </v-text-field>
                            <v-list
                                dense
                            >
                                <v-list-item-group :color="themeColor">
                                    <!--                                <v-subheader>Parent</v-subheader>-->
                                    <v-list-item
                                        :color="themeColor"
                                        :key="item.id"
                                        @click="showTicket(item.parent_ticket_data.id)"
                                        link
                                        v-for="(item, i) in ticket.merged_parent"
                                    >
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-list-item-title v-on="on">
                                            <span>
                                                {{item.parent_ticket_data.number}}|{{item.parent_ticket_data.name}}
                                            </span>
                                                    <br>
                                                    <span style="font-weight: lighter;">
                                                    {{item.parent_ticket_data.creator !== null && item.parent_ticket_data.creator.user_data !== null ?
                                                    item.parent_ticket_data.creator.user_data.name + " " +
                                                    item.parent_ticket_data.creator.user_data.surname : ''}},
                                                    {{item.parent_ticket_data.from !== null ? item.parent_ticket_data.from.name : ''}}
                                            </span>
                                                    <br>
                                                    <span style="font-weight: lighter;">
                                                    {{item.parent_ticket_data.last_update}}
                                            </span>
                                                </v-list-item-title>
                                            </template>
                                            <span>{{item.parent_ticket_data.number}}|{{item.parent_ticket_data.name}}</span>
                                        </v-tooltip>
                                    </v-list-item>
                                    <!--                                <v-subheader>Child</v-subheader>-->
                                    <v-list-item
                                        :key="item.id"
                                        @click="showTicket(item.child_ticket_data.id)"
                                        link
                                        v-for="(item, i) in ticket.merged_child"
                                    >
                                        <v-tooltip bottom>
                                            <template v-slot:activator="{ on, attrs }">
                                                <v-list-item-title v-on="on">
                                            <span>
                                                    {{item.child_ticket_data.number}}|{{item.child_ticket_data.name}}
                                            </span>
                                                    <br>
                                                    <span style="font-weight: lighter;">
                                                    {{item.child_ticket_data.creator !== null && item.child_ticket_data.creator.user_data !== null ?
                                                    item.child_ticket_data.creator.user_data.name + " " +
                                                    item.child_ticket_data.creator.user_data.surname : ''}},
                                                    {{item.child_ticket_data.from !== null ? item.child_ticket_data.from.name : ''}}
                                            </span>
                                                    <br>
                                                    <span style="font-weight: lighter;">
                                                    {{item.child_ticket_data.last_update}}
                                            </span>
                                                </v-list-item-title>
                                            </template>
                                            <span>{{item.child_ticket_data.number}}|{{item.child_ticket_data.name}}</span>
                                        </v-tooltip>

                                    </v-list-item>
                                </v-list-item-group>
                            </v-list>
                            <v-btn
                                @click="linkTicketProcess()"
                                color="#f2f2f2"
                                small
                            >
                                New {{langMap.main.link}}
                            </v-btn>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br v-if="linkBlock">
                <v-expansion-panels
                    multiple
                >
                    <v-expansion-panel v-if="mergeBlock">
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>Merged tickets</strong>
                            </span>

                            <template v-slot:actions>
                                <v-icon @click.native.stop="hideMergeBlock">mdi-close</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-text-field :color="themeColor" :label="langMap.main.search" @input="getTickets"
                                          v-model="ticketsSearch">
                                <template slot="append">
                                    <v-menu
                                        bottom
                                        rounded
                                        transition="slide-y-transition"
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
                                                :key="item.id"
                                                @click="selectSearchCategory(item)"
                                                link
                                                v-for="item in searchCategories"
                                            >
                                                <v-list-item-title>
                                                    {{ item.name }}
                                                </v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </template>
                            </v-text-field>
                            <div style="max-height: 390px; overflow-y: auto;">
                                <v-list
                                    dense
                                >
                                    <v-list-item-group :color="themeColor">
                                        <v-list-item
                                            :color="themeColor"
                                            :key="item.id"
                                            @click="showTicket(item.parent_ticket_data.id)"
                                            v-for="(item, i) in tickets"
                                        >
                                            <v-tooltip bottom>
                                                <template v-slot:activator="{ on, attrs }">
                                                    <v-list-item-title v-on="on">
                                                <span>
                                                    <v-checkbox
                                                        :color="themeColor"
                                                        :disabled="mergeTicketForm.parent_ticket_id === item.id"
                                                        :item-color="themeColor"
                                                        :key="item.id"
                                                        :value="item.id"
                                                        dense
                                                        hide-details
                                                        style="display: inline-block;"
                                                        v-model="mergeTicketForm.child_ticket_id"
                                                    />
                                                     <v-btn
                                                         :color="mergeTicketForm.parent_ticket_id === item.id ? 'red' : themeColor"
                                                         @click="mergeTicketForm.parent_ticket_id = item.id"
                                                         fab
                                                         outlined
                                                         x-small
                                                     >
                                                    <v-icon dark>
                                                        mdi-medal-outline
                                                    </v-icon>
                                                </v-btn>
                                                </span>
                                                        <span>
                                                    {{item.number}}|{{item.name}}
                                                </span>
                                                        <br>
                                                        <span style="font-weight: lighter;">
                                                    {{item.creator !== null && item.creator.user_data !== null ?
                                                    item.creator.user_data.name + " " +
                                                    item.creator.user_data.surname : ''}},
                                                    {{item.from !== null ? item.from.name : ''}}
                                                </span>
                                                        <br>
                                                        <span style="font-weight: lighter;">
                                                    {{item.last_update}}
                                                </span>
                                                    </v-list-item-title>
                                                </template>
                                                <span>{{item.number}}|{{item.name}}</span>
                                            </v-tooltip>
                                        </v-list-item>
                                    </v-list-item-group>
                                </v-list>
                            </div>
                            <v-textarea
                                :color="themeColor"
                                auto-grow
                                dense
                                label="Add a note..."
                                row-height="25"
                                rows="2"
                                v-model="mergeTicketForm.merge_comment"
                            />
                            <v-spacer></v-spacer>
                            <v-btn color="red" text>{{langMap.main.cancel}}
                            </v-btn>
                            <v-btn @click="mergeTicket()" color="green" text>
                                {{langMap.ticket.merge}}
                            </v-btn>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br v-if="mergeBlock">
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
                                        :key="history.id"
                                        v-for="(history, i) in ticket.histories"
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

    import EventBus from "../../components/EventBus";

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
                mergeBlock: false,
                linkBlock: false,
                thirdColumnPanels: [0],
                firstColumnSize: 8,
                secondColumnSize: 4,
                thirdColumnSize: 0,
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
                types: [],
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
                    merge_comment: '',
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
            this.getTypes()
            this.getTeams()
            this.getTickets()
            // if (localStorage.getticket('auth_token')) {
            //     this.$router.push('tickets')
            // }
            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeColor = color;
            })
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
                        this.$store.state.pageName = '#' + this.ticket.id + ' | ' + this.ticket.number + ' | ' + this.ticket.name
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
                        this.progressBuffer = this.progressBuffer + 10;
                    } else {
                        console.log('error')
                    }

                });
            },
            getTypes() {
                axios.get('/api/ticket_types').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.types = response.data
                        this.progressBuffer = this.progressBuffer + 10;
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
            showMergeBlock() {
                this.thirdColumn = false
                this.mergeBlock = !this.mergeBlock
                this.manageThirdColumn()
            },
            showLinkBlock() {
                this.thirdColumn = false
                this.linkBlock = !this.linkBlock
                this.manageThirdColumn()
            },
            hideMergeBlock() {
                // if (this.thirdColumn === true) {
                //     this.thirdColumn = false
                // }
                // this.manageThirdColumn()
                this.mergeBlock = false
            },
            hideLinkBlock() {
                // if (this.thirdColumn === true) {
                //     this.thirdColumn = false
                // }
                // this.manageThirdColumn()
                this.linkBlock = false
            },
            manageThirdColumn() {
                if (this.thirdColumn === false) {
                    this.firstColumnSize = 6
                    this.secondColumnSize = 3
                    this.thirdColumnSize = 3
                    this.thirdColumn = true
                } else {
                    this.firstColumnSize = 8
                    this.secondColumnSize = 4
                    this.thirdColumnSize = 0
                    this.thirdColumn = false
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
                if (this.ticket.is_spam === 0) {
                    this.snackbarMessage = 'Ticket marked as spam'
                } else {
                    this.snackbarMessage = 'Ticket marked as NOT spam'
                }
                axios.post('/api/spam/ticket', {'id': this.ticket.id}).then(response => {
                    response = response.data
                    if (response.success === true) {
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
            changeType(id) {
                this.ticket.ticket_type_id = id
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
