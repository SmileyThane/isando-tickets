<template>
    <v-container>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-expansion-panels>
                            <v-expansion-panel v-if="!this.$store.state.roles.includes(this.clientId)">
                                <v-expansion-panel-header>
                                    Add New Product
                                    <template v-slot:actions>
                                        <v-icon color="submit">mdi-plus</v-icon>
                                    </template>
                                </v-expansion-panel-header>
                                <v-expansion-panel-content>
                                    <v-form>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    label="Name"
                                                    name="product_name"
                                                    type="text"
                                                    v-model="productForm.product_name"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <div class="col-md-6">
                                                <v-text-field
                                                    color="green"
                                                    label="Description"
                                                    name="product_name"
                                                    type="text"
                                                    v-model="productForm.product_description"
                                                    required
                                                ></v-text-field>
                                            </div>
                                            <v-btn
                                                dark
                                                fab
                                                right
                                                bottom
                                                color="green"
                                                @click="addProduct"
                                            >
                                                <v-icon>mdi-plus</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-form>
                                </v-expansion-panel-content>
                            </v-expansion-panel>
                        </v-expansion-panels>
                        <v-data-table
                            :headers="headers"
                            :items="products"
                            :options.sync="options"
                            :server-items-length="totalProducts"
                            :loading="loading"
                            :footer-props="footerProps"
                            class="elevation-1"
                            hide-default-footer
                            fixed-header
                            loading-text="Give me a second..."
                        >
                            <template v-slot:top>
                                <v-row>
                                    <v-col sm="12" md="10">
                                        <v-text-field @input="getProducts" v-model="productsSearch" color="green"
                                                      label="Search..." class="mx-4"></v-text-field>
                                    </v-col>
                                    <v-col sm="12" md="2">
                                        <v-select
                                            class="mx-4"
                                            color="green"
                                            item-color="green"
                                            :items="footerProps.itemsPerPageOptions"
                                            label="Items per page"
                                            @change="updateItemsCount"
                                        ></v-select>
                                    </v-col>
                                </v-row>
                            </template>
                            <template v-slot:footer>
                                <v-pagination color="green"
                                              v-model="options.page"
                                              :length="lastPage"
                                              circle
                                              :page="options.page"
                                              :total-visible="5"
                                >
                                </v-pagination>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <v-icon
                                    small
                                    class="mr-2"
                                    @click="showItem(item)"
                                >
                                    mdi-eye
                                </v-icon>
                                <v-icon
                                    small
                                    @click="showItem(item)"
                                >
                                    mdi-delete
                                </v-icon>
                            </template>

                        </v-data-table>
                    </div>
                </div>
            </div>
        </div>
    </v-container>
</template>


<script>
    export default {

        data() {
            return {
                clientId: 6,
                totalProducts: 0,
                lastPage: 0,
                loading: 'green',
                options: {
                    page: 1,
                    sortDesc: [false],
                    sortBy: ['id']
                },
                footerProps: {
                    itemsPerPage: 10,
                    showFirstLastPage: true,
                    itemsPerPageOptions: [10, 25, 50, 100],
                },
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'product_data.id',
                    },
                    {text: 'name', value: 'product_data.name'},
                    {text: 'Description', value: 'product_data.description'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                productsSearch: '',
                products: [],
                productForm: {
                    product_name: '',
                    product_description: '',
                },
            }
        },
        mounted() {
            this.getProducts()
        },
        methods: {
            getProducts() {
                this.loading = "green"
                if (this.options.sortDesc.length <= 0) {
                    this.options.sortBy[0] = 'id'
                    this.options.sortDesc[0] = false
                }
                if (this.totalProducts < this.options.itemsPerPage) {
                    this.options.page = 1
                }
                axios.get(`api/product?
                    search=${this.productsSearch}&
                    sort_by=${this.options.sortBy[0]}&
                    sort_val=${this.options.sortDesc[0]}&
                    per_page=${this.options.itemsPerPage}&
                    page=${this.options.page}`).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.products = response.data.data
                        this.totalProducts = response.data.total
                        this.lastPage = response.data.last_page
                        this.loading = false
                    } else {
                        console.log('error')
                    }

                });
            },
            addProduct() {
                console.log(this.productForm)
                axios.post('api/product', this.productForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getProducts()
                    } else {
                        console.log('error')
                    }
                });
            },
            showItem(item) {
                this.$router.push(`/product/${item.product_data.id}`)
            },
            updateItemsCount(value) {
                this.options.itemsPerPage = value
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
