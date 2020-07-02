<template>
    <v-container>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"></div>

                    <div class="card-body">
                        <v-expansion-panels>
                            <v-expansion-panel>
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
                            :items-per-page="25"
                            class="elevation-1"
                        >
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
                headers: [
                    {
                        text: 'ID',
                        align: 'start',
                        sortable: false,
                        value: 'id',
                    },
                    {text: 'name', value: 'product_data.name'},
                    {text: 'Description', value: 'product_data.description'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
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
                axios.get('api/product').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.products = response.data.data
                    } else {
                        console.log('error')
                    }

                });
            },
            addProduct() {
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
                this.$router.push(`/product/${item.id}`)
            }
        }
    }
</script>
