<template>
    <v-tabs-items v-model="activeTab">
        <v-tab-item v-for="item in tabs" :key="item.id">
            <v-snackbar
                v-model="snackbar"
                :bottom="true"
                :color="actionColor"
                :right="true"
            >
                {{ snackbarMessage }}
            </v-snackbar>
            <v-data-table
                :expanded.sync="expanded"
                :footer-props="footerProps"
                :headers="headers"
                :items="tickets"
                :loading="loading"
                :loading-text="langMap.main.loading"
                :options.sync="options"
                :server-items-length="totalTickets"
                :single-expand="singleExpand"
                class="elevation-4"
                fixed-header
                dense
                height="75vh"
                hide-default-footer
                item-key="id"
                show-expand
                @click:row="showItem"
            >
                <template v-slot:top>
                    <v-row style="padding-top: 10px">
                        <v-col md="5" sm="12">
                            <v-text-field
                                v-model="ticketsSearch"
                                :color="themeBgColor"
                                :disabled="filterId !== '' || searchDisabled"
                                :label="langMap.main.search"
                                hide-details
                                prepend-icon="mdi-magnify"
                                style="padding-top: 4px"
                                @input="
                  getTickets();
                  updateUrl();
                "
                            >
                                <template slot="prepend">
                                    <v-menu bottom rounded transition="slide-y-transition">
                                        <template v-slot:activator="{ on: menu, attrs }">
                                            <v-btn
                                                :disabled="searchDisabled"
                                                text
                                                v-bind="attrs"
                                                v-on="{ ...menu }"
                                                small
                                            >
                        <span v-if="searchLabel !== ''">
                          {{ searchLabel }}
                        </span>
                                                <v-icon v-else>$expand</v-icon>
                                            </v-btn>
                                        </template>
                                        <v-list dense>
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
                        </v-col>

                        <v-col md="5" sm="12">
                            <v-autocomplete
                                v-if="!filterPanel"
                                v-model="filterId"
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                :items="filters"
                                :label="langMap.filter.saved_filters"
                                style="align-items: flex-end"
                                clearable
                                dense
                                hide-details
                                item-text="name"
                                item-value="id"
                                @change="getTickets"
                            >
                                <template v-slot:append-outer>
                                    <v-icon :disabled="filterId === ''" @click="removeFilter">
                                        mdi-delete
                                    </v-icon>
                                </template>
                                <template slot="prepend">
                                    <v-menu bottom rounded transition="slide-y-transition">
                                        <template v-slot:activator="{ on: menu, attrs }">
                                            <v-btn
                                                text
                                                v-if="!filterPanel"
                                                small
                                                @click="filterPanel = true"
                                            >
                                                <v-icon>mdi-filter</v-icon>
                                            </v-btn>
                                        </template>
                                    </v-menu>
                                </template>
                            </v-autocomplete>
                            <v-text-field
                                v-if="filterPanel"
                                v-model="filterName"
                                :color="themeBgColor"
                                :label="langMap.main.name"
                                style="margin-bottom: 0"
                                dense
                            >
                                <template slot="prepend">
                                    <v-menu bottom rounded transition="slide-y-transition">
                                        <template v-slot:activator="{ on: menu, attrs }">
                                            <v-btn v-if="filterPanel" text small @click="saveFilter">
                                                <v-icon>mdi-filter-plus</v-icon>
                                            </v-btn>
                                        </template>
                                    </v-menu>
                                </template>
                            </v-text-field>
                        </v-col>

                        <v-col md="1" sm="12">
                            <v-select
                                v-model="options.itemsPerPage"
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                :items="footerProps.itemsPerPageOptions"
                                :label="langMap.main.items_per_page"
                                hide-details
                                style="padding-top: 4px"
                                @change="updateItemsCount"
                            ></v-select>
                        </v-col>

                        <v-col md="1" sm="12">
                            <v-pagination
                                v-model="options.page"
                                :color="themeBgColor"
                                :length="lastPage"
                                :page="options.page"
                                :total-visible="0"
                                class="mx-4 d-flex"
                                circle
                            >
                            </v-pagination>
                        </v-col>
                    </v-row>
                    <v-expand-transition>
                        <v-row v-show="filterPanel">
                            <v-col
                                v-for="filterParam in filterParams"
                                :key="filterParam.name"
                                :cols="filterParam.flex"
                                class="mx-4"
                            >
                                <v-checkbox
                                    v-model="filterParam.active"
                                    :color="themeBgColor"
                                    :label="filterParam.name"
                                >
                                </v-checkbox>
                                <v-expand-transition>
                                    <v-card v-show="filterParam.active" elevation="5">
                                        <v-card-text style="padding: 5px 10px">
                                            <v-select
                                                v-model="filterParam.selectedCompareParam"
                                                :color="themeBgColor"
                                                :items="filterParam.compareParams"
                                                :label="langMap.filter.compare_param"
                                                hide-details
                                                item-text="name"
                                                item-value="value"
                                            ></v-select>
                                            <v-select
                                                v-if="filterParam.type === 'select'"
                                                v-model="filterParam.value"
                                                :color="themeBgColor"
                                                :items="filterParam.prefilled_values"
                                                :label="langMap.filter.value"
                                                hide-details
                                                item-text="name"
                                                item-value="id"
                                                @change="manageFilter"
                                            >
                                            </v-select>
                                            <v-text-field
                                                v-if="filterParam.type === 'string'"
                                                v-model="filterParam.value"
                                                :color="themeBgColor"
                                                hide-details
                                                label="value"
                                                @input="manageFilter"
                                            >
                                            </v-text-field>
                                        </v-card-text>
                                    </v-card>
                                </v-expand-transition>
                            </v-col>
                        </v-row>
                    </v-expand-transition>
                </template>
                <template v-slot:footer>
                    <v-pagination
                        v-model="options.page"
                        :color="themeBgColor"
                        :length="lastPage"
                        :page="options.page"
                        :total-visible="5"
                        circle
                    >
                    </v-pagination>
                </template>
                <template v-slot:item.ticket_type_id="{ item }">
                    {{ $helpers.i18n.localized(item.ticket_type) }}
                </template>
                <template v-slot:item.status.name="{ item }"></template>
                <template v-slot:item.name="{ index, item }">
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on, attrs }">
              <span
                  v-bind="attrs"
                  v-on="on"
                  style="
                  display: block;
                  max-width: 350px;
                  text-overflow: ellipsis;
                  overflow: hidden;
                  white-space: nowrap;
                "
              >
                {{ item.name }}
              </span>
                        </template>
                        <span>
              {{ item.name }}
            </span>
                    </v-tooltip>
                </template>
                <template v-slot:item.data-table-expand="{ index, item }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <v-menu offset-y>
                                <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                        :style="`color: ${item.priority.color === 'amber' ? '#FEBE00' : item.priority.color}`"
                                        dark
                                        icon
                                        x-small
                                        v-bind="attrs"
                                        v-on="on"
                                        @click.prevent.stop="expandItem(item)"
                                    >
                                        <v-icon x-small> mdi-checkbox-blank-circle</v-icon>
                                    </v-btn>
                                </template>
                                <v-list>
                                    <v-list-item
                                        link
                                        v-for="(element, idx) in dropdownItems"
                                        :key="idx"
                                    >
                                        <v-list-item-title
                                            style="font-size: 16px;"
                                            @click="handleClickDropdownItem(item, element.action)"
                                        >
                                            <div style="display: flex; align-items: center;">
                                                <v-icon>{{ element.icon }}</v-icon>
                                                <span style="margin-left: 16px;">{{ element.title }}</span>
                                            </div>
                                        </v-list-item-title>
                                    </v-list-item>
                                </v-list>
                            </v-menu>
                        </template>
                        <span>{{ item.priority.name }}.{{ item.status.name }}</span>
                    </v-tooltip>
                </template>
                <template v-slot:item.last_update="{ item }">
                    {{
                        moment(item.last_update).isValid()
                            ? moment(item.last_update).format('DD.MM. hh:mm')
                            : item.last_update
                    }}
                </template>
                <template v-slot:item.number="{ item }">
                    <v-tooltip top>
                        <template v-slot:activator="{ on, attrs }">
                            <div v-on="on">
                                {{ item.number }}
                                <v-badge
                                    :color="item.status.color"
                                    dot
                                    inline
                                    @click="showItem(item)"
                                >
                                    <div v-on="on">#{{ item.id }}</div>
                                </v-badge>
                            </div>
                        </template>
                        {{ item.priority.name }}.{{ item.status.name }}
                    </v-tooltip>
                </template>
                <template v-slot:item.category.name="{ item }">
                    {{ item.category ? item.category.name : '' }}
                </template>
                <template v-slot:item.product.name="{ item }">
                    {{ item.product ? item.product.name : '' }}
                </template>
                <template v-slot:item.assigned_person_full_name="{ item }">
                    <div
                        v-if="item.assigned_person_full_name"
                        class="justify-center"
                        @click="showItem(item)"
                    >
                        {{ item.assigned_person_full_name }}
                    </div>
                    <div v-else></div>
                </template>
            </v-data-table>

            <template>
                <v-dialog v-model="removeTicketDialog" max-width="480" persistent>
                    <v-card>
                        <v-card-title
                            :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                        >
                            {{ langMap.main.delete_selected }}?
                        </v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="grey darken-1"
                                text
                                @click="removeTicketDialog = false"
                            >
                                {{ langMap.main.cancel }}
                            </v-btn>
                            <v-btn
                                color="red darken-1"
                                text
                                @click="deleteTicket(selectedticketId)"
                            >
                                {{ langMap.main.delete }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </template>
            <template>
                <v-dialog v-model="mergeTicketDialog" max-width="480" persistent>
                    <v-card>
                        <v-card-title
                            :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                            class="mb-5"
                        >
                            {{ langMap.main.link }}
                        </v-card-title>
                        <v-card-text>
                            <v-autocomplete
                                v-model="mergeTicketForm.parent_ticket_id"
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                :items="tickets"
                                :label="langMap.ticket.subject"
                                dense
                                item-text="name"
                                item-value="id"
                            />
                            <v-autocomplete
                                v-model="mergeTicketForm.child_ticket_id"
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                :items="tickets"
                                :label="langMap.ticket.subject"
                                dense
                                item-text="name"
                                item-value="id"
                                multiple
                            />
                            <v-textarea
                                v-model="mergeTicketForm.merge_comment"
                                :color="themeBgColor"
                                :label="langMap.main.description"
                                auto-grow
                                dense
                                row-height="25"
                                rows="2"
                                shaped
                            />
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                :color="themeBgColor"
                                darken-1
                                text
                                @click="mergeTicketDialog = false"
                            >
                                {{ langMap.main.cancel }}
                            </v-btn>
                            <v-btn :color="themeBgColor" darken-1 text @click="mergeTicket()">
                                {{ langMap.main.link }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </template>
            <template>
                <v-dialog v-model="closeTicketDialog" max-width="540" persistent>
                    <v-card>
                        <v-card-title
                            :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                        >
                            {{ langMap.ticket.close_ticket_title }}
                        </v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="grey darken-1"
                                text
                                @click="closeTicketDialog = false"
                            >
                                {{ langMap.main.cancel }}
                            </v-btn>
                            <v-btn color="red darken-1" text @click="closeTicket()">
                                {{ langMap.ticket.close_ticket }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </template>

            <v-dialog v-model="assignTicketDialog" v-if="ticket" max-width="350" persistent>
                <v-card>
                    <v-card-title
                        :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                    >
                        {{ langMap.main.assign_priority }}
                    </v-card-title>
                    <div style="padding: 0 24px; display: flex; justify-content: center; height: 200px;">
                        <v-select
                            :items="priorities"
                            v-model="ticket.priority_id"
                            label="Solo field"
                            class="mt-7"
                            solo
                            item-text="name"
                            item-value="id"
                        >
                            <template v-slot:item="data">
                                <v-list-item v-bind="data.attrs" v-on="data.on">
                                    <v-badge :color="data.item.color" dot inline></v-badge>
                                    <v-list-item-content class="ml-2">
                                        {{ data.item.name }}
                                    </v-list-item-content>
                                </v-list-item>
                            </template>

                            <template v-slot:selection="data">
                                <v-badge :color="data.item.color" dot inline></v-badge>
                                <span class="ml-2">{{ data.item.name }}</span>
                            </template>
                        </v-select>
                    </div>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="grey darken-1"
                            text
                            @click="assignTicketDialog = false"
                        >
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="darken-1" :color="themeBgColor" text @click="changePriority">
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="previewLastResponses" v-if="currentTicketInfo" max-width="800" persistent>
                <v-card>
                    <v-card-title
                        :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`"
                    >
                        {{ langMap.main.preview_last_response }}
                    </v-card-title>
                    <div
                        v-if="currentTicketInfo.answers.length"
                        v-for="(answer, index) in [currentTicketInfo.answers[0]]"
                        :key="index"
                        class="ticket--answer"
                    >
                        <v-card class="mx-auto" dense outlined>
                            <v-list-item>
                                <v-list-item-content>
                                    <v-col :cols="11">
                      <span class="text-left" style="font-weight: bold">
                        <v-avatar
                            v-if="
                            answer.employee.user_data.avatar_url ||
                            answer.employee.user_data.full_name
                          "
                            class="mr-2 mb-2"
                            color="grey darken-1"
                            size="2em"
                        >
                          <v-img
                              v-if="answer.employee.user_data.avatar_url"
                              :src="answer.employee.user_data.avatar_url"
                          />
                          <span
                              v-else-if="answer.employee.user_data.full_name"
                              class="white--text"
                          >
                            {{
                                  answer.employee.user_data.full_name
                                      .split(/\s/)
                                      .reduce(
                                          (response, word) =>
                                              (response += word.slice(0, 1)),
                                          ''
                                      )
                                      .substr(0, 2)
                                      .toLocaleUpperCase()
                              }}
                          </span>
                        </v-avatar>
                        <v-icon v-else class="mr-2" large
                        >mdi-account-circle</v-icon
                        >

                        {{ answer.employee.user_data.full_name }}
                        {{
                              answer.created_at_time !== ''
                                  ? moment(answer.created_at_time).isValid()
                                      ? moment(answer.created_at_time).format(
                                          'DD.MM.YYYY HH:mm'
                                      )
                                      : answer.created_at_time
                                  : moment(answer.created_at).format(
                                      'DD.MM.YYYY HH:mm'
                                  )
                          }}
                        {{
                              answer.created_at !== answer.updated_at
                                  ? ', ' +
                                  langMap.main.updated +
                                  ' ' +
                                  (answer.updated_at_time !== ''
                                      ? moment(answer.updated_at_time).isValid()
                                          ? moment(answer.updated_at_time).format(
                                              'DD.MM.YYYY HH:mm'
                                          )
                                          : answer.updated_at_time
                                      : moment(answer.updated_at).format(
                                          'DD.MM.YYYY HH:mm'
                                      ))
                                  : ''
                          }}
                        :
                        <span style="color: grey"
                        ><strong v-if="answer.is_internal"
                        >[{{ langMap.ticket.internal_note }}]</strong
                        ></span
                        >
                      </span>
                                    </v-col>
                                    <v-col
                                        cols="1"
                                    >
<!--                                        <v-btn-->
<!--                                            :color="themeBgColor"-->
<!--                                            :title="langMap.ticket.edit_answer"-->
<!--                                            icon-->
<!--                                            right-->
<!--                                            @click="editAnswer(answer)"-->
<!--                                        >-->
<!--                                            <v-icon>mdi-pencil</v-icon>-->
<!--                                        </v-btn>-->
                                    </v-col>
                                    <br class="pb-2"/>
                                    <div v-html="answer.answer"></div>
                                    <v-col v-if="answer.attachments.length > 0" cols="12">
                                        <h4>{{ langMap.main.attachments }}</h4>
                                        <div v-for="attachment in answer.attachments">
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
                        <v-spacer> &nbsp;</v-spacer>
                    </div>
                    <div style="display: flex; justify-content: center; padding: 12px 26px;" v-else >
                        <span>No answers</span>
                    </div>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="red darken-1" text @click="previewLastResponses = false">
                            {{ langMap.main.close }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-tab-item>
    </v-tabs-items>
</template>

<script>
import EventBus from '../EventBus.vue';

export default {
    name: 'ticketsTable',
    props: {
        searchLabel: {
            type: String,
            default: 'company',
        },
        searchValue: {
            type: String,
            default: '',
        },
        activeTab: {
            type: Number,
            default: 1,
        },
        tabs: {
            type: Array,
            default: [],
        },
        pageName: {
            type: String,
            default: '',
        },
        page: {
            type: [String, Number],
            default: 1,
        },
        tab: {
            type: [String, Number],
            default: 1,
        },
        searchDisabled: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            expanded: [],
            singleExpand: false,
            totalTickets: 0,
            lastPage: 0,
            loading: this.themeBgColor,
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [
                    {
                        text: 10,
                        value: 10,
                    },
                    {
                        text: 20,
                        value: 20,
                    },
                    {
                        text: 50,
                        value: 50,
                    },
                    {
                        text: 100,
                        value: 100,
                    },
                    {
                        text: this.$store.state.lang.lang_map.sidebar.all,
                        value: 10000,
                    },
                ],
            },
            headers: [
                {text: '', value: 'data-table-expand', width: '20px'},
                {
                    text: `${this.$store.state.lang.lang_map.ticket.number}`,
                    value: 'number',
                    width: '160px',
                },
                {
                    text: `${this.$store.state.lang.lang_map.ticket.last_update}`,
                    value: 'last_update',
                    width: '100px',
                },
                {
                    text: `${this.$store.state.lang.lang_map.ticket.type}`,
                    value: 'ticket_type_id',
                    width: '150px',
                },
                {
                    text: `${this.$store.state.lang.lang_map.ticket.company_from}`,
                    value: 'from_entity_name',
                    width: '250px',
                },
                {
                    text: `${this.$store.state.lang.lang_map.main.contact}`,
                    value: 'contact_full_name',
                    width: '150px',
                },
                {
                    text: `${this.$store.state.lang.lang_map.ticket.subject}`,
                    value: 'name',
                    width: '260px',
                },
                {
                    text: `${this.$store.state.lang.lang_map.main.product}`,
                    value: 'product.name',
                    width: '200px',
                },
                {
                    text: `${this.$store.state.lang.lang_map.team.members}`,
                    value: 'assigned_person_full_name',
                    width: '80px',
                },
            ],
            dropdownItems: [
                { action: 'view', icon: 'mdi-eye', title: this.$store.state.lang.lang_map.ticket.view },
                { action: 'link', icon: 'mdi-clipboard-flow', title: this.$store.state.lang.lang_map.ticket.link_tickets },
                { action: 'delete', icon: 'mdi-delete', title: this.$store.state.lang.lang_map.individuals.delete },
                { action: 'close', icon: 'mdi-check', title: this.$store.state.lang.lang_map.main.close },
                { action: 'assign', icon: 'mdi mdi-alpha-p-box-outline', title: this.$store.state.lang.lang_map.main.assign_priority },
                { action: 'preview', icon: 'mdi mdi-file-find', title: this.$store.state.lang.lang_map.main.preview_last_response },
            ],
            types: [],
            tickets: [],
            options: {
                page: this.$route.query.page ? parseInt(this.$route.query.page) : 1,
                sortDesc: [true],
                sortBy: ['id'],
                withSpam: false,
                onlyOpen: true,
                onlyForUser: false,
                itemsPerPage: localStorage.itemsPerPage
                    ? parseInt(localStorage.itemsPerPage)
                    : 10,
                priority: null,
                type: null,
            },
            ticketsSearch: '',
            filterId: '',
            searchCategories: [
                {
                    id: 1,
                    name: 'Company',
                },
                {
                    id: 2,
                    name: 'ID',
                },
                {
                    id: 3,
                    name: 'Subject',
                },
                {
                    id: 4,
                    name: 'Contact',
                },
            ],
            filters: [],
            filterName: '',
            filterPanel: false,
            filterParams: [
                {
                    active: false,
                    value: null,
                    selectedCompareParam: null,
                    query_fields: [],
                    compareParams: [],
                },
            ],
            mergeTicketForm: {
                parent_ticket_id: null,
                child_ticket_id: null,
                merge_comment: null,
            },
            selectedticketId: null,
            closeTicketDialog: false,
            removeTicketDialog: false,
            mergeTicketDialog: false,
            ticket: null,
            assignTicketDialog: false,
            priorities: [],
            previewLastResponses: false,
            currentTicketInfo: null,
        };
    },
    mounted() {
        let that = this;
        this.setOptionsByActiveTab(this.activeTab);
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.getTicketFilterParameters();
        this.getFilters();
        this.getProducts();
        this.getPriorities();
        this.getTypes();
        this.getStatuses();
        setTimeout(() => {
            if (!this.$helpers.auth.checkPermissionByIds([1])) {
                this.$router.push('knowledge_base');
            }
        }, 500);

        if (this.searchValue) {
            this.ticketsSearch = this.searchValue;
        }
    },
    methods: {
        updateUrl() {
            const query = {...this.$route.query};

            if (this.ticketsSearch?.length) {
                query.search = this.ticketsSearch;
            } else {
                delete query.search;
            }

            if (this.searchLabel?.length) {
                query.searchLabel = this.searchLabel;
            } else {
                delete query.searchLabel;
            }
        },
        manageFilter() {
            this.queryArray = [];
            this.filterParams.forEach((filterParam) => {
                if (filterParam.active === true) {
                    filterParam.query_fields.forEach((field) => {
                        if (filterParam.value && filterParam.selectedCompareParam) {
                            this.queryArray.push({
                                field: field,
                                compare_parameter: filterParam.selectedCompareParam,
                                value: filterParam.value,
                            });
                        }
                    });
                }
            });
        },
        expandItem(item) {
            this.ticket = item;
            // const query = {...this.$route.query};
            // if (this.expanded.indexOf(item) !== -1) {
            //     delete query.expanded;
            //     this.expanded = [];
            //     this.$router.push({name: this.pageName, query});
            // } else {
            //     this.expanded.splice(0, 1, item);
            //     this.$router.push({
            //         name: this.pageName,
            //         query: {...query, expanded: item.id},
            //     });
            // }
        },
        saveFilter() {
            let data = {
                name: this.filterName,
                filter_parameters: this.queryArray,
            };
            axios.post('/api/ticket_query', data).then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.snackbarMessage = 'Filter was created';
                    this.actionColor = 'success';
                    this.snackbar = true;
                    this.filterPanel = false;
                    this.getFilters();
                } else {
                    this.snackbarMessage = 'Filter creating error';
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        removeFilter() {
            axios.delete(`/api/ticket_filters/${this.filterId}`).then((response) => {
                response = response.data;
                if (response.success) {
                    this.filterId = '';
                    this.getFilters();
                }
            });
            this.getTickets();
        },
        selectSearchCategory(item) {
            this.searchLabel = item.name;
        },
        getTicket() {
            axios.get(`/api/ticket/${this.ticket.id}`).then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.currentTicketInfo = response.data
                }
            })
        },
        getProducts() {
            axios.get('/api/product').then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.products = response.data.data;
                }
            });
        },
        getTicketFilterParameters() {
            axios.get('/api/ticket_filter_parameters').then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.filterParams = response.data;
                }
            });
        },
        getPriorities() {
            axios.get('/api/ticket_priorities').then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.priorities = response.data;
                }
            });
        },
        getTypes() {
            axios.get('/api/ticket_types').then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.types = response.data;
                }
            });
        },
        getStatuses() {
            axios.get('/api/ticket_statuses').then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.ticketStatuses = response.data;
                }
            });
        },
        getTickets() {
            this.loading = this.themeBgColor;
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id';
                this.options.sortDesc[0] = true;
            }
            if (this.totalTickets > 0 && this.totalTickets < this.options.itemsPerPage) {
                this.options.page = 1;
            }

            axios
                .get('/api/ticket', {
                    params: {
                        search_param: this.searchLabel,
                        search: this.searchValue ? this.searchValue : this.ticketsSearch,
                        sort_by: this.manageSortableField(this.options.sortBy[0]),
                        sort_val: this.options.sortDesc[0],
                        with_spam: this.options.withSpam,
                        only_for_user: this.options.onlyForUser,
                        only_open: this.options.type === 7 ? false : this.options.onlyOpen,
                        per_page: this.options.itemsPerPage,
                        minified: this.minifiedTickets,
                        filter_id: this.filterId,
                        page: this.options.page,
                        priority: this.options.priority,
                        type: this.options.type,
                        status: this.options.status,
                    },
                })
                .then((response) => {
                    response = response.data;
                    if (response.success === true) {
                        this.tickets = response.data.data;
                        this.totalTickets = response.data.total;
                        this.lastPage = response.data.last_page;
                        if (this.searchValue) {
                            this.ticketsSearch = this.searchValue;
                        }
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                    this.loading = false;
                });
        },
        getFilters() {
            axios.get(`/api/ticket_filters`).then((response) => {
                response = response.data;
                this.filters = response.data;
            });
        },
        showItem(item) {
            localStorage.setItem('backPath', JSON.stringify(this.$route.fullPath));
            this.$router.push(`/ticket/${item.id}`)
        },
        ticketDeleteProcess(item) {
            this.selectedticketId = item.id;
            this.removeTicketDialog = true;
        },
        deleteTicket(id) {
            axios.delete(`/api/ticket/${id}`).then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.getTickets();
                    this.snackbarMessage = 'Ticket was deleted ';
                    this.actionColor = 'success';
                    this.snackbar = true;
                    this.removeTicketDialog = false;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        mergeTicketProcess(id) {
            this.mergeTicketForm.parent_ticket_id = id;
            this.mergeTicketDialog = true;
            this.minifiedTickets = true;
        },
        mergeTicket() {
            axios.post('/api/link/ticket', this.mergeTicketForm).then((response) => {
                response = response.data;
                if (response.success === true) {
                    this.minifiedTickets = false;
                    this.getTickets();
                    this.mergeTicketForm.merge_comment = null;
                    this.mergeTicketForm.parent_ticket_id = null;
                    this.mergeTicketForm.child_ticket_id = null;
                    this.mergeTicketDialog = false;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value;
            localStorage.itemsPerPage = value;
            this.options.page = 1;
        },
        manageSortableField(value) {
            if (value === 'last_update') return 'updated_at';
            if (value === 'status.name') return 'status_id';
            if (value === 'assigned_person_full_name') return 'to_company_user_id';
            if (value === 'product.name') return 'to_product_id';
            if (value === 'name') return 'name';
            if (value === 'from.name') return 'from_entity_id';
            if (value === 'to.name') return 'to_entity_id';
            if (value === 'priority.name') return 'priority_id';
            return value;
        },
        updateTicket() {
            axios
                .patch(`/api/ticket/${this.ticket.id}`, this.ticket)
                .then((response) => {
                    response = response.data;
                    if (response.success === true) {
                        this.getTickets();
                        this.updateDialog = false;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
        },
        closeTicket() {
            this.ticket.status_id = 5;
            this.updateTicket();
            this.expanded = [];
            this.closeTicketDialog = false;
        },
        setOptionsByActiveTab(tab) {
            const options = this.options;
            const emptyOptions = {
                onlyOpen: false,
                withSpam: false,
                onlyForUser: false,
                priority: null,
                type: null,
                status: null,
            };
            switch (tab) {
                case 0: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        status: this.ticketStatuses?.map((status) => status.id),
                    });
                }
                case 1: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        onlyOpen: true,
                        type: this.types
                            .filter((type) => type.id !== 7)
                            .map((type) => type.id),
                    });
                }
                case 2: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        onlyForUser: true,
                    });
                }
                case 3: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        onlyOpen: true,
                        priority: 1,
                        type: this.types
                            .filter((type) => type.id !== 7)
                            .map((type) => type.id),
                    });
                }
                case 4: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        onlyOpen: true,
                        type: 4,
                    });
                }
                case 5: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        onlyOpen: true,
                        type: 7,
                    });
                }
                case 6: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        withSpam: true,
                    });
                }
                case 7: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        status: this.ticketStatuses?.filter((status) => status.id === 5)
                            .map((status) => status.id),
                    });
                }
                default: {
                    return (this.options = {
                        ...options,
                        ...emptyOptions,
                        onlyOpen: true,
                        type: this.types
                            .filter((type) => type.id !== 7)
                            .map((type) => type.id),
                    });
                }
            }
        },
        handleClickDropdownItem(item, action) {
            switch(action) {
                case 'view':
                    return this.showItem(item)
                case 'link':
                    return this.mergeTicketProcess(item.id)
                case 'delete':
                    return this.ticketDeleteProcess(item)
                case 'close':
                    return this.closeTicketDialog = true
                case 'assign':
                    return this.assignTicketDialog = true
                case 'preview':
                    this.getTicket();
                    return this.previewLastResponses = true
                default:
                    return this.showItem(item)
            }
        },
        changePriority() {
            this.updateTicket();
            this.assignTicketDialog = false
        },
    },
    watch: {
        activeTab(value) {
            this.setOptionsByActiveTab(value);
            this.options.page = 1
            if (value !== parseInt(this.$route.query.tab) && !isNaN(value)) {
                this.$router.push({
                    name: this.pageName,
                    query: { ...this.$route.query, tab: value },
                });
            }
        },
        searchLabel: {
            handler: _.debounce(function (value) {
                this.updateUrl();
                this.getTickets();
            }, 500),
            deep: true,
        },
        ticketsSearch: {
            handler: _.debounce(function (v) {
                this.updateUrl();
                this.getTickets();
            }, 500),
            deep: true,
        },
        searchValue(value) {
            this.getTickets()
            this.ticketsSearch = value
        },
        tickets() {
            if (this.$route.query.expanded && this.tickets.length) {
                this.expanded = this.tickets.filter(
                    (ticket) =>
                        ticket.id.toString() === this.$route.query.expanded.toString()
                );
            }
        },
        options: {
            handler: _.debounce(function () {
                this.getTickets();
            }, 500),
            deep: true,
        },
        loading() {
            if (this.$parent.$parent.$refs.container) {
                this.$parent.$parent.$refs.container.scrollTop = 0;
            }
        },
        'options.page'(value) {
            if (value !== parseInt(this.$route.query.page)) {
                this.$router.push({
                    name: this.pageName,
                    query: {...this.$route.query, page: value,},
                });

            }
        },
        '$route'() {
            if (this.$route.query.page) {
                this.options.page = parseInt(this.$route.query.page)
            }

            if(typeof this.$route.query.page === "undefined"){
                this.options.page = 1
            }
        }
    },
};
</script>
