<template>
    <v-container fluid>
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
            hide-default-footer
            item-key="id"
            show-expand
            @click:row="showItem"
        >
            <template v-slot:top>

                <v-row>
                    <v-col md="10" sm="12">
                        <v-text-field
                            v-model="ticketsSearch"
                            :color="themeBgColor"
                            :disabled="filterId !== ''"
                            :label="langMap.main.search"
                            class="ma-2"
                            hide-details
                            prepend-icon="mdi-magnify"
                            @input="getTickets"

                        >
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
                    </v-col>
                    <v-col md="2" sm="12">
                        <v-select
                            v-model="options.itemsPerPage"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            :items="footerProps.itemsPerPageOptions"
                            :label="langMap.main.items_per_page"
                            class="ma-2"
                            hide-details
                            @change="updateItemsCount"
                        ></v-select>
                    </v-col>
                    <v-col md="4" sm="12">
                        <v-autocomplete
                            v-if="!filterPanel"
                            v-model="filterId"
                            :color="themeBgColor"
                            :item-color="themeBgColor"
                            :items="filters"
                            :label="langMap.filter.saved_filters"
                            class="ma-2"
                            dense
                            hide-details
                            item-text="name"
                            item-value="id"
                            prepend-icon="mdi-filter"
                            @change="getTickets"
                        >
                            <template v-slot:append-outer>
                                <v-icon
                                    :disabled="filterId === ''"
                                    @click="removeFilter"
                                >
                                    mdi-delete
                                </v-icon>
                            </template>
                        </v-autocomplete>
                        <v-text-field
                            v-if="filterPanel"
                            v-model="filterName"
                            :color="themeBgColor"
                            :label="langMap.main.name"
                            class="ma-2"
                            dense
                            prepend-icon="mdi-text"
                        >
                        </v-text-field>
                    </v-col>
                    <v-col md="6" sm="12">
                        <v-btn :color="!filterPanel ? 'grey darken-1' : 'red darken-1'"
                               class="ma-2"
                               outlined
                               @click="filterPanel = !filterPanel">
                            {{ !filterPanel ? langMap.main.add : langMap.main.close }}
                        </v-btn>
                        <v-btn v-if="filterPanel"
                               class="ma-2"
                               color="green darken-1"
                               outlined
                               @click="saveFilter">
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-col>
                    <v-col md="2">
                        <v-checkbox
                            v-model="options.withSpam"
                            :color="themeBgColor"
                            class="ma-2"
                            hide-details
                            label="Spam only"

                        >
                        </v-checkbox>
                        <v-checkbox
                            v-model="options.onlyForUser"
                            :color="themeBgColor"
                            class="ma-2"
                            hide-details
                            label="Assigned for me Only"
                        >
                        </v-checkbox>
                    </v-col>

                </v-row>
                <v-expand-transition>
                    <v-row v-show="filterPanel">
                        <v-col v-for="filterParam in filterParams"
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
                                <v-card
                                    v-show="filterParam.active"
                                    elevation="5"
                                >
                                    <v-card-text style="padding: 5px 10px ;">
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
                <v-pagination v-model="options.page"
                              :color="themeBgColor"
                              :length="lastPage"
                              :page="options.page"
                              :total-visible="5"
                              circle
                >
                </v-pagination>
            </template>
            <template v-slot:item.status.name="{ item }">
                <v-badge :color="item.status.color" dot inline @click="showItem(item)">
                    {{ item.status.name }}
                </v-badge>
            </template>
            <template v-slot:item.priority.name="{ item }">
                <v-badge :color="item.priority.color" dot inline @click="showItem(item)">
                    {{ item.priority.name }}
                </v-badge>
            </template>
            <template v-slot:item.category.name="{ item }">
                {{ item.category ? item.category.name : '' }}
            </template>
            <template v-slot:item.product.name="{ item }">
                {{ item.product ? item.product.full_name : '' }}
            </template>
            <template v-slot:item.assigned_person="{ item }">
                <div v-if="item.assigned_person && item.assigned_person.user_data" class="justify-center"
                     @click="showItem(item)">
                    {{ item.assigned_person.user_data.full_name }}
                </div>
                <div v-else> {{ langMap.ticket.no_assigned }}</div>
            </template>
            <!--            <template v-slot:item.actions="{ item }">-->
            <!--            </template>-->
            <template v-slot:expanded-item="{ headers, item }">

                <td :colspan="headers.length">
                    <v-spacer>
                        &nbsp;
                    </v-spacer>
                    <h3>{{ item.number }}</h3>
                    <p><strong>{{ langMap.ticket.contact_name }}:</strong>
                        {{ item.contact && item.contact.user_data ? item.contact.user_data.full_name : '' }}</p>
                    <p><strong>{{ langMap.ticket.contact_email }}:</strong>
                        {{ item.contact && item.contact.user_data ? item.contact.user_data.email : '' }}</p>
                    <p><strong>{{ langMap.ticket.due_date }}:</strong> {{ item.due_date }}</p>
                    <p><strong>{{ langMap.ticket.access_details }}:</strong> {{ item.access_details }}</p>
                    <p><strong>{{ langMap.main.actions }}:</strong></p>
                    <p>
                        <v-btn
                            color="grey"
                            dark
                            fab
                            x-small
                            @click="showItem(item)"
                        >
                            <v-icon
                            >
                                mdi-eye
                            </v-icon>
                        </v-btn>

                        <v-tooltip top>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    :color="themeBgColor"
                                    dark
                                    fab
                                    x-small
                                    @click="mergeTicketProcess(item.id)"
                                >
                                    <v-icon dark>mdi-clipboard-flow</v-icon>
                                </v-btn>
                            </template>
                            <span>Link tickets</span>
                        </v-tooltip>
                        <v-btn
                            color="error"
                            dark
                            fab
                            x-small
                            @click="ticketDeleteProcess(item)"
                        >
                            <v-icon
                            >
                                mdi-delete
                            </v-icon>
                        </v-btn>
                    </p>
                </td>
            </template>
        </v-data-table>
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
                        <v-btn color="red darken-1" text @click="deleteTicket(selectedticketId)">
                            {{ langMap.main.delete }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
        <template>
            <v-dialog v-model="mergeTicketDialog" max-width="480" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
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
                        <v-btn :color="themeBgColor" darken-1 text @click="mergeTicketDialog = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn :color="themeBgColor" darken-1 text @click="mergeTicket()">
                            {{ langMap.main.link }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </template>
    </v-container>
</template>

<script>
import EventBus from "../../components/EventBus";

export default {
    data() {
        return {
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            clientId: 6,
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            expanded: [],
            singleExpand: false,
            totalTickets: 0,
            lastPage: 0,
            loading: this.themeBgColor,
            products: [],
            priorities: [],
            types: [],
            options: {
                page: 1,
                sortDesc: [true],
                sortBy: ['id'],
                withSpam: false,
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [10, 25, 50, 100],
            },
            filterParams: [
                {
                    active: false,
                    value: null,
                    selectedCompareParam: null,
                    query_fields: [],
                    compareParams: []
                }
            ],
            searchLabel: '',
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
            headers: [
                {text: '', value: 'data-table-expand'},
                // {
                //     text: 'ID',
                //     align: 'start',
                //     sortable: false,
                //     value: 'id',
                // },
                {text: `${this.$store.state.lang.lang_map.ticket.number}`, value: 'number'},
                {text: `${this.$store.state.lang.lang_map.ticket.status}`, value: 'status.name'},
                {text: `${this.$store.state.lang.lang_map.ticket.priority}`, value: 'priority.name'},
                {text: `${this.$store.state.lang.lang_map.main.category}`, value: 'category.name'},
                {text: `${this.$store.state.lang.lang_map.ticket.company_from}`, value: 'from.name'},
                {text: `${this.$store.state.lang.lang_map.ticket.contact_name}`, value: 'contact.user_data.full_name'},
                {text: `${this.$store.state.lang.lang_map.ticket.subject}`, value: 'name'},
                {text: `${this.$store.state.lang.lang_map.ticket.product_name}`, value: 'product.name'},
                {text: `${this.$store.state.lang.lang_map.team.members}`, value: 'assigned_person'},
                {text: `${this.$store.state.lang.lang_map.ticket.last_update}`, value: 'last_update'},
            ],
            mergeTicketForm: {
                parent_ticket_id: null,
                child_ticket_id: null,
                merge_comment: null
            },
            minifiedTickets: false,
            ticketsSearch: '',
            tickets: [],
            removeTicketDialog: false,
            mergeTicketDialog: false,
            selectedticketId: null,
            filterName: '',
            filterPanel: false,
            filters: [],
            queryArray: [],
            tempFilter: [],
            filterId: ''
        }
    },
    mounted() {
        if (!this.$helpers.auth.checkPermissionByIds([1])) {
            this.$router.push('knowledge_base')
        }
        let that = this;
        EventBus.$on('update-theme-fg-color', function (color) {
            that.themeFgColor = color;
        });
        EventBus.$on('update-theme-bg-color', function (color) {
            that.themeBgColor = color;
        });
        this.getTicketFilterParameters()
        this.getFilters()
        this.getProducts()
        this.getPriorities()
        this.getTypes()
    },
    methods: {
        manageFilter() {
            this.queryArray = [];
            this.filterParams.forEach(filterParam => {
                if (filterParam.active === true) {
                    filterParam.query_fields.forEach(field => {
                        if (filterParam.value && filterParam.selectedCompareParam) {
                            this.queryArray.push({
                                'field': field,
                                'compare_parameter': filterParam.selectedCompareParam,
                                'value': filterParam.value
                            })
                        }
                    });
                }
            });
            console.log(this.queryArray)
        },
        saveFilter() {
            let data = {
                'name': this.filterName,
                'filter_parameters': this.queryArray
            }
            axios.post('/api/ticket_query', data).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = 'Filter was created'
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.filterPanel = false
                    this.getFilters()
                } else {
                    this.snackbarMessage = 'Filter creating error'
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        removeFilter() {
            this.filterId = ''
            this.getTickets()
        },
        selectSearchCategory(item) {
            this.searchLabel = item.name
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
        getTicketFilterParameters() {
            axios.get('/api/ticket_filter_parameters').then(response => {
                response = response.data
                if (response.success === true) {
                    this.filterParams = response.data
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
        getTypes() {
            axios.get('/api/ticket_types').then(response => {
                response = response.data
                if (response.success === true) {
                    this.types = response.data
                } else {
                    console.log('error')
                }

            });
        },
        getTickets() {
            this.loading = this.themeBgColor
            if (this.options.sortDesc.length <= 0) {
                this.options.sortBy[0] = 'id'
                this.options.sortDesc[0] = true
            }
            if (this.totalTickets < this.options.itemsPerPage) {
                this.options.page = 1
            }
            let queryParams = new URLSearchParams({
                search_param: this.searchLabel,
                search: this.ticketsSearch,
                sort_by: this.manageSortableField(this.options.sortBy[0]),
                sort_val: this.options.sortDesc[0],
                with_spam: this.options.withSpam,
                only_for_user: this.options.onlyForUser,
                per_page: this.options.itemsPerPage,
                minified: this.minifiedTickets,
                page: this.options.page,
                filter_id: this.filterId
            });
            axios.get(`/api/ticket?${queryParams.toString()}`)
                .then(
                    response => {
                        response = response.data
                        if (response.success === true) {
                            this.tickets = response.data.data
                            this.totalTickets = response.data.total
                            this.lastPage = response.data.last_page
                        } else {
                            this.snackbarMessage = this.langMap.main.generic_error;
                            this.actionColor = 'error'
                            this.snackbar = true;
                        }
                        this.loading = false
                    });
        },
        getFilters() {
            axios.get(`/api/ticket_filters`)
                .then(
                    response => {
                        response = response.data
                        this.filters = response.data
                    });
        },
        showItem(item) {
            location.href = `/ticket/${item.id}`;
            // this.$router.push()
        },
        ticketDeleteProcess(item) {
            this.selectedticketId = item.id
            this.removeTicketDialog = true
        },
        deleteTicket(id) {
            axios.delete(`/api/ticket/${id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getTickets()
                    this.snackbarMessage = 'Ticket was deleted '
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.removeTicketDialog = false
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
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
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        updateItemsCount(value) {
            this.options.itemsPerPage = value
            localStorage.itemsPerPage = value;
            this.options.page = 1
        },
        manageSortableField(value) {
            if (value === 'last_update') return 'updated_at'
            if (value === 'status.name') return 'status_id'
            if (value === 'assigned_person') return 'to_company_user_id'
            if (value === 'product.name') return 'to_product_id'
            if (value === 'name') return 'name'
            if (value === 'from.name') return 'from_entity_id'
            if (value === 'to.name') return 'to_entity_id'
            if (value === 'priority.name') return 'priority_id'
            return value
        }
    },
    watch: {
        options: {
            handler: _.debounce(function (v) {
                this.getTickets()
            }, 1000),
            deep: true,
        },
    },
}
</script>

<style>
.v-data-table-header th {
    white-space: nowrap;
}
</style>
