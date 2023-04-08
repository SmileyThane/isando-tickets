<template>
    <v-container fluid>
        <v-overlay :value="overlay">
            <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
        <v-snackbar
            v-model="snackbar"
            :bottom="true"
            :color="actionColor"
            :right="true"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <v-row>

            <v-container>
                <v-row justify="space-around">
                    <v-col cols="12">
                        <p class="title text-center">{{ langMap.ticket.create_ticket }}</p>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col lg="10" md="12" offset-lg="1" offset-xl="2" sm="12" xl="8">
                        <v-card class="pa-2">
                            <v-card-text>
                                <div class="row">
                                    <div class="col-md-4 col-sm-12">
                                        <v-autocomplete
                                            v-model="ticketForm.from"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="suppliers"
                                            hide-details
                                            item-text="name"
                                            item-value="item"
                                            label="Ticket is submitted by"
                                            @input="getContacts"
                                        />
                                    </div>
                                    <v-col class="col-md-4 col-sm-12">
                                        <v-autocomplete
                                            v-model="ticketForm.contact_company_user_id"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="employees"
                                            hide-details
                                            item-text="user_data.full_name"
                                            item-value="id"
                                            label="Contact person"
                                        >
                                            <template v-slot:append-outer>
                                                <v-btn :color="themeBgColor"
                                                       :disabled="Object.keys(ticketForm.from)[0] === 'App\\Company'" :title="langMap.individuals.add_new" icon
                                                       @click="createContactDlg = true;">
                                                    <v-icon>mdi-plus</v-icon>
                                                </v-btn>
                                            </template>
                                        </v-autocomplete>
                                    </v-col>
                                    <v-col class="col-md-4 mb-4">
                                        <v-autocomplete
                                            v-model="ticketForm.to"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="suppliers"
                                            :label="langMap.ticket.company_to"
                                            hide-details
                                            item-text="name"
                                            item-value="item"
                                        ></v-autocomplete>

                                    </v-col>
                                </div>
                                <v-expansion-panels multiple>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header style="background: rgb(240, 240, 240);">
                                            Ticket details:
                                        </v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <v-container>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <v-text-field
                                                            v-model="ticketForm.name"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :label="langMap.ticket.subject"
                                                            hide-details
                                                        ></v-text-field>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <v-select
                                                            v-model="ticketForm.priority_id"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :items="priorities"
                                                            :label="langMap.ticket.priority"
                                                            item-text="name"
                                                            item-value="id"
                                                        >
                                                        </v-select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <v-select
                                                            v-model="ticketForm.ticket_type_id"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :items="types"
                                                            :label="langMap.main.type"
                                                            item-text="name"
                                                            item-value="id"
                                                        >
                                                        </v-select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <v-select
                                                            v-model="ticketForm.category_id"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :items="categories"
                                                            :label="langMap.main.category"
                                                            item-text="name"
                                                            item-value="id"
                                                        />
                                                    </div>
                                                    <div class="col-md-12">
                                                        <v-select
                                                            v-model="ticketForm.to_product_id"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :items="products"
                                                            :label="langMap.ticket.product_name"
                                                            item-text="full_name"
                                                            item-value="id"
                                                        />
                                                    </div>
                                                    <div class="col-md-12">
                                                        <v-textarea
                                                            v-model="ticketForm.description"
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :label="langMap.main.description"
                                                            auto-grow
                                                            outlined
                                                            row-height="25"
                                                            rows="3"
                                                        ></v-textarea>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <v-label>{{ langMap.ticket.access_data }}:</v-label>
                                                    </div>
                                                    <v-col cols="md-12">
                                                        <v-file-input
                                                            :color="themeBgColor"
                                                            :item-color="themeBgColor"
                                                            :label="langMap.main.attachments"
                                                            :show-size="1000"
                                                            chips
                                                            multiple
                                                            prepend-icon="mdi-paperclip"
                                                            v-on:change="onFileChange('ticketForm')"
                                                        >
                                                            <template v-slot:selection="{ index, text }">
                                                                <v-chip
                                                                    :color="themeBgColor"
                                                                    :text-color="themeFgColor"
                                                                    class="ma-2"
                                                                >
                                                                    {{ text }}
                                                                </v-chip>
                                                            </template>
                                                        </v-file-input>
                                                    </v-col>
                                                </div>
                                            </v-container>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                    <v-expansion-panel>
                                        <v-expansion-panel-header style="background: rgb(240, 240, 240);">
                                            Callback and remote access details:
                                        </v-expansion-panel-header>
                                        <v-expansion-panel-content>
                                            <div class="row mt-2">
                                                <div class="col-md-6">
                                                    <v-tooltip bottom>
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-textarea
                                                                v-model="ticketForm.connection_details"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.ticket.ip_address"
                                                                auto-grow
                                                                outlined
                                                                row-height="25"
                                                                rows="3"
                                                                v-bind="attrs"
                                                                v-on="on"
                                                            ></v-textarea>
                                                        </template>
                                                        <span>{{ langMap.ticket.ip_description }}</span>
                                                    </v-tooltip>
                                                </div>
                                                <div class="col-md-6">
                                                    <v-tooltip bottom>
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-textarea
                                                                v-model="ticketForm.access_details"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.ticket.access_details"
                                                                auto-grow
                                                                outlined
                                                                row-height="25"
                                                                rows="3"
                                                                v-bind="attrs"
                                                                v-on="on"
                                                            ></v-textarea>
                                                        </template>
                                                        <span>{{ langMap.ticket.access_description }}</span>
                                                    </v-tooltip>
                                                </div>
                                                <v-col cols="12">
                                                    <v-tooltip v-model="availabilityTooltip" bottom>
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-textarea
                                                                v-model="ticketForm.availability"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                auto-grow
                                                                label="Availability of the contact person for callback – e.g. add working hours…"
                                                                outlined
                                                                row-height="25"
                                                                rows="1"
                                                                v-bind="attrs"
                                                                v-on="on"
                                                            ></v-textarea>
                                                        </template>
                                                        <span>{{ langMap.ticket.availability_description }}</span>
                                                    </v-tooltip>
                                                </v-col>
                                            </div>
                                        </v-expansion-panel-content>
                                    </v-expansion-panel>
                                </v-expansion-panels>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn
                                    color="#4caf50"
                                    style="color: white;"
                                    @click="submit()"
                                    v-text="langMap.main.create"
                                >
                                </v-btn>

                                <v-btn
                                    class="ml-2"
                                    color="#4caf50"
                                    style="color: white;"
                                    @click="assignDlg = true"
                                    v-text="langMap.ticket.create_and_assign"
                                />

                                <v-btn
                                    class="ml-2"
                                    color="#4caf50"
                                    style="color: white;"
                                    @click="savetoLS()"
                                >
                                    Save for later
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>

        </v-row>

        <v-row justify="center">
            <v-dialog v-model="createContactDlg" max-width="600px" persistent>
                <v-card dense outlined>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.individuals.add_new }}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12">
                                    {{ langMap.ticket.company_from }}:
                                    {{ getSupplierName() }}
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="createContactForm.description"
                                        :color="themeBgColor"
                                        :label="langMap.main.description"
                                        name="description"
                                        type="text"
                                    />
                                </v-col>
                                <v-col cols="4">
                                    <v-text-field
                                        v-model="createContactForm.name"
                                        :color="themeBgColor"
                                        :label="langMap.main.name"
                                        name="name"
                                        required
                                        type="text"
                                    />
                                </v-col>
                                <v-col cols="4">
                                    <v-text-field
                                        v-model="createContactForm.middle_name"
                                        :color="themeBgColor"
                                        :label="langMap.main.middle_name"
                                        name="name"
                                        required
                                        type="text"
                                    />
                                </v-col>
                                <v-col cols="4">
                                    <v-text-field
                                        v-model="createContactForm.surname"
                                        :color="themeBgColor"
                                        :label="langMap.main.last_name"
                                        name="name"
                                        required
                                        type="text"
                                    />
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field
                                        v-model="createContactForm.email"
                                        :color="themeBgColor"
                                        :label="langMap.main.email"
                                        name="email"
                                        prepend-icon="mdi-mail"
                                        required
                                        type="text"
                                    />
                                </v-col>
                                <v-col cols="6">
                                    <p v-for="(item, i) in createContactForm.phones" :key="item.id" class="mb-2">
                                        <v-icon v-if="item.type" :title="$helpers.i18n.localized(item.type)"
                                                dense left small v-text="item.type.icon"/>
                                        {{ item.phone }}

                                        <v-icon :color="themeBgColor" :title="langMap.main.delete" right small
                                                @click="deletePhone(i)">mdi-trash-can
                                        </v-icon>
                                    </p>

                                    <v-expansion-panels accordion>
                                        <v-expansion-panel>
                                            <v-expansion-panel-header>
                                                {{ langMap.main.new_phone }}
                                                <template v-slot:actions>
                                                    <v-icon :color="themeBgColor">mdi-plus</v-icon>
                                                </template>
                                            </v-expansion-panel-header>
                                            <v-expansion-panel-content>
                                                <v-form>
                                                    <v-row>
                                                        <v-col cols="12">
                                                            <v-text-field
                                                                v-model="phoneForm.phone"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :label="langMap.main.phone"
                                                                dense
                                                            />
                                                            <v-select
                                                                v-model="phoneForm.phone_type"
                                                                :color="themeBgColor"
                                                                :item-color="themeBgColor"
                                                                :items="phoneTypes"
                                                                :label="langMap.main.type"
                                                                dense
                                                                item-value="id"
                                                            >
                                                                <template slot="selection" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ $helpers.i18n.localized(data.item) }}
                                                                </template>
                                                                <template slot="item" slot-scope="data">
                                                                    <v-icon left small v-text="data.item.icon"></v-icon>
                                                                    {{ $helpers.i18n.localized(data.item) }}
                                                                </template>
                                                            </v-select>
                                                        </v-col>
                                                        <v-btn
                                                            :color="themeBgColor"
                                                            bottom
                                                            dark
                                                            fab
                                                            right
                                                            small
                                                            @click="addPhone"
                                                        >
                                                            <v-icon :color="themeBgColor"
                                                                    :style="`color: ${themeFgColor};`">mdi-plus
                                                            </v-icon>
                                                        </v-btn>
                                                    </v-row>
                                                </v-form>
                                            </v-expansion-panel-content>
                                        </v-expansion-panel>
                                    </v-expansion-panels>
                                </v-col>
                                <v-col cols="6">
                                    <v-autocomplete
                                        v-model="createContactForm.language_id"
                                        :color="themeBgColor"
                                        :item-color="themeBgColor"
                                        :items="languages"
                                        :label="langMap.main.language"
                                        item-text="name"
                                        item-value="id"
                                        name="language"
                                        prepend-icon="mdi-web"
                                    />
                                </v-col>
                                <v-col cols="6">
                                    <v-checkbox
                                        v-model="createContactForm.is_active"
                                        :disabled="$helpers.auth.checkPermissionByIds(['36,37'])"
                                        :label="langMap.main.give_access"
                                        color="success"
                                        hide-details
                                    />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="red" text @click="createContactDlg=false">{{ langMap.main.cancel }}</v-btn>
                        <v-btn :color="themeBgColor" text @click="addEmployee()">
                            {{ langMap.main.save }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="assignDlg" max-width="600px" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.ticket.assign_to }}
                    </v-card-title>
                    <v-card-text>
                        <v-form>
                            <v-autocomplete
                                v-model="ticketForm.to_team_id"
                                :color="themeBgColor"
                                :disabled="selectionDisabled"
                                :item-color="themeBgColor"
                                :items="tTeams"
                                :label="langMap.sidebar.team"
                                dense
                                item-text="name"
                                item-value="id"
                                @change="selectTeam(); ticketForm.to_company_user_id = null;"
                            />
                            <v-autocomplete
                                v-model="ticketForm.to_company_user_id"
                                :color="themeBgColor"
                                :disabled="selectionDisabled"
                                :item-color="themeBgColor"
                                :items="tEmployees"
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
                                   v-text="langMap.ticket.create_and_assign"
                                   @click.native.stop="assignDlg = false; submit()"
                            />
                            <v-btn :color="themeBgColor"
                                   :disabled="!ticketForm.to_company_user_id || selectionDisabled || $helpers.auth.checkPermissionByIds([36])"
                                   class="ma-2"
                                   color="grey"
                                   small
                                   style="color: white;"
                                   v-text="langMap.ticket.clear_agent"
                                   @click.native.stop="ticketForm.to_company_user_id = null"
                            />
                            <v-btn class="ma-2"
                                   color="white" small
                                   style="color: black;"
                                   @click="assignDlg = false; ticketForm.to_team_id = null; ticketForm.to_company_user_id= null;"
                                   v-text="langMap.main.cancel"
                            />
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-dialog>
            <v-dialog v-model="saveForLaterDialog" max-width="580" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.ticket.save_for_later }}
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="saveForLaterFlag = false; saveForLater()">
                            {{ langMap.main.no }}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="saveForLaterFlag = true; saveForLater()">
                            {{ langMap.main.yes }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-row>

    </v-container>
</template>
<script>
import EventBus from "../../components/EventBus";

export default {
    data() {
        return {
            clientId: 6,
            overlay: false,
            snackbar: false,
            actionColor: '',
            snackbarMessage: '',
            availabilityTooltip: false,
            e1: 1,
            steps: 2,
            vertical: false,
            altLabels: true,
            editable: true,
            langMap: this.$store.state.lang.lang_map,
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            ticketForm: {
                from: {
                    name: ''
                },
                from_entity_type: '',
                from_entity_id: '',
                to: '',
                to_entity_type: '',
                to_entity_id: '',
                contact_company_user_id: '',
                to_product_id: '',
                priority_id: '',
                category_id: '',
                ticket_type_id: '',
                name: '',
                description: '',
                availability: '',
                connection_details: '',
                access_details: '',
                files: [],
                to_team_id: null,
                to_company_user_id: null,
                can_be_edited: true
            },
            suppliers: [],
            products: [],
            priorities: [],
            categories: [],
            types: [],
            employees: [],
            onFileChange(form) {
                this[form].files = null;
                this[form].files = event.target.files;
            },
            createContactDlg: false,
            createContactForm: {
                client_id: '',
                name: '',
                middle_name: '',
                surname: '',
                language_id: '',
                email: '',
                is_active: 0,
                description: '',
                phones: []
            },
            phoneForm: {
                phone: '',
                phone_type: '',
            },
            phoneTypes: [],
            languages: [],
            assignDlg: false,
            selectionDisabled: false,
            tTeams: [],
            tEmployees: [],
            ticketFormIsSaved: false,
            saveForLaterDialog: false,
            saveForLaterFlag: false,
            canBeRedirected: false,
            redirectRoute: ''
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
        ticketForm() {
            this.ticketFormIsSaved = false;
        }
    },
    beforeRouteLeave(to, from, next) {
        this.redirectRoute = to
        this.initSaveForLater()
        if (this.canBeRedirected) {
            next()
        }
    },
    mounted() {
        this.getSuppliers()
        this.getProducts()
        this.getPriorities()
        this.getTypes()
        this.getCategories()
        this.getLanguages()
        // this.getCompany()
        this.getPhoneTypes()
        this.getTeams()

        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeBgColor = color;
        });
        this.loadSavedForm();
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
                    this.ticketForm.from = this.$store.state.roles.includes(this.clientId) ? this.suppliers[1].item : this.suppliers[0].item;
                    this.ticketForm.to = this.suppliers[0].item
                    this.getContacts(this.ticketForm.from)
                }
            });
        },
        getProducts() {
            axios.get('/api/product', {
                params: {
                    sort_by: 'full_name',
                    sort_val: false
                }
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.products = response.data.data
                    this.ticketForm.to_product_id = this.products[0].id
                }

            });
        },
        getPriorities() {
            axios.get('/api/ticket_priorities').then(response => {
                response = response.data
                if (response.success === true) {
                    this.priorities = response.data
                    this.ticketForm.priority_id = this.priorities[1].id
                }

            });
        },
        getTypes() {
            axios.get('/api/ticket_types').then(response => {
                response = response.data
                if (response.success === true) {
                    this.types = response.data
                }

            });
        },
        getCategories() {
            axios.get('/api/ticket_categories').then(response => {
                response = response.data
                if (response.success === true) {
                    this.categories = response.data
                }

            });
        },
        getContacts(entityItem) {
            this.employees = []
            let route = '';

            if (Object.keys(entityItem)[0] === 'App\\Company') {
                route = `/api/company/${Object.values(entityItem)[0]}`
                this.createContactForm.client_id = '';
            } else {
                route = `/api/client/${Object.values(entityItem)[0]}`
                this.createContactForm.client_id = Object.values(entityItem)[0];
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
                    // this.ticketForm.contact_company_user_id = this.employees[0].id
                } else {
                }

            });
        },
        submit() {
            if (!this.ticketForm.name) {
                return false;
            }
            this.overlay = true;
            this.ticketForm.from_entity_type = Object.keys(this.ticketForm.from)[0]
            this.ticketForm.from_entity_id = Object.values(this.ticketForm.from)[0]
            this.ticketForm.to_entity_type = Object.keys(this.ticketForm.to)[0]
            this.ticketForm.to_entity_id = Object.values(this.ticketForm.to)[0]
            this.addTicket()
        },
        addTicket() {
            this.saveForLater();
            const config = {
                headers: {'content-type': 'multipart/form-data'}
            }
            let formData = new FormData();
            for (let key in this.ticketForm) {
                switch (key) {
                    case 'to_team_id':
                    case 'to_company_user_id':
                        if (this.ticketForm[key]) {
                            formData.append(key, this.ticketForm[key]);
                        }
                        break;
                    case 'files':
                        break;
                    default:
                        formData.append(key, this.ticketForm[key]);
                }
            }

            Array.from(this.ticketForm.files).forEach(file => formData.append('files[]', file));
            axios.post('/api/ticket', formData, config).then(response => {
                response = response.data
                if (response.success === true) {
                    this.ticketForm = {
                        from: {
                            name: ''
                        },
                        from_entity_type: '',
                        from_entity_id: '',
                        to: '',
                        to_entity_type: '',
                        to_entity_id: '',
                        contact_company_user_id: '',
                        to_product_id: '',
                        priority_id: '',
                        category_id: '',
                        ticket_type_id: '',
                        name: '',
                        description: '',
                        availability: '',
                        connection_details: '',
                        access_details: '',
                        files: [],
                        to_team_id: null,
                        to_company_user_id: null,
                        can_be_edited: true
                    }
                    window.open('/ticket/' + response.data.id, '_self')
                } else {
                    this.overlay = false;
                    this.snackbarMessage = 'Check ticket problems and try again, please!'
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        addEmployee() {
            axios.post(`/api/client/employee`, this.createContactForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.createContactDlg = false;
                    this.ticketForm.contact_company_user_id = response.data.company_user_id;
                    this.getContacts(this.ticketForm.from);
                    this.createContactForm = {
                        client_id: '',
                        name: '',
                        middle_name: '',
                        surname: '',
                        language_id: '',
                        email: '',
                        is_active: 0,
                        phones: [],
                        description: ''
                    };
                    this.snackbarMessage = this.langMap.company.employee_created;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }

            });
        },
        getLanguages() {
            axios.get('/api/lang').then(response => {
                response = response.data
                if (response.success === true) {
                    this.languages = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getSupplierName() {
            let index = this.suppliers.findIndex(supplier => supplier.item === this.ticketForm.from);
            return index > -1 ? this.suppliers[index].name : '';
        },
        addPhone() {
            if (this.phoneForm.phone_type) {
                this.phoneForm.type = this.phoneTypes.find(x => x.id === this.phoneForm.phone_type);
            }
            this.createContactForm.phones.push(this.phoneForm);
            this.phoneForm = {
                phone: '',
                phone_type: '',
                type: null
            }
            this.$forceUpdate();
        },
        deletePhone(index) {
            this.createContactForm.phones.splice(index, 1);
        },
        getPhoneTypes() {
            axios.get(`/api/phone_types`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.phoneTypes = response.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getTeams() {
            axios.get(`/api/team`
            ).then(response => {
                response = response.data
                if (response.success === true) {
                    this.tTeams = response.data.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        selectTeam() {
            if (this.ticketForm.can_be_edited === false) {
                this.selectionDisabled = true
            }
            if (this.ticketForm.to_team_id !== null) {
                this.assignPanel = [0];
                axios.get(`/api/team/${this.ticketForm.to_team_id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.tEmployees = response.data.employees
                    }
                });
            }
        },
        initSaveForLater() {
            if (this.ticketFormIsSaved === false) {
                this.saveForLaterDialog = true
            } else {
                this.canBeRedirected = true
            }
        },
        saveForLater() {
            if (this.ticketFormIsSaved === false) {
                if (this.saveForLaterFlag) {
                    this.savetoLS();
                } else {
                    localStorage.removeItem('ticketForm');
                    this.ticketFormIsSaved = false;
                }
            }
            this.canBeRedirected = true
            this.$router.push(this.redirectRoute)
        },
        savetoLS() {
            localStorage.setItem('ticketForm', JSON.stringify(this.ticketForm));
            this.ticketFormIsSaved = true;
            this.snackbarMessage = 'Form saved!'
            this.actionColor = 'success'
            this.snackbar = true;
        },
        loadSavedForm() {
            try {
                const json = localStorage.getItem('ticketForm');
                if (json) {
                    this.ticketForm = JSON.parse(json);
                    this.snackbarMessage = 'The saved form is loaded!'
                    this.actionColor = 'success'
                    this.snackbar = true;
                }
            } catch (e) {
                console.log(e);
            }
        },
    }
}
</script>
