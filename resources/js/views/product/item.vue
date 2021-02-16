<template>
    <v-container fluid>

        <div class="row">
            <v-snackbar
                :bottom="true"
                :right="true"
                v-model="snackbar"
                :color="actionColor"
            >
                {{ snackbarMessage }}
            </v-snackbar>
            <div class="col-md-6">
                <v-card class="elevation-12 without-bottom">
                    <v-toolbar
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.product.info}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-icon v-if="!enableToEdit" @click="enableToEdit = true">mdi-pencil</v-icon>
                        <v-btn v-if="enableToEdit" color="white" style="color: black; margin-right: 10px" @click="cancelUpdateProduct">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn v-if="enableToEdit" color="white" style="color: black;" @click="updateProduct">
                            {{langMap.main.update}}
                        </v-btn>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-select
                                :color="themeColor"
                                :label="langMap.main.category"
                                name="category_id"
                                prepend-icon="mdi-rename-box"
                                v-model="product.category_id"
                                :readonly="!enableToEdit"
                                :items="categories"
                                :item-color="themeColor"
                                item-value="id"
                                item-text="full_name"
                                dense
                            />

                            <v-text-field
                                :color="themeColor"
                                :label="langMap.main.name"
                                name="name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="product.product_name"
                                :error-messages="errors.product_name"
                                lazy-validation
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                            <v-textarea
                                rows="1"
                                auto-grow
                                :color="themeColor"
                                :label="langMap.main.description"
                                name="description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="product.product_description"
                                :error-messages="errors.product_description"
                                lazy-validation
                                :readonly="!enableToEdit"
                                dense
                            ></v-textarea>
                            <v-text-field
                                :color="themeColor"
                                :label="langMap.product.code"
                                name="code"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="product.product_code"
                                :error-messages="errors.product_code"
                                lazy-validation
                                :readonly="!enableToEdit"
                                dense
                            ></v-text-field>
                        </v-form>
                        <v-col v-if="product.attachments && product.attachments.length > 0 " cols="12">
                            <h4>{{ langMap.main.attachments }}</h4>
                            <div
                                v-for="attachment in product.attachments"
                                v-if="product.attachments.length > 0"
                            >
                                <v-chip
                                    :color="themeColor"
                                    :href="attachment.link"
                                    class="ma-2"
                                    text-color="white"
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
                        dense
                        :color="themeColor"
                        dark
                        flat
                    >
                        <v-toolbar-title>{{langMap.product.product_clients}}</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="product.clients"
                            :footer-props="footerProps"
                            class="elevation-1"
                            :options.sync="options"
                            @click:row="showCompany"
                            @update:options="updateItemsPerPage"
                        >
                            <template v-slot:item.actions="{ item }">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn v-bind="attrs" v-on="on" icon @click.native.stop="showDeleteCustomerDlg(item)">
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
                                    {{langMap.customer.add_new}}
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <v-col cols="md-12">
                                                <v-autocomplete
                                                    :color="themeColor"
                                                    :item-color="themeColor"
                                                    item-text="name"
                                                    item-value="id"
                                                    v-model="supplierForm.client_id"
                                                    :items="suppliers"
                                                    :label="langMap.customer.customer"
                                                ></v-autocomplete>
                                            </v-col>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                :color="themeColor"
                                                @click="addProductClient"
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
            </div>
        </div>

        <v-row justify="center">
            <v-dialog v-model="deleteCustomerDlg" persistent max-width="480">
                <v-card>
                    <v-card-title>{{langMap.product.unlink_product}}?</v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="deleteCustomerDlg = false">
                            {{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" text @click="deleteCustomerDlg = false; deleteProductClient(clientProductId)">
                            {{langMap.main.delete}}
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
                themeColor: this.$store.state.themeColor,
                snackbarMessage: '',
                enableToEdit: false,
                errors: [],
                langMap: this.$store.state.lang.lang_map,
                options: {
                    itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10,
                },
                footerProps: {
                    showFirstLastPage: true,
                    itemsPerPageOptions: [10, 25, 50, 100],
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
                    clients: []
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
                that.themeColor = color;
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
            updateProduct() {
                axios.patch(`/api/product/${this.$route.params.id}`, this.product).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.product.product_name = response.data.name
                        this.product.product_description = response.data.description
                        this.snackbarMessage = this.langMap.main.update_successful
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.enableToEdit = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error'
                        this.snackbar = true;
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
                axios.get('/api/client',{
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
                axios.post(`/api/product/client`, this.supplierForm).then(response => {
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
            showDeleteCustomerDlg(item)
            {
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
