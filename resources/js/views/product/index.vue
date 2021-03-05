<template>
    <v-container fluid>
        <v-snackbar
            :bottom="true"
            :right="true"
            v-model="snackbar"
            :color="actionColor"
        >
            {{ snackbarMessage }}
        </v-snackbar>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-expansion-panels>
                            <v-expansion-panel v-if="!this.$store.state.roles.includes(this.clientId)">
                                <v-expansion-panel-header>
                                    {{ langMap.product.add_new }}
                                    <template v-slot:actions>
                                        <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <v-select
                                                    :color="themeBgColor"
                                                    :label="langMap.main.category"
                                                    name="category_id"
                                                    prepend-icon="mdi-rename-box"
                                                    v-model="productForm.category_id"
                                                    :items="categories"
                                                    :item-color="themeBgColor"
                                                    item-value="id"
                                                    item-text="full_name"
                                                />
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    :color="themeBgColor"
                                                    :label="langMap.main.name"
                                                    name="product_name"
                                                    type="text"
                                                    v-model="productForm.product_name"
                                                    prepend-icon="mdi-text"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-textarea
                                                    :color="themeBgColor"
                                                    :label="langMap.main.description"
                                                    name="product_description"
                                                    v-model="productForm.product_description"
                                                    rows="1"
                                                    auto-grow
                                                    required
                                                ></v-textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <v-text-field
                                                    :color="themeBgColor"
                                                    :label="langMap.product.code"
                                                    name="product_code"
                                                    v-model="productForm.product_code"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-4">
                                                <v-file-input
                                                    :color="themeBgColor"
                                                    :item-color="themeBgColor"
                                                    :label="langMap.ticket.add_attachments"
                                                    :show-size="1000"
                                                    chips
                                                    multiple
                                                    prepend-icon="mdi-paperclip"
                                                    v-on:change="onFileChange('productForm')"
                                                >
                                                    <template v-slot:selection="{ index, text }">
                                                        <v-chip
                                                            :color="themeBgColor"
                                                        >
                                                            {{ text }}
                                                        </v-chip>
                                                    </template>
                                                </v-file-input>
                                            </div>
                                            <br>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                :color="themeBgColor"
                                                @click="addProduct"
                                            >
                                                <v-icon :color="themeBgColor" :style="`color: ${themeFgColor};`">mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-data-table
                            show-expand
                            :headers="headers"
                            :items="products"
                            :single-expand="singleExpand"
                            :expanded.sync="expanded"
                            :options.sync="options"
                            :server-items-length="totalProducts"
                            :loading="loading"
                            :footer-props="footerProps"
                            class="elevation-1"
                            hide-default-footer
                            :loading-text="langMap.main.loading"
                            @click:row="showItem"
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col sm="12" md="10">
                                        <v-text-field @input="getProducts" v-model="productsSearch" :color="themeBgColor"
                                                      :label="langMap.main.search" class="mx-4"></v-text-field>
                                    </v-col>
                                    <v-col sm="12" md="2">
                                        <v-select
                                            class="mx-4"
                                            :color="themeBgColor"
                                            :item-color="themeBgColor"
                                            :items="footerProps.itemsPerPageOptions"
                                            :label="langMap.main.items_per_page"
                                            v-model="options.itemsPerPage"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:footer>
                                <v-pagination :color="themeBgColor"
                                              v-model="options.page"
                                              :length="lastPage"
                                              circle
                                              :page="options.page"
                                              :total-visible="5"
                                >
                                </v-pagination>
                            </template>
                            <template v-slot:expanded-item="{ headers, item }">
                                <td :colspan="headers.length">
                                    <v-spacer>
                                        &nbsp;
                                    </v-spacer>
                                    <p><strong>{{langMap.main.actions}}:</strong></p>
                                    <p>
                                        <v-btn
                                            color="grey"
                                            dark
                                            @click="showItem(item)"
                                            fab
                                            x-small
                                        >
                                            <v-icon
                                            >
                                                mdi-eye
                                            </v-icon>
                                        </v-btn>

                                        <v-btn
                                            color="error"
                                            dark
                                            @click="deleteProcess(item)"
                                            fab
                                            x-small
                                        >
                                            <v-icon>
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </p>
                                </td>
                            </template>

                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
        <template>
            <v-dialog v-model="removeProductDialog" persistent max-width="480">
                <v-card>
                    <v-card-title class="mb-5" :style="`color: ${themeFgColor}; background-color: ${themeBgColor};`">
                        {{langMap.main.delete_selected}}?
                    </v-card-title>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey darken-1" text @click="removeProductDialog = false">{{langMap.main.cancel}}
                        </v-btn>
                        <v-btn color="red darken-1" disabled text @click="deleteProduct(selectedProductId)">
                            {{langMap.main.delete}}
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
                clientId: 6,
                snackbar: false,
                actionColor: '',
                snackbarMessage: '',
                totalProducts: 0,
                lastPage: 0,
                loading: this.themeBgColor,
                expanded: [],
                singleExpand: false,
                langMap: this.$store.state.lang.lang_map,
                themeFgColor: this.$store.state.themeFgColor,
