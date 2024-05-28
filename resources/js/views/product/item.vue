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
                        <v-icon :color="themeFgColor" @click="enableToEdit = true">mdi-pencil
                        </v-icon>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                v-model="product.category.name"
                                :color="themeBgColor"
                                :error-messages="errors.category_id"
                                :label="langMap.main.category"
                                prepend-icon="mdi-rename-box"
                                lazy-validation
                                type="text"
                                name="name"
                                readonly
                                dense
                            ></v-text-field>

                            <v-text-field
                                v-model="product.product_name"
                                :color="themeBgColor"
                                :error-messages="errors.product_name"
                                :label="langMap.main.name"
                                type="text"
                                name="name"
                                prepend-icon="mdi-rename-box"
                                readonly
                                dense
                                lazy-validation
                            ></v-text-field>
                            <v-textarea
                                v-model="product.product_description"
                                :color="themeBgColor"
                                :error-messages="errors.product_description"
                                :label="langMap.main.description"
                                type="text"
                                name="description"
                                prepend-icon="mdi-comment-text"
                                rows="1"
                                readonly
                                auto-grow
                                dense
                                lazy-validation
                            ></v-textarea>
                            <v-text-field
                                v-model="product.product_code"
                                :color="themeBgColor"
                                :error-messages="errors.product_code"
                                :label="langMap.product.code"
                                name="code"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                readonly
                                dense
                                lazy-validation
                            ></v-text-field>
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

        <v-dialog v-model="enableToEdit" max-width="800px" persistent>
            <v-card class="elevation-16">
                <v-toolbar
                    :color="themeBgColor"
                    class="mb-5"
                    dark
                    dense
                    flat
                >
                    <v-toolbar-title :style="`color: ${themeFgColor};`">{{ langMap.kb.category_details }}
                    </v-toolbar-title>
                </v-toolbar>
                <v-card-text>
                    <v-row>
                        <v-col cols="6">
                            <label>{{ langMap.kb.parent_category }}</label>
                            <v-expansion-panels class="mb-5">
                                <v-expansion-panel>
                                    <v-expansion-panel-header>
                                        {{ langMap.company.new_product_category }}
                                        <template v-slot:actions>
                                            <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">
                                                mdi-plus
                                            </v-icon>
                                        </template>
                                    </v-expansion-panel-header>
                                    <v-expansion-panel-content>
                                        <v-form>
                                            <div class="row pb-3">
                                                <v-col class="pa-1" cols="md-6">
                                                    <v-text-field
                                                        v-model="productCategoryForm.name"
                                                        :color="themeBgColor"
                                                        :item-color="themeBgColor"
                                                        :label="langMap.main.name"
                                                        dense
                                                    ></v-text-field>
                                                </v-col>
                                                <v-col class="pa-1" cols="6">
                                                    <v-select
                                                        v-model="productCategoryForm.parent_id"
                                                        :color="themeBgColor"
                                                        :item-color="themeBgColor"
                                                        :items="productCategoriesFlat"
                                                        :label="langMap.company.parent_product_category"
                                                        dense
                                                        item-text="full_name"
                                                        item-value="id"
                                                    >
                                                    </v-select>
                                                </v-col>
                                                <div style="width: 100%; display: flex; justify-content: flex-end;">
                                                    <v-btn
                                                        :color="themeBgColor"
                                                        v-text="langMap.main.add"
                                                        bottom
                                                        dark
                                                        right
                                                        small
                                                        @click="submitNewData($route.params.id, productCategoryForm, 'addProductCategory')"
                                                    />
                                                </div>
                                            </div>
                                        </v-form>
                                    </v-expansion-panel-content>
                                </v-expansion-panel>
                            </v-expansion-panels>
                            <perfect-scrollbar>
                                <v-treeview :active="product.category_id"
                                            :color="themeBgColor"
                                            :items="categories"
                                            activatable
                                            dense
                                            open-all
                                            @update:active="refreshCategoryForm">
                                    <template v-slot:prepend="{ item }">
                                        <v-icon>mdi-folder</v-icon>
                                    </template>
                                    <template v-slot:label="{ item }">
                                        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; gap: 12px;" @mouseenter="showPencilId = item.id"
                                             @mouseleave="showPencilId = 0">
                                            {{ $helpers.i18n.localized(item) }}
                                            <div>
                                                <v-btn v-if="showPencilId === item.id"
                                                       @click="isCanEditCategory = true; editableCategoryItem = item"
                                                       class="cursor-pointer" icon>
                                                    <v-icon>mdi-pencil</v-icon>
                                                </v-btn>
                                                <v-btn v-if="showPencilId === item.id"
                                                       @click="isCanRemoveCategory = true; removeCategoryItem = item"
                                                       class="cursor-pointer" icon>
                                                    <v-icon>mdi-delete</v-icon>
                                                </v-btn>
                                            </div>
                                        </div>
                                    </template>
                                </v-treeview>
                            </perfect-scrollbar>
                        </v-col>
                                                    <v-col cols="6">
                                                        <v-form>
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
                                                    </v-col>
                    </v-row>
                </v-card-text>
                <v-card-actions style="justify-content: flex-end">
                    <v-btn text @click="cancelUpdateProduct"
                           v-text="langMap.main.cancel"/>
                    <v-btn :color="themeBgColor" text
                           @click="updateProduct"
                           v-text="langMap.main.update"/>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isCanEditCategory" max-width="480" persistent>
            <v-card>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.company.edit_category }}: {{ editableCategoryItem.name }}
                </v-card-title>
                <v-card-text class="mt-6">
                    <v-text-field
                        v-model="editableCategoryItem.name"
                        :color="themeBgColor"
                        :label="langMap.main.name"
                        type="text"
                        dense
                    ></v-text-field>
                    <v-select
                        v-model="editableCategoryItem.parent_id"
                        :color="themeBgColor"
                        :item-color="themeBgColor"
                        :items="productCategoriesFlat"
                        :label="langMap.company.parent_product_category"
                        item-text="full_name"
                        item-value="id"
                        dense
                    >
                    </v-select>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="clearEditableCategory">
                        {{ langMap.main.cancel }}
                    </v-btn>
                    <v-btn color="red darken-1" text
                           @click="editCategory()">
                        {{ langMap.main.edit }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="isCanRemoveCategory" max-width="480" persistent>
            <v-card>
                <v-card-title :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`" class="mb-5">
                    {{ langMap.kb.delete_category }}
                </v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="grey darken-1" text @click="clearRemoveCategoryItem">
                        {{ langMap.main.cancel }}
                    </v-btn>
                    <v-btn color="red darken-1" text
                           @click="removeCategory()">
                        {{ langMap.main.delete }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

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
            categories: [],
            categoryForm: {
                type: this.$route.params.alias,
                id: null,
                parent_id: this.$route.query.category ?
                    this.$route.query.category :
                    (this.$route.query.parent_category ? this.$route.query.parent_category : null),
                name: '',
                name_de: '',
                description: '',
                description_de: '',
                icon: '',
                icon_color: this.$store.state.themeBgColor,
                _active: [],
                is_internal: 0,
                is_draft: 0
            },
            productCategoryForm: {
                name: '',
                company_id: '',
                parent_id: '',
            },
            productCategoriesFlat: [],
            productCategoriesTree: [],
            showPencilId: 0,
            editableCategoryItem: {
                name: '',
                parent_id: ''
            },
            removeCategoryItem: {
                name: '',
                parent_id: ''
            },
            isCanEditCategory: false,
            isCanRemoveCategory: false,
        }
    },
    mounted() {
        this.getProduct();
        this.getSuppliers();
        this.getCategories();
        this.getProductCategoriesFlat();
        this.getProductCategoriesTree();
        this.productCategoryForm.company_id = this.$route.params.id;
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
                if (key !== 'files' && key !== 'category_id') {
                    formData.append(key, this.product[key]);
                }
            }
            if (this.product.files) {
                Array.from(this.product.files).forEach(file => formData.append('files[]', file));
            }
            if(this.product.category) {
                formData.append('category_id', this.product.category.id);
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
            this.clearCategoryForm();
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
            axios.get(`/api/main_company/product_categories/tree`).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.productCategoriesTree = response.data;
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
        },
        refreshCategoryForm(parent) {
            this.categoryForm.parent_id = parent.length > 0 ? parent[0] : null;

            if (parent.length > 0) {
                const selectedId = parent[0];
                this.product.category = this.findCategoryById(this.categories, selectedId);
            } else {
                this.product.category = null;
            }

            this.$forceUpdate();
        },
        findCategoryById(categories, id) {
            for (const category of categories) {
                if (category.id === id) {
                    return category;
                }
                if (category.children) {
                    const found = this.findCategoryById(category.children, id);
                    if (found) {
                        return found;
                    }
                }
            }
            return null;
        },
        clearCategoryForm() {
            this.categoryForm = {
                id: null,
                type: this.$route.params.alias,
                parent_id: this.getCategoryIdFromQuery,
                name: '',
                name_de: '',
                description: '',
                description_de: '',
                icon: '',
                icon_color: this.themeBgColor,
                _active: []
            };
        },
        fillCategoryForm(category) {
            this.categoryForm = {
                id: category.id,
                type: this.$route.params.alias,
                parent_id: category.parent_id,
                name: category.name,
                name_de: category.name_de,
                description: category.description,
                description_de: category.description_de,
                icon_color: category.icon_color,
                icon: category.icon,
                _active: [category.parent_id],
                is_draft: category.is_draft,
                is_internal: category.is_internal
            };
            this.$forceUpdate();
        },
        addProductCategory() {
            this.snackbar = false;
            axios.post(`/api/company/${this.$route.params.id}/product_category`, this.productCategoryForm).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getProduct();
                    this.getCategories();
                    this.getProductCategoriesTree();
                    this.getProductCategoriesFlat();
                    this.productCategoryForm.parent_id = '';
                    this.productCategoryForm.name = '';
                    this.snackbarMessage = this.langMap.company.product_category_created;
                    this.actionColor = 'success';
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        getProductCategoriesFlat() {
            axios.get(`/api/main_company/product_categories/flat`).then(response => {
                response = response.data;
                if (response.success === true) {
                    console.log('success flat', response.data)
                    this.productCategoriesFlat = response.data;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        submitNewData(id, data, method) {
            data.entity_id = id
            this[method](data)
        },
        editCategory(){
            axios.patch(`/api/product_category/${this.editableCategoryItem.id}`, {
                name: this.editableCategoryItem.name,
                company_id: this.editableCategoryItem.company_id,
                parent_id: this.editableCategoryItem.parent_id,
            }).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getProduct();
                    this.getCategories();
                    this.clearEditableCategory();
                    this.snackbarMessage = this.langMap.company.product_category_edited;
                    this.actionColor = 'success';
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        removeCategory() {
            axios.delete(`/api/product_category/${this.removeCategoryItem.id}`, {
                name: this.removeCategoryItem.name,
                company_id: this.removeCategoryItem.company_id,
                parent_id: this.removeCategoryItem.parent_id,
            }).then(response => {
                response = response.data;
                if (response.success === true) {
                    this.getProduct();
                    this.getCategories();
                    this.clearRemoveCategoryItem();
                    this.snackbarMessage = this.langMap.company.product_category_deleted;
                    this.actionColor = 'success';
                    this.snackbar = true;
                } else {
                    this.snackbarMessage = this.langMap.main.generic_error;
                    this.actionColor = 'error';
                    this.snackbar = true;
                }
            });
        },
        clearEditableCategory() {
            this.isCanEditCategory = false;
            this.editableCategoryItem = {
                name: '',
                category_id: ''
            }
        },
        clearRemoveCategoryItem() {
            this.isCanRemoveCategory = false;
            this.removeCategoryItem = {
                name: '',
                category_id: ''
            }
        },
    },
}
</script>
