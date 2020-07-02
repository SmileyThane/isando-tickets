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
        </div>
    </v-container>
</template>


<script>
    export default {

        data() {
            return {
                product: {
                    product_name: '',
                    product_description: '',
                },
            }
        },
        mounted() {
            this.getProduct()
        },
        methods: {
            getProduct() {
                axios.get(`/api/product/${this.$route.params.id}`).then(response => {
                    response = response.data
                    if (response.success === true) {
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
            }
        }
    }
</script>