themeBgColor: this.$store.state.themeBgColor,
                options: {
                    page: 1,
                    sortDesc: [false],
                    sortBy: ['id'],
                    itemsPerPage: localStorage.itemsPerPage ? parseInt(localStorage.itemsPerPage) : 10
                },
                footerProps: {
                    showFirstLastPage: true,
                    itemsPerPageOptions: [10, 25, 50, 100],
                },
                headers: [
                    {text: '', value: 'data-table-expand'},
                    {text: 'ID', align: 'start', sortable: false, value: 'id'},
                    {text: `${this.$store.state.lang.lang_map.product.code}`, value: 'product_code', sortable: false},
                    {text: `${this.$store.state.lang.lang_map.main.category}`, value: 'category.full_name', sortable: false, width: '25%'},
                    {text: `${this.$store.state.lang.lang_map.main.name}`, value: 'name', width: '20%'},
                    {text: `${this.$store.state.lang.lang_map.main.description}`, value: 'description', width: '30%'},
                ],
                productsSearch: '',
                products: [],
                removeProductDialog: false,
                selectedProductId: null,
                productForm: {
                    product_code: '',
                    category_id: '',
                    product_name: '',
                    product_description: '',
                    files: []
                },
                categories: []
            }
        },
        mounted() {
            this.getProducts();
            this.getCategories();

            let that = this;
            EventBus.$on('update-theme-color', function (color) {
                that.themeBgColor = color;
            });
        },
        methods: {
            getProducts() {
                this.loading = this.themeBgColor
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id'
                    this.options.sortDesc[0] = false
                }
                if (this.totalProducts < this.options.itemsPerPage) {
                    this.options.page = 1
                }
                axios.get('/api/product', {
                    params: {
                        search: this.productsSearch,
                        sort_by: this.options.sortBy[0],
                        sort_val: this.options.sortDesc[0],
                        per_page: this.options.itemsPerPage,
                        page: this.options.page
                    }
                }).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.products = response.data.data
                        this.totalProducts = response.data.total
                        this.lastPage = response.data.last_page
                        this.lastPage = response.data.last_page
                        this.loading = false
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
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
            onFileChange(form) {
                this[form].files = null;
                this[form].files = event.target.files;
            },
            addProduct() {
                const config = {
                    headers: {'content-type': 'multipart/form-data'}
                }
                let formData = new FormData();
                for (let key in this.productForm) {
                    if (key !== 'files') {
                        formData.append(key, this.productForm[key]);
                    }
                }
                Array.from(this.productForm.files).forEach(file => formData.append('files[]', file));
                axios.post('/api/product', formData, config).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getProducts();
                        this.snackbarMessage = this.langMap.product.product_created;
                        this.actionColor = 'success'
                        this.snackbar = true;
                    } else {
                        this.snackbarMessage = this.langMap.main.generic_error;
                        this.actionColor = 'error';
                        this.snackbar = true;
                    }
                });
            },
            showItem(item) {
                this.$router.push(`/product/${item.id}`)
            },
            deleteProcess(item) {
                this.selectedProductId = item.id
                this.removeProductDialog = true
            },
            deleteProduct(id) {
                axios.delete(`/api/product/${id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getProducts()
                        this.snackbarMessage = this.langMap.customer.product_deleted;
                        this.actionColor = 'success'
                        this.snackbar = true;
                        this.removeProductDialog = false
                    } else {
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
        },
        watch: {
            options: {
                handler() {
                    this.getProducts()
                },
                deep: true,
            },
        },
    }
</script>
