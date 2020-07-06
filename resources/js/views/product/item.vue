<template>
    <v-container>

        <div class="row">
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Product information</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <v-card-text>
                        <v-form>
                            <v-text-field
                                color="green"
                                label="Name"
                                name="name"
                                prepend-icon="mdi-rename-box"
                                type="text"
                                v-model="product.product_name"
                                required
                            ></v-text-field>
                            <v-text-field
                                color="green"
                                label="Description"
                                name="description"
                                prepend-icon="mdi-comment-text"
                                type="text"
                                v-model="product.product_description"
                                required
                            ></v-text-field>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="green" style="color: white;" @click="updateProduct">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </div>
            <div class="col-md-6">
                <v-card class="elevation-12">
                    <v-toolbar
                        color="green"
                        dark
                        flat
                    >
                        <v-toolbar-title>Product clients</v-toolbar-title>
                        <v-spacer></v-spacer>
                    </v-toolbar>
                    <div class="card-text">
                        <v-data-table
                            :headers="headers"
                            :items="product.clients"
                            :items-per-page="25"
                            class="elevation-1"
                        ></v-data-table>
                    </div>
                </v-card>
                <v-spacer>
                    &nbsp;
                </v-spacer>
                <v-expansion-panels>
                    <v-expansion-panel>
                        <v-expansion-panel-header>
                            Add New Product Client
                            <template v-slot:actions>
                                <v-icon color="submit">mdi-plus</v-icon>
                            </template>
                        </v-expansion-panel-header>
                        <v-expansion-panel-content>
                            <v-form>
                                <div class="row">
                                    <v-col cols="md-12">
                                        <v-autocomplete
                                            color="green"
                                            item-color="green"
                                            item-text="name"
                                            item-value="id"
                                            v-model="supplierForm.client_id"
                                            :items="suppliers"
                                            label="Client"
                                        ></v-autocomplete>
                                    </v-col>
                                    <v-btn
                                        dark
                                        fab
                                        right
                                        bottom
                                        color="green"
                                        @click="addProductClient"
                                    >
                                        <v-icon>mdi-plus</v-icon>
                                    </v-btn>
                                </div>
                            </v-form>
                        </v-expansion-panel-content>
                    </v-expansion-panel>
                </v-expansion-panels>
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
                        value: 'client_data.id',
                    },
                    {text: 'name', value: 'client_data.name'},
                    {text: 'Description', value: 'client_data.description'},
                    {text: 'Actions', value: 'actions', sortable: false},
                ],
                product: {
                    id: '',
                    product_name: '',
                    product_description: '',
                    clients: []
                },
                suppliers: [],
                supplierForm: {
                    client_id: null,
                    product_id: null
                }
            }
        },
        mounted() {
            this.getProduct()
            this.getSuppliers()
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
                        console.log('error')
                    }

                });
            },
            updateProduct() {
                axios.patch(`/api/product/${this.$route.params.id}`, this.product).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.product.product_name = response.data.name
                        this.product.product_description = response.data.description
                    } else {
                        console.log('error')
                    }

                });
            },
            getSuppliers() {
                axios.get('/api/client').then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.suppliers = response.data.data
                    } else {
                        console.log('error')
                    }
                });
            },
            addProductClient() {
                axios.post(`/api/product/client`, this.supplierForm).then(response => {
                    response = response.data
                    if (response.success === true) {
                        this.getProduct()
                    } else {
                        console.log('error')
                    }

                });
            }
        }
    }
</script>
