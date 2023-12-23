<template>
    <v-container fluid>

        <div class="row">
            <v-snackbar
                v-model="snackbar"
                :bottom="true"
                :color="actionColor"
                :right="true"
            >
                {{ snackbarMessage }}
            </v-snackbar>
            <div class="col-md-6">
                <v-card class="elevation-12 without-bottom">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.product.info }}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" :color="themeFgColor" @click="enableToEdit = true">mdi-pencil
                        </v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px"
                               @click="cancelUpdateProduct">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateProduct">
                            {{ langMap.main.update }}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-select
                                v-model="product.category_id"
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                :items="categories"
                                :label="langMap.main.category"
                                :readonly="!enableToEdit"
                                dense
                                item-text="full_name"
                                item-value="id"
                                name="category_id"
                                prepend-icon="mdi-rename-box"
                            />

                            <v-text-field
                                v-model="product.product_name"
                                :color="themeBgColor"
                                :error-messages="errors.product_name"
                                :label="langMap.main.name"
                                :readonly="!enableToEdit"
                                dense
                                lazy-validation
                                name="name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                            ></v-text-field>
                            <v-textarea
                                v-model="product.product_description"
                                :color="themeBgColor"
                                :error-messages="errors.product_description"
                                :label="langMap.main.description"
                                :readonly="!enableToEdit"
                                auto-grow
                                dense
                                lazy-validation
                                name="description"
                                prepend-icon="mdi-comment-text"
                                rows="1"
                                type="text"
                            ></v-textarea>
                            <v-text-field
                                v-model="product.product_code"
                                :color="themeBgColor"
                                :error-messages="errors.product_code"
                                :label="langMap.product.code"
                                :readonly="!enableToEdit"
                                dense
                                lazy-validation
                                name="code"
                                prepend-icon="mdi-rename-box"
                                type="text"
                            ></v-text-field>
                            <v-file-input
                                v-show="enableToEdit"
                                :color="themeBgColor"
                                :item-color="themeBgColor"
                                :label="langMap.ticket.add_attachments"
                                :show-size="1000"
                                chips
                                multiple
                                prepend-icon="mdi-paperclip"
                                v-on:change="onFileChange('product')"
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
                        </v-form>
                        <v-col v-if="product.attachments && product.attachments.length > 0 " cols="12">
                            <h4>{{ langMap.main.attachments }}</h4>
                            <div
                                v-for="attachment in product.attachments"
                                v-if="product.attachments.length > 0"
                            >
                                <v-chip
                                    :close="enableToEdit"
                                    :color="themeBgColor"
                                    :href="attachment.link"
                                    class="ma-2"
                                    text-color="white"
                                    @click:close="removeAttachment(attachment.id)"
                                >
                                    {{ attachment.name }}
                                </v-chip>
                            </div>
                        </v-col>
                    </v-card-text>
                </v-card>
            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        :color="themeBgColor"
                        dark
                        dense
                        flat
                    >
                        <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.product.product_clients }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :footer-props="footerProps"
                            :headers="headers"
                            :items="product.clients"
                            :options.sync="options"
                            class="elevation-1"
                            @click:row="showCompany"
                            @update:options="updateItemsPerPage"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon
                                               @click.native.stop="showDeleteCustomerDlg(item)">
                                            <v-icon
                                                small
                                            >
                                                mdi-link-off
                                            </v-icon>
                                        </v-btn>
                                    </template>
                                    <span>{{ langMap.product.unlink_product }}</span>
                                </v-tooltip>
                            </template>

                        </v-data-table>
                        <v-spacer>
                            &nbsp;
                        </v-spacer>
                        <v-expansion-panels>
                            <v-expansion-panel>
                                <v-expansion-panel-header>
                                    {{ langMap.customer.add_new }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus
                                        </v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="md-12">
                                                <v-autocomplete
                                                    v-model="supplierForm.client_id"
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :items="suppliers"
                                                    :label="langMap.customer.customer"
                                                    item-text="name"
                                                    item-value="id"
                                                ></v-autocomplete>
                                            </v-col>
                                            <v-btn
                                                :color="themeBgColor"
                                                bottom
                                                dark
                                                fab
                                                right
                                                @click="addProductClient"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                    mdi-plus
                                                </v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                    </v-card-text>
                </v-card>
            </div>
        </div>

        <v-row justify="center">
            <v-dialog v-model="deleteCustomerDlg" max-width="480" persistent>
                <v-card>
                    <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                        {{ langMap.product.unlink_product }}?
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="deleteCustomerDlg = false">
                            {{ langMap.main.cancel }}
                        </v-btn>
                        <v-btn color="red darken-1" text
                               @click="deleteCustomerDlg = false; deleteProductClient(clientProductId)">
                            {{ langMap.main.delete }}
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
            snackbar: false,
            actionColor: '',
            themeFgColor: this.$store.state.themeFgColor,
            themeBgColor: this.$store.state.themeBgColor,
            snackbarMessage: '',
            enableToEdit: false,
            errors: [],
            langMap: this.$store.state.lang.lang_map,
            options: {
                itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10,
            },
            footerProps: {
                showFirstLastPage: true,
                itemsPerPageOptions: [
                    {
                        'text': 10,
                        'value': 10,
                    },
                    {
                        'text': 20,
                        'value': 20,
                    },
                    {
                        'text': 50,
                        'value': 50,
                    },
                    {
                        'text': 100,
                        'value': 100,
                    },
                    {
                        'text': this.$store.state.lang.lang_map.sidebar.all,
                        'value': 10000,
                    }
                ],
            },
            headers: [
                {
                    text: 'ID',
                    align: 'start',
                    sortable: false,
                    value: 'client_data.id',
                },
                {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'client_data.name'},
                {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'client_data.description'},
                {text: `${this.$store.state.lang.lang_map.main.actions}`, value: 'actions', sortable: false},
            ],
            product: {
                id: '',
                category_id: '',
                product_code: '',
                product_name: '',
                product_description: '',
                clients: [],
                files: []
            },
            suppliers: [],
            supplierForm: {
                client_id: null,
                product_id: null
            },
            deleteCustomerDlg: false,
            clientProductId: null,
            categories: []
        }
    },
    mounted() {
        this.getProduct();
        this.getSuppliers();
        this.getCategories();
        let that = this;
        EventBus.$on('update-theme-color', function (color) {
            that.themeBgColor = color;
        });
    },
    methods: {
        getProduct() {
            axios.get(`/api/product/${this.$route.params.id}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.product = response.data
                    this.supplierForm.product_id = this.product.id = response.data.id
                    this.product.product_name = response.data.name
                    this.product.product_description = response.data.description
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }

            });
        },
        onFileChange(form) {
            this[form].files = null;
            this[form].files = event.target.files;
        },
        updateProduct() {
            const config = {
                headers: {'content-type': 'multipart/form-data'}
            }
            let formData = new FormData();
            for (let key in this.product) {
                if (key !== 'files') {
                    formData.append(key, this.product[key]);
                }
            }
            if (this.product.files) {
                Array.from(this.product.files).forEach(file => formData.append('files[]', file));
            }
            axios.post(`/api/product/${this.$route.params.id}`, formData, config).then(response => {
                response = response.data
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.main.update_successful
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.enableToEdit = false
                    this.getProduct()
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }

            });
        },
        removeAttachment(id) {
            axios.delete(`/api/file/${id}`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.snackbarMessage = this.langMap.kb.attachment_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                    this.getProduct();
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.errorType = 'error';
                    this.alert = true;
                }
            });
        },

        cancelUpdateProduct() {
            this.getProduct();
            this.enableToEdit = false;
        },
        showCompany(item) {
            this.$router.push(`/customer/${item.client_id}`)
        },
        getSuppliers() {
            axios.get('/api/client', {
                params: {
                    sort_by: 'name',
                    sort_val: false
                }
            }).then(response => {
                response = response.data
                if (response.success === true) {
                    this.suppliers = response.data.data
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        getCategories() {
            axios.get(`/api/main_company/product_categories/flat`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.categories = [{
                        id: null,
                        name: this.langMap.main.none,
                        full_name: this.langMap.main.none,
                        parent_id: null
                    }].concat(response.data);
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        addProductClient() {
            axios.post(`/api/product/${this.supplierForm.product_id}/client`, this.supplierForm).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getProduct()
                    this.snackbarMessage = this.langMap.customer.product_added;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        showDeleteCustomerDlg(item) {
            this.clientProductId = item.id;
            this.deleteCustomerDlg = true;
        },
        deleteProductClient(productId) {
            axios.delete(`/api/product/client/${productId}`).then(response => {
                response = response.data
                if (response.success === true) {
                    this.getProduct()
                    this.clientProductId = null;
                    this.snackbarMessage = this.langMap.customer.product_deleted;
                    this.actionColor = 'success'
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error'
                    this.snackbar = true;
                }
            });
        },
        updateItemsPerPage(options) {
            localStorage.itemsPerPage = options.itemsPerPage;
        }

    }
}
</script>
