<template>
    <v-container fluid>
        <v-overlay
            :value="!isLoaded"
            light
            opacity="0.45"
        >
            <v-progress-circular
                :color="themeBgColor"
                :rotate="360"
                :size="150"
                :value="progressBuffer"
                :width="15"
            >
                <span class="white--text">{{ progressBuffer }} %</span>
            </v-progress-circular>

        </v-overlay>
        <v-snackbar
            v-model="snackbar"
            :bottom="true"
            :color="actionColor"
            :right="true"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <template>
            <v-dialog v-model="serverAccessDialog" max-width="480">
                <v-card dense outlined>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        Server access
                    </v-card-title>
                    <v-card-text>
                        <v-label v-if="ticket.connection_details">
                            {{ langMap.ticket.ip_address }}:
                        </v-label>
                        <span v-if="ticket.connection_details" v-text="ticket.connection_details">
                            </span>
                        <br/>
                        <v-label v-if="ticket.access_details">
                            {{ langMap.ticket.access_details }}:
                        </v-label>
                        <span v-if="ticket.access_details" v-text="ticket.access_details">
                            </span>
                        <br/>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="serverAccessDialog = false">{{ langMap.main.cancel }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="updateDialog" max-width="60%">
                <v-card dense outlined>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        <span class="text-capitalize">{{ langMap.main.edit }}</span>
                        <v-spacer></v-spacer>
                        <v-btn :color="themeBgColor" darken-1 text @click="updateDialog = false">{{
                                langMap.main.cancel
                            }}
                        </v-btn>
                        <v-btn :color="themeBgColor" dark @click="updateTicket">{{ langMap.main.update }}
                        </v-btn>
                    </v-card-title>
                    <v-card-text>

                        <v-row>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="ticket.original_name"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :label="langMap.ticket.subject"
                                    dense
                                />
                                <v-autocomplete
                                    v-model="from"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :items="suppliers"
                                    :label="langMap.ticket.company_from"
                                    dense
                                    item-text="name"
                                    item-value="item"
                                    @input="getContacts"
                                />
                                <v-autocomplete
                                    v-model="ticket.contact_company_user_id"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :items="contacts"
                                    :label="langMap.ticket.company_contact"
                                    dense
                                    item-text="user_data.full_name"
                                    item-value="id"
                                />
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    v-model="ticket.availability"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :label="langMap.ticket.availability_description"
                                    auto-grow
                                    dense
                                    prepend-icon="mdi-text"
                                    row-height="25"
                                    rows="1"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select
                                    v-model="ticket.internal_billing_id"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :items="internalBillings"
                                    :label="langMap.profile.internal_billing"
                                    item-text="name"
                                    item-value="id"
                                    prepend-icon="mdi-cash"
                                ></v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    v-model="ticket.connection_details"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :label="langMap.ticket.ip_address"
                                    auto-grow
                                    dense
                                    prepend-icon="mdi-text"
                                    row-height="25"
                                    rows="1"
                                ></v-textarea>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-textarea
                                    v-model="ticket.access_details"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :label="langMap.ticket.access_details"
                                    auto-grow
                                    dense
                                    prepend-icon="mdi-text"
                                    row-height="25"
                                    rows="1"
                                ></v-textarea>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="answerDialog" max-width="50%" :retain-focus="false" :eager="true">
                <v-card dense outlined>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ ticketAnswer.id ? langMap.ticket.edit_answer : langMap.ticket.create_answer }}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <Tinymce
                                        v-model="ticketAnswer.answer"
                                        :placeholder="langMap.ticket.answer_description"
                                    />
                                </div>
                                <div class="col-md-12">
                                    <v-select
                                        v-model="selectedSignature"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :items="signatures"
                                        :label="langMap.notification.signature"
                                        dense
                                        item-text="name"
                                        item-value="signature"
                                    >
                                        <template slot="selection" slot-scope="data">
                                            <div class="text--black mt-3" v-html="data.item.signature"></div>
                                        </template>
                                    </v-select>
                                </div>
                                <div class="col-md-12">
                                    <v-file-input
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
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
                                                :color="themeBgColor"
                                                :text-color="themeFgColor"
                                                class="ma-2">
                                                {{ text }}
                                            </v-chip>
                                        </template>
                                    </v-file-input>
                                </div>
                            </div>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="grey darken-1" text @click="answerDialog = false">{{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" dark @click="addTicketAnswer">{{ ticketAnswer.id ? langMap.main.update : langMap.main.create }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="noteDialog" max-width="50%" :retain-focus="false" :eager="true">
                <v-card dense outlined>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ ticketNotice.id ? langMap.ticket.edit_internal_note : langMap.ticket.add_internal_note }}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <div class="row">
                                <div class="col-md-12 mt-2">
                                    <Tinymce
                                        v-model="ticketNotice.notice"
                                        :placeholder="ticketNotice.id ? langMap.ticket.edit_internal_note : langMap.ticket.add_internal_note"
                                    />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <v-select
                                    v-model="selectedSignature"
                                    :color="themeBgColor"
                                    :item-color="themeBgColor"
                                    :items="signatures"
                                    :label="langMap.notification.signature"
                                    dense
                                    item-text="name"
                                    item-value="signature"
                                >
                                    <template slot="selection" slot-scope="data">
                                        <div class="text--black mt-3" v-html="data.item.signature"></div>
                                    </template>
                                </v-select>
                            </div>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="grey darken-1" text @click="noteDialog = false">{{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" dark @click="addTicketNotice">{{ ticketNotice.id ? langMap.main.update : langMap.main.create }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="ticketLinkDialog" max-width="480" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.main.link }}
                    </v-card-title>
                    <v-card-text>
                        <v-autocomplete
                            v-model="linkTicketForm.parent_ticket_id"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            :items="tickets"
                            :label="langMap.ticket.subject"
                            dense
                            item-text="name"
                            item-value="id"
                        />
                        <v-autocomplete
                            v-model="linkTicketForm.child_ticket_id"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            :items="tickets"
                            :label="langMap.ticket.subject"
                            dense
                            item-text="name"
                            item-value="id"
                            multiple
                        />
                        <br/>
                        <v-textarea
                            v-model="linkTicketForm.merge_comment"
                            :color="themeBgColor"
                            :label="langMap.main.description"
                            auto-grow
                            dense
                            row-height="25"
                            rows="2"
                        />
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn :color="themeBgColor" darken-1 text @click="ticketLinkDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" darken-1 text @click="linkTicket()">
                            {{ langMap.main.link }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="removeTicketDialog" max-width="480" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.main.delete_selected }}?
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeTicketDialog = false">{{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteTicket()">
                            {{ langMap.main.delete }}
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
                    <v-btn :color="themeBgColor" class="ma-2" small style="color: white;"
                           @click="answerDialog = true"
                    >{{ langMap.ticket.create_answer }}
                    </v-btn>
                    <v-btn v-if="!$helpers.auth.checkPermissionByIds([36])"
                           :color="!linkBlock ? '#f2f2f2' : themeBgColor"
                           :outlined="linkBlock"
                           class="ma-2 d-sm-none d-md-flex"
                           small
                           @click="showLinkBlock"
                    >
                        {{ langMap.main.link }}
                    </v-btn>
                    <v-btn
                        v-if="!$helpers.auth.checkPermissionByIds([36]) && (ticket.parent_id !== null || ticket.child_tickets !== null)"
                        :color="!mergeBlock ? '#f2f2f2' : themeBgColor"
                        :outlined="mergeBlock"
                        class="ma-2 d-sm-none d-md-flex"
                        small
                        @click="showMergeBlock"
                    >
                        {{ langMap.ticket.merge }}
                    </v-btn>
                    <v-btn
                        v-show="!thirdColumn"
                        class="ma-2" color="#f2f2f2" small
                        @click="manageThirdColumn"
                    >
                        {{ langMap.ticket.ticket_history }}
                    </v-btn>
                    <v-btn v-if="!$helpers.auth.checkPermissionByIds([36])"
                           :color="this.ticket.is_spam ? 'red' : '#f2f2f2'" class="ma-2 d-sm-none d-md-flex"
                           small
                           @click="markAsSpam"
                    >
                        Spam
                    </v-btn>
                    <v-btn v-if="!$helpers.auth.checkPermissionByIds([36])"
                           class="ma-2 d-sm-none d-md-flex" color="#f2f2f2" small
                           @click="ticketDeleteProcess"
                    >
                        {{ langMap.main.delete }}
                    </v-btn>
                    <v-spacer></v-spacer>
                    <v-menu
                        offset-y
                        rounded
                    >
                        <template v-slot:activator="{ on: menu, attrs }">
                            <v-btn
                                v-bind="attrs"
                                v-on="{...menu}"
                                class="ma-2 float-md-right"
                                small
                            >
                                <v-badge
                                    :color="ticket.status.color"
                                    dot
                                    inline
                                >
                                    <!--                                    <strong>{{ langMap.ticket_statuses[ticket.status.name] }}</strong>-->
                                    <strong>{{ ticket.status.name }}</strong>
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
                                    {{ langMap.ticket.close_ticket }}
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
                                v-bind="attrs"
                                v-on="{...menu}"
                                class="ma-2 float-md-right"
                                small
                            >
                                <v-badge
                                    :color="ticket.priority.color"
                                    dot
                                    inline
                                >
                                    <!--                                    <strong>{{ langMap.ticket_priorities[ticket.priority.name] }}</strong>-->
                                    <strong>{{ ticket.priority.name }}</strong>
                                </v-badge>
                            </v-btn>
                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                v-for="priority in priorities"
                                :key="priority.id"
                                link
                                @click="changePriority(priority.id)"
                            >
                                <v-list-item-title>
                                    <v-badge
                                        :color="priority.color"
                                        dot
                                        inline
                                    >
                                        <!--                                        <strong>{{ langMap.ticket_priorities[priority.name] }}</strong>-->
                                        <strong>{{ priority.name }}</strong>
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
                                v-bind="attrs"
                                v-on="{...menu}"
                                class="ma-2 float-md-right"
                                small
                            >
                                <!--                                <strong>{{ langMap.ticket_types[ticket.ticket_type.name] }}</strong>-->
                                <strong>{{ ticket.ticket_type.name }}</strong>

                            </v-btn>
                        </template>
                        <v-list
                            dense
                        >
                            <v-list-item
                                v-for="type in types"
                                :key="type.id"
                                link
                                @click="changeType(type.id)"
                            >
                                <v-list-item-title>
                                    <!--                                    <strong>{{ langMap.ticket_types[type.name] }}</strong>-->
                                    <strong>{{ type.name }}</strong>
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
                                v-bind="attrs"
                                v-on="{...menu }"
                                class="ma-2 d-sm-flex d-md-none"
                                color="#f2f2f2"
                                small
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
                                <v-list-item-title>{{ langMap.main.link }}</v-list-item-title>
                            </v-list-item>
                            <v-list-item
                                v-if="ticket.parent_id !== null || ticket.child_tickets !== null"
                                @click="showMergeBlock"
                            >
                                <v-list-item-title>{{ langMap.ticket.megre }}</v-list-item-title>
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
                                active-class="red--text"
                                color="red"
                                link
                                @click="ticketDeleteProcess"
                            >
                                <v-list-item-title>{{ langMap.main.delete }}</v-list-item-title>
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
                    v-model="assignPanel"
                    class="d-sm-flex d-md-none"
                >
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                                        <span>
                                            <strong>{{ langMap.ticket.reported_by }}: </strong>
                                            <span v-if="ticket.contact !== null" class="float-md-right text-md-right">
                                                <v-avatar
                                                    v-if="ticket.contact.user_data.avatar_url || ticket.contact.user_data.full_name"
                                                    class="mr-2 mb-2"
                                                    color="grey darken-1"
                                                    size="2em"
                                                >
                                                    <v-img v-if="ticket.contact.user_data.avatar_url"
                                                           :src="ticket.contact.user_data.avatar_url"/>
                                                    <span v-else-if="ticket.contact.user_data.full_name"
                                                          class="white--text">
                                                    {{
                                                            ticket.contact.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                                        }}
                                                    </span>
                                                   </v-avatar>
                                                <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>
                                                {{ ticket.contact.user_data.full_name }}
                                                <br/>
                                                {{ ticket.from.name }}
                                            </span>
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
                            <br/>
                            <v-btn class="float-md-right"
                                   color="white" small
                                   style="color: black;"
                                   @click="updateDialog = true"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                                {{ langMap.main.edit }}
                            </v-btn>
                            <span>
                                    <v-label>
                                        {{ langMap.ticket.contact_email }}:
                                    </v-label>
                                <span v-if="ticket.contact !== null">
                                    {{ ticket.contact.user_data.email }}
                                </span>
                                </span>
                            <br/>
                            <span v-if="ticket.product !== null">
                                <v-label>
                                    {{ langMap.ticket.product_name }}:
                                </v-label>
                                {{ ticket.product.name }}
                            </span>
                            <br/>
                            <v-label v-if="ticket.availability">
                                {{ langMap.ticket.availability }}:
                            </v-label>
                            <span v-if="ticket.availability" v-html="ticket.availability">
                            </span>
                            <br/>
                            <v-btn class="float-md-right"
                                   color="#F0" small
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
                        <div v-for="child_ticket in ticket.child_tickets"
                             v-if="ticket.child_tickets"
                             :key="child_ticket.id"
                        >
                            <div v-for="answer in child_ticket.answers"
                                 v-if="child_ticket.answers.length > 0"
                                 :key="answer.id"
                                 class="ticket--answer"
                            >
                                <v-card
                                    class="mx-auto"
                                    dense
                                    outlined
                                >
                                    <v-list-item>
                                        <v-list-item-content>
                                            <span
                                                class="text-left"
                                                style="font-weight: bold;"
                                            >
                                                <v-avatar
                                                    v-if="answer.employee.user_data.avatar_url || answer.employee.user_data.full_name"
                                                    class="mr-2 mb-2"
                                                    color="grey darken-1"
                                                    size="2em"
                                                >
                                                    <v-img v-if="answer.employee.user_data.avatar_url"
                                                           :src="answer.employee.user_data.avatar_url"/>
                                                    <span v-else-if="answer.employee.user_data.full_name"
                                                          class="white--text">
                                                        {{
                                                            answer.employee.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                                        }}
                                                    </span>
                                                </v-avatar>
                                                <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>

                                                {{ answer.employee.user_data.full_name }}

                                                {{
                                                    answer.created_at_time !== '' ? answer.created_at_time : answer.created_at
                                                }} - {{ ticket.name }}:
                                            </span>
                                            <div v-html="answer.answer"></div>
                                            <v-col v-if="answer.attachments.length > 0 " cols="12">
                                                <h4>{{ langMap.main.attachments }}</h4>
                                                <div
                                                    v-for="attachment in answer.attachments"
                                                    v-if="answer.attachments.length > 0"
                                                >
                                                    <v-chip
                                                        :color="themeBgColor"
                                                        :href="attachment.link"
                                                        :text-color="themeFgColor"
                                                        class="ma-2"
                                                    >
                                                        {{ attachment.name }}
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
                                        <span
                                            v-if="child_ticket.merged_parent_info.length > 0"
                                            class="text-left"
                                            style="font-weight: bold;"
                                        >
                                             {{ child_ticket.merged_parent_info }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-left"
                                            style="font-weight: bold;">
                                            <v-avatar
                                                v-if="ticket.creator.user_data.avatar_url || ticket.creator.user_data.full_name"
                                                class="mr-2 mb-2"
                                                color="grey darken-1"
                                                size="2em"
                                            >
                                                    <v-img v-if="ticket.creator.user_data.avatar_url"
                                                           :src="ticket.creator.user_data.avatar_url"/>
                                                    <span v-else-if="ticket.creator.user_data.full_name"
                                                          class="white--text">
                                                        {{
                                                            ticket.creator.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                                        }}
                                                    </span>
                                                </v-avatar>
                                                <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>

                                                {{ ticket.creator.user_data.full_name }}
                                                {{
                                                ticket.created_at_time !== '' ? ticket.created_at_time : ticket.created_at
                                            }} - {{ ticket.name }}:
                                        </span>
                                        <div v-html="child_ticket.description"></div>
                                        <v-col v-if="child_ticket.attachments && child_ticket.attachments.length > 0 "
                                               cols="12">
                                            <h4>{{ langMap.main.attachments }}</h4>
                                            <div
                                                v-for="attachment in child_ticket.attachments"
                                                v-if="child_ticket.attachments.length > 0"
                                            >
                                                <v-chip
                                                    :color="themeBgColor"
                                                    :href="attachment.link"
                                                    :text-color="themeFgColor"
                                                    class="ma-2"
                                                >
                                                    {{ attachment.name }}
                                                </v-chip>
                                            </div>
                                        </v-col>
                                        <span v-if="ticket.merge_comment"
                                              class="caption text-center"
                                        >
                                                ({{ ticket.merge_comment }})
                                        </span>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-card>
                            <v-spacer>
                                &nbsp;
                            </v-spacer>
                        </div>
                        <div
                            v-if="ticket.merged_child_info.length > 0"
                        >
                            <v-card

                                class="mx-auto"
                                color="#f2f2f2"
                                dense
                                outlined
                            >
                                <v-list-item>
                                    <v-list-item-content>
                                        <span
                                            class="text-left"
                                            style="font-weight: bold;"
                                        >
                                             {{ ticket.merged_child_info }}
                                        </span>
                                        <span v-if="ticket.merge_comment"
                                              class="caption text-center"
                                        >
                                                ({{ ticket.merge_comment }})
                                    </span>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-card>

                            <v-spacer>
                                &nbsp;
                            </v-spacer>
                        </div>
                        <div v-for="(answer, index) in ticket.answers"
                             :key="answer.id"
                             class="ticket--answer"
                        >
                            <v-card
                                class="mx-auto"
                                dense
                                outlined
                            >
                                <v-list-item>
                                    <v-list-item-content>
                                        <v-col :cols="index == 0 && currentUser.id == answer.employee.user_data.id ? 11 : 12">
                                        <span class="text-left" style="font-weight: bold;">
                                            <v-avatar
                                                v-if="answer.employee.user_data.avatar_url || answer.employee.user_data.full_name"
                                                class="mr-2 mb-2"
                                                color="grey darken-1"
                                                size="2em"
                                            >
                                                    <v-img v-if="answer.employee.user_data.avatar_url" :src="answer.employee.user_data.avatar_url"/>
                                                    <span v-else-if="answer.employee.user_data.full_name" class="white--text">
                                                        {{answer.employee.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase() }}
                                                    </span>
                                            </v-avatar>
                                            <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>

                                            {{ answer.employee.user_data.full_name }}
                                            {{ answer.created_at_time !== '' ? answer.created_at_time : answer.created_at }}
                                            {{ answer.created_at !== answer.updated_at ? ', ' + langMap.main.updated + ' ' + (answer.updated_at_time !== '' ? answer.updated_at_time : answer.updated_at) : '' }}
                                            :
                                        </span>
                                        </v-col>
                                        <v-col v-if="index == 0 && currentUser.id == answer.employee.user_data.id"  cols="1">
                                        <v-btn right icon :color="themeBgColor" :title="langMap.ticket.edit_answer" @click="editAnswer(answer)">
                                            <v-icon>mdi-pencil</v-icon>
                                        </v-btn>
                                        </v-col>
                                        <div v-html="answer.answer"></div>
                                        <v-col v-if="answer.attachments.length > 0 " cols="12">
                                            <h4>{{ langMap.main.attachments }}</h4>
                                            <div
                                                v-for="attachment in answer.attachments"
                                            >
                                                <v-chip
                                                    :color="themeBgColor"
                                                    :href="attachment.link"
                                                    :text-color="themeFgColor"
                                                    class="ma-2"
                                                >
                                                    {{ attachment.name }}
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
                            v-if="ticket.description || ticket.attachments"
                            class="mx-auto"
                            color="#f2f2f2"
                            dense
                            outlined

                        >
                            <v-list-item>
                                <v-list-item-content>
                                    <!--                                    <span v-text="ticket.merge_comment"></span>-->
                                    <span class="text-left" style="font-weight: bold;">
                                        <v-avatar
                                            v-if="ticket.creator.user_data.avatar_url || ticket.creator.user_data.full_name"
                                            class="mr-2 mb-2"
                                            color="grey darken-1"
                                            size="2em"
                                        >
                                                    <v-img v-if="ticket.creator.user_data.avatar_url"
                                                           :src="ticket.creator.user_data.avatar_url"/>
                                                    <span v-else-if="ticket.creator.user_data.full_name"
                                                          class="white--text">
                                                        {{
                                                            ticket.creator.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                                        }}
                                                    </span>
                                                </v-avatar>
                                                <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>

                                                {{ ticket.creator.user_data.full_name }}
                                                {{
                                            ticket.created_at_time !== '' ? ticket.created_at_time : ticket.created_at
                                        }} - {{ ticket.name }}:
                                    </span>
                                    <div v-html="ticket.description"></div>
                                    <v-col v-if="ticket.attachments && ticket.attachments.length > 0 " cols="12">
                                        <h4>{{ langMap.main.attachments }}</h4>
                                        <div
                                            v-for="attachment in ticket.attachments"
                                            v-if="ticket.attachments.length > 0"
                                        >
                                            <v-chip
                                                :color="themeBgColor"
                                                :href="attachment.link"
                                                :text-color="themeFgColor"
                                                class="ma-2"
                                            >
                                                {{ attachment.name }}
                                            </v-chip>
                                        </div>
                                    </v-col>
                                </v-list-item-content>
                            </v-list-item>
                        </v-card>

                    </v-card-text>
                </v-card>

                <br/>

            </v-col>
            <v-col
                :md="secondColumnSize"
                cols="12"
                sm="12"
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
                                 <strong>{{ langMap.ticket.reported_by }}: </strong>
                                 <span v-if="ticket.contact !== null" class="float-md-right text-md-right">
                                     <v-avatar
                                         v-if="ticket.contact.user_data.avatar_url || ticket.contact.user_data.full_name"
                                         class="mr-2 mb-2"
                                         color="grey darken-1"
                                         size="2em"
                                     >
                                         <v-img v-if="ticket.contact.user_data.avatar_url"
                                                :src="ticket.contact.user_data.avatar_url"/>
                                         <span v-else-if="ticket.contact.user_data.full_name" class="white--text">
                                            {{
                                                 ticket.contact.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                             }}
                                         </span>
                                     </v-avatar>

                                     <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>
                                     {{ ticket.contact.user_data.full_name }}
                                     <br/>
                                     {{ ticket.from.name }}
                                  </span>
                            </span>
                            <v-spacer></v-spacer>
                            <template v-slot:actions>
                                <v-icon>$expand</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br/>
                            <v-btn class="float-md-right"
                                   color="white" small
                                   style="color: black;"
                                   @click="updateDialog = true"
                            >
                                <v-icon small>mdi-pencil</v-icon>
                                {{ langMap.main.edit }}
                            </v-btn>
                            <span>
                                    <v-label>
                                        {{ langMap.ticket.contact_email }}:
                                    </v-label>
                                <span v-if="ticket.contact !== null">
                                    {{ ticket.contact.user_data.email }}
                                </span>
                                </span>
                            <br/>
                            <v-label v-if="ticket.availability">
                                {{ langMap.ticket.availability }}:
                            </v-label>
                            <span v-if="ticket.availability" v-html="ticket.availability">
                            </span>
                            <br/>
                            <v-btn class="float-md-right"
                                   color="#F0" small
                                   style="color: black;"
                                   @click="serverAccessDialog = true"
                            >
                                <v-icon small>mdi-eye</v-icon>
                                Server access
                            </v-btn>
                            <br/>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br/>
                <v-expansion-panels
                    class="d-sm-none d-md-flex"
                >
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                 <strong>{{ langMap.sidebar.product }}: </strong>
                                 <span v-if="ticket.product" class="float-md-right text-md-right">
                                     {{ ticket.product.full_name }}
                                  </span>
                            </span>
                            <v-spacer></v-spacer>
                            <template v-slot:actions>
                                <v-btn class="float-md-right"
                                       color="white" small
                                       style="color: black;"
                                       @click="true"
                                >
                                    {{ langMap.main.edit }}
                                </v-btn>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br/>
                            <v-select
                                v-model="ticket.to_product_id"
                                :items="products"
                                :label="langMap.sidebar.product"
                                color="green"
                                dense
                                item-color="green"
                                item-text="full_name"
                                item-value="id"
                                @input="updateTicket"
                            />
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br/>
                <v-expansion-panels
                    v-model="teamAssignPanel"
                >
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <span v-if="ticket.team !== null">
                                <strong>{{ langMap.sidebar.team }}: </strong>
                                    <span class="float-md-right">{{ ticket.team.name }} </span>
                                    <br/>
                                </span>
                                 <span v-else>
                                    <strong>{{ langMap.ticket.no_assigned }}</strong>
                                </span>
                                <span v-if="ticket.assigned_person !== null">
                                <strong>{{ langMap.team.members }}: </strong>
                                    <span class="float-md-right text-md-right">
                                        <v-avatar
                                            v-if="ticket.assigned_person.user_data.avatar_url || ticket.assigned_person.user_data.full_name"
                                            class="mr-2 mb-2"
                                            color="grey darken-1"
                                            size="2em"
                                        >
                                         <v-img v-if="ticket.assigned_person.user_data.avatar_url"
                                                :src="ticket.assigned_person.user_data.avatar_url"/>
                                         <span v-else-if="ticket.assigned_person.user_data.full_name"
                                               class="white--text">
                                            {{
                                                 ticket.assigned_person.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                             }}
                                         </span>
                                        </v-avatar>
                                        {{ ticket.assigned_person.user_data.full_name }}
                                    </span>
                                </span>
                            </span>
                            <v-spacer></v-spacer>
                            <template v-slot:actions>
                                <v-btn v-if="!$helpers.auth.checkPermissionByIds([36])"
                                       class="float-md-right"
                                       color="white" small
                                       style="color: black;"
                                       @click="true"
                                >
                                    {{ langMap.main.edit }}
                                </v-btn>
                                <!--                                <v-icon>$expand</v-icon>-->
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br/>
                            <v-form>
                                <v-autocomplete
                                    v-model="ticket.to_team_id"
                                    :color="themeBgColor"
                                    :disabled="selectionDisabled"
                                    :item-color="themeBgColor"
                                    :items="teams"
                                    :label="langMap.sidebar.team"
                                    dense
                                    item-text="name"
                                    item-value="id"
                                    @change="selectTeam(); ticket.to_company_user_id = null;"
                                ></v-autocomplete>
                                <v-autocomplete
                                    v-model="ticket.to_company_user_id"
                                    :color="themeBgColor"
                                    :disabled="selectionDisabled"
                                    :item-color="themeBgColor"
                                    :items="employees"
                                    :label="langMap.team.members"
                                    dense
                                    item-value="employee.id"
                                >
                                    <template v-slot:selection="data">
                                        {{ data.item.employee.user_data.full_name }}
                                        <!--                                        ({{ data.item.employee.user_data.email }})-->
                                    </template>
                                    <template v-slot:item="data">
                                        {{ data.item.employee.user_data.full_name }}
                                        <!--                                        ({{ data.item.employee.user_data.email }})-->
                                    </template>
                                </v-autocomplete>
                                <v-btn :color="themeBgColor"
                                       :disabled="selectionDisabled || $helpers.auth.checkPermissionByIds([36])"
                                       class="ma-2"
                                       small
                                       style="color: white;"
                                       @click.native.stop="updateTicket"
                                >
                                    {{ langMap.ticket.assign_to }}
                                </v-btn>
                                <v-btn :color="themeBgColor"
                                       :disabled="!ticket.to_company_user_id || selectionDisabled || $helpers.auth.checkPermissionByIds([36])"
                                       class="ma-2"
                                       small
                                       color="grey"
                                       style="color: white;"
                                       @click.native.stop="ticket.to_company_user_id = null; updateTicket()"
                                >
                                    {{ langMap.ticket.clear_agent }}
                                </v-btn>
                                <v-btn class="ma-2"
                                       color="white" small
                                       style="color: black;"
                                       @click="teamAssignPanel = []"
                                >
                                    Cancel
                                </v-btn>
                            </v-form>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
                <br/>
                <v-expansion-panels v-if="!$helpers.auth.checkPermissionByIds([36])" v-model="notesPanel" multiple>
                    <v-expansion-panel>
                        <v-expansion-panel-header
                            style="background:#F0F0F0;"
                        >
                            <span>
                                <strong>{{ langMap.ticket.internal_notes }}</strong>
                            </span>
                            <template v-slot:actions>
                                <v-btn
                                    color="white" small
                                    style="color: black;"
                                    @click.native.stop="noteDialog = true"
                                >
                                    {{ langMap.ticket.add_internal_note }}
                                </v-btn>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <br/>
                            <span v-for="childTicket in ticket.child_tickets"
                                  :key="childTicket.id"
                            >
                            <div v-for="noticeItem in childTicket.notices"
                                 :key="noticeItem.id"
                            >
                                <v-card
                                    class="mx-auto"
                                    dense
                                    outlined
                                >
                                    <v-list-item three-line>
                                        <v-list-item-content class="custom-small-text">
                                            <strong>
                                                {{ childTicket.number }} |

                                                <v-avatar
                                                    v-if="noticeItem.employee.user_data.avatar_url || noticeItem.employee.user_data.full_name"
                                                    class="mr-2 mb-2"
                                                    color="grey darken-1"
                                                    size="2em"
                                                >
                                                    <v-img v-if="noticeItem.employee.user_data.avatar_url"
                                                           :src="noticeItem.employee.user_data.avatar_url"/>
                                                    <span v-else-if="noticeItem.employee.user_data.full_name"
                                                          class="white--text">
                                                        {{
                                                            noticeItem.employee.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                                        }}
                                                    </span>
                                                </v-avatar>
                                                <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>

                                                {{ noticeItem.employee.user_data.full_name }}

                                                {{ noticeItem.created_at }}:</strong>
                                            <div v-html="noticeItem.notice"></div>
                                        </v-list-item-content>

                                    </v-list-item>
                                </v-card>
                                <br/>
                            </div>
                            </span>
                            <div v-for="(noticeItem, index) in ticket.notices"
                                 :key="noticeItem.id"
                            >
                                <v-card
                                    class="mx-auto"
                                    dense
                                    outlined
                                >
                                    <v-list-item three-line>
                                        <v-list-item-content class="custom-small-text">
                                            <v-col :cols="index == 0 && currentUser.id == noticeItem.employee.user_data.id ? 11 : 12">
                                            <strong>
                                                <v-avatar
                                                    v-if="noticeItem.employee.user_data.avatar_url || noticeItem.employee.user_data.full_name"
                                                    class="mr-2 mb-2"
                                                    color="grey darken-1"
                                                    size="2em"
                                                >
                                                    <v-img v-if="noticeItem.employee.user_data.avatar_url"
                                                           :src="noticeItem.employee.user_data.avatar_url"/>
                                                    <span v-else-if="noticeItem.employee.user_data.full_name"
                                                          class="white--text">
                                                        {{
                                                            noticeItem.employee.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                                        }}
                                                    </span>
                                                </v-avatar>
                                                <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>

                                                {{ noticeItem.employee.user_data.full_name }}

                                                {{ noticeItem.created_at }}
                                                {{ noticeItem.created_at != noticeItem.updated_at ? ', ' + langMap.main.updated + ' ' + noticeItem.updated_at : ''}}
                                                :
                                            </strong>
                                            </v-col>
                                            <v-col v-if="index == 0 && currentUser.id == noticeItem.employee.user_data.id" cols="1">
                                                <v-btn icon :color="themeBgColor" right @click="editNotice(noticeItem)" :title="langMap.ticket.edit_notice">
                                                    <v-icon>mdi-pencil</v-icon>
                                                </v-btn>
                                            </v-col>
                                            <div v-html="noticeItem.notice"></div>
                                        </v-list-item-content>

                                    </v-list-item>
                                </v-card>
                                <br/>
                            </div>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>

            </v-col>
            <v-scale-transition>
                <v-col
                    v-show="thirdColumn"
                    :md="thirdColumnSize"
                    cols="12"
                    sm="12"
                >
                    <v-expand-transition>
                        <div v-show="linkBlock">
                            <v-expansion-panels
                                v-model="thirdColumnPanels"
                                multiple
                            >
                                <v-expansion-panel>
                                    <v-expansion-panel-header
                                        style="background:#F0F0F0;"
                                    >
                            <span>
                                <strong>Linked tickets</strong>
                                <v-badge
                                    v-if="ticket.merged_parent.length > 0 || ticket.merged_child.length > 0"
                                    :content="ticket.merged_parent.length+ticket.merged_child.length"
                                    color="orange"
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
                                        <v-text-field v-model="ticketsSearch" :color="themeBgColor"
                                                      :label="langMap.main.search"
                                                      @input="getTickets">
                                            <template slot="prepend">
                                                <v-menu
                                                    bottom
                                                    rounded
                                                    transition="slide-y-transition"
                                                >
                                                    <template v-slot:activator="{ on: menu, attrs }">
                                                        <v-btn
                                                            v-bind="attrs"
                                                            v-on="{...menu}"
                                                            text
                                                        >
                                                <span v-if="searchLabel !== ''">
                                                    {{ searchLabel }}
                                                </span>
                                                            <v-icon v-else>$expand</v-icon>
                                                        </v-btn>
                                                    </template>
                                                    <v-list
                                                        dense
                                                    >
                                                        <v-list-item
                                                            v-for="item in searchCategories"
                                                            :key="item.id"
                                                            link
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
                                        <v-list
                                            dense
                                        >
                                            <v-list-item-group :color="themeBgColor">
                                                <!--                                <v-subheader>Parent</v-subheader>-->
                                                <v-list-item
                                                    v-for="(item, i) in ticket.merged_parent"
                                                    :key="item.id"
                                                    :color="themeBgColor"
                                                    link
                                                    @click="showTicket(item.parent_ticket_data.id)"
                                                >
                                                    <v-tooltip bottom>
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-list-item-title v-on="on">
                                            <span>
                                                {{ item.parent_ticket_data.number }} | {{
                                                    item.parent_ticket_data.name
                                                }}
                                            </span>
                                                                <br/>
                                                                <span style="font-weight: lighter;">
                                                    {{
                                                                        item.parent_ticket_data.creator !== null && item.parent_ticket_data.creator.user_data !== null ? item.parent_ticket_data.creator.user_data.full_name : ''
                                                                    }},
                                                    {{
                                                                        item.parent_ticket_data.from !== null ? item.parent_ticket_data.from.name : ''
                                                                    }}
                                            </span>
                                                                <br/>
                                                                <span style="font-weight: lighter;">
                                                    {{ item.parent_ticket_data.last_update }}
                                            </span>
                                                            </v-list-item-title>
                                                        </template>
                                                        <span>
                                                {{ item.parent_ticket_data.number }} | {{
                                                                item.parent_ticket_data.name
                                                            }}
                                            </span>
                                                    </v-tooltip>
                                                </v-list-item>
                                                <!--                                <v-subheader>Child</v-subheader>-->
                                                <v-list-item
                                                    v-for="(item, i) in ticket.merged_child"
                                                    :key="item.id"
                                                    link
                                                    @click="showTicket(item.child_ticket_data.id)"
                                                >
                                                    <v-tooltip bottom>
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-list-item-title v-on="on">
                                            <span>
                                                    {{ item.child_ticket_data.number }} | {{
                                                    item.child_ticket_data.name
                                                }}
                                            </span>
                                                                <br/>
                                                                <span style="font-weight: lighter;">
                                                    {{
                                                                        item.child_ticket_data.creator !== null && item.child_ticket_data.creator.user_data !== null ? item.child_ticket_data.creator.user_data.full_name : ''
                                                                    }},
                                                    {{
                                                                        item.child_ticket_data.from !== null ? item.child_ticket_data.from.name : ''
                                                                    }}
                                            </span>
                                                                <br/>
                                                                <span style="font-weight: lighter;">
                                                    {{ item.child_ticket_data.last_update }}
                                            </span>
                                                            </v-list-item-title>
                                                        </template>
                                                        <span>
                                                {{ item.child_ticket_data.number }} | {{ item.child_ticket_data.name }}
                                            </span>
                                                    </v-tooltip>

                                                </v-list-item>
                                            </v-list-item-group>
                                        </v-list>
                                        <br/>
                                        <v-btn
                                            color="#f2f2f2"
                                            small
                                            @click="linkTicketProcess()"
                                        >
                                            New {{ langMap.main.link }}
                                        </v-btn>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                            <br>
                        </div>
                    </v-expand-transition>
                    <v-expand-transition>
                        <div v-show="mergeBlock">
                            <v-expansion-panels
                                multiple
                            >
                                <v-expansion-panel>
                                    <v-expansion-panel-header
                                        style="background:#F0F0F0;"
                                    >
                            <span>
                                <strong>{{ langMap.ticket.merged_abbr_full }}</strong>
                            </span>

                                        <template v-slot:actions>
                                            <v-icon @click.native.stop="hideMergeBlock">mdi-close</v-icon>
                                        </template>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-list
                                            dense
                                        >
                                            <v-list-item-group :color="themeBgColor">
                                                <v-list-item

                                                    v-for="(item, i) in ticket.child_tickets"
                                                    v-if="ticket.child_tickets"
                                                    :key="item.id"
                                                    :color="themeBgColor"
                                                >
                                                    <v-list-item-title>
                                                        <v-tooltip bottom>
                                                            <template v-slot:activator="{ on, attrs }">
                                                        <span v-on="on">
                                                    {{ item.number }}|{{ item.name }}
                                                </span>
                                                            </template>
                                                            <span>{{ item.number }}|{{ item.name }}</span>
                                                        </v-tooltip>
                                                        <v-tooltip top>
                                                            <template v-slot:activator="{ on, attrs }">

                                                                <v-icon v-on="on"
                                                                        color="red"
                                                                        small
                                                                        style="float: right"
                                                                        @click="mergeTicketForm.parent_ticket_id === item.id ? null: removeMerge(item.id)"
                                                                >
                                                                    {{
                                                                        mergeTicketForm.parent_ticket_id === item.id ? 'mdi-medal-outline' : 'mdi-cancel'
                                                                    }}
                                                                </v-icon>
                                                            </template>
                                                            <span>{{ langMap.main.cancel }}</span>
                                                        </v-tooltip>

                                                    </v-list-item-title>

                                                </v-list-item>
                                            </v-list-item-group>
                                        </v-list>
                                        <v-text-field v-model="ticketsSearch" :color="themeBgColor"
                                                      :label="langMap.main.search"
                                                      @input="getTickets">
                                            <template slot="prepend">
                                                <v-menu
                                                    bottom
                                                    rounded
                                                    transition="slide-y-transition"
                                                >
                                                    <template v-slot:activator="{ on: menu, attrs }">
                                                        <v-btn
                                                            v-bind="attrs"
                                                            v-on="{...menu}"
                                                            text
                                                        >
                                                <span v-if="searchLabel !== ''">
                                                    {{ searchLabel }}
                                                </span>
                                                            <v-icon v-else>$expand</v-icon>
                                                        </v-btn>
                                                    </template>
                                                    <v-list
                                                        dense
                                                    >
                                                        <v-list-item
                                                            v-for="item in searchCategories"
                                                            :key="item.id"
                                                            link
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
                                        <perfect-scrollbar style="height: 400px;">
                                            <div style="max-height: 390px;">
                                                <v-list
                                                    dense
                                                >
                                                    <v-list-item-group :color="themeBgColor">
                                                        <v-list-item
                                                            v-for="(item, i) in tickets"
                                                            :key="i"
                                                            :color="themeBgColor"
                                                            @click="
                                                            !mergeTicketForm.child_ticket_id.includes(item.id) ?
                                                            mergeTicketForm.child_ticket_id.push(item.id) :
                                                            mergeTicketForm.child_ticket_id.splice(mergeTicketForm.child_ticket_id.indexOf(item.id), 1)"
                                                        >
                                                            <v-list-item-title>
                                                <span>
                                                    <v-checkbox
                                                        :key="item.number"
                                                        v-model="mergeTicketForm.child_ticket_id"
                                                        :color="themeBgColor"
                                                        :disabled="item.parent_id !== null"
                                                        :item-color="themeBgColor"
                                                        :value="item.id"
                                                        dense
                                                        hide-details
                                                        style="display: inline-block; margin-top: 0!important"
                                                        @click="
                                                            !mergeTicketForm.child_ticket_id.includes(item.id) ?
                                                            mergeTicketForm.child_ticket_id.push(item.id) :
                                                            mergeTicketForm.child_ticket_id.splice(mergeTicketForm.child_ticket_id.indexOf(item.id), 1)"
                                                    />
                                                    <v-tooltip bottom>
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <span v-on="on">
                                                            {{ item.number }} | {{ item.name }}
                                                        </span>
                                                    </template>
                                                    <span>{{ item.number }} | {{ item.name }}</span>
                                                </v-tooltip>
                                                <v-tooltip top>
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-icon v-on="on"
                                                                :color="mergeTicketForm.parent_ticket_id === item.id ? 'red' : themeBgColor"
                                                                :disabled="!mergeTicketForm.child_ticket_id.includes(item.id)"
                                                                dark
                                                                style="float: right"
                                                                @click="mergeTicketForm.parent_ticket_id = item.id"
                                                        >
                                                            mdi-medal-outline
                                                        </v-icon>
                                                    </template>
                                                    <span>{{ langMap.main.primary }}</span>
                                                </v-tooltip>
                                                </span>

                                                                <br/>
                                                                <span style="font-weight: lighter;">
                                                    {{
                                                                        item.creator !== null && item.creator.user_data !== null ? item.creator.user_data.full_name : ''
                                                                    }},
                                                    {{ item.from !== null ? item.from.name : '' }}
                                                </span>
                                                                <br/>
                                                                <span style="font-weight: lighter;">
                                                    {{ item.last_update }}
                                                </span>
                                                            </v-list-item-title>

                                                        </v-list-item>
                                                    </v-list-item-group>
                                                </v-list>
                                            </div>

                                        </perfect-scrollbar>
                                        <br/>
                                        <v-textarea
                                            v-model="mergeTicketForm.merge_comment"
                                            :color="themeBgColor"
                                            auto-grow
                                            dense
                                            label="Add a note..."
                                            row-height="25"
                                            rows="2"
                                        />
                                        <v-spacer></v-spacer>
                                        <v-btn color="red" text>{{ langMap.main.cancel }}
                                        </v-btn>
                                        <v-btn color="green" text @click="mergeTicket()">
                                            {{ langMap.ticket.merge }}
                                        </v-btn>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                            <br>
                        </div>
                    </v-expand-transition>


                    <v-expansion-panels>
                        <v-expansion-panel>
                            <v-expansion-panel-header
                                style="background:#F0F0F0;"
                            >
                            <span>
                                <strong>{{ langMap.ticket.ticket_history }}</strong>
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
                                                    <v-avatar
                                                        v-if="history.employee.user_data.avatar_url || history.employee.user_data.full_name"
                                                        class="mr-2 mb-2"
                                                        color="grey darken-1"
                                                        size="2em"
                                                    >
                                                        <v-img v-if="history.employee.user_data.avatar_url"
                                                               :src="history.employee.user_data.avatar_url"/>
                                                        <span v-else-if="history.employee.user_data.full_name"
                                                              class="white--text">
                                                        {{
                                                                history.employee.user_data.full_name.split(/\s/).reduce((response, word) => response += word.slice(0, 1), '').substr(0, 2).toLocaleUpperCase()
                                                            }}
                                                    </span>
                                                    </v-avatar>
                                                    <v-icon v-else class="mr-2" large>mdi-account-circle</v-icon>

                                                    {{ history.employee.user_data.full_name }}

                                                    {{ history.created_at }}:
                                                </strong>
                                                <p>{{ history.description }}</p>
                                            </v-list-item-title>
                                        </v-list-item>
                                    </v-list-item-group>
                                </v-list>
                            </v-expansion-panel-content>
                        </v-expansion-panel>
                    </v-expansion-panels>

                </v-col>
            </v-scale-transition>
        </v-row>
    </v-container>

</template>

<script>

import EventBus from "../../components/EventBus";

export default {
    data() {
        return {
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            selectionDisabled: false,
            assignPanel: [],
            notesPanel: [],
            teamAssignPanel: [],
            signatures: [],
            selectedSignature: '',
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
            searchLabel: '',
            internalBillings: [],
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
                status_id: null,
                to_company_user_id: null,
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
                to_entity_id: null,
                to_team_id: null,
                contact_company_user_id: null,
                to_product_id: null,
                priority_id: null,
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
                id: '',
                files: [],
                answer: ''
            },
            ticketNotice: {
                id: '',
                notice: '',
            },
            onFileChange(form) {
                this[form].files = null;
                // console.log(event.target.files);
                this[form].files = event.target.files;
            },
            currentUser: {
                id: ''
            }
        }
    },
    watch: {
        checkProgress(value) {
            if (value === 100) {
                this.isLoaded = true;
            }
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
        this.getSignatures()
        this.getCurrentUser()
        // if (localStorage.getticket('auth_token')) {
        //     this.$router.push('tickets')
        // }
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeBgColor = color;
        });

        let ticketId = this.$route.params.id;
        window.history.replaceState({}, this.langMap.sidebar.ticket_list, '/tickets');
        window.history.pushState({ticket_id: ticketId}, this.langMap.sidebar.ticket, `/ticket/${ticketId}`);
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
                    this.getInternalBilling()
                }
            });
        },
        getSignatures() {
            axios.get('/api/email_signatures').then(response => {
                response = response.data
                if (response.success === true) {
                    this.signatures = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getTickets() {
            let search = this.ticketsSearch;
            axios.get('/api/ticket', {
                params: {
                    search_param: this.searchLabel,
                    search: this.ticketsSearch,
                    minified: true,
                }
            }).then(response => {
                if (this.ticketsSearch === search) {
                    response = response.data
                    let result = response.data.data
                    if (result.length > 1) {
                        let elementPos = result.map(function (x) {
                            return x.id;
                        }).indexOf(this.ticket.id);
                        if (elementPos !== -1) {
                            let temp = result[0]
                            result[0] = result[elementPos]
                            result[elementPos] = temp
                        }
                    }
                    if (this.ticketsSearch === '') {
                        this.mergeParentTickets = result
                        this.linkParentTickets = result
                    }
                    this.mergeTicketForm.child_ticket_id = [this.ticket.id]
                    this.tickets = result
                }
            });
        },
        getSuppliers() {
            axios.get('/api/supplier').then(response => {
                response = response.data
                if (response.success === true) {
                    this.suppliers = response.data
                    this.progressBuffer = this.progressBuffer + 20;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
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
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
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
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
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
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
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
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
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
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        selectTeam() {
            if (this.ticket.can_be_edited === false) {
                this.selectionDisabled = true
            }
            if (this.ticket.to_team_id !== null) {
                this.assignPanel = [0];
                axios.get(`/api/team/${this.ticket.to_team_id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.employees = response.data.employees
                    }
                });

            }
        },
        updateTicket() {
            this.ticket.from_entity_id = Object.values(this.from)[0]
            this.ticket.from_entity_type = Object.keys(this.from)[0]
            this.ticket.name = this.ticket.original_name
            axios.patch(`/api/ticket/${this.$route.params.id}`, this.ticket).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getTicket()
                    this.updateDialog = false
                    this.teamAssignPanel = []
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
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
                if (key !== 'files' && key !== 'id') {
                    if (key =='answer' && this.selectedSignature !== '') {
                        this.ticketAnswer[key] += '<hr><br/>' + this.selectedSignature
                    }
                    formData.append(key, this.ticketAnswer[key]);
                }
            }
            Array.from(this.ticketAnswer.files).forEach(file => formData.append('files[]', file));

            if (this.ticketAnswer.id) {
                axios.post(`/api/ticket/${this.$route.params.id}/answer/${this.ticketAnswer.id}`, formData, config).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.ticketAnswer.id = null
                        this.ticketAnswer.answer = ''
                        this.ticketAnswer.files = []
                        this.selectedSignature = ''
                        this.getTicket()
                        this.answerDialog = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            } else {
                axios.post(`/api/ticket/${this.$route.params.id}/answer`, formData, config).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.ticketAnswer.id = null
                        this.ticketAnswer.answer = ''
                        this.ticketAnswer.files = []
                        this.selectedSignature = ''
                        this.getTicket()
                        this.answerDialog = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            }
        },
        addTicketNotice() {
            if (this.selectedSignature !== '') {
                this.ticketNotice.notice += '<hr><br/>' + this.selectedSignature
            }

            if (this.ticketNotice.id) {
                axios.post(`/api/ticket/${this.$route.params.id}/notice/${this.ticketNotice.id}`, {
                    notice: this.ticketNotice.notice
                }).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.ticketNotice.id = ''
                        this.ticketNotice.notice = ''
                        this.selectedSignature = ''
                        this.getTicket()
                        this.noteDialog = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            } else {
                axios.post(`/api/ticket/${this.$route.params.id}/notice`, {
                    notice: this.ticketNotice.notice
                }).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.ticketNotice.id = ''
                        this.ticketNotice.notice = ''
                        this.selectedSignature = ''
                        this.getTicket()
                        this.noteDialog = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
                    }
                });
            }
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
            this.mergeBlock = false
        },
        hideLinkBlock() {
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
            const index = this.mergeTicketForm.child_ticket_id.indexOf(this.mergeTicketForm.parent_ticket_id);
            if (index > -1) {
                this.mergeTicketForm.child_ticket_id.splice(index, 1);
            }
            axios.post('/api/merge/ticket', this.mergeTicketForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getTickets()
                    this.getTicket()
                    this.mergeTicketForm.child_ticket_id = []
                    this.mergeTicketForm.parent_ticket_id = null
                    this.mergeTicketForm.merge_comment = null
                    this.snackbarMessage = this.langMap.history_actions.ticket_merged;
                    this.actionColor = 'alert'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        removeMerge(id) {
            axios.delete(`/api/merge/ticket/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getTickets()
                    this.getTicket()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
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
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
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
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
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
        getInternalBilling() {
            axios.get(`/api/billing/internal?${this.createAdditionalBillingIds()}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.internalBillings = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        createAdditionalBillingIds() {
            let queryString = '';

            if (this.ticket.from_company_user_id !== null) {
                queryString += `additional_user_ids[]=${this.ticket.creator.user_id}&`;
            }
            if (this.ticket.to_company_user_id !== null) {
                queryString += `additional_user_ids[]=${this.ticket.assigned_person.user_id}&`;
            }
            if (this.ticket.contact_company_user_id !== null) {
                queryString += `additional_user_ids[]=${this.ticket.contact.user_id}&`;
            }

            if (this.ticket.internal_billing_id !== null) {
                queryString += `internal_billing_id=${this.ticket.internal_billing_id}&`;
            }

            return queryString.slice(0, -1)
        },
        getCurrentUser() {
            axios.get('/api/user').then(response => {
                response = response.data
                if (response.success === true) {
                    this.currentUser = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        editAnswer(answer) {
            this.ticketAnswer.id = answer.id;
            this.ticketAnswer.answer = answer.answer;
            this.selectedSignature = '';
            this.answerDialog = true;
        },
        editNotice(notice) {
            this.ticketNotice.id = notice.id;
            this.ticketNotice.notice = notice.notice;
            this.selectedSignature = '';
            this.noteDialog = true;
        }
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
